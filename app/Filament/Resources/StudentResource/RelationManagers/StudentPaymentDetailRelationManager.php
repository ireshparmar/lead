<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\Package;
use App\Models\StudentFee;
use App\Models\StudentPaymentDetail;
use Closure;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Livewire\Component as Livewire;


class StudentPaymentDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'studentPayments';

    protected static ?string $label = '';



    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_fee_id')
                    ->label('Package Name')
                    ->relationship('studentFees')
                    ->options(function () {
                        $studentFees = StudentFee::with('package')->get();
                        return $studentFees->pluck('package.package_name', 'id')->toArray();
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $fee = StudentFee::find($state);
                        if ($fee) {
                            // Set total amount
                            $set('total_amount', $fee->total_amount);
                            // Calculate pending amount based on total payments
                            $totalPayments = $fee->payments()->sum('payment_amount');
                            $set('pending_amount', $fee->total_amount - $totalPayments);
                        } else {
                            // Reset values if no fee is selected
                            $set('total_amount', 0);
                            $set('pending_amount', 0);
                        }
                    }),
                Forms\Components\TextInput::make('total_amount')
                    ->label('Total Amount')
                    ->disabled(),
                Forms\Components\TextInput::make('pending_amount')
                    ->label('Pending Amount')
                    ->disabled(),
                Forms\Components\TextInput::make('payment_amount')
                    ->label('Payment Amount')
                    ->numeric()
                    ->required()
                    ->rules([
                        'numeric',
                        'min:0',
                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {

                            $studentFeeId = $get('student_fee_id');
                            if ($studentFeeId) {
                                // Fetch the StudentFees record
                                $studentFee = StudentFee::where('id', $studentFeeId)->first();
                                if ($studentFee) {
                                    $totalAmount = $studentFee->total_amount;
                                    $paidAmount = $studentFee->payments()->sum('payment_amount');
                                    $maxAllowed = $totalAmount - $paidAmount;

                                    // Debugging information
                                    // dd($totalAmount, $paidAmount, $maxAllowed, $value);

                                    // Allow payment if no existing payments or if the payment amount is within the allowed limit
                                    if ($paidAmount > 0 && $value > $maxAllowed) {
                                        $fail("Payment amount cannot exceed the pending amount of {$maxAllowed}.");
                                    }
                                } else {
                                    $fail("Student fee record not found.");
                                }
                            } else {
                                $fail("Student fee ID is required.");
                            }
                        }
                    ]),
                Forms\Components\DatePicker::make('payment_date')
                    ->label('Payment Date')
                    ->required(),
                Forms\Components\TextInput::make('payment_mode')
                    ->label('Payment Mode')
                    ->required(),
                Forms\Components\Textarea::make('remark')
                    ->label('Remark'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('studentPayments')
            ->columns([
                TextColumn::make('studentFees.package.package_name')
                    ->label('Package Name')->sortable()->toggleable(),
                TextColumn::make('payment_amount')->label('Recieved Amount')->sortable()->toggleable(),
                TextColumn::make('payment_date')->date()->sortable()->toggleable(),
                TextColumn::make('payment_mode')->label('Payment Mode')->toggleable(),
                TextColumn::make('remark')->label('Remark')->toggleable(),
                TextColumn::make('createdBy.name')->sortable()->toggleable(),
                TextColumn::make('updatedBy.name')->sortable()->toggleable(),
                TextColumn::make('created_at')->date()->sortable()->toggleable(),
                TextColumn::make('updated_at')->date()->sortable()->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->after(function (Livewire $livewire) {
                    // Runs after the form fields are saved to the database.
                    $livewire->dispatch('refreshStudentFeesRelationManager');
                })
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->after(function (Livewire $livewire) {
                    // Runs after the form fields are saved to the database.
                    $livewire->dispatch('refreshStudentFeesRelationManager');
                }),
            ]);
    }
}
