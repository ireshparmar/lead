<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentAddmissionResource\Pages;
use App\Filament\Resources\StudentAddmissionResource\RelationManagers;
use App\Models\Student;
use App\Models\StudentAddmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class StudentAddmissionResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Students';

    protected static ?string $label = 'Student Admissions';

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
                if (!Auth::user()->hasRole('Admin')) {
                    $query->whereHas('studentAdmissions', function (Builder $query) {
                        $query->where('allocated_user', Auth::user()->id);
                    });
                } else {
                    $query->whereHas('studentAdmissions');
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('enrollment_number')->label('Enrollment Number')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('enrollment_date')->label('Enrollment Date')->date()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('first_name')->label('First Name')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('middle_name')->label('Middle Name')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('last_name')->label('Last Name')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('birth_date')->label('Birth Date')->date()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('mobile')->label('Mobile')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('gender')->label('Gender')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('address')->label('Address')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('postal_code')->label('Postal Code')->searchable()->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('preferredCountry.name')->label('Preferred Country')->toggleable()->sortable(),
                // Tables\Columns\TextColumn::make('state.name')->label('State')->toggleable()->sortable(),
                // Tables\Columns\TextColumn::make('city.name')->label('City')->toggleable()->sortable(),
                Tables\Columns\TextColumn::make('remark')->label('Remark')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('emergency_name')->label('Emergency Contact Name'),
                Tables\Columns\TextColumn::make('emergency_relation')->label('Emergency Contact Relation'),
                Tables\Columns\TextColumn::make('emergency_contact_code')->label('Emergency Contact Code'),
                Tables\Columns\TextColumn::make('emergency_contact_no')->label('Emergency Contact Number'),
                Tables\Columns\TextColumn::make('emergency_detail')->label('Emergency Contact Detail'),
                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By'),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By'),
            ])
            ->filters([

                SelectFilter::make('country')->label('Preffered Country')->relationship('preferredCountry', 'name')->multiple()->preload()->searchable(),
                SelectFilter::make('gender')->options(config('app.gender'))->label('Gender')->multiple(),
                SelectFilter::make('purpose')->relationship('purpose', 'purpose_name')->multiple()->preload()->searchable(),
                SelectFilter::make('service')->relationship('service', 'service_name')->multiple()->preload()->searchable(),
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
            ])->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    // Action::make('detail')
                    //     ->icon('heroicon-o-document-magnifying-glass')
                    //     ->url(fn($record) => route('filament.admin.resources.students.detail', $record->id)),
                ])
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
            'index' => Pages\ListStudentAddmissions::route('/'),
            'create' => Pages\CreateStudentAddmission::route('/create'),
            'edit' => \App\Filament\Resources\StudentResource\Pages\EditStudent::route('../students/{record}/edit?activeRelationManager=3'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
