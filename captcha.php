<?php

session_start();

header("Content-Type: image/png");

class Captcha
{

    private $img;
    private $code;

    public function generateRandomStrWithRandomSize()
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";

        $chars = str_shuffle($chars);

        return substr($chars, 0, rand(5, 8));
    }

    public function addRandomColor()
    {
        return imagecolorallocate($this->img,
            rand(1, 100),
            rand(1, 100),
            rand(1, 100)
        );
    }

    public function getFonts()
    {
        $files = scandir('font/');

        $fonts = [];

        $authorizedExtensions = [
            'ttf',
            'otf'
        ];

        foreach ($files as $file) {
            $extensionFile = pathinfo($file)['extension'];

            if (in_array($extensionFile, $authorizedExtensions))
                array_push($fonts, realpath('.') . "/font/" . $file);

        }

        return $fonts;
    }

    public function getRandomFonts($fonts)
    {
        return $fonts[rand(0, count($fonts) - 1)];
    }

    public function writeString($text)
    {
        $text = str_split($text);
        $fonts = $this->getFonts();

        foreach ($text as $key => $char)
            imagettftext($this->img,
                rand(24, 35),
                rand(-25, 25),
                (20 + ($key * 40)),
                100,
                $this->addRandomColor(),
                $this->getRandomFonts($fonts),
                $char
            );


        //imagestring($this->img, 20, 20, 0, implode($text, ""), $this->addRandomColor()); debugging line
    }

    public function drawLine()
    {
        imageline($this->img,
            (20 + rand(10, 300)),
            rand(70, 200),
            (20 + rand(10, 300)),
            rand(70, 200),
            $this->addRandomColor()
        );
    }

    public function drawCircle()
    {
        $circle = rand(50, 100);
        imagearc($this->img,
            (20 + rand(10, 300)),
            rand(70, 200),
            $circle,
            $circle,
            0,
            360,
            $this->addRandomColor()
        );
    }

    public function drawSquare()
    {
        imagerectangle($this->img,
            (20 + rand(10, 300)),
            rand(70, 200),
            (20 + rand(10, 300)),
            rand(70, 200),
            $this->addRandomColor()
        );
    }

    public function drawLinesOrCircles()
    {
        $forms = [
            'drawLine',
            'drawCircle',
            'drawSquare'
        ];

        for ($i = 0; $i <= rand(2, 5); $i++)
            call_user_func(array($this, $forms[rand(0, count($forms) - 1)]));

    }

    public function generate($witdh, $heigth)
    {

        $this->img = imagecreate($witdh, $heigth);

        $generatedString = $this->generateRandomStrWithRandomSize();
        $_SESSION['code'] = $generatedString;
        $back = $this->addRandomColor();

        $this->writeString($generatedString);

        $this->drawLinesOrCircles();

        $this->return_image();
        
        return $this;
    }

    public function return_image()
    {
        imagepng($this->img);
    }

    public function getCode() 
    {
        return $this->code;
    }
}


$captcha = new Captcha();

$captcha->generate(350, 200);
?>
