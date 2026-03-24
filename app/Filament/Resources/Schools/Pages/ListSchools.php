<?php

namespace App\Filament\Resources\Schools\Pages;

use App\Filament\Resources\Schools\SchoolResource;
use Filament\Resources\Pages\ListRecords;

class ListSchools extends ListRecords
{
    protected static string $resource = SchoolResource::class;

    public function getTableColumnsSessionKey(): string
    {
        return parent::getTableColumnsSessionKey() . '_v2';
    }

    public function getHasReorderedTableColumnsSessionKey(): string
    {
        return parent::getHasReorderedTableColumnsSessionKey() . '_v2';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
