<?php

namespace App\Imports;

use App\Models\Country;
use App\Models\Lead;
use App\Models\User;
use App\Models\VisaType;
use App\Rules\checkAgent;
use App\Rules\checkCountry;
use App\Rules\checkVisaType;
use Filament\Tables\Columns\Summarizers\Count;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\PersistRelations;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LeadsImport implements ToCollection, WithHeadingRow, PersistRelations, WithValidation
{
    use Importable;

    protected $columnMapping;

    private $agents;
    private $countries;
    private $visaTypes;

    public function __construct()
    {
        $this->agents = User::select('id','name','email','status')->where('status','Active')->role('Agent')->get();
        $this->countries = Country::select('id','name','status')->where('status','Active')->pluck('id','name')->toArray();
        $this->visaTypes = VisaType::select('id','name','status')->where('status',1)->pluck('id','name')->toArray();
    }


    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $agent = $this->agents->where('email',$row['agent_email'])->first();
            $lead = Lead::create([
                'full_name' => $row['full_name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'passport_no' => $row['passport_no'],
                'address' => $row['address'],
                'job_offer' => $row['job_offer'],
                'visa_type_id' => $this->visaTypes[ucfirst($row['visa_type'])] ?? NULL,
                'amount' => $row['amount'],
                'agent_id' => $agent->id ?? NULL,
                'created_by' => auth()->user()->id
            ]);
            $lead->country()->detach();
            $lead->country()->attach($this->countries[ucfirst($row['country'])]);
        }
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required'],
            'email' => ['required','email','unique:leads,email,NULL,id,deleted_at,NULL'],
            'phone' => ['required'],
            'country' => ['required',new checkCountry],
            'visa_type' => ['required',new checkVisaType],
            'agent_email' => ['required', new checkAgent],
            'job_offer' => ['required','in:Yes,No'],
            'amount'    => ['nullable','numeric']
        ];
    }

    public function prepareForValidation($data, $index)
    {
        $data['job_offer'] = ucfirst($data['job_offer']);
        $data['visa_type'] = ucfirst($data['visa_type']);

        return $data;
    }
}
