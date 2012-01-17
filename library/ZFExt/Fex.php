<?php

class ZFExt_Fex {

	protected $_defaultResource = 'fex';

	protected $_defaultPrivileges = array(
			'translate' => 'translate',
			'image' => 'image',
			'editable' => 'editable',
			'navigation' => 'navigation',
			);

	protected $_defaultController = 'fex';

	protected $_defaultActions = array(
			'translate' => 'translate',
			'image' => 'image',
			'editable' => 'editable',
			'navigation' => 'navigation',
			);

	protected static $_instance = null;

	protected $_acl = null;

	protected $_auth = null;

	protected $_registry = array();
	protected $_registryAloha = array();

	protected $_initAloha = false;
	protected $_initExtJs = false;
	protected $_initNavigation = false;
	/**
	 * Returns an instance of Zend_Auth
	 *
	 * Singleton pattern implementation
	 *
	 * @return Zend_Auth Provides a fluent interface
	 */
	public static function getInstance()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
	public function hasAloha() {
		return $this->_initAloha;
	}

	public function hasNavigation() {
		return $this->_initNavigation;
	}

	public function initNavigation() {
		if (!$this->allowed($this->getDefaultResource(), $this->getDefaultPrivilege('navigation'))) {
			return $this;
		}
		if (!$this->_initNavigation) {
			$view = Zend_Layout::getMvcInstance()->getView();
			$view->headLink()->appendStylesheet('/fex/css/style.css');
			$this->initAloha();
			$this->setRegistry('navigation', '
					<script type="text/javascript" src="/fex/js/third/md5.js"></script>
					<script type="text/javascript">
						$(\'.fex-navigation a\').each(function() {$(this).fexNavigation()});
						$(\'.fex-navigation\').each(function() {$(this).fexNavigationAdd()});
					</script>');
			$this->_initNavigation = true;
		}
		return $this;
	}

	public function getDefaultResource() {
		return $this->_defaultResource;
	}

	public function setDefaultResource($_defaultResource) {
		$this->_defaultResource = $_defaultResource;
		return $this;
	}

	public function getDefaultPrivilege($_defaultPrivilege) {
		if (array_key_exists($_defaultPrivilege, $this->_defaultPrivileges)) {
			return $this->_defaultPrivileges[$_defaultPrivilege];
		} else {
			return null;
		}
	}

	public function setDefaultPrivilege($_defaultPrivilege, $_value) {
		$this->_defaultPrivileges[$_defaultPrivilege] = $_value;
		return $this;
	}

	public function getDefaultController() {
		return $this->_defaultController;
	}

	public function setDefaultController($_defaultController) {
		$this->_defaultController = $_defaultController;
		return $this;
	}

	public function getDefaultAction($_defaultAction) {
		if (array_key_exists($_defaultAction, $this->_defaultActions)) {
			return $this->_defaultActions[$_defaultAction];
		} else {
			return null;
		}
	}

	public function setDefaultAction($_defaultAction, $_value) {
		$this->_defaultActions[$_defaultAction] = $_value;
		return $this;
	}

	public function setAuth(Zend_Auth $_auth) {
		$this->_auth = $_auth;
		return $this;
	}

	public function getAuth() {
		if (empty($this->_auth)) {
			$this->_auth = Zend_Auth::getInstance();
		}
		return $this->_auth;
	}

	public function setAcl(Zend_Acl $_acl) {
		$this->_acl = $_acl;
		return $this;
	}

	public function getAcl() {
		if (empty($this->_acl)) {
			$this->_acl = Zend_Registry::get('Zend_Acl');
		}
		return $this->_acl;
	}

	public function setRegistry($key, $value) {
		$this->_registry[$key] = $value;
		return $this;
	}

	public function getRegistry() {
		return $this->_registry;
	}

	public function setRegistryAloha($key, $value) {
		$this->_registryAloha[$key] = $value;
		return $this;
	}

	public function getRegistryAloha() {
		return $this->_registryAloha;
	}

	public function allowed($resource, $privilege) {
		if ($this->getAuth()->hasIdentity() && is_object($this->_auth->getIdentity())) {
			$role = $this->getAuth()->getIdentity()->Role;
		} else {
			$role = 'guest';
		}
		if (!$this->getAcl()->has($resource)) {
			$resource = null;
		}
		return $this->getAcl()->isAllowed($role, $resource, $privilege);
	}

	/**
	 * Fex-enable a view instance
	 *
	 * @param  Zend_View_Interface $view
	 * @return void
	 */
	public static function enableView(Zend_View_Interface $view)
	{
		if (false === $view->getPluginLoader('helper')->getPaths('ZFExt_Fex_View_Helper')) {
			$view->addHelperPath('ZFExt/Fex/View/Helper', 'ZFExt_Fex_View_Helper');
		}
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

	public function initAloha() {
		if ($this->_initAloha == false) {
			$this->_initAloha = true;
			$view = Zend_Layout::getMvcInstance()->getView();
			$view->headLink()->appendStylesheet('/fex/js/third/aloha/css/aloha.css');
		}
	}
}