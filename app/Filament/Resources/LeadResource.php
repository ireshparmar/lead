<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Closure;
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
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;

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
                            Forms\Components\Select::make('agent_id')
                            ->relationship(
                                name: 'agent',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query, Model $record) {
                                    if (Auth::user()->hasRole('Admin') && $record->agent_id == $record->created_by) {
                                        $query->where('id', $record->agent_id);
                                        //this condition is for to not allow Admin or other staff to change Agent, if lead is created by agent
                                    }
                                    $query->whereHas('roles', function ($query) use ($record) {
                                        $query->where('name', 'Agent');

                                    });
                                })->visible(fn() => Auth::user()->hasRole('Admin')),

                                Forms\Components\Select::make('status')
                                ->options(config('app.leadStatus'))
                                ->default('New')
                                ->required(),

                            ])->columnSpan(1)->columns(2),
                            Forms\Components\Section::make('Documents')
                            ->schema([
                                        Repeater::make('documents')
                                            ->label('')
                                            ->relationship()
                                            ->schema([
                                                Forms\Components\FileUpload::make('doc_name')
                                                ->name('Select File')
                                                ->downloadable()
                                                ->openable()
                                                ->reactive()
                                                ->previewable(true)
                                                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                                ->preserveFilenames()
                                                ->afterStateUpdated(function ($state, callable $get, callable $set, $record, $context) {
                                                    $index = $context->getIndex();
                                                    $set("documents.{$index}.doc_name", !empty($state));
                                                })
                                                ->required(),
                                                Forms\Components\Select::make('doc_type')->name('type')
                                                ->options(config('app.leadDocType'))
                                                //->required(fn (callable $get) => !is_null($get('doc_name'))),
                                                ->required()
                                            ])
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                $data['user_id'] = auth()->id();
                                                return $data;
                                            })->defaultItems(0)->addActionLabel('Add Document'),
                             ])->collapsed()->columnSpan(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
                    ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
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
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->toggleable(),
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
                            Notification::make()
                            ->title('Lead assigned')
                            ->success()
                            ->send();
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
                SelectFilter::make('status')->options(config('app.leadStatus'))->preload()->multiple()

            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->visible(fn($record) => Auth::user()->hasRole('Admin') || $record->created_by ===  Auth::user()->id),
                    Action::make('payments')->form([
                        Repeater::make('payments')
                        ->label('')
                        ->relationship()
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
                                                // Debugging to ensure values are correct

                                                if ($totalPaid > $lead->amount) {
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }


}
