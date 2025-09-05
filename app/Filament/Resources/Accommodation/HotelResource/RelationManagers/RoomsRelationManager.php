<?php

namespace App\Filament\Resources\Accommodation\HotelResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class RoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'rooms';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('room_type')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('price_idr')
                            ->required()
                            ->numeric(),
                        TextInput::make('price_usd')
                            ->numeric(),
                        FileUpload::make('image')
                            ->maxSize(3072)
                            ->helperText(new HtmlString('<small style="color:red; "><sup>*</sup><i>Max 3Mb</i></small>'))
                            ->downloadable()
                            ->reorderable()
                            ->panelLayout('grid')
                            ->image()
                            ->imageEditor()
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                    ->replace(' ', '_')
                            )
                            ->directory('Hotels'),
                        MarkdownEditor::make('description'),
                        TextInput::make('quota')
                            ->minValue(1)
                            ->numeric(),
                        TextInput::make('used_count')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->default(true)
                    ])->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('room_type')
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('room_type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_idr')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('price_usd')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('description')
                    ->limit(70),
                Tables\Columns\TextColumn::make('quota')
                    ->numeric(decimalPlaces:0),
                Tables\Columns\TextColumn::make('used_count'),
                ToggleColumn::make('is_active')
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
