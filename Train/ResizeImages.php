<?php
namespace Train;

use Imagick;
class ResizeImages
{
    public function __construct()
    {
        $this->resize();
    }

    public function resize()
    {
        $files = glob(__DIR__ . "/data/cropped/[0-9]/*.png");

        foreach ($files as $file) {
            echo "Resizing {$file}\n";

            $image = new Imagick($file);

            $image->resizeImage(10, 10, 0, 0);

            $image->writeImage($file);
        }
    }
}

new ResizeImages;