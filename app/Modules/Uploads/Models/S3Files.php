<?php

namespace App\Modules\Uploads\Models;

use App\Modules\Uploads\Presenters\S3FilesPresenter;
use App\Modules\Users\Presenters\UsersPresenter;
use Devesharp\Patterns\Presenter\PresentableTrait;
use Devesharp\Support\ModelGetTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class S3Files extends Model
{
    use HasFactory, ModelGetTable, PresentableTrait;

    protected string $presenter = S3FilesPresenter::class;

    /* Fillable */
    protected $fillable = [
        'title', 'path', 'auth_by', 'size', 'user_id', 'original_name'
    ];
    /* @array $appends */
    public $appends = ['url', 'uploaded_time', 'size_in_kb'];
}
