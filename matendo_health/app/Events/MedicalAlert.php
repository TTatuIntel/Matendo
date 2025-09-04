<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Patient;

class MedicalAlert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $patient;
    public $alertType;
    public $alertData;
    public $severity;

    /**
     * Create a new event instance.
     */
    public function __construct(Patient $patient, string $alertType, array $alertData, string $severity = 'medium')
    {
        $this->patient = $patient;
        $this->alertType = $alertType;
        $this->alertData = $alertData;
        $this->severity = $severity;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            // Broadcast to all medical staff
            new Channel('medical-alerts'),
            // Broadcast to patient's specific doctors
            new PrivateChannel('patient.' . $this->patient->id . '.doctors'),
            // Broadcast to admin users
            new PrivateChannel('admin.alerts')
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'medical-alert';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => uniqid('alert_'),
            'patient' => [
                'id' => $this->patient->id,
                'name' => $this->patient->user->name,
                'age' => $this->patient->age,
                'room' => $this->patient->room_number,
            ],
            'alert_type' => $this->alertType,
            'severity' => $this->severity,
            'message' => $this->getAlertMessage(),
            'data' => $this->alertData,
            'timestamp' => now()->toISOString(),
            'requires_immediate_attention' => in_array($this->severity, ['high', 'critical'])
        ];
    }

    /**
     * Get human-readable alert message
     */
    private function getAlertMessage(): string
    {
        $messages = [
            'vitals_abnormal' => "Patient {$this->patient->user->name} has abnormal vital signs",
            'medication_missed' => "Patient {$this->patient->user->name} missed scheduled medication",
            'emergency_button' => "Patient {$this->patient->user->name} pressed emergency button",
            'critical_vitals' => "CRITICAL: Patient {$this->patient->user->name} has life-threatening vital signs",
            'no_data' => "Patient {$this->patient->user->name} hasn't reported vitals in 24+ hours",
            'doctor_review_needed' => "Patient {$this->patient->user->name} requires immediate doctor review",
        ];

        return $messages[$this->alertType] ?? "Alert for patient {$this->patient->user->name}";
    }
}
