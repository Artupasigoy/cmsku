<?php

namespace App\Filament\Resources\LayananTIKResource\Pages;

use App\Filament\Resources\LayananTIKResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLayananTIKs extends ManageRecords
{
    protected static string $resource = LayananTIKResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
