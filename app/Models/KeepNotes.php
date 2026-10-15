<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeepNotes extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Fields the API may set. user_id is NOT here: it always comes from the
     * signed-in user, never from the request.
     */
    protected $fillable = [
        'title',
        'content',
        'reminder',
        'archive',
        'trash',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
