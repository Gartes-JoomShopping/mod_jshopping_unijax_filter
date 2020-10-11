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

class JFormFieldCharacteristics extends JFormField {

	public $type = 'characteristics';

	protected function getInput(){
		require_once JPATH_SITE.'/components/com_jshopping/lib/factory.php'; 

		return JHTML::_( 'select.genericlist', JSFactory::getAllProductExtraField(), $this->name.'[]', 'class="inputbox" size="8" id = "characteristic" multiple="multiple"', 'id', 'name', empty($this->value) ? '0' : $this->value );
	}
}
?>