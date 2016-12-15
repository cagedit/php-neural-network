<?php
namespace Train;

use Exception, Imagick;

class ImageCroppingTool
{
    /** @var Imagick */
    private $image;

    /** @var String */
    private $exportDir;

    /** @var int */
    private $height;

    /** @var int */
    private $width;

    /** @var int */
    private $stepHeight;

    /** @var int */
    private $stepWidth;

    public function __construct(String $file, String $exportDir, int $stepHeight, int $stepWidth)
    {
        if (!file_exists($file)) {
            throw new Exception("Training data file [{$file}] does not exist.");
        }

        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0777, true);
        }

        $this->image = new Imagick($file);
        $this->exportDir = $exportDir;

        $this->height = $this->image->getImageHeight();
        $this->width = $this->image->getImageWidth();

        $this->stepHeight = $stepHeight;
        $this->stepWidth = $stepWidth;
    }

    public function saveAllSets()
    {
        $stepHeight = 28;
        $stepWidth = 28;

        $index = $y = $x = 0;
        while(true) {
            $image = $this->getImage();

            $image->cropImage($stepWidth, $stepHeight, $x, $y);

            ++$index;
            file_put_contents("{$this->exportDir}/set_{$index}.png", $image->getImageBlob());

            $x += $stepWidth;

            if ($x >= $this->width) {
                $y += $stepHeight;
                $x = 0;
            }

            if ($y >= $this->height) {
                break;
            }
        }
    }

    private function getImage()
    {
        return clone $this->image;
    }
}