# Kafka Consumer Filament

Filament UI package for `gurento/kafka-consumer`.

## Install

```bash
composer require gurento/kafka-consumer gurento/kafka-consumer-filament
```

## Register Plugin (Filament v4/v5)

In your Panel Provider:

```php
use Gurento\KafkaConsumerFilament\Filament\Plugins\KafkaConsumerPlugin;

return $panel
    ->plugins([
        KafkaConsumerPlugin::make(),
    ]);
```

This registers the `Kafka Topics` resource with consume logs relation manager.
