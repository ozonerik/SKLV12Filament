<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Services\LulusanExcelImportService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Throwable;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    public function mount(): void
    {
        parent::mount();

        $feedback = session('lulusan_import_feedback');
        if (! is_array($feedback) || empty($feedback['message'])) {
            return;
        }

        $notification = Notification::make()
            ->title('Import Lulusan')
            ->body((string) $feedback['message']);

        match ((string) ($feedback['type'] ?? 'danger')) {
            'success' => $notification->success(),
            'warning' => $notification->warning(),
            default => $notification->danger(),
        };

        $notification->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importLulusanExcel')
                ->label('Import Excel Lulusan')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->schema([
                    Placeholder::make('template_download')
                        ->content(
                            new HtmlString(
                                '<p class="text-sm text-gray-700">Gunakan format template: <a href="/template_xls/template_lulusan_new.xlsx" download class="font-semibold text-blue-600 hover:text-blue-800 underline">Download Template</a></p>'
                            )
                        ),
                    FileUpload::make('excel_file')
                        ->label('File Excel')
                        ->disk('local')
                        ->directory('imports/lulusan')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $storedPath = $data['excel_file'] ?? null;

                    if (! $storedPath) {
                        Notification::make()
                            ->title('File upload tidak ditemukan.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $absolutePath = Storage::disk('local')->path($storedPath);

                    try {
                        $result = app(LulusanExcelImportService::class)->preview($absolutePath);

                        $token = (string) Str::uuid();
                        Cache::put("lulusan_import_preview:{$token}", [
                            'stored_path' => $storedPath,
                            'return_url' => request()->header('referer') ?: request()->fullUrl(),
                            'preview_result' => $result,
                        ], now()->addMinutes(30));

                        $previewUrl = URL::temporarySignedRoute(
                            'lulusan-import.preview',
                            now()->addMinutes(30),
                            ['token' => $token]
                        );

                        return redirect($previewUrl);
                    } catch (Throwable $exception) {
                        report($exception);

                        Storage::disk('local')->delete($storedPath);

                        Notification::make()
                            ->title('Preview gagal')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            CreateAction::make(),
        ];
    }
}
