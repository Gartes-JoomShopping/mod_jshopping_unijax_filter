<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Nevigen.com
* @website https://nevigen.com//
* @email support@nevigen.com
* @copyright Copyright © Nevigen.com. All rights reserved.
* @license Proprietary. Copyrighted Commercial Software
* @license agreement https://nevigen.com//license-agreement.html
**/

defined('_JEXEC') or die;

class JFormFieldMulticharacteristics extends JFormField {

	public $type = 'multicharacteristics';

	protected function getInput(){
		require_once JPATH_SITE.'/components/com_jshopping/lib/factory.php';
		
		$allProductExtraField = JSFactory::getAllProductExtraField();
		$multiProductExtraField = array();
		foreach ($allProductExtraField as $extraField) {
			if ($extraField->type == 0 && $extraField->multilist == 1) {
				$multiProductExtraField[$extraField->id] = $extraField;
			}
		}

		return JHTML::_( 'select.genericlist', $multiProductExtraField, $this->name.'[]', 'class="inputbox" size="8" id = "multicharacteristics" multiple="multiple"', 'id', 'name', empty($this->value) ? '0' : $this->value );
	}
}
?>