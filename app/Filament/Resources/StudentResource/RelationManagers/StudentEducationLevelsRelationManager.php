<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\StudentEducationLevel;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class StudentEducationLevelsRelationManager extends RelationManager
{
    protected static string $relationship = 'studentEducationLevels';

    protected static ?string $label = '';

    protected static ?string $title = 'Education Levels';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('education_level_id')
                    ->relationship('educationLevel', 'name', modifyQueryUsing: fn(Builder $query) => $query->active())
                    ->required()
                    ->label('Education Level'),
                Select::make('status')
                    ->options(config('app.educationStatus'))
                    ->required()
                    ->label('Education Status')
                    ->reactive(),

                TextInput::make('school_or_uni')
                    ->label('School or University'),

                TextInput::make('degree_or_dept')
                    ->label('Degree or Department'),

                DatePicker::make('start_date')
                    ->required()
                    ->label('Start Date'),

                DatePicker::make('end_date')
                    ->label('End Date')
                    ->required(fn(callable $get) => $get('status') === 'Completed')
                    ->reactive(),

                Select::make('duration_id')
                    ->relationship('duration', 'name', modifyQueryUsing: fn(Builder $query) => $query->active())
                    ->required()
                    ->label('Duration')
                    ->reactive(),

                TextInput::make('gpa_or_percentage')
                    ->numeric()
                    ->label('GPA or Percentage'),

                Textarea::make('note')
                    ->label('Note'),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('educationLevel.name')->label('Education Level')->sortable()->toggleable(),
                TextColumn::make('status')->label('Education Status')->sortable()->toggleable(),
                TextColumn::make('school_or_uni')->label('School or University')->sortable()->toggleable(),
                TextColumn::make('degree_or_dept')->label('Degree or Department')->sortable()->toggleable(),
                TextColumn::make('isVerified')
                    ->label('Verified Status')
                    ->color(fn(string $state): string => match ($state) {
                        'Verified' => 'success',
                        'Unverified' => 'danger',
                        default => 'gray',
                    })
                    ->badge()
                    ->sortable()
                    ->toggleable()
                    ->action(
                        Action::make('Verified Status')->form([
                            Select::make('isVerified')->options(config('app.verifiedStatus'))->required(),
                            Textarea::make('remark')
                        ])->action(function (StudentEducationLevel $record, array $data) {
                            $record->isVerified = $data['isVerified'];
                            $record->remark = $data['remark'] ?? null; // Assuming `remark` is a column in your model
                            if ($data['isVerified'] == 'Verified') {
                                $record->verified_by = Auth::id();
                                $record->verified_date = Carbon::now();
                            } else {
                                $record->verified_by = null;
                                $record->verified_date = null;
                            }
                            $record->save();
                        })->mountUsing(function (Forms\ComponentContainer $form, Model $record) {
                            // Load existing payments data into the form
                            $form->fill([
                                'isVerified' => $record->isVerified,
                                'remark'  => $record->remark
                            ]);
                        })
                    ),
                TextColumn::make('verifiedBy.name')->label('Verified By')->sortable()->toggleable(),
                TextColumn::make('verified_date')->dateTime()->label('Verified Date')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('remark')->label('Verified Remark')->toggleable(),
                TextColumn::make('start_date')->label('Start Date')->sortable()->toggleable(),
                TextColumn::make('end_date')->label('End Date')->sortable()->toggleable(),
                TextColumn::make('duration.name')->label('Duration')->sortable()->toggleable(),
                TextColumn::make('gpa_or_percentage')->label('GPA or Percentage'),
                TextColumn::make('note')->label('Note'),
                TextColumn::make('createdBy.name')->label('Created By'),
                TextColumn::make('updatedBy.name')->label('Updated By'),
            ])
            ->filters([
                SelectFilter::make('status')->label('Education Status')->options(config('app.educationStatus'))->multiple()->preload(),
                SelectFilter::make('education_level_id')->label('Education Levels')->relationship('educationLevel', 'name')->multiple()->preload(),
                SelectFilter::make('duration_id')->label('Durations')->relationship('duration', 'name')->multiple()->preload(),
                SelectFilter::make('isVerified')->label('Verified Status')->options(config('app.verifiedStatus'))->multiple()->preload(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
