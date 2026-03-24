<?php

namespace App\Filament\Resources\Grades\Pages;

use App\Filament\Resources\Grades\GradeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGrade extends CreateRecord
{
    protected static string $resource = GradeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Hapus school_year_id karena tidak ada di tabel grades
        unset($data['school_year_id']);
        
        return $data;
    }
}
