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
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Major;
use App\Models\SchoolYear;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'Siswa';

    protected static ?string $pluralModelLabel = 'Siswa';

    protected static ?string $navigationLabel = 'Siswa';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_year_id')
                    ->relationship('schoolYear', 'name')
                    ->label('Tahun Pelajaran')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('major_id')
                    ->relationship('major', 'kode_jurusan')
                    ->label('Jurusan')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('nisn')
                    ->label('NISN')
                    ->required(),
                TextInput::make('nis')
                    ->label('NIS')
                    ->required(),
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                Select::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->required(),
                TextInput::make('pob')
                    ->label('Tempat Lahir')
                    ->required(),
                DatePicker::make('dob')
                    ->label('Tanggal Lahir')
                    ->native(false)
                    ->locale('id')
                    ->required()
                    ->displayFormat('d/m/Y'),
                TextInput::make('father_name')
                    ->label('Nama Ayah')
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->helperText('Biarkan kosong untuk memakai default password tanggal lahir (ddmmyyyy).')
                    ->dehydrateStateUsing(fn(?string $state) => filled($state) ? $state : null)
                    ->dehydrated(fn(?string $state) => filled($state)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('schoolYear.name')
                    ->label('Tahun Pelajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('major.konsentrasi_keahlian')
                    ->label('Jurusan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable(),
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('jenis_kelamin')
                    ->label('Jenis Kelamin'),
                TextColumn::make('pob')
                    ->label('Tempat Lahir')
                    ->searchable(),
                TextColumn::make('dob')
                    ->date('d/m/Y')
                    ->label('Tanggal Lahir')
                    ->sortable(),
                TextColumn::make('father_name')
                    ->label('Nama Ayah')
                    ->searchable(),
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
                SelectFilter::make('major_id')
                    ->label('Jurusan')
                    ->options(Major::query()->orderBy('konsentrasi_keahlian')->pluck('konsentrasi_keahlian', 'id')->all())
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('student', function (Builder $studentQuery) use ($data): void {
                            $studentQuery->where('major_id', $data['value']);
                        });
                    }),
                SelectFilter::make('school_year_id')
                    ->label('Tahun Pelajaran')
                    ->options(SchoolYear::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas('student', function (Builder $studentQuery) use ($data): void {
                            $studentQuery->where('school_year_id', $data['value']);
                        });
                    }),
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
        return 'Main';
    }



    public static function getNavigationSort(): ?int
    {
        return 1;
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
