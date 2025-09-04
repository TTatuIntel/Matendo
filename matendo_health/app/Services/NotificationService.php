<?php

namespace App\Services;

use App\Events\RealTimeNotification;
use App\Events\MedicalAlert;
use App\Models\User;
use App\Models\Patient;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Send a real-time notification to a user
     */
    public function sendToUser(User $user, array $notificationData): void
    {
        try {
            // Store in database
            $notification = Notification::create([
                'type' => $notificationData['type'] ?? 'info',
                'notifiable_type' => get_class($user),
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'title' => $notificationData['title'] ?? 'Notification',
                    'message' => $notificationData['message'] ?? '',
                    'type' => $notificationData['type'] ?? 'info',
                    'data' => $notificationData['data'] ?? [],
                    'action_url' => $notificationData['action_url'] ?? null,
                ]),
                'read_at' => null,
            ]);

            // Broadcast in real-time
            broadcast(new RealTimeNotification($user, [
                'id' => $notification->id,
                'type' => $notificationData['type'] ?? 'info',
                'title' => $notificationData['title'] ?? 'Notification',
                'message' => $notificationData['message'] ?? '',
                'data' => $notificationData['data'] ?? [],
                'action_url' => $notificationData['action_url'] ?? null,
            ]));

        } catch (\Exception $e) {
            logger('Failed to send notification: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to multiple users
     */
    public function sendToUsers(array $users, array $notificationData): void
    {
        foreach ($users as $user) {
            $this->sendToUser($user, $notificationData);
        }
    }

    /**
     * Send notification to all users of a specific role
     */
    public function sendToRole(string $role, array $notificationData): void
    {
        $users = User::where('role', $role)->get();
        $this->sendToUsers($users, $notificationData);
    }

    /**
     * Send medical alert
     */
    public function sendMedicalAlert(Patient $patient, string $alertType, array $alertData, string $severity = 'medium'): void
    {
        try {
            // Store alert in database
            $alertNotification = [
                'type' => 'medical_alert',
                'title' => 'Medical Alert',
                'message' => $this->getAlertMessage($patient, $alertType),
                'data' => array_merge($alertData, [
                    'patient_id' => $patient->id,
                    'alert_type' => $alertType,
                    'severity' => $severity
                ]),
                'action_url' => route('doctor.patients.monitor', $patient->id)
            ];

            // Send to patient's doctors
            if ($patient->doctors) {
                foreach ($patient->doctors as $doctor) {
                    $this->sendToUser($doctor->user, $alertNotification);
                }
            }

            // Send to admins for high/critical alerts
            if (in_array($severity, ['high', 'critical'])) {
                $this->sendToRole('admin', $alertNotification);
            }

            // Broadcast medical alert
            broadcast(new MedicalAlert($patient, $alertType, $alertData, $severity));

        } catch (\Exception $e) {
            logger('Failed to send medical alert: ' . $e->getMessage());
        }
    }

    /**
     * Send appointment notification
     */
    public function sendAppointmentNotification(User $user, array $appointmentData, string $type = 'scheduled'): void
    {
        $titles = [
            'scheduled' => 'Appointment Scheduled',
            'confirmed' => 'Appointment Confirmed',
            'cancelled' => 'Appointment Cancelled',
            'reminder' => 'Appointment Reminder',
            'completed' => 'Appointment Completed'
        ];

        $this->sendToUser($user, [
            'type' => 'appointment',
            'title' => $titles[$type] ?? 'Appointment Update',
            'message' => $this->getAppointmentMessage($appointmentData, $type),
            'data' => $appointmentData,
            'action_url' => $user->role === 'patient' 
                ? route('patient.appointments.index') 
                : route('doctor.appointments.index')
        ]);
    }

    /**
     * Send system notification to admins
     */
    public function sendSystemAlert(string $title, string $message, array $data = []): void
    {
        $this->sendToRole('admin', [
            'type' => 'system',
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => route('admin.monitoring.index')
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(string $notificationId, User $user): bool
    {
        try {
            $notification = Notification::where('id', $notificationId)
                ->where('notifiable_id', $user->id)
                ->whereNull('read_at')
                ->first();

            if ($notification) {
                $notification->update(['read_at' => now()]);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            logger('Failed to mark notification as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(User $user): int
    {
        try {
            return Notification::where('notifiable_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        } catch (\Exception $e) {
            logger('Failed to mark all notifications as read: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get unread notification count for user
     */
    public function getUnreadCount(User $user): int
    {
        return Notification::where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get recent notifications for user
     */
    public function getRecentNotifications(User $user, int $limit = 10): array
    {
        return Notification::where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                $data = json_decode($notification->data, true);
                return [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'info',
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? '',
                    'data' => $data['data'] ?? [],
                    'action_url' => $data['action_url'] ?? null,
                    'created_at' => $notification->created_at->toISOString(),
                    'read_at' => $notification->read_at?->toISOString(),
                    'is_read' => !is_null($notification->read_at)
                ];
            })
            ->toArray();
    }

    /**
     * Get alert message for medical alerts
     */
    private function getAlertMessage(Patient $patient, string $alertType): string
    {
        $messages = [
            'vitals_abnormal' => "Patient {$patient->user->name} has abnormal vital signs",
            'medication_missed' => "Patient {$patient->user->name} missed scheduled medication",
            'emergency_button' => "Patient {$patient->user->name} pressed emergency button",
            'critical_vitals' => "CRITICAL: Patient {$patient->user->name} has life-threatening vital signs",
            'no_data' => "Patient {$patient->user->name} hasn't reported vitals in 24+ hours",
            'doctor_review_needed' => "Patient {$patient->user->name} requires immediate doctor review",
        ];

        return $messages[$alertType] ?? "Alert for patient {$patient->user->name}";
    }

    /**
     * Get appointment message
     */
    private function getAppointmentMessage(array $appointmentData, string $type): string
    {
        $patientName = $appointmentData['patient_name'] ?? 'Patient';
        $doctorName = $appointmentData['doctor_name'] ?? 'Doctor';
        $date = $appointmentData['appointment_date'] ?? '';

        $messages = [
            'scheduled' => "New appointment scheduled with Dr. {$doctorName} on {$date}",
            'confirmed' => "Your appointment with Dr. {$doctorName} on {$date} has been confirmed",
            'cancelled' => "Your appointment with Dr. {$doctorName} on {$date} has been cancelled",
            'reminder' => "Reminder: You have an appointment with Dr. {$doctorName} tomorrow",
            'completed' => "Your appointment with Dr. {$doctorName} has been completed"
        ];

        return $messages[$type] ?? "Appointment update";
    }
}
