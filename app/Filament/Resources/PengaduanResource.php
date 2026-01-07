<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengaduanResource\Pages;
use App\Models\Pengaduan;
use Filament\Forms\Components\DatePicker;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PengaduanResource extends Resource
{
    public static function getModel(): string
    {
        return Pengaduan::class;
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-chat-bubble-left-right';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Layanan Publik';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Section::make('Data Pelapor')
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('nomor_tiket')
                                    ->label('Nomor Tiket')
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('nama_pelapor')
                                    ->label('Nama Pelapor')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->email()
                                    ->required(),
                                TextInput::make('telepon')
                                    ->tel(),
                                Textarea::make('alamat')
                                    ->rows(2),
                                Toggle::make('is_anonymous')
                                    ->label('Anonim'),
                            ])->columns(2),

                        Section::make('Status Pengaduan')
                            ->columnSpan(1)
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'baru' => 'Baru',
                                        'diproses' => 'Diproses',
                                        'ditanggapi' => 'Ditanggapi',
                                        'selesai' => 'Selesai',
                                        'ditolak' => 'Ditolak',
                                    ])
                                    ->default('baru')
                                    ->required(),
                                Select::make('handled_by')
                                    ->label('Ditangani Oleh')
                                    ->relationship('handler', 'name')
                                    ->searchable()
                                    ->preload(),
                                DateTimePicker::make('tanggal_tanggapan')
                                    ->label('Tanggal Tanggapan'),
                            ]),
                    ]),

                Section::make('Detail Pengaduan')
                    ->schema([
                        Select::make('kategori')
                            ->options([
                                'layanan' => 'Layanan Publik',
                                'infrastruktur' => 'Infrastruktur',
                                'keamanan' => 'Keamanan',
                                'administrasi' => 'Administrasi',
                                'lainnya' => 'Lainnya',
                            ]),
                        TextInput::make('judul')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('isi_pengaduan')
                            ->label('Isi Pengaduan')
                            ->required()
                            ->rows(5),
                        TextInput::make('lokasi_kejadian')
                            ->label('Lokasi Kejadian'),
                        DatePicker::make('tanggal_kejadian')
                            ->label('Tanggal Kejadian'),
                        FileUpload::make('lampiran')
                            ->multiple()
                            ->directory('pengaduan-lampiran')
                            ->downloadable(),
                    ])->columns(2),

                Section::make('Tanggapan')
                    ->schema([
                        Textarea::make('tanggapan')
                            ->rows(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_tiket')
                    ->label('Tiket')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('kategori')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'baru' => 'info',
                        'diproses' => 'warning',
                        'ditanggapi' => 'primary',
                        'selesai' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'baru' => 'Baru',
                        'diproses' => 'Diproses',
                        'ditanggapi' => 'Ditanggapi',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
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
            'index' => Pages\ListPengaduans::route('/'),
            'create' => Pages\CreatePengaduan::route('/create'),
            'edit' => Pages\EditPengaduan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
