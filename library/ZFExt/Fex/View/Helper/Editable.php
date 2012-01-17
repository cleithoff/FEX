<?php

class ZFExt_Fex_View_Helper_Editable {

	protected $_hasInit = false;

	protected function init() {
		if (!$this->_hasInit) {
			$this->_hasInit = true;
			ZFExt_Fex::getInstance()->setRegistry('editable', '<script>$(\'.fex-editable\').each(function() {$(this).aloha()});</script>');
			ZFExt_Fex::getInstance()->initAloha();
		}
	}

	public function editable($tag, $content, array $attrs = null) {
		$html = '';
		$editable = array();
		if (!empty($attrs['resource']) && !empty($attrs['privilege']) && ZFExt_Fex::getInstance()->allowed($attrs['resource'], $attrs['privilege'])) {
			$this->init();
			if (empty($attrs['class'])) {
				$attrs['class'] = '';
			}
			$attrs['class'] .= ' fex-editable';
		}
		foreach ($attrs as $attr => $value) {
			switch ($attr) {
				case 'resource' :
				case 'privilege' :
				case 'controller' :
				case 'action' :
				default :
					$editable[] = $attr . '="' . $value . '"';
					break;
			}
		}
		$html .= '<' . $tag . ' ' . implode(' ', $editable) . '>' . $content . '</' . $tag . '>';
		return $html;
	}
}