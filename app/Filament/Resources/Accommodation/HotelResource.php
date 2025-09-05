<?php

namespace App\Filament\Resources\Accommodation;

use App\Filament\Resources\Accommodation\HotelResource\Pages;
use App\Filament\Resources\Accommodation\HotelResource\RelationManagers;
use App\Filament\Resources\Accommodation\HotelResource\RelationManagers\RoomsRelationManager;
use App\Models\Accommodation\Hotel;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;
    protected static ?string $navigationGroup = 'Accommodation';
    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Hotel')
                    ->schema([
                        TextInput::make('hotel_code')
                            ->default('H-' . random_int(10000, 99999))
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(20)
                            ->unique(Hotel::class, 'hotel_code', ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('hotel_star')
                            ->maxValue(5)
                            ->minValue(0)
                            ->numeric(),
                        TextInput::make('distance')
                            ->maxLength(255),
                        MarkdownEditor::make('description')
                            ->columnSpanFull(),
                        FileUpload::make('feature_image')
                            ->required()
                            ->maxSize(10240)
                            ->helperText(new HtmlString('<small style="color:red; "><sup>*</sup><i>Max 3Mb</i></small>'))
                            ->downloadable()
                            ->reorderable()
                            ->panelLayout('grid')
                            ->image()
                            ->imageEditor()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                    ->replace('', '_')
                            )
                            ->directory('Hotels'),
                        FileUpload::make('galleries')
                            ->helperText(new HtmlString('<small style="color:red; "><sup>*</sup><i>Max 3Mb</i></small>'))
                            ->maxSize(3072)
                            ->downloadable()
                            ->reorderable()
                            ->panelLayout('grid')
                            ->image()
                            ->imageEditor()
                            ->multiple()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                    ->replace(' ', '_')
                            )
                            ->directory('Hotels'),
                        Toggle::make('is_active')
                            ->default(true)
                            ->required(),
                    ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hotel_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hotel_star')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('distance')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('feature_image'),
                Tables\Columns\ImageColumn::make('galleries')
                    ->stacked()
                    ->circular(),
                Tables\Columns\TextColumn::make('rooms.room_type')
                    ->label('Room Type')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('rooms.price_idr')
                    ->label('Price IDR')
                    ->numeric(decimalPlaces: 0)
                    ->searchable(),
                Tables\Columns\TextColumn::make('rooms.price_usd')
                    ->label('Price USD')
                    ->numeric(decimalPlaces: 0)
                    ->searchable(),
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
            RoomsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
            'edit' => Pages\EditHotel::route('/{record}/edit'),
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
