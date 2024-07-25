<?php

namespace App\Imports;

use App\Models\Country;
use App\Models\Lead;
use App\Models\User;
use App\Models\VisaType;
use App\Rules\checkAgent;
use App\Rules\checkCountry;
use App\Rules\checkVisaType;
use App\Rules\uniqueEmail;
use Filament\Tables\Columns\Summarizers\Count;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\PersistRelations;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Maatwebsite\Excel\Validators\Failure;

class LeadsImport implements ToCollection, WithHeadingRow, PersistRelations, WithValidation
{
    use Importable;

    protected $columnMapping;

    private $agents;
    private $countries;
    private $visaTypes;
    private $emails = [];
    private $duplicateEmails = [];

    public function __construct()
    {
        $this->agents = User::select('id','name','email','status')->where('status','Active')->role('Agent')->get();
        $this->countries = Country::select('id','name','status')->where('status','Active')->pluck('id','name')->toArray();
        $this->visaTypes = VisaType::select('id','name','status')->where('status',1)->pluck('id','name')->toArray();
    }


    public function collection(Collection $rows)
    {

        $this->emails = [];
        $allRows = $rows->toArray();
          // Collect and identify duplicate emails
          foreach ($allRows as $index => $row) {
            $email = $row['email'];
            if (isset($this->emails[$email])) {
                $this->emails[$email][] = $index; // Save row index
            } else {
                $this->emails[$email] = [$index];
            }
        }

        // Filter duplicates
        $duplicateEmails = array_filter($this->emails, function($indexes) {
            return count($indexes) > 1;
        });

          // Throw validation exception if there are duplicate emails
          if (!empty($duplicateEmails)) {
            $messages = [];
            foreach ($duplicateEmails as $email => $indexes) {
                foreach ($indexes as $index) {
                    $rowData = $allRows[$index];
                    $rowDataString = implode(', ', array_map(fn($key, $value) => "$key: $value", array_keys($rowData), $rowData));
                    $messages[] = "The email '{$email}' is duplicated in row " . ($index + 2) . " with data: $rowDataString.";
                    $failures[] = new Failure(
                        $index + 2, // Adjust for heading row
                        'email',
                        ["The email '{$email}' is duplicated."],
                        $rowData
                    );
                }
            }
           // Throw ExcelValidationException with the LaravelValidationException and messages
            throw new ExcelValidationException(
                \Illuminate\Validation\ValidationException::withMessages($messages),
                $failures
            );
        }



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
                'created_by' => auth()->user()->id,
                'is_imported' => 1
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
            'agent_email' => ['nullable', new checkAgent],
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
