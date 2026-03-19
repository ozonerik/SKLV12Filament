<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('pob')
                    ->required(),
                DatePicker::make('dob')
                    ->required(),
                TextInput::make('nis')
                    ->required(),
                TextInput::make('nisn')
                    ->required(),
                TextInput::make('father_name')
                    ->required(),
                Select::make('major_id')
                    ->relationship('major', 'program_keahlian')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->helperText('Biarkan kosong untuk memakai default password tanggal lahir (ddmmyyyy).')
                    ->dehydrateStateUsing(fn (?string $state) => filled($state) ? $state : null)
                    ->dehydrated(fn (?string $state) => filled($state)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('pob')
                    ->searchable(),
                TextColumn::make('dob')
                    ->date()
                    ->sortable(),
                TextColumn::make('nis')
                    ->searchable(),
                TextColumn::make('nisn')
                    ->searchable(),
                TextColumn::make('father_name')
                    ->searchable(),
                TextColumn::make('major.program_keahlian')
                    ->label('Jurusan')
                    ->searchable()
                    ->sortable(),
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
            'index' => ListStudents::route('/'),
            'create' => CreateStudent::route('/create'),
            'edit' => EditStudent::route('/{record}/edit'),
        ];
    }
}
