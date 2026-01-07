<?php

namespace App\Filament\Resources\HalamanStatisResource\Pages;

use App\Filament\Resources\HalamanStatisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHalamanStatis extends ListRecords
{
    protected static string $resource = HalamanStatisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
