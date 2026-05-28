<?php

namespace App\Settings;

use App\Enums\Currency;
use Spatie\LaravelSettings\Settings;

class CompanySettings extends Settings
{
    public ?string $logo_path = null;

    public ?string $phone = null;

    public ?string $email = null;

    public ?string $address = null;

    public ?string $website = null;

    public Currency $currency = Currency::GEL;

    public string $primary_color = '#f59e0b';

    public bool $client_progress_enabled = true;

    public bool $budget_tracking_enabled = true;

    public bool $proof_upload_enabled = true;

    public bool $chat_enabled = false;

    public bool $reviews_enabled = false;

    public static function group(): string
    {
        return 'company';
    }
}
