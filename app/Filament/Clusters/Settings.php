<?php

namespace App\Filament\Clusters;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Clusters\Cluster;

class Settings extends Cluster
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
}
