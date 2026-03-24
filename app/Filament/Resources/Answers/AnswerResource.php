<?php

namespace App\Filament\Resources\Answers;

use App\Filament\Resources\Answers\Pages\CreateAnswer;
use App\Filament\Resources\Answers\Pages\EditAnswer;
use App\Filament\Resources\Answers\Pages\ListAnswers;
use App\Models\Answer;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnswerResource extends Resource
{
    protected static ?string $model = Answer::class;

    protected static string|BackedEnum|null $navigationIcon = 'fluentui-book-letter-20-o';

    protected static ?string $modelLabel = 'Jawaban Siswa';

    protected static ?string $pluralModelLabel = 'Jawaban Siswa';

    protected static ?string $navigationLabel = 'Jawaban Siswa';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->label('Siswa')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('question_id')
                    ->relationship('question', 'question_text')
                    ->label('Pertanyaan')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('question_option_id')
                    ->relationship('option', 'option_text')
                    ->label('Jawaban (Pilihan ganda)')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Textarea::make('answer_text')
                    ->label('Jawaban (Essay)')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->persistColumnsInSession(false)
            ->columns([
                TextColumn::make('student.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('question.question_text')
                    ->label('Pertanyaan')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('option.option_text')
                    ->label('Jawaban (Pilihan ganda)')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('answer_text')
                    ->label('Jawaban (Essay)')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
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
        return 'Questionnaire';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnswers::route('/'),
            'create' => CreateAnswer::route('/create'),
            'edit' => EditAnswer::route('/{record}/edit'),
        ];
    }
}
