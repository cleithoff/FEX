<?php

class ZFExt_Fex_View_Helper_Image {

	protected $_hasInit = false;

	protected function init() {
		if (!$this->_hasInit) {
			$this->_hasInit = true;
			ZFExt_Fex::getInstance()->setRegistry('image', '<script>$(\'.fex-image\').each(function() {$(this).fexImage()});</script>');
			$view = Zend_Layout::getMvcInstance()->getView();
			$view->headLink()->appendStylesheet('/fex/css/style.css');
			$view->headScript()->appendFile('/fex/js/image.js');
			$view->headScript()->appendFile('/fex/js/third/jquery.clip.js');
			$view->headScript()->appendFile('/fex/js/third/jquery.mousewheel.min.js');
		}
	}

	public function image(array $attrs = null) {
		if (empty($attrs['resource']) && empty($attrs['privilege'])) {
			$attrs['resource'] = ZFExt_Fex::getInstance()->getDefaultResource();
			$attrs['privilege'] = ZFExt_Fex::getInstance()->getDefaultPrivilege('image');
		}
		$html = '';
		$img = array();
		if (!empty($attrs['src']) && (!empty($attrs['width']) || !empty($attrs['height']))) {
			$attrs['src'] = ZFExt_Fex::thumbnail($attrs['src'], $attrs['width'], $attrs['height']);
		}
		if (!empty($attrs['resource']) && !empty($attrs['privilege']) && ZFExt_Fex::getInstance()->allowed($attrs['resource'], $attrs['privilege'])) {
			$this->init();
			if (empty($attrs['class'])) {
				$attrs['class'] = '';
			}
			$attrs['class'] .= ' fex-image';
		}
		foreach ($attrs as $attr => $value) {
			switch ($attr) {
				case 'src' :
				case 'width' :
				case 'height' :
				case 'alt' :
				case 'class' :
				case 'id' :
				case 'ismap' :
				case 'longdesc' :
				case 'usemap' :
				case 'style' :
				case 'title' :
				case 'dir' :
				case 'lang' :
				case 'onclick' :
				case 'ondblclick' :
				case 'onmousedown' :
				case 'onmouseup' :
				case 'onmouseover' :
				case 'onmousemove' :
				case 'onmouseout' :
				case 'onkeypress' :
				case 'onkeydown' :
				case 'onkeyup' :
					$img[] = $attr . '="' . $value . '"';
					break;
			}
		}
		$html .= '<img ' . implode(' ', $img) . (Zend_Layout::getMvcInstance()->getView()->doctype()->isXhtml() ? ' /' : ' ') . '>';
		if (!empty($attrs['href']) && !ZFExt_Fex::getInstance()->allowed($attrs['resource'], $attrs['privilege'])) {
			$html = '<a href="' . $attrs['href'] . '">' . $html . '</a>';
		}
		return $html;
	}

}