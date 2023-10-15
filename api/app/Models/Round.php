<?php

namespace App\Models;

use App\Enums\MatchResultEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Round extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];

    protected $casts = [
        'date' => 'datetime',
        'result' => MatchResultEnum::class,
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function stadium(): BelongsTo
    {
        return $this->belongsTo(Stadium::class);
    }

    public function homeClub(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function awayClub(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}
