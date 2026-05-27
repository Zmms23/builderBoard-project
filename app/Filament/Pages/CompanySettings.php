<?php

namespace App\Filament\Pages;

use App\Enums\Currency;
use App\Models\Company;
use App\Models\CompanySetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property-read Schema $form
 */
class CompanySettings extends Page
{
    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('settings.navigation.label');
    }

    public function getTitle(): string | Htmlable
    {
        return __('settings.pages.company_settings');
    }

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->form->fill($this->getCompanySetting()->attributesToArray());
    }

    public function save(): void
    {
        $this->getCompanySetting()->update($this->form->getState());

        Notification::make()
            ->success()
            ->title(__('settings.notifications.saved'))
            ->send();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->model($this->getCompanySetting())
            ->operation('edit')
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('settings.sections.profile'))
                    ->schema([
                        TextInput::make('phone')
                            ->label(__('settings.fields.phone'))
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label(__('settings.fields.email'))
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
                            ->options(Currency::class)
                            ->required(),
                        ColorPicker::make('primary_color')
                            ->label(__('settings.fields.primary_color'))
                            ->required(),
                    ])
                    ->columns(2),
                Section::make(__('settings.sections.features'))
                    ->schema([
                        Toggle::make('client_progress_enabled')
                            ->label(__('settings.fields.client_progress_enabled')),
                        Toggle::make('budget_tracking_enabled')
                            ->label(__('settings.fields.budget_tracking_enabled')),
                        Toggle::make('proof_upload_enabled')
                            ->label(__('settings.fields.proof_upload_enabled')),
                        Toggle::make('chat_enabled')
                            ->label(__('settings.fields.chat_enabled')),
                        Toggle::make('reviews_enabled')
                            ->label(__('settings.fields.reviews_enabled')),
                    ])
                    ->columns(2),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make($this->getFormActions())
                    ->key('form-actions'),
            ]);
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('settings.actions.save'))
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    protected function getCompanySetting(): CompanySetting
    {
        $tenant = Filament::getTenant();

        abort_unless($tenant instanceof Company, 404);

        return $tenant->setting()->firstOrCreate();
    }
}
