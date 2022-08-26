<?php

namespace App\Modules\Uploads\Repositories;

use Devesharp\Patterns\Repository\RepositoryMysql;

/**
 * Class S3FilesRepository
 *
 * @method public                                      Builder getModel()
 * @method \App\Modules\Uploads\Models\S3Files findById($id, $enabled = true)
 * @method \App\Modules\Uploads\Models\S3Files findIdOrFail($id, $enabled = true)
 */
class S3FilesRepository extends RepositoryMysql
{
    /**´
     * @var string
     */
    protected $model = \App\Modules\Uploads\Models\S3Files::class;
}
