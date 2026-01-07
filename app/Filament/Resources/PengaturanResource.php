<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengaturanResource\Pages;
use App\Models\Pengaturan;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PengaturanResource extends Resource
{
    public static function getModel(): string
    {
        return Pengaturan::class;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Sistem';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationLabel(): string
    {
        return 'Pengaturan';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Setting')
                    ->schema([
                        TextInput::make('key')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Contoh: site_name, contact_email'),
                        TextInput::make('label')
                            ->required()
                            ->maxLength(255),
                        Select::make('group')
                            ->options([
                                'general' => 'Umum',
                                'contact' => 'Kontak',
                                'social' => 'Media Sosial',
                                'seo' => 'SEO',
                                'appearance' => 'Tampilan',
                            ])
                            ->default('general'),
                        Select::make('type')
                            ->options([
                                'text' => 'Text',
                                'textarea' => 'Textarea',
                                'number' => 'Number',
                                'email' => 'Email',
                                'url' => 'URL',
                                'file' => 'File',
                                'boolean' => 'Boolean',
                            ])
                            ->default('text'),
                        Textarea::make('value')
                            ->label('Nilai')
                            ->rows(3),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(2),
                        TextInput::make('order')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_public')
                            ->label('Dapat diakses publik'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->searchable(),
                Tables\Columns\TextColumn::make('group')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Publik')
                    ->boolean(),
            ])
            ->defaultSort('group')
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'general' => 'Umum',
                        'contact' => 'Kontak',
                        'social' => 'Media Sosial',
                        'seo' => 'SEO',
                        'appearance' => 'Tampilan',
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
            'index' => Pages\ManagePengaturans::route('/'),
        ];
    }
}
