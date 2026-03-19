<?php

namespace App\Filament\Resources\Headmasters\Pages;

use App\Filament\Resources\Headmasters\HeadmasterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHeadmasters extends ListRecords
{
    protected static string $resource = HeadmasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
