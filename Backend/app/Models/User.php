<?php

namespace App\Models;

use App\Events\UserSaving;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\MailResetPasswordToken;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'pre_name',
        'last_name',
        'profile_picture',
        'email',
        'password',
        'email_verification_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_code'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Activities
     */
    public function sendActivity($short, $details = '', $attributes = []) {
        Activity::create([
            'issuer_type' => 1, // 1 => User
            'issuer_id' => $this->id,
            'short' => $short,
            'details' => $details,
            'attributes' => json_encode($attributes)
        ]);
    }

    /**
     * Override reset password notification
     * @param string $token
     */
    public function sendPasswordResetNotification($token) {
        $this->notify(new MailResetPasswordToken($token));
    }

    /**
     * Override confirm email notification
     */
    public function sendEmailVerificationNotification() {
        $this->notify(new EmailVerificationNotification($this->email_verification_code));
    }

    /**
     * Role Relation
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Badges Relation
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges');
    }

    /**
     * User has permission
     */
    public function hasPermission(string $permission): bool {
        if ($this->role->permissions->where('name', $permission)->first()) {
            return true;
        }

        return false;
    }
}
