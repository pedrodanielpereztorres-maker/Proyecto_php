<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Panel principal';

    protected static ?int $navigationSort = -2;

    public function getColumns(): int | array
    {
        return [
            'sm' => 1,
            'xl' => 2,
            '2xl' => 3,
        ];
    }
}
