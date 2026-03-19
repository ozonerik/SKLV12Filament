<?php

namespace App\Filament\Resources\Skls\Pages;

use App\Filament\Resources\Skls\SklResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSkl extends EditRecord
{
    protected static string $resource = SklResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
