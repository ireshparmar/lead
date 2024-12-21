<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentAgentCommissionResource\Pages;
use App\Filament\Resources\StudentAgentCommissionResource\RelationManagers;
use App\Helpers\CurrencyHelper;
use App\Models\Invoice;
use App\Models\StudentAgentCommission;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class StudentAgentCommissionResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Agent Commissions';

    protected static ?string $label = 'Students';

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
                $query->where('invoice_type', 'student')->with(['commission.student.studentAdmissions' => function ($query) {
                    $query->limit(1)->with([
                        'college' => function ($collegeQuery) {
                            $collegeQuery->limit(1);
                        },
                        'degree' => function ($degreeQuery) {
                            $degreeQuery->limit(1);
                        },
                    ]);
                }, 'createdBy', 'commission.agent']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Date')->toggleable()->sortable()->date(),
                Tables\Columns\TextColumn::make('invoice_number')->label('Invoice Number')->toggleable()->sortable()->searchable(),
                Tables\Columns\TextColumn::make('commission.student.full_name')->label('Student')->toggleable()->sortable()->searchable(['students.first_name', 'students.last_name']),
                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('commission.student.studentAdmissions.0.college.college_name')->label('College')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('commission.admission_by')->label('Admissin By')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('commission.student.studentAdmissions.0.fees_currency')->label('Currency')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('commission.agent.name')->label('Agent')->toggleable()->searchable(['users.name']),
                Tables\Columns\TextColumn::make('agent_commission')->label('Commission')->toggleable(),
                Tables\Columns\TextColumn::make('agent_commission_in_base_currency')->formatStateUsing(function (Invoice $record) {
                    return CurrencyHelper::convert($record->agent_commission, $record->currency, $record->base_currency, $record->base_currency_rate);
                })->label('Agent Commission In Base Currency(' . config('app.base_currency') . ')')->toggleable(),
                Tables\Columns\TextColumn::make('agent_payment_status')->label('Status')->badge()->colors([
                    'Commission Paid' => 'active', // Green for active
                    'Pending Payment' => 'pending', // Yellow for pending
                ])->toggleable()->sortable()->action(
                    Action::make('Status')->form([
                        Select::make('agent_payment_status')->options(['Commission Paid' => 'Commission Paid'])->required(),
                        Textarea::make('agent_payment_remarks'),
                        DatePicker::make('agent_payment_date')->label('Payment Date')->required()
                    ])->action(function (Invoice $record, array $data) {
                        $record->agent_payment_status = $data['agent_payment_status'];
                        $record->agent_payment_date = $data['agent_payment_date'];
                        $record->agent_payment_remarks = $data['agent_payment_remarks'] ?? null; // Assuming `remark` is a column in your model
                        $record->agent_payment_status_updated_by = Auth::id();
                        $record->save();
                    })->disabled(function (Model $record) {
                        return $record->agent_payment_status == 'Commission Paid';
                    })
                ),
                Tables\Columns\TextColumn::make('agent_payment_date')->label('Payment Date')->toggleable()->sortable()->date(),
                Tables\Columns\TextColumn::make('agent_payment_remarks')->label('Remarks')->toggleable(),

            ])
            ->filters([

                SelectFilter::make('agent_payment_status')->label('Status')
                    ->options(['Pending Payment' => 'Pending Payment', 'Commission Paid' => 'Commission Paid'])->multiple(),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
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
            'index' => Pages\ListStudentAgentCommissions::route('/'),
            // 'create' => Pages\CreateStudentAgentCommission::route('/create'),
            // 'edit' => Pages\EditStudentAgentCommission::route('/{record}/edit'),
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
