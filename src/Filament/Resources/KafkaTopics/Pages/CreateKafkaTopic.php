<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Pages;

use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\KafkaTopicResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKafkaTopic extends CreateRecord
{
    protected static string $resource = KafkaTopicResource::class;
}
