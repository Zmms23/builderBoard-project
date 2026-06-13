<?php

namespace App\Filament\Pages;

use App\Enums\Currency;
use App\Settings\CompanySettings as CompanySettingsData;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class CompanySettings extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = CompanySettingsData::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('settings.navigation.label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('settings.pages.company_settings');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('settings.sections.profile'))
                    ->schema([
                        FileUpload::make('logo_path')
                            ->label(__('settings.fields.logo'))
                            ->image()
                            ->disk('public')
                            ->directory('company-logos')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                        TextInput::make('phone')
                            ->label(__('settings.fields.phone'))
                            ->helperText(__('settings.help.phone'))
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('settings.fields.email'))
                            ->helperText(__('settings.help.email'))
                            ->email()
                            ->maxLength(255),
                        TextInput::make('address')
                            ->label(__('settings.fields.address'))
                            ->maxLength(255),
                        TextInput::make('website')
                            ->label(__('settings.fields.website'))
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make(__('settings.sections.branding'))
                    ->schema([
                        Select::make('currency')
                            ->label(__('settings.fields.currency'))
                            ->helperText(__('settings.help.currency'))
                            ->options(Currency::class)
                            ->required(),
                        ColorPicker::make('primary_color')
                            ->label(__('settings.fields.primary_color'))
                            ->helperText(__('settings.help.primary_color'))
                            ->required(),
                    ])
                    ->columns(2),
                Section::make(__('settings.sections.features'))
                    ->schema([
                        Toggle::make('client_progress_enabled')
                            ->label(__('settings.fields.client_progress_enabled'))
                            ->helperText(__('settings.help.client_progress_enabled')),
                        Toggle::make('budget_tracking_enabled')
                            ->label(__('settings.fields.budget_tracking_enabled'))
                            ->helperText(__('settings.help.budget_tracking_enabled')),
                        Toggle::make('proof_upload_enabled')
                            ->label(__('settings.fields.proof_upload_enabled'))
                            ->helperText(__('settings.help.proof_upload_enabled')),
                        Toggle::make('chat_enabled')
                            ->label(__('settings.fields.chat_enabled'))
                            ->helperText(__('settings.help.chat_enabled')),
                        Toggle::make('reviews_enabled')
                            ->label(__('settings.fields.reviews_enabled'))
                            ->helperText(__('settings.help.reviews_enabled')),
                    ])
                    ->columns(2),
                Section::make(__('settings.sections.payment_receiving'))
                    ->description(__('settings.sections.payment_receiving_description'))
                    ->schema([
                        Toggle::make('cash_payments_enabled')
                            ->label(__('settings.fields.cash_payments_enabled'))
                            ->helperText(__('settings.help.cash_payments_enabled')),
                        Toggle::make('bank_transfer_enabled')
                            ->label(__('settings.fields.bank_transfer_enabled'))
                            ->helperText(__('settings.help.bank_transfer_enabled')),
                        TextInput::make('bank_name')
                            ->label(__('settings.fields.bank_name'))
                            ->maxLength(255),
                        TextInput::make('bank_account_name')
                            ->label(__('settings.fields.bank_account_name'))
                            ->maxLength(255),
                        TextInput::make('bank_account_number')
                            ->label(__('settings.fields.bank_account_number'))
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('payment_instructions')
                            ->label(__('settings.fields.payment_instructions'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function getSavedNotificationTitle(): ?string
    {
        return __('settings.notifications.saved');
    }
}
