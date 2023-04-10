<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifyUser extends Model
{
    use HasFactory;
    use HasFactory;
    protected $fillable = [
        'token',
        'user_id',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
