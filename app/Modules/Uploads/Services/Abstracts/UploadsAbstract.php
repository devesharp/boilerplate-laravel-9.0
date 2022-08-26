<?php

namespace App\Modules\Uploads\Services\Abstracts;

abstract class UploadsAbstract
{
    abstract public function uploadFile($file, $name, $requester): void;
}
