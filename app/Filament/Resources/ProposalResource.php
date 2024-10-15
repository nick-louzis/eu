<?php

namespace App\Filament\Resources;

use Log;
use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Partner;
use Filament\Forms\Get;
use App\Models\Category;
use App\Models\Proposal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Http\Request;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Columns\DateColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MultiSelect;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Forms\Components\MorphToSelect;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\MultiSelectFilter;
use App\Filament\Resources\ProposalResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\BelongsToManyMultiSelect;
use App\Filament\Resources\ProposalResource\RelationManagers;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use App\Filament\Resources\ProposalResource\RelationManagers\PartnersRelationManager;

class ProposalResource extends Resource
{
    protected static ?string $model = Proposal::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make()->schema([
                        TextInput::make('title')
                        ->label('Τίτλος')
                        ->required()
                        ->autofocus()
                        ->maxLength(100),

                        DatePicker::make('submission_date')
                        ->label('Ημερομηνία Υποβολής')
                        ->required()
                        ->displayFormat('Y-m-d')
                        ->default(now()),

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
                    
                        Toggle::make('approved')
                        ->label('Εγκρίθηκε')
                        ->default(false),
                    ])
                ]),
                
                Group::make()->schema([
                    Section::make()->schema([
                        Textarea::make('description')
                        ->label('Περιγραφή')
                        ->maxLength(400)
                        ->reactive()
                        ->extraAttributes([
                            'x-data' => '{ charCount: 0 }',
                            'x-ref' => 'description',
                            'x-init' => 'if ($refs.description) { charCount = $refs.description?.querySelector("textarea").value?.length; document.getElementById("charCount").textContent = charCount; }',
                            'x-on:input' => 'if ($refs.description) { charCount = $refs.description?.querySelector("textarea").value?.length; document.getElementById("charCount").textContent = charCount; }',
                        ])
                        ->hint(fn() => new HtmlString('<span id="charCount">0</span> / 400 χαρακτήρες')),

                        Select::make('categories')
                        ->relationship('categories', 'name')
                        ->label('Call')
                        ->placeholder('Διάλεξε Call')
                        ->multiple() 
                        ->searchable()
                        ->preload(10),
                        
                        FileUpload::make('attachments')
                        ->label('Ανέβαστε Αρχεία')
                        ->hint('PDF (.pdf) ή Word (.doc | .docx | .dot | .dotx | .odt | .rtf)')
                        ->multiple() // Allow multiple file uploads
                        ->nullable() // Field can be empty
                        ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                        ->maxSize(3000)
                        ->multiple()
                        ->panelLayout('grid')
                        ->downloadable()
                        ->preserveFilenames(),

                    ])
                ])
            ]);
    }


    

    public static function table(Table $table): Table
    {
        
        return $table
        ->columns([
            // Title Column
            TextColumn::make('title')
                ->label('Title')
                ->sortable()
                ->searchable(),

           TextColumn::make('submission_date')->date(),

           TextColumn::make('partners')
                ->label('Partners')
                ->formatStateUsing(function ($record) {
                    return $record->partners->count(); 
            }),

            // IconColumn::make('approved')
            //     ->icon(fn (bool $state): string => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
            //     ->color(fn (bool $state): string => $state ? 'success' : 'danger')
            //     ->tooltip(fn (bool $state) => $state ? 'Approved' : 'Not Approved'),
    
            ToggleColumn::make('approved')->label('Approved')
            ->disabled(function ($record) {
                // userCanEdit is a public method of the Proposal model
                ['isAdmin' => $isAdmin, 'isCordinator' => $isCoordinator] = $record->userCanEdit();
        
                // Disable the toggle unless the user is either admin (isAdmin <= 2) or the coordinator
                return !($isAdmin || $isCoordinator);
            })
            ->extraAttributes(function ($record) {
                ['isAdmin' => $isAdmin, 'isCordinator' => $isCoordinator] = $record->userCanEdit();
        
                $tooltipMessage = '';
                if (!$isAdmin && !$isCoordinator) {
                    $tooltipMessage = 'Μόνο Διαχειριστής ή Συντοντιστής μπορεί να αλλάξει αυτό το πεδίο.';
                } elseif ($isCoordinator) {
                    $tooltipMessage = 'Είστε ο Συντονιστής και μπορείτε να εναλλάσσετε αυτό το πεδίο.';
                } elseif ($isAdmin) {
                    $tooltipMessage = 'Είστε Διαχειριστής με άδεια να εναλλάσσετε αυτό το πεδίο';
                }
        
                return ['title' => $tooltipMessage]; // Tooltip content
            }),
            
            // TextColumn::make('description')
            //     ->label('Description'),
    
            
            TextColumn::make('coordinatorable')
                ->label('Coordinator')
                ->formatStateUsing(function ($record) {
                    // Format the coordinator name based on its value (User or Partner)
                    $isUser = strpos($record->coordinatorable_type, 'User');

                    $displayName = $isUser ? $record->coordinatorable->name : $record->coordinatorable->firstname . " " . $record->coordinatorable->lastname;
                    if ($record->userCanEdit()['isCordinator']) {
                        $displayName .= ' (You)';
                    }
                    return $displayName;
                })
                ->badge(),

           
            
        ])
        ->filters([
            SelectFilter::make('categories')
                ->relationship('categories', 'name')
                ->multiple()
                ->label("Επιλογή CALL")
                ->searchable()
                ->preload(10),
            // SelectFilter::make('submission_date')
            //     ->label('Ημερομηνία Υποβολής')
            //     ->options([
            //         'upcoming' => 'Προσεχώς (Προθεσμία Σήμερα ή Αργότερα)',
            //         'today' => 'Σήμερα',
            //         'past_due' => 'Προθεσμίες που έχουν λήξει',
            //     ])
            //     ->query(function (Builder $query, $state) {
            //         $value = $state['value'];
            //         if ($value === 'upcoming') {
            //             // Filter for submissions due today or in the future
            //             return $query->where('submission_date', '>=', Carbon::today());
            //         } elseif ($value === 'past_due') {
            //             // Filter for submissions that are past due
            //             return $query->where('submission_date', '<', Carbon::today());
            //         } elseif ($value === 'today') {
            //             return $query->whereDate('submission_date', Carbon::today());
            //         } else return $query;    
            // }),
            DateRangeFilter::make('submission_date')->label("Ημερομηνία Υποβολής")->useRangeLabels(),
            // Filter::make('approved')
            //     ->query(fn (Builder $query): Builder => $query->where('approved', true)),
            SelectFilter::make('approved')
            ->label('Κατάσταση Έγκρισης')
            ->options([
                1 => 'Έχει Εγκριθεί', 
                0 => 'Δεν Έχει Εγκριθεί',
            ]),
        
            SelectFilter::make('coordinatorable')
                ->label('Συντονιστής')
                ->options(function () {
                // Fetch options from both User and Partner models
                    $userOptions = User::query()
                        ->selectRaw('id, name AS label, "App\\Models\\User" as type')
                        ->get()
                        ->mapWithKeys(fn ($user) => ["User-{$user->id}" => "{$user->label}"]);

                    $partnerOptions = Partner::query()
                        ->selectRaw('id, CONCAT(firstname, " ", lastname) AS label, "App\\Models\\Partner" as type')
                        ->get()
                        ->mapWithKeys(fn ($partner) => ["Partner-{$partner->id}" => "{$partner->label}"]);

                    // Merge the options from both User and Partner
                    return $userOptions->merge($partnerOptions)->toArray();
                })
                ->query(function (Builder $query, array $state) {
                    // Ensure $state['value'] exists and contains the expected delimiter
                    if (!isset($state['value']) || strpos($state['value'], '-') === false) {
                        return;
                    }

                    
                    [$type, $id] = explode('-', $state['value']);

                    // Apply the filter based on the type and ID, only if both parts are valid
                    if ($type && $id) {
                        // Check the correct namespace for the type (User or Partner)
                        $fullType = $type === 'User' ? 'App\\Models\\User' : ($type === 'Partner' ? 'App\\Models\\Partner' : null);

                        // If the type doesn't match either User or Partner, return null
                        if (!$fullType) {
                            return;
                        }

                        // Apply the query for the polymorphic relationship
                        $query->where('coordinatorable_type', $fullType)
                            ->where('coordinatorable_id', $id);
                    }
                
            }),

            
            Filter::make('coordinator')
                ->label('Είσαι Συντονηστής')
                ->query(function ($query) {
                    $user = auth()->user();
                    // Filter the records to only show where the current user is the coordinator
                    return $query->where('coordinatorable_id', $user->id)
                                ->where('coordinatorable_type', 'App\Models\User');
            })
            
        ], layout: FiltersLayout::AboveContent)
        // ->filtersFormSchema(fn (array $filters): array => [
        //     Section::make()
        //         ->schema([
        //             $filters['categories'],
        //             $filters['submission_date'],
        //             $filters['approved'],
        //     ])->columns(3),
        //     Section::make()
        //         ->schema([
        //             $filters['coordinator'],
        //             $filters['coordinatorable'],
        //     ])->columns(4)
        // ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(function ($record) {
                    // Destructure the returned array from userCanEdit()
                    ['isAdmin' => $isAdmin, 'isCordinator' => $isCoordinator] = $record->userCanEdit();
        
                    // Make the Edit action visible only if the user is an admin (isAdmin <= 2) or the coordinator
                    return $isAdmin || $isCoordinator;
                })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(fn () => auth()->user()->isAdmin),
                ]),
            ]);
    }

    // public function apply(Request $request): Builder
    // {
    //     $query = parent::apply($request);

    //     $categoryId = $request->get('category');
    //     if ($categoryId) {
    //         $query->whereHas('category_proposals', function ($query) use ($categoryId) {
    //             $query->where('category_id', $categoryId);
    //         });
    //     }

    //     return $query;
    // }

    public static function getRelations(): array
    {
        return [
            PartnersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProposals::route('/'),
            'create' => Pages\CreateProposal::route('/create'),
            'edit' => Pages\EditProposal::route('/{record}/edit'),
        ];
    }
}
