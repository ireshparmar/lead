<?php

namespace App\Filament\Resources\StudentCommissionResource\Pages;

use App\Filament\Resources\StudentCommissionResource;
use App\Helpers\CurrencyHelper;
use App\Models\StudentAdmission;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentCommission extends CreateRecord
{
    protected static string $resource = StudentCommissionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $studentAdmission = StudentAdmission::find($data['admission_id']);
        $data['base_currency_rate'] = CurrencyHelper::findBaseCurrencyRate($studentAdmission->fees_currency);
        return $data;
    }
}
