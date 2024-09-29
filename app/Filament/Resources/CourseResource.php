<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Campus;
use App\Models\College;
use App\Models\Country;
use App\Models\Course;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Course Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('country_id')
                    ->relationship('country', 'name')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('college_id', null);
                        $set('campus_id', null);
                    }),
                Forms\Components\Select::make('college_id')
                    ->relationship('college', 'college_name')
                    ->required()
                    ->reactive()
                    ->preload()
                    ->options(function (callable $get) {
                        $countryId = $get('country_id');
                        //dd($countryId);
                        if ($countryId) {
                            return College::where('country_id', $countryId)->pluck('college_name', 'id');
                        }
                        return [];
                    })->afterStateUpdated(function ($state, callable $set) {
                        $set('campus_id', null);
                    }),
                Forms\Components\Select::make('campus_id')
                    ->relationship('campus', 'campus_name')
                    ->required()
                    ->reactive()
                    ->preload()
                    ->options(function (callable $get) {
                        $collegeId = $get('college_id');
                        if ($collegeId) {
                            return Campus::where('college_id', $collegeId)->pluck('campus_name', 'id');
                        }
                        return [];
                    }),
                Forms\Components\Select::make('stream_id')
                    ->relationship('stream', 'name')
                    ->required(),
                Forms\Components\Select::make('eligibility_id')
                    ->label('Min. Eligibility')
                    ->relationship('minEligibility', 'name')
                    ->required(),
                Forms\Components\Select::make('degree_id')
                    ->relationship('degree', 'name')
                    ->required(),
                Forms\Components\Textarea::make('course_description')
                    ->nullable(),
                Forms\Components\TextInput::make('duration')
                    ->required(),
                Forms\Components\TextInput::make('fees')
                    ->nullable()
                    ->numeric(),
                Forms\Components\TextInput::make('eligibility')
                    ->nullable(),
                Forms\Components\Textarea::make('facility')
                    ->nullable(),
                Forms\Components\Textarea::make('document')
                    ->nullable(),
                Forms\Components\Textarea::make('remarks')
                    ->nullable(),
                Forms\Components\Textarea::make('other')
                    ->nullable(),
                Forms\Components\FileUpload::make('broucher')
                    ->nullable()
                    ->openable()
                    ->downloadable()
                    ->previewable()
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->directory(config('app.UPLOAD_DIR') . '/course/broucher'),
                Forms\Components\TextInput::make('program_link')
                    ->nullable(),
                Forms\Components\TextInput::make('own_comission')
                    ->nullable(),
                Forms\Components\TextInput::make('agent_comission')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name')->label('Country')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('college.college_name')->label('College')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('campus.campus_name')->label('Campus')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('stream.name')->label('Stream')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('minEligibility.name')->label('Min Eligibility')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('degree.name')->label('Degree')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('duration')->label('Duration')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('fees')->formatStateUsing(function ($record) {
                    return $record->fees . '(' . $record->country->currency . ')';
                })->label('Fees')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('eligibility')->label('Eligibility')->toggleable(),
                Tables\Columns\TextColumn::make('own_comission')->label('Own Comission')->toggleable(),
                Tables\Columns\TextColumn::make('agent_comission')->label('Agent Comission')->toggleable(),
                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')->label('Updated At')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country_id')->relationship('country', 'name')->label('Country')->multiple()->preload(),
                Tables\Filters\SelectFilter::make('college_id')->relationship('college', 'college_name')->label('College')->multiple()->preload(),
                Tables\Filters\SelectFilter::make('campus_id')->relationship('campus', 'campus_name')->label('Campus')->multiple()->preload(),
                Tables\Filters\SelectFilter::make('stream_id')->relationship('stream', 'name')->label('Stream')->multiple()->preload(),
                Tables\Filters\SelectFilter::make('eligibility_id')->relationship('minEligibility', 'name')->label('Min Eligibility')->multiple()->preload(),
                Tables\Filters\SelectFilter::make('degree_id')->relationship('degree', 'name')->label('Degree')->multiple()->preload(),
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
