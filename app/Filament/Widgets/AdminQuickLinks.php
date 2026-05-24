<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AdminQuickLinks extends Widget
{
    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.admin-quick-links';

    protected int | string | array $columnSpan = 'full';
}
