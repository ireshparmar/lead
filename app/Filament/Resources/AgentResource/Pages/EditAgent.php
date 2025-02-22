<?php

namespace App\Filament\Resources\AgentResource\Pages;

use App\Filament\Resources\AgentResource;
use App\Models\AgentDoc;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgent extends EditRecord
{
    protected static string $resource = AgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     dd($this->form->getState());
    //     if (!empty($data['doc_org_name'])) {
    //         foreach ($data['doc_org_name'] as $key =>   $doc) {
    //             $mimeType = strtolower(pathinfo($doc)['extension']);
    //             AgentDoc::create([
    //                 'agent_id' => $this->record->id,
    //                 'doc_name' => $key,
    //                 'doc_org_name' => $doc,
    //                 'created_by' => auth()->user()->id,
    //                 'mime_type' => $mimeType,
    //             ]);
    //         }
    //     }

    //     return $data;
    // }
}
