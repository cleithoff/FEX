<?php

class ZFExt_Fex_View_Helper_Translate extends Zend_View_Helper_Translate {

	protected $_hasInitTranslate = false;

	public $_decorator_span = array(
			array(
					array('openDiv' =>'HtmlTag'),
					array('tag' => 'span', 'openOnly' => true,
							'class' => 'fex-translate'),
			),
			array(
					array('closeDiv' =>'HtmlTag'),
					array('tag' => 'span', 'closeOnly' => true)
			),
	);

	protected function initTranslate() {
		if (!$this->_hasInitTranslate) {
			$this->_hasInitTranslate = true;
			ZFExt_Fex::getInstance()->setRegistryAloha('translate', '
					$("fieldset.fex-translate legend").wrapInner(function(){ return "<span class=\"fex-translate\" fexlocale=\"" + $(this).parent().attr("fexlocale") + "\" fexecute=\"" + $(this).parent().attr("fexecute") + "\" />"});
					$("fieldset.fex-translate").removeAttr("fexecute");
					$("fieldset.fex-translate").removeAttr("fexlocale");
					$("fieldset.fex-translate").removeClass("fex-translate");

					$("label.fex-translate").wrapInner(function(){ return "<span class=\"fex-translate\" fexlocale=\"" + $(this).attr("fexlocale") + "\" fexecute=\"" + $(this).attr("fexecute") + "\" />"});
					$("label.fex-translate").removeAttr("fexecute");
					$("label.fex-translate").removeAttr("fexlocale");
					$("label.fex-translate").removeClass("fex-translate");
					$(".fex-translate").aloha();
			');
			ZFExt_Fex::getInstance()->initAloha();
		}
	}

	public function translate($messageid = null, array $attrs = null) {
		if (empty($attrs['locale'])) {
			$attrs['locale'] = key(Zend_Locale::getDefault());
		}
		if (empty($attrs['resource']) && empty($attrs['privilege'])) {
			$attrs['resource'] = ZFExt_Fex::getInstance()->getDefaultResource();
			$attrs['privilege'] = ZFExt_Fex::getInstance()->getDefaultPrivilege('translate');
		}

		if (!empty($attrs['resource']) && !empty($attrs['privilege']) && ZFExt_Fex::getInstance()->allowed($attrs['resource'], $attrs['privilege'])) {
			$this->initTranslate();
			if (empty($attrs['controller']) && empty($attrs['action'])) {
				$attrs['controller'] = ZFExt_Fex::getInstance()->getDefaultController();
				$attrs['action'] = ZFExt_Fex::getInstance()->getDefaultAction('translate');
			}
			if (is_object($messageid)) {
				if (is_a($messageid, 'Zend_Form')) {
					$displayGroups = $messageid->getDisplayGroups();
					if (!empty($displayGroups)) {
						foreach ($displayGroups as $displaygroup) {
							$decorator = $displaygroup->getDecorator('Fieldset');
							if ($displaygroup->getDecorator('Fieldset'))
								$displaygroup->getDecorator('Fieldset')->setOption('class', 'fex-translate ' . $displaygroup->getDecorator('Fieldset')->getOption('class'));
								$displaygroup->getDecorator('Fieldset')->setOption('fexecute', '/' . $attrs['controller'] . '/' . $attrs['action']);
								$displaygroup->getDecorator('Fieldset')->setOption('fexlocale', $attrs['locale']);
						}
					}
					$elements = $messageid->getElements();
					foreach($elements as $element) {
						$decoratorLabel = $element->getDecorator('Label');
						if (!empty($decoratorLabel)) {
							$decoratorLabel->setOption('class', 'fex-translate ' . $decoratorLabel->getOption('class'));
							$decoratorLabel->setOption('fexecute', '/' . $attrs['controller'] . '/' . $attrs['action']);
							$decoratorLabel->setOption('fexlocale', $attrs['locale']);
							$decoratorLabel->setOption('disableFor', true);
							$decoratorLabel->setOption('requiredSuffix', false);
							$decoratorLabel->setOption('requiredPrefix', false);
							$decoratorLabel->setOption('optionalSuffix', false);
							$decoratorLabel->setOption('optionalPrefix', false);
						}
					}
				}
			} elseif (is_array($messageid)) {
			} elseif (is_string($messageid)) {
				return '<span class="fex-translate" fexlocale="' . $attrs['locale'] . '" fexecute="/' . $attrs['controller'] . '/' . $attrs['action'] .'">' . parent::translate($messageid) . '</span>';
			} else {
				return parent::translate($messageid);
			}
			return $messageid;
		} else {
			return parent::translate($messageid);
		}
	}
}