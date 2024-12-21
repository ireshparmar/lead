<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GenerateLeadInvoiceResource\Pages;
use App\Filament\Resources\GenerateLeadInvoiceResource\RelationManagers;
use App\Helpers\CurrencyHelper;
use App\Models\Commission;
use App\Models\GenerateLeadInvoice;
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

class GenerateLeadInvoiceResource extends Resource
{
    protected static ?string $model = Commission::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    protected static ?string $navigationGroup = 'Invoice Management';

    protected static ?string $label = 'Generate Lead Invoice';

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
                $query->where(['commissionable_type' => 'lead'])
                    ->with(['lead.country', 'agent']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Commission Date')->toggleable()->sortable()->date(),
                Tables\Columns\TextColumn::make('lead.full_name')
                    ->label(
                        ' Name'
                    )
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->colors([
                    'Invoice Generated' => 'active', // Green for active
                    'Pending Invoice' => 'pending', // Yellow for pending
                ])->label('Status')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('lead.country.currency')->label('Currency')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('own_commission')->formatStateUsing(function (Commission $record) {
                    return $record->own_commission;
                })->label('Own Commission')->toggleable(),
                Tables\Columns\TextColumn::make('own_commission_in_base_currency')->formatStateUsing(function (Commission $record) {
                    return CurrencyHelper::convert($record->own_commission, $record->lead->country[0]['currency'], config('app.base_currency'), $record->base_currency_rate);
                })->label('Own Commission In Base Currency(' . config('app.base_currency') . ')')->toggleable(),
                Tables\Columns\TextColumn::make('agent.name')->label('Agent')->toggleable(),
                Tables\Columns\TextColumn::make('agent_commission')->formatStateUsing(function (Commission $record) {
                    return $record->agent_commission;
                }),
                Tables\Columns\TextColumn::make('agent_commission_in_base_currency')->formatStateUsing(function (Commission $record) {
                    return CurrencyHelper::convert($record->agent_commission, $record->lead->country[0]['currency'], config('app.base_currency'));
                })->label('Agent Commission In Base Currency(' . config('app.base_currency') . ')')->toggleable(),
            ])
            ->filters([
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

                                $agentCommission = $record->agent_commission;
                                $ownCommission = $record->own_commission;

                                Invoice::updateOrCreate(
                                    ['commission_id' => $record->id],
                                    [
                                        'invoice_number' => 'NAND-' . date('Y') . date('md') . substr(time(), -5) . '-' . str_pad($record->id, 4, '0', STR_PAD_LEFT),
                                        'agent_commission' => $agentCommission,
                                        'own_commission' => $ownCommission,
                                        'base_currency' => config('app.base_currency'),
                                        'base_currency_rate' => $record->base_currency_rate,
                                        'payment_currency' => $record->lead->country[0]['currency'],
                                        'created_by' => auth()->user()->id,
                                        'invoice_type' => 'lead',
                                    ]
                                );

                                Notification::make()->title('Invoice Generated')->success()->send();
                            }
                        }
                    })
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
            'index' => Pages\ListGenerateLeadInvoices::route('/'),
            //'create' => Pages\CreateGenerateLeadInvoice::route('/create'),
            //'edit' => Pages\EditGenerateLeadInvoice::route('/{record}/edit'),
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
