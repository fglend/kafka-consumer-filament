<?php

namespace Gurento\KafkaConsumerFilament;

use Illuminate\Support\ServiceProvider;

class KafkaConsumerFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Host app can register KafkaConsumerPlugin in its panel provider.
    }

    public function boot(): void
    {
    }
}
