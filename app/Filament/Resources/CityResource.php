<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Masters';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('country_id')
                    ->label('Country')
                    ->relationship('country', 'name')
                    ->required()
                    ->live(),

                Select::make('state_id')
                    ->label('State')
                    ->relationship('state', 'name')
                    ->required()
                    ->reactive()
                    ->options(function (callable $get) {
                        $countryId = $get('country_id');
                        if ($countryId) {
                            return State::where('country_id', $countryId)->where('status','Active')->pluck('name', 'id');
                        }
                        return State::all()->pluck('name', 'id');
                    }),

                TextInput::make('name')
                    ->label('City Name')
                    ->minLength(2)
                    ->maxLength(255)
                    ->required()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('City Name'),
                Tables\Columns\TextColumn::make('state.name')->label('State'),
                Tables\Columns\TextColumn::make('country.name')->label('Country'),
                Tables\Columns\ToggleColumn::make('status')
                ->searchable()
                ->sortable()
                ->updateStateUsing(function (City $record, $state) {
                    $status = $state ? 'Active' : 'Inactive';
                    $record->status = $status;
                    $record->save();
                })
                ->getStateUsing( function (City $record){
                    return $record->status == 'Active' ? 1 : 0;
                 })

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country_id')
                    ->relationship('country', 'name')
                    ->label('Country'),

                Tables\Filters\SelectFilter::make('state_id')
                    ->relationship('state', 'name')
                    ->label('State'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
