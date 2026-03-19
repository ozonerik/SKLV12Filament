<?php

namespace App\Filament\Resources\Grades;

use App\Filament\Resources\Grades\Pages\CreateGrade;
use App\Filament\Resources\Grades\Pages\EditGrade;
use App\Filament\Resources\Grades\Pages\ListGrades;
use App\Models\Grade;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('score')
                    ->required()
                    ->minValue(0)
                    ->maxValue(100)
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('student.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('score')
                    ->numeric()
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
            'index' => ListGrades::route('/'),
            'create' => CreateGrade::route('/create'),
            'edit' => EditGrade::route('/{record}/edit'),
        ];
    }
}
