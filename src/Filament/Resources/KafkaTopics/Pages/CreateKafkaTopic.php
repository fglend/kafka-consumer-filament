<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Pages;

use Filament\Resources\Pages\CreateRecord;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\KafkaTopicResource;

class CreateKafkaTopic extends CreateRecord
{
    protected static string $resource = KafkaTopicResource::class;
}
