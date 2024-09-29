<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IntakeMonthResource\Pages;
use App\Filament\Resources\IntakeMonthResource\RelationManagers;
use App\Models\Intakemonth;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IntakeMonthResource extends Resource
{
    protected static ?string $model = Intakemonth::class;

    protected static ?string $breadcrumb = 'Intake Month';

    protected static ?string $label = 'Intake Month';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Masters';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('inmonth_name')
                    ->label('Month')
                    ->unique(ignoreRecord: true)
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(config('app.status'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('inmonth_name')->label('Month')->sortable()->toggleable(),
                Tables\Columns\ToggleColumn::make('status')->label('Status')->sortable()->toggleable()->updateStateUsing(function (Intakemonth $record, $state) {
                    $status = $state ? 'Active' : 'Inactive';
                    $record->status = $status;
                    $record->save();
                })->getStateUsing(function (Intakemonth $record) {
                    return $record->status == 'Active' ? 1 : 0;
                }),
                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By')->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(config('app.status')),
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
            'index' => Pages\ListIntakeMonths::route('/'),
            'create' => Pages\CreateIntakeMonth::route('/create'),
            'edit' => Pages\EditIntakeMonth::route('/{record}/edit'),
        ];
    }
}
