<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadInvoiceManagementResource\Pages;
use App\Filament\Resources\LeadInvoiceManagementResource\RelationManagers;
use App\Helpers\CurrencyHelper;
use App\Models\Invoice;
use App\Models\LeadInvoiceManagement;
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

class LeadInvoiceManagementResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Invoice Management';

    protected static ?string $label = 'Lead Invoices';

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
                $query->where('invoice_type', 'lead')->with(['commission.lead.country', 'createdBy', 'commission.agent']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Date')->toggleable()->sortable()->date(),
                Tables\Columns\TextColumn::make('invoice_number')->label('Invoice Number')->toggleable()->sortable()->searchable(),
                Tables\Columns\TextColumn::make('commission.lead.full_name')->label('Lead')->toggleable()->sortable()->searchable(['leads.full_name']),
                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Own Commission Status')->badge()->colors([
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
                    })->disabled(function (Model $record) {
                        return $record->status == 'Commission Received';
                    })
                ),
                Tables\Columns\TextColumn::make('remarks')->label('Remarks')->toggleable(),
                Tables\Columns\TextColumn::make('payment_date')->label('Payment Date')->toggleable()->sortable()->date(),
                Tables\Columns\TextColumn::make('commission.lead.country.currency')->label('Currency')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('own_commission')->label('Own Commission')->toggleable(),
                Tables\Columns\TextColumn::make('own_commission_in_base_currency')->formatStateUsing(function (Invoice $record) {
                    return CurrencyHelper::convert($record->own_commission, $record->currency, $record->base_currency, $record->base_currency_rate);
                })->label('Own Commission In Base Currency(' . config('app.base_currency') . ')')->toggleable(),
                Tables\Columns\TextColumn::make('commission.agent.name')->label('Agent')->toggleable()->searchable(['users.name']),
                Tables\Columns\TextColumn::make('agent_commission')->label('Agent Commission')->toggleable(),
                Tables\Columns\TextColumn::make('agent_commission_in_base_currency')->formatStateUsing(function (Invoice $record) {
                    return CurrencyHelper::convert($record->agent_commission, $record->currency, $record->base_currency, $record->base_currency_rate);
                })->label('Agent Commission In Base Currency(' . config('app.base_currency') . ')')->toggleable(),

            ])
            ->filters([
                SelectFilter::make('status')->label('Own Commission Status')
                    ->options(['Pending Payment' => 'Pending Payment', 'Commission Received' => 'Commission Received'])->multiple(),

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
            'index' => Pages\ListLeadInvoiceManagement::route('/'),
            // 'create' => Pages\CreateLeadInvoiceManagement::route('/create'),
            //'edit' => Pages\EditLeadInvoiceManagement::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model           $record): bool
    {
        return false;
    }
}
