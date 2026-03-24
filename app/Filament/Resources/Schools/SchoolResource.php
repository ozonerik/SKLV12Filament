<?php

namespace App\Filament\Resources\Schools;

use App\Filament\Resources\Schools\Pages\EditSchool;
use App\Filament\Resources\Schools\Pages\ListSchools;
use App\Models\School;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $modelLabel = 'Profil Sekolah';

    protected static ?string $pluralModelLabel = 'Profil Sekolah';

    protected static ?string $navigationLabel = 'Profil Sekolah';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Sekolah')
                    ->required()
                    ->maxLength(255),
                Textarea::make('vision')
                    ->label('Visi Sekolah')
                    ->required()
                    ->default('Mewujudkan insan yang berakhlak mulia dan kompeten.')
                    ->rows(2)
                    ->maxLength(65535),
                Textarea::make('address')
                    ->label('Alamat Sekolah')
                    ->required()
                    ->rows(3)
                    ->maxLength(65535),
                TextInput::make('city')
                    ->label('Kab/Kota')
                    ->required()
                    ->default('Indramayu')
                    ->maxLength(100),
                TextInput::make('postal_code')
                    ->label('Kodepos')
                    ->required()
                    ->maxLength(20),
                TextInput::make('website')
                    ->label('Website')
                    ->url()
                    ->nullable()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->nullable()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Telp')
                    ->nullable()
                    ->maxLength(50),
                TextInput::make('province')
                    ->label('Provinsi')
                    ->required()
                    ->maxLength(255),
                TextInput::make('kcd_wilayah')
                    ->label('KCD Wilayah')
                    ->required()
                    ->maxLength(50),
                FileUpload::make('province_logo')
                    ->label('Logo Provinsi')
                    ->image()
                    ->directory('schools')
                    ->disk('public')
                    ->nullable(),
                FileUpload::make('school_logo')
                    ->label('Logo Sekolah')
                    ->image()
                    ->directory('schools')
                    ->disk('public')
                    ->nullable(),
                FileUpload::make('school_stamp')
                    ->label('Stamp Sekolah')
                    ->image()
                    ->directory('schools')
                    ->disk('public')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->persistColumnsInSession(false)
            ->columnManagerTriggerAction(fn(Action $action): Action => $action->label('Pilih Kolom'))
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Sekolah')
                    ->searchable()
                    ->toggleable(),
                ImageColumn::make('province_logo')
                    ->label('Logo Provinsi')
                    ->disk('public')
                    ->square()
                    ->toggleable(),
                ImageColumn::make('school_logo')
                    ->label('Logo Sekolah')
                    ->disk('public')
                    ->square()
                    ->toggleable(),
                ImageColumn::make('school_stamp')
                    ->label('Stamp Sekolah')
                    ->disk('public')
                    ->square()
                    ->toggleable(),
                TextColumn::make('address')
                    ->label('Alamat Sekolah')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('city')
                    ->label('Kab/Kota')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('postal_code')
                    ->label('Kodepos')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('website')
                    ->label('Website')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('Telp')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('province')
                    ->label('Provinsi')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('kcd_wilayah')
                    ->label('KCD Wilayah')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchools::route('/'),
            'edit' => EditSchool::route('/{record}/edit'),
        ];
    }
}
