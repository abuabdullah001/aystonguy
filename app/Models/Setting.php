<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'group_name',
        'key',
        'value',
    ];

    public static function getValue(string $group, string $key, ?string $default = null): ?string
    {
        return static::query()
            ->where('group_name', $group)
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    public static function setValue(string $group, string $key, ?string $value): self
    {
        return static::query()->updateOrCreate(
            ['group_name' => $group, 'key' => $key],
            ['value' => $value]
        );
    }

    public static function getGroup(string $group): array
    {
        return static::query()
            ->where('group_name', $group)
            ->pluck('value', 'key')
            ->toArray();
    }
}
