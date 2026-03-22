<?php

namespace App\Filament\Resources\Questionnaires\Pages;

use App\Filament\Resources\Questionnaires\QuestionnaireResource;
use App\Models\Questionnaire;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditQuestionnaire extends EditRecord
{
    protected static string $resource = QuestionnaireResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
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
                $this->record->getKey(),
            )
        ) {
            throw ValidationException::withMessages([
                'data.start_date' => 'Sudah ada kuesioner aktif lain pada tahun pelajaran ini dengan periode yang bertumpang tindih.',
            ]);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
