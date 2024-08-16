<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EducationLevelResource\Pages;
use App\Filament\Resources\EducationLevelResource\RelationManagers;
use App\Models\EducationLevel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EducationLevelResource extends Resource
{
    protected static ?string $model = EducationLevel::class;

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
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name')->sortable()->toggleable(),
                Tables\Columns\ToggleColumn::make('status')->label('Status')->sortable()->toggleable()->updateStateUsing(function (EducationLevel $record, $state) {
                    $status = $state ? 'Active' : 'Inactive';
                    $record->status = $status;
                    $record->save();
                })->getStateUsing(function (EducationLevel $record) {
                    return $record->status == 'Active' ? 1 : 0;
                }),
                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By')->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                    ]),
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
            'index' => Pages\ListEducationLevels::route('/'),
            'create' => Pages\CreateEducationLevel::route('/create'),
            'edit' => Pages\EditEducationLevel::route('/{record}/edit'),
        ];
    }
}
