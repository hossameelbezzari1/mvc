<?php

namespace App\Models;

class Users extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = ['username', 'email', 'password', 'role'];
    protected $dates = ['created_at', 'updated_at', 'last_login'];
    protected $casts = ['user_id' => 'int', 'role' => 'string'];

    public function getTable()
    {
        return $this->table;
    }
}