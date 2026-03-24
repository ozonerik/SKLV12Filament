<?php

namespace App\Filament\Resources\Headmasters;

use App\Filament\Resources\Headmasters\Pages\CreateHeadmaster;
use App\Filament\Resources\Headmasters\Pages\EditHeadmaster;
use App\Filament\Resources\Headmasters\Pages\ListHeadmasters;
use App\Models\Headmaster;
use Filament\Forms\Components\FileUpload; // Import initially untuk FileUpload, tapi kita akan menggunakan TextInput untuk menyimpan path/URL tanda tangan
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
//use Filament\Forms\Form; // Menggunakan Form untuk mendefinisikan form secara lebih fleksibel
use Filament\Tables\Columns\ImageColumn; // Tambahkan untuk menampilkan gambar di tabel

class HeadmasterResource extends Resource
{
    protected static ?string $model = Headmaster::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'Kepala Sekolah';

    protected static ?string $pluralModelLabel = 'Kepala Sekolah';

    protected static ?string $navigationLabel = 'Kepala Sekolah';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Kepala Sekolah')
                    ->required(),
                TextInput::make('rank')
                    ->label('Pangkat/Golongan')
                    ->required(),
                TextInput::make('nip')
                    ->label('NIP')
                    ->required(),
                // Perubahan di sini: Menggunakan FileUpload
                FileUpload::make('ttd')
                    ->label('Tanda Tangan')
                    ->image()
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->disk('public')
                    ->directory('headmasters/signatures')
                    ->required(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->persistColumnsInSession(false)
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kepala Sekolah')
                    ->searchable(),
                TextColumn::make('rank')
                    ->label('Pangkat/Golongan')
                    ->searchable(),
                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable(),
                // Menampilkan preview tanda tangan di tabel
                ImageColumn::make('ttd')
                    ->label('Tanda Tangan')
                    ->disk('public') // Pastikan ini sesuai dengan disk yang digunakan di FileUpload
                    //->directory('headmasters/signatures') // Pastikan ini sesuai dengan directory yang digunakan di
                    ->visibility('public'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHeadmasters::route('/'),
            'create' => CreateHeadmaster::route('/create'),
            'edit' => EditHeadmaster::route('/{record}/edit'),
        ];
    }
}
