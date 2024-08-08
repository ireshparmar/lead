<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;


class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\TextInput::make('first_name')
                    ->label('First Name')
                    ->required(),
                Forms\Components\TextInput::make('middle_name')
                    ->label('Middle Name'),
                Forms\Components\TextInput::make('last_name')
                    ->label('Last Name')
                    ->required(),
                Forms\Components\TextInput::make('enrollment_number')
                    ->label('Enrollment Number')
                    ->required(),
                Forms\Components\DatePicker::make('enrollment_date')
                    ->label('Enrollment Date')
                    ->required(),
                Forms\Components\DatePicker::make('birth_date')
                    ->label('Birth Date')
                    ->required(),
                Forms\Components\TextInput::make('country_code')
                    ->label('Country Code'),
                Forms\Components\TextInput::make('mobile')
                    ->label('Mobile'),
                Forms\Components\TextInput::make('gender')
                    ->label('Gender'),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email(),
                Forms\Components\Select::make('inquiry_source_id')
                    ->label('Inquiry Source')
                    ->relationship('inquirySource', 'name'),
                Forms\Components\TextInput::make('address')
                    ->label('Address'),
                Forms\Components\TextInput::make('postal_code')
                    ->label('Postal Code'),
                Forms\Components\Select::make('country_id')
                    ->label('Country')
                    ->relationship('country', 'name'),
                Forms\Components\Select::make('state_id')
                    ->label('State')
                    ->relationship('state', 'name'),
                Forms\Components\Select::make('city_id')
                    ->label('City')
                    ->relationship('city', 'name'),
                Forms\Components\Select::make('reference_by')
                    ->label('Reference By')
                    ->relationship('reference', 'name'),
                Forms\Components\Select::make('purpose_id')
                    ->label('Purpose')
                    ->relationship('purpose', 'name'),
                Forms\Components\Select::make('service_id')
                    ->label('Service')
                    ->relationship('service', 'name'),
                Forms\Components\Select::make('pref_country_id')
                    ->label('Preferred Country')
                    ->relationship('preferredCountry', 'name'),
                Forms\Components\TextInput::make('remark')
                    ->label('Remark'),
                Forms\Components\Select::make('agent_id')
                    ->label('Agent')
                    ->relationship('agent', 'name'),
                Forms\Components\TextInput::make('emergency_name')
                    ->label('Emergency Contact Name'),
                Forms\Components\TextInput::make('emergency_relation')
                    ->label('Emergency Contact Relation'),
                Forms\Components\TextInput::make('emergency_contact_code')
                    ->label('Emergency Contact Code'),
                Forms\Components\TextInput::make('emergency_contact_no')
                    ->label('Emergency Contact Number'),
                Forms\Components\TextInput::make('emergency_detail')
                    ->label('Emergency Contact Detail'),
                Forms\Components\Select::make('created_by')
                    ->label('Created By')
                    ->relationship('creator', 'name'),
                Forms\Components\Select::make('updated_by')
                    ->label('Updated By')
                    ->relationship('updater', 'name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('first_name')->label('First Name'),
            Tables\Columns\TextColumn::make('middle_name')->label('Middle Name'),
            Tables\Columns\TextColumn::make('last_name')->label('Last Name'),
            Tables\Columns\TextColumn::make('enrollment_number')->label('Enrollment Number'),
            Tables\Columns\TextColumn::make('enrollment_date')->label('Enrollment Date')->date(),
            Tables\Columns\TextColumn::make('birth_date')->label('Birth Date')->date(),
            Tables\Columns\TextColumn::make('country_code')->label('Country Code'),
            Tables\Columns\TextColumn::make('mobile')->label('Mobile'),
            Tables\Columns\TextColumn::make('gender')->label('Gender'),
            Tables\Columns\TextColumn::make('email')->label('Email'),
            Tables\Columns\TextColumn::make('address')->label('Address'),
            Tables\Columns\TextColumn::make('postal_code')->label('Postal Code'),
            Tables\Columns\TextColumn::make('remark')->label('Remark'),
            Tables\Columns\TextColumn::make('emergency_name')->label('Emergency Contact Name'),
            Tables\Columns\TextColumn::make('emergency_relation')->label('Emergency Contact Relation'),
            Tables\Columns\TextColumn::make('emergency_contact_code')->label('Emergency Contact Code'),
            Tables\Columns\TextColumn::make('emergency_contact_no')->label('Emergency Contact Number'),
            Tables\Columns\TextColumn::make('emergency_detail')->label('Emergency Contact Detail'),
            Tables\Columns\TextColumn::make('createdBy.name')->label('Created By'),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By'),
        ])
        ->filters([
            SelectFilter::make('country_id')->relationship('country', 'name')->label('Country'),
            SelectFilter::make('state_id')->relationship('state', 'name')->label('State'),
            SelectFilter::make('city_id')->relationship('city', 'name')->label('City'),
            SelectFilter::make('gender')->options([
                'Male' => 'Male',
                'Female' => 'Female',
            ])->label('Gender'),
            Filter::make('enrollment_date')
                ->label('Enrollment Date')
                ->form([
                    Forms\Components\DatePicker::make('enrollment_date_from')->label('From'),
                    Forms\Components\DatePicker::make('enrollment_date_to')->label('To'),
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when($data['enrollment_date_from'], fn($query) => $query->whereDate('enrollment_date', '>=', $data['enrollment_date_from']))
                        ->when($data['enrollment_date_to'], fn($query) => $query->whereDate('enrollment_date', '<=', $data['enrollment_date_to']));
                }),
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
