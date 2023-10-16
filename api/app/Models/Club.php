<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];

    public function seasons(): BelongsToMany
    {
        return $this->belongsToMany(Season::class);
    }

    public function homeRounds(): HasMany
    {
        return $this->hasMany(Round::class, 'home_club_id');
    }

    public function awayRounds(): HasMany
    {
        return $this->hasMany(Round::class, 'away_club_id');
    }
}
