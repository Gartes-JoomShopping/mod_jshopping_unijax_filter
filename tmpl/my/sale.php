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

if ($modHelper->json && is_array($modHelper->json->result)) {
	$enableSales = (array)$modHelper->json->result['sales[]'];
	$ajax = false;
} else {
	$enableSales = $modHelper->getEnableSales();
	$ajax = true;
}
if (count($enableSales)==1) {
	$optionsClass = $inputClass = $modHelper->uf_class;
	$inputState = $modHelper->uf_disable;
	$posCount = 0;
} else {
	$optionsClass = $inputClass = '';
	if ($activeSales) {
		$inputState = 'checked="checked"';
	} else {
		$inputState = '';
	}
	$posCount = $enableSales[2];
}
?>
<div id="uf_sales" class="uf_options <?php echo $optionsClass ?>">
	<div><span></span></div>
	<div class="uf_options_sale">
		<input type="hidden" name="sales[]" value="0" />
		<div class="uf_input <?php echo $inputClass ?>">
			<input id="uf_sale" type="checkbox" name="sales[]" value="1" <?php echo $inputState ?> onclick="unijaxFilter.updateForm(this)" />
			<label class="uf_sale" for="uf_sale"><span><?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_SALES_ONLY') ?></span><span class="uf_close"></span></label>
			<?php if ($modHelper->params->show_count_pos) { ?>
			<span class="uf_count">
				<?php if (!$ajax) { ?>
				(<?php echo $posCount ?>)
				<?php } ?>
			</span>
			<?php } ?>
		</div>
    </div>
</div>