<?php

namespace App\Filament\Resources\Skls\Pages;

use App\Filament\Resources\Skls\SklResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSkl extends CreateRecord
{
    protected static string $resource = SklResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
