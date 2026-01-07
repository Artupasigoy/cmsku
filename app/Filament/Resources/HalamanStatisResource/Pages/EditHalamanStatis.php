<?php

namespace App\Filament\Resources\HalamanStatisResource\Pages;

use App\Filament\Resources\HalamanStatisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHalamanStatis extends EditRecord
{
    protected static string $resource = HalamanStatisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
