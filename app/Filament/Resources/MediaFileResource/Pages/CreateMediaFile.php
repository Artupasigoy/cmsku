<?php

namespace App\Filament\Resources\MediaFileResource\Pages;

use App\Filament\Resources\MediaFileResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateMediaFile extends CreateRecord
{
    protected static string $resource = MediaFileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-fill metadata from uploaded file
        if (!empty($data['path'])) {
            $path = $data['path'];
            $disk = Storage::disk('public');

            if ($disk->exists($path)) {
                $data['original_name'] = basename($path);
                $data['extension'] = pathinfo($path, PATHINFO_EXTENSION);
                $data['size'] = $disk->size($path);
                $data['mime_type'] = $disk->mimeType($path);
                $data['uploaded_by'] = auth()->id();
            }
        }

        return $data;
    }
}
