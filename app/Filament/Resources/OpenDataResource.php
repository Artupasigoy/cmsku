<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpenDataResource\Pages;
use App\Models\OpenData;
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

class OpenDataResource extends Resource
{
    public static function getModel(): string
    {
        return OpenData::class;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-circle-stack';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Konten';
    }

    public static function getNavigationSort(): ?int
    {
        return 9;
    }

    public static function getNavigationLabel(): string
    {
        return 'Open Data';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Informasi Dataset')
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Dataset')
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
                                FileUpload::make('file_path')
                                    ->label('File Dataset')
                                    ->required()
                                    ->directory('open-data')
                                    ->acceptedFileTypes(['text/csv', 'application/json', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                                    ->maxSize(51200)
                                    ->downloadable(),
                            ]),

                        Section::make('Metadata')
                            ->columnSpan(1)
                            ->schema([
                                TextInput::make('file_name')
                                    ->label('Nama File')
                                    ->disabled()
                                    ->dehydrated(),
                                Select::make('format')
                                    ->options([
                                        'csv' => 'CSV',
                                        'json' => 'JSON',
                                        'xlsx' => 'Excel (XLSX)',
                                        'xls' => 'Excel (XLS)',
                                    ]),
                                Select::make('license')
                                    ->label('Lisensi')
                                    ->options([
                                        'CC-BY-4.0' => 'CC BY 4.0',
                                        'CC-BY-SA-4.0' => 'CC BY-SA 4.0',
                                        'CC0' => 'Public Domain (CC0)',
                                        'ODC-BY' => 'ODC Attribution',
                                    ])
                                    ->default('CC-BY-4.0'),
                                TextInput::make('source')
                                    ->label('Sumber'),
                                TextInput::make('tahun')
                                    ->label('Tahun')
                                    ->placeholder(date('Y')),
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
                Tables\Columns\TextColumn::make('title')
                    ->label('Dataset')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('format')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('license')
                    ->label('Lisensi')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tahun')
                    ->sortable(),
                Tables\Columns\TextColumn::make('download_count')
                    ->label('Downloads')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('format')
                    ->options([
                        'csv' => 'CSV',
                        'json' => 'JSON',
                        'xlsx' => 'Excel',
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
            'index' => Pages\ManageOpenData::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
