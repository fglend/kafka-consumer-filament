<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\KafkaTopicResource;

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
}
