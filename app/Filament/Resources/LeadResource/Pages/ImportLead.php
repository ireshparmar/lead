<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Exports\LeadErrorExport;
use App\Filament\Resources\LeadResource;
use App\Imports\LeadsImport;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\Concerns\Interaction;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

use function PHPUnit\Framework\throwException;

class ImportLead extends Page implements HasForms
{

    use InteractsWithForms, WithFileUploads;

    public ?array $data = [];

    protected static string $resource = LeadResource::class;

    protected static string $view = 'filament.resources.lead-resource.pages.import-lead';

    protected static bool $shouldRegisterNavigation = false;




    public function form(Form $form) : Form {
        //Run command to delete old import files
        Artisan::call('app:delete-lead-import-files');

        if (Request::isMethod('post'))
        {
            session()->forget(['file']);

        }
        return  $form->schema([
            FileUpload::make('file')
                    ->label('Choose Excel File')
                    ->directory(config('app.UPLOAD_DIR').'/tempImport')
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                    ->validationMessages([
                        'mimetypes' => 'Invalid file type. Please upload .xls or .xlsx files',
                    ])
                    ->required()

        ])->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('Import'))
                ->extraAttributes(['class' => 'mr-4'])
                ->submit('save'),
        ];
    }

    protected function downloadErrorFileAction():array{
        return [
            Action::make('save')
                ->label(__('Download Error'))
                ->extraAttributes(['class' => 'p-2'])
                ->url(Storage::url(session('file')))
                ->color('danger'),
        ];
    }

    protected function downloadSampleFile():array{
        $file = '/sample/Sample-Lead-Import.xlsx';
        if(auth()->user()->hasRole('Agent')){
            $file = '/sample/Sample-Lead-Import-Agent.xlsx';
        }
        return [
            Action::make('save')
                ->label(__('Sample Excel'))
                ->extraAttributes(['class' => 'p-2'])
                ->url($file),
        ];
    }

    public function save()
    {


            $data = $this->form->getState();

            $file = storage_path('app/public/'. $data['file']);
            //$import = Excel::import(new LeadsImport(), $file);

            $import = new LeadsImport();
           // $import->import($file);
            //sdd($import);

            try {
                $import->import($file);
                Notification::make()
                            ->title('Lead data imported successfully.')
                            ->success()
                            ->send();
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e){
                $failures = $e->failures();

                $attributes = ['full_name','email', 'phone', 'passport_no', 'job_offer', 'visa_type', 'amount', 'agent_email', 'created_date']; // Adjust these as needed
                $columnNames = [
                    'full_name' => 'Name',
                    'email' => 'Email',
                    'phone' => 'Phone',
                    'passport_no' => 'Passport No',
                    'job_offer' => 'Job Offer',
                    'visa_type' => 'Visa Type',
                    'amount' => 'Amount',
                    'agent_email' => 'Agent Email',
                    'created_date' => 'Created Date'
                ];
                if(auth()->user()->hasRole('Agent')){
                    unset($attributes[6],$attributes[7]);
                    unset($columnNames['amount'],$columnNames['agent_email']);
                }

                // Create the validation errors export
                $path = config('app.UPLOAD_DIR').'/tempImport/error/lead-import-validation-errors-' . time() . '.xlsx';


                session(['file' => $path]);
                Excel::store(new LeadErrorExport($failures, $attributes, $columnNames), $path);

                Notification::make()
                            ->title('File you are importing is invalid. Please check error file.')
                            ->danger()
                            ->send();

                return redirect(url(ImportLead::getUrl()));
            }catch (Halt $exception) {
                return;
            }

    }

}
