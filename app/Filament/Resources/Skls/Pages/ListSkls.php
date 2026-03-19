<?php

namespace App\Filament\Resources\Skls\Pages;

use App\Filament\Resources\Skls\SklResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSkls extends ListRecords
{
    protected static string $resource = SklResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
