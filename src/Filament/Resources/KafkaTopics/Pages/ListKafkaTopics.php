<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\KafkaTopicResource;

class ListKafkaTopics extends ListRecords
{
    protected static string $resource = KafkaTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
