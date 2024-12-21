<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GenerateStudentInvoiceResource\Pages;
use App\Filament\Resources\GenerateStudentInvoiceResource\RelationManagers;
use App\Helpers\CurrencyHelper;
use App\Models\Commission;
use App\Models\GenerateStudentInvoice;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GenerateStudentInvoiceResource extends Resource
{
    protected static ?string $model = Commission::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Invoice Management';

    protected static ?string $label = 'Generate Student Invoice';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->where(['commissionable_type' => 'student'])
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
                    ->sortable(),
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
                SelectFilter::make('admission_by')->options(config('app.admission_by'))->label('Admission By')->multiple(),
                Filter::make('created_at')
                    ->label('Date Range')
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
            ->actions([])
            ->bulkActions([
                BulkAction::make('Generate Invoice')
                    ->action(function (Collection $records) {
                        if (!empty($records)) {
                            foreach ($records as $record) {

                                $record->status = 'Invoice Generated';
                                $record->save();

                                $agentCommission = $ownCommission = 0;

                                if ($record->commission_type === 'one-time') {
                                    $agentCommission = $record->agent_commission;
                                    $ownCommission = $record->own_commission;
                                } else {
                                    foreach ($record->semesters as $semester) {
                                        $agentCommission += $semester->agent_commission;
                                        $ownCommission += $semester->own_commission;
                                    }
                                }

                                Invoice::updateOrCreate(
                                    ['commission_id' => $record->id],
                                    [
                                        'commission_id' => $record->id,
                                        'invoice_number' => 'NAND-' . date('Y') . date('md') . substr(time(), -5) . '-' . str_pad($record->id, 4, '0', STR_PAD_LEFT),
                                        'agent_commission' => $agentCommission,
                                        'own_commission' => $ownCommission,
                                        'base_currency' => config('app.base_currency'),
                                        'base_currency_rate' => $record->base_currency_rate,
                                        'payment_currency' => $record->lead->country[0]['currency'],
                                        'created_by' => auth()->user()->id,
                                        'invoice_type' => 'student',

                                    ]
                                );

                                Notification::make()->title('Invoice Generated')->success()->send();
                            }
                        }
                    })
            ]);;
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
            'index' => Pages\ListGenerateStudentInvoices::route('/'),
            //'create' => Pages\CreateGenerateInvoice::route('/create'),
            // 'edit' => Pages\EditGenerateInvoice::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }
}
