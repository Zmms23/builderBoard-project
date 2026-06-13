<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('company.client_email_notifications_enabled', true);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('company.client_email_notifications_enabled');
    }
};
