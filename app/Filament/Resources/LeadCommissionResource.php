<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadCommissionResource\Pages;
use App\Filament\Resources\CommissionResource\RelationManagers;
use App\Helpers\CurrencyHelper;
use App\Models\Commission;
use App\Models\Lead;
use App\Models\Student;
use App\Models\StudentAdmission;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function PHPUnit\Framework\returnValueMap;

class LeadCommissionResource extends Resource
{
    protected static ?string $model = Commission::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Commissions';

    protected static ?string $label = 'Lead Commissions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('commissionable_type')
                    ->default('lead')
                    ->required()
                    ->reactive(), // Makes the field reactive to trigger changes in other fields
                Forms\Components\Select::make('commissionable_id')
                    ->label('Select Lead')
                    ->options(function (callable $get) {
                        return \App\Models\Lead::query()
                            ->pluck('full_name', 'id')
                            ->toArray(); // Adjust to your Lead model's structure

                    })
                    ->preload()
                    ->reactive()
                    ->required()
                    ->searchable() // Optional: Makes the select searchable
                    ->placeholder('Select a lead')->preload(),

                Forms\Components\TextInput::make('Agent')
                    ->disabled()
                    ->reactive()
                    ->visible(function (string $context, $state, callable $set, callable $get) {
                        return $get('commissionable_type') === 'lead' && Lead::whereHas('agent')->where('id', $get('commissionable_id'))->count() > 0;
                    }),
                Fieldset::make('Commission')
                    ->schema([
                        Forms\Components\Hidden::make('agent_id')->reactive(),
                        Forms\Components\Select::make('commission_type')
                            ->label('Commission Structure')
                            ->options(function (callable $get) {
                                return [
                                    'one-time' => 'One Time Commission',
                                ];
                            })
                            ->default('one-time')
                            ->required()
                            ->reactive(),

                        Forms\Components\TextInput::make('remarks')->label('Remarks'),
                        Forms\Components\TextInput::make('own_commission')
                            ->label('Own Commission')
                            ->visible(function ($context, $state, callable $set, callable $get) {
                                return $get('commission_type') === 'one-time';
                            })

                            ->numeric()
                            ->reactive(),
                        Forms\Components\TextInput::make('agent_commission')
                            ->label('Agent Commission')
                            ->visible(function ($context, $state, callable $set, callable $get) {
                                return $get('commission_type') === 'one-time';
                            })

                            ->numeric()
                            ->reactive(),




                    ])

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('commissionable_type', 'lead')
                    ->with(['lead.country']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Commission Date')->toggleable()->sortable()->date(),
                Tables\Columns\TextColumn::make('lead.full_name')
                    ->label(
                        'Lead Name'
                    )
                    ->toggleable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()->colors([
                    'Invoice Generated' => 'active', // Green for active
                    'Pending Invoice' => 'pending', // Yellow for pending
                ])->toggleable()->sortable()->searchable(),
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
            'index' => Pages\ListLeadCommissions::route('/'),
            'create' => Pages\CreateLeadCommission::route('/create'),
            'edit' => Pages\EditLeadCommission::route('/{record}/edit'),
        ];
    }
}
