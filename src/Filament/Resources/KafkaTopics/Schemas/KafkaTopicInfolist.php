<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KafkaTopicInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Topic Details')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('topic')->copyable(),
                        TextEntry::make('model_class')->formatStateUsing(fn (string $state): string => class_basename($state)),
                        TextEntry::make('upsert_key')->badge(),
                        IconEntry::make('is_active')->boolean(),
                    ]),
                    TextEntry::make('description')->placeholder('—')->columnSpanFull(),
                ]),
            Section::make('Consumption Stats')
                ->schema([
                    Grid::make(4)->schema([
                        TextEntry::make('messages_consumed')->numeric(),
                        TextEntry::make('messages_failed')->numeric(),
                        TextEntry::make('messages_reconsumed')->numeric(),
                        TextEntry::make('last_consumed_at')->dateTime()->placeholder('Never'),
                    ]),
                    TextEntry::make('consumer_last_error')->placeholder('None')->columnSpanFull(),
                ]),
        ]);
    }
}
