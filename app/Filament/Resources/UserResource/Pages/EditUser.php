<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->name = $data['name'];
        $record->email = $data['email'];
        // $record->organization = $data['organization'];
        // $record->telephone = $data['telephone'];
        $record->isAdmin = $data['isAdmin'] ?? 0;
        
        if (!empty($data['password'])) {
            // $record->password = Hash::make($data['password']);
            $record->password = $data['password'];
        }

        $record->save();
        Log::info('Updating user', ['id' => $record->id, 'data' => $data]);
        return $record;
    }
}
