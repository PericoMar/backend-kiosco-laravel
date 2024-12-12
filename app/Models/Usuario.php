<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Usuario extends Model
{
    // Specify table name if different from the plural of the model name
    protected $table = 'Usuarios';

    // Define fillable fields for mass assignment
    protected $fillable = ['usuario', 'password', 'nombre', 'rol', 'cliente_id'];

    // Hide sensitive attributes when the model is serialized
    protected $hidden = ['password'];

    /**
     * Check if provided credentials match a user record.
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public static function validateCredentials($username, $password)
    {
        $user = self::where('usuario', $username)->first();
        
        if ($user && Hash::check($password, $user->password)) {
            return true;
        }
        
        return false;
    }
}
