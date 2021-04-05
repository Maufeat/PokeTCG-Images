<?php

namespace PokeTCG;

use Imagine\Imagick\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

class CardFactory {

    public $interface;

    public $image;

    public $offsetX;
    public $offsetY;
    public $width;
    public $height;
    public $format;

    public $holoType = "none";

    private $borderX = 14;
    private $borderY = 14;

    function __construct($height = 460, $width = 330, $format = "png"){
        $imagine = new Imagine();
        $this->interface = $imagine->create(new Box($width, $height));
        $this->width = $width;
        $this->height = $height;
        $this->format = $format;
    }

    function setOffset(float $x, float $y) {
        $this->offsetX = (($x * $this->borderX) - (0.5 * $this->borderX)) * 2;
        $this->offsetY = (($y * $this->borderY) - (0.5 * $this->borderY)) * 2;
    }

    function setImage(string $image) {
        $this->image = $image;
    }

    function setFormat(string $format) {
        $this->format = $format;
    }

    function setHolo(string $holo) {
        $this->holoType = $holo;
    }

    function create() {
        $imagine = new Imagine();
        $ocInterface = $imagine->open($this->image);
        $ocInterface->resize(new Box($this->width, $this->height));

        $color = $ocInterface->getColorAt(new Point($this->width/2, $this->height - 5));

        if($this->offsetX < 0) {
            $ocInterface->crop(new Point($this->offsetX * -1, 0), new Box($this->width - $this->offsetX * -1, $this->height));
        }
        if($this->offsetY < 0) {
            $ocInterface->crop(new Point(0, $this->offsetY * -1), new Box($this->width - $this->offsetX * -1, $this->height - $this->offsetY * -1));
        }

        if($this->offsetX < 0) {
            $this->offsetX = 0;
        }
        if($this->offsetY < 0) {
            $this->offsetY = 0;
        }

        $this->interface->draw()->rectangle(new Point(0,0), new Point($this->width, $this->height), $color, true);
        $this->image = $ocInterface;
        $this->interface->paste($ocInterface, new Point($this->offsetX, $this->offsetY));        
    }

    function show() {
        if($this->holoType == "none") {
            $this->interface->show($this->format);
        } else {
            $holo = new HoloFilter($this, "F:\\xampp\\htdocs\\sparkles.gif");
            $generatedGif = $holo->apply($this->interface);
            $generatedGif->show("gif", array(
                'animated'       => true,
                'animated.loops' => 0,
            ));
        }
    }

} 