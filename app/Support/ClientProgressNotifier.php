<?php

namespace App\Support;

use App\Mail\ClientProofUploadMail;
use App\Models\Client;
use App\Models\ProofUpload;
use App\Settings\CompanySettings;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ClientProgressNotifier
{
    public function sendProofUploadNotification(ProofUpload $proofUpload): void
    {
        $settings = app(CompanySettings::class);

        if (! $settings->client_progress_enabled
            || ! $settings->client_email_notifications_enabled
            || ! $settings->proof_upload_enabled
            || ! $proofUpload->is_client_visible
        ) {
            return;
        }

        $proofUpload->loadMissing(['company', 'order.client', 'project']);

        $client = $proofUpload->order?->client;

        if (! $client instanceof Client || blank($client->email)) {
            return;
        }

        try {
            Mail::to($client->email, $client->name)
                ->send(new ClientProofUploadMail($proofUpload));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
