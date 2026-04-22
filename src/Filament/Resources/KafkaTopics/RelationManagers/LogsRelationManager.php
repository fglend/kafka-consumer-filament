<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\RelationManagers;

use Gurento\KafkaConsumer\Services\KafkaConsumerService;
use Gurento\KafkaConsumer\Models\KafkaConsumeLog;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    protected static ?string $title = 'Consume Logs';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('consumed_at', 'desc')
            ->columns([
                TextColumn::make('consumed_at')
                    ->label('Time')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'success' => 'success',
                        'reconsumed_success' => 'success',
                        'failed' => 'danger',
                        'skipped' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('attempt_count')
                    ->label('Attempts')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('upsert_key_value')
                    ->label('Key Value')
                    ->placeholder('—'),

                TextColumn::make('next_retry_at')
                    ->label('Next Retry')
                    ->since()
                    ->placeholder('—'),

                TextColumn::make('resolved_at')
                    ->label('Resolved')
                    ->since()
                    ->placeholder('—'),

                TextColumn::make('error')
                    ->label('Error')
                    ->wrap()
                    ->placeholder('—')
                    ->visible(fn(): bool => true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'success' => 'Success',
                        'reconsumed_success' => 'Re-consumed Success',
                        'failed' => 'Failed',
                        'skipped' => 'Skipped',
                    ]),
            ])
            ->recordActions([
                Action::make('retry')
                    ->label('Re-consume')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn ($record): bool => $record->status === 'failed' && $record->retryable)
                    ->requiresConfirmation()
                    ->action(function (KafkaConsumeLog $record, KafkaConsumerService $service): void {
                        $succeeded = $service->reconsumeLog($record);
                        $level = $succeeded ? 'success' : 'danger';

                        Notification::make()
                            ->title($succeeded ? 'Re-consume succeeded' : 'Re-consume failed')
                            ->body('Attempted 1 record.')
                            ->{$level}()
                            ->send();
                    }),
                ViewAction::make()
                    ->modalHeading('Log Entry')
                    ->infolist(fn(Schema $schema): Schema => $schema->components([
                        \Filament\Infolists\Components\TextEntry::make('consumed_at')->dateTime(),
                        \Filament\Infolists\Components\TextEntry::make('status')->badge(),
                        \Filament\Infolists\Components\TextEntry::make('attempt_count')->numeric(),
                        \Filament\Infolists\Components\TextEntry::make('next_retry_at')->dateTime()->placeholder('—'),
                        \Filament\Infolists\Components\TextEntry::make('resolved_at')->dateTime()->placeholder('—'),
                        \Filament\Infolists\Components\TextEntry::make('upsert_key_value')->placeholder('—'),
                        \Filament\Infolists\Components\TextEntry::make('error')->placeholder('—')->columnSpanFull(),
                        \Filament\Infolists\Components\TextEntry::make('kafka_partition')->placeholder('—'),
                        \Filament\Infolists\Components\TextEntry::make('kafka_offset')->placeholder('—'),
                        \Filament\Infolists\Components\TextEntry::make('kafka_key')->placeholder('—'),
                        \Filament\Infolists\Components\TextEntry::make('payload')
                            ->label('Payload')
                            ->getStateUsing(fn($record) => json_encode($record->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
                            ->columnSpanFull(),
                    ])),
            ]);
    }
}
