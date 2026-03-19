<?php

namespace App\Filament\Resources\Headmasters\Pages;

use App\Filament\Resources\Headmasters\HeadmasterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHeadmaster extends EditRecord
{
    protected static string $resource = HeadmasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
