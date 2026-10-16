<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait UploadPdfTrait
{
    /**
     * Save uploaded PDF file to the specified location.
     *
     * @param \Illuminate\Http\UploadedFile $uploadedFile
     * @param string $uploadLocation
     * @return string
     */
    public function savePdfFile($uploadedFile, $uploadLocation)
    {
        // Generate a unique file name with the current timestamp
        $fileName = 'file_' . time() . '.pdf';

        // Ensure the directory exists
        if (!is_dir($uploadLocation)) {
            mkdir($uploadLocation, 0755, true);
        }

        // Store the file in the desired location
        $uploadedFile->move($uploadLocation, $fileName);
        $lastModified = $uploadLocation . DIRECTORY_SEPARATOR  . $fileName;
        // Return the properly formatted path
        return $lastModified;
    }

}
