<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Gurento\KafkaConsumer\Models\KafkaTopic;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Pages\CreateKafkaTopic;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Pages\EditKafkaTopic;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Pages\ListKafkaTopics;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Pages\ViewKafkaTopic;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\RelationManagers\LogsRelationManager;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Schemas\KafkaTopicForm;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Schemas\KafkaTopicInfolist;
use Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Tables\KafkaTopicsTable;
use UnitEnum;

class KafkaTopicResource extends Resource
{
    protected static ?string $model = KafkaTopic::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-on-square';

    protected static ?string $navigationLabel = 'Kafka Topics';

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?string $recordTitleAttribute = 'topic';

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
