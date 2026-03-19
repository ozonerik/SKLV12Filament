<?php

namespace App\Filament\Resources\Questions\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'options';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        // Opsi hanya relevan untuk pertanyaan pilihan ganda.
        if (($ownerRecord->type ?? null) !== 'pg') {
            return false;
        }

        return parent::canViewForRecord($ownerRecord, $pageClass);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('option_text')
                ->label('Opsi')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('option_text')
            ->columns([
                TextColumn::make('option_text')->searchable(),
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

