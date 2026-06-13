<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ProofUpload;
use App\Settings\CompanySettings;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ProofUploadPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->enabled()
            && $authUser->can('ViewAny:ProofUpload');
    }

    public function view(AuthUser $authUser, ProofUpload $proofUpload): bool
    {
        return $this->enabled()
            && $authUser->can('View:ProofUpload');
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->enabled()
            && $authUser->can('Create:ProofUpload');
    }

    public function update(AuthUser $authUser, ProofUpload $proofUpload): bool
    {
        return $this->enabled()
            && $authUser->can('Update:ProofUpload');
    }

    public function delete(AuthUser $authUser, ProofUpload $proofUpload): bool
    {
        return $this->enabled()
            && $authUser->can('Delete:ProofUpload');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->enabled()
            && $authUser->can('DeleteAny:ProofUpload');
    }

    public function restore(AuthUser $authUser, ProofUpload $proofUpload): bool
    {
        return $this->enabled()
            && $authUser->can('Restore:ProofUpload');
    }

    public function forceDelete(AuthUser $authUser, ProofUpload $proofUpload): bool
    {
        return $this->enabled()
            && $authUser->can('ForceDelete:ProofUpload');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $this->enabled()
            && $authUser->can('ForceDeleteAny:ProofUpload');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $this->enabled()
            && $authUser->can('RestoreAny:ProofUpload');
    }

    public function replicate(AuthUser $authUser, ProofUpload $proofUpload): bool
    {
        return $this->enabled()
            && $authUser->can('Replicate:ProofUpload');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $this->enabled()
            && $authUser->can('Reorder:ProofUpload');
    }

    private function enabled(): bool
    {
        return app(CompanySettings::class)->proof_upload_enabled;
    }
}
