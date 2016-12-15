<?php
namespace Train;

use Imagick, ImagickPixel;

class TrainNumbers
{
    private $sets;
    
    public function __construct()
    {
        $this->sets = glob(__DIR__ . "/data/cropped/[0-9]/*.png");
        shuffle($this->sets);

        $this->train();
    }

    private function train()
    {
        $oneImage = current($this->sets);
        
        $image = new Imagick($oneImage);

        $iterator = $image->getPixelIterator();

        $colors = [];
        foreach ($iterator as $pixels) {
            /** @var ImagickPixel $pixel */
            foreach ($pixels as $pixel) {
                $color = $pixel->getColor();

                $colors[] = $color['r'] + $color['b'] + $color['g'] > 700 ? 1 : 0;
            }
        }

        var_dump(count($colors));


    }
}

new TrainNumbers;