<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Expenses';

    protected static ?string $modelLabel = 'Expense';

    //protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        $schema = [
            Forms\Components\Select::make('category_id')
            ->label('Expense Category')
            ->options(ExpenseCategory::all()->pluck('name', 'id'))
            ->required(),
        Forms\Components\DatePicker::make('date')
            ->required(),
        Forms\Components\TextInput::make('amount')
            ->required()
            ->numeric()
            ->minValue(0),
        Forms\Components\Textarea::make('description'),
        ];

        $agentId = request()->query('agent_id');

        if($agentId){
            $schema[] =  Forms\Components\Hidden::make('agent_id')->default(request()->query('agent_id'));
        } else {
            $schema[] =  Forms\Components\Select::make('agent_id')
            ->relationship(
                name: 'agent',
                titleAttribute: 'name',
                modifyQueryUsing: fn (Builder $query) => $query->whereHas('roles', function ($query) {
                    $query->where('name', 'Agent');
             }),
            )
            ->required();
        }
        $schema[]= Forms\Components\FileUpload::make('doc_name')
                   ->label('Files')
                   ->directory(config('app.UPLOAD_DIR').'/expenses')
                   ->multiple()
                   ->downloadable()
                   ->openable()
                   ->reactive()
                   ->previewable(true)
                   ->storeFileNamesIn('doc_org_name')
                   ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png']);
        return $form
            ->schema($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agent.name')->label('Agent')->toggleable(),
                Tables\Columns\TextColumn::make('category.name')->label('Category')->toggleable(),
                Tables\Columns\TextColumn::make('date')->date()->toggleable(),
                Tables\Columns\TextColumn::make('amount')->money('inr')->toggleable(),
                Tables\Columns\TextColumn::make('description')->limit(50)->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('agent_id')->relationship('agent', 'name', fn (Builder $query) => $query->whereHas('roles', function ($query) {
                    $query->where('name', 'Agent');
             }))->preload()->multiple(),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->modifyQueryUsing(function (Builder $query) {
                $agentId = request()->query('agent_id');
                if($agentId){
                    return $query->where('agent_id', '=', $agentId);
                }

            });
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
