<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReputationVote extends Model
{
    use HasFactory;

    protected $fillable = ['voter_id', 'user_id', 'vote'];

    public function voter()
    {
        return $this->belongsTo(User::class, 'voter_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
