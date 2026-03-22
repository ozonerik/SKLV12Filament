<?php

namespace App\Filament\Resources\QuestionOptions;

use App\Filament\Resources\QuestionOptions\Pages\CreateQuestionOption;
use App\Filament\Resources\QuestionOptions\Pages\EditQuestionOption;
use App\Filament\Resources\QuestionOptions\Pages\ListQuestionOptions;
use App\Models\QuestionOption;
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

class QuestionOptionResource extends Resource
{
    protected static ?string $model = QuestionOption::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'option_text';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('question_id')
                    ->relationship('question', 'question_text')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('option_text')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('option_text')
            ->columns([
                TextColumn::make('question.question_text')
                    ->label('Pertanyaan')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('option_text')
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
            'index' => ListQuestionOptions::route('/'),
            'create' => CreateQuestionOption::route('/create'),
            'edit' => EditQuestionOption::route('/{record}/edit'),
        ];
    }
}
