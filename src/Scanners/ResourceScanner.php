<?php

namespace Jdiassdev\LaravelTypesGen\Scanners;

use Illuminate\Support\Facades\File;

class ResourceScanner
{
    public function scan(string $path): array
    {
        if (!File::isDirectory($path)) {
            return [];
        }

        $resources = [];

        foreach (File::allFiles($path) as $file) {
            if (str_ends_with($file->getFilename(), 'Resource.php')) {
                $resources[] = $file->getRealPath();
            }
        }

        return $resources;
    }
}
