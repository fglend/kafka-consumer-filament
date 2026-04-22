<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    protected static ?string $title = 'Consume Logs';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('consumed_at', 'desc')
            ->columns([
                TextColumn::make('consumed_at')->dateTime()->sortable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('attempt_count')->numeric(),
                TextColumn::make('upsert_key_value')->placeholder('—'),
                TextColumn::make('error')->wrap()->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'success' => 'Success',
                    'failed' => 'Failed',
                    'reconsumed_success' => 'Re-consumed Success',
                ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
