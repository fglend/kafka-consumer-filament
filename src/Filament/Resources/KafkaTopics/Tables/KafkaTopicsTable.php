<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Tables;

use Gurento\KafkaConsumerFilament\Filament\Plugins\KafkaConsumerPlugin;
use Gurento\KafkaConsumer\Actions\MarkKafkaTopicHealthyAction;
use Gurento\KafkaConsumer\Actions\ReconsumeKafkaTopicFailuresAction;
use Gurento\KafkaConsumer\Actions\ResetKafkaTopicCountersAction;
use Gurento\KafkaConsumer\Models\KafkaTopic;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KafkaTopicsTable
{
    public static function configure(Table $table): Table
    {
        $plugin = KafkaConsumerPlugin::get();
        $pollInterval = $plugin ? $plugin->getTablePollInterval() : '10s';

        if ($pollInterval !== null) {
            $table->poll($pollInterval);
        }

        return $table
            ->columns([
                TextColumn::make('topic')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('model_class')
                    ->label('Target Model')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->searchable(),

                TextColumn::make('upsert_key')
                    ->label('Upsert Key')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('messages_consumed')
                    ->label('Consumed')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('messages_failed')
                    ->label('Failed')
                    ->numeric()
                    ->sortable()
                    ->color(fn (KafkaTopic $record): string => $record->messages_failed > 0 ? 'danger' : 'gray'),

                TextColumn::make('pending_retries')
                    ->label('Pending Retries')
                    ->getStateUsing(fn (KafkaTopic $record): int => $record->pendingRetryCount())
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'warning' : 'success'),

                TextColumn::make('failure_rate')
                    ->label('Failure Rate')
                    ->getStateUsing(fn (KafkaTopic $record): string => $record->failure_rate . '%')
                    ->badge()
                    ->color(fn (KafkaTopic $record): string => $record->failure_rate > 10 ? 'danger' : ($record->failure_rate > 0 ? 'warning' : 'success')),

                TextColumn::make('last_consumed_at')
                    ->label('Last Consumed')
                    ->since()
                    ->sortable()
                    ->placeholder('Never'),

                TextColumn::make('consumer_last_heartbeat_at')
                    ->label('Heartbeat')
                    ->since()
                    ->placeholder('Never')
                    ->sortable(),

                TextColumn::make('health_status')
                    ->label('Health')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'healthy' => 'success',
                        'degraded' => 'warning',
                        'stalled' => 'danger',
                        'inactive' => 'gray',
                        default => 'info',
                    }),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('active')
                    ->label('Active Only')
                    ->query(fn (Builder $q) => $q->where('is_active', true))
                    ->default(),
            ])
            ->recordActions([
                Action::make('reconsume')
                    ->label('Re-consume Failed')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalDescription('Retry failed messages for this topic.')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('limit')
                            ->label('Retry Limit')
                            ->default(50)
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(500)
                            ->required(),
                    ])
                    ->action(function (KafkaTopic $record, array $data): void {
                        $stats = app(ReconsumeKafkaTopicFailuresAction::class)
                            ->execute($record, (int) ($data['limit'] ?? 50));

                        Notification::make()
                            ->title('Re-consume completed')
                            ->body("Attempted {$stats['attempted']}, success {$stats['success']}, failed {$stats['failed']}.")
                            ->success()
                            ->send();
                    }),
                Action::make('markHealthy')
                    ->label('Mark Healthy')
                    ->icon('heroicon-o-shield-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (KafkaTopic $record): void {
                        app(MarkKafkaTopicHealthyAction::class)->execute($record);

                        Notification::make()
                            ->title('Topic marked healthy')
                            ->success()
                            ->send();
                    }),
                Action::make('resetCounters')
                    ->label('Reset Counters')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (KafkaTopic $record): void {
                        app(ResetKafkaTopicCountersAction::class)->execute($record);

                        Notification::make()
                            ->title('Counters reset')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
