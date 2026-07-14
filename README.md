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

### 1. Install the packages

This plugin is the UI layer for [`gurento/kafka-consumer`](https://packagist.org/packages/gurento/kafka-consumer), which does the actual consuming. Install both:

```bash
composer require gurento/kafka-consumer gurento/kafka-consumer-filament
```

### 2. Set up `gurento/kafka-consumer`

Publish the config and migrations, then migrate:

```bash
php artisan vendor:publish --tag=kafka-consumer-config
php artisan vendor:publish --tag=kafka-consumer-migrations
php artisan migrate
```

This creates two tables:

- `kafka_topics` — topic-to-model mappings, counters, and health metadata (what this plugin manages)
- `kafka_consume_logs` — per-message processing logs (what the logs viewer reads)

Review `config/kafka-consumer.php` for defaults (consumer group, retry attempts, backoff, health thresholds). The consumer itself ships with a plug-and-play engine based on `mateusjunges/laravel-kafka` — make sure the `rdkafka` PHP extension is installed and your broker settings are configured in the host app's `config/kafka.php`.

Add your Kafka connection settings to `.env`:

```dotenv
KAFKA_BROKERS=localhost:9092
KAFKA_CONSUMER_GROUP_ID=app-consumer
KAFKA_DEBUG=false
```

- `KAFKA_BROKERS` — comma-separated broker list (host:port)
- `KAFKA_CONSUMER_GROUP_ID` — default consumer group used when `--group` is not passed
- `KAFKA_DEBUG` — set to `true` to enable verbose librdkafka debug output while troubleshooting

### 3. Register the plugin

In your Filament panel provider:

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

### 4. Start consuming

Create a topic mapping in the UI, then run the consumer (as a daemon under Supervisor/systemd in production):

```bash
php artisan gurento:kafka-consume
```

See the [`gurento/kafka-consumer` README](https://github.com/gurento/kafka-consumer#readme) for command options (`--topics`, `--group`, `--from-beginning`, `--stop-on-empty`, `--reconsume-failed`), replay patterns, events, and custom engines.

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

## Creating a Topic Mapping

The create/edit form covers everything a topic needs to go from raw Kafka payload to an upserted Eloquent record — no code required.

### Topic Configuration

| Field | Description |
|---|---|
| **Kafka Topic** | The topic name to consume (e.g. `HR_APP.LIVE.office`), unique per row |
| **Target Model** | Eloquent model to upsert into — searchable dropdown scanned from `app/Models` (override via `modelOptions()`) |
| **Upsert Key** | Model column used as the unique key for `updateOrCreate` (default `id`) |
| **Exclude Payload Keys** | Top-level payload keys to strip before mapping (e.g. `old_values`, `meta`) |
| **Active** | Toggle whether the consumer processes this topic |
| **Retry settings** | Max re-consume attempts, retry backoff (seconds), and health stale threshold — per topic, falling back to config when unset |

### Field Mapping (payload matching)

A repeater that maps payload fields to model columns — each row is one `from → to` pair:

| Payload Field (`from`) | Model Column (`to`) |
|---|---|
| `uuid` | `id` |
| `name` | `name` |

Given `{"uuid": "off-001", "name": "Accounting Office"}`, the consumer writes `id = off-001`, `name = Accounting Office` and upserts by the configured upsert key. Unmapped fields are skipped, so mappings stay explicit and reviewable. Rows collapse to a readable `uuid → id` label.

### Relation Syncs (relationships)

A repeater for syncing `BelongsToMany` relationships from nested arrays in the payload:

| Field | Description |
|---|---|
| **Payload Key** | The nested array in the payload (e.g. `office_controller`) |
| **Model Relationship** | The relationship method on the target model |
| **Related Model** | The related Eloquent model (searchable dropdown) |
| **Lookup Key** | Field inside each payload item used to find the related record — auto-detects `uuid` then `id` when blank |
| **Related Model Key** | Column on the related model to match against — defaults to its primary key |

Example: with payload key `office_controller` and payload

```json
{
  "uuid": "off-001",
  "office_controller": [
    {"uuid": "emp-001"},
    {"uuid": "emp-002"}
  ]
}
```

the consumer looks up each employee by `uuid` and calls `$office->office_controller()->sync([...])`. Items whose related record doesn't exist yet are skipped.

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
