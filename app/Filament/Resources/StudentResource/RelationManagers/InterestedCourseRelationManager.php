<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\StudentInterestedCourse;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction as ActionsCreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class InterestedCourseRelationManager extends RelationManager
{
    protected static string $relationship = 'interestedCourse';

    protected static ?string $label = '';

    protected static ?string $title = 'Interested Course';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->required()
                    ->reactive()
                    ->options(function ($get) {
                        $studentId = $this->getOwnerRecord()->id;
                        $preferredCountryId = \App\Models\Student::find($studentId)->preferred_country_id;

                        $countriesQuery = \App\Models\Country::query();

                        if ($preferredCountryId) {
                            // Order the preferred country first, then the rest
                            $countriesQuery->orderByRaw("id = ? DESC", [$preferredCountryId]);
                        }

                        $countries = $countriesQuery->get();

                        return $countries->pluck('name', 'id');
                    })
                    ->afterStateUpdated(function (callable $set) {
                        $set('college_id', null);
                        $set('campus_id', null);
                        $set('min_eligibility', null);
                        $set('duration', null);
                        $set('facility', null);
                        $set('document', null);
                        $set('fees', null);
                        $set('eligibility', null);
                    }),
                Forms\Components\Select::make('course_id')
                    ->relationship('course', 'name', function ($query, $get) {
                        // Apply a constraint to filter courses based on selected country
                        return $query->where('country_id', $get('country_id'));
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('college_id', null);
                        $set('campus_id', null);
                        $set('min_eligibility', null);
                        $set('duration', null);
                        $set('facility', null);
                        $set('document', null);
                        $set('fees', null);
                        $set('eligibility', null);
                    })
                    ->options(function ($get) {
                        $courses = \App\Models\Course::where('country_id', $get('country_id'))->get();
                        return $courses->pluck('degree.name', 'degree.id');
                    }),
                Forms\Components\Select::make('college_id')
                    ->relationship('college', 'name', function ($query, $get) {
                        // Apply a constraint to filter colleges based on selected course
                        return $query->where('course_id', $get('course_id'));
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $get) {
                        $set('campus_id', null);
                        $set('min_eligibility', null);
                        $set('duration', null);
                        $set('facility', null);
                        $set('document', null);
                        $set('fees', null);
                        $set('eligibility', null);
                    })
                    ->options(function ($get, callable $set) {
                        $colleges = \App\Models\Course::with('college')->where('country_id', $get('country_id'))->where('degree_id', $get('course_id'))->get();
                        $set('selected_course_id', @$colleges[0]->id);
                        return $colleges->pluck('college.college_name', 'college.id');
                    }),
                Forms\Components\Hidden::make('selected_course_id')->reactive(),

                Forms\Components\Select::make('campus_id')
                    ->relationship('campus', 'campus_name', function ($query, $get) {
                        // Apply a constraint to filter campuses based on selected college
                        return $query->where('college_id', $get('college_id'));
                    })
                    ->reactive()
                    ->required()
                    ->afterStateUpdated(function (callable $set, $get) {
                        $set('min_eligibility', null);
                        $set('duration', null);
                        $set('facility', null);
                        $set('document', null);
                        $set('fees', null);
                        $set('eligibility', null);

                        if ($get('selected_course_id') && $get('campus_id')) {
                            $course = \App\Models\Course::with(['minEligibility'])->where('id', $get('selected_course_id'))->where('campus_id', $get('campus_id'))->first();
                            if ($course) {
                                $course = $course->toArray();
                                $set('min_eligibility', $course['min_eligibility']['id']);
                                $set('duration', $course['duration']);
                                $set('facility', $course['facility']);
                                $set('document', $course['document']);
                                $set('fees', $course['fees']);
                                $set('eligibility', $course['eligibility']);
                            }
                        }
                    })
                    ->options(function ($get) {
                        $campuses = \App\Models\Campus::where('college_id', $get('college_id'))->get();
                        return $campuses->pluck('campus_name', 'id');
                    }),
                Forms\Components\Select::make('min_eligibility')
                    ->relationship('minEligibility', 'name')
                    ->disabled()
                    ->label('Min Eligibility')
                    ->nullable()
                    ->dehydrateStateUsing(function ($state) {
                        return $state;
                    }) // Force the value to be null when saving
                    ->dehydrated(), // Only dehydrate (save) the value if it's not null,
                Forms\Components\TextInput::make('duration')
                    ->nullable()
                    ->readOnly(),
                Forms\Components\Textarea::make('facility')
                    ->nullable()
                    ->readOnly(),
                Forms\Components\Textarea::make('document')
                    ->nullable()
                    ->readOnly(),
                Forms\Components\TextInput::make('fees')
                    ->nullable()
                    ->readOnly(),
                Forms\Components\TextInput::make('eligibility')
                    ->nullable()
                    ->readOnly(),
                Forms\Components\Select::make('status')
                    ->options(config('app.interestedCourseStatus'))
                    ->required(),
                Forms\Components\Textarea::make('remark')
                    ->nullable(),
                Forms\Components\Select::make('reference_portal_id')
                    ->relationship('referencePortal', 'name')
                    ->nullable()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $get) {
                        $refPortal = \App\Models\ReferencePortal::where('id', $get('reference_portal_id'))->first();
                        $set('ref_link', null);
                        if ($refPortal) {
                            $set('ref_link', $refPortal->reference_link);
                        }
                    }),
                Forms\Components\TextInput::make('ref_link')
                    ->nullable(),

                Forms\Components\Select::make('intakemonth_id')
                    ->relationship('intakeMonth', 'inmonth_name')
                    ->nullable(),
                Forms\Components\Select::make('intakeyear_id')
                    ->relationship('intakeYear', 'inyear_name')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('course.degree.name')
            ->columns([
                Tables\Columns\TextColumn::make('country.name')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('course.degree.name')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('college.college_name')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('campus.campus_name')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('minEligibility.name')->label('Min. Eligibility')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('duration')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('facility')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('document')->toggleable(),
                Tables\Columns\TextColumn::make('fees')->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->toggleable()
                    ->action(
                        Action::make('Status')->form([
                            Select::make('status')->options(config('app.interestedCourseStatus'))->required(),
                            Select::make('is_move_to_application')->options(['Yes' => 'Yes', 'No' => 'No'])->required(),
                            Select::make('allocate_to')->options(['Yes' => 'Yes', 'No' => 'No'])->required(),
                            Select::make('allocated_user')->label('Allocate To')->options(
                                function () {
                                    $users = \App\Models\User::where('status', 'Active')->whereHas('roles', function ($query) {
                                        $query->whereIn('name', ['Admin', 'Staff']);
                                    })->get();
                                    return $users->pluck('name', 'id');
                                }

                            ),
                            Textarea::make('note')->nullable()

                        ])->action(function (StudentInterestedCourse $record, array $data) {
                            $record->isVerified = $data['isVerified'];
                            $record->remark = $data['remark'] ?? null; // Assuming `remark` is a column in your model
                            if ($data['isVerified'] == 'Verified') {
                                $record->verified_by = Auth::id();
                                $record->verified_date = Carbon::now();
                            } else {
                                $record->verified_by = null;
                                $record->verified_date = null;
                            }
                            $record->save();
                        })->mountUsing(function (Forms\ComponentContainer $form, Model $record) {
                            // Load existing payments data into the form
                            $form->fill([
                                'isVerified' => $record->isVerified,
                                'remark'  => $record->remark
                            ]);
                        })
                    ),
                Tables\Columns\TextColumn::make('remark'),
                Tables\Columns\TextColumn::make('referencePortal.name')->toggleable(),
                Tables\Columns\TextColumn::make('ref_link')->toggleable(),
                Tables\Columns\TextColumn::make('eligibility')->toggleable(),
                Tables\Columns\TextColumn::make('intakeMonth.inmonth_name')->toggleable(),
                Tables\Columns\TextColumn::make('intakeYear.inyear_name')->toggleable(),
                Tables\Columns\TextColumn::make('createdBy.name')->toggleable(),
                Tables\Columns\TextColumn::make('updatedBy.name')->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make()
            ]);
    }
}
