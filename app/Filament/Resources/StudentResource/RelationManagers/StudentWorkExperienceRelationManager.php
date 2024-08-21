<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\StudentWorkExperience;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class StudentWorkExperienceRelationManager extends RelationManager
{
    protected static string $relationship = 'workExperience';

    protected static ?string $label = '';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('company_name')
                    ->label('Company Name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('company_address')
                    ->label('Company Address')
                    ->maxLength(255),

                Forms\Components\TextInput::make('designation')
                    ->label('Designation')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Checkbox::make('is_working')
                    ->inline()
                    ->label('Currently Working Here')
                    ->reactive(),

                Forms\Components\DatePicker::make('from_date')
                    ->label('From Date')
                    ->required(),

                Forms\Components\DatePicker::make('to_date')
                    ->label('To Date')
                    ->reactive()
                    ->required(fn(callable $get) => $get('is_working') == '1'),
                Forms\Components\TextInput::make('job_type')
                    ->label('Job Type')
                    ->maxLength(255),

                Forms\Components\Textarea::make('job_description')
                    ->label('Job Description'),


            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('companyname')
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Company Name')
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('company_address')
                    ->label('Company Address')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('is_working')
                    ->label('Working Here')
                    ->getStateUsing(function ($record) {
                        return $record->is_working ? 'Yes' : 'No';
                    })
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('job_type')
                    ->label('Job Type')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('job_descrioption')
                    ->label('Job Description')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('from_date')
                    ->date()
                    ->label('From Date')
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('to_date')
                    ->date()
                    ->label('To Date')
                    ->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('isVerified')
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
                        ])->action(function (StudentWorkExperience $record, array $data) {
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
