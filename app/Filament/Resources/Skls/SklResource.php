<?php

namespace App\Filament\Resources\Skls;

use App\Filament\Resources\Skls\Pages\CreateSkl;
use App\Filament\Resources\Skls\Pages\EditSkl;
use App\Filament\Resources\Skls\Pages\ListSkls;
use App\Models\Skl;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SklResource extends Resource
{
    protected static ?string $model = Skl::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'letter_number';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->options(['Lulus' => 'Lulus', 'Tidak Lulus' => 'Tidak lulus'])
                    ->required(),
                DatePicker::make('letter_date')
                    ->required(),
                DateTimePicker::make('published_at')
                    ->required(),
                Toggle::make('is_questionnaire_completed')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('letter_number')
            ->columns([
                TextColumn::make('student.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                // Menampilkan Jurusan dari relasi Student -> Major
                TextColumn::make('student.major.konsentrasi_keahlian')
                    ->label('Jurusan')
                    ->sortable()
                    ->searchable(),
                // Menampilkan Tahun Pelajaran dari relasi Student -> SchoolYear
                TextColumn::make('student.schoolYear.name')
                    ->label('Tahun Pelajaran')
                    ->sortable(),
                TextColumn::make('letter_number')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('letter_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_questionnaire_completed')
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
            'index' => ListSkls::route('/'),
            'create' => CreateSkl::route('/create'),
            'edit' => EditSkl::route('/{record}/edit'),
        ];
    }
}
