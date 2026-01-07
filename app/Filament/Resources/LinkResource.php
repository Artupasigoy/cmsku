<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LinkResource\Pages;
use App\Models\Link;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class LinkResource extends Resource
{
    public static function getModel(): string
    {
        return Link::class;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-link';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Sistem';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationLabel(): string
    {
        return 'Menu & Link';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Link')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('url')
                            ->label('URL')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('/halaman atau https://example.com'),
                        TextInput::make('icon')
                            ->label('Icon (Heroicons)')
                            ->placeholder('heroicon-o-home'),
                        Select::make('group')
                            ->options([
                                'header' => 'Header',
                                'footer' => 'Footer',
                                'sidebar' => 'Sidebar',
                                'quick_links' => 'Quick Links',
                                'social' => 'Social Media',
                            ])
                            ->default('footer'),
                        Select::make('parent_id')
                            ->label('Parent')
                            ->relationship('parent', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        TextInput::make('order')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_external')
                            ->label('Link External'),
                        Toggle::make('open_new_tab')
                            ->label('Buka di Tab Baru'),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->limit(30),
                Tables\Columns\TextColumn::make('group')
                    ->badge(),
                Tables\Columns\TextColumn::make('parent.title')
                    ->label('Parent')
                    ->placeholder('-'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'header' => 'Header',
                        'footer' => 'Footer',
                        'sidebar' => 'Sidebar',
                        'quick_links' => 'Quick Links',
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
            'index' => Pages\ManageLinks::route('/'),
        ];
    }
}
