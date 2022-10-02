<?php

namespace App\Modules\Users\Models;

use Devesharp\Support\ModelGetTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersAccessTokens extends Model
{
    use HasFactory, ModelGetTable;

    protected $guarded = [];
}
