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
	$enableDeliveryTimes = (array)$modHelper->json->result['delivery_times[]'];
} else {
	$enableDeliveryTimes = null;
}
if ($enableDeliveryTimes !== null && !count($enableDeliveryTimes)) {
	$labelClass = $optionsClass = $modHelper->uf_class;
} else {
	$labelClass = count($activeDeliveryTimes) ? ' uf_label_selected' : '';
	$optionsClass = '';
}
if ($modHelper->params->qty_delivery_times && $modHelper->params->qty_delivery_times < $count && !count($activeDeliveryTimes)) {
	$labelClass .= ' uf_close';
} 
?>
<div class="uf_wrapper uf_wrapper_delivery_times">
	<div id="uf_delivery_times_label" class="uf_label_delivery_times <?php echo $labelClass ?>">
		<?php if ($modHelper->params->show_delivery_times == 1 || $modHelper->params->show_delivery_times == 4) { ?>
		<span class="uf_trigon"></span>
		<?php } ?>
		<span><?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_DELIVERY_TIME') ?></span>
	</div>
	<div id="uf_delivery_times" class="uf_options <?php echo $optionsClass ?>">
	<?php if ($modHelper->params->show_delivery_times == 1 || $modHelper->params->show_delivery_times == 4) { ?>
		<div class="uf_select_options chzn-container-multi">
			<ul id="uf_delivery_times_select_options" class="chzn-choices"></ul>
		</div>
		<div class="uf_options_delivery_time<?php if ($modHelper->params->options_qnt && $modHelper->params->options_qnt < $count) { echo ' uf_overflow'; } ?><?php if ($modHelper->params->show_delivery_times == 1) { echo ' uf_hidecheckbox'; } ?>">
			<input type="hidden" name="delivery_times[]" value="0" />
			<?php
			foreach($displayDeliveryTimes as $v) {
				if ($enableDeliveryTimes !== null && !in_array($v->id, $enableDeliveryTimes)) {
					$inputClass = $modHelper->uf_class;
					$inputState = $modHelper->uf_disable;
				} else {
					$inputClass = '';
					if (in_array($v->id, $activeDeliveryTimes)) {
						$inputState = 'checked="checked"';
					} else {
						$inputState = '';
					}
				}
			?>
			<div class="uf_input <?php echo $inputClass ?>">
				<input id="uf_delivery_time_<?php echo $v->id ?>" type="checkbox" name="delivery_times[]" value="<?php echo $v->id ?>" <?php echo $inputState ?> onclick="unijaxFilter.updateForm(this)" />
				<label class="uf_input_label" for="uf_delivery_time_<?php echo $v->id ?>"><span><?php echo $v->name ?></span></label>
				<?php if ($modHelper->params->show_count_pos) { ?>
				<span class="uf_count">
					<?php
					if ($enableDeliveryTimes !== null) {
						$pos = array_search($v->id, $enableDeliveryTimes);
						$posCount = 0;
						if ($pos !== false) {
							$c_json = (array)$modHelper->json->result['c_delivery_times[]'];
							if ($c_json[$pos]) {
								$posCount = $c_json[$pos];
							}
						}
					?>
					(<?php echo $posCount ?>)
					<?php } ?>
				</span>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	<?php } else { ?>
		<select name="delivery_times[]" data-placeholder="<?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_SELECT_FILTER'.($modHelper->params->show_delivery_times == 3 ? '_MULTI' : '')) ?>" class="uf_chosen" <?php if ($modHelper->params->show_delivery_times == 3) { ?>multiple="multiple"<?php } ?> onchange="unijaxFilter.updateForm(this)">
			<?php if ($modHelper->params->show_delivery_times == 2) { ?>
			<option value=""></option>
			<?php } ?>
			<?php
			foreach($displayDeliveryTimes as $v) {
				if ($enableDeliveryTimes !== null && !in_array($v->id, $enableDeliveryTimes)) {
					$inputClass = $modHelper->uf_class;
					$inputState = $modHelper->uf_disable;
				} else {
					$inputClass = '';
					if (in_array($v->id, $activeDeliveryTimes)) {
						$inputState = 'selected="selected"';
					} else {
						$inputState = '';
					}
				}
			?>
			<option class="<?php echo $inputClass ?>" value="<?php echo $v->id ?>" <?php echo $inputState ?>><?php echo $v->name ?></option>
			<?php } ?>
		</select>
		<?php if ($modHelper->params->show_delivery_times == 3) { ?>
		<input type="hidden" name="delivery_times[]" value="" />
		<?php } ?>
	<?php } ?>
	</div>
</div>