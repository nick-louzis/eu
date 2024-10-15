<?php

namespace App\Filament\Resources\PostResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\AttachAction;
use Filament\Resources\RelationManagers\RelationManager;

class PartnersRelationManager extends RelationManager
{
    protected static string $relationship = 'partners';
    protected static ?string $title = 'Συνεργάτες';
    public static function label(): string
    {
        return __('Συνεργάτες');
    }

    
    public static function pluralLabel(): string
    {
        return __('Partners');
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('order')->numeric(),
            ]);
    }


    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute("fullname")
            ->columns([
                Tables\Columns\TextColumn::make('fullname')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('tel')->sortable()->badge()->searchable(),
                Tables\Columns\TextColumn::make('order')->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                Tables\Actions\AttachAction::make()
                ->preloadRecordSelect()
                ->recordSelectSearchColumns(['firstname', 'lastname', 'fullname', 'email', 'tel'])
                ->form(fn (AttachAction $action): array => [
                    $action->getRecordSelect()->multiple()->searchable(),
                    // Select::make('partners')
                    //         ->relationship('partners') 
                    //         ->searchable()
                    //         ->multiple()
                    //         ->preload(),
                    // Forms\Components\TextInput::make('order')->numeric()->required(),
                ]),
                
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make()
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
