<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\LeadReminder;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingLeadReminders extends BaseWidget
{
    protected static ?int $sort = 4;

    protected static ?string $heading = 'Lead Reminders';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LeadReminder::with('lead')->upcomingReminders()->where('status','Pending')->orderBy('reminder_date_time', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('lead.lead_unique_id')->label('Lead ID'),
                Tables\Columns\TextColumn::make('reminder_date_time')->label('Reminder Date & Time')->dateTime(),
                Tables\Columns\TextColumn::make('note')->label('Note'),
                Tables\Columns\SelectColumn::make('status')->label('Status')->options(['Pending'=>'Pending','Done'=>'Done'])
                ->updateStateUsing(function (LeadReminder $record, $state) {
                    $status = $state ? 'Done' : 'Pending';
                    $record->status = $status;
                    $record->save();
                })->getStateUsing( function (LeadReminder $record){
                    return $record->status;
                }),
            ]);
    }
}
