<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermohonanInformasiResource\Pages;
use App\Models\PermohonanInformasi;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PermohonanInformasiResource extends Resource
{
    public static function getModel(): string
    {
        return PermohonanInformasi::class;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-inbox-arrow-down';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Layanan Publik';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationLabel(): string
    {
        return 'PPID';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Data Pemohon')
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('nomor_registrasi')
                                    ->label('Nomor Registrasi')
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('nama_pemohon')
                                    ->label('Nama Pemohon')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('nik')
                                    ->label('NIK')
                                    ->maxLength(16),
                                TextInput::make('email')
                                    ->email()
                                    ->required(),
                                TextInput::make('telepon')
                                    ->tel(),
                                Textarea::make('alamat')
                                    ->rows(2),
                                TextInput::make('pekerjaan'),
                            ])->columns(2),

                        Section::make('Status')
                            ->columnSpan(1)
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'diterima' => 'Diterima',
                                        'diproses' => 'Diproses',
                                        'ditolak' => 'Ditolak',
                                        'selesai' => 'Selesai',
                                    ])
                                    ->default('diterima')
                                    ->required(),
                                DateTimePicker::make('tanggal_permohonan')
                                    ->label('Tanggal Permohonan'),
                                DateTimePicker::make('tanggal_respon')
                                    ->label('Tanggal Respon'),
                                Select::make('handled_by')
                                    ->label('Ditangani Oleh')
                                    ->relationship('handler', 'name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ]),

                Section::make('Permohonan Informasi')
                    ->schema([
                        Textarea::make('rincian_informasi')
                            ->label('Rincian Informasi yang Diminta')
                            ->required()
                            ->rows(4),
                        Textarea::make('tujuan_penggunaan')
                            ->label('Tujuan Penggunaan Informasi')
                            ->rows(2),
                        Select::make('cara_memperoleh')
                            ->label('Cara Memperoleh Informasi')
                            ->options([
                                'email' => 'Email',
                                'fax' => 'Fax',
                                'langsung' => 'Datang Langsung',
                            ])
                            ->default('email'),
                        Select::make('cara_mendapat_salinan')
                            ->label('Cara Mendapat Salinan')
                            ->options([
                                'softcopy' => 'Softcopy',
                                'hardcopy' => 'Hardcopy',
                            ])
                            ->default('softcopy'),
                    ])->columns(2),

                Section::make('Catatan Admin')
                    ->schema([
                        Textarea::make('catatan_admin')
                            ->rows(3),
                        FileUpload::make('file_dokumen')
                            ->label('File Dokumen Respon')
                            ->directory('ppid-dokumen')
                            ->downloadable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_registrasi')
                    ->label('No. Registrasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_pemohon')
                    ->label('Pemohon')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'diterima' => 'info',
                        'diproses' => 'warning',
                        'ditolak' => 'danger',
                        'selesai' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('tanggal_permohonan')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('handler.name')
                    ->label('Handler')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'diterima' => 'Diterima',
                        'diproses' => 'Diproses',
                        'ditolak' => 'Ditolak',
                        'selesai' => 'Selesai',
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
            'index' => Pages\ListPermohonanInformasis::route('/'),
            'create' => Pages\CreatePermohonanInformasi::route('/create'),
            'edit' => Pages\EditPermohonanInformasi::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
