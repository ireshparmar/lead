<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DurationResource\Pages;
use App\Filament\Resources\DurationResource\RelationManagers;
use App\Models\Duration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DurationResource extends Resource
{
    protected static ?string $model = Duration::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Masters';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(config('app.status'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name')->sortable()->toggleable(),
                Tables\Columns\ToggleColumn::make('status')->label('Status')->sortable()->toggleable()->updateStateUsing(function (Duration $record, $state) {
                    $status = $state ? 'Active' : 'Inactive';
                    $record->status = $status;
                    $record->save();
                })->getStateUsing(function (Duration $record) {
                    return $record->status == 'Active' ? 1 : 0;
                }),
                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By')->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(config('app.status')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListDurations::route('/'),
            'create' => Pages\CreateDuration::route('/create'),
            'edit' => Pages\EditDuration::route('/{record}/edit'),
        ];
    }
}
