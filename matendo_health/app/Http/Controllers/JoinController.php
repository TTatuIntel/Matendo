<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JoinController extends Controller
{
    const MAX_FILE_SIZE = 5 * 1024 * 1024;
    const ALLOWED_RESUME_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    const ALLOWED_DOC_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/jpg'
    ];

    /**
     * Handle the join form submission
     */
    public function store(Request $request)
    {
        try {

            // Generate reference code exactly like the original
            $reference_number = 'REF-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

            // Sanitize inputs exactly like the original
            $first_name = $this->sanitizeInput($request->input('firstName'));
            $last_name = $this->sanitizeInput($request->input('lastName'));
            $email = $this->sanitizeInput($request->input('email'));
            $phone = $this->sanitizeInput($request->input('phone'));
            $address = $this->sanitizeInput($request->input('address'));
            $location = $this->sanitizeInput($request->input('location'));
            $coordinates = $this->sanitizeInput($request->input('coordinates'));
            $profession = $this->sanitizeInput($request->input('profession'));
            $other_profession = ($profession === 'Other') ? $this->sanitizeInput($request->input('otherProfession')) : null;
            $specialization = $this->sanitizeInput($request->input('specialization'));
            $years_experience = intval($request->input('yearsExperience', 0));
            $license_number = $this->sanitizeInput($request->input('licenseNumber'));

            $work_type = json_encode($request->input('workType', []));
            $shift_type = json_encode($request->input('shiftType', []));
            $preferred_location = $this->sanitizeInput($request->input('preferredLocation'));
            $start_date = $this->sanitizeInput($request->input('startDate'));

            // Handle file uploads using the same base64 approach
            list($resume_base64, $resume_name, $resume_mime, $resume_size) = 
                $this->getBase64File($request, 'resume', self::ALLOWED_RESUME_TYPES);
            list($license_base64, $license_name, $license_mime, $license_size) = 
                $this->getBase64File($request, 'license', self::ALLOWED_DOC_TYPES);
            list($certifications_base64, $certifications_name, $certifications_mime, $certifications_size) = 
                $this->getBase64File($request, 'certifications', self::ALLOWED_DOC_TYPES);

            if (!$resume_base64) {
                throw new \Exception("Resume file is required.");
            }

            // Create application record
            $application = Application::create([
                'reference_number' => $reference_number,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'location' => $location,
                'coordinates' => $coordinates,
                'profession' => $profession,
                'other_profession' => $other_profession,
                'specialization' => $specialization,
                'years_experience' => $years_experience,
                'license_number' => $license_number,
                'work_type' => $work_type,
                'shift_type' => $shift_type,
                'preferred_location' => $preferred_location,
                'start_date' => $start_date,
                'resume_base64' => $resume_base64,
                'resume_name' => $resume_name,
                'resume_mime' => $resume_mime,
                'resume_size' => $resume_size,
                'license_base64' => $license_base64,
                'license_name' => $license_name,
                'license_mime' => $license_mime,
                'license_size' => $license_size,
                'certifications_base64' => $certifications_base64,
                'certifications_name' => $certifications_name,
                'certifications_mime' => $certifications_mime,
                'certifications_size' => $certifications_size,
                'status' => 'pending',
                'confirmed' => false,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'form_metadata' => json_encode([
                    'submission_timestamp' => now()->format('Y-m-d H:i:s'),
                    'form_version' => '1.0'
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'reference_number' => $reference_number
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Sanitize input exactly like the original
     */
    private function sanitizeInput($data)
    {
        return $data === null ? null : htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Handle file upload and convert to base64 exactly like the original
     */
    private function getBase64File(Request $request, $key, $allowed_types)
    {
        if (!$request->hasFile($key) || !$request->file($key)->isValid()) {
            return [null, null, null, null];
        }

        $file = $request->file($key);
        
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception(ucfirst($key) . " file is too large. Max size 5MB.");
        }

        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $allowed_types)) {
            throw new \Exception("Invalid $key file type.");
        }

        $fileContent = file_get_contents($file->getRealPath());
        if ($fileContent === false) {
            throw new \Exception("Failed to read $key file.");
        }

        return [
            base64_encode($fileContent),
            $this->sanitizeInput($file->getClientOriginalName()),
            $mimeType,
            $file->getSize()
        ];
    }
}
