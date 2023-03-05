<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sinus extends Model
{
	use HasFactory, SoftDeletes;

    protected $table = 'sinuses';

	protected $fillable = [
		'name',
		'user_id',
		'date_name',
		'archived',
		'avatar',
	];
}
