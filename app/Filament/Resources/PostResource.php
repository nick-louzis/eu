<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use App\Models\User;
use Filament\Tables;
use App\Models\Partner;
use Filament\Forms\Set;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Support\Markdown;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use PHPUnit\Framework\Constraint\IsEmpty;
use Filament\Forms\Components\ColorPicker;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Forms\Components\MorphToSelect;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\PostResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResource\RelationManagers\PartnersRelationManager;


class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $modelLabel = 'Project';

    private static function getIconState(string $state, array $iconArray): string
    {
        return $iconArray[$state];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Project Information')
                ->description("Manage your project")
                ->schema([
                    TextInput::make('title')->rules('min:5')->required()->afterStateUpdated(function(string $operation, Set $set, ?string $state) {
                        if($operation === "create") {
                            $set('slug', Str::slug($state));
                        }
                    })->live(onBlur: true),     
                    Select::make('category_id')->label('Category')
                    ->relationship('category','name')->searchable()->nullable()->dehydrateStateUsing(fn($state) => $state ?? 0)->preload(), // essentially choose 'uncategorized' if you dont select a category yourself,
                    // TextInput::make('description')->rules(['min:5','max:100'])->columnSpanFull()->required(),
                    Textarea::make('description')
                        ->label('Περιγραφή')
                        ->maxLength(100)
                        ->reactive()
                        ->extraAttributes([
                            'x-data' => '{ charCount: 0 }',
                            'x-ref' => 'description',
                            'x-init' => 'if ($refs.description) { charCount = $refs.description?.querySelector("textarea").value?.length; document.getElementById("charCount").textContent = charCount; }',
                            'x-on:input' => 'if ($refs.description) { charCount = $refs.description?.querySelector("textarea").value?.length; document.getElementById("charCount").textContent = charCount; }',
                        ])
                        ->hint(fn() => new HtmlString('<span id="charCount">0</span> / 100 χαρακτήρες'))->columnSpanFull(),            
                    // Select::make('users_id')
                    // ->label('Users')
                    // ->multiple()
                    // ->options(User::all()->pluck('name','id')),
                    MorphToSelect::make('coordinatorable')
                    ->label('Συντονιστής')
                    ->types([
                        MorphToSelect\Type::make(User::class)->titleAttribute('name')->searchColumns(['name', 'email'])->label('User'),
                        MorphToSelect\Type::make(Partner::class)->label('Partner')
                        ->searchColumns(['firstname', 'lastname','email', 'tel'])
                        ->getOptionLabelFromRecordUsing(fn (Partner $record) => "{$record->firstname} {$record->lastname}")
                    ])
                    ->searchable()
                    ->preload()
                    ->required(),

                    Select::make('partners')
                            ->relationship('partners') 
                            ->label('Συνεργάτες')
                            ->searchable()
                            ->multiple()
                            ->getSearchResultsUsing(function (string $search): array {
                                return Partner::query()
                                    ->where(fn($query) => $query->where('fullname', 'like', "%{$search}%"))
                                    ->limit(5) // Limit results for performance
                                    ->get()
                                    ->mapWithKeys(fn ($partner) =>[$partner->id => $partner->fullname])
                                    ->toArray();
                            })
                            ->getOptionLabelFromRecordUsing(fn (Partner $record) => $record->fullname)
                            ->hiddenOn('edit'),

                    
                    
                            
                        RichEditor::make('content')->ColumnSpanFull()
                ])->collapsible()
                ->columnSpan(2)->columns(2),
                
                Group::make()->schema([
                    Section::make('Meta')
                    ->description('')
                    ->schema([
                        Checkbox::make('published')->label('Finished'),
                        ColorPicker::make('color')->default('#ffffff'),
                        TextInput::make('slug')->rules('max:15')->unique(ignoreRecord: true),
                        TagsInput::make('tags')->suggestions([
                            'tag-me',
                            'kwstas_tag',
                            'very tag n taggy',
                            'livewire',
                            'these auto suggested tags are TagsInput property'
                        ])
                    ])->collapsible()->columnSpan(1),
                    // Section::make('Project Members')
                    // ->description('Select members for this project')
                    // ->schema([
                    //     Select::make('Project Members')
                    //     ->relationship('users','name')
                    //     ->searchable()
                    //     ->multiple()->label('Select from list')
                    // ])

                    // FileUpload::make('thumbnail')->disk('public')->directory('thumbnails')->downloadable(),
                    Section::make('Media')
                    ->schema([
                        FileUpload::make('thumbnail')
                        ->label('Upload Project Thumbnail')
                        ->directory('thumbnails')
                        ->hint('Image formats (e.g., JPG, PNG, GIF)')
                        ->nullable()
                        ->maxSize(2048)
                        ->image()
                        ->imageEditor(),
                        
                        FileUpload::make('attachments')
                        ->disk('public')
                        ->directory('attachments')
                        ->acceptedFileTypes(['application/pdf'])
                        ->multiple()
                        ->downloadable()
                        ->preserveFilenames()
                        ->panelLayout('flex')
                        ->openable()
                        ->reorderable()
                        ->previewable()
                        ->uploadingMessage('Uploading attachment...')->nullable()
                    ])
                ])
                
            ])->columns(3);
    }
                
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')->label("")->circular()->size(50),
                TextColumn::make('title')->sortable()->searchable(),
                TextInputColumn::make('description')
                // ->disabled(fn($record) => !(
                //     $record->coordinatorable_type === 'App\Models\User' 
                //     && $record->coordinatorable_id === auth()->user()->id
                // ) && auth()->user()->isAdmin == 0)
                ->placeholder('Εισάγετε σύντομη περιγραφή...')
                ->rules(['max:100']),
                TextColumn::make('category.name')->label('Category')->sortable()->searchable(),
                TextColumn::make('slug')->toggleable()->searchable(), 
                TextColumn::make('tags'),
                ColorColumn::make('color'),
                TextColumn::make('created_at')->label('Date Created')->date('n/j/Y')->sortable(),
                IconColumn::make('published')
                ->icon(fn (string $state): string => self::getIconState($state, ['0' => '',
                    '1' => 'heroicon-o-check-circle',]))
                ->color('primary')
                ->sortable()
                ->label("Finished"),
                
                // TextInput::make('attachments')
                //     ->label('Download Attachments')
                //     ->url()
    
            ])
            ->filters([
                Filter::make('Finished Projects')->query(
                    function (Builder $query): Builder {
                        return $query->where('published', true);
                    }
                ),
                SelectFilter::make('category_id')
                    ->label('Category')
                    // ->options(Category::all()->pluck('name','id'))
                    ->relationship('category','name')
                    ->preload(1)
                    ->multiple(),
            ])
            ->actions([
                //
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
            PartnersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
