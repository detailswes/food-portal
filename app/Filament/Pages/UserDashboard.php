<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\EditAction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\TextInputColumn;

class UserDashboard extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'User';
    protected static ?string $title = 'Manage Users (Advanced)';
    protected static string $view = 'filament.pages.user-dashboard';

    public function table(Table $table): Table
    {
        return $table
            ->query(User::where('id', '!=', auth()->id()))
            ->columns([
                ImageColumn::make('avatar')
                    ->circular()
                    ->label('Avatar'),
                TextInputColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->updateStateUsing(function (User $record, string $state) {
                        $record->update(['name' => $state]);
                        Notification::make()
                            ->title('User Updated')
                            ->body('The name has been updated successfully.')
                            ->success()
                            ->send();
                    }),
                TextInputColumn::make('email')
                    ->sortable()
                    ->searchable()
                    ->updateStateUsing(function (User $record, string $state) {
                        $record->update(['email' => $state]);
                        Notification::make()
                            ->title('User Updated')
                            ->body('The email has been updated successfully.')
                            ->success()
                            ->send();
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Joined'),
            ])
            ->headerActions([
                Action::make('Create User')
                    ->button()
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Create User')
                    ->form([
                        FileUpload::make('avatar')
                            ->image()
                            ->avatar()
                            ->directory('avatars'),
                        TextInput::make('name')->required(),
                        TextInput::make('email')->email()->required(),
                        TextInput::make('password')
                            ->password()
                            ->minLength(8)
                            ->required()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                    ])
                    ->action(fn (array $data) => $this->createUser($data)),
            ])
            ->actions([
                Action::make('Edit')
                    ->icon('heroicon-o-pencil')
                    ->form(fn (User $record) => [
                        FileUpload::make('avatar')
                            ->image()
                            ->avatar()
                            ->directory('avatars'),
                        TextInput::make('name')->default($record->name)->required(),
                        TextInput::make('email')->email()->default($record->email)->required(),
                        TextInput::make('password')
                            ->password()
                            ->minLength(8)
                            ->hidden()
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                    ])
                    ->action(fn (User $record, array $data) => $this->updateUser($record, $data))
                    ->modalHeading('Edit User'),
                
                Action::make('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (User $record) => $this->deleteUser($record)),
            ])
            ->bulkActions([
                BulkAction::make('Delete Selected')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($records) => $this->bulkDelete($records)),
            ])
            ->paginated(false) // Ensure full width
            ->defaultSort('created_at', 'desc');
    }
    

    protected function createUser(array $data)
    {
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'avatar' => $data['avatar'] ?? null,
        ]);

        Notification::make()
            ->title('User Added')
            ->success()
            ->send();

        $this->refreshTable();
    }

    protected function updateUser(User $record, array $data)
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (isset($data['avatar'])) {
            $updateData['avatar'] = $data['avatar'];
        }

        if (!empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        $record->update($updateData);
        Notification::make()->title('User Updated')->success()->send();
        $this->refreshTable();
    }

    protected function deleteUser(User $record)
    {
        $record->delete();
        Notification::make()->title('User Deleted')->danger()->send();
        $this->refreshTable();
    }

    protected function bulkDelete($records)
    {
        $records->each->delete();
        Notification::make()->title('Selected Users Deleted')->danger()->send();
        $this->refreshTable();
    }

    protected function refreshTable()
    {
        $this->dispatch('refreshTable');
    }
}