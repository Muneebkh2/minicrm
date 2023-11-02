<?php

namespace App\Service;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class Media
{
    private const STORAGE_PATH = 'companies/logo';

    public function upload($file)
    {
        $storedFile = Storage::disk('public')->putFile(self::STORAGE_PATH, $file);

        if ($storedFile !== false) {
            return $storedFile;
        } else {
            throw new Exception('uploading file Failed!');
        }
    }


    public function delete($fileName): bool
    {
        return Storage::disk('public')->delete(self::STORAGE_PATH.$fileName);
    }
}
