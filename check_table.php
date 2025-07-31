<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $columns = \DB::select('DESCRIBE user_locations');
    echo "Table structure:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field}: {$column->Type}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 