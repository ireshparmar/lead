<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EligibilityResource\Pages;
use App\Filament\Resources\EligibilityResource\RelationManagers;
use App\Models\Eligibility;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EligibilityResource extends Resource
{
    protected static ?string $model = Eligibility::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Course Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('createdBy.name')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('updatedBy.name')->searchable()->sortable()->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListEligibilities::route('/'),
            'create' => Pages\CreateEligibility::route('/create'),
            'edit' => Pages\EditEligibility::route('/{record}/edit'),
        ];
    }
}
