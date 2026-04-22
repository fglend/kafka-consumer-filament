# Kafka Consumer Filament

`gurento/kafka-consumer-filament` is the Filament admin UI companion for `gurento/kafka-consumer`.

It provides a ready Kafka operations interface for:

- managing topic configurations
- viewing consume logs
- running re-consume actions from UI

Compatible with Filament v4 and v5.

## Installation

```bash
composer require gurento/kafka-consumer gurento/kafka-consumer-filament
```

## Register Plugin

In your Filament panel provider:

```php
use Gurento\KafkaConsumerFilament\Filament\Plugins\KafkaConsumerPlugin;

return $panel
    ->plugins([
        KafkaConsumerPlugin::make(),
    ]);
```

## What It Registers

- `Kafka Topics` resource
- CRUD pages for topic mappings
- `Consume Logs` relation manager
- operations actions (for re-consume/health workflows depending on your setup)

## Typical Workflow

1. Open `Kafka Topics` in Filament.
2. Create topic mapping:
   - topic name
   - target model class
   - upsert key
   - field mappings
3. Run consumer command:

```bash
php artisan gurento:kafka-consume
```

4. Monitor logs in the resource relation manager.
5. Re-consume failed messages when needed.

## Security and Access

This package only provides UI classes.

You should define policies/permissions in host app to restrict who can:

- edit topic mappings
- run replay actions
- inspect payload/error logs

## Troubleshooting

### Resource not visible

- Ensure plugin is added to the same panel you are using.
- Run `php artisan optimize:clear`.

### Class not found errors

- Confirm both packages are installed.
- Run `composer dump-autoload`.

## License

MIT
