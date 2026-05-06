<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectAssignmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Project $project,
        public string $projectRole,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Project Assignment: '.$this->project->name);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.project-assignment');
    }
}
