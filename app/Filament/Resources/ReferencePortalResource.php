<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferencePortalResource\Pages;
use App\Filament\Resources\ReferencePortalResource\RelationManagers;
use App\Models\ReferencePortal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReferencePortalResource extends Resource
{
    protected static ?string $model = ReferencePortal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Course Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Reference Portal')
                    ->required(),

                Forms\Components\TextInput::make('reference_link')
                    ->label('Reference Link')
                    ->nullable()
                    ->url(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('reference_link')->label('Reference Link')->searchable(),
                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By')->searchable(),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListReferencePortals::route('/'),
            'create' => Pages\CreateReferencePortal::route('/create'),
            'edit' => Pages\EditReferencePortal::route('/{record}/edit'),
        ];
    }
}
