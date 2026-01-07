<?php

namespace App\Filament\Resources\SPBEResource\Pages;

use App\Filament\Resources\SPBEResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSPBEs extends ManageRecords
{
    protected static string $resource = SPBEResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
