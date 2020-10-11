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

?>
<?php
foreach($modHelper->allAttributes as $attr) {
	if (!isset($displayAttributes[$attr->attr_id])) continue;
	$attr_id = $attr->attr_id;
	$attr_values = $displayAttributes[$attr->attr_id];
	$count = count($attr_values);
	if ($count <= $modHelper->params->once_option) {
		continue;
	}
	if ($attr->independent) {
		$pre='';
	} else {
		$pre = 'd_';
	}
	if ($modHelper->json && is_array($modHelper->json->result)) {
		$enableAttributes = (array)$modHelper->json->result[$pre.'attributes['.$attr->attr_id.'][]'];
	} else {
		$enableAttributes = null;
	}
	if ($enableAttributes !== null && !count($enableAttributes)) {
		$labelClass = $optionsClass = $modHelper->uf_class;
	} else {
		$labelClass = (is_array($activeAttributes[$attr->attr_id]) && count($activeAttributes[$attr->attr_id])) ? ' uf_label_selected' : '';
		$optionsClass = '';
	}
	if ((in_array($attr->attr_id, $modHelper->params->close_attributes_id) || ($modHelper->params->qty_attributes && $modHelper->params->qty_attributes < $count)) && !count($activeAttributes[$attr->attr_id])) {
		$labelClass .= ' uf_close';
	} 
?>
<div class="uf_wrapper uf_wrapper_attributes_<?php echo $attr->attr_id ?>">
	<div id="uf_attributes_<?php echo $attr->attr_id ?>_label" class="uf_label_attributes_<?php echo $attr->attr_id ?> <?php echo $labelClass ?>">
		<?php if ($modHelper->params->show_attributes == 1 || $modHelper->params->show_attributes == 4) { ?>
		<span class="uf_trigon"></span>
		<?php } ?>
		<span><?php echo $attr->name;?></span>
		<?php if ($modHelper->params->show_attributes_desc && $attr->description) { ?>
		<div class="uf_tooltip" onmouseover="unijaxFilter.tip(this,'show')" onmouseout="unijaxFilter.tip(this,'hide')"></div>
		<div class="uf_preview"><?php echo $attr->description?></div>
		<?php } ?>    
	</div>
	<div id="uf_attributes_<?php echo $attr->attr_id ?>" class="uf_options <?php echo $optionsClass ?>">
	<?php if ($modHelper->params->show_attributes == 1 || $modHelper->params->show_attributes == 4) { ?>    
		<div class="uf_select_options chzn-container-multi">
			<ul id="uf_attributes_<?php echo $attr->attr_id ?>_select_options" class="chzn-choices"></ul>
		</div>
		<div class="uf_options_attribute_<?php echo $attr->attr_id ?><?php if ($modHelper->params->options_qnt && $modHelper->params->options_qnt < $count) { echo ' uf_overflow'; } ?><?php if ($modHelper->params->show_attributes == 1) { echo ' uf_hidecheckbox'; } ?>">
			<input type="hidden" name="<?php echo $pre ?>attributes[<?php echo $attr->attr_id?>][]" value="0" />
			<?php
			foreach($attr_values as $attr_value) {
				if ($enableAttributes !== null && !in_array($attr_value->value_id, $enableAttributes)) {
					$inputClass = $modHelper->uf_class;
					$inputState = $modHelper->uf_disable;
				} else {
					$inputClass = '';
					if (is_array($activeAttributes[$attr->attr_id]) && in_array($attr_value->value_id, $activeAttributes[$attr->attr_id])) {
						$inputState = 'checked="checked"';
					} else {
						$inputState = '';
					}
				}
			?>
			<div class="uf_input <?php echo $inputClass ?>">
				<input id="uf_attribute_<?php echo $attr->attr_id ?>_<?php echo $attr_value->value_id ?>" type="checkbox" name="<?php echo $pre ?>attributes[<?php echo $attr->attr_id ?>][]" value="<?php echo $attr_value->value_id ?>" <?php echo $inputState ?> onclick="unijaxFilter.updateForm(this)" />
				<label class="uf_input_label" for="uf_attribute_<?php echo $attr->attr_id ?>_<?php echo $attr_value->value_id ?>"><span><?php echo $attr_value->name ?></span></label>
				<?php if ($modHelper->params->show_count_pos) { ?>
				<span class="uf_count">
					<?php
					if ($enableAttributes !== null) {
						$pos = array_search($attr_value->value_id, $enableAttributes);
						$posCount = 0;
						if ($pos !== false) {
							$c_json = (array)$modHelper->json->result['c_'.$pre.'attributes['.$attr->attr_id.'][]'];
							if ($c_json[$pos]) {
								$posCount = $c_json[$pos];
							}
						}
					?>
					(<?php echo $posCount ?>)
					<?php } ?>
				</span>
				<?php } ?>
				<?php if ($modHelper->params->show_attribute_image && $attr_value->image) {?>
				<img class="uf_attr_img" src="<?php echo $modHelper->jshopConfig->image_attributes_live_path.'/'.$attr_value->image ?>" />
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	<?php } else { ?>
		<?php if ($modHelper->params->show_attributes == 3) { ?>
		<input type="hidden" name="<?php echo $pre ?>attributes[<?php echo $attr->attr_id ?>][]" value="0" />
		<?php } ?>
		<select name="<?php echo $pre ?>attributes[<?php echo $attr->attr_id ?>][]" data-placeholder="<?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_SELECT_FILTER'.($modHelper->params->show_attributes == 3 ? '_MULTI' : '')) ?>" class="uf_chosen" <?php if ($modHelper->params->show_attributes == 3) { ?>multiple="multiple"<?php } ?> onchange="unijaxFilter.updateForm(this)">
			<?php if ($modHelper->params->show_attributes == 2) { ?>
			<option value=""></option>
			<?php } ?>
			<?php
			foreach($attr_values as $attr_value) {
				if ($enableAttributes !== null && !in_array($attr_value->value_id, $enableAttributes)) {
					$inputClass = $modHelper->uf_class;
					$inputState = $modHelper->uf_disable;
				} else {
					$inputClass = '';
					if (is_array($activeAttributes[$attr->attr_id]) && in_array($attr_value->value_id, $activeAttributes[$attr->attr_id])) {
						$inputState = 'selected="selected"';
					} else {
						$inputState = '';
					}
				}
			?>
			<option class="<?php echo $inputClass ?>" value="<?php echo $attr_value->value_id ?>" <?php echo $inputState ?>><?php echo $attr_value->name ?></option>
			<?php } ?>
		</select>
	<?php } ?>
	</div>
</div>
<?php } ?>