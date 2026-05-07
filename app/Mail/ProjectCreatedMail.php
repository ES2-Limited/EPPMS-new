<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Project $project,
        public ?string $projectUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: (app_organisation()->name ?? 'ePPMS').' New Project Created: '.$this->project->name);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.project-created');
    }
}
