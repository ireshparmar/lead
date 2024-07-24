<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgentResource\Pages;
use App\Filament\Resources\AgentResource\Pages\CreateAgent;
use App\Filament\Resources\AgentResource\RelationManagers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Models\Agent;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AgentResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Agents';

    protected static ?string $modelLabel = 'Agents';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->mutateDehydratedStateUsing(fn($state)=> Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(Page $livewire) => ($livewire instanceof CreateAgent))
                    ->maxLength(255),
                    Forms\Components\Select::make('status')
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive'
                    ])->required()->preload(),
                Forms\Components\Select::make('roles')
                    ->relationship('roles','name',function($query){
                        $query->where('name', 'Agent');
                    }) ->default(function () {
                        return Role::where('name', 'Agent')->first()->id ?? null; // Set the default to the first active role or null if none found
                    })->preload(),
                Forms\Components\Select::make('permissions')
                    ->multiple()
                    ->relationship('permissions','name')->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('status')
                    ->updateStateUsing(function (User $record, $state) {
                                if($state){
                                    $record->status = 'Active';
                                } else {
                                    $record->status = 'Inactive';
                                }
                                $record->save();
                    })->getStateUsing( function (User $record){
                        return $record->status == 'Active' ? 1 : 0;
                     }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('addExpense')
                    ->icon('heroicon-s-currency-rupee')
                    ->label('Add Expense')
                    ->url(fn (User $record) => route('filament.admin.resources.expenses.create', ['agent_id' => $record->id])),
                    Tables\Actions\Action::make('addExpense')
                    ->icon('heroicon-s-list-bullet')
                    ->label('View Expenses')
                    ->url(fn (User $record) => route('filament.admin.resources.expenses.index', ['agent_id' => $record->id])),
                ])

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->whereHas('roles', function ($query) {
                    $query->where('name', 'Agent');
             })

          );
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
            'index' => Pages\ListAgents::route('/'),
            'create' => Pages\CreateAgent::route('/create'),
            'edit' => Pages\EditAgent::route('/{record}/edit'),
          //  'add-expense' => Pages\AddExpense::route('/{record}/add-expense'),

        ];
    }
}
