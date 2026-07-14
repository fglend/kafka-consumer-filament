<?php

namespace Gurento\KafkaConsumerFilament\Filament\Plugins;

use BackedEnum;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\KafkaTopicResource;
use UnitEnum;

class KafkaConsumerPlugin implements Plugin
{
    use EvaluatesClosures;

    protected string|Closure|null $navigationLabel = null;

    protected string|BackedEnum|Closure|null $navigationIcon = null;

    protected string|UnitEnum|Closure|null $navigationGroup = null;

    protected int|Closure|null $navigationSort = null;

    protected bool|Closure $navigationBadge = false;

    protected string|Closure|null $modelLabel = null;

    protected string|Closure|null $pluralModelLabel = null;

    protected string|Closure|null $slug = null;

    protected string|Closure|null $tablePollInterval = '10s';

    protected array|Closure|null $modelOptions = null;

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

    /**
     * Resolve the registered plugin instance from the current panel, if any.
     */
    public static function get(): ?static
    {
        if (! function_exists('filament')) {
            return null;
        }

        $panel = filament()->getCurrentOrDefaultPanel();

        if (! $panel || ! $panel->hasPlugin('kafka-consumer')) {
            return null;
        }

        /** @var static */
        return $panel->getPlugin('kafka-consumer');
    }

    public function navigationLabel(string|Closure|null $label): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function getNavigationLabel(): ?string
    {
        return $this->evaluate($this->navigationLabel);
    }

    public function navigationIcon(string|BackedEnum|Closure|null $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function getNavigationIcon(): string|BackedEnum|null
    {
        return $this->evaluate($this->navigationIcon);
    }

    public function navigationGroup(string|UnitEnum|Closure|null $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): string|UnitEnum|null
    {
        return $this->evaluate($this->navigationGroup);
    }

    public function navigationSort(int|Closure|null $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function getNavigationSort(): ?int
    {
        return $this->evaluate($this->navigationSort);
    }

    /**
     * Show a navigation badge with the number of pending retries across topics.
     */
    public function navigationBadge(bool|Closure $condition = true): static
    {
        $this->navigationBadge = $condition;

        return $this;
    }

    public function hasNavigationBadge(): bool
    {
        return (bool) $this->evaluate($this->navigationBadge);
    }

    public function modelLabel(string|Closure|null $label): static
    {
        $this->modelLabel = $label;

        return $this;
    }

    public function getModelLabel(): ?string
    {
        return $this->evaluate($this->modelLabel);
    }

    public function pluralModelLabel(string|Closure|null $label): static
    {
        $this->pluralModelLabel = $label;

        return $this;
    }

    public function getPluralModelLabel(): ?string
    {
        return $this->evaluate($this->pluralModelLabel);
    }

    public function slug(string|Closure|null $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->evaluate($this->slug);
    }

    /**
     * Table auto-refresh interval (e.g. '10s'). Pass null to disable polling.
     */
    public function tablePollInterval(string|Closure|null $interval): static
    {
        $this->tablePollInterval = $interval;

        return $this;
    }

    public function getTablePollInterval(): ?string
    {
        return $this->evaluate($this->tablePollInterval);
    }

    /**
     * Override the target-model dropdown options (class => label).
     */
    public function modelOptions(array|Closure|null $options): static
    {
        $this->modelOptions = $options;

        return $this;
    }

    public function getModelOptions(): ?array
    {
        return $this->evaluate($this->modelOptions);
    }
}
