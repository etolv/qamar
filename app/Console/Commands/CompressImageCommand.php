<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Spatie\Image\Image;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CompressImageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:compress-image-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Product::chunk(5, function ($products) {
            foreach ($products as $product) {
                $this->info($product->id);
                continue;
            }
        });
        $directory = 'public/image'; // Define the directory
        $files = Storage::files($directory); // Get all files in the directory

        foreach ($files as $file) {
            $filePath = storage_path('app/' . $file); // Get the full path to the file
            $this->info($filePath); // Display file path in the console

            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION)); // Get the file extension

            // Check if the file is a valid image (jpg, jpeg, png, gif)
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $this->info("Compressing " . $file);

                // Compress based on the image type
                $compressed = $this->compressImage($filePath, $filePath, 25); // Compress with 25% quality

                if ($compressed) {
                    $this->info("Compressed successfully: " . $filePath);
                } else {
                    $this->error("Failed to compress: " . $filePath);
                }
            }
        }
    }

    private function compressImage($source, $destination, $quality)
    {
        try {
            $info = getimagesize($source); // Get image information (mime type)
            $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION)); // Get file extension

            // If the destination is not .jpg or .jpeg, change it to .jpg
            if (!in_array($extension, ['jpg', 'jpeg'])) {
                $destination = preg_replace('/\.[^.]+$/', '.jpg', $destination);
            }

            // Convert and compress the image based on its MIME type
            if ($info['mime'] == 'image/jpeg') {
                // Compress JPEG images
                $image = imagecreatefromjpeg($source);
                imagejpeg($image, $destination, $quality); // Compress with given quality
            } elseif ($info['mime'] == 'image/gif') {
                // Convert GIF to JPEG
                $image = imagecreatefromgif($source);
                imagejpeg($image, $destination, $quality); // Save as JPEG with compression
            } elseif ($info['mime'] == 'image/png') {
                // Convert PNG to JPEG (lossy compression)
                $image = imagecreatefrompng($source);
                $background = imagecreatetruecolor(imagesx($image), imagesy($image));

                // Set the background to white for images with transparency
                $white = imagecolorallocate($background, 255, 255, 255);
                imagefill($background, 0, 0, $white);
                imagecopy($background, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

                imagejpeg($background, $destination, $quality); // Compress and save as JPEG

                // Free memory and destroy the temporary background
                imagedestroy($background);

                // After successfully compressing the image, delete the original PNG
                if (file_exists($source)) {
                    unlink($source); // Delete the original PNG image
                }
            }

            // Free memory
            imagedestroy($image);

            return true; // Return true if compression/conversion is successful
        } catch (\Exception $e) {
            return false; // Return false if there's any error
        }
    }


    private function compressImageV2($source, $destination, $quality)
    {
        try {
            $info = getimagesize($source); // Get image information (mime type)
            $extension = strtolower(pathinfo($destination, PATHINFO_EXTENSION));

            // If the destination is not .jpg or .jpeg, change it to .jpg
            if (!in_array($extension, ['jpg', 'jpeg'])) {
                $destination = preg_replace('/\.[^.]+$/', '.jpg', $destination);
            }

            // Convert and compress the image based on its MIME type
            if ($info['mime'] == 'image/jpeg') {
                // Compress JPEG images
                $image = imagecreatefromjpeg($source);
                imagejpeg($image, $destination, $quality); // Compress with given quality
            } elseif ($info['mime'] == 'image/gif') {
                // Convert GIF to JPEG
                $image = imagecreatefromgif($source);
                imagejpeg($image, $destination, $quality); // Save as JPEG with compression
            } elseif ($info['mime'] == 'image/png') {
                // Convert PNG to JPEG (lossy compression)
                $image = imagecreatefrompng($source);
                $background = imagecreatetruecolor(imagesx($image), imagesy($image));

                // Set the background to white for images with transparency
                $white = imagecolorallocate($background, 255, 255, 255);
                imagefill($background, 0, 0, $white);
                imagecopy($background, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

                imagejpeg($background, $destination, $quality); // Compress and save as JPEG
            }

            // Free up memory
            imagedestroy($image);
            if (isset($background)) {
                imagedestroy($background);
            }

            return true; // Return true if compression/conversion is successful
        } catch (\Exception $e) {
            return false; // Return false if there's any error
        }
    }

    // function compressImage($source, $destination, $quality)
    // {
    //     try {

    //         $info = getimagesize($source);

    //         if ($info['mime'] == 'image/jpeg')
    //             $image = imagecreatefromjpeg($source);

    //         elseif ($info['mime'] == 'image/gif')
    //             $image = imagecreatefromgif($source);

    //         elseif ($info['mime'] == 'image/png')
    //             $image = imagecreatefrompng($source);

    //         imagejpeg($image, $destination, $quality);

    //         return $destination;
    //     } catch (\Exception $e) {
    //         return false;
    //     }
    // }
}
