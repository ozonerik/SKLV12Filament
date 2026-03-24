<?php

namespace App\Filament\Resources\Questionnaires\Pages;

use App\Filament\Resources\Questionnaires\QuestionnaireResource;
use App\Models\Questionnaire;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateQuestionnaire extends CreateRecord
{
    protected static string $resource = QuestionnaireResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (
            ($data['is_active'] ?? false)
            && filled($data['school_year_id'] ?? null)
            && filled($data['start_date'] ?? null)
            && filled($data['end_date'] ?? null)
            && Questionnaire::hasActiveOverlapForSchoolYear(
                (int) $data['school_year_id'],
                (string) $data['start_date'],
                (string) $data['end_date'],
            )
        ) {
            throw ValidationException::withMessages([
                'data.start_date' => 'Sudah ada kuesioner aktif lain pada tahun pelajaran ini dengan periode yang bertumpang tindih.',
            ]);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
