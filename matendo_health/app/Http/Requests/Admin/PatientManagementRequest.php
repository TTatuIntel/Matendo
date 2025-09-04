<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientManagementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage-patients') || $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $patientId = $this->route('patient') ? $this->route('patient')->id : null;

        return match ($this->getMethod()) {
            'POST' => $this->createRules(),
            'PUT', 'PATCH' => $this->updateRules($patientId),
            default => []
        };
    }

    /**
     * Rules for creating a patient
     */
    private function createRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
            'insurance_number' => 'nullable|string|max:100|unique:patients,insurance_number',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string|max:1000',
            'chronic_conditions' => 'nullable|string|max:1000',
            'password' => 'required|string|min:8|confirmed'
        ];
    }

    /**
     * Rules for updating a patient
     */
    private function updateRules($patientId): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($patientId, 'id')
            ],
            'phone' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
            'insurance_number' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('patients', 'insurance_number')->ignore($patientId, 'user_id')
            ],
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'nullable|string|max:1000',
            'chronic_conditions' => 'nullable|string|max:1000'
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Patient name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.regex' => 'Please provide a valid phone number.',
            'emergency_phone.regex' => 'Please provide a valid emergency contact phone number.',
            'date_of_birth.before' => 'Date of birth must be before today.',
            'gender.in' => 'Please select a valid gender option.',
            'insurance_number.unique' => 'This insurance number is already registered.',
            'blood_type.in' => 'Please select a valid blood type.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.'
        ];
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return [
            'date_of_birth' => 'date of birth',
            'emergency_contact' => 'emergency contact name',
            'emergency_phone' => 'emergency contact phone',
            'insurance_number' => 'insurance number',
            'blood_type' => 'blood type',
            'chronic_conditions' => 'chronic conditions'
        ];
    }
}
