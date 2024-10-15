<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Pages\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';
    protected static ?string $title = 'Projects';
    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
            ->description('Edit Project Information')
            ->schema([
            TextInput::make('title')->rules('min:5')->required(),
            TextInput::make('slug')->rules('max:8')->required(),
            ColorPicker::make('color')->required(),       
                        
            Select::make('users_id')
            ->label('Users')
            ->multiple()
            ->options(User::all()->pluck('name','id')),
            MarkdownEditor::make('content')->ColumnSpanFull()
            ])->collapsible()
            ->columnSpan(2)->columns(2),
            
            Section::make('Meta')
                ->description('')
                ->schema([
                    Checkbox::make('published'),
                    TagsInput::make('tags')
                ])->collapsible()->columnSpan(1),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\CheckBoxColumn::make('published'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('New Project'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
