<?php

use \BFI\FrontController;

/**
 * This class is used to make fake-static files,
 * like dynamically created Javascript or CSS
 */
class StaticFilesController extends \BFI\Controller\AController
{
    protected function _preDispatch()
    {
        $this->_view->enableLayout(false);
    }

    public function captchaAction()
    {
        $this->_enableView(false);
        $draw = new ImagickDraw();

        $width = 106;
        $height = 28;
        $validChars = array_merge(range('A', 'Z'), range(1, 9));
        $draw->setfont(BASE_PATH . '/static/Ubuntu-B.ttf');

        $image = new Imagick();
        $image->newImage($width, $height, new ImagickPixel());
        $image->addnoiseimage(Imagick::NOISE_RANDOM);
        $image->blurimage(10, 2);
        $image->negateimage(true, Imagick::CHANNEL_ALL);

        $top = 20;
        $captchastr = '';
        for ($left = 5; $left <= 90; $left += 20) {
            $rot = rand(-20, 20);
            $char = $validChars[array_rand($validChars)];
            $captchastr .= $char;
            $draw->setfontsize(18);
            $draw->setfillcolor('#6699FF');
            $image->annotateimage($draw, $left, $top, $rot, $char);
        }

        \BFI\Session::set('captcha', $captchastr);

        $image->setImageFormat('png');
        $image->setimagedepth(8);
        $image->setimagecompression(Imagick::COMPRESSION_BZIP);
        $image->setimagecompressionquality(0);
        $image->stripimage();
        header("Content-Type: image/png");
        echo $image;
    }
}
