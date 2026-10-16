<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

trait UploadImageTrait
{
    // ================================================================
    // ================= Save File In Folder Function =================
    // ================================================================
    // A new one with image resize (now when any image is uploaded, it will be resized to 100x35)
    // $orginal_image = the image file
    // $upload_location = the location where the image will be saved
    // $maxWidth = the maximum width of the image
    // $maxHeight = the maximum height of the image
    // $last_image = the final image name with path
    // $img_ext = the image extension
    // $img_name = the image name
    // $name_gen = a unique name for the image
    // $originalWidth = the original width of the image
    // $originalHeight = the original height of the image
    // $newWidth = the new width of the image
    // $newHeight = the new height of the image
    // $ratio = the ratio of the original width to the original height
    // $newImage = the new image resource
    // $source = the source image resource
    function saveFile($orginal_image, $upload_location, $maxWidth = 100, $maxHeight = 35)
    {
        // Create directory if it doesn't exist
        if (! file_exists($upload_location)) {
            mkdir($upload_location, 0755, true);
        }

        $name_gen   = hexdec(uniqid());
        $img_ext    = strtolower($orginal_image->getClientOriginalExtension());
        $img_name   = $name_gen . '.' . $img_ext;
        $last_image = $upload_location . $img_name;

        // SVG is a vector format - getimagesize()/GD can't read or resize
        // it, so just copy the file through as-is like saveVideoFile() does.
        if ($img_ext === 'svg') {
            $orginal_image->move($upload_location, $img_name);

            return $last_image;
        }

        // Get image details
        list($originalWidth, $originalHeight) = getimagesize($orginal_image->getPathname());

        // Calculate new dimensions
        $ratio     = $originalWidth / $originalHeight;
        $newWidth  = $maxWidth;
        $newHeight = $maxHeight ?: $maxWidth / $ratio;

        if ($maxHeight && ! $maxWidth) {
            $newHeight = $maxHeight;
            $newWidth  = $maxHeight * $ratio;
        }

        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Handle different image types
        switch ($img_ext) {
            case 'jpeg':
            case 'jpg':
                $source = imagecreatefromjpeg($orginal_image->getPathname());
                break;
            case 'png':
                $source = imagecreatefrompng($orginal_image->getPathname());
                break;
            case 'gif':
                $source = imagecreatefromgif($orginal_image->getPathname());
                break;
            default:
                throw new \Exception("Unsupported image type");
        }

        // Resize and save
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        switch ($img_ext) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($newImage, $last_image);
                break;
            case 'png':
                imagepng($newImage, $last_image);
                break;
            case 'gif':
                imagegif($newImage, $last_image);
                break;
        }

        imagedestroy($newImage);
        imagedestroy($source);

        return $last_image;
    }

    // function saveFile($orginal_image, $upload_location)
    // {

    //     $name_gen = hexdec(uniqid());
    //     $img_ext = strtolower($orginal_image->getClientOriginalExtension());
    //     $img_name = $name_gen . '.' . $img_ext;
    //     $last_image = $upload_location . $img_name;
    //     $orginal_image->move($upload_location, $img_name);

    //     // $file_extension = $orginal_image->getClientOriginalExtension();
    //     // $file_name = rand() . '-' . time() . '.' . $file_extension;
    //     // $path = $upload_location;
    //     // $orginal_image->move($path, $file_name);

    //     return $last_image;
    // }

    // ================================================================
    // ============ save File With Original Name Function =============
    // ================================================================
    function saveFileWithOriginalName($table_name, $table_column, $orginal_image, $original_name, $upload_location)
    {
        if (! file_exists($upload_location)) {
            File::makeDirectory($upload_location, $mode = 0755, true, true);
        }

        $img_ext_firstsearch = $orginal_image->getClientOriginalExtension();
        $img_ext_tosearch    = '.' . $img_ext_firstsearch;
        $img_search_name     = str_replace($img_ext_tosearch, '', $original_name);

        $check_old = DB::table($table_name)->where($table_column, 'like', '%' . $img_search_name . '%')->get();
        $counter   = $check_old->count();

        if ($counter > 0) {
            $img_name = $img_search_name . '(' . $counter . ')';
        } else {
            $img_name = $img_search_name;
        }

        $file_type = exif_imagetype($orginal_image);
        switch ($file_type) {
            case '1': //IMAGETYPE_GIF
                $imagejpg = imagecreatefromgif($orginal_image);
                break;
            case '2': //IMAGETYPE_JPEG
                $imagejpg = imagecreatefromjpeg($orginal_image);
                break;
            case '3': //IMAGETYPE_PNG
                $imagejpg = imagecreatefrompng($orginal_image);
                imagepalettetotruecolor($imagejpg);
                imagealphablending($imagejpg, true);
                imagesavealpha($imagejpg, true);
                break;
            case '6': // IMAGETYPE_BMP
                $imagejpg = imagecreatefrombmp($orginal_image);
                break;
            case '15': //IMAGETYPE_Webp

                $imagejpg = imagecreatefromwebp($orginal_image);
                break;
            case '16': //IMAGETYPE_XBM
                $imagejpg = imagecreatefromxbm($orginal_image);
                break;
            default:

                $file_base_64 = base64_encode(file_get_contents($orginal_image->path()));
                $file_decoded = base64_decode($file_base_64);
                $imagejpg     = imagecreatefromstring($file_decoded);
        }

        $file_name = $img_name;
        $image     = imagewebp($imagejpg, $upload_location . $file_name . '.webp');

        return $upload_location . $file_name . '.webp';
    }

    // ================================================================
    // ================= Save Video File Function ======================
    // ================================================================
    // Plain move, no image processing - saveFile() above resizes with
    // GD functions (getimagesize/imagecreatetruecolor/...) which only
    // work on actual images and error out on video files.
    function saveVideoFile($orginal_video, $upload_location)
    {
        if (! is_dir($upload_location)) {
            mkdir($upload_location, 0755, true);
        }

        $fileName = 'file_' . time() . '.' . $orginal_video->getClientOriginalExtension();
        $orginal_video->move($upload_location, $fileName);

        return $upload_location . $fileName;
    }
}
