<?php

declare(strict_types=1);

namespace App\Filament\Resources\Seccions\Pages;

use App\Filament\Resources\Seccions\SeccionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use PhpParser\Node\Stmt\Label;

class ListSeccions extends ListRecords
{
    protected static string $resource = SeccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva Sección'),
        ];
    }
}
