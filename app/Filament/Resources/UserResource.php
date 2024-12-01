<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Table;
use App\Http\Livewire\InlineEditUser; // Import the Livewire component
use Livewire\WithFileUploads;

class UserResource extends Resource
{
    use WithFileUploads;

    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Manage Users';
    protected static ?string $slug = 'users';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('User Information')
                ->description('Basic details of the user')
                ->schema([
                    FileUpload::make('avatar')
                        ->image()
                        ->avatar()
                        ->live()
                        ->columnSpanFull(),
                    TextInput::make('name')
                        ->required()
                        ->live()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->required()
                        ->email()
                        ->live()
                        ->maxLength(255),
                    TextInput::make('password')
                        ->password()
                        ->minLength(8)
                        ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                        ->dehydrated(fn ($state) => filled($state))
                        ->hiddenOn('edit'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->circular()
                    ->label('Profile'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Name'),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Email'),
               
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Joined At'),
            ])
            ->filters([
                // Future filter options
            ])
            ->actions([
                EditAction::make()
                    ->modal()
                    ->modalWidth('lg')
                    ->modalHeading('Edit User')
                    ->icon('heroicon-o-pencil-square')
                    ->action(function ($record) {
                        // Trigger Livewire component to edit the user
                        return view('livewire.inline-edit-user', ['userId' => $record->id]);
                    }),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->tooltip('Delete User')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
