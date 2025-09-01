<?php

namespace App\Filament\Resources\UserResource\ParticpantResource\RelationManagers;

use App\Models\Registration\Participant;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';

    public function form(Form $form): Form
    {
        $countries = countries();
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('nik')
                            ->maxLength(16)
                            ->minLength(10)
                            ->numeric(),
                        Select::make('title')
                            ->native(false)
                            ->options([
                                'Prof' => 'Prof',
                                'MD' => 'MD',
                                'Mr' => 'Mr',
                                'Mrs' => 'Mrs',
                                'Ms' => 'Ms',
                            ]),
                        TextInput::make('title_specialist')
                            ->placeholder('SpU, SpBP, SpBS')
                            ->maxLength(255),
                        Select::make('speciality')
                            ->required()
                            ->native(false)
                            ->options([
                                'Specialist' => 'Specialist',
                                'Resident' => 'Resident',
                                'General Practitioner' => 'General Practitioner',
                                'Medical Student' => 'Medical Student',
                            ]),
                        TextInput::make('name_on_certificate')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('institution')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone_number')
                            ->tel()
                            ->required()
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),
                    ])->columns(2),
                Section::make()
                    ->schema([
                        Select::make('country')
                            ->required()
                            ->searchable()
                            ->options(collect($countries)->mapWithKeys(function ($country) {
                                return [$country['name'] => $country['name']];
                            })->all()),
                        TextInput::make('province')
                            ->maxLength(255),
                        TextInput::make('city')
                            ->maxLength(255),
                        TextInput::make('postal_code')
                            ->numeric(),
                        Textarea::make('address')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make()
                    ->schema([
                        Select::make('participant_type')
                            ->required()
                            ->options([
                                'Participant' => 'Participant',
                                'Faculty' => 'Faculty',
                                'Committee' => 'Committee',
                                'Moderator' => 'Moderator',
                                'Instructor' => 'Instructor',
                            ])
                            ->default('Participant')
                            ->searchable()
                            ->multiple()
                            ->native(false),
                        TextInput::make('id_participant')
                            ->default(fn() => 'EVENT-' . random_int(10000, 99999))
                            ->required()
                            ->readOnly()
                            ->unique(Participant::class, 'id_participant', ignoreRecord: true),
                    ])->columns(2)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id_participant')
            ->columns([
                Tables\Columns\TextColumn::make('id_participant'),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->getStateUsing(fn($record) => $record->first_name . ' ' . $record->last_name)
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('nik')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title_specialist')
                    ->searchable(),
                Tables\Columns\TextColumn::make('speciality')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name_on_certificate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('institution')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->sortable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
                Tables\Columns\TextColumn::make('province')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('postal_code')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->sortable(),
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
