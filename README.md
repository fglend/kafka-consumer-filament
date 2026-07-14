# Kafka Consumer Filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gurento/kafka-consumer-filament.svg?style=flat-square)](https://packagist.org/packages/gurento/kafka-consumer-filament)
[![Total Downloads](https://img.shields.io/packagist/dt/gurento/kafka-consumer-filament.svg?style=flat-square)](https://packagist.org/packages/gurento/kafka-consumer-filament)
[![License](https://img.shields.io/github/license/gurento/kafka-consumer-filament?style=flat-square)](LICENSE)

A [Filament](https://filamentphp.com) admin panel for [`gurento/kafka-consumer`](https://packagist.org/packages/gurento/kafka-consumer) — manage Kafka topic mappings, monitor consumer health, and replay failed messages without leaving your admin panel.

## Features

- **Topic management** — full CRUD for topic-to-model mappings, field maps, and relation syncs
- **Live monitoring** — auto-polling table with consumed/failed counters, failure rate, heartbeat, lag, and health badges
- **Consume logs** — per-message log viewer with payload inspection, Kafka partition/offset/key metadata, and status filters
- **One-click operations** — re-consume failed messages, mark topics healthy, and reset counters from the UI
- **Fully customizable** — navigation label, icon, group, badge, slug, labels, and polling are all configurable via a fluent plugin API

## Requirements

| Dependency | Version |
|---|---|
| PHP | 8.2+ |
| Laravel | 11 / 12 |
| Filament | 4.x / 5.x |
| gurento/kafka-consumer | ^1.0 |

## Installation

```bash
composer require gurento/kafka-consumer gurento/kafka-consumer-filament
```

Then register the plugin in your Filament panel provider:

```php
use Gurento\KafkaConsumerFilament\Filament\Plugins\KafkaConsumerPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            KafkaConsumerPlugin::make(),
        ]);
}
```

That's it — a **Kafka Topics** resource appears in your panel's navigation.

## Customization

Every presentation element is dynamic. Configure it fluently on the plugin — each setter accepts a plain value or a `Closure`:

```php
KafkaConsumerPlugin::make()
    ->navigationLabel('Event Streams')            // sidebar title
    ->navigationIcon('heroicon-o-queue-list')     // hero icon (string or BackedEnum)
    ->navigationGroup('Integrations')
    ->navigationSort(5)
    ->navigationBadge()                           // badge showing pending retries
    ->modelLabel('Stream')
    ->pluralModelLabel('Streams')
    ->slug('event-streams')                       // URL: /admin/event-streams
    ->tablePollInterval('30s')                    // null disables auto-refresh
    ->modelOptions(fn (): array => [              // restrict target-model dropdown
        \App\Models\Office::class => 'Office',
        \App\Models\Employee::class => 'Employee',
    ]),
```

### Available options

| Method | Type | Default | Description |
|---|---|---|---|
| `navigationLabel()` | `string\|Closure` | `Kafka Topics` | Sidebar navigation title |
| `navigationIcon()` | `string\|BackedEnum\|Closure` | `heroicon-o-arrow-down-on-square` | Navigation icon |
| `navigationGroup()` | `string\|UnitEnum\|Closure` | `System` | Navigation group |
| `navigationSort()` | `int\|Closure` | Filament default | Sort order within the group |
| `navigationBadge()` | `bool\|Closure` | `false` | Show pending-retry count as a badge |
| `modelLabel()` / `pluralModelLabel()` | `string\|Closure` | `kafka topic(s)` | Record labels used across pages |
| `slug()` | `string\|Closure` | `kafka-topics` | Resource URL slug |
| `tablePollInterval()` | `string\|null\|Closure` | `10s` | Table auto-refresh interval; `null` disables |
| `modelOptions()` | `array\|Closure` | `app/Models` scan | Options for the target-model dropdown |

All options are optional — `KafkaConsumerPlugin::make()` alone keeps the defaults above.

## Typical Workflow

1. Open **Kafka Topics** in your panel.
2. Create a topic mapping: topic name, target model, upsert key, field mappings, optional relation syncs.
3. Run the consumer:

   ```bash
   php artisan gurento:kafka-consume
   ```

4. Monitor counters, health, and logs in the resource (the table auto-refreshes).
5. Re-consume failed messages from the row action or the logs relation manager when needed.

## What It Registers

- `KafkaTopicResource` — list, create, view, and edit pages
- `LogsRelationManager` — read-only consume-log browser with payload inspection and per-log retry
- Row actions: **Re-consume Failed**, **Mark Healthy**, **Reset Counters**

## Security & Access

This package ships UI classes only — it does not impose authorization. Define policies/permissions in your host app to control who can:

- edit topic mappings
- run replay actions
- inspect payload and error logs

## Troubleshooting

**Resource not visible** — ensure the plugin is registered on the panel you're viewing, then run `php artisan optimize:clear`.

**Class not found** — confirm both packages are installed and run `composer dump-autoload`.

**Customizations not applying** — plugin options are read at runtime from the current panel; make sure you configure them on the same `KafkaConsumerPlugin::make()` instance passed to `->plugins([...])`.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for release history.

## License

The MIT License (MIT). See [LICENSE](LICENSE) for details.
