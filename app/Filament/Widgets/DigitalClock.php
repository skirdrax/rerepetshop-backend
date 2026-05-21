<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DigitalClock extends Widget
{
    protected string $view = 'filament.widgets.digital-clock';
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 1; // sama dengan KataKata biar sejajar
}