<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IntakeYearResource\Pages;
use App\Filament\Resources\IntakeYearResource\RelationManagers;
use App\Models\Intakeyear;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IntakeYearResource extends Resource
{
    protected static ?string $model = Intakeyear::class;

    protected static ?string $breadcrumb = 'Intake Year';

    protected static ?string $label = 'Intake Year';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Masters';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('inmonth_name')
                ->label('Year')
                ->unique(ignoreRecord:true)
                ->required(),
            Select::make('status')
                ->label('Status')
                ->options([
                    'Active' => 'Active',
                    'Inactive' => 'Inactive',
                ])
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('inyear_name')->label('Year')->sortable()->toggleable(),
                Tables\Columns\ToggleColumn::make('status')->label('Status')->sortable()->toggleable()->updateStateUsing(function (Intakeyear $record, $state) {
                    $status = $state ? 'Active' : 'Inactive';
                    $record->status = $status;
                    $record->save();
                })->getStateUsing( function (Intakeyear $record){
                    return $record->status == 'Active' ? 1 : 0;
                 }),
                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By')->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                    ]),
            ]) ->actions([
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
            'index' => Pages\ListIntakeYears::route('/'),
            'create' => Pages\CreateIntakeYear::route('/create'),
            'edit' => Pages\EditIntakeYear::route('/{record}/edit'),
        ];
    }
}
