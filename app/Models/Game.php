<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function players()
    {
        return $this->belongsToMany(User::class, "history")->withPivot(['points', 'kills']);
    }

    public function history()
    {
        return $this->hasMany(History::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'played_at' => 'date',
    ];
}
