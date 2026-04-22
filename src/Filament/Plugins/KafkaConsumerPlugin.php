<?php

namespace Gurento\KafkaConsumerFilament\Filament\Plugins;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\KafkaTopicResource;

class KafkaConsumerPlugin implements Plugin
{
    public function getId(): string
    {
        return 'kafka-consumer';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            KafkaTopicResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
