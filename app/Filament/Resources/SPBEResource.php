<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SPBEResource\Pages;
use App\Models\SPBE;
use Filament\Forms\Components\FileUpload;
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

class SPBEResource extends Resource
{
    public static function getModel(): string
    {
        return SPBE::class;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-server-stack';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Layanan Publik';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getNavigationLabel(): string
    {
        return 'SPBE';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Aplikasi')
                    ->schema([
                        TextInput::make('nama_aplikasi')
                            ->label('Nama Aplikasi')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Textarea::make('deskripsi')
                            ->rows(3),
                        FileUpload::make('thumbnail')
                            ->image()
                            ->directory('spbe'),
                        TextInput::make('url')
                            ->url()
                            ->label('URL Aplikasi'),
                        TextInput::make('domain'),
                        Select::make('kategori')
                            ->options([
                                'pelayanan' => 'Pelayanan',
                                'administrasi' => 'Administrasi',
                                'internal' => 'Internal',
                                'infrastruktur' => 'Infrastruktur',
                            ]),
                        TextInput::make('opd_pengelola')
                            ->label('OPD Pengelola'),
                        TextInput::make('tahun_operasional')
                            ->label('Tahun Operasional')
                            ->numeric(),
                        Select::make('status')
                            ->options([
                                'aktif' => 'Aktif',
                                'maintenance' => 'Maintenance',
                                'discontinued' => 'Discontinued',
                            ])
                            ->default('aktif'),
                        TextInput::make('order')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_featured')
                            ->label('Featured'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail'),
                Tables\Columns\TextColumn::make('nama_aplikasi')
                    ->label('Aplikasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'aktif' => 'success',
                        'maintenance' => 'warning',
                        'discontinued' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('opd_pengelola')
                    ->label('OPD')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
            ])
            ->defaultSort('order')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'maintenance' => 'Maintenance',
                        'discontinued' => 'Discontinued',
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
            'index' => Pages\ManageSPBEs::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
