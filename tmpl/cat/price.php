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

?>
<div class="uf_wrapper uf_wrapper_prices">
	<div id="uf_prices_label" class="uf_label_prices<?php if ($priceFrom > 0 || $priceTo > 0) echo ' uf_label_selected' ?>">
		<span><?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_PRICE') ?> (<?php echo $modHelper->jshopConfig->currency_code ?>)</span>
	</div>
	<div id="uf_prices" class="uf_options_input">
		<div class="uf_trackbar_inputs input-prepend input-append">
			<input type="text" name="pricefrom" id="uf_prices_from" placeholder="<?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_FROM')?>" value="<?php if ($priceFrom > 0) echo $priceFrom ?>" onkeyup="unijaxFilter.updateForm(this, <?php echo $modHelper->params->input_delay ?>)" <?php if ($modHelper->params->show_trackbar){ ?>data-limit-from="<?php echo $trackbarParams['prices']->leftLimit ?>"<?php } ?> />
			<span class="uf_inputreset" onclick="unijaxFilter.clearInput('prices')"><?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_PRICE_SEPARATOR') ?></span>
			<input type="text" name="priceto" id="uf_prices_to" placeholder="<?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_TO') ?>" value="<?php if ($priceTo > 0) echo $priceTo ?>" onkeyup="unijaxFilter.updateForm(this, <?php echo $modHelper->params->input_delay ?>)" <?php if ($modHelper->params->show_trackbar){ ?>data-limit-to="<?php echo $trackbarParams['prices']->rightLimit ?>"<?php } ?> />
		</div>
		<?php if ($modHelper->params->show_trackbar){ ?>
		<div id="uf_prices_trackbar" class="uf_trackbar"></div>
		<?php } ?>    
	</div>
</div>