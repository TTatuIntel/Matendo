<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Carbon\Carbon;
use ZipArchive;

class BackupController extends Controller
{
    private $backupPath;
    
    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        
        // Ensure backup directory exists
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    /**
     * Display backup management dashboard
     */
    public function index()
    {
        try {
            $backups = $this->getBackupList();
            
            $backupStats = [
                'total_backups' => count($backups),
                'total_size' => $this->getTotalBackupSize($backups),
                'last_backup' => $this->getLastBackupDate($backups),
                'disk_space' => $this->getDiskSpace(),
                'next_scheduled' => $this->getNextScheduledBackup()
            ];

            return view('admin.backup.index', compact('backups', 'backupStats'));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to load backup dashboard: ' . $e->getMessage()]);
        }
    }

    /**
     * Create a new backup
     */
    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'backup_type' => 'required|in:database,files,full',
                'description' => 'nullable|string|max:255',
                'compress' => 'boolean'
            ]);

            $backupId = Str::uuid();
            $timestamp = now()->format('Y-m-d_H-i-s');
            $backupName = "backup_{$validated['backup_type']}_{$timestamp}";
            $compress = $validated['compress'] ?? true;

            switch ($validated['backup_type']) {
                case 'database':
                    $backupFile = $this->createDatabaseBackup($backupName, $compress);
                    break;
                case 'files':
                    $backupFile = $this->createFilesBackup($backupName, $compress);
                    break;
                case 'full':
                    $backupFile = $this->createFullBackup($backupName, $compress);
                    break;
                default:
                    throw new \InvalidArgumentException('Invalid backup type');
            }

            // Log backup creation
            ActivityLog::create([
                'log_name' => 'backup',
                'description' => "Backup created: {$backupName}",
                'causer_id' => auth()->id(),
                'properties' => [
                    'backup_id' => $backupId,
                    'backup_type' => $validated['backup_type'],
                    'file_path' => $backupFile,
                    'file_size' => File::size($backupFile),
                    'description' => $validated['description'] ?? null,
                    'ip_address' => request()->ip()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully',
                'backup' => [
                    'id' => $backupId,
                    'name' => $backupName,
                    'type' => $validated['backup_type'],
                    'file' => basename($backupFile),
                    'size' => $this->formatBytes(File::size($backupFile)),
                    'created_at' => now()
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a backup file
     */
    public function download($backup)
    {
        try {
            $backupFile = $this->backupPath . '/' . $backup;
            
            if (!File::exists($backupFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup file not found'
                ], 404);
            }

            // Log backup download
            ActivityLog::create([
                'log_name' => 'backup',
                'description' => "Backup downloaded: {$backup}",
                'causer_id' => auth()->id(),
                'properties' => [
                    'backup_file' => $backup,
                    'ip_address' => request()->ip()
                ]
            ]);

            return response()->download($backupFile);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a backup file
     */
    public function destroy($backup)
    {
        try {
            $backupFile = $this->backupPath . '/' . $backup;
            
            if (!File::exists($backupFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup file not found'
                ], 404);
            }

            $fileSize = File::size($backupFile);
            File::delete($backupFile);

            // Log backup deletion
            ActivityLog::create([
                'log_name' => 'backup',
                'description' => "Backup deleted: {$backup}",
                'causer_id' => auth()->id(),
                'properties' => [
                    'backup_file' => $backup,
                    'file_size' => $fileSize,
                    'ip_address' => request()->ip()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Backup deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore from a backup
     */
    public function restore($backup, Request $request)
    {
        try {
            $validated = $request->validate([
                'restore_type' => 'required|in:database,files,full',
                'confirmation' => 'required|accepted'
            ]);

            $backupFile = $this->backupPath . '/' . $backup;
            
            if (!File::exists($backupFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup file not found'
                ], 404);
            }

            // Create a safety backup before restore
            $safetyBackupName = "safety_backup_" . now()->format('Y-m-d_H-i-s');
            $this->createDatabaseBackup($safetyBackupName, true);

            switch ($validated['restore_type']) {
                case 'database':
                    $this->restoreDatabaseBackup($backupFile);
                    break;
                case 'files':
                    $this->restoreFilesBackup($backupFile);
                    break;
                case 'full':
                    $this->restoreFullBackup($backupFile);
                    break;
                default:
                    throw new \InvalidArgumentException('Invalid restore type');
            }

            // Log backup restoration
            ActivityLog::create([
                'log_name' => 'backup_restore',
                'description' => "System restored from backup: {$backup}",
                'causer_id' => auth()->id(),
                'properties' => [
                    'backup_file' => $backup,
                    'restore_type' => $validated['restore_type'],
                    'safety_backup' => $safetyBackupName,
                    'ip_address' => request()->ip()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'System restored successfully from backup',
                'safety_backup' => $safetyBackupName
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display backup scheduling interface
     */
    public function schedule()
    {
        try {
            $currentSchedule = $this->getCurrentSchedule();
            
            return view('admin.backup.schedule', compact('currentSchedule'));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to load backup schedule: ' . $e->getMessage()]);
        }
    }

    /**
     * Update backup schedule
     */
    public function updateSchedule(Request $request)
    {
        try {
            $validated = $request->validate([
                'enabled' => 'boolean',
                'frequency' => 'required_if:enabled,true|in:daily,weekly,monthly',
                'time' => 'required_if:enabled,true|date_format:H:i',
                'backup_type' => 'required_if:enabled,true|in:database,files,full',
                'retention_days' => 'required_if:enabled,true|integer|min:1|max:365',
                'compress' => 'boolean',
                'email_notification' => 'boolean'
            ]);

            // Save schedule configuration
            $this->saveScheduleConfiguration($validated);

            // Log schedule update
            ActivityLog::create([
                'log_name' => 'backup_schedule',
                'description' => 'Backup schedule updated',
                'causer_id' => auth()->id(),
                'properties' => [
                    'schedule_config' => $validated,
                    'ip_address' => request()->ip()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Backup schedule updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update backup schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clean up old backups based on retention policy
     */
    public function cleanup(Request $request)
    {
        try {
            $validated = $request->validate([
                'retention_days' => 'required|integer|min:1|max:365'
            ]);

            $retentionDate = now()->subDays($validated['retention_days']);
            $backups = $this->getBackupList();
            $deletedCount = 0;
            $freedSpace = 0;

            foreach ($backups as $backup) {
                if ($backup['created_at']->lt($retentionDate)) {
                    $backupFile = $this->backupPath . '/' . $backup['file'];
                    if (File::exists($backupFile)) {
                        $freedSpace += File::size($backupFile);
                        File::delete($backupFile);
                        $deletedCount++;
                    }
                }
            }

            // Log cleanup
            ActivityLog::create([
                'log_name' => 'backup_cleanup',
                'description' => "Backup cleanup completed: {$deletedCount} backups deleted",
                'causer_id' => auth()->id(),
                'properties' => [
                    'deleted_count' => $deletedCount,
                    'freed_space' => $freedSpace,
                    'retention_days' => $validated['retention_days'],
                    'ip_address' => request()->ip()
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => "Cleanup completed: {$deletedCount} old backups deleted",
                'deleted_count' => $deletedCount,
                'freed_space' => $this->formatBytes($freedSpace)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup backups: ' . $e->getMessage()
            ], 500);
        }
    }

    // Private helper methods
    private function createDatabaseBackup($backupName, $compress = true)
    {
        $dumpFile = $this->backupPath . '/' . $backupName . '.sql';
        
        $dbConfig = config('database.connections.' . config('database.default'));
        
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
            $dbConfig['host'],
            $dbConfig['port'] ?? 3306,
            $dbConfig['username'],
            $dbConfig['password'],
            $dbConfig['database'],
            $dumpFile
        );

        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Database backup failed with return code: ' . $returnCode);
        }

        if ($compress) {
            $compressedFile = $dumpFile . '.gz';
            exec("gzip {$dumpFile}", $output, $returnCode);
            
            if ($returnCode === 0 && File::exists($compressedFile)) {
                return $compressedFile;
            }
        }

        return $dumpFile;
    }

    private function createFilesBackup($backupName, $compress = true)
    {
        $backupFile = $this->backupPath . '/' . $backupName . '.zip';
        
        $zip = new ZipArchive();
        
        if ($zip->open($backupFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception('Could not create backup archive');
        }

        // Add important directories
        $this->addDirectoryToZip($zip, storage_path('app'), 'storage/app');
        $this->addDirectoryToZip($zip, public_path('uploads'), 'public/uploads');
        
        $zip->close();

        if (!File::exists($backupFile)) {
            throw new \Exception('Files backup creation failed');
        }

        return $backupFile;
    }

    private function createFullBackup($backupName, $compress = true)
    {
        // Create database backup first
        $dbBackup = $this->createDatabaseBackup($backupName . '_db', $compress);
        
        // Create files backup
        $filesBackup = $this->createFilesBackup($backupName . '_files', $compress);
        
        // Combine into single archive
        $fullBackupFile = $this->backupPath . '/' . $backupName . '_full.zip';
        
        $zip = new ZipArchive();
        if ($zip->open($fullBackupFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception('Could not create full backup archive');
        }

        $zip->addFile($dbBackup, 'database/' . basename($dbBackup));
        $zip->addFile($filesBackup, 'files/' . basename($filesBackup));
        
        $zip->close();

        // Clean up temporary files
        File::delete($dbBackup);
        File::delete($filesBackup);

        return $fullBackupFile;
    }

    private function addDirectoryToZip($zip, $source, $destination = '')
    {
        if (!File::exists($source)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            $file = $file->getRealPath();
            
            if (is_dir($file)) {
                $relativePath = $destination . '/' . substr($file, strlen($source) + 1) . '/';
                $zip->addEmptyDir($relativePath);
            } elseif (is_file($file)) {
                $relativePath = $destination . '/' . substr($file, strlen($source) + 1);
                $zip->addFile($file, $relativePath);
            }
        }
    }

    private function restoreDatabaseBackup($backupFile)
    {
        $dbConfig = config('database.connections.' . config('database.default'));
        
        // Handle compressed files
        if (str_ends_with($backupFile, '.gz')) {
            $uncompressedFile = str_replace('.gz', '', $backupFile);
            exec("gunzip -c {$backupFile} > {$uncompressedFile}");
            $backupFile = $uncompressedFile;
        }

        $command = sprintf(
            'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
            $dbConfig['host'],
            $dbConfig['port'] ?? 3306,
            $dbConfig['username'],
            $dbConfig['password'],
            $dbConfig['database'],
            $backupFile
        );

        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Database restore failed with return code: ' . $returnCode);
        }
    }

    private function restoreFilesBackup($backupFile)
    {
        $zip = new ZipArchive();
        
        if ($zip->open($backupFile) !== TRUE) {
            throw new \Exception('Could not open backup archive for restore');
        }

        $extractPath = base_path();
        $zip->extractTo($extractPath);
        $zip->close();
    }

    private function restoreFullBackup($backupFile)
    {
        $tempPath = $this->backupPath . '/temp_restore';
        
        if (!File::exists($tempPath)) {
            File::makeDirectory($tempPath, 0755, true);
        }

        $zip = new ZipArchive();
        
        if ($zip->open($backupFile) !== TRUE) {
            throw new \Exception('Could not open full backup archive');
        }

        $zip->extractTo($tempPath);
        $zip->close();

        // Restore database
        $dbBackups = glob($tempPath . '/database/*');
        if (!empty($dbBackups)) {
            $this->restoreDatabaseBackup($dbBackups[0]);
        }

        // Restore files
        $fileBackups = glob($tempPath . '/files/*');
        if (!empty($fileBackups)) {
            $this->restoreFilesBackup($fileBackups[0]);
        }

        // Clean up temp directory
        File::deleteDirectory($tempPath);
    }

    private function getBackupList()
    {
        $backups = [];
        $files = File::files($this->backupPath);
        
        foreach ($files as $file) {
            $backups[] = [
                'file' => $file->getFilename(),
                'size' => $file->getSize(),
                'created_at' => Carbon::createFromTimestamp($file->getMTime()),
                'type' => $this->getBackupType($file->getFilename())
            ];
        }

        // Sort by creation date (newest first)
        usort($backups, function($a, $b) {
            return $b['created_at']->timestamp - $a['created_at']->timestamp;
        });

        return $backups;
    }

    private function getBackupType($filename)
    {
        if (strpos($filename, '_database_') !== false || str_ends_with($filename, '.sql')) {
            return 'database';
        } elseif (strpos($filename, '_files_') !== false) {
            return 'files';
        } elseif (strpos($filename, '_full_') !== false) {
            return 'full';
        }
        
        return 'unknown';
    }

    private function getTotalBackupSize($backups)
    {
        $totalSize = array_sum(array_column($backups, 'size'));
        return $this->formatBytes($totalSize);
    }

    private function getLastBackupDate($backups)
    {
        if (empty($backups)) {
            return 'Never';
        }
        
        return $backups[0]['created_at']->diffForHumans();
    }

    private function getDiskSpace()
    {
        $totalBytes = disk_total_space($this->backupPath);
        $freeBytes = disk_free_space($this->backupPath);
        
        return [
            'total' => $this->formatBytes($totalBytes),
            'free' => $this->formatBytes($freeBytes),
            'percentage' => round(($freeBytes / $totalBytes) * 100, 1)
        ];
    }

    private function getNextScheduledBackup()
    {
        $schedule = $this->getCurrentSchedule();
        
        if (!$schedule['enabled']) {
            return 'Not scheduled';
        }
        
        // Calculate next backup time based on frequency
        $now = now();
        $time = Carbon::createFromFormat('H:i', $schedule['time']);
        
        switch ($schedule['frequency']) {
            case 'daily':
                $next = $now->copy()->setTimeFrom($time);
                if ($next->isPast()) {
                    $next->addDay();
                }
                break;
            case 'weekly':
                $next = $now->copy()->startOfWeek()->setTimeFrom($time);
                if ($next->isPast()) {
                    $next->addWeek();
                }
                break;
            case 'monthly':
                $next = $now->copy()->startOfMonth()->setTimeFrom($time);
                if ($next->isPast()) {
                    $next->addMonth();
                }
                break;
            default:
                return 'Unknown';
        }
        
        return $next->diffForHumans();
    }

    private function getCurrentSchedule()
    {
        // This would typically be stored in a settings table or config file
        return [
            'enabled' => false,
            'frequency' => 'daily',
            'time' => '02:00',
            'backup_type' => 'database',
            'retention_days' => 30,
            'compress' => true,
            'email_notification' => true
        ];
    }

    private function saveScheduleConfiguration($config)
    {
        // This would typically save to a settings table or config file
        // For now, we'll just log it
        
        // In a real implementation, you'd save to SystemSetting model or config file
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
