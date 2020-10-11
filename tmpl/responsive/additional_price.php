<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Nevigen.com
* @website https://nevigen.com/
* @email support@nevigen.com
* @copyright Copyright © Nevigen.com. All rights reserved.
* @license Proprietary. Copyrighted Commercial Software
* @license agreement https://nevigen.com/license-agreement.html
**/

defined('_JEXEC') or die;

if ($modHelper->json && is_array($modHelper->json->result)) {
	$enableAdditionalPrices = (array)$modHelper->json->result['additional_prices[]'];
	$ajax = false;
} else {
	$enableAdditionalPrices = $modHelper->getEnableAdditionalPrices();
	$ajax = true;
}
if (count($enableAdditionalPrices)==1) {
	$optionsClass = $inputClass = $modHelper->uf_class;
	$inputState = $modHelper->uf_disable;
	$posCount = 0;
} else {
	$optionsClass = $inputClass = '';
	if ($activeAdditionalPrices) {
		$inputState = 'checked="checked"';
	} else {
		$inputState = '';
	}
	$posCount = $enableAdditionalPrices[2];
}
?>
<div id="uf_additional_prices" class="uf_options <?php echo $optionsClass ?>">
	<div><span></span></div>
	<div class="uf_options_additional_price">
		<input type="hidden" name="additional_prices[]" value="0" />
		<div class="uf_input <?php echo $inputClass ?>">
			<input id="uf_additional_price" type="checkbox" name="additional_prices[]" value="1" <?php echo $inputState ?> onclick="unijaxFilter.updateForm(this)" />
			<label class="uf_additional_price" for="uf_additional_price"><span><?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_ADDITIONAL_PRICE') ?></span><span class="uf_close"></span></label>
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