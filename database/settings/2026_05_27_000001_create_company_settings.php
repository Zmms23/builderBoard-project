<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('company.logo_path', null);
        $this->migrator->add('company.phone', null);
        $this->migrator->add('company.email', null);
        $this->migrator->add('company.address', null);
        $this->migrator->add('company.website', null);
        $this->migrator->add('company.currency', 'GEL');
        $this->migrator->add('company.primary_color', '#f59e0b');
        $this->migrator->add('company.client_progress_enabled', true);
        $this->migrator->add('company.budget_tracking_enabled', true);
        $this->migrator->add('company.proof_upload_enabled', true);
        $this->migrator->add('company.chat_enabled', false);
        $this->migrator->add('company.reviews_enabled', false);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('company.logo_path');
        $this->migrator->deleteIfExists('company.phone');
        $this->migrator->deleteIfExists('company.email');
        $this->migrator->deleteIfExists('company.address');
        $this->migrator->deleteIfExists('company.website');
        $this->migrator->deleteIfExists('company.currency');
        $this->migrator->deleteIfExists('company.primary_color');
        $this->migrator->deleteIfExists('company.client_progress_enabled');
        $this->migrator->deleteIfExists('company.budget_tracking_enabled');
        $this->migrator->deleteIfExists('company.proof_upload_enabled');
        $this->migrator->deleteIfExists('company.chat_enabled');
        $this->migrator->deleteIfExists('company.reviews_enabled');
    }
};
