<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
<<<<<<< HEAD
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int    $id
 * @property string $name
 * @property string $email
 * @property string $role
 * @property string $phone
 * @property string $avatar
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
=======
use Laravel\Sanctum\HasApiTokens; // 👈 add this

class User extends Authenticatable
{
    use HasApiTokens, Notifiable; // 👈 add HasApiTokens here
>>>>>>> 1a966354809047339de1b44f686874e08c54a24e

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'avatar'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }
<<<<<<< HEAD

    public function devices()
    {
        return $this->hasMany(Device::class);
    }
=======
>>>>>>> 1a966354809047339de1b44f686874e08c54a24e
}