<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sinus extends Model
{
	use HasFactory;

    protected $table = 'sinuses';

	protected $fillable = [
		'name',
		'date_name',
	];
}
