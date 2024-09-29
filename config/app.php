<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    'leadStatus' => [
        'New' => 'New',
        'Assigned' => 'Assigned',
        'Completed' => 'Completed',
        'Canceled' => 'Canceled',
        'Hold'     => 'Hold',
        'Refund'   => 'Refund',
    ],
    'leadDocType' => [
        'adharcard' => 'Adhar Card',
        'passport' => 'Passport',
        'drive_licence' => 'Driving Licence',
        'pcc' => 'Pcc',
        'resume' => 'Resume',
        '10th_mark_sheet' => '10th Mark Sheet',
        '12th_mark_sheet' => '12th Mark Sheet',
        '12th_credit_ceritificate' => '12th Credit Certificate',
        'bachelor_mark_sheet' => "Bachelor's Mark Sheet + Degree Certificate",
        'master_mark_sheet' => "Master's Mark Sheet + Degree Certificate",
        'backlog_ceritificate' => "Backlog Certificate/No Backlog Ceritificate",
        'transcript' => 'Transcript',
        'letter_recomd' => 'Letter of Recommendation',
        'med_instr' => 'Medium of Instruction',
        'Ielts_sc_card' => 'IELTS Score Card',
        'app_letter' => 'Appoinment Letter',
        'salary_slip_3month' => 'Salary Slip (Last 3 Month)',
        'salary_acc_state' => 'Salary Account Statement',
        'other'  => 'Other'


    ],

    'FILE_DISK' => env('FILESYSTEM_DISK'),
    'UPLOAD_DIR' => 'uploads',
    'paymentFilter' => [
        '1' => 'First Payment',
        '2' => 'Second Payment',
        '3' => 'Third Payment'
    ],
    'gender' => [
        'Male' => 'Male',
        'Female' => 'Female',
        'Transgender'  => 'Transgender'
    ],
    'educationStatus' => [
        'Completed' => 'Completed',
        'In Process' => 'In Process'
    ],
    'verifiedStatus' => [
        'Verified' => 'Verified',
        'Unverified' => 'Unverified',
        'Reupload' => 'Reupload',

    ],
    'enterancExamType' => [
        'Language' => 'Language',
        'Aptitude' => 'Aptitude'
    ],
    'docTypeType' => [
        'Compulsory' => 'Compulsory',
        'Optional' => 'Optional',
    ],
    'docTypeModule' => [
        'General' => 'General',
        'Student' => 'Student',
    ],
    'status'  => [
        'Active' => 'Active',
        'Inactive' => 'Inactive',
    ],

    'interestedCourseStatus' => [
        'Course Selected' => 'Course Selected',
        'Documents Upload' => 'Document Upload',
        'Moved To Application' => 'Moved To Application',
        'Waiting For IELTS?PTE' => 'Waiting For IELTS?PTE',
    ],

    'collegeAppStatus' => [
        'New' => 'New',
        'Application Prepared' => 'Application Prepared',
        'Application Email To College' => 'Application Email To College',
        'Application In Followup With College' => 'Application In Followup With College',
        'Application Confirmation For Admission' => 'Application Confirmation For Admission',
        'Application Recieved Now Uplodad I20 In Document Management' => 'Application Recieved Now Uplodad I20 In Document Management',
        'Offer Accepted' => 'Offer Accepted',
        'Given For Admission Process' => 'Given For Admission Process',
        'Intake Of This University Is Closed' => 'Intake Of This University Is Closed'
    ],

    'studAdmissionStatus' => [
        'New' => 'New',
        'Admission Form Prepared' => 'Admission Form Prepared',
        'Admission Form Send To College' => 'Admission Form Send To College',
        'Admission Fee Paid' => 'Admission Fee Paid',
        'Given For Visa Process' => 'Given For Visa Process',
        'GT Part 1' => 'GT Part 1',
        'GT Part 2' => 'GT Part 2',
        'Application Fees' => 'Application Fees',
        'GIS Payment Done' => 'GIS Payment Done',
        'Tution Fee Paid' => 'Tution Fee Paid',
        'PLA Letter Recieved' => 'PLA Letter Recieved',
    ],

    'studentVisaStatus' => [
        'New' => 'New',
        'Student Visa Filed' => 'Student Visa Filed',
        'Document Received' => 'Document Received',
        'Appoinment Confirm' => 'Appoinment Confirm',
        'Visa Approval' => 'Visa Approval',
        'Visa Rejected - Reporcess' => 'Visa Rejected - Reporcess',
    ],

    'packageType' => [
        'Immigration' => 'Immigration',
        'Student' => 'Student',
    ]


];
