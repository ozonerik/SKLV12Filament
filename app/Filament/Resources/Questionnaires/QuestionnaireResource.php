<?php

namespace App\Filament\Resources\Questionnaires;

use App\Filament\Resources\Questionnaires\Pages\CreateQuestionnaire;
use App\Filament\Resources\Questionnaires\Pages\EditQuestionnaire;
use App\Filament\Resources\Questionnaires\Pages\ListQuestionnaires;
use App\Filament\Resources\Questionnaires\RelationManagers\QuestionsRelationManager;
use App\Models\Questionnaire;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuestionnaireResource extends Resource
{
    protected static ?string $model = Questionnaire::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                DatePicker::make('start_date')
                        ->native(false)
                        ->locale('id')
                    ->required()
                    ->displayFormat('d/m/Y'),
                DatePicker::make('end_date')
                        ->native(false)
                        ->locale('id')
                    ->required()
                    ->displayFormat('d/m/Y'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date('d/m/Y')
                    ->sortable(),
                IconColumn::make('is_active')
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
            QuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuestionnaires::route('/'),
            'create' => CreateQuestionnaire::route('/create'),
            'edit' => EditQuestionnaire::route('/{record}/edit'),
        ];
    }
}
