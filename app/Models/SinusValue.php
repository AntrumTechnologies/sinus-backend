<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class SinusValue extends Model
{
	use HasFactory, SoftDeletes, Notifiable;

    protected $table = 'sinusvalues';
	protected $fcm_tokens = [];

	protected $fillable = [
		'sinus_id',
		'date',
		'value',
		'latitude',
		'longitude',
		'tags',
		'description',
	];

	public function updateFcmTokens($tokens)
	{
		$this->fcm_tokens = $tokens;
		return;
	}

	public function getFcmTokens()
	{
		return $this->fcm_tokens;
	}

	/**
	 * Specifies the user's FCM token
	 *
	 * @return string|array
	 */
	public function routeNotificationForFcm()
	{
		Log::debug("Sending notification to devices with FCM token(s): ". implode(", ", $this->fcm_tokens), ['user_id' => Auth::id()]);
		return $this->fcm_tokens;
	}
}
