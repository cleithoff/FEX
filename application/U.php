<?php

class U {

	public static function buildToken($length=40){
		$dummy = array_merge(range('0', '9'), range('a', 'z'), range('A', 'Z')/*, array('#','&','@','$','_','%','?','+')*/);
		// shuffle array
		mt_srand((double)microtime()*1000000);
		for ($i = 1; $i <= (count($dummy)*2); $i++) {
			$swap = mt_rand(0, count($dummy)-1);
			$tmp = $dummy[$swap];
			$dummy[$swap] = $dummy[0];
			$dummy[0] = $tmp;
		}
		return substr(implode('',$dummy), 0, $length);
	}

	public static function thumbnail($image, $width, $height, $force_rebuild = false, $crop = null) {
		if (empty($width) && empty($height)) {
			return $image;
		}
		$pathinfo = pathinfo ($_SERVER['DOCUMENT_ROOT'] . $image, PATHINFO_DIRNAME | PATHINFO_BASENAME | PATHINFO_EXTENSION | PATHINFO_FILENAME);
		if (!file_exists($pathinfo['dirname'] . '/thumb')) {
			//echo $pathinfo['dirname'] . '/thumb';
			mkdir($pathinfo['dirname'] . '/thumb', 0777);
			chmod($pathinfo['dirname'] . '/thumb', 0777);
		}
		if (!empty($crop)) {
			$fcrop = '?' . $crop['width'] . 'x' . $crop['height'] . '_' . ($crop['left'] * -1) . 'x' . ($crop['top'] * -1);
		}
		$new_name = str_replace('.', '_' . intval($width) . 'x' . intval($height) . '.', $pathinfo['filename'] . '.' . $pathinfo['extension']);
		$fimage = str_replace('./', '/', dirname($image) . '/thumb/' . $new_name);
		if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $fimage) || $force_rebuild) {
			$img = null;
			$color = array (
					'red' => 255,
					'green' => 255,
					'blue' => 255,
					'alpha' => 127,
			);
			$savealpha = true;
			switch (strtolower($pathinfo['extension'])) {
				case 'jpg' :
				case 'jpeg' :
					$img = imagecreatefromjpeg($_SERVER['DOCUMENT_ROOT'] . $image);
					break;
				case 'png' :
					$img = imagecreatefrompng($_SERVER['DOCUMENT_ROOT'] . $image);
					imagealphablending( $img, false);
					imagesavealpha( $img, true );
					break;
				case 'gif' :
					$img = imagecreatefromgif($_SERVER['DOCUMENT_ROOT'] . $image);
					$transparentIndex = imagecolortransparent($img);
					$color = imagecolorsforindex($img, $transparentIndex);
					$color['alpha'] = 255;
					break;
			}
			if (!empty($img)) {
				$dst_width = $width;
				$dst_height = $height;

				$orig_width = imagesx($img);
				$orig_height = imagesy($img);

				if (empty($dst_width)) {
					$dst_width = $orig_width * $dst_height / $orig_height;
				}
				if (empty($dst_height)) {
					$dst_height = $orig_height * $dst_width / $orig_width;
				}

				// keep aspect ratio
				$wscale = $dst_width / $orig_width;
				$hscale = $dst_height / $orig_height;
				$scale = $wscale > $hscale ? $wscale : $hscale;

				$scale_width = round($orig_width * $scale);
				$scale_height = round($orig_height * $scale);

				if (!empty($crop)) {

					$scale_width = $crop['width'];
					$scale_height = $crop['height'];
				}
				$newimg = imagecreatetruecolor($scale_width, $scale_height);
				if (strtolower($pathinfo['extension']) == 'gif') {
					$bgc = ImageColorAllocate( $newimg, $color['red'], $color['green'], $color['blue']);
					$bgc_index = ImageColorTransparent( $newimg, $bgc );
					ImageFill( $newimg, 0,0, $bgc_index );
					ImageCopyResized($newimg, $img, 0,0, 0,0, $scale_width, $scale_height, $orig_width, $orig_height);
				} else {
					imagealphablending( $newimg, false);
					imagesavealpha( $newimg, $savealpha);
					$bgc = imagecolorallocatealpha($newimg, $color['red'], $color['green'], $color['blue'], $color['alpha']);
					ImageFilledRectangle ($newimg, 0, 0, $scale_width, $scale_height, $bgc);
					imagealphablending( $newimg, false);
					imagesavealpha( $newimg, $savealpha);
					imagecopyresampled($newimg, $img, 0, 0, 0, 0, $scale_width, $scale_height, $orig_width, $orig_height);
					imagealphablending( $newimg, true);
					imagesavealpha( $newimg, $savealpha );
				}

				// Starting point of crop
				$px = floor($scale_width / 2) - floor($dst_width / 2);
				$py = floor($scale_height / 2) - floor($dst_height / 2);
				$im = imagecreatetruecolor($dst_width, $dst_height);
				imagealphablending( $im, false);
				imagesavealpha( $im, $savealpha);
				$bgc = imagecolorallocatealpha($im, $color['red'], $color['green'], $color['blue'], $color['alpha']);
				ImageFilledRectangle ($im, 0, 0, $dst_width, $dst_height, $bgc);
				imagealphablending( $im, false);
				imagesavealpha( $im, $savealpha);
				//$im = $newimg;
				imagecopy ($im, $newimg, 0, 0, empty($crop) ? $px : ($crop['left'] * -1), empty($crop) ? $py : ($crop['top'] * -1), $dst_width , $dst_height);
				imagealphablending( $im, true);
				imagesavealpha( $im, true );
				switch (strtolower($pathinfo['extension'])) {
					case 'jpg' :
					case 'jpeg' :
						imagejpeg($im, $_SERVER['DOCUMENT_ROOT'] . $fimage, 90);
						break;
					case 'png' :
						imagepng($im, $_SERVER['DOCUMENT_ROOT'] . $fimage, 9);
						break;
					case 'gif' :
						//imagetruecolortopalette($im, true, 256);
						imagecolortransparent($im, $bgc);
						imagegif($im, $_SERVER['DOCUMENT_ROOT'] . $fimage);
						break;
				}
				return $fimage . (!empty($fcrop) ? $fcrop : '');
			}
		}
		return $fimage . (!empty($fcrop) ? $fcrop : '');
	}
}