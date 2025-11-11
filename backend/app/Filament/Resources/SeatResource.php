<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeatResource\Pages;
use App\Filament\Resources\SeatResource\RelationManagers;
use App\Models\Seat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SeatResource extends Resource
{
    protected static ?string $model = Seat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('hall_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('seat_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('row')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('column')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->suffix('Eur')
                    ->mutateStateUsing(fn ($state) => $state ? $state / 100 : null)
                    ->mutateDehydratedStateUsing(fn ($state) => $state ? (int) ($state * 100) : null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hall_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('seat_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('row')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('column')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => number_format($state / 100, 2) . ' Eur')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeats::route('/'),
            'create' => Pages\CreateSeat::route('/create'),
            'edit' => Pages\EditSeat::route('/{record}/edit'),
        ];
    }
}
