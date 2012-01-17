<?php

require_once "ZFExt/Fex.php";

/**
 * @see Zend_Registry
 */
require_once 'Zend/Registry.php';

/**
 * @see Zend_View_Helper_Abstract
 */
require_once 'Zend/View/Helper/Abstract.php';

class ZFExt_Fex_View_Helper_Fex extends Zend_View_Helper_Abstract
{
	/**
	 * @var Zend_View_Interface
	 */
	public $view;


	public function fex()
	{
		$fex = '';
		if (ZFExt_Fex::getInstance()->hasAloha()) {
			$fex .=
			'<script src="/fex/js/third/aloha/lib/vendor/ext-4.0.7/bootstrap.js" type="text/javascript"></script>' .
			'<script src="/fex/js/third/aloha/aloha-config.js" type="text/javascript"></script>' .
			'<script src="/fex/js/third/aloha/lib/aloha.js" type="text/javascript" data-aloha-plugins="common/format,
			common/table,
			common/list,
			common/link,
			common/highlighteditables,
			common/block,
			common/undo,
			common/contenthandler,
			common/paste,
			common/characterpicker,
			common/commands,
			extra/flag-icons,
			common/abbr,
			extra/wai-lang,
			extra/numerated-headers,
			extra/formatlesspaste,
			extra/browser,
			extra/linkbrowser,
			fex/save"></script>' .
			'<script type="text/javascript">
			Aloha.ready( function(){
				var $ = Aloha.jQuery;
				// register all editable areas
				' . (implode('', ZFExt_Fex::getInstance()->getRegistryAloha())) . '
				// hide loading div
				//$("#aloha-loading").hide();
				//$("#aloha-loading span").html("Loading Plugins");
			});
			</script>

			';
		}
		if (ZFExt_Fex::getInstance()->hasNavigation()) {
			$fex .= '<script src="/fex/js/navigation.js" type="text/javascript"></script>';
		}
		$fex .= implode('', ZFExt_Fex::getInstance()->getRegistry());
		return $fex;
	}

	/**
	 * Set view object
	 *
	 * @param  Zend_View_Interface $view
	 * @return void
	 */
	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}

}
