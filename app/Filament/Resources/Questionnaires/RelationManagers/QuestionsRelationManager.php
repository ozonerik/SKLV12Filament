<?php

namespace App\Filament\Resources\Questionnaires\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('question_text')
                ->label('Pertanyaan')
                ->required()
                ->columnSpanFull(),
            Select::make('type')
                ->options([
                    'pg' => 'Pilihan ganda',
                    'essay' => 'Essay',
                ])
                ->required(),
            TextInput::make('weight')
                ->numeric()
                ->required()
                ->default(1),
            TextInput::make('order')
                ->numeric()
                ->required()
                ->default(1),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question_text')
            ->columns([
                TextColumn::make('order')->sortable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('question_text')->wrap()->searchable(),
                TextColumn::make('weight')->numeric()->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}

