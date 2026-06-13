<?php

namespace App\Observers;

use App\Models\ProofUpload;
use App\Support\ClientProgressNotifier;

class ProofUploadObserver
{
    public function created(ProofUpload $proofUpload): void
    {
        app(ClientProgressNotifier::class)->sendProofUploadNotification($proofUpload);
    }

    public function updated(ProofUpload $proofUpload): void
    {
        if (! $proofUpload->wasChanged('is_client_visible') || ! $proofUpload->is_client_visible) {
            return;
        }

        app(ClientProgressNotifier::class)->sendProofUploadNotification($proofUpload);
    }
}
