<?php

namespace App\Filament\Clusters\Product\Resources\Registration;

use App\Filament\Clusters\Product;
use App\Filament\Clusters\Product\Resources\Registration\ProductResource\Pages;
use App\Filament\Clusters\Product\Resources\Registration\ProductResource\RelationManagers;
use App\Models\Registration\Product as RegistrationProduct;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = RegistrationProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';

    protected static ?string $cluster = Product::class;

    public static function form(Form $form): Form
    {
        return $form
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Product Details')
                            ->schema([
                                Forms\Components\TextInput::make('id_product')
                                    ->required()
                                    ->readOnly()
                                    ->default(fn() => 'EVENT-' . random_int(10000, 99999))
                                    ->unique(RegistrationProduct::class, 'id_product', ignoreRecord: true),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('regtype_id')
                                    ->required()
                                    ->relationship('regtype', 'name'),
                                Forms\Components\TextInput::make('early_bird_idr')
                                    ->label('Early Bird (IDR)')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('early_bird_usd')
                                    ->label('Early Bird (USD)')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\DatePicker::make('early_bird_start')
                                    ->native(false)
                                    ->required(),
                                Forms\Components\DatePicker::make('early_bird_end')
                                    ->native(false)
                                    ->required(),
                                Forms\Components\TextInput::make('regular_idr')
                                    ->label('Regular (IDR)')
                                    ->numeric(),
                                Forms\Components\TextInput::make('regular_usd')
                                    ->label('Regular (USD)')
                                    ->numeric(),
                                Forms\Components\DatePicker::make('regular_start')
                                    ->native(false),
                                Forms\Components\DatePicker::make('regular_end')
                                    ->native(false),
                                Forms\Components\TextInput::make('on_site_idr')
                                    ->label('On Site (IDR)')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\TextInput::make('on_site_usd')
                                    ->label('On Site (USD)')
                                    ->required()
                                    ->numeric(),
                                Forms\Components\DatePicker::make('on_site_start')
                                    ->native(false)
                                    ->required(),
                                Forms\Components\DatePicker::make('on_site_end')
                                    ->native(false)
                                    ->required(),
                                Forms\Components\TextInput::make('quota')
                                    ->required()
                                    ->numeric(),
                            ])
                            ->columns(2),
                    ])->columnSpan([
                        'lg' => 2
                    ]),
                Group::make()
                    ->schema([
                        Section::make('Status')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true)
                                    ->required(),
                                Forms\Components\Toggle::make('is_early_bird')
                                    ->required(),
                                Forms\Components\Toggle::make('is_regular')
                                    ->required(),
                                Forms\Components\Toggle::make('is_on_site')
                                    ->required(),
                            ])->columns([
                                'default' => 2,
                                'md' => 2,
                            ]),
                    ])->columnSpan([
                        'lg' => 1
                    ])
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id_product')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('regtype.name')
                    ->label('Registration Type')
                    ->badge()
                    ->color(fn($record): string => $record->regtype?->regcategory?->color ?? 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('regtype.regcategory.title')
                    ->label('category')
                    ->badge()
                    ->color(fn($record): string => $record->regtype?->regcategory?->color ?? 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('early_bird_idr')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('early_bird_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('early_bird_start')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('early_bird_end')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('regular_idr')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('regular_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('regular_start')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('regular_end')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('on_site_idr')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('on_site_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('on_site_start')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('on_site_end')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quota')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active'),
                Tables\Columns\ToggleColumn::make('is_early_bird'),
                Tables\Columns\ToggleColumn::make('is_regular'),
                Tables\Columns\ToggleColumn::make('is_on_site'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
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
