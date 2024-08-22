<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollegeResource\Pages;
use App\Filament\Resources\CollegeResource\RelationManagers;
use App\Models\College;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CollegeResource extends Resource
{
    protected static ?string $model = College::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Course Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('college_name')
                    ->required()
                    ->label('College / University'),
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->required()
                    ->label('Country'),
                Forms\Components\TextInput::make('address')
                    ->label('Address'),
                Forms\Components\TextInput::make('postal_code')
                    ->label('Postal Code'),
                Forms\Components\TextInput::make('contact_person')
                    ->label('Contact Person'),
                Forms\Components\TextInput::make('phone_no')
                    ->label('Phone Number'),
                Forms\Components\TextInput::make('email_id')
                    ->email()
                    ->label('Email ID'),
                Forms\Components\TextInput::make('website')
                    ->url()
                    ->label('Website'),
                Forms\Components\FileUpload::make('image')
                    ->label('Image')
                    ->acceptedFileTypes(['image/jpeg', 'image/png'])
                    ->directory(config('app.UPLOAD_DIR') . '/colleges')
                    ->downloadable()
                    ->openable()
                    ->previewable(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Image')->toggleable(),
                Tables\Columns\TextColumn::make('college_name')->label('College Name')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('country.name')->label('Country')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('address')->label('Address')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('postal_code')->label('Postal Code')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('contact_person')->label('Contact Person')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('phone_no')->label('Phone Number')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('email_id')->label('Email ID')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('website')->label('Website')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By'),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By'),
            ])
            ->filters([
                SelectFilter::make('country')->label('Country')->relationship('country', 'name')->multiple()->preload()->searchable(),


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
            'index' => Pages\ListColleges::route('/'),
            'create' => Pages\CreateCollege::route('/create'),
            'edit' => Pages\EditCollege::route('/{record}/edit'),
        ];
    }
}
