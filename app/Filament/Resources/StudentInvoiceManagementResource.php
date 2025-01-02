<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentInvoiceManagementResource\Pages;
use App\Filament\Resources\StudentInvoiceManagementResource\RelationManagers;
use App\Helpers\CurrencyHelper;
use App\Models\Commission;
use App\Models\Invoice;
use App\Models\StudentInvoiceManagement;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class StudentInvoiceManagementResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Invoice Management';

    protected static ?string $label = 'Student Invoices';

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
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()->colors([
                    'Commission Received' => 'active', // Green for active
                    'Pending Payment' => 'pending', // Yellow for pending
                ])->toggleable()->sortable()->action(
                    Action::make('Own Commission Status')->form([
                        Select::make('status')->options(['Commission Received' => 'Commission Received'])->required(),
                        Textarea::make('remarks'),
                        DatePicker::make('payment_date')->label('Payment Date')->required()
                    ])->action(function (Invoice $record, array $data) {
                        $record->status = $data['status'];
                        $record->payment_date = $data['payment_date'];
                        $record->remarks = $data['remarks'] ?? null; // Assuming `remark` is a column in your model
                        $record->updated_by = Auth::id();
                        $record->save();
                    })->mountUsing(function (Forms\ComponentContainer $form, Model $record) {
                        // Load existing payments data into the form
                        $form->fill([
                            'isVerified' => $record->isVerified,
                            'remark'  => $record->remark
                        ]);
                    })->disabled(function (Model $record) {
                        return $record->status == 'Commission Received';
                    })
                ),
                Tables\Columns\TextColumn::make('remarks')->label('Remarks')->toggleable(),
                Tables\Columns\TextColumn::make('payment_date')->label('Payment Date')->toggleable()->sortable()->date(),
                Tables\Columns\TextColumn::make('commission.student.studentAdmissions.0.fees_currency')->label('Currency')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('own_commission')->label('Own Commission')->toggleable(),
                Tables\Columns\TextColumn::make('own_commission_in_base_currency')->formatStateUsing(function (Invoice $record) {
                    return CurrencyHelper::convert($record->own_commission, $record->currency, $record->base_currency, $record->base_currency_rate);
                })->label('Own Commission In Base Currency(' . config('app.base_currency') . ')')->toggleable(),
                Tables\Columns\TextColumn::make('commission.agent.name')->label('Agent')->toggleable()->searchable(['users.name']),
                Tables\Columns\TextColumn::make('agent_commission')->label('Agent Commission')->toggleable(),
                Tables\Columns\TextColumn::make('agent_commission_in_base_currency')->formatStateUsing(function (Invoice $record) {
                    return CurrencyHelper::convert($record->agent_commission, $record->currency, $record->base_currency, $record->base_currency_rate);
                })->label('Commission In Base Currency(' . config('app.base_currency') . ')')->toggleable(),


            ])
            ->filters([
                SelectFilter::make('status')->label('Status')
                    ->options(['Pending Payment' => 'Pending Payment', 'Commission Received' => 'Commission Received'])->multiple(),
                Filter::make('payment_date')
                    ->label('Payment Date')
                    ->form([
                        Forms\Components\DatePicker::make('payment_date_from')->label('Payment From'),
                        Forms\Components\DatePicker::make('payment_date_to')->label('Payment To'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['payment_date_from'], fn($query) => $query->whereDate('payment_date', '>=', $data['payment_date_from']))
                            ->when($data['payment_date_to'], fn($query) => $query->whereDate('payment_date', '<=', $data['payment_date_to']));
                    }),

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
            'index' => Pages\ListStudentInvoiceManagement::route('/'),
            // 'create' => Pages\CreateStudentInvoiceManagement::route('/create'),
            //  'edit' => Pages\EditStudentInvoiceManagement::route('/{record}/edit'),
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
