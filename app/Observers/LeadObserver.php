<?php

namespace App\Observers;

use App\Models\Lead;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LeadObserver
{
    /**
     * Handle the Lead "created" event.
     */
    public function created(Lead $lead): void
    {
        // Generate the random string with the auto-incrementing ID
        $randomString = 'NAND-'.$lead->id;
        $lead->lead_unique_id = $randomString;
        $lead->save();

        $users = User::role('Admin')->get();
        $resourceUrl = route('filament.admin.resources.leads.edit', $lead->id);
        Notification::make()
                         ->success()
                         ->title('Lead created')
                         ->body('The new lead has been created by agent ('.auth()->user()->name.').')
                         ->actions([
                            Action::make('view')
                                    ->url($resourceUrl)
                                    ->button()
                                    ->markAsRead()
                          ])
                         ->sendToDatabase($users);

    }

    /**
     * Handle the Lead "updated" event.
     */
    public function updated(Lead $lead): void
    {
        //Remove refund record and files if recorded is updated from Refund to any other status
        if($lead->getOriginal('status') == 'Refund'){
            if(!empty($lead->refund_docs)){

                foreach($lead->refund_docs as $refundDoc ){
                    Storage::disk(config('app.FILE_DISK'))->delete($refundDoc);
                }
                $lead->refund_reson = NULL;
                $lead->refund_docs = NULL;
                $lead->save();
            }
        }
    }

    /**
     * Handle the Lead "deleted" event.
     */
    public function deleted(Lead $lead): void
    {
        //
    }

    /**
     * Handle the Lead "restored" event.
     */
    public function restored(Lead $lead): void
    {
        //
    }

    /**
     * Handle the Lead "force deleted" event.
     */
    public function forceDeleted(Lead $lead): void
    {
        //
    }
}
