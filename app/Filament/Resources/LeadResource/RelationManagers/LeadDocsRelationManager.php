<?php

namespace App\Filament\Resources\LeadResource\RelationManagers;

use App\Models\Lead;
use App\Models\LeadDoc;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class LeadDocsRelationManager extends RelationManager
{
    protected static string $relationship = 'lead_docs';

    protected static ?string $label = 'Document';

    protected static ?string $title = 'Documents';

    public function form(Form $form): Form
    {
        return $form
            ->schema([


                Forms\Components\Select::make('doc_type')->name('type')
                ->options(config('app.leadDocType'))
                //->required(fn (callable $get) => !is_null($get('doc_name'))),
                ->required()
                ->live()
                ->rules([
                    fn (RelationManager $livewire, Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($livewire, $get) {
                        $count = LeadDoc::where(['lead_id' => $livewire->getOwnerRecord()->id,'doc_type' =>$value])
                        ->where('id','!=',$get('id'))->count();
                        if($count >=1 && $get('doc_type')!='other'){
                            $fail('The document of this type is already uploaded.');
                        }

                    },

                ]),
                Forms\Components\TextInput::make('other_type')
                                ->label('Other')
                                ->default(null)
                                ->hidden(fn (Get $get) => $get('doc_type') !== 'other')
                                ->requiredIf('doc_type', 'other'),
                                Forms\Components\FileUpload::make('doc_name')
                                ->name('Select File')
                                ->downloadable()
                                ->openable()
                                ->reactive()
                                ->previewable(true)
                                ->storeFileNamesIn('doc_org_name')
                                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                ->afterStateUpdated(function ($state, callable $get, callable $set, $record, $context) {
                                    //$index = $context->getIndex();
                                   // $set("documents.{$index}.doc_name", !empty($state));
                                })
                                ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('doc_name')
            ->columns([
                Tables\Columns\ImageColumn::make('doc_image')
                ->label('Image')
                ->width('80px')
                ->height('80px')
                ->square()
                ->defaultImageUrl(url('/images/placeholder.jpg'))
                ->getStateUsing(function (LeadDoc $record): string {
                    $extension = pathinfo($record->doc_name);
                    if(!in_array(strtolower($extension['extension']),['jpg','png','jpeg'])){
                        return url('/images/placeholder.jpg');
                    }
                    return $record->doc_name;
                }),
                Tables\Columns\TextColumn::make('doc_org_name')->label('Name')->sortable(),
                Tables\Columns\TextColumn::make('doc_type')->label('Type')
                ->getStateUsing(function (LeadDoc $record): string {
                    if($record->doc_type == 'other'){
                        return config('app.leadDocType')[$record->doc_type].' ('.$record->other_type.')';
                    }else {
                        return config('app.leadDocType')[$record->doc_type];

                    }
                 })->sortable(),

            ])
            ->filters([
                SelectFilter::make('doc_type')->options(config('app.leadDocType'))->preload()->multiple(),

            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('Download')
                ->icon('heroicon-o-arrow-down-tray')
                ->label('')
                ->tooltip('Download')
                ->action(function ($record){
                    return response()->download(Storage::disk(config('app.FILE_DISK'))->path($record->doc_name));
                })
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
            $data['user_id'] = auth()->user()->id;

            return $data;
        });
    }
}
