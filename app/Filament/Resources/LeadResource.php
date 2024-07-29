<?php

namespace App\Filament\Resources;

use App\Filament\Imports\LeadImporter;
use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\Pages\ImportLead;
use App\Filament\Resources\LeadResource\RelationManagers;
use App\Filament\Resources\LeadResource\RelationManagers\LeadDocsRelationManager;
use App\Filament\Resources\LeadResource\RelationManagers\LeadRemindersRelationManager;
use App\Imports\LeadsImport;
use App\Livewire\LeadImport;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\LeadAssigned;
use Carbon\Carbon;
use Closure;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Filament\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Livewire\DatabaseNotifications;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Modal\Actions\ButtonAction;
use Filament\Pages\Page;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\ModalAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\HtmlString;
use LaraZeus\Popover\Tables\PopoverColumn;
use Livewire\Component;
use Livewire\Mechanisms\HandleComponents\ComponentContext;
use Maatwebsite\Excel\Facades\Excel;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('full_name')
                                ->maxLength(255)
                                ->required()
                                ->default(null),
                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord:true)
                                ->default(null),
                            Forms\Components\TextInput::make('phone')
                                ->tel()
                                ->required()
                                ->maxLength(255)
                                ->default(null),
                            Forms\Components\TextInput::make('passport_no')
                                ->maxLength(255)
                                ->default(null),
                            Forms\Components\TextInput::make('address')
                                ->maxLength(255)
                                ->default(null),
                            Forms\Components\Select::make('visa_type_id')
                                ->relationship('visaType','name')
                                ->required(),
                            Forms\Components\Select::make('country_id')
                                ->relationship('country','name')
                                ->required(),

                                Forms\Components\Select::make('job_offer')
                                ->options(['Yes'=>'Yes',
                                'No'=>'No']),
                            Forms\Components\TextInput::make('amount')
                                 ->numeric()
                                 ->inputMode('decimal') ->visible(fn() => Auth::user()->hasRole('Admin')|| Auth::user()->hasRole('Staff')),
                            Forms\Components\Select::make('assigned_to')
                            ->label('Assigned To')
                            ->relationship(
                                name: 'agent',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->whereHas('roles', function ($query) {
                                    $query->where('name', 'Staff');
                            }),
                            ) ->visible(fn() => Auth::user()->hasRole('Admin')),
                            DatePicker::make('created_date')->required(),
                            Forms\Components\Select::make('agent_id')
                            ->relationship(
                                name: 'agent',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query, ?Model $record) {
                                    if (Auth::user()->hasRole('Admin') && isset($record->agent_id) && $record->agent_id == $record->created_by) {
                                           $query->where('id', $record->agent_id);

                                        //this condition is for to not allow Admin or other staff to change Agent, if lead is created by agent
                                    }
                                    $query->whereHas('roles', function ($query) use ($record) {
                                        $query->where('name', 'Agent');

                                    });
                                })->visible(fn() => Auth::user()->hasRole('Admin')),

                                Forms\Components\Select::make('status')
                                ->options(config('app.leadStatus'))
                                ->live()
                                ->default('New')
                                ->required(),
                                Forms\Components\TextInput::make('refund_amount')
                                ->label('Refund Amount')
                                ->numeric()
                                ->inputMode('decimal')
                                ->default(null)
                                ->hidden(fn (Get $get) => $get('status') !== 'Refund')
                                ->requiredIf('status', 'Refund'),
                                Forms\Components\Textarea::make('refund_reson')
                                ->label('Refund Reason')
                                ->default(null)
                                ->hidden(fn (Get $get) => $get('status') !== 'Refund'),
                                Forms\Components\FileUpload::make('refund_docs')
                                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                ->multiple()
                                ->downloadable()
                                ->openable()
                                ->previewable(false)
                                ->preserveFilenames()
                                ->directory(config('app.UPLOAD_DIR').'/leadDocs/refund')
                                ->label('Refund Documents')
                                ->hidden(fn (Get $get) => $get('status') !== 'Refund'),


                            ])->columnSpan(2)->columns(2),



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
                    ->headerActions([



                        Action::make('Import')
                        ->label('Import')
                        ->url(ImportLead::getUrl())

                        // ImportAction::make()
                        //     ->importer(LeadImporter::class)

                    ])
                    ->columns([
                Tables\Columns\TextColumn::make('lead_unique_id')
                    ->label('Lead Id')
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return !empty($record->lead_unique_id) ? $record->lead_unique_id : '-';
                    }),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    Tables\Columns\TextColumn::make('country.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('passport_no')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(true),
                PopoverColumn::make('Payments')
                    ->label('Total Payments(₹)')
                    ->getStateUsing(function ($record) {
                        return $record->payments->sum('amount');
                    })
                    ->offset(10) // int px, for more: https://alpinejs.dev/plugins/anchor#offset
                    ->popOverMaxWidth('none')
                    ->content(fn($record) => view('filament.resources.lead-resource.payment-list', ['record' => $record]))
                    ->trigger('hover')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->toggleable(),
                    Tables\Columns\TextColumn::make('created_date')
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return !empty($record->created_date) ? Carbon::parse($record->created_date)->format('d M Y') : '-';
                    }),
                Tables\Columns\TextColumn::make('job_offer')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('pcc')
                ->getStateUsing(function ($record) {
                    return $record->hasPccDocument() ? 'Yes' : 'No';
                })
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('visaType.name')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('agent.name')
                    ->label('Agent')
                    ->numeric()
                    ->sortable()
                    ->toggleable() ->visible(fn() => Auth::user()->hasRole('Admin')),
                Tables\Columns\SelectColumn::make('assigned_to')
                    ->options(User::whereHas('roles', function ($query) {
                        $query->where('name', 'Staff');
                 })->pluck("name","id")->toArray())
                 ->visible(fn() => Auth::user()->hasRole('Admin'))
                ->updateStateUsing(function (Lead $record, $state) {
                        $record->assigned_to = $state;
                        $record->save();

                        if($state){

                            $recipient = User::find($state);

                            // Create the data for the notification
                            $data = [
                                'message' => 'A lead has been assigned to you.'
                            ];
                         $resourceUrl = route('filament.admin.resources.leads.edit', $record->id);
                         //   $recepient->notify(new LeadAssigned($data));
                         Notification::make()
                         ->title('A lead has been assigned to you.')
                         ->actions([
                            NotificationAction::make('view')
                                    ->url($resourceUrl)
                                    ->button()
                                    ->markAsRead()
                          ])
                         ->sendToDatabase($recipient);

                     event(new DatabaseNotificationsSent($recipient));




                        }

                })
                ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id','desc')
            ->filters([
                SelectFilter::make('visaType')->relationship('visaType', 'name', fn (Builder $query) => $query)->preload()->multiple(),
                SelectFilter::make('status')->options(config('app.leadStatus'))->preload()->multiple(),
                SelectFilter::make('country')->relationship('country', 'name', fn (Builder $query) => $query)->preload()->multiple(),
                SelectFilter::make('payment_count')
                ->label('Payment Count')
                ->options(config('app.paymentFilter'))
                ->query(function (Builder $query, array $data) {

                    if (!empty($data['values'])) {
                        $paymentCounts = array_map('intval', $data['values']);
                        $query->whereHas('payments', function (Builder $query) use ($paymentCounts) {
                            $query->havingRaw('COUNT(lead_payments.id) IN (' . implode(',', $paymentCounts) . ')');
                        }, '>=', 1);

                    }
                })
                ->preload()
                ->multiple(),
                SelectFilter::make('pcc')
                              ->label('Pcc')
                              ->options(['Yes'=>'Yes','No'=>'No'])
                              ->query(function (Builder $query, array $data) {
                                if (!empty($data['value'])) {
                                    if($data['value'] == 'Yes'){
                                        $query->whereHas('lead_docs', function (Builder $query) {
                                            $query->where('doc_type', 'pcc');
                                        });
                                    } else {
                                        $query->whereHas('lead_docs', function (Builder $query) {
                                            $query->where('doc_type', '!=', 'pcc');
                                        });
                                    }
                                }
                              })


            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()->visible(!auth()->user()->hasRole('Agent')),
                    Tables\Actions\DeleteAction::make()->visible(fn($record) => ((Auth::user()->hasRole('Admin') || $record->created_by ===  Auth::user()->id) && !auth()->user()->hasRole('Agent'))),
                    Action::make('payments')->form([
                        Repeater::make('payments')
                        ->label('')
                        ->relationship()
                        ->maxItems(3)
                        ->schema([
                            Forms\Components\TextInput::make('amount')
                                 ->numeric()
                                 ->inputMode('decimal')
                                 ->required()
                                 ->live()
                                 ->rules([
                                    fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                        $leadId = $get('lead_id') ?? null;
                                        if ($leadId) {
                                            $lead = Lead::find($leadId);
                                            if ($lead) {
                                                // Existing payments from the database
                                                $totalPaid = 0;

                                                // New payments from the Repeater field
                                                $repeaterValues = $get('../../payments'); // Ensure 'payments' matches your Repeater field name
                                                if(!empty($repeaterValues)){
                                                    foreach($repeaterValues as $item){
                                                        $totalPaid+=$item['amount'];
                                                    }
                                                }
                                                if(empty($lead->amount)){
                                                    $fail('Please update amount in lead before adding payment detail.');
                                                }
                                                // Debugging to ensure values are correct
                                                if ($totalPaid > $lead->amount && !empty($lead->amount)) {
                                                    $fail('The total payments exceed the total amount defined in the lead.');
                                                }
                                            }
                                        }
                                    },
                                ]),
                             Forms\Components\DatePicker::make('payment_date')
                                 ->required(),


                        ])
                        ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                            $data['user_id'] = auth()->id();
                            return $data;
                        })
                        ->addActionLabel('Add payment')->orderable(false)->columns(2)
                    ])->mountUsing(function (Forms\ComponentContainer $form, Model $record) {
                        // Load existing payments data into the form
                        $form->fill([
                            'payments' => $record->payments->toArray(),
                            'lead_id'  => $record->id
                        ]);
                    })->icon('heroicon-s-currency-rupee')


                ])

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->visible(function ()  {
                       return Auth::user()->hasRole('Admin');
                    }),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('Staff')) {
                    return $query->where(function ($query) {
                        $query->where('assigned_to', '=', auth()->id())
                        ->orWhere('created_by','=', auth()->id());
                });
                }
                if (auth()->user()->hasRole('Agent')) {
                    return $query->where(function ($query) {
                            $query->where('agent_id', '=', auth()->id())
                            ->orWhere('created_by','=', auth()->id());
                    });
                }
            });
    }



    public static function getRelations(): array
    {
        return [
            LeadDocsRelationManager::class,
            LeadRemindersRelationManager::class

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
            'import-lead' =>Pages\ImportLead::route('/import-lead')
        ];
    }


}
