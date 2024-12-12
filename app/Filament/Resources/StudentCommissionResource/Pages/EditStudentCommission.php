<?php

namespace App\Filament\Resources\StudentCommissionResource\Pages;

use App\Filament\Resources\StudentCommissionResource;
use App\Helpers\CurrencyHelper;
use App\Models\CommissionSemester;
use App\Models\StudentAdmission;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\Request;

class EditStudentCommission extends EditRecord
{
    protected static string $resource = StudentCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();
        $studentAdmission = StudentAdmission::find($data['admission_id']);
        $data['base_currency_rate'] = CurrencyHelper::findBaseCurrencyRate($studentAdmission->fees_currency);
        if ($data['commission_type'] == 'semester-wise') {
            $data['own_commission'] = Null;
            $data['agent_commission'] = Null;
        } else {
            $requestData = request()->all();
            $requestData = isset($requestData['components'][0]['snapshot']) ? json_decode($requestData['components'][0]['snapshot'], true) : [];
            if (isset($requestData['data']['data'][0]['id']) && !empty($requestData['data']['data'][0]['id'])) {
                CommissionSemester::where('commission_id', $requestData['data']['data'][0]['id'])->delete();
            }
        }

        return $data;
    }
}
