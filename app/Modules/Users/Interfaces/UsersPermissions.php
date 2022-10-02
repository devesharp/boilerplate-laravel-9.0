<?php

namespace App\Modules\Users\Interfaces;

enum UsersPermissions
{
    case USERS_CREATE;
    case USERS_UPDATE;
    case USERS_VIEW;
    case USERS_DELETE;
    case USERS_SEARCH;
}
