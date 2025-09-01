<?php

namespace App\Filament\Resources\Registration;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\RegStatus;
use App\Filament\Resources\OrderResource\Widgets\OrderStats as WidgetsOrderStats;
use App\Filament\Resources\Registration\OrderResource\Pages;
use App\Filament\Resources\Registration\OrderResource\RelationManagers;
use App\Filament\Resources\Registration\OrderResource\Schemas\Pricing;
use App\Filament\Resources\Registration\OrderResource\Widgets\OrderStats;
use App\Widgets\StatsOverview;
use App\Models\Registration\Order;
use App\Models\Registration\Participant;
use App\Models\Registration\Product;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use stdClass;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Registration';
    protected static ?string $navigationLabel = 'Registration Orders';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([])->live();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->state(
                        static function (HasTable $livewire, stdClass $rowLoop): string {
                            return (string) (
                                $rowLoop->iteration +
                                ($livewire->getTableRecordsPerPage() * (
                                    $livewire->getTablePage() - 1
                                ))
                            );
                        }
                    ),
                TextColumn::make('reg_code')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('participant')
                    ->label('Full Name')
                    ->getStateUsing(fn($record) => $record->participant->first_name . ' ' . $record->participant->last_name)
                    ->sortable()
                    ->searchable(['participant.first_name', 'participant.last_name']),
                TextColumn::make('participant.country')
                    ->label('Country')
                    ->sortable()
                    ->searchable(),

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
                TextColumn::make('items.product.name')
                    ->label('Product'),
                // ->tooltip(fn($record) => $record->items->pluck('product.name')->join(', ')),
                TextColumn::make('items.quantity')
                    ->label('Qty'),
                TextColumn::make('items.unit_price')
                    ->label('Unit Price')
                    ->numeric(decimalPlaces: 0),
                TextColumn::make('total')
                    ->numeric(decimalPlaces: 0),
                TextColumn::make('transaction.payment_method')
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
                TextColumn::make('transaction.payment_date')
                    ->date()
                    ->label('Payment Date'),
                TextColumn::make('transaction.payment_status')
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
                TextColumn::make('transaction.amount')
                    ->label('Paid Amount')
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),
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

    public static function getWidgets(): array
    {
        return [
            WidgetsOrderStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            // 'create' => Pages\CreateOrder::route('/create'),
            'create' => Pages\Registration::route('/create'),
            'edit' => Pages\RegistrationEdit::route('/{record}/edit'),
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
