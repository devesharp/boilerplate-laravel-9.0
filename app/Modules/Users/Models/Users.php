<?php

namespace App\Modules\Users\Models;

use App\Modules\Users\Factories\UsersFactory;
use App\Modules\Users\Presenters\UsersPresenter;
use App\Modules\Users\Services\UsersAuthService;
use Devesharp\Patterns\Presenter\PresentableTrait;
use Devesharp\Support\ModelGetTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Users extends Authenticatable implements JWTSubject
{
    use HasFactory, ModelGetTable, PresentableTrait;

    protected string $presenter = UsersPresenter::class;

    protected $guarded = [];

    protected $casts = [
        'enabled' => 'bool'
    ];

    protected static function newFactory()
    {
        return UsersFactory::new();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            't' => app(UsersAuthService::class)->createTokenForUser($this)
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
}
