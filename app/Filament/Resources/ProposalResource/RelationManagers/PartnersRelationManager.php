<?php

namespace App\Filament\Resources\ProposalResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\AttachAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PartnersRelationManager extends RelationManager
{
    protected static string $relationship = 'partners';

    public static function label(): string
    {
        return __('Συνεργάτες');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('assignments')->label("Αναθέσεις")->placeholder("Γράψε μια σύντομη περιγραφη του ρόλου και των αναθέσεων του συνεργάτη")->columnSpanFull(),
                TextInput::make('order')->numeric()->columnSpan(1),
            ]);
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\CreateAction::make()->modal(),
            Tables\Actions\EditAction::make()->modal(),
            Tables\Actions\DeleteAction::make(),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')->required(),
            // Other fields...
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('fullname')
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
                Tables\Actions\AttachAction::make()
                ->preloadRecordSelect()
                ->recordSelectSearchColumns(['firstname', 'lastname', 'fullname', 'email', 'tel'])
                ->form(fn (AttachAction $action): array => [
                    $action->getRecordSelect()->multiple()->searchable()->preload(),
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
