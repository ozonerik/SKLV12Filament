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

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('rank')
                    ->required(),
                TextInput::make('nip')
                    ->required(),
                // Perubahan di sini: Menggunakan FileUpload
                FileUpload::make('ttd')
                    ->label('Tanda Tangan')
                    ->image() // Memastikan file adalah gambar
                    ->disk('public') // MEMASTIKAN tersimpan di storage/app/public
                    ->directory('headmasters/signatures') // File akan ada di storage/app/public/headmasters/signatures
                    ->visibility('public')
                    ->previewable(false) // Nonaktifkan preview karena kita akan menampilkan gambar di tabel, bukan di form
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('rank')
                    ->searchable(),
                TextColumn::make('nip')
                    ->searchable(),
                // Menampilkan preview tanda tangan di tabel
                ImageColumn::make('ttd')
                    ->label('Tanda Tangan')
                    ->disk('public') // Pastikan ini sesuai dengan disk yang digunakan di FileUpload
                    //->directory('headmasters/signatures') // Pastikan ini sesuai dengan directory yang digunakan di
                    ->visibility('public'),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
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

    public static function getPages(): array
    {
        return [
            'index' => ListHeadmasters::route('/'),
            'create' => CreateHeadmaster::route('/create'),
            'edit' => EditHeadmaster::route('/{record}/edit'),
        ];
    }
}
