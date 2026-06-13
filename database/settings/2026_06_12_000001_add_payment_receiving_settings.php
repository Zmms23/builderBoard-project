<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('company.cash_payments_enabled', true);
        $this->migrator->add('company.bank_transfer_enabled', true);
        $this->migrator->add('company.bank_name', null);
        $this->migrator->add('company.bank_account_name', null);
        $this->migrator->add('company.bank_account_number', null);
        $this->migrator->add('company.payment_instructions', null);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('company.cash_payments_enabled');
        $this->migrator->deleteIfExists('company.bank_transfer_enabled');
        $this->migrator->deleteIfExists('company.bank_name');
        $this->migrator->deleteIfExists('company.bank_account_name');
        $this->migrator->deleteIfExists('company.bank_account_number');
        $this->migrator->deleteIfExists('company.payment_instructions');
    }
};
