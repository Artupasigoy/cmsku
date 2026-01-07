<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DokumenResource\Pages;
use App\Models\Dokumen;
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

class DokumenResource extends Resource
{
    public static function getModel(): string
    {
        return Dokumen::class;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-arrow-down';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Konten';
    }

    public static function getNavigationSort(): ?int
    {
        return 6;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Informasi Dokumen')
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Dokumen')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->rows(3),
                                FileUpload::make('file_path')
                                    ->label('Upload File')
                                    ->required()
                                    ->directory('dokumen')
                                    ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                                    ->maxSize(10240)
                                    ->downloadable()
                                    ->openable()
                                    ->previewable(false),
                            ]),

                        Section::make('Kategorisasi')
                            ->columnSpan(1)
                            ->schema([
                                Select::make('kategori_id')
                                    ->relationship('kategori', 'name', fn(Builder $query) => $query->where('type', 'dokumen'))
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')->required()->maxLength(255),
                                        TextInput::make('slug')->required()->maxLength(255),
                                    ]),
                                TextInput::make('tahun')
                                    ->label('Tahun')
                                    ->maxLength(4)
                                    ->placeholder(date('Y')),
                                TextInput::make('file_name')
                                    ->label('Nama File')
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('file_type')
                                    ->label('Tipe File')
                                    ->disabled()
                                    ->dehydrated(),
                                Toggle::make('is_public')
                                    ->label('Dokumen Publik')
                                    ->default(true)
                                    ->helperText('Jika dimatikan, hanya admin yang dapat melihat'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('kategori.name')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_type')
                    ->label('Tipe')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('download_count')
                    ->label('Downloads')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Publik')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('kategori')
                    ->relationship('kategori', 'name'),
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Status Publik'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDokumens::route('/'),
            'create' => Pages\CreateDokumen::route('/create'),
            'edit' => Pages\EditDokumen::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
