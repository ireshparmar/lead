<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\Package;
use App\Models\StudentPaymentDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;

class StudentFeesRelationManager extends RelationManager
{
    protected static string $relationship = 'studentFees';

    protected $listeners = ['refreshStudentFeesRelationManager' => '$refresh'];


    protected static ?string $label = '';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('package_id')
                    ->label('Package Name')
                    ->relationship('package', 'package_name')
                    ->options(Package::where('package_type', 'Student')->pluck('package_name', 'id'))
                    ->required(),
                Forms\Components\TextInput::make('total_amount')
                    ->label('Total Amount')
                    ->numeric()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('fees')
            ->columns([
                TextColumn::make('package.package_name')->label('Package Name')->sortable()->toggleable(),
                TextColumn::make('total_amount')->label('Total Amount')->sortable()->toggleable(),
                TextColumn::make('pending_amount')
                    ->label('Pending Amount')
                    ->state(function ($record) {
                        if ($record instanceof \App\Models\StudentFee) {
                            $totalPayments = $record->payments()->sum('payment_amount');
                            return $record->total_amount - $totalPayments;
                        }
                        return 0;
                    }),
                TextColumn::make('createdBy.name')->sortable()->toggleable(),
                TextColumn::make('updatedBy.name')->sortable()->toggleable(),
                TextColumn::make('created_at')->date()->toggleable(),
                TextColumn::make('updated_at')->date()->sortable()->toggleable(),
            ])
            ->filters([
                //
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
