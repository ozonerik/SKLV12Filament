<?php

namespace App\Filament\Siswa\Pages;

use App\Models\Skl;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;

class SiswaDashboard extends Dashboard
{
    public function mount(): void
    {
        $student = Filament::auth()->user();

        if (! $student) {
            return;
        }

        $skl = Skl::query()
            ->where('student_id', $student->getAuthIdentifier())
            ->first();

        // Jika belum isi kuesioner, paksa ke halaman kuesioner.
        if ($skl && (! $skl->is_questionnaire_completed)) {
            $this->redirect(IsiKuesioner::getUrl(panel: 'siswa'));

            return;
        }

        // Jika sudah isi kuesioner, arahkan ke halaman kelulusan & SKL.
        if ($skl && $skl->is_questionnaire_completed) {
            $this->redirect(KelulusanDanSkl::getUrl(panel: 'siswa'));
        }
    }
}

