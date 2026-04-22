<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Pages;

use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\KafkaTopicResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditKafkaTopic extends EditRecord
{
    protected static string $resource = KafkaTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->redirect(
            $this->getResource()::getUrl('view', ['record' => $this->record->getKey()])
        );
    }
}
