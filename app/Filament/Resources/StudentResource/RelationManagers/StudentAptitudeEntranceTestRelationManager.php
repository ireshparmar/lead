<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\StudentAptitudeEntranceTest;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class StudentAptitudeEntranceTestRelationManager extends RelationManager
{
    protected static string $relationship = 'aptitudeEntranceTest';

    protected static ?string $label = '';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Test Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('test_center')
                    ->label('Test Center')
                    ->required()
                    ->maxLength(255),

                Forms\Components\DatePicker::make('test_date')
                    ->label('Test Date')
                    ->required(),

                Forms\Components\DatePicker::make('expire_date')
                    ->label('Expire Date')
                    ->required(),

                Forms\Components\TextInput::make('verbal_reasoning')
                    ->label('Verbal Reasoning Score')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('quantitative_reasoning')
                    ->label('Quantitative Reasoning Score')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('analytical_reasoning')
                    ->label('Analytical Reasoning Score')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('overall_score')
                    ->label('Overall Score')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('report_no')
                    ->label('Report Number')
                    ->maxLength(50),

                Forms\Components\TextInput::make('username')
                    ->label('Username')
                    ->maxLength(100),

                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Test Name')
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('test_center')
                    ->label('Test Center')
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('test_date')
                    ->label('Test Date')
                    ->date()
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('expire_date')
                    ->label('Expire Date')
                    ->date()
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('isVerified')
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
                        ])->action(function (StudentAptitudeEntranceTest $record, array $data) {
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
                Tables\Columns\TextColumn::make('verbal_reasoning')
                    ->label('Verbal Reasoning Score')
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('quantitative_reasoning')
                    ->label('Quantitative Reasoning Score')
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('analytical_reasoning')
                    ->label('Analytical Reasoning Score')
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('overall_score')
                    ->label('Overall Score')
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('report_no')
                    ->label('Report Number')
                    ->sortable()->toggleable(),


                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By')->toggleable(),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By')->toggleable(),
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
