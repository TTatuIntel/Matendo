<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Document;

class NewDocumentUploaded extends Notification
{
    use Queueable;

    protected $document;
    protected $uploaderName;

    /**
     * Create a new notification instance.
     */
    public function __construct(Document $document, $uploaderName = null)
    {
        $this->document = $document;
        $this->uploaderName = $uploaderName ?? 'Your doctor';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Document Uploaded to Your Medical Records')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->uploaderName . ' has uploaded a new document to your medical records.')
            ->line('Document: ' . $this->document->title)
            ->line('Category: ' . ucwords(str_replace('_', ' ', $this->document->category)))
            ->line('File: ' . $this->document->file_name)
            ->when($this->document->description, function ($mail) {
                return $mail->line('Description: ' . $this->document->description);
            })
            ->action('View Your Documents', route('patient.documents.index'))
            ->line('You can access this document anytime from your patient portal.')
            ->salutation('Best regards, Medical Monitor Team');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'document_id' => $this->document->id,
            'title' => $this->document->title,
            'category' => $this->document->category,
            'file_name' => $this->document->file_name,
            'uploader_name' => $this->uploaderName,
            'message' => $this->uploaderName . ' uploaded a new document: ' . $this->document->title,
            'action_url' => route('patient.documents.show', $this->document->id),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
