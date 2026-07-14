<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Schemas;

use Gurento\KafkaConsumerFilament\Filament\Plugins\KafkaConsumerPlugin;
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
    /** Model classes for the dropdown — plugin override first, then app/Models scan. */
    private static function modelOptions(): array
    {
        $pluginOptions = KafkaConsumerPlugin::get()?->getModelOptions();

        if ($pluginOptions !== null) {
            return $pluginOptions;
        }

        return collect(glob(app_path('Models/*.php')))
            ->mapWithKeys(function (string $path): array {
                $class = 'App\\Models\\' . pathinfo($path, PATHINFO_FILENAME);
                return [$class => class_basename($class)];
            })
            ->all();
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Topic Configuration')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('topic')
                                    ->label('Kafka Topic')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('HR_APP.LIVE.office')
                                    ->maxLength(255),

                                Select::make('model_class')
                                    ->label('Target Model')
                                    ->options(self::modelOptions())
                                    ->searchable()
                                    ->required()
                                    ->helperText('Full class name of the Eloquent model to upsert into.'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('upsert_key')
                                    ->label('Upsert Key (Model Column)')
                                    ->required()
                                    ->default('id')
                                    ->helperText('Model column used as the unique key for updateOrCreate.'),

                                TagsInput::make('exclude_keys')
                                    ->label('Exclude Payload Keys')
                                    ->placeholder('e.g. old_values, owner')
                                    ->helperText('Top-level keys to strip from the payload before mapping.'),
                            ]),

                        Textarea::make('description')
                            ->rows(2)
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('max_reconsume_attempts')
                                    ->label('Max Re-consume Attempts')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(3)
                                    ->required(),

                                TextInput::make('retry_backoff_seconds')
                                    ->label('Retry Backoff (Seconds)')
                                    ->numeric()
                                    ->minValue(5)
                                    ->default(60)
                                    ->required(),

                                TextInput::make('health_stale_after_seconds')
                                    ->label('Health Stale Threshold (Seconds)')
                                    ->numeric()
                                    ->minValue(30)
                                    ->default(300)
                                    ->required(),
                            ]),
                    ]),

                Section::make('Relation Syncs')
                    ->description('Sync BelongsToMany relationships from nested arrays in the payload.')
                    ->schema([
                        Repeater::make('relations')
                            ->label('')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('payload_key')
                                            ->label('Payload Key')
                                            ->required()
                                            ->placeholder('office_controller'),

                                        TextInput::make('relationship')
                                            ->label('Model Relationship')
                                            ->required()
                                            ->placeholder('office_controller'),
                                    ]),

                                Grid::make(3)
                                    ->schema([
                                        Select::make('related_model')
                                            ->label('Related Model')
                                            ->options(self::modelOptions())
                                            ->searchable()
                                            ->required(),

                                        TextInput::make('related_lookup_key')
                                            ->label('Lookup Key (payload item field)')
                                            ->placeholder('uuid or id — auto-detected if blank'),

                                        TextInput::make('related_model_key')
                                            ->label('Related Model Key')
                                            ->placeholder('id (default: primary key)'),
                                    ]),
                            ])
                            ->addActionLabel('Add Relation Sync')
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => filled($state['payload_key'] ?? null)
                                ? ($state['payload_key'] . ' → ' . ($state['relationship'] ?? '?'))
                                : null
                            )
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),

                Section::make('Field Mapping')
                    ->description('Map payload keys to model columns. Leave empty to skip unmapped fields.')
                    ->schema([
                        Repeater::make('field_map')
                            ->label('')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('from')
                                            ->label('Payload Field')
                                            ->required()
                                            ->placeholder('uuid'),

                                        TextInput::make('to')
                                            ->label('Model Column')
                                            ->required()
                                            ->placeholder('id'),
                                    ]),
                            ])
                            ->addActionLabel('Add Field Mapping')
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => filled($state['from'] ?? null)
                                ? ($state['from'] . ' → ' . ($state['to'] ?? '?'))
                                : null
                            )
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
