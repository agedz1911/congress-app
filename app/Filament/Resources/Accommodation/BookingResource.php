<?php

namespace App\Filament\Resources\Accommodation;

use App\Filament\Resources\Accommodation\BookingResource\Pages;
use App\Filament\Resources\Accommodation\BookingResource\RelationManagers;
use App\Models\Accommodation\Booking;
use App\Models\Accommodation\HotelRoom;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;
    protected static ?string $navigationGroup = 'Accommodation';
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking_code'),
                TextColumn::make('hotel.name')
                    ->searchable()
                    ->description(fn(Booking $record): string => $record->room->room_type),
                TextColumn::make('participant')
                    ->searchable(query: function ($query, $search) {
                        $query->whereHas('participant', function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                    })
                    ->formatStateUsing(fn($record) => $record->participant->first_name . ' ' . $record->participant->last_name),
                TextColumn::make('participant.country'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'New' => 'info',
                        'Processing' => 'warning',
                        'Validated' => 'success',
                        'Cancelled' => 'danger',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'New' => 'heroicon-m-sparkles',
                        'Processing' => 'heroicon-m-arrow-path',
                        'Validated' => 'heroicon-m-check-circle',
                        'Cancelled' => 'heroicon-m-x-circle'
                    }),
                TextColumn::make('check_in_date')
                    ->date('d M Y'),
                TextColumn::make('check_out_date')
                    ->date('d M Y'),
                TextColumn::make('total_night'),
                TextColumn::make('subtotal')
                    ->numeric(decimalPlaces: 0),
                TextColumn::make('coupon'),
                TextColumn::make('discount')
                    ->numeric(decimalPlaces: 0),
                TextColumn::make('total')
                    ->numeric(decimalPlaces: 0),
                TextColumn::make('bookingTransaction.payment_method')
                    ->sortable()
                    ->label('Payment Method')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Bank Transfer' => 'info',
                        'Credit Card' => 'success',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Bank Transfer' => 'heroicon-m-banknotes',
                        'Credit Card' => 'heroicon-m-credit-card'
                    }),
                TextColumn::make('bookingTransaction.payment_date')
                    ->date()
                    ->label('Payment Date'),
                TextColumn::make('bookingTransaction.payment_status')
                    ->sortable()
                    ->label('Payment Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Paid' => 'success',
                        'Unpaid' => 'danger',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Paid' => 'heroicon-m-check-circle',
                        'Unpaid' => 'heroicon-m-x-circle'
                    }),
                TextColumn::make('bookingTransaction.amount')
                    ->label('Paid Amount')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('bookingTransaction.kurs')
                    ->label('Total Kurs')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\Accommodation::route('/create'),
            'edit' => Pages\AccommodationEdit::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
