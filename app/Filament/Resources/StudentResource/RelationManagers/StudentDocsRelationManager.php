<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\StudentDocument;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction as DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentDocsRelationManager extends RelationManager
{
    protected static string $relationship = 'studentDocuments';

    protected static ?string $label = '';

    protected static ?string $title = 'Documents';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('doc_name')
                    ->required()
                    ->label('Document File')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->directory(config('app.UPLOAD_DIR') . '/studentDocs')
                    ->downloadable()
                    ->openable()
                    ->previewable(false)
                    ->storeFileNamesIn('doc_org_name'),

                Forms\Components\Select::make('doc_type_id')
                    ->relationship('docType', 'name', function ($query) {
                        $query->whereRaw("JSON_CONTAINS(module, '\"Student\"')")
                            ->orderByRaw("CASE WHEN type = 'Compulsory' THEN 0 ELSE 1 END")
                            ->orderBy('name');
                    })
                    ->required()
                    ->label('Document Type')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} - ({$record->type})"),

                Forms\Components\Textarea::make('note')
                    ->label('Note')
                    ->nullable(),


            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('doc_name')
            ->columns([
                Tables\Columns\TextColumn::make('docType.name')->label('Document Type')->sortable()->toggleable()->searchable(),
                Tables\Columns\ImageColumn::make('doc_name')
                    ->label('File')
                    ->width('80px')
                    ->height('80px')
                    ->square()
                    ->defaultImageUrl(url('/images/placeholder.jpg'))
                    ->getStateUsing(function (StudentDocument $record): string {
                        $extension = pathinfo($record->doc_name);
                        if (!in_array(strtolower($extension['extension']), ['jpg', 'png', 'jpeg'])) {
                            return url('/images/placeholder.jpg');
                        }
                        return $record->doc_name;
                    })->toggleable()->searchable(),

                Tables\Columns\TextColumn::make('doc_org_name')->label('Name')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('isVerified')
                    ->label('Verified Status')
                    ->color(fn(string $state): string => match ($state) {
                        'Verified' => 'success',
                        'Unverified' => 'danger',
                        'Reupload' => 'info',
                        default => 'gray',
                    })
                    ->badge()
                    ->sortable()
                    ->toggleable()
                    ->action(
                        Action::make('Verified Status')->form([
                            Select::make('isVerified')->options(config('app.verifiedStatus'))->required(),
                            Textarea::make('remark')
                        ])->action(function (StudentDocument $record, array $data) {
                            $record->isVerified = $data['isVerified'];
                            $record->remark = $data['remark'] ?? null; // Assuming `remark` is a column in your model
                            if ($data['isVerified'] == 'Verified') {
                                $record->verified_by = Auth::id();
                                $record->verified_date = Carbon::now();
                            } else {
                                $record->verified_by = null;
                                $record->verified_date = null;
                            }
                            $record->save();
                        })->mountUsing(function (Forms\ComponentContainer $form, Model $record) {
                            // Load existing payments data into the form
                            $form->fill([
                                'isVerified' => $record->isVerified,
                                'remark'  => $record->remark
                            ]);
                        })
                    ),
                Tables\Columns\TextColumn::make('verifiedBy.name')->label('Verified By')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('verified_date')->dateTime()->label('Verified Date')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('remark')->label('Verified Remark')->toggleable(),
                Tables\Columns\TextColumn::make('note')->label('Note'),
                Tables\Columns\TextColumn::make('createdBy.name')->label('Created By'),
                Tables\Columns\TextColumn::make('updatedBy.name')->label('Updated By'),
            ])
            ->filters([
                SelectFilter::make('isVerified')->label('Verified Status')->options(config('app.verifiedStatus'))->multiple()->preload(),
                SelectFilter::make('doc_type_id')->label('Document Type')->options(function () {
                    // Fetch options where module is 'Student'
                    return \App\Models\DocumentType::whereRaw("JSON_CONTAINS(module, '\"Student\"')")
                        ->pluck('name', 'id')
                        ->toArray();
                })
                    ->searchable()
                    ->multiple()
                    ->preload(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->label('')
                    ->tooltip('Download')
                    ->action(function ($record) {
                        return response()->download(Storage::disk(config('app.FILE_DISK'))->path($record->doc_name));
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    protected function configureDeleteAction(DeleteAction $action): void
    {
        $action->after(function ($record) {

            // Get the file path from the record
            $filePath = $record->doc_name; // Adjust this to your actual file path attribute
            // Check if the file exists and delete it
            if (Storage::disk(config('app.FILE_DISK'))->exists($filePath)) {
                Storage::disk(config('app.FILE_DISK'))->delete($filePath);
            }
        });
    }
}
