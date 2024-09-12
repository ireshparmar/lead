<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Tables;
use App\Models\StudentAdmissionDocument;
use Filament\Forms\Components\View;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class AdmissionDocumentsTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    public $recordId;




    public function table(Table $table): Table
    {

        return $table
            ->query(StudentAdmissionDocument::query()->where('student_admission_id', $this->recordId))
            ->columns([
                Tables\Columns\TextColumn::make('docType.name')->label('Document Type')->sortable()->toggleable(),
                Tables\Columns\ImageColumn::make('doc_name')
                    ->label('File')
                    ->width('80px')
                    ->height('80px')
                    ->square()
                    ->defaultImageUrl(url('/images/placeholder.jpg'))
                    ->getStateUsing(function (StudentAdmissionDocument $record): string {
                        $extension = pathinfo($record->doc_name);
                        if (!in_array(strtolower($extension['extension']), ['jpg', 'png', 'jpeg'])) {
                            return url('/images/placeholder.jpg');
                        }
                        return $record->doc_name;
                    })->toggleable(),
                TextColumn::make('doc_org_name')->label('File Name')->sortable()->toggleable(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Tables\Actions\Action::make('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->label('')
                    ->tooltip('Download')
                    ->action(function ($record) {
                        return response()->download(Storage::disk(config('app.FILE_DISK'))->path($record->doc_name));
                    }),
                Tables\Actions\DeleteAction::make(),
            ]);
    }



    public function render()
    {
        return view('livewire.admission-documents-table');
    }
}
