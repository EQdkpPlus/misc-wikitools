<?php

require_once 'github.php';
ini_set('display_errors', 1);
class VersionCheck
{
    public function handler()
    {
        switch ($this->get('action'))
        {
            case 'compare_version':
                if (!$this->get('version') || !$this->get('repo'))
                {
                    break;
                }
                $eqdkp = new GitHub('EQdkpPlus', $this->get('repo'));
                $text = $this->compareVersion($this->get('version'), $eqdkp->getLatestVersion());
                $this->displayImage($text);
                break;
        }
    }

    private function get($parameter)
    {
        if (isset($_GET[$parameter]) && !empty($_GET[$parameter]))
        {
            return $_GET[$parameter];
        }

        return;
    }

    private function compareVersion($sample, $latest)
    {
        $result;
        $text;

        if (strpos($sample, 'x') !== false)
        {
            $min = str_replace('x', '0', $sample);
            $max = str_replace('x', '999', $sample);

            if (version_compare($min, $latest) != 1 && version_compare($max, $latest) != -1)
            {
                $result = 0;
            }
            elseif (version_compare($max, $latest) == 1)
            {
                $result = 1;
            }
            elseif (version_compare($min, $latest) == -1)
            {
                $result = -1;
            }
        }
        else
        {
            $result = version_compare($sample, $latest);
        }

        switch ($result)
        {
            case 0:
                $text = 'aktuelle Version: '.$latest;
                break;
            case 1:
                $text = 'kommende Version: '.$sample;
                break;
            case -1:
                $text = 'veraltete Version: '.$sample;
                break;
        }

        return 'Dieser Artikel bezieht sich auf die '.$text;
    }

    private function createImageWithText($text)
    {
        $x = 565;
        $y = 50;

        $image = imagecreatetruecolor($x, $y);
        $background = imagecolorallocate($image, 170, 170, 170);
        imagefilledrectangle($image, 0, 0, $x, $y, $background);
		
		$background2 = imagecolorallocate($image, 249, 249, 249);
        imagefilledrectangle($image, 1, 1, $x-2, $y-2, $background2);

		$font = 'fonts/verdana.ttf';
        $color = imagecolorallocate($image, 0, 0, 0);
        //imageString($image, 5, 5, 5, $text, $color);
		imagettftext($image, 12, 0, 55, 21, $color, $font, $text);
		imagettftext($image, 12, 0, 55, 43, $color, $font, "Das ist die zweite Zeile");
		
		$myicon = 'icons/clock.png';
		$icon = imagecreatefrompng($myicon);
		list($width_orig, $height_orig) = getimagesize($myicon);

		imagecopyresampled($image, $icon, 5, 5, 0, 0, 42, 42, $width_orig, $height_orig);
		
        return $image;
    }

    private function displayImage($text)
    {
        header ('Content-Type: image/png');

        $image = $this->createImageWithText($text);

        imagepng($image);
        imagedestroy($image);
    }
}

$versionCheck = new VersionCheck();
$versionCheck->handler();

?>
