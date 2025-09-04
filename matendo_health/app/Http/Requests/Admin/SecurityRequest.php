<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SecurityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage-security') || $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return match ($this->getRouteAction()) {
            'createIncident' => $this->createIncidentRules(),
            'blockIp' => $this->blockIpRules(),
            'unblockIp' => $this->unblockIpRules(),
            'revokeSession' => $this->revokeSessionRules(),
            'forcePasswordReset' => $this->forcePasswordResetRules(),
            'exportSecurityLogs' => $this->exportLogsRules(),
            default => []
        };
    }

    /**
     * Get the current route action
     */
    private function getRouteAction(): string
    {
        $routeName = $this->route()->getName();
        
        return match ($routeName) {
            'admin.security.create-incident' => 'createIncident',
            'admin.security.block-ip' => 'blockIp',
            'admin.security.unblock-ip' => 'unblockIp',
            'admin.security.revoke-session' => 'revokeSession',
            'admin.security.force-password-reset' => 'forcePasswordReset',
            'admin.security.export-logs' => 'exportSecurityLogs',
            default => 'unknown'
        };
    }

    /**
     * Rules for creating security incident
     */
    private function createIncidentRules(): array
    {
        return [
            'title' => 'required|string|max:255|min:5',
            'description' => 'required|string|max:2000|min:10',
            'severity' => 'required|in:low,medium,high,critical',
            'incident_type' => [
                'required',
                'string',
                'max:100',
                Rule::in([
                    'unauthorized_access',
                    'data_breach',
                    'malicious_activity',
                    'policy_violation',
                    'system_compromise',
                    'suspicious_behavior',
                    'failed_authentication',
                    'privilege_escalation',
                    'data_exfiltration',
                    'malware_detection'
                ])
            ],
            'affected_user_id' => 'nullable|exists:users,id',
            'ip_address' => 'nullable|ip',
            'evidence' => 'nullable|array|max:10',
            'evidence.*' => 'string|max:500',
            'immediate_action_taken' => 'nullable|string|max:1000'
        ];
    }

    /**
     * Rules for blocking IP address
     */
    private function blockIpRules(): array
    {
        return [
            'ip_address' => [
                'required',
                'ip',
                function ($attribute, $value, $fail) {
                    // Prevent blocking localhost or private IPs in production
                    if (app()->environment('production')) {
                        if (in_array($value, ['127.0.0.1', '::1']) || 
                            filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                            $fail('Cannot block localhost or private IP addresses in production.');
                        }
                    }
                    
                    // Prevent blocking admin's current IP
                    if ($value === request()->ip()) {
                        $fail('Cannot block your own IP address.');
                    }
                }
            ],
            'reason' => 'required|string|max:255|min:5',
            'duration' => 'nullable|integer|min:1|max:525600', // Max 1 year in minutes
            'block_type' => 'required|in:temporary,permanent'
        ];
    }

    /**
     * Rules for unblocking IP address
     */
    private function unblockIpRules(): array
    {
        return [
            'ip_address' => 'required|ip',
            'reason' => 'nullable|string|max:255'
        ];
    }

    /**
     * Rules for revoking session
     */
    private function revokeSessionRules(): array
    {
        return [
            'reason' => 'nullable|string|max:255'
        ];
    }

    /**
     * Rules for forcing password reset
     */
    private function forcePasswordResetRules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    // Prevent forcing password reset on own account
                    if ($value === auth()->id()) {
                        $fail('Cannot force password reset on your own account.');
                    }
                    
                    // Check if user is admin and current user has permission
                    $targetUser = \App\Models\User::find($value);
                    if ($targetUser && $targetUser->role === 'admin' && !$this->user()->can('manage-admin-users')) {
                        $fail('Insufficient permissions to reset admin user password.');
                    }
                }
            ],
            'reason' => 'required|string|max:255|min:10',
            'notify_user' => 'boolean'
        ];
    }

    /**
     * Rules for exporting security logs
     */
    private function exportLogsRules(): array
    {
        return [
            'date_from' => 'nullable|date|before_or_equal:today',
            'date_to' => 'nullable|date|after_or_equal:date_from|before_or_equal:today',
            'log_type' => 'nullable|in:all,incidents,failed_logins,blocks,scans'
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Incident title is required.',
            'title.min' => 'Incident title must be at least 5 characters long.',
            'title.max' => 'Incident title cannot exceed 255 characters.',
            'description.required' => 'Incident description is required.',
            'description.min' => 'Incident description must be at least 10 characters long.',
            'description.max' => 'Incident description cannot exceed 2000 characters.',
            'severity.required' => 'Please select an incident severity level.',
            'severity.in' => 'Please select a valid severity level.',
            'incident_type.required' => 'Please select an incident type.',
            'incident_type.in' => 'Please select a valid incident type.',
            'affected_user_id.exists' => 'The selected user does not exist.',
            'ip_address.required' => 'IP address is required.',
            'ip_address.ip' => 'Please provide a valid IP address.',
            'evidence.array' => 'Evidence must be provided as a list.',
            'evidence.max' => 'Cannot attach more than 10 evidence items.',
            'evidence.*.string' => 'Each evidence item must be text.',
            'evidence.*.max' => 'Each evidence item cannot exceed 500 characters.',
            'immediate_action_taken.max' => 'Immediate action description cannot exceed 1000 characters.',
            'reason.required' => 'A reason is required for this action.',
            'reason.min' => 'Reason must be at least 5 characters long.',
            'reason.max' => 'Reason cannot exceed 255 characters.',
            'duration.min' => 'Block duration must be at least 1 minute.',
            'duration.max' => 'Block duration cannot exceed 1 year.',
            'block_type.required' => 'Please select a block type.',
            'block_type.in' => 'Please select a valid block type.',
            'user_id.required' => 'Please select a user.',
            'user_id.exists' => 'The selected user does not exist.',
            'notify_user.boolean' => 'Notify user field must be true or false.',
            'date_from.date' => 'Please provide a valid start date.',
            'date_from.before_or_equal' => 'Start date cannot be in the future.',
            'date_to.date' => 'Please provide a valid end date.',
            'date_to.after_or_equal' => 'End date must be after or equal to start date.',
            'date_to.before_or_equal' => 'End date cannot be in the future.',
            'log_type.in' => 'Please select a valid log type.'
        ];
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return [
            'incident_type' => 'incident type',
            'affected_user_id' => 'affected user',
            'ip_address' => 'IP address',
            'immediate_action_taken' => 'immediate action taken',
            'block_type' => 'block type',
            'user_id' => 'user',
            'notify_user' => 'notify user',
            'date_from' => 'start date',
            'date_to' => 'end date',
            'log_type' => 'log type'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Custom validation logic
            if ($this->getRouteAction() === 'createIncident') {
                $this->validateSecurityIncident($validator);
            }
            
            if ($this->getRouteAction() === 'blockIp') {
                $this->validateIpBlocking($validator);
            }
        });
    }

    /**
     * Custom validation for security incidents
     */
    private function validateSecurityIncident($validator)
    {
        $severity = $this->input('severity');
        $incidentType = $this->input('incident_type');
        
        // Critical incidents require immediate action
        if ($severity === 'critical' && empty($this->input('immediate_action_taken'))) {
            $validator->errors()->add(
                'immediate_action_taken', 
                'Immediate action description is required for critical incidents.'
            );
        }
        
        // Data breach incidents require affected user
        if (in_array($incidentType, ['data_breach', 'unauthorized_access']) && empty($this->input('affected_user_id'))) {
            $validator->errors()->add(
                'affected_user_id', 
                'Affected user is required for ' . str_replace('_', ' ', $incidentType) . ' incidents.'
            );
        }
        
        // Validate evidence format
        $evidence = $this->input('evidence', []);
        foreach ($evidence as $index => $item) {
            if (empty(trim($item))) {
                $validator->errors()->add(
                    "evidence.{$index}", 
                    'Evidence item cannot be empty.'
                );
            }
        }
    }

    /**
     * Custom validation for IP blocking
     */
    private function validateIpBlocking($validator)
    {
        $blockType = $this->input('block_type');
        $duration = $this->input('duration');
        
        // Temporary blocks require duration
        if ($blockType === 'temporary' && empty($duration)) {
            $validator->errors()->add('duration', 'Duration is required for temporary blocks.');
        }
        
        // Permanent blocks should not have duration
        if ($blockType === 'permanent' && !empty($duration)) {
            $validator->errors()->add('duration', 'Duration should not be specified for permanent blocks.');
        }
        
        // Validate reasonable block duration
        if ($blockType === 'temporary' && $duration) {
            if ($duration < 5) {
                $validator->errors()->add('duration', 'Block duration should be at least 5 minutes.');
            }
            
            if ($duration > 43200) { // 30 days
                $validator->errors()->add('duration', 'Block duration should not exceed 30 days. Use permanent block for longer restrictions.');
            }
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Log validation failures for security monitoring
        activity()
            ->causedBy($this->user())
            ->withProperties([
                'action' => $this->getRouteAction(),
                'errors' => $validator->errors()->toArray(),
                'input' => $this->except(['password']),
                'ip_address' => $this->ip(),
                'user_agent' => $this->userAgent()
            ])
            ->log('Security validation failed');

        parent::failedValidation($validator);
    }

    /**
     * Get validated data with sanitization
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);
        
        // Sanitize input based on action
        if ($this->getRouteAction() === 'createIncident') {
            $validated = $this->sanitizeIncidentData($validated);
        }
        
        return $validated;
    }

    /**
     * Sanitize security incident data
     */
    private function sanitizeIncidentData($data)
    {
        if (isset($data['title'])) {
            $data['title'] = strip_tags(trim($data['title']));
        }
        
        if (isset($data['description'])) {
            $data['description'] = strip_tags(trim($data['description']));
        }
        
        if (isset($data['reason'])) {
            $data['reason'] = strip_tags(trim($data['reason']));
        }
        
        if (isset($data['immediate_action_taken'])) {
            $data['immediate_action_taken'] = strip_tags(trim($data['immediate_action_taken']));
        }
        
        if (isset($data['evidence']) && is_array($data['evidence'])) {
            $data['evidence'] = array_map(function($item) {
                return strip_tags(trim($item));
            }, array_filter($data['evidence']));
        }
        
        return $data;
    }
}
