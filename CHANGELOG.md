# Changelog

## 1.1.1 - 2026-07-17

- Laravel 13 support: `illuminate/support` constraint widened to `^11.0|^12.0|^13.0`

## 1.1.0 - 2026-07-14

- Fluent plugin configuration: `navigationLabel()`, `navigationIcon()`, `navigationGroup()`, `navigationSort()`, `navigationBadge()`, `modelLabel()`, `pluralModelLabel()`, `slug()`, `tablePollInterval()`, `modelOptions()` — all accept values or closures
- Resource navigation icon, label, group, sort, slug, and model labels now resolve from the plugin at runtime (previous hardcoded values remain the defaults)
- Optional navigation badge showing the count of pending retryable failures
- Table poll interval configurable (or disabled) via plugin
- Target-model dropdown options can be overridden via `modelOptions()`

## 1.0.0 - 2026-04-22

- Initial Filament UI package
- Kafka topics resource, pages, table, infolist
- consume logs relation manager
- plugin-based panel registration
