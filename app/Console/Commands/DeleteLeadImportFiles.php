<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DeleteLeadImportFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-lead-import-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete files that are uploaded for import lead. And also delete error files.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Define the directory and threshold
        $directory = 'uploads/tempImport';
        $thresholdDate = Carbon::now()->subDays(2);
        // Retrieve all files from the directory recursively
        $files = Storage::disk(config('app.FILE_DISK'))->allFiles($directory);

        // Loop through each file
        collect($files)->each(function ($filePath) use ($thresholdDate) {

            // Get file timestamp
            $fileTimestamp = Storage::disk(config('app.FILE_DISK'))->lastModified($filePath);

            // Convert the timestamp to a Carbon instance
            $fileDate = Carbon::createFromTimestamp($fileTimestamp);
            // Check if the file is older than the threshold date
            if ($fileDate < $thresholdDate) {
                // Delete the file
                Storage::disk(config('app.FILE_DISK'))->delete($filePath);
            }
        });
    }
}
