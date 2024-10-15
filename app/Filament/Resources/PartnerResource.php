<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Partner;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Barryvdh\Debugbar\Facades\Debugbar;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\PartnersResource\Pages;


class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    private static function countryOptions() {
        $countries = DB::table('countries')->get();

        return $countries->mapWithKeys(function ($country) {
            $flagExists = $country->flag !== "";
            return [$country->id => $country->flag . ($flagExists ? ' ' : '') . $country->name]; // Combine flag and country name
        });
    }

    public static function form(Form $form): Form
    {
        return $form
                ->schema([
                    Group::make()->schema([

                        Section::make("Partner Details")
                            ->description("Fields with an asterisk (*) are required")
                            ->schema([
                                
                                Section::make('General Information')
                                    ->icon("heroicon-o-user")
                                    ->iconColor('black')
                                    ->iconSize('md')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('firstname')->required()->label('First Name'),
                                                TextInput::make('lastname')->required()->label('Last Name'),
                                                TextInput::make('email')->required()->email()->label('Email'),
                                                TextInput::make('tel')->label('Phone Number')->tel()->nullable(),
                                            ]),
                                        ])
                                    ->collapsible(),
                                    Section::make("Address Information")
                                        ->icon("heroicon-o-home")
                                        ->iconColor('black')
                                        ->iconSize('md')
                                        ->schema([
                                            Grid::make(2)
                                            ->schema([
                                                TextInput::make('town')->required()->label('Town'),
                                                Select::make('country')
                                                    ->extraAttributes([
                                                        'id' => 'country-select', // Set custom ID
                                                        'class' => 'custom-select-class', // Set custom class
                                                    ])
                                                    ->options(fn () => self::countryOptions())
                                                    ->searchable()
                                                    ->required()
                                                    ->native(false)
                                                    ->default(69)
                                                    ->label('Country'),

                                                TextInput::make('postal_code')
                                                    ->label('Postal Code')
                                                    ->required()
                                                    ->maxLength(8)
                                                    ->regex('/^(?:[A-Z]{1,2}\d[A-Z\d]? ?\d[A-Z]{2}|\d{4} ?\d{2}|\d{5}|\d{4})$/') // Postal code regex
                                                    ->nullable(),
                                        ])
                                    ])->collapsible()
                                ])

                        ]),

                    Group::make()->schema([
                        Section::make('Website & Media')
                            ->description("Optional Fields")
                            ->icon("heroicon-o-link")
                            ->iconColor('black')
                            ->iconSize('md')->schema([

                            TextInput::make('website')->url()->nullable()->label('Website'),

                            FileUpload::make('logo')
                            ->disk('public')
                            ->directory('logos')
                            ->downloadable()
                            ->nullable()
                            ->label('Logo')
                            ->image()
                            ->imageEditor(),

                            FileUpload::make('attachments')
                                ->disk('public')
                                ->directory('attachments')
                                ->acceptedFileTypes(['application/pdf'])
                                ->downloadable()
                                ->preserveFilenames()
                                ->panelLayout('flex')
                                ->openable()
                                ->reorderable()
                                ->previewable()
                                ->uploadingMessage('Uploading attachment...')
                                ->nullable()
                                ->label('Attachments'),

                        ])     
                    ]),

                ]);

                
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fullname')
                ->label('Όνοματεπώνυμο')
                ->sortable()
                ->searchable(),
                TextColumn::make('email')
                ->searchable(),
                TextColumn::make('tel')
                ->label('Τηλέφωνο')
                ->sortable()
                ->searchable()
                ->badge(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
