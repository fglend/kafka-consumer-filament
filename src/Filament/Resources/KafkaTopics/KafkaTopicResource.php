<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics;

use Gurento\KafkaConsumerFilament\Filament\Plugins\KafkaConsumerPlugin;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Pages\CreateKafkaTopic;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Pages\EditKafkaTopic;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Pages\ListKafkaTopics;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Pages\ViewKafkaTopic;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\RelationManagers\LogsRelationManager;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Schemas\KafkaTopicForm;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Schemas\KafkaTopicInfolist;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Tables\KafkaTopicsTable;
use Gurento\KafkaConsumer\Models\KafkaConsumeLog;
use Gurento\KafkaConsumer\Models\KafkaTopic;
use BackedEnum;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class KafkaTopicResource extends Resource
{
    protected static ?string $model = KafkaTopic::class;

    protected static ?string $recordTitleAttribute = 'topic';

    protected static function plugin(): ?KafkaConsumerPlugin
    {
        return KafkaConsumerPlugin::get();
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return static::plugin()?->getNavigationIcon() ?? 'heroicon-o-arrow-down-on-square';
    }

    public static function getNavigationLabel(): string
    {
        return static::plugin()?->getNavigationLabel() ?? 'Kafka Topics';
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return static::plugin()?->getNavigationGroup() ?? 'System';
    }

    public static function getNavigationSort(): ?int
    {
        return static::plugin()?->getNavigationSort() ?? parent::getNavigationSort();
    }

    public static function getModelLabel(): string
    {
        return static::plugin()?->getModelLabel() ?? parent::getModelLabel();
    }

    public static function getPluralModelLabel(): string
    {
        return static::plugin()?->getPluralModelLabel() ?? parent::getPluralModelLabel();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return static::plugin()?->getSlug() ?? parent::getSlug($panel);
    }

    public static function getNavigationBadge(): ?string
    {
        if (! static::plugin()?->hasNavigationBadge()) {
            return null;
        }

        $pending = KafkaConsumeLog::query()
            ->where('status', 'failed')
            ->where('retryable', true)
            ->where(function (Builder $query): void {
                $query->whereNull('next_retry_at')
                    ->orWhere('next_retry_at', '<=', now());
            })
            ->whereHas('topic', fn (Builder $query) => $query->where('is_active', true))
            ->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return KafkaTopicForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KafkaTopicInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KafkaTopicsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            LogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKafkaTopics::route('/'),
            'create' => CreateKafkaTopic::route('/create'),
            'view' => ViewKafkaTopic::route('/{record}'),
            'edit' => EditKafkaTopic::route('/{record}/edit'),
        ];
    }
}
