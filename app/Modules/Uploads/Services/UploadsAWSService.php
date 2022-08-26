<?php

namespace App\Modules\Uploads\Services;

use Illuminate\Support\Facades\Storage;

class UploadsAWSService
{
    protected \Aws\S3\S3Client $s3;

    public function __construct(protected $disk = 's3')
    {
        $this->s3 = new \Aws\S3\S3Client([
            'region' => config('filesystems.disks.'.$disk.'.region'),
            'version' => 'latest',
            'credentials' => [
                'key'    => config('filesystems.disks.'.$disk.'.key'),
                'secret' => config('filesystems.disks.'.$disk.'.secret'),
            ]
        ]);
    }

    public function uploadPublicFile(string $key, $filename, string $contentType)
    {
        $uploaded = Storage::disk('s3')->put($key, file_get_contents($filename));

        if (!$uploaded) {
            throw new \Exception('Erro ao fazer upload do arquivo');
        }

        return [
            'key' => $key,
            'url' => config('filesystems.disks.'.$this->disk.'.url') . $key,
        ];
    }
}
