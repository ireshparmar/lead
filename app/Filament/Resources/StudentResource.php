<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers\CollegeApplicationRelationManager;
use App\Filament\Resources\StudentResource\RelationManagers\InterestedCourseRelationManager;
use App\Filament\Resources\StudentResource\RelationManagers\StudentAdmissionsRelationManager;
use App\Filament\Resources\StudentResource\RelationManagers\StudentAptitudeEntranceTestRelationManager;
use App\Filament\Resources\StudentResource\RelationManagers\StudentDocsRelationManager;
use App\Filament\Resources\StudentResource\RelationManagers\StudentEducationLevelsRelationManager;
use App\Filament\Resources\StudentResource\RelationManagers\StudentLanguageEntranceTestsRelationManager;
use App\Filament\Resources\StudentResource\RelationManagers\StudentVisaRelationManager;
use App\Filament\Resources\StudentResource\RelationManagers\StudentWorkExperienceRelationManager;
use App\Models\Student;
use App\Models\StudentLanguageEntranceTest;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Str;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Student Details')
                    ->schema([
                        Forms\Components\TextInput::make('enrollment_number')
                            ->label('Enrollment Number')
                            ->readOnly()
                            ->dehydrated(true)
                            ->required()
                            ->default(function () {
                                $currentYear = now()->year;
                                $nextYear = $currentYear + 1;

                                // Get the last student's enrollment number and extract the numeric part
                                $lastStudent = \App\Models\Student::latest('id')->first();
                                $lastNumber = 0;

                                if ($lastStudent) {
                                    // Adjust the regular expression pattern to match the format "NAND/YYYY-YYY/NNNN"
                                    $pattern = '/^NAND\/' . $currentYear . '-' . $nextYear . '\/(\d+)$/';
                                    // Check if the pattern matches the last enrollment number
                                    if (preg_match($pattern, $lastStudent->enrollment_number, $matches)) {
                                        $lastNumber = (int) $matches[1];
                                    }
                                }

                                // Increment the number
                                $newNumber = $lastNumber + 1;
                                // Generate the new enrollment number
                                return 'NAND/' . $currentYear . '-' . $nextYear . '/' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
                            }),
                        Forms\Components\DatePicker::make('enrollment_date')
                            ->label('Enrollment Date')
                            ->required(),
                        Forms\Components\TextInput::make('first_name')
                            ->label('First Name')
                            ->required(),
                        Forms\Components\TextInput::make('middle_name')
                            ->label('Middle Name'),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Last Name')
                            ->required(),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Birth Date')
                            ->required(),
                        PhoneInput::make('mobile')
                            ->defaultCountry('INDIA')
                            ->required()
                            ->validateFor(
                                country: 'AUTO', // default: 'AUTO'
                                lenient: true, // default: false
                            )
                            ->unique(ignoreRecord: true)
                            ->rules(['numeric']),
                        Forms\Components\Select::make('gender')
                            ->label('Gender')
                            ->options(config('app.gender'))->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('inquiry_source_id')
                            ->label('Inquiry Source')
                            ->relationship('inquirySource', 'insource_name', modifyQueryUsing: fn(Builder $query) => $query->active())->required(),
                        Forms\Components\TextInput::make('address')
                            ->label('Address')->required(),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Postal Code')->required(),
                        Forms\Components\Select::make('country_id')
                            ->label('Country')
                            ->relationship('country', 'name')
                            ->required()
                            ->reactive()
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Clear the state and city selection when country changes
                                $set('state_id', null);
                                $set('city_id', null);
                            }),

                        Forms\Components\Select::make('state_id')
                            ->label('State')
                            ->relationship('state', 'name')
                            ->required()
                            ->reactive()
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Clear the city selection when state changes
                                $set('city_id', null);
                            })
                            ->options(function (callable $get) {
                                $countryId = $get('country_id');

                                if ($countryId) {
                                    return \App\Models\State::where('country_id', $countryId)->pluck('name', 'id');
                                }

                                return [];
                            }),

                        Forms\Components\Select::make('city_id')
                            ->label('City')
                            ->relationship('city', 'name')
                            ->required()
                            ->reactive()
                            ->searchable()
                            ->preload()
                            ->options(function (callable $get) {
                                $stateId = $get('state_id');

                                if ($stateId) {
                                    return \App\Models\City::where('state_id', $stateId)->pluck('name', 'id');
                                }

                                return [];
                            }),
                        Forms\Components\Select::make('reference_by')
                            ->label('Reference By')
                            ->relationship('reference', 'first_name'),
                    ]),
                Fieldset::make('Other Details')
                    ->schema([
                        Forms\Components\Select::make('purpose_id')
                            ->label('Purpose')
                            ->relationship('purpose', 'purpose_name', modifyQueryUsing: fn(Builder $query) => $query->active())
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set) {
                                // Clear the service selection when purpose changes
                                $set('service_id', null);
                            }),

                        Forms\Components\Select::make('service_id')
                            ->label('Service')
                            ->relationship('service', 'service_name')
                            ->required()
                            ->reactive()
                            ->options(function (callable $get) {
                                $purposeId = $get('purpose_id');

                                if ($purposeId) {
                                    return \App\Models\Service::where('purpose_id', $purposeId)->where('status', 'Active')->pluck('service_name', 'id');
                                }

                                return [];
                            }),
                        Forms\Components\Select::make('pref_country_id')
                            ->label('Preferred Country')
                            ->searchable()
                            ->preload()
                            ->relationship('preferredCountry', 'name')->required(),
                        Forms\Components\TextInput::make('remark')
                            ->label('Remarks'),
                        Forms\Components\Select::make('agent_id')
                            ->label('Agent')
                            ->relationship('agent', 'name')
                            ->preload()
                            ->searchable(),
                    ]),
                Fieldset::make('Emergancy Contact Details')
                    ->schema([
                        Forms\Components\TextInput::make('emergency_name')
                            ->label('Emergency Contact Name'),
                        Forms\Components\TextInput::make('emergency_relation')
                            ->label('Emergency Contact Relation')
                            ->dehydrateStateUsing(fn(?string $state): ?string => $state ? ucwords($state) : null),
                        PhoneInput::make('emergency_contact_no')
                            ->label('Emergency Contact Number')
                            ->defaultCountry('INDIA')
                            ->validateFor(
                                country: 'AUTO', // default: 'AUTO'
                                lenient: true, // default: false
                            )
                            ->rules(['numeric']),
                        Forms\Components\TextInput::make('emergency_detail')
                            ->label('Emergency Contact Detail'),
                    ])



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('enrollment_number')->label('Enrollment Number')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('enrollment_date')->label('Enrollment Date')->date()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('first_name')->label('First Name')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('middle_name')->label('Middle Name')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('last_name')->label('Last Name')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('birth_date')->label('Birth Date')->date()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('mobile')->label('Mobile')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('gender')->label('Gender')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('address')->label('Address')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('postal_code')->label('Postal Code')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('preferredCountry.name')->label('Preferred Country')->toggleable()->sortable(),
                // Tables\Columns\TextColumn::make('state.name')->label('State')->toggleable()->sortable(),
                // Tables\Columns\TextColumn::make('city.name')->label('City')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('remark')->label('Remark')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('emergency_name')->label('Emergency Contact Name'),
                Tables\Columns\TextColumn::make('emergency_relation')->label('Emergency Contact Relation'),
                Tables\Columns\TextColumn::make('emergency_contact_code')->label('Emergency Contact Code'),
                Tables\Columns\TextColumn::make('emergency_contact_no')->label('Emergency Contact Number'),
                Tables\Columns\TextColumn::make('emergency_detail')->label('Emergency Contact Detail'),
                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By'),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By'),
            ])
            ->filters([
                // Filter::make('created_at')
                //     ->form([
                //         Forms\Components\Select::make('country_id')
                //             ->label('Country')
                //             ->relationship('country', 'name')
                //             ->required()
                //             ->reactive()
                //             ->searchable()
                //             ->preload()
                //             ->afterStateUpdated(function ($state, callable $set) {
                //                 // Clear the state and city selection when country changes
                //                 $set('state_id', null);
                //                 $set('city_id', null);
                //             })->multiple(),

                //         Forms\Components\Select::make('state_id')
                //             ->label('State')
                //             ->relationship('state', 'name')
                //             ->required()
                //             ->reactive()
                //             ->searchable()
                //             ->preload()
                //             ->afterStateUpdated(function ($state, callable $set) {
                //                 // Clear the city selection when state changes
                //                 $set('city_id', null);
                //             })
                //             ->options(function (callable $get) {
                //                 $countryId = $get('country_id');

                //                 if ($countryId) {
                //                     return \App\Models\State::where('country_id', $countryId)->pluck('name', 'id');
                //                 }

                //                 return [];
                //             })->multiple(),

                //         Forms\Components\Select::make('city_id')
                //             ->label('City')
                //             ->relationship('city', 'name')
                //             ->required()
                //             ->reactive()
                //             ->searchable()
                //             ->preload()
                //             ->options(function (callable $get) {
                //                 $stateId = $get('state_id');

                //                 if ($stateId) {
                //                     return \App\Models\City::where('state_id', $stateId)->pluck('name', 'id');
                //                 }

                //                 return [];
                //             })->multiple()
                //     ])->query(function (Builder $query, array $data): Builder {
                //         return $query
                //             ->when(
                //                 $data['country_id'],
                //                 fn(Builder $query, $date): Builder => $query->whereIn('country_id', $data['country_id']),
                //             )
                //             ->when(
                //                 $data['state_id'],
                //                 fn(Builder $query, $date): Builder => $query->whereIn('state_id', $data['state_id']),
                //             )
                //             ->when(
                //                 $data['city_id'],
                //                 fn(Builder $query, $date): Builder => $query->whereIn('city_id', $data['city_id']),
                //             );
                //     }),
                SelectFilter::make('country')->label('Preffered Country')->relationship('preferredCountry', 'name')->multiple()->preload()->searchable(),
                SelectFilter::make('gender')->options(config('app.gender'))->label('Gender')->multiple(),
                SelectFilter::make('purpose')->relationship('purpose', 'purpose_name')->multiple()->preload()->searchable(),
                SelectFilter::make('service')->relationship('service', 'service_name')->multiple()->preload()->searchable(),
                Filter::make('enrollment_date')
                    ->label('Enrollment Date')
                    ->form([
                        Forms\Components\DatePicker::make('enrollment_date_from')->label('From'),
                        Forms\Components\DatePicker::make('enrollment_date_to')->label('To'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['enrollment_date_from'], fn($query) => $query->whereDate('enrollment_date', '>=', $data['enrollment_date_from']))
                            ->when($data['enrollment_date_to'], fn($query) => $query->whereDate('enrollment_date', '<=', $data['enrollment_date_to']));
                    }),
            ])->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Action::make('detail')
                        ->icon('heroicon-o-document-magnifying-glass')
                        ->url(fn($record) => route('filament.admin.resources.students.detail', $record->id)),
                ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Personal Details', [
                StudentEducationLevelsRelationManager::class,
                StudentLanguageEntranceTestsRelationManager::class,
                StudentAptitudeEntranceTestRelationManager::class,
                StudentWorkExperienceRelationManager::class,
                StudentDocsRelationManager::class
            ]),
            RelationGroup::make('Counselling', [
                InterestedCourseRelationManager::class
            ]),
            RelationGroup::make('Application', [
                CollegeApplicationRelationManager::class
            ]),
            RelationGroup::make('Admission', [
                StudentAdmissionsRelationManager::class
            ]),
            RelationGroup::make('Visa', [
                StudentVisaRelationManager::class
            ]),


        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'detail' => Pages\StudentDetail::route('/{record}/detail'),
            'admission-documents' => Pages\AdmissionDocuments::route('/{record}/admission-documents'),  // Make sure this is registered properly

        ];
    }
}
