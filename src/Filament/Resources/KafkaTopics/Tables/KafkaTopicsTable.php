<?php

namespace Gurento\KafkaConsumerFilament\Filament\Resources\KafkaTopics\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KafkaTopicsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('10s')
            ->columns([
                TextColumn::make('topic')->searchable()->sortable(),
                TextColumn::make('model_class')->label('Target Model')->formatStateUsing(fn (string $state): string => class_basename($state)),
                TextColumn::make('messages_consumed')->numeric()->sortable(),
                TextColumn::make('messages_failed')->numeric()->sortable(),
                TextColumn::make('last_consumed_at')->since()->placeholder('Never'),
                IconColumn::make('is_active')->boolean()->sortable(),
            ])
            ->filters([
                Filter::make('active')->query(fn (Builder $query) => $query->where('is_active', true))->default(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
