<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Filament\Resources\Questions\QuestionResource;
use App\Models\QuestionOption;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;

    private ?array $questionOptions = null;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('questionnaire_id')
                    ->relationship('questionnaire', 'title')
                    ->label('Kuesioner')
                    ->searchable()
                    ->preload()
                    ->required(),
                Textarea::make('question_text')
                    ->label('Pertanyaan')
                    ->required()
                    ->columnSpanFull(),
                Select::make('type')
                    ->label('Tipe')
                    ->options(['essay' => 'Essay', 'pg' => 'Pilihan ganda'])
                    ->required()
                    ->live(),
                TextInput::make('weight')
                    ->label('Bobot')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('order')
                    ->label('Urutan')
                    ->required()
                    ->numeric()
                    ->default(0),
                Repeater::make('question_options')
                    ->label('Opsi')
                    ->visible(fn(Get $get): bool => $get('type') === 'pg')
                    ->schema([
                        TextInput::make('option_text')
                            ->label('Opsi')
                            ->required(),
                    ])
                    ->addActionLabel('Tambah Opsi')
                    ->columnSpanFull(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pisahkan nested question_options dari data utama
        if (isset($data['question_options'])) {
            $this->questionOptions = $data['question_options'];
            unset($data['question_options']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        // Simpan nested question options setelah question dibuat
        if (isset($this->questionOptions) && is_array($this->questionOptions)) {
            foreach ($this->questionOptions as $option) {
                if (filled($option['option_text'] ?? null)) {
                    QuestionOption::create([
                        'question_id' => $this->record->id,
                        'option_text' => $option['option_text'],
                    ]);
                }
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
