<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostHistory extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'post_id',
        'user_id',
        'title',
        'content'
    ];

    /**
     * User relation
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
}
