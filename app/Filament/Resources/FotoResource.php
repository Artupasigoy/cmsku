<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FotoResource\Pages;
use App\Models\Foto;
use Filament\Forms\Components\DatePicker;
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

class FotoResource extends Resource
{
    public static function getModel(): string
    {
        return Foto::class;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-camera';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Media';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationLabel(): string
    {
        return 'Galeri Foto';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Foto')
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
                                FileUpload::make('image')
                                    ->label('Gambar')
                                    ->image()
                                    ->required()
                                    ->directory('galeri-foto')
                                    ->imageEditor()
                                    ->columnSpanFull(),
                                TextInput::make('alt_text')
                                    ->label('Alt Text')
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->rows(2),
                            ]),

                        Section::make('Metadata')
                            ->columnSpan(1)
                            ->schema([
                                Select::make('kategori_id')
                                    ->relationship('kategori', 'name', fn(Builder $query) => $query->where('type', 'galeri'))
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('album')
                                    ->maxLength(255),
                                DatePicker::make('tanggal_foto')
                                    ->label('Tanggal Foto'),
                                TextInput::make('lokasi')
                                    ->maxLength(255),
                                TextInput::make('fotografer')
                                    ->maxLength(255),
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
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->square(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('album')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_foto')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('order')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('album')
                    ->options(fn() => Foto::distinct()->pluck('album', 'album')->filter()->toArray()),
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
            'index' => Pages\ListFotos::route('/'),
            'create' => Pages\CreateFoto::route('/create'),
            'edit' => Pages\EditFoto::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
