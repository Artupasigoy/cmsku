<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use App\Models\Video;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class VideoResource extends Resource
{
    public static function getModel(): string
    {
        return Video::class;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-video-camera';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Media';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getNavigationLabel(): string
    {
        return 'Video';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Video')
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->rows(3),
                                Select::make('source')
                                    ->label('Sumber Video')
                                    ->options([
                                        'youtube' => 'YouTube',
                                        'vimeo' => 'Vimeo',
                                        'upload' => 'Upload',
                                        'other' => 'Lainnya',
                                    ])
                                    ->default('youtube')
                                    ->reactive(),
                                TextInput::make('video_url')
                                    ->label('URL Video')
                                    ->url()
                                    ->placeholder('https://www.youtube.com/watch?v=...')
                                    ->hidden(fn($get) => $get('source') === 'upload'),
                                TextInput::make('video_id')
                                    ->label('Video ID')
                                    ->helperText('Opsional, jika URL sudah diisi akan diekstrak otomatis'),
                                Textarea::make('embed_code')
                                    ->label('Embed Code')
                                    ->rows(3)
                                    ->helperText('Opsional, iframe embed code'),
                            ]),

                        Section::make('Pengaturan')
                            ->columnSpan(1)
                            ->schema([
                                FileUpload::make('thumbnail')
                                    ->image()
                                    ->directory('video-thumbnails'),
                                Select::make('kategori_id')
                                    ->relationship('kategori', 'name', fn(Builder $query) => $query->where('type', 'galeri'))
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('duration')
                                    ->label('Durasi')
                                    ->placeholder('00:05:30'),
                                TextInput::make('order')
                                    ->numeric()
                                    ->default(0),
                                Toggle::make('is_featured')
                                    ->label('Featured'),
                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Cover'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(35),
                Tables\Columns\TextColumn::make('source')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'youtube' => 'danger',
                        'vimeo' => 'info',
                        'upload' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Durasi'),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('order')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('source')
                    ->options([
                        'youtube' => 'YouTube',
                        'vimeo' => 'Vimeo',
                        'upload' => 'Upload',
                    ]),
            ])
            ->actions([
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
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
