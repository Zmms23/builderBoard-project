<?php

namespace App\Mail;

use App\Models\ProofUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ClientProofUploadMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ProofUpload $proofUpload)
    {
        $this->proofUpload->loadMissing(['company', 'order.client', 'project']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.client_proof_upload.subject', [
                'company' => $this->proofUpload->company?->name ?? config('app.name'),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.client-proof-upload',
            with: [
                'client' => $this->proofUpload->order?->client,
                'company' => $this->proofUpload->company,
                'order' => $this->proofUpload->order,
                'photoUrl' => $this->photoUrl(),
                'project' => $this->proofUpload->project,
                'proofUpload' => $this->proofUpload,
            ],
        );
    }

    private function photoUrl(): ?string
    {
        if (blank($this->proofUpload->photo_path)) {
            return null;
        }

        return asset(Storage::disk('public')->url($this->proofUpload->photo_path));
    }
}
