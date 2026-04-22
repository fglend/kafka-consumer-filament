<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Schemas;

use Gurento\KafkaConsumer\Models\KafkaTopic;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KafkaTopicInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Topic Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('topic')
                                    ->label('Kafka Topic')
                                    ->copyable(),

                                TextEntry::make('model_class')
                                    ->label('Target Model')
                                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                                    ->helperText(fn (string $state): string => $state),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('upsert_key')
                                    ->label('Upsert Key')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('exclude_keys')
                                    ->label('Excluded Keys')
                                    ->badge()
                                    ->separator(',')
                                    ->color('warning')
                                    ->placeholder('None'),
                            ]),

                        TextEntry::make('description')
                            ->placeholder('—')
                            ->columnSpanFull(),

                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean(),
                    ]),

                Section::make('Consumption Stats')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('messages_consumed')
                                    ->label('Messages Consumed')
                                    ->numeric(),

                                TextEntry::make('messages_failed')
                                    ->label('Messages Failed')
                                    ->numeric()
                                    ->color('danger'),

                                TextEntry::make('messages_reconsumed')
                                    ->label('Messages Re-consumed')
                                    ->numeric(),

                                TextEntry::make('last_consumed_at')
                                    ->label('Last Consumed')
                                    ->dateTime()
                                    ->placeholder('Never'),

                                TextEntry::make('logs_count')
                                    ->label('Log Entries')
                                    ->getStateUsing(fn (KafkaTopic $record): int => $record->logs()->count()),
                            ]),

                        Grid::make(4)
                            ->schema([
                                TextEntry::make('health_status')
                                    ->label('Queue Health')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'healthy' => 'success',
                                        'degraded' => 'warning',
                                        'stalled' => 'danger',
                                        'inactive' => 'gray',
                                        default => 'info',
                                    }),

                                TextEntry::make('consumer_last_heartbeat_at')
                                    ->label('Last Heartbeat')
                                    ->dateTime()
                                    ->placeholder('Never'),

                                TextEntry::make('consumer_lag_seconds')
                                    ->label('Lag (Seconds)')
                                    ->numeric()
                                    ->placeholder('—'),

                                TextEntry::make('failure_rate')
                                    ->label('Failure Rate')
                                    ->getStateUsing(fn (KafkaTopic $record): string => $record->failure_rate . '%')
                                    ->badge(),

                                TextEntry::make('pending_retries')
                                    ->label('Pending Retries')
                                    ->getStateUsing(fn (KafkaTopic $record): int => $record->pendingRetryCount())
                                    ->badge()
                                    ->color(fn (int $state): string => $state > 0 ? 'warning' : 'success'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('max_reconsume_attempts')
                                    ->label('Max Re-consume Attempts')
                                    ->numeric(),

                                TextEntry::make('retry_backoff_seconds')
                                    ->label('Retry Backoff (Seconds)')
                                    ->numeric(),

                                TextEntry::make('health_stale_after_seconds')
                                    ->label('Stale Threshold (Seconds)')
                                    ->numeric(),
                            ]),

                        TextEntry::make('consumer_last_error')
                            ->label('Last Consumer Error')
                            ->placeholder('None')
                            ->columnSpanFull(),
                    ]),

                Section::make('Field Map')
                    ->description('Payload fields mapped to model columns.')
                    ->schema([
                        RepeatableEntry::make('field_map')
                            ->label('')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('from')
                                            ->label('Payload Field')
                                            ->badge()
                                            ->color('info'),

                                        TextEntry::make('to')
                                            ->label('Model Column')
                                            ->badge()
                                            ->color('success'),
                                    ]),
                            ])
                            ->placeholder('No field mappings defined.')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),

                Section::make('Relation Syncs')
                    ->description('BelongsToMany relationships synced from nested payload arrays.')
                    ->schema([
                        RepeatableEntry::make('relations')
                            ->label('')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('payload_key')
                                            ->label('Payload Key')
                                            ->badge()
                                            ->color('info'),

                                        TextEntry::make('relationship')
                                            ->label('Relationship')
                                            ->badge()
                                            ->color('success'),
                                    ]),

                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('related_model')
                                            ->label('Related Model')
                                            ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—')
                                            ->helperText(fn (?string $state): string => $state ?? ''),

                                        TextEntry::make('related_lookup_key')
                                            ->label('Lookup Key')
                                            ->badge()
                                            ->color('gray')
                                            ->placeholder('auto (uuid → id)'),

                                        TextEntry::make('related_model_key')
                                            ->label('Related Model Key')
                                            ->badge()
                                            ->color('gray')
                                            ->placeholder('primary key'),
                                    ]),
                            ])
                            ->placeholder('No relation syncs configured.')
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }
}
