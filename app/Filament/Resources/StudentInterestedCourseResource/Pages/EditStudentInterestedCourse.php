<?php

namespace App\Filament\Resources\StudentInterestedCourseResource\Pages;

use App\Filament\Resources\StudentInterestedCourseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentInterestedCourse extends EditRecord
{
    protected static string $resource = StudentInterestedCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
