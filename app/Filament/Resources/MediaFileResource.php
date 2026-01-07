<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaFileResource\Pages;
use App\Models\MediaFile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MediaFileResource extends Resource
{
    public static function getModel(): string
    {
        return MediaFile::class;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-folder-open';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Media';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationLabel(): string
    {
        return 'File Manager';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Upload File')
                            ->columnSpan(2)
                            ->schema([
                                FileUpload::make('path')
                                    ->label('File')
                                    ->required()
                                    ->disk('public')
                                    ->directory('media-files')
                                    ->preserveFilenames()
                                    ->maxSize(51200)
                                    ->downloadable()
                                    ->openable()
                                    ->columnSpanFull(),
                                TextInput::make('name')
                                    ->label('Nama File')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('alt_text')
                                    ->label('Alt Text')
                                    ->rows(2),
                                Textarea::make('caption')
                                    ->rows(2),
                            ]),

                        Section::make('Pengaturan')
                            ->columnSpan(1)
                            ->schema([
                                Select::make('folder')
                                    ->options([
                                        'images' => 'Images',
                                        'documents' => 'Documents',
                                        'videos' => 'Videos',
                                        'downloads' => 'Downloads',
                                        'misc' => 'Miscellaneous',
                                    ])
                                    ->searchable()
                                    ->nullable()
                                    ->placeholder('Root'),
                                Toggle::make('is_public')
                                    ->label('Public')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label('')
                    ->disk('public')
                    ->square()
                    ->defaultImageUrl(fn($record) => $record->is_image ? null : asset('images/file-icon.png'))
                    ->visibility('visible'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('extension')
                    ->label('Type')
                    ->badge()
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'jpg', 'jpeg', 'png', 'gif', 'webp' => 'success',
                        'pdf' => 'danger',
                        'doc', 'docx' => 'info',
                        'xls', 'xlsx' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('formatted_size')
                    ->label('Size'),
                Tables\Columns\TextColumn::make('folder')
                    ->placeholder('Root')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('download_count')
                    ->label('Downloads')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('folder')
                    ->options([
                        'images' => 'Images',
                        'documents' => 'Documents',
                        'videos' => 'Videos',
                        'downloads' => 'Downloads',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => $record->url)
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMediaFiles::route('/'),
            'create' => Pages\CreateMediaFile::route('/create'),
            'edit' => Pages\EditMediaFile::route('/{record}/edit'),
        ];
    }
}
