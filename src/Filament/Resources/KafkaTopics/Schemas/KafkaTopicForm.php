<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KafkaTopicForm
{
    private static function modelOptions(): array
    {
        return collect(glob(app_path('Models/*.php')))
            ->mapWithKeys(function (string $path): array {
                $class = 'App\\Models\\' . pathinfo($path, PATHINFO_FILENAME);
                return [$class => class_basename($class)];
            })
            ->all();
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Topic Configuration')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('topic')->required()->unique(ignoreRecord: true),
                        Select::make('model_class')->options(self::modelOptions())->searchable()->required(),
                    ]),
                    Grid::make(2)->schema([
                        TextInput::make('upsert_key')->required()->default('id'),
                        TagsInput::make('exclude_keys'),
                    ]),
                    Textarea::make('description')->rows(2)->columnSpanFull(),
                    Toggle::make('is_active')->default(true),
                    Grid::make(3)->schema([
                        TextInput::make('max_reconsume_attempts')->numeric()->minValue(1)->default(3)->required(),
                        TextInput::make('retry_backoff_seconds')->numeric()->minValue(5)->default(60)->required(),
                        TextInput::make('health_stale_after_seconds')->numeric()->minValue(30)->default(300)->required(),
                    ]),
                ]),
            Section::make('Field Mapping')
                ->schema([
                    Repeater::make('field_map')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('from')->required(),
                                TextInput::make('to')->required(),
                            ]),
                        ])
                        ->addActionLabel('Add Field Mapping')
                        ->defaultItems(0)
                        ->collapsible()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
