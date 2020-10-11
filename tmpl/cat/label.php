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
	$enableLabels = (array)$modHelper->json->result['labels[]'];
} else {
	$enableLabels = null;
}
if ($enableLabels !== null && !count($enableLabels)) {
	$labelClass = $optionsClass = $modHelper->uf_class;
} else {
	$labelClass = count($activeLabels) ? ' uf_label_selected' : '';
	$optionsClass = '';
}
if ($modHelper->params->qty_labels && $modHelper->params->qty_labels < $count && !count($activeLabels)) {
	$labelClass .= ' uf_close';
} 
?>
<div class="uf_wrapper uf_wrapper_labels">
	<div id="uf_labels_label" class="uf_label_labels <?php echo $labelClass ?>">
		<?php if ($modHelper->params->show_labels == 1 || $modHelper->params->show_labels == 4) { ?>
		<span class="uf_trigon"></span>
		<?php } ?>
		<span><?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_LABELS') ?></span>
	</div>
	<div id="uf_labels" class="uf_options <?php echo $optionsClass ?>">
	<?php if ($modHelper->params->show_labels == 1 || $modHelper->params->show_labels == 4) { ?>
		<div class="uf_select_options chzn-container-multi">
			<ul id="uf_labels_select_options" class="chzn-choices"></ul>
		</div>
		<div class="uf_options_label<?php if ($modHelper->params->options_qnt && $modHelper->params->options_qnt < $count) { echo ' uf_overflow'; } ?><?php if ($modHelper->params->show_labels == 1) { echo ' uf_hidecheckbox'; } ?>">
			<input type="hidden" name="labels[]" value="0" />
			<?php
			foreach($displayLabels as $v) {
				if ($enableLabels !== null && !in_array($v->id, $enableLabels)) {
					$inputClass = $modHelper->uf_class;
					$inputState = $modHelper->uf_disable;
				} else {
					$inputClass = '';
					if (in_array($v->id, $activeLabels)) {
						$inputState = 'checked="checked"';
					} else {
						$inputState = '';
					}
				}
			?>
			<div class="uf_input <?php echo $inputClass ?>">
				<input id="uf_label_<?php echo $v->id ?>" type="checkbox" name="labels[]" value="<?php echo $v->id ?>" <?php echo $inputState ?> onclick="unijaxFilter.updateForm(this)" />
				<label class="uf_label" for="uf_label_<?php echo $v->id ?>"><span><?php echo $v->name ?></span></label>
				<?php if ($modHelper->params->show_count_pos) { ?>
				<span class="uf_count">
					<?php
					if ($enableLabels !== null) {
						$pos = array_search($v->id, $enableLabels);
						$posCount = 0;
						if ($pos !== false) {
							$c_json = (array)$modHelper->json->result['c_labels[]'];
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
		<select name="labels[]" data-placeholder="<?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_SELECT_FILTER'.($modHelper->params->show_labels == 3 ? '_MULTI' : '')) ?>" class="uf_chosen" <?php if ($modHelper->params->show_labels == 3) { ?>multiple="multiple"<?php } ?> onchange="unijaxFilter.updateForm(this)">
			<?php if ($modHelper->params->show_labels == 2) { ?>
			<option value=""></option>
			<?php } ?>
			<?php
			foreach($displayLabels as $v) {
				if ($enableLabels !== null && !in_array($v->id, $enableLabels)) {
					$inputClass = $modHelper->uf_class;
					$inputState = $modHelper->uf_disable;
				} else {
					$inputClass = '';
					if (in_array($v->id, $activeLabels)) {
						$inputState = 'selected="selected"';
					} else {
						$inputState = '';
					}
				}
			?>
			<option class="<?php echo $inputClass ?>" value="<?php echo $v->id ?>" <?php echo $inputState ?>><?php echo $v->name ?></option>
			<?php } ?>
		</select>
		<?php if ($modHelper->params->show_labels == 3) { ?>
		<input type="hidden" name="labels[]" value="" />
		<?php } ?>
	<?php } ?>
	</div>
</div>