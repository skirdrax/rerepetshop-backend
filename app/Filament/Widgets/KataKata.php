<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class KataKata extends Widget
{
    protected string $view = 'filament.widgets.katakata';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return false;
    }
}