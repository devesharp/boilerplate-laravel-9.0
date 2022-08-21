<?php

namespace App\Modules\Users\Interfaces;

enum UsersRoles: string {
    case SIMPLE = 'simple';
    case ADMIN = 'admin';
}
