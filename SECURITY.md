# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |
| < 1.0   | :x:                |

Only the latest `1.x` release receives security fixes. Please upgrade to the most recent version before reporting an issue.

## Reporting a Vulnerability

If you discover a security vulnerability in `gurento/kafka-consumer-filament`, please report it privately. **Do not open a public GitHub issue.**

- Email: **gdferrer@up.edu.ph**
- Subject line: `[SECURITY] kafka-consumer-filament — <short description>`

Please include:

- A description of the vulnerability and its impact
- Steps to reproduce (a minimal example is ideal)
- The package version and PHP/Laravel/Filament versions affected
- Any suggested fix, if you have one

You can expect an acknowledgement within **72 hours**.

If the issue lies in the underlying consumer package rather than this UI package, please report it against `gurento/kafka-consumer` instead — when in doubt, report here and it will be routed.

## Disclosure Process

1. Your report is acknowledged and triaged.
2. A fix is developed and validated privately.
3. A patched release is published to Packagist, with the fix noted in the [CHANGELOG](CHANGELOG.md).
4. After the release, the vulnerability may be disclosed publicly. Reporters are credited unless they prefer to remain anonymous.

## Operational Security Notes

This package exposes Kafka topic configuration and consume logs inside a Filament admin panel. When deploying:

- Restrict panel access with Filament's authorization (policies/guards) — see the **Security & Access** section of the [README](README.md).
- Consume logs can contain raw message payloads; limit who can view the resource.
- Keep `KAFKA_BROKERS` and related credentials in your environment (`.env`), never in committed config.
