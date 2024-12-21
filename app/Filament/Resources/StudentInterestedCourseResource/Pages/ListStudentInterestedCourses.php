<?php

namespace App\Filament\Resources\StudentInterestedCourseResource\Pages;

use App\Filament\Resources\StudentInterestedCourseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentInterestedCourses extends ListRecords
{
    protected static string $resource = StudentInterestedCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
