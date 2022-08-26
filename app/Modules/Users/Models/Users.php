<?php

namespace App\Modules\Users\Models;

use App\Modules\Users\Factories\UsersFactory;
use App\Modules\Users\Interfaces\UsersPermissions;
use App\Modules\Users\Presenters\UsersPresenter;
use App\Modules\Users\Services\UsersAuthService;
use Devesharp\Patterns\Presenter\PresentableTrait;
use Devesharp\Support\ModelGetTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use phpDocumentor\Reflection\Types\This;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Silber\Bouncer\Conductors\GivesAbilities;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class Users extends Authenticatable implements JWTSubject
{
    use HasFactory, ModelGetTable, PresentableTrait, HasRolesAndAbilities;
    use HasRolesAndAbilities {
        allow as traitAllow;
    }

    protected string $presenter = UsersPresenter::class;

    protected $guarded = [];

    protected $dates = [
        'email_verified_at',
        'remember_token_at',
        'deleted_at',
    ];

    protected $casts = [
        'enabled' => 'bool'
    ];

    protected static function newFactory()
    {
        return UsersFactory::new();
    }

    /**
     * Sobreescreve o método para poder aceitar enum UsersPermissions
     *
     * @param $abilities
     * @param $arguments
     * @return bool
     */
    public function can($abilities, $arguments = [])
    {
        if ($abilities instanceof UsersPermissions) {
            return parent::can($abilities->name, $arguments);
        }
        return parent::can($abilities, $arguments);
    }

    /**
     * Sobreescreve o método para poder aceitar enum UsersPermissions
     *
     * @param $abilities
     * @param $arguments
     * @return bool
     */
    public function canAny($abilities, $arguments = [])
    {
        $newAbilities = [];

        foreach ($abilities as $ability) {
            if ($ability instanceof UsersPermissions) {
                $newAbilities[] = $ability->name;
            } else {
                $newAbilities[] = $ability;
            }
        }

        return parent::canAny($newAbilities, $arguments);
    }

    public function allow($ability = null, $model = null)
    {
        foreach ((array) $ability as $permission) {
            if ($permission instanceof UsersPermissions) {
                $this->traitAllow($permission->name, $model);
            } else {
                $this->traitAllow($permission, $model);
            }
        }
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
