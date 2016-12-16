<?php
namespace Train;

use Imagick, ImagickPixel, Net, Logging\Logger;

class TrainNumbers implements TrainInterface
{
    /** @var array */
    private $sets;

    /** @var Net */
    private $network;

    public function __construct()
    {
        $this->loadTrainingFileList();
    }

    /**
     * @param Imagick $image
     * @return array
     */
    private function getInputsForImage($image)
    {
        $iterator = $image->getPixelIterator();

        $colors = [];
        foreach ($iterator as $pixels) {
            /** @var ImagickPixel $pixel */
            foreach ($pixels as $pixel) {
                $color = $pixel->getColor();

                $colors[] = $color['r'] + $color['b'] + $color['g'] > 700 ? 1 : 2;
            }
        }

        return $colors;

    }

    private function loadRandomFrom(int $num)
    {
        $randomFile = array_pop($this->sets[$num]);

        if (!file_exists($randomFile)) {
            return false;
        }

        return new Imagick($randomFile);
    }

    public function train(): TrainInterface
    {
        Logger::debug("Beginning to train the network!");

        $pass = $success = 0;
        while (true) {
            foreach (range(3, 4) as $target) {
                $image = $this->loadRandomFrom($target);
                $target = 1 / $target;


                if ($image === false) {
                    continue;
                }
                $inputs = $this->getInputsForImage($image);

                Logger::debug("Pass #" . ++$pass);
                $targetOutput = [$target];


                $this->network->feedForward($inputs);
                $output = $this->network->getResults();

                Logger::debug("Actual Output: " . json_encode($output));

                $this->network->backProp($targetOutput);

                Logger::debug("Net current error: {$this->network->getError()}");
                Logger::debug("Net recent average error: {$this->network->getRecentAvgError()}");

                if ($this->network->getRecentAvgError() < 0.01) {
                    $success++;
                } else $success = 0;
                if ($pass > 10000 || $success >= 3) {
                    Logger::debug("Error has dropped to an acceptable level. Training is complete!");
                    break 2;
                }
            }
        }

        return $this;
    }

    public function setNetwork(Net $network): TrainInterface
    {
        $this->network = $network;
        return $this;
    }

    public function getTopology(): array
    {
        return [100, 10, 1];
    }

    private function loadTrainingFileList()
    {
        foreach (range(1, 9) as $i) {
            $set = glob(__DIR__ . "/data/cropped/[{$i}]/*.png");
            shuffle($set);
            $this->sets[$i] = $set;
        }
    }
}

new TrainNumbers;