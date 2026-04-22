<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Pages;

use Gurento\KafkaConsumer\Actions\MarkKafkaTopicHealthyAction;
use Gurento\KafkaConsumer\Actions\ReconsumeKafkaTopicFailuresAction;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\KafkaTopicResource;
use Gurento\KafkaConsumer\Models\KafkaTopic;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewKafkaTopic extends ViewRecord
{
    protected static string $resource = KafkaTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reconsume')
                ->label('Re-consume Failed')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
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
                ->action(function (KafkaTopic $record): void {
                    app(MarkKafkaTopicHealthyAction::class)->execute($record);

                    Notification::make()
                        ->title('Topic marked healthy')
                        ->success()
                        ->send();
                }),
            EditAction::make(),
        ];
    }
}
