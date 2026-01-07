<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Models\Slider;
use Filament\Forms\Components\DateTimePicker;
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

class SliderResource extends Resource
{
    public static function getModel(): string
    {
        return Slider::class;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-photo';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Sistem';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getNavigationLabel(): string
    {
        return 'Slider/Banner';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Konten')
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('subtitle')
                                    ->rows(2),
                                FileUpload::make('image')
                                    ->label('Gambar')
                                    ->image()
                                    ->required()
                                    ->directory('sliders')
                                    ->imageEditor(),
                                TextInput::make('button_text')
                                    ->label('Teks Tombol')
                                    ->maxLength(100),
                                TextInput::make('button_url')
                                    ->label('URL Tombol')
                                    ->url(),
                            ]),

                        Section::make('Pengaturan')
                            ->columnSpan(1)
                            ->schema([
                                Select::make('position')
                                    ->label('Posisi')
                                    ->options([
                                        'home' => 'Home',
                                        'news' => 'Berita',
                                        'services' => 'Layanan',
                                    ])
                                    ->default('home'),
                                TextInput::make('order')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->default(0),
                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                                DateTimePicker::make('start_date')
                                    ->label('Mulai Tampil'),
                                DateTimePicker::make('end_date')
                                    ->label('Selesai Tampil'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Banner'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('position')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
            ])
            ->defaultSort('order')
            ->reorderable('order')
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
            'index' => Pages\ManageSliders::route('/'),
        ];
    }
}
