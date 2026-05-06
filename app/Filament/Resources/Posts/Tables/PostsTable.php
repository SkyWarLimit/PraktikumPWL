<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Checkbox;
use Filament\Actions\Action;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->toggleable(),
                TextColumn::make('title')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
                TextColumn::make('slug')
                ->sortable()
                ->toggleable()
                ->searchable(),
                TextColumn::make('category.name')
                ->sortable()
                ->toggleable()
                ->searchable(),
                ColorColumn::make('color')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('image')
                    ->disk('public')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('published')
                    ->boolean()
                    ->label('Published')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('tags')
                    ->label('Tags')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])-> defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('created_at')
                    ->label('Creation Date')
                        ->schema([
                            DatePicker::make('created_at')
                                ->label('Select Date :'),
                        ])
                    
                    ->query(function ($query, $data) {
                        return $query
                        ->when(
                            $data['created_at'],
                            fn ($query, $date) => $query->whereDate('created_at', $date)
                        );
                    }),
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category')
                    ->preload(),
            ])
            ->recordActions([
                ReplicateAction::make()
                ->icon('heroicon-o-document-duplicate'),
                EditAction::make()
                    ->icon('heroicon-o-pencil'),
                DeleteAction::make()
                    ->icon('heroicon-o-trash'),
                Action::make('status')
                    ->label('status change')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->color('success')
                    ->schema([
                        Checkbox::make('published')
                            ->label('Published')
                            ->default(fn ($record): bool => $record->published),
                    ])
                    ->action(function ($record, $data) {
                        $record->update(['published' => $data['published']]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
