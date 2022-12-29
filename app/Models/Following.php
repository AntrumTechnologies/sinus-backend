<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Following extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'following';

	protected $fillable = [
		'user_id',
		'following_user_id',
	];
}
