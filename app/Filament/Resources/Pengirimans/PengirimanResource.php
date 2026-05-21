<?php

namespace App\Filament\Resources\Pengirimans;

use App\Filament\Resources\Pengirimans\Pages\CreatePengiriman;
use App\Filament\Resources\Pengirimans\Pages\EditPengiriman;
use App\Filament\Resources\Pengirimans\Pages\ListPengiriman;
use App\Filament\Resources\Pengirimans\Pages\ViewPengiriman;
use App\Filament\Resources\Pengirimans\Schemas\PengirimanForm;
use App\Filament\Resources\Pengirimans\Schemas\PengirimanInfolist;
use App\Filament\Resources\Pengirimans\Tables\PengirimanTable;
use App\Models\Pengiriman;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PengirimanResource extends Resource
{
    protected static ?string $model = Pengiriman::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $recordTitleAttribute = 'resi';

    protected static ?string $navigationLabel = 'Pengiriman';
    protected static ?string $modelLabel = 'Pengiriman';
    protected static ?string $pluralModelLabel = 'Pengiriman';
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Toko';

    public static function form(Schema $schema): Schema
    {
        return PengirimanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PengirimanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PengirimanTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPengiriman::route('/'),
            'create' => CreatePengiriman::route('/create'),
            'view' => ViewPengiriman::route('/{record}'),
            'edit' => EditPengiriman::route('/{record}/edit'),
        ];
    }
}