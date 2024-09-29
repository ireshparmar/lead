<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Purpose;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Masters';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('purpose_id')
                ->relationship('purpose','purpose_name')
                ->required(),
            TextInput::make('service_name')
                ->label('Service Name')
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
            Tables\Columns\TextColumn::make('service_name')->label('Service Name')->sortable()->toggleable(),
            Tables\Columns\TextColumn::make('purpose.purpose_name')->label('Purpose')->sortable()->toggleable(),
            Tables\Columns\ToggleColumn::make('status')->label('Status')->sortable()->toggleable()->updateStateUsing(function (Service $record, $state) {
                $status = $state ? 'Active' : 'Inactive';
                $record->status = $status;
                $record->save();
            })->getStateUsing( function (Service $record){
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
            Tables\Filters\SelectFilter::make('purpose_id')
                ->label('Pupose')
                ->options(Purpose::pluck("purpose_name","id")->toArray())
        ])->actions([
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
