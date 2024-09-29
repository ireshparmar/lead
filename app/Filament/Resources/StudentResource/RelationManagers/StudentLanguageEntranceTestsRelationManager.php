<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\StudentLanguageEntranceTest;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Type\Decimal;

class StudentLanguageEntranceTestsRelationManager extends RelationManager
{
    protected static string $relationship = 'languageEntranceTest';

    protected static ?string $label = '';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('enterance_exam_id')
                    ->relationship('enteranceExam', 'name', modifyQueryUsing: fn(Builder $query) => $query->active()->type('Language'))
                    ->required(),

                TextInput::make('test_center')
                    ->label('Test Center')
                    ->required()
                    ->maxLength(255),

                DatePicker::make('test_date')
                    ->label('Test Date')
                    ->required(),

                DatePicker::make('expire_date')
                    ->label('Expire Date')
                    ->required(),

                TextInput::make('read_score')
                    ->label('Reading Score')
                    ->required()
                    ->numeric(),

                TextInput::make('write_score')
                    ->label('Writing Score')
                    ->required()->numeric(),

                TextInput::make('speak_score')
                    ->label('Speaking Score')
                    ->required()->numeric(),

                TextInput::make('listen_score')
                    ->label('Listening Score')
                    ->required()->numeric(),

                TextInput::make('overall_score')
                    ->label('Overall Score')
                    ->required()->numeric(),

                TextInput::make('report_no')
                    ->label('Report Number')
                    ->maxLength(50)
                    ->nullable(),

                TextInput::make('username')
                    ->label('Username')
                    ->maxLength(100)
                    ->nullable(),

                TextInput::make('password')
                    ->label('Password')
                    ->maxLength(255)
                    ->nullable(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('enteranceExam.name')
                    ->label('Test Name')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('test_center')
                    ->label('Test Center')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('test_date')
                    ->label('Test Date')
                    ->date()
                    ->toggleable(),

                TextColumn::make('expire_date')
                    ->label('Expire Date')
                    ->date()
                    ->toggleable(),
                TextColumn::make('isVerified')
                    ->label('Verified Status')
                    ->color(fn(string $state): string => match ($state) {
                        'Verified' => 'success',
                        'Unverified' => 'danger',
                        'Reupload' => 'info',
                        default => 'gray',
                    })
                    ->badge()
                    ->sortable()
                    ->toggleable()
                    ->action(
                        Action::make('Verified Status')->form([
                            Select::make('isVerified')->options(config('app.verifiedStatus'))->required(),
                            Textarea::make('remark')
                        ])->action(function (StudentLanguageEntranceTest $record, array $data) {
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
                    )->toggleable(),
                Tables\Columns\TextColumn::make('verifiedBy.name')->label('Verified By')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('verified_date')->dateTime()->label('Verified Date')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('remark')->label('Verified Remark')->toggleable(),
                TextColumn::make('read_score')
                    ->label('Reading Score')->toggleable(),

                TextColumn::make('write_score')
                    ->label('Writing Score')->toggleable(),

                TextColumn::make('speak_score')
                    ->label('Speaking Score')->toggleable(),

                TextColumn::make('listen_score')
                    ->label('Listening Score')->toggleable(),

                TextColumn::make('overall_score')
                    ->label('Overall Score')->toggleable(),

                TextColumn::make('report_no')
                    ->label('Report Number')->toggleable(),

                TextColumn::make('username')
                    ->label('Username')->toggleable(),

                TextColumn::make('password')
                    ->label('Password')->toggleable(),


                TextColumn::make('createdBy.name')->label('Created By')->toggleable(),
                TextColumn::make('updatedBy.name')->label('Updated By')->toggleable(),
            ])
            ->filters([
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
