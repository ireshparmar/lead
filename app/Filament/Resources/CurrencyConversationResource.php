<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyConversationResource\Pages;
use App\Filament\Resources\CurrencyConversationResource\RelationManagers;
use App\Models\Currency;
use App\Models\CurrencyConversation;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpOffice\PhpSpreadsheet\Cell\IgnoredErrors;

class CurrencyConversationResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Masters';

    protected static ?string $label = 'Currency Conversations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('currency_unit')->label('Unit')->default(1)->readOnly(),
                Select::make('currency')->required()->options(config('app.currency'))->unique(ignoreRecord: true),
                TextInput::make('base_currency_rate')->required()->rules(['regex:/^\d+(\.\d+)?$/']),
                TextInput::make('base_currency')->default(config('app.base_currency'))->readOnly()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('currency_unit')->toggleable()->sortable(),
                TextColumn::make('currency')->toggleable()->sortable(),
                TextColumn::make('base_currency_rate')->toggleable()->sortable(),
                TextColumn::make('base_currency')->toggleable()->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCurrencyConversations::route('/'),
            'create' => Pages\CreateCurrencyConversation::route('/create'),
            'edit' => Pages\EditCurrencyConversation::route('/{record}/edit'),
        ];
    }
}
