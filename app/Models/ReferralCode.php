<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReferralCode extends Model
{
    protected $fillable = ['user_id', 'code'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateForUser(User $user): self
    {
        $base = strtoupper(substr(preg_replace('/[^A-Z0-9]/', '', strtoupper($user->name)), 0, 5));
        if (strlen($base) < 3) $base = 'USER';
        do {
            $code = $base . '-' . strtoupper(Str::random(4));
        } while (static::where('code', $code)->exists());

        return static::create(['user_id' => $user->id, 'code' => $code]);
    }
}
