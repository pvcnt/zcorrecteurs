<?php

namespace Zco\Bundle\FileBundle\Util;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class BatchUploadResult
{
    public $failed = [];
    public $success = [];
    public $total;

    /**
     * Constructor.
     *
     * @param int $total
     */
    public function __construct(int $total)
    {
        $this->total = $total;
    }

    public function addFailed(UploadedFile $uploadedFile, string $message)
    {
        $this->failed[] = [
            'name' => $uploadedFile->getClientOriginalName(),
            'message' => $message,
        ];
    }

    public function addSuccess(UploadedFile $uploadedFile, $id)
    {
        $this->success[] = [
            'name' => $uploadedFile->getClientOriginalName(),
            'id' => $id,
        ];
    }
}