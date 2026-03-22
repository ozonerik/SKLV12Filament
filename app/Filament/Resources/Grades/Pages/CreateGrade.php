<?php

namespace App\Filament\Resources\Grades\Pages;

use App\Filament\Resources\Grades\GradeResource;
use App\Models\SchoolYear;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class CreateGrade extends CreateRecord
{
    protected static string $resource = GradeResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_year_id')
                    ->label('Tahun Pelajaran')
                    ->options(SchoolYear::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),
                Select::make('student_id')
                    ->label('Siswa')
                    ->relationship(
                        'student',
                        'name',
                        fn ($query, $get) => $get('school_year_id')
                            ? $query->where('school_year_id', $get('school_year_id'))
                            : $query
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('subject_id')
                    ->relationship('subject', 'kode')
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Hapus school_year_id karena tidak ada di tabel grades
        unset($data['school_year_id']);
        
        return $data;
    }
}
