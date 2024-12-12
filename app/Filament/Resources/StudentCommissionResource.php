<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentCommissionResource\Pages;
use App\Filament\Resources\CommissionResource\RelationManagers;
use App\Helpers\CurrencyHelper;
use App\Models\Commission;
use App\Models\Student;
use App\Models\StudentAdmission;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use PhpParser\Node\Stmt\Label;

use function PHPUnit\Framework\returnValueMap;

class StudentCommissionResource extends Resource
{
    protected static ?string $model = Commission::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Commissions';

    protected static ?string $label = 'Student Commissions';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('commissionable_type')
                    ->default('student')
                    ->required()
                    ->reactive(), // Makes the field reactive to trigger changes in other fields
                Forms\Components\Hidden::make('admission_fees_currency')
                    ->reactive(),
                Forms\Components\Hidden::make('admission_id')
                    ->reactive(),
                Forms\Components\Select::make('commissionable_id')
                    ->label('Select Student')
                    ->options(function (callable $get) {
                        return \App\Models\Student::query()->whereHas('studentAdmissions')->get()->mapWithKeys(function ($student) {
                            return [$student->id => $student->full_name_with_enrollment];
                        });
                    })
                    ->reactive()
                    ->required()
                    ->searchable() // Optional: Makes the select searchable
                    ->placeholder('Select a student')->preload()
                    ->afterStateHydrated(function ($state, callable $set, callable $get) {
                        $studentId = $get('commissionable_id');
                        $type = $get('commissionable_type');
                        if ($studentId && $type === 'student') {
                            // Fetch the admission record with related data
                            $admission = \App\Models\StudentAdmission::with([
                                'degree',
                                'college_application',
                                'college',
                                'campus',
                                'intakeMonth',
                                'intakeYear',
                                'referencePortal',
                                'student.agent'
                            ])
                                ->where('student_id', $studentId)
                                ->first();

                            // Check if admission data exists and set field values
                            if ($admission) {

                                $set('admission_id', $admission->id);
                                // Convert currency and set Fees In Base Currency
                                $baseCurrency = config('app.base_currency'); // Define your base currency
                                $convertedAmount = CurrencyHelper::convert($admission->fees, $admission->fees_currency, $baseCurrency);
                                $set('admission_fees_currency', $admission->fees_currency);
                                $set('Fees In Base Currency', $convertedAmount ? $convertedAmount . ' ' . $baseCurrency : 'Conversion Failed');

                                $set('Admission', $admission->degree->name ?? 'No Admission Found');
                                $set('Application Date', $admission->college_application->app_date ?? 'Not Available');
                                $set('Status', $admission->status ?? 'Not Available');
                                $set('College', $admission->college->college_name ?? 'Not Available');
                                $set('Campus', $admission->campus->campus_name ?? 'Not Available');
                                $set('Intake Month', $admission->intakeMonth->inmonth_name ?? 'Not Available');
                                $set('Intake Year', $admission->intakeYear->inyear_name ?? 'Not Available');
                                $set('Fees', $admission->fees ? $admission->fees . ' ' . $admission->fees_currency : 'Not Available');
                                //$set('Fees In Base Currency', $admission->fees_in_base_currency ?? 'Not Available');
                                $set('Referral Portal', $admission->referencePortal->name ?? 'Not Available');
                                $set('Referral Portal Link', $admission->referencePortal->reference_link ?? 'Not Available');
                                $set('Agent', $admission->student->agent->name ?? 'Not Available');
                                $set('agent_id', $admission->student->agent_id ?? '');
                            } else {
                                // Clear all fields if no admission is found
                                $set('Admission', 'No Admission Found');
                                $set('Application Date', '');
                                $set('Status', '');
                                $set('College', '');
                                $set('Campus', '');
                                $set('Intake Month', '');
                                $set('Intake Year', '');
                                $set('Fees', '');
                                $set('Fees In Base Currency', '');
                                $set('Referral Portal', '');
                                $set('Referral Portal Link', '');
                                $set('admission_id', '');
                                $set('admission_fees_currency', '');
                            }
                        } else {
                            // Clear all fields if no student is selected
                            $set('Admission', '');
                            $set('Application Date', '');
                            $set('Status', '');
                            $set('College', '');
                            $set('Campus', '');
                            $set('Intake Month', '');
                            $set('Intake Year', '');
                            $set('Fees', '');
                            $set('Fees In Base Currency', '');
                            $set('Referral Portal', '');
                            $set('Referral Portal Link', '');
                            $set('admission_id', '');
                            $set('admission_fees_currency', '');
                        }
                    })
                    ->afterStateUpdated(function (string $context, $state, callable $set, callable $get) {
                        $studentId = $get('commissionable_id');
                        $type = $get('commissionable_type');

                        if ($studentId && $type === 'student') {
                            // Fetch the admission record with related data
                            $admission = \App\Models\StudentAdmission::with([
                                'degree',
                                'college_application',
                                'college',
                                'campus',
                                'intakeMonth',
                                'intakeYear',
                                'referencePortal',
                                'student.agent'
                            ])
                                ->where('student_id', $studentId)
                                ->first();

                            // Check if admission data exists and set field values
                            if ($admission) {

                                $set('admission_id', $admission->id);
                                // Convert currency and set Fees In Base Currency
                                $baseCurrency = config('app.base_currency'); // Define your base currency
                                $convertedAmount = CurrencyHelper::convert($admission->fees, $admission->fees_currency, $baseCurrency);

                                $set('Fees In Base Currency', $convertedAmount ? $convertedAmount . ' ' . $baseCurrency : 'Conversion Failed');

                                $set('admission_fees_currency', $admission->fees_currency);
                                $set('Admission', $admission->degree->name ?? 'No Admission Found');
                                $set('Application Date', $admission->college_application->app_date ?? 'Not Available');
                                $set('Status', $admission->status ?? 'Not Available');
                                $set('College', $admission->college->college_name ?? 'Not Available');
                                $set('Campus', $admission->campus->campus_name ?? 'Not Available');
                                $set('Intake Month', $admission->intakeMonth->inmonth_name ?? 'Not Available');
                                $set('Intake Year', $admission->intakeYear->inyear_name ?? 'Not Available');
                                $set('Fees', $admission->fees ? $admission->fees . ' ' . $admission->fees_currency : 'Not Available');
                                //$set('Fees In Base Currency', $admission->fees_in_base_currency ?? 'Not Available');
                                $set('Referral Portal', $admission->referencePortal->name ?? 'Not Available');
                                $set('Referral Portal Link', $admission->referencePortal->reference_link ?? 'Not Available');
                                $set('Agent', $admission->student->agent->name ?? 'Not Available');
                                $set('agent_id', $admission->student->agent_id ?? '');
                            } else {
                                // Clear all fields if no admission is found
                                $set('Admission', 'No Admission Found');
                                $set('Application Date', '');
                                $set('Status', '');
                                $set('College', '');
                                $set('Campus', '');
                                $set('Intake Month', '');
                                $set('Intake Year', '');
                                $set('Fees', '');
                                $set('Fees In Base Currency', '');
                                $set('Referral Portal', '');
                                $set('Referral Portal Link', '');
                                $set('admission_id', '');
                                $set('admission_fees_currency', '');
                            }
                        } else {
                            // Clear all fields if no student is selected
                            $set('Admission', '');
                            $set('Application Date', '');
                            $set('Status', '');
                            $set('College', '');
                            $set('Campus', '');
                            $set('Intake Month', '');
                            $set('Intake Year', '');
                            $set('Fees', '');
                            $set('Fees In Base Currency', '');
                            $set('Referral Portal', '');
                            $set('Referral Portal Link', '');
                            $set('admission_id', '');
                            $set('admission_fees_currency', '');
                        }
                    }),
                Forms\Components\TextInput::make('Admission')
                    ->disabled()
                    ->reactive()
                    ->visible(function (string $context, $state, callable $set, callable $get) {
                        return $get('commissionable_type') === 'student';
                    }),
                Forms\Components\TextInput::make('Application Date')
                    ->disabled()
                    ->reactive()
                    ->visible(function (string $context, $state, callable $set, callable $get) {
                        return $get('commissionable_type') === 'student';
                    }),
                Forms\Components\TextInput::make('Status')
                    ->disabled()
                    ->reactive()
                    ->visible(function (string $context, $state, callable $set, callable $get) {
                        return $get('commissionable_type') === 'student';
                    }),
                Forms\Components\TextInput::make('College')
                    ->disabled()
                    ->reactive()
                    ->visible(function (string $context, $state, callable $set, callable $get) {
                        return $get('commissionable_type') === 'student';
                    }),
                Forms\Components\TextInput::make('Campus')
                    ->disabled()
                    ->reactive()
                    ->visible(function (string $context, $state, callable $set, callable $get) {
                        return $get('commissionable_type') === 'student';
                    }),
                Forms\Components\TextInput::make('Intake Month')
                    ->disabled()
                    ->reactive()
                    ->visible(function (string $context, $state, callable $set, callable $get) {
                        return $get('commissionable_type') === 'student';
                    }),
                Forms\Components\TextInput::make('Intake Year')
                    ->disabled()
                    ->reactive()
                    ->visible(function (string $context, $state, callable $set, callable $get) {
                        return $get('commissionable_type') === 'student';
                    }),
                Forms\Components\TextInput::make('Fees')
                    ->disabled()
                    ->reactive()
                    ->visible(function (string $context, $state, callable $set, callable $get) {
                        return $get('commissionable_type') === 'student';
                    }),
                Forms\Components\TextInput::make('Fees In Base Currency')
                    ->disabled()
                    ->reactive()
                    ->visible(function (string $context, $state, callable $set, callable $get) {
                        return $get('commissionable_type') === 'student';
                    }),
                Forms\Components\TextInput::make('Referral Portal')
                    ->disabled()
                    ->reactive()
                    ->visible(function (string $context, $state, callable $set, callable $get) {
                        return $get('commissionable_type') === 'student';
                    }),
                Forms\Components\TextInput::make('Referral Portal Link')
                    ->disabled()
                    ->reactive()
                    ->visible(function (string $context, $state, callable $set, callable $get) {
                        return $get('commissionable_type') === 'student';
                    }),
                Forms\Components\TextInput::make('Agent')
                    ->disabled()
                    ->reactive()
                    ->visible(function (string $context, $state, callable $set, callable $get) {
                        return $get('commissionable_type') === 'student' && Student::whereHas('agent')->where('id', $get('commissionable_id'))->count() > 0;
                    }),
                Fieldset::make('Commission')
                    ->schema([
                        Forms\Components\Hidden::make('agent_id')->reactive(),
                        Forms\Components\Select::make('commission_type')
                            ->label('Commission Structure')
                            ->options(function (callable $get) {
                                if ($get('commissionable_type') === 'student') {
                                    return [
                                        'one-time' => 'One Time Commission',
                                        'semester-wise' => 'Semester Wise Commission',
                                    ];
                                } else {
                                    return [
                                        'one-time' => 'One Time Commission',
                                    ];
                                }
                            })
                            ->default('one-time')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $get, callable $set, $livewire) {
                                if ($livewire->data['commission_type'] === 'one-time') {
                                    $set('semesters', null);
                                } else {
                                    $set('semesters', [
                                        [],
                                    ]);
                                    $set('own_commission', null);
                                    $set('own_commission_in_base_currency', null);
                                    $set('agent_commission', null);
                                    $set('agent_commission_in_base_currency', null);
                                }
                            }),
                        Forms\Components\Select::make('admission_by')
                            ->label('Admission By')
                            ->options([
                                'Direct College' => 'Direct College',
                                'Refferal Portal' => 'Refferal Portal',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('remarks')->label('Remarks'),
                        Forms\Components\TextInput::make('own_commission')
                            ->label(fn(callable $get) => $get('admission_id')  ? 'Own Commission (' . $get('admission_fees_currency') . ')' : 'Own Commission')
                            ->visible(function ($context, $state, callable $set, callable $get) {
                                return $get('commission_type') === 'one-time';
                            })

                            ->numeric()
                            ->reactive()
                            ->afterStateUpdated(function (callable $get, callable $set, $livewire) {
                                // $studentAdmission = StudentAdmission::find($livewire->data['admission_id']);
                                if ($livewire->data['admission_id']) {
                                    $convertedAmount = CurrencyHelper::convert($get('own_commission'),  $livewire->data['admission_fees_currency'], config('app.base_currency'));
                                    $set('own_commission_in_base_currency', $convertedAmount);
                                }
                            })
                            ->afterStateHydrated(function ($state, callable $set, callable $get, $livewire) {
                                // $studentAdmission = StudentAdmission::find($livewire->data['admission_id']);
                                if (isset($livewire->data['admission_id'])) {
                                    $convertedAmount = CurrencyHelper::convert($get('own_commission'),  $livewire->data['admission_fees_currency'], config('app.base_currency'));
                                    $set('own_commission_in_base_currency', $convertedAmount);
                                }
                            }),
                        Forms\Components\TextInput::make('own_commission_in_base_currency')
                            ->label('Own Commission (' . config('app.base_currency') . ')')
                            ->numeric()
                            ->disabled()
                            ->reactive()
                            ->visible(function ($context, $state, callable $set, callable $get) {
                                return $get('commission_type') === 'one-time';
                            }),
                        Forms\Components\TextInput::make('agent_commission')
                            ->label(fn(callable $get) => $get('admission_id')  ? 'Agent Commission (' . $get('admission_fees_currency') . ')' : 'Agent Commission')
                            ->visible(function ($context, $state, callable $set, callable $get) {
                                return $get('commission_type') === 'one-time';
                            })

                            ->numeric()
                            ->reactive()
                            ->afterStateUpdated(function (callable $get, callable $set, $livewire) {
                                //$studentAdmission = StudentAdmission::find($livewire->data['admission_id']);
                                if ($livewire->data['admission_fees_currency']) {
                                    $convertedAmount = CurrencyHelper::convert($get('agent_commission'),  $livewire->data['admission_fees_currency'], config('app.base_currency'));
                                    $set('agent_commission_in_base_currency', $convertedAmount);
                                }
                            })
                            ->afterStateHydrated(function ($state, callable $set, callable $get, $livewire) {
                                //$studentAdmission = StudentAdmission::find($livewire->data['admission_id']);
                                if (isset($livewire->data['admission_fees_currency'])) {
                                    $convertedAmount = CurrencyHelper::convert($get('agent_commission'),  $livewire->data['admission_fees_currency'], config('app.base_currency'));
                                    $set('agent_commission_in_base_currency', $convertedAmount);
                                }
                            }),
                        Forms\Components\TextInput::make('agent_commission_in_base_currency')
                            ->label('Agent Commission (' . config('app.base_currency') . ')')
                            ->numeric()
                            ->disabled()
                            ->reactive()
                            ->visible(function ($context, $state, callable $set, callable $get) {
                                return $get('commission_type') === 'one-time';
                            }),

                        Repeater::make('semesters')
                            ->relationship('semesters')
                            ->label('')
                            ->schema([
                                Forms\Components\Select::make('term_start_month')
                                    ->options(config('app.months'))->required(),
                                Forms\Components\Select::make('term_start_year')
                                    ->options(function () {
                                        $years = [];
                                        $currentYear = date('Y');
                                        for ($i = -5; $i <= 5; $i++) {
                                            $years[$currentYear + $i] = $currentYear + $i;
                                        }
                                        return $years;
                                    })
                                    ->required(),
                                Forms\Components\TextInput::make('term_fees')->numeric()->required(),
                                Fieldset::make('')
                                    ->schema([
                                        Forms\Components\TextInput::make('own_commission')
                                            ->label(fn(callable $get, $livewire) => $livewire->data['admission_id']  ? 'Own Commission (' . $livewire->data['admission_fees_currency'] . ')' : 'Own Commission')
                                            ->numeric()
                                            ->afterStateUpdated(function (callable $get, callable $set, $livewire) {
                                                //$studentAdmission = StudentAdmission::find($livewire->data['admission_id']);
                                                if ($livewire->data['admission_id']) {
                                                    $convertedAmount = CurrencyHelper::convert($get('own_commission'),  $livewire->data['admission_fees_currency'], config('app.base_currency'));
                                                    $set('own_commission_in_base_currency', $convertedAmount);
                                                }
                                            })
                                            ->afterStateHydrated(function ($state, callable $set, callable $get, $livewire) {
                                                //$studentAdmission = StudentAdmission::find($livewire->data['admission_id']);
                                                if (isset($livewire->data['admission_id'])) {
                                                    $convertedAmount = CurrencyHelper::convert($get('own_commission'),  $livewire->data['admission_fees_currency'], config('app.base_currency'));
                                                    $set('own_commission_in_base_currency', $convertedAmount);
                                                }
                                            }),
                                        Forms\Components\TextInput::make('own_commission_in_base_currency')
                                            ->label('Own Commission (' . config('app.base_currency') . ')')
                                            ->numeric()
                                            ->disabled()
                                            ->reactive(),
                                    ]),
                                Fieldset::make('')
                                    ->schema([
                                        Forms\Components\TextInput::make('agent_commission')
                                            ->label(fn(callable $get, $livewire) => $livewire->data['admission_id']  ? 'Agent Commission (' . $livewire->data['admission_fees_currency'] . ')' : 'Agent Commission')
                                            ->numeric()
                                            ->afterStateUpdated(function (callable $get, callable $set, $livewire) {
                                                //$studentAdmission = StudentAdmission::find($livewire->data['admission_id']);
                                                if ($livewire->data['admission_id']) {
                                                    $convertedAmount = CurrencyHelper::convert($get('agent_commission'),  $livewire->data['admission_fees_currency'], config('app.base_currency'));
                                                    $set('agent_commission_in_base_currency', $convertedAmount);
                                                }
                                            })
                                            ->afterStateHydrated(function ($state, callable $set, callable $get, $livewire) {
                                                // $studentAdmission = StudentAdmission::find($livewire->data['admission_id']);
                                                if (isset($livewire->data['admission_id'])) {
                                                    $convertedAmount = CurrencyHelper::convert($get('agent_commission'),  $livewire->data['admission_fees_currency'], config('app.base_currency'));
                                                    $set('agent_commission_in_base_currency', $convertedAmount);
                                                }
                                            }),

                                        Forms\Components\TextInput::make('agent_commission_in_base_currency')
                                            ->label('Agent Commission (' . config('app.base_currency') . ')')
                                            ->numeric()
                                            ->disabled()
                                            ->reactive(),
                                    ]),
                            ])
                            ->visible(function ($context, $state, callable $set, callable $get) {
                                return $get('commission_type') === 'semester-wise' && $get('commissionable_type') === 'student';
                            })
                            ->defaultItems(function (callable $get) {
                                return 1;
                            })
                            ->reorderable(false)
                            ->reactive()
                            ->columns(3)
                            ->columnSpan(3)
                            ->addActionLabel('Add Semester'),


                    ])->visible(function ($context, $state, callable $set, callable $get) {
                        return $get('commissionable_id');
                    })

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('commissionable_type', 'student')
                    ->with(['student' => function ($morphQuery) {
                        $morphQuery->when(
                            $morphQuery->getModel() === 'App\Models\Student',
                            function ($studentQuery) {
                                $studentQuery->select('id', 'first_name', 'last_name')->with([
                                    'studentAdmissions' => function ($admissionsQuery) {
                                        $admissionsQuery->limit(1)->with([
                                            'college' => function ($collegeQuery) {
                                                $collegeQuery->limit(1);
                                            },
                                            'degree' => function ($degreeQuery) {
                                                $degreeQuery->limit(1);
                                            },
                                        ]);
                                    },
                                ]);
                            }
                        );
                    }, 'semesters']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Commission Date')->toggleable()->sortable()->date(),
                Tables\Columns\TextColumn::make('student.full_name')
                    ->label(
                        'Student Name'
                    )
                    ->toggleable()
                    ->sortable()
                    ->searchable(['students.first_name', 'students.last_name']),
                Tables\Columns\TextColumn::make('student.studentAdmissions.0.college.college_name')->label('College')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('student.studentAdmissions.0.degree.name')->label('Degree')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('admission_by')->label('Admissin By')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->colors([
                    'Invoice Generated' => 'active', // Green for active
                    'Pending Invoice' => 'pending', // Yellow for pending
                ])->label('Status')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('student.studentAdmissions.0.fees_currency')->label('Currency')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('own_commission')->formatStateUsing(function (Commission $record) {
                    if ($record->commission_type === 'one-time') {
                        return $record->own_commission;
                    } else {
                        return $record->semesters->sum('own_commission');
                    }
                })->label('Own Commission')->toggleable(),
                Tables\Columns\TextColumn::make('own_commission_in_base_currency')->formatStateUsing(function (Commission $record) {
                    if ($record->commission_type === 'one-time') {
                        return CurrencyHelper::convert($record->own_commission, $record->student->studentAdmissions->first()->fees_currency, config('app.base_currency'), $record->base_currency_rate);
                    } else {

                        $totalOwnCommission = 0;
                        foreach ($record->semesters as $semester) {
                            $totalOwnCommission += CurrencyHelper::convert($semester->own_commission, $semester->fees_currency, config('app.base_currency'), $semester->base_currency_rate);
                        }
                        return $totalOwnCommission;
                    }
                })->label('Own Commission In Base Currency(' . config('app.base_currency') . ')')->toggleable(),
                Tables\Columns\TextColumn::make('agent.name')->label('Agent')->toggleable(),
                Tables\Columns\TextColumn::make('agent_commission')->formatStateUsing(function (Commission $record) {
                    if ($record->commission_type === 'one-time') {
                        return $record->agent_commission;
                    } else {
                        return $record->semesters->sum('agent_commission');
                    }
                }),
                Tables\Columns\TextColumn::make('agent_commission_in_base_currency')->formatStateUsing(function (Commission $record) {
                    if ($record->commission_type === 'one-time') {
                        return CurrencyHelper::convert($record->agent_commission, $record->student->studentAdmissions->first()->fees_currency, config('app.base_currency'));
                    } else {
                        $totalAgentCommissionInBaseCurrency = 0;
                        foreach ($record->semesters as $semester) {
                            $totalAgentCommissionInBaseCurrency += CurrencyHelper::convert($semester->agent_commission, $semester->fees_currency, config('app.base_currency'), $semester->base_currency_rate);
                        }
                        return $totalAgentCommissionInBaseCurrency;
                    }
                })->label('Agent Commission In Base Currency(' . config('app.base_currency') . ')')->toggleable(),

            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(['Pending Invoice', 'Invoice Generated'])->multiple(),
                Filter::make('created_at')
                    ->label('Commission Date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')->label('From'),
                        Forms\Components\DatePicker::make('date_to')->label('To'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['date_from'], fn($query) => $query->whereDate('created_at', '>=', $data['date_from']))
                            ->when($data['date_to'], fn($query) => $query->whereDate('created_at', '<=', $data['date_to']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentCommissions::route('/'),
            'create' => Pages\CreateStudentCommission::route('/create'),
            'edit' => Pages\EditStudentCommission::route('/{record}/edit'),
        ];
    }
}
