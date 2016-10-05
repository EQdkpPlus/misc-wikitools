<?php

require_once 'github.php';
//ini_set('display_errors', 1);
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
				
				$lang = (strlen($this->get('lang'))) ? $this->get('lang') : 'en';
				
				$strCacheName = md5($this->get('repo').'_'.$this->get('version').'_'.$lang);
				$strCacheFolder = 'cache/data/';
				if(file_exists($strCacheFolder.$strCacheName.'.txt') && (filemtime($strCacheFolder.$strCacheName.'.txt')+3600 > time())){
					$arrText = unserialize(file_get_contents($strCacheFolder.$strCacheName.'.txt'));
				} else {
					$eqdkp = new GitHub('EQdkpPlus', $this->get('repo'));
					$arrText = $this->compareVersion($this->get('version'), $eqdkp->getLatestVersion(), $lang, $this->get('repo'));
					file_put_contents($strCacheFolder.$strCacheName.'.txt', serialize($arrText));
				}

                $this->displayImage($arrText);
                break;
        }
    }

    private function get($parameter)
    {
        if (isset($_GET[$parameter]) && !empty($_GET[$parameter]))
        {
            return filter_var($_GET[$parameter], FILTER_SANITIZE_STRING);
        }

        return;
    }

    private function compareVersion($sample, $latest, $lang='en', $repo='')
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

		if($lang == 'de'){
			$text = 'Dieser Artikel bezieht sich auf die ';
			$subtext = "";
			$icon = "";
			switch ($result)
			{
				case 1:
					$text .= 'kommende Version: ';
					$subtext = array('Wir bieten keinen Support für Funktionen oder Informationen,', 'die sich auf die zukünftige Versionen beziehen.');
					$icon = 'icons/attention.png';
					break;
				case -1:
					$text .= 'veraltete Version: ';
					$subtext = array('Bitte hilf mit, die fehlenden Informationen zu recherchieren ', 'und einzufügen.');
					$icon = 'icons/clock.png';
					break;
			}

		} else {
			$text = 'This article refers to the ';
			$subtext = "";
			$icon = "";
			switch ($result)
			{
				case 1:
					$text .= 'future version: ';
					$subtext = array('We do not offer support for functionality or informations', 'that refer to future versions.');
					$icon = 'icons/attention.png';
					break;
				case -1:
					$text .= 'outdated version: ';
					$subtext = array('Please help us investigate the missing informations', 'and update this article.');
					$icon = 'icons/clock.png';
					break;
			}
			
		}

        return array($result, $text.$sample, $subtext, $icon, $lang, $repo, $sample);
    }

    private function createImageWithText($text, $subtext, $myicon, $result)
    {
        $x = 565;
        $y = 50;
		
		$intSubLines = (is_array($subtext)) ? count($subtext) : ((strlen($subtext)) ? 1 : 0);
		$y = $y -25 + ($intSubLines*25);

        $image = imagecreatetruecolor($x, $y);
        $background = imagecolorallocate($image, 170, 170, 170);
        imagefilledrectangle($image, 0, 0, $x, $y, $background);
		
		$background2 = imagecolorallocate($image, 249, 249, 249);
        imagefilledrectangle($image, 1, 1, $x-2, $y-2, $background2);
		
		if($myicon != ""){
			$icon = imagecreatefrompng($myicon);
			list($width_orig, $height_orig) = getimagesize($myicon);

			imagecopyresampled($image, $icon, 5, ($y/2 - 21), 0, 0, 42, 42, $width_orig, $height_orig);
			$imageMarginLeft = 55;
		} else {
			$imageMarginLeft = 5;
		}
		
		$font = 'fonts/verdana.ttf';
		$color = imagecolorallocate($image, 0, 0, 0);
		imagettftext($image, 11, 0, $imageMarginLeft, 21, $color, $font, $text);
		
		if(is_array($subtext)){
			$intStart = 43;
			foreach($subtext as $val){
				imagettftext($image, 11, 0, $imageMarginLeft, $intStart, $color, $font, $val);
				$intStart += 22;
			}
		} elseif(strlen($subtext)){
			imagettftext($image, 11, 0, $imageMarginLeft, 43, $color, $font, $subtext);
		}
		
        return $image;
    }

    private function displayImage($arrText)
    {
	    header ('Content-Type: image/png');
		
		//show empty pixel if version is recent
		if($arrText[0] == 0){
			echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
			exit;
		}

		//Check for cached images
		$strCacheName = md5($arrText[0].'_'.$arrText[4].'_'.$arrText[5].'_'.$arrText[6]);
		$strCacheFolder = 'cache/images/';
		if(file_exists($strCacheFolder.$strCacheName.'.png') && (filemtime($strCacheFolder.$strCacheName.'.png')+3600 > time())){
			echo file_get_contents($strCacheFolder.$strCacheName.'.png');
			exit;
		} else {
			$image = $this->createImageWithText($arrText[1], $arrText[2], $arrText[3], $arrText[0]);
			imagepng($image, $strCacheFolder.$strCacheName.'.png', 0);
			imagedestroy($image);
			echo file_get_contents($strCacheFolder.$strCacheName.'.png');
			exit;
		}

    }
}

$versionCheck = new VersionCheck();
$versionCheck->handler();

?>
