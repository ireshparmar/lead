<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentTypeResource\Pages;
use App\Filament\Resources\DocumentTypeResource\RelationManagers;
use App\Models\DocumentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentTypeResource extends Resource
{
    protected static ?string $model = DocumentType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Masters';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Document Name'),

                Forms\Components\Select::make('module')
                    ->options(config('app.docTypeModule'))
                    ->multiple()
                    ->required()
                    ->label('Module')
                    ->afterStateHydrated(fn($component, $state) => $component->state(is_array($state) ? $state : json_decode($state, true))), // Handle both cases


                Forms\Components\Select::make('type')
                    ->options(config('app.docTypeType'))
                    ->required()
                    ->label('Type'),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(config('app.status'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Document Name')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('module')
                    ->label('Module')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(function ($state) {
                        // Decode JSON string to array
                        $array = json_decode($state, true);

                        // Check if $array is an array
                        if (is_array($array)) {
                            // Implode the array into a comma-separated string
                            return implode(', ', $array);
                        }

                        // Return $state as-is if it's not a valid array
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('type')->sortable()->searchable()->toggleable(),
                Tables\Columns\ToggleColumn::make('status')->label('Status')->sortable()->toggleable()->updateStateUsing(function (DocumentType $record, $state) {
                    $status = $state ? 'Active' : 'Inactive';
                    $record->status = $status;
                    $record->save();
                })->getStateUsing(function (DocumentType $record) {
                    return $record->status == 'Active' ? 1 : 0;
                }),
                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By')->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(config('app.status'))
                    ->multiple(),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options(config('app.docTypeType'))
                    ->multiple(),
                Tables\Filters\SelectFilter::make('module')
                    ->label('Module')
                    ->options(config('app.docTypeModule'))
                    ->multiple()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['values'])) {
                            $query->where(function ($q) use ($data) {
                                foreach ($data['values'] as $value) {
                                    $q->orWhereRaw("JSON_CONTAINS(module, '\"$value\"')"); // Directly insert value into query string

                                }
                            });
                            //  dd($query->toSql(), $query->getBindings());
                        }
                    }),


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
            'index' => Pages\ListDocumentTypes::route('/'),
            'create' => Pages\CreateDocumentType::route('/create'),
            'edit' => Pages\EditDocumentType::route('/{record}/edit'),
        ];
    }
}
