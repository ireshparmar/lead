<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampusResource\Pages;
use App\Filament\Resources\CampusResource\RelationManagers;
use App\Models\Campus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CampusResource extends Resource
{
    protected static ?string $model = Campus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Course Management';

    protected static ?string $label = 'Campus/Location';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('college_id')
                    ->relationship('college', 'college_name')
                    ->required(),
                Forms\Components\TextInput::make('campus_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')->maxLength(255),
                Forms\Components\TextInput::make('postal_code')->maxLength(20),
                Forms\Components\TextInput::make('phone_no')->maxLength(20),
                Forms\Components\TextInput::make('contact_person')->maxLength(255),
                Forms\Components\TextInput::make('email_id')->email()->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('college.college_name')->label('College')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('campus_name')->label('Campus Name')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('address')->label('Address')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('postal_code')->label('Postal Code')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('phone_no')->label('Phone No')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('contact_person')->label('Contact Person')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('email_id')->label('Email ID')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By')->searchable()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('college_id')
                    ->relationship('college', 'college_name')
                    ->label('College'),
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
            'index' => Pages\ListCampuses::route('/'),
            'create' => Pages\CreateCampus::route('/create'),
            'edit' => Pages\EditCampus::route('/{record}/edit'),
        ];
    }
}
