<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\Country;
use App\Models\Course;
use App\Models\Degree;
use App\Models\StudentAdmission;
use App\Models\StudentCollegeApplication;
use App\Models\StudentInterestedCourse;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CollegeApplicationRelationManager extends RelationManager
{
    protected static string $relationship = 'collegeApplication';

    protected static ?string $label = '';

    protected static ?string $title = 'College Application';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('course_id')
                    ->relationship('course')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $get) {
                        // Clear the country when the degree changes
                        $set('country_id', null);
                        $set('college_id', null);
                        $set('campus_id', null);
                        $set('min_eligibility', null);
                        $set('duration', null);
                        $set('facility', null);
                        $set('document', null);
                        $set('fees', null);
                        $set('eligibility', null);
                    })
                    ->options(function ($get, $livewire) {

                        $degrees = Degree::whereHas('courses', function ($query) use ($livewire) {
                            $query->whereHas('interestedCourse', function ($subQuery) use ($livewire) {
                                // Filter courses that are in StudentInterestedCourse for the selected student
                                $subQuery->where('student_id', $livewire->ownerRecord->id);
                            });
                        });
                        if ($livewire->mountedTableActions[0] != 'view') {

                            $degrees = $degrees->whereHas('courses.interestedCourse', function ($query) use ($livewire) {
                                // Filter interested courses that are NOT used in any college application for the student
                                $query->where('student_id', $livewire->ownerRecord->id)
                                    ->whereNotIn('id', function ($subQuery) use ($livewire) {
                                        // Subquery to exclude courses used in college applications for the student
                                        $subQuery->select('interested_course_id')
                                            ->from('student_college_applications')
                                            ->where('student_id', $livewire->ownerRecord->id);
                                    });
                            });
                        }


                        $degrees = $degrees->pluck('name', 'id')
                            ->toArray();
                        return $degrees;
                    }),
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->required()
                    ->reactive()
                    ->options(function (callable $get, $livewire) {
                        // Dynamically load country options based on the selected course
                        $courseId = $get('course_id');

                        if (!$courseId) {
                            return [];
                        }

                        $countries = Country::whereHas('studentInterestedCourses', function ($query) use ($courseId, $livewire) {
                            $query->where('degree_id', $courseId)
                                ->where('student_id', $livewire->ownerRecord->id);
                        });

                        if ($livewire->mountedTableActions[0] != 'view') {
                            $countries = $countries->whereHas('course.interestedCourse', function ($query) use ($livewire) {
                                // Filter interested courses that are NOT used in any college application for the student
                                $query->where('student_id', $livewire->ownerRecord->id)
                                    ->whereNotIn('id', function ($subQuery) use ($livewire) {
                                        // Subquery to exclude courses used in college applications for the student
                                        $subQuery->select('interested_course_id')
                                            ->from('student_college_applications')
                                            ->where('student_id', $livewire->ownerRecord->id);
                                    });
                            });
                        }

                        $countries = $countries->pluck('name', 'id')->toArray();

                        // Fetch countries associated with the selected course's degree
                        return $countries;
                    })
                    ->afterStateUpdated(function (callable $set, $get, $livewire) {
                        $course = StudentInterestedCourse::with('course')->where([
                            'country_id' => $get('country_id'),
                            'degree_id' => $get('course_id'),
                            'student_id' => $livewire->ownerRecord->id,
                        ])->get();
                        if (!$course->isEmpty()) {
                            $set('degree_id', $course[0]->degree_id);
                            $set('course_id', $course[0]->course_id);
                            $set('interested_course_id', $course[0]->id);
                            $set('course_id', $course[0]->course_id);
                            $set('college_id', $course[0]->college_id);
                            $set('campus_id', $course[0]->campus_id);
                            $set('min_eligibility', $course[0]->eligibility_id);
                            $set('duration', $course[0]->duration);
                            $set('facility', $course[0]->facility);
                            $set('document', $course[0]->document);
                            $set('fees', $course[0]->fees);
                            $set('eligibility', $course[0]->eligibility);
                        } else {
                            $set('college_id', null);
                            $set('campus_id', null);
                            $set('min_eligibility', null);
                            $set('duration', null);
                            $set('facility', null);
                            $set('document', null);
                            $set('fees', null);
                            $set('eligibility', null);
                        }
                    }),
                // Only dehydrate (save) the value if it's not null,,
                Forms\Components\Hidden::make('interested_course_id')->reactive(),
                Forms\Components\Hidden::make('degree_id')->reactive(),
                Forms\Components\Select::make('college_id')
                    ->disabled()
                    ->relationship('college', 'college_name')
                    ->required()
                    ->reactive()
                    ->disabled()
                    ->dehydrateStateUsing(function ($state) {
                        return $state;
                    }) // Force the value to be null when saving
                    ->dehydrated(), // Only dehydrate (save) the value if it's not null,,

                Forms\Components\Select::make('campus_id')
                    ->disabled()
                    ->relationship('campus', 'campus_name')
                    ->reactive()
                    ->required()
                    ->dehydrateStateUsing(function ($state) {
                        return $state;
                    }) // Force the value to be null when saving
                    ->dehydrated(), // Only dehydrate (save) the value if it's not null,,
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
                    ->nullable(),
                Forms\Components\TextInput::make('fees_currency')
                    ->nullable()
                    ->readOnly(),
                Forms\Components\TextInput::make('eligibility')
                    ->nullable()
                    ->readOnly(),
                Forms\Components\Select::make('status')
                    ->options(config('app.collegeAppStatus'))
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

                Forms\Components\DatePicker::make('app_date')
                    ->nullable(),
                Forms\Components\TextInput::make('app_number')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('course.degree.name')
            ->columns([
                Tables\Columns\TextColumn::make('app_number')->label('Application Number')->toggleable(),
                Tables\Columns\TextColumn::make('app_date')->date()->toggleable(),
                Tables\Columns\TextColumn::make('country.name')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('course.degree.name')->label('Course')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('college.college_name')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('campus.campus_name')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('minEligibility.name')->label('Min. Eligibility')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('duration')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('facility')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('document')->toggleable(),
                // Tables\Columns\TextColumn::make('fees')->toggleable(),
                Tables\Columns\TextColumn::make('fees')->formatStateUsing(function ($record) {
                    return $record->fees . '(' . $record->country->currency . ')';
                })->label('Fees')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->toggleable()
                    ->action(
                        Action::make('Status')->form([
                            Select::make('status')
                                ->options(config('app.collegeAppStatus'))
                                ->required(),
                            Select::make('is_move_to_admission')->options(['Yes' => 'Yes', 'No' => 'No'])->required(),
                            Select::make('allocate_to')->options(['Yes' => 'Yes', 'No' => 'No'])->required()->reactive(),
                            Select::make('allocated_user')->label('Allocate To')->options(
                                function () {
                                    $users = \App\Models\User::where('status', 'Active')->whereHas('roles', function ($query) {
                                        $query->whereIn('name', ['Admin', 'Staff']);
                                    })->get();
                                    return $users->pluck('name', 'id');
                                }

                            )->visible(fn($get) => $get('allocate_to') === 'Yes'),
                            Textarea::make('note')->nullable()->visible(fn($get) => $get('allocate_to') === 'Yes'),

                        ])->action(function (StudentCollegeApplication $record, array $data) {
                            $record->status = $data['status'];
                            $record->is_move_to_admission = $data['is_move_to_admission'];
                            $record->allocate_to = $data['allocate_to'];
                            if (isset($data['allocated_user']) && !empty($data['allocated_user'])) {
                                $record->allocated_user = $data['allocated_user'];
                            }
                            $record->note = $data['note'];
                            $record->save();

                            $admission  = new StudentAdmission();
                            $adData = [
                                'application_id' => $record->id,
                                'student_id' => $record->student_id,
                                'country_id' => $record->country_id,
                                'course_id' => $record->course_id,
                                'college_id' => $record->college_id,
                                'campus_id' => $record->campus_id,
                                'degree_id' => $record->degree_id,
                                'min_eligibility'  => $record->min_eligibility,
                                'duration'  => $record->duration,
                                'facility' => $record->facility,
                                'document' => $record->document,
                                'fees' => $record->fees,
                                'fees_currency' => $record->country->currency,
                                'status' => 'New',
                                'reference_portal_id'  => $record->reference_portal_id,
                                'ref_link'  => $record->ref_link,
                                'eligibility'  => $record->eligibility,
                                'intakemonth_id'  => $record->intakemonth_id,
                                'intakeyear_id'  => $record->intakeyear_id,
                                'created_by'  => Auth::id(),
                                'app_number' => $record->app_number,
                            ];
                            $admission->create($adData);
                            Notification::make()
                                ->title('Application moved to admission successfully.')
                                ->success()
                                ->send();
                        })->mountUsing(function (Forms\ComponentContainer $form, Model $record) {
                            // Load existing payments data into the form
                            $form->fill([
                                'isVerified' => $record->isVerified,
                                'remark'  => $record->remark,
                                'status' => $record->status,
                                'is_move_to_admission' => $record->is_move_to_admission,
                                'allocate_to' => $record->allocate_to,
                                'allocated_user' => $record->allocated_user,
                                'note' => $record->note
                            ]);
                        })
                    )->color(function ($record) {
                        if ($record->is_move_to_admission == 'Yes') {
                            return 'gray';
                        }
                    })
                    ->formatStateUsing(function ($record) {
                        if ($record->is_move_to_admission == 'Yes') {
                            return '<span class="text-grey-500">' . $record->status . '</span>';
                        }
                        return $record->status;
                    })->html(),
                Tables\Columns\TextColumn::make('remark'),
                Tables\Columns\TextColumn::make('referencePortal.name')->toggleable(),
                Tables\Columns\TextColumn::make('ref_link')->toggleable(),
                Tables\Columns\TextColumn::make('eligibility')->toggleable(),
                Tables\Columns\TextColumn::make('intakeMonth.inmonth_name')->toggleable(),
                Tables\Columns\TextColumn::make('intakeYear.inyear_name')->toggleable(),
                Tables\Columns\TextColumn::make('createdBy.name')->toggleable(),
                Tables\Columns\TextColumn::make('updatedBy.name')->toggleable(),
            ])
            ->recordClasses(function ($record) {
                if ($record->is_move_to_admission == 'Yes') {
                    return  'custom-strikethrough';
                }
            })
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
            ]);
    }
}
