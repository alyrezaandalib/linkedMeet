<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppVersion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'platform',
        'version',
        'is_mandatory',
        'release_notes',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
    ];

    public static function hasMandatoryUpdate(string $platform, string $currentVersion): bool
    {
        return self::where('is_mandatory', true)
            ->where('platform', $platform)->get()
            ->contains(function ($version) use ($currentVersion) {
                return version_compare($version->version, $currentVersion, '>');
            });
    }
}
