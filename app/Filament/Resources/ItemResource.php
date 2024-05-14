<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique('items', 'name', ignoreRecord: true)
                    ->maxLength(30),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),   
                FileUpload::make('img')
                    ->directory('item-attachments')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ]),
                Forms\Components\TextInput::make('count')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('purchase_price')
                    ->numeric(),
                Forms\Components\TextInput::make('recommended_price')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\ImageColumn::make('img')
                        ->square()
                        ->size(120),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('name')
                            ->searchable(),
                        Tables\Columns\TextColumn::make('category.title')
                            ->limit(30)
                            ->searchable()
                            ->visibleFrom('md'),
                    ]),
                    Tables\Columns\TextColumn::make('count')
                        ->suffix(' шт.'),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('purchase_price')
                            ->money('rub'),
                        Tables\Columns\TextColumn::make('recommended_price')
                            ->money('rub'),
                    ]),

                ])

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
            'view' => Pages\ViewItem::route('/{record}'),
        ];
    }
}
