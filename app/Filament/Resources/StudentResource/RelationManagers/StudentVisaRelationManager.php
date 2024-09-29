<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\DocumentType;
use App\Models\StudentVisa;
use App\Models\StudentVisaDocument;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentVisaRelationManager extends RelationManager
{
    protected static string $relationship = 'studentVisas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('college_name')
                    ->disabled()
                    ->label('College')
                    ->maxLength(255)
                    ->formatStateUsing(function ($record) {
                        return  $record->studentAdmission?->college?->college_name;
                    }),

                Forms\Components\TextInput::make('degree_name')
                    ->disabled()
                    ->label('Degree')
                    ->formatStateUsing(function ($record) {
                        return  $record->studentAdmission?->degree?->name;
                    }),
                Forms\Components\TextInput::make('country_name')
                    ->disabled()
                    ->label('Country')
                    ->formatStateUsing(function ($record) {
                        return  $record->studentAdmission?->country?->name;
                    }),
                Forms\Components\TextInput::make('studentAdmission.created_at')
                    ->disabled()
                    ->label('Admission Date')
                    ->formatStateUsing(function ($record) {
                        return $record ? Carbon::parse($record->studentAdmission->created_at)->format('d/m/Y') : null;
                    }),
                Forms\Components\TextInput::make('studentAdmission.app_number')
                    ->disabled()
                    ->label('Application No.')
                    ->formatStateUsing(function ($record) {
                        return  $record->studentAdmission?->app_number;
                    }),
                Forms\Components\TextInput::make('visa_type')
                    ->label('Visa Type')
                    ->required(),
                Forms\Components\Select::make('intakeyear_id')
                    ->relationship('intakeYear', 'inyear_name') // Correct relationship reference
                    ->label('Intake Year'),
                Forms\Components\Select::make('intakemonth_id')
                    ->relationship('intakeMonth', 'inmonth_name') // Correct relationship reference
                    ->label('Intake Month'),
                Forms\Components\DatePicker::make('app_submission_date')
                    ->label('App. Submission Date'),
                Forms\Components\Select::make('status')
                    ->options(config('app.studentVisaStatus'))
                    ->label('Status')
                    ->required(),
                Forms\Components\Select::make('visa_done')
                    ->options(['Yes' => 'Yes', 'No' => 'No'])
                    ->label('Visa Done?'),
                Fieldset::make('Visa Details')
                    ->schema([
                        Forms\Components\TextInput::make('visa_no')
                            ->label('Visa No.')
                            ->required(),
                        Forms\Components\DatePicker::make('visa_date')
                            ->required()
                            ->label('Visa Date'),
                        Forms\Components\DatePicker::make('expire_date')
                            ->required()
                            ->label('Expire Date'),
                    ]),
                Fieldset::make('Extra Details')
                    ->schema([

                        Forms\Components\DatePicker::make('travel_date')
                            ->required()
                            ->label('Travel Date'),
                        Forms\Components\TextInput::make('ticket')
                            ->required()
                            ->label('Ticket'),
                        Forms\Components\Textarea::make('contact_detail')
                            ->label('Contact Detail'),
                        Forms\Components\Textarea::make('address')
                            ->label('Address'),
                        Forms\Components\Textarea::make('more_detail')
                            ->label('More Detail'),
                        Forms\Components\Textarea::make('remark')
                            ->label('Remarks'),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('')
            ->columns([
                Tables\Columns\TextColumn::make('studentVisas.studentAdmission.app_number')->toggleable(),
                Tables\Columns\TextColumn::make('visa_no')->toggleable(),
                Tables\Columns\TextColumn::make('visa_type')->toggleable(),
                Tables\Columns\TextColumn::make('visa_date')
                    ->label('Visa Issue Date')->date()->toggleable(),
                Tables\Columns\TextColumn::make('expire_date')
                    ->label('Visa Expiry Date')->date()->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->toggleable()
                    ->action(
                        Action::make('Status')->form([
                            Select::make('status')
                                ->options(config('app.studentVisaStatus'))
                                ->required(),
                            Select::make('visa_done')->options(['Yes' => 'Yes', 'No' => 'No'])->required(),
                            Textarea::make('note')->nullable()

                        ])->action(function (StudentVisa $record, array $data) {
                            $record->status = $data['status'];
                            $record->visa_done = $data['visa_done'];
                            $record->note = isset($data['note']) ? $data['note'] : '';
                            $record->save();


                            Notification::make()
                                ->title('Visa status updated successfully.')
                                ->success()
                                ->send();
                        })->mountUsing(function (Forms\ComponentContainer $form, Model $record) {
                            // Load existing payments data into the form
                            $form->fill([
                                'visa_done' => $record->visa_done,
                                'status' => $record->status,
                                'note' => $record->note
                            ]);
                        })
                    ),

                Tables\Columns\TextColumn::make('studentVisas.intakeMonth.name')->toggleable(),
                Tables\Columns\TextColumn::make('studentVisas.intakeYear.year')->toggleable(),
                Tables\Columns\TextColumn::make('travel_date')->date()->toggleable(),
                Tables\Columns\TextColumn::make('ticket')->toggleable(),
                Tables\Columns\TextColumn::make('visa_detail')->toggleable(),
                Tables\Columns\TextColumn::make('visa_done')->label('Visa Done?')->toggleable(),
                Tables\Columns\TextColumn::make('createdBy.name')->toggleable(),
                Tables\Columns\TextColumn::make('updatedBy.name')->toggleable(),


            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('visaDocuments')
                    ->label('Documents')
                    ->color('primary')
                    ->icon('heroicon-o-link')
                    ->action(function ($record, $livewire, $data) {
                        $admissionDocs = new StudentVisaDocument();
                        $admissionDocs->student_visa_id = $record->id;
                        $admissionDocs->student_id = $record->student_id;
                        $admissionDocs->doc_type_id = $data['doc_type_id'];
                        $admissionDocs->doc_name = $data['doc_name'];
                        $admissionDocs->doc_org_name = $data['doc_org_name'];
                        $admissionDocs->remark = $data['remark'];
                        $admissionDocs->save();
                    })
                    ->form(function ($record) {
                        return [
                            Select::make('doc_type_id')
                                ->label('Document Type')
                                ->options(
                                    DocumentType::where(['type' => 'Optional', 'status' => 'Active'])->pluck('name', 'id')->toArray()
                                )->preload()->searchable()->required(),
                            FileUpload::make('doc_name')
                                ->label('Upload Document')
                                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                ->directory(config('app.UPLOAD_DIR') . '/studentDocs/visas')
                                ->downloadable()
                                ->openable()
                                ->previewable(false)
                                ->storeFileNamesIn('doc_org_name')
                                ->required(),
                            Textarea::make('remark')->nullable(),
                            View::make('filament.resources.student-resource.pages.visa-documents')->viewData(['record' => $record])
                        ];
                    })
                    ->modalWidth('xl') // Customize the modal size ('sm', 'md', 'lg', 'xl', '2xl', etc.)
            ]);
    }
}
