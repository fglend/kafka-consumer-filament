# Changelog

## Unreleased

- Laravel 13 support: `illuminate/support` constraint widened to `^11.0|^12.0|^13.0`
- New `SECURITY.md` with supported versions, private vulnerability reporting, and operational security notes
- LICENSE file added
- README: Plumb score badges; requirements table updated to Laravel 11 / 12 / 13

## 1.2.3 - 2026-07-14

- README: topic mapping configuration details, including field mapping and relation syncs

## 1.2.2 - 2026-07-14

- README: expanded installation instructions with detailed setup steps, configuration options, and usage guidelines

## 1.2.1 - 2026-07-14

- README: detailed features, installation instructions, and customization options

## 1.2.0 - 2026-07-14

- Fluent plugin configuration: `navigationLabel()`, `navigationIcon()`, `navigationGroup()`, `navigationSort()`, `navigationBadge()`, `modelLabel()`, `pluralModelLabel()`, `slug()`, `tablePollInterval()`, `modelOptions()` — all accept values or closures
- Resource navigation icon, label, group, sort, slug, and model labels now resolve from the plugin at runtime (previous hardcoded values remain the defaults)
- Optional navigation badge showing the count of pending retryable failures
- Table poll interval configurable (or disabled) via plugin
- Target-model dropdown options can be overridden via `modelOptions()`

## 1.0.1 - 2026-04-22

- Detailed topic management features and improved consume logging in the UI

## 1.0.0 - 2026-04-22

- Initial Filament UI package
- Kafka topics resource, pages, table, infolist
- consume logs relation manager
- plugin-based panel registration
