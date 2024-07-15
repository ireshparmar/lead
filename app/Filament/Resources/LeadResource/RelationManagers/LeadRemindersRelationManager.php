<?php

namespace App\Filament\Resources\LeadResource\RelationManagers;

use App\Models\LeadReminder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeadRemindersRelationManager extends RelationManager
{
    protected static string $relationship = 'lead_reminders';

    protected static ?string $label = 'Reminder';

    protected static ?string $title = 'Reminders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('reminder_date_time')
                    ->required(),
                Forms\Components\Textarea::make('note')->rows(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('reminder_date')
            ->columns([
                Tables\Columns\TextColumn::make('reminder_date_time')->label('Reminder Date')->sortable(),
                Tables\Columns\TextColumn::make('note'),

                Tables\Columns\SelectColumn::make('status')->sortable()
                ->options(['Pending'=>'Pending','Done'=>'Done'])
                ->updateStateUsing(function (LeadReminder $record, $state) {
                    $status = $state == 'Pending' ? 'Pending' : 'Done';
                    $record->status = $status;
                    $record->save();
                })->getStateUsing( function (LeadReminder $record){
                    return $record->status;
                }),
            ])
            ->filters([
                SelectFilter::make('status')->options(['Pending'=>'Pending','Done'=>'Done'])->preload()->multiple(),

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function configureCreateAction(CreateAction $action): void
    {
        parent::configureCreateAction($action);
        $action->mutateFormDataUsing(function ($data) {
            // Example:
            $data['created_by'] = auth()->user()->id;

            return $data;
        });

    }
    protected function configureEditAction(EditAction $action): void
    {
        parent::configureEditAction($action);
        $action->mutateFormDataUsing(function ($data) {
            // Example:
            $data['updated_by'] = auth()->user()->id;

            return $data;
        });

    }
}
