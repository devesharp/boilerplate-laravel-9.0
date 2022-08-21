<?php

namespace App\Modules\Users\Models;

use App\Modules\Users\Factories\UsersFactory;
use App\Modules\Users\Presenters\UsersPresenter;
use Devesharp\Patterns\Presenter\PresentableTrait;
use Devesharp\Support\ModelGetTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
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
}
