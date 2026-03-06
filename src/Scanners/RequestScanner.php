<?php

namespace Jdiassdev\LaravelTypesGen\Scanners;

use Illuminate\Support\Facades\File;

class RequestScanner
{
    public function scan(string $path): array
    {
        $files = File::allFiles($path);

        $requests = [];

        foreach ($files as $file) {
            if (str_ends_with($file->getFilename(), 'Request.php')) {
                $requests[] = $file->getRealPath();
            }
        }

        return $requests;
    }
}
