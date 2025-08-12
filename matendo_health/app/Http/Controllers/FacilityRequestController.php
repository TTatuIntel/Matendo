<?php

namespace App\Http\Controllers;

use App\Models\FacilityRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FacilityRequestController extends Controller
{
    const MAX_FILE_SIZE = 5 * 1024 * 1024;
    const ALLOWED_DOC_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png',
        'image/jpg'
    ];

    /**
     * Store a new facility request
     */
    public function store(Request $request)
    {
        try {
            if ($request->method() !== 'POST') {
                throw new \Exception('Invalid request method');
            }

            // Generate reference number exactly like the original
            $reference_number = 'REQ-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

            // Sanitize inputs exactly like the original
            $facility_name = $this->sanitizeInput($request->input('facilityName'));
            $contact_person = $this->sanitizeInput($request->input('contactPerson'));
            $email = $this->sanitizeInput($request->input('email'));
            $phone = $this->sanitizeInput($request->input('phone'));
            $coordinates = $this->sanitizeInput($request->input('coordinates'));
            
            // Handle JSON fields for multi-select
            $facility_type = json_encode($request->input('facilityType', []));
            $other_facility_type = $this->sanitizeInput($request->input('otherFacilityType'));
            $positions = json_encode($request->input('positions', []));
            $other_position = $this->sanitizeInput($request->input('otherPosition'));
            $employment_type = json_encode($request->input('duration', [])); // duration maps to employment_type
            $shift_type = json_encode($request->input('shiftType', []));
            
            $staff_number = intval($request->input('staffNumber', 0));
            $start_date = $this->sanitizeInput($request->input('startDate'));
            $job_requirement_option = $this->sanitizeInput($request->input('job-requirement-option'));
            $qualifications = $this->sanitizeInput($request->input('qualifications'));
            $experience = $this->sanitizeInput($request->input('experience'));
            $job_description = $this->sanitizeInput($request->input('jobDescription'));

            // Handle job description file upload if provided
            list($job_description_base64, $job_description_name, $job_description_mime, $job_description_size) = 
                ($job_requirement_option === 'upload') ? 
                $this->getBase64File($request, 'jobDescriptionFile', self::ALLOWED_DOC_TYPES) : 
                [null, null, null, null];

            // Create facility request record
            $facilityRequest = FacilityRequest::create([
                'reference_number' => $reference_number,
                'facility_name' => $facility_name,
                'contact_person' => $contact_person,
                'email' => $email,
                'phone' => $phone,
                'coordinates' => $coordinates,
                'facility_type' => $facility_type,
                'other_facility_type' => $other_facility_type,
                'positions' => $positions,
                'other_position' => $other_position,
                'employment_type' => $employment_type,
                'shift_type' => $shift_type,
                'staff_number' => $staff_number,
                'start_date' => $start_date,
                'job_requirement_option' => $job_requirement_option,
                'qualifications' => $qualifications,
                'experience' => $experience,
                'job_description' => $job_description,
                'job_description_base64' => $job_description_base64,
                'job_description_name' => $job_description_name,
                'job_description_mime' => $job_description_mime,
                'job_description_size' => $job_description_size,
                'status' => 'pending',
                'priority' => 'normal',
                'confirmed' => false,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'form_metadata' => json_encode([
                    'submission_timestamp' => now()->format('Y-m-d H:i:s'),
                    'form_version' => '1.0',
                    'job_requirement_option' => $job_requirement_option
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Facility request submitted successfully',
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
