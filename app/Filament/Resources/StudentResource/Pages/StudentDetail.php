<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\LeadResource\RelationManagers\LeadDocsRelationManager;
use App\Filament\Resources\LeadResource\RelationManagers\LeadRemindersRelationManager;
use App\Filament\Resources\StudentResource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Concerns\HasRelationManagers;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class StudentDetail extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms, WithFileUploads, InteractsWithInfolists, InteractsWithRecord, HasRelationManagers;

    protected static string $resource = StudentResource::class;

    protected static string $view = 'filament.resources.student-resource.pages.student-detail';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function getSubheading(): string|Htmlable|null
    {
        $svg = svg('heroicon-o-user')->toHtml();
        return new HtmlString("
        <div class=\"flex items-center\">
            <strong>{$this->record->first_name} {$this->record->last_name} {$this->record->enrollment_number}</strong>
        </div>
    ");
        return new HtmlString('<i class="fa fa-user"></i> <strong>' . e($this->record->first_name . ' ' . $this->record->last_name . ' ' . $this->record->enrollment_number) . '</strong>');
    }
    public function getInfolist(string $name): ?Infolist
    {
        return Infolist::make()
            ->record($this->record) // Use $this->record to access the current record
            ->schema([
                Section::make([
                    'default' => 1,
                    'sm' => 2,
                    'md' => 3,
                    'lg' => 4,
                    'xl' => 6,
                    '2xl' => 8,
                ])
                    ->schema([
                        TextEntry::make('enrollment_number')
                            ->label('Enrollemnt No'),
                        TextEntry::make('enrollment_date')
                            ->label('Enrollemnt Date'),
                        TextEntry::make('mobile')
                            ->label('Mobile'),
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('preferredCountry.name')
                            ->label('Preffered Country'),
                        TextEntry::make('purpose.purpose_name')
                            ->label('Purpose'),
                        TextEntry::make('service.service_name')
                            ->label('Preffered Service'),


                    ])->columns(3)

            ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }
}
