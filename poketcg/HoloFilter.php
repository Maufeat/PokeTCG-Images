<?php

namespace PokeTCG;

use Imagine\Filter\Transformation;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Imagine\Image\BoxInterface;
use Imagine\Imagick\Imagine;

class HoloFilter implements \Imagine\Filter\FilterInterface {

    private $cardFactory;
    private $holoPath;

    public function __construct(CardFactory $cardFactory, string $holoPath)
    {
        $this->cardFactory = $cardFactory;
        $this->holoPath = $holoPath;
    }

    public function apply(ImageInterface $card){
        $size = $card->getSize();
        $canvas = new Box($size->getWidth(), $size->getHeight());
        $palette = new \Imagine\Image\Palette\RGB();

        $holoGlow = $this->getHoloEffect($size);

        $holo = new Imagine();
        $holoInterface = $holo->open($this->holoPath);
        $holoInterface->layers()->coalesce();
        $holoDiff = new Imagine();
        $holoDiffMask = $holoDiff->open("F:\\xampp\\htdocs\\sparkles_diff.gif");
        $holoDiffMask->layers()->coalesce();

        $i = 0;
        foreach ($holoInterface->layers() as $frame) {

            $tempImagine = new Imagine();

            $offset = $i / count($holoInterface->layers());
            if($offset == 0)
                $offset = 0.01; 
                
            $frame->resize($canvas);
            
            //Sparkle
            $glitterInterface = $tempImagine->create($size);
            $glitterInterface->paste($frame, new Point(0,0));
            $glitterInterface->effects()->negative();
            $frame->applyMask($glitterInterface);
            $glitterInterface->paste($frame, new Point(0,0));

            //Glow
            $tempInterface = $tempImagine->create($holoGlow->getSize(), $palette->color(array(0,0,0), 0));
            $tempInterface->paste($holoGlow, new Point(0,0));
            $width = $tempInterface->getSize()->getWidth();
            $height = $tempInterface->getSize()->getHeight();
            $tempInterface->crop(new Point($width - ($width * $offset), $height - ($height * $offset)), new Box($width, $height));

            $frame->paste($card, new Point($this->cardFactory->offsetX, $this->cardFactory->offsetY));
            $frame->paste($glitterInterface, new Point($this->cardFactory->offsetX, $this->cardFactory->offsetY));
            $frame->paste($tempInterface, new Point(0,0));
            $i++;
        }
        
        return $holoInterface;
    }

    private function getHoloEffect(BoxInterface $size) {

        $size = new Box($size->getWidth() * 2, $size->getHeight()*2);

        $canvas = new Imagine();
        $bwCanvas = new Imagine();

        $palette = new \Imagine\Image\Palette\RGB();

        $blue = $palette->color(array(0,231,255));
        $pink = $palette->color(array(255,0,231));
        $white = $palette->color("fff");

        $playvas = new Imagine();

        $bwFill  = new \Imagine\Image\Fill\Gradient\Vertical($size->getHeight(),$white, $palette->color("000", 99));
        
        $blueFill  = new \Imagine\Image\Fill\Gradient\Vertical($size->getHeight(),$palette->color("fff"), $blue);
        $pinkFill  = new \Imagine\Image\Fill\Gradient\Vertical($size->getHeight(),$palette->color("fff"), $pink);

        $blackWhiteCanvas = $bwCanvas->create($size);
        $bwInterface = $playvas->create(new Box($size->getWidth(), $size->getHeight() / 4))->fill($bwFill);
        $blackWhiteCanvas->paste($bwInterface, new Point(0, 0));
        $blackWhiteCanvas->paste($bwInterface->flipVertically(), new Point(0, $bwInterface->getSize()->getHeight()));
        $blackWhiteCanvas->paste($bwInterface->flipVertically(), new Point(0, ($bwInterface->getSize()->getHeight() * 2) - 10));
        $blackWhiteCanvas->paste($bwInterface->flipVertically(), new Point(0, ($bwInterface->getSize()->getHeight() * 3) - 10));

        $canvasInterface = $canvas->create($size);
        $blueInterface = $playvas->create(new Box($size->getWidth(), $size->getHeight() / 4))->fill($blueFill);
        $pinkInterface = $playvas->create(new Box($size->getWidth(), $size->getHeight() / 4))->fill($pinkFill);
        $canvasInterface->paste($blueInterface, new Point(0, 0));
        $canvasInterface->paste($blueInterface->flipVertically(), new Point(0, $blueInterface->getSize()->getHeight()));
        $canvasInterface->paste($pinkInterface, new Point(0, $pinkInterface->getSize()->getHeight() * 2));
        $canvasInterface->paste($pinkInterface->flipVertically(), new Point(0, $pinkInterface->getSize()->getHeight() * 3));
        $canvasInterface->applyMask($blackWhiteCanvas);

        $transform = new Transformation();
        $transform->rotate(-45, $palette->color(array(0,0,0), 0));
        $transform->apply($canvasInterface);

        return $canvasInterface;
    }

}