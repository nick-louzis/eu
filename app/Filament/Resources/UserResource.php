<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Barryvdh\Debugbar\Facades\Debugbar;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\CheckboxColumn;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $modelLabel = 'Users';
    
    public static function form(Form $form): Form
    {   
        // $isCreate = $form->getOperation() === "create";
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Account Credentials')->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('email')->required()->email(),
                
                    TextInput::make('password')
                        ->password()
                        ->label('Password')
                        ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser) // Required only on create
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? \Hash::make($state) : null),
                    ])->collapsible()
                ]),
                

                
                Group::make()->schema([
                    Section::make('Διαχείριση Ρόλων Χρήστη')
                    
                    ->schema([
                        Select::make('isAdmin')
                            ->label('Επιλογή Ρόλου')
                            ->options([
                                0 => 'Κανονικός Χρήστης',
                                1 => 'Admin', 
                            ])
                            ->default(0)
                            ->required()
                    ])->visible(fn () => auth()->user()->isAdmin)->reactive(),
                    Section::make('Additional Informaton')->schema([
                        FileUpload::make('thumbnail')->disk('public')->directory('thumbnails')->downloadable()->image()->avatar()
                        ->circleCropper()
                        ->imageEditor()
                        ->label('Εικόνα Προφίλ'),
                        FileUpload::make('attachments')->disk('public')->directory('attachments')->acceptedFileTypes(['application/pdf'])
                    ->multiple()->downloadable()->preserveFilenames()->panelLayout('flex')->openable()->reorderable()->previewable()
                    ->uploadingMessage('Uploading attachment...')->nullable()
                    ])->collapsible()              
                ])
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('id')->label('Order'),
                TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->extraAttributes(fn ($record) => ['class' => $record->isAdmin == 1 ? 'isAdmin' : '']),
                TextColumn::make('email')->searchable()->sortable(),
                IconColumn::make('isAdmin')
                ->icon(fn (string $state): string => match ($state) {
                    '0' => '',
                    '1' => 'heroicon-o-check-circle',
                })->color("success")
                    ->label('Admin'),
                TextColumn::make('organization')->label('Organization'),
                TextColumn::make('telephone')->label('Phone Number')
                
            ])
            ->filters([
                SelectFilter::make('isAdmin')
                    ->label('By Role')
                    ->options([
                        true => 'Admin',
                        false => 'Regular Users',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                ])->icon('heroicon-m-ellipsis-horizontal')
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(fn () => auth()->user()->isAdmin),
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
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
  
}
