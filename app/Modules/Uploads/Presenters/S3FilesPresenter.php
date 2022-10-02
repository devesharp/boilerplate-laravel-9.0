<?php

namespace App\Modules\Uploads\Presenters;

use Devesharp\Patterns\Presenter\Presenter;
use Illuminate\Support\Facades\Storage;

class S3FilesPresenter extends Presenter
{
    public function urlAttribute()
    {
        return Storage::disk('s3')->url($this->path);
    }

    public function uploadedTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function sizeInKbAttribute()
    {
        return round($this->size / 1024, 2);
    }
}
