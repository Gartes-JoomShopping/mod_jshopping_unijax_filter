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
	$enableVendors = (array)$modHelper->json->result['vendors[]'];
} else {
	$enableVendors = null;
}
if ($enableVendors !== null && !count($enableVendors)) {
	$labelClass = $optionsClass = $modHelper->uf_class;
} else {
	$labelClass = count($activeVendors) ? ' uf_label_selected' : '';
	$optionsClass = '';
}
if ($modHelper->params->qty_vendors && $modHelper->params->qty_vendors < $count && !count($activeVendors)) {
	$labelClass .= ' uf_close';
} 
?>
<div class="uf_wrapper uf_wrapper_vendors">
	<div id="uf_vendors_label" class="uf_label_vendors <?php echo $labelClass ?>">
		<?php if ($modHelper->params->show_vendors == 1 || $modHelper->params->show_vendors == 4) { ?>
		<span class="uf_trigon"></span>
		<?php } ?>
		<span><?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_VENDOR') ?></span>
	</div>
	<div id="uf_vendors" class="uf_options <?php echo $optionsClass ?>">
	<?php if ($modHelper->params->show_vendors == 1 || $modHelper->params->show_vendors == 4) { ?>
		<div class="uf_select_options chzn-container-multi">
			<ul id="uf_vendors_select_options" class="chzn-choices"></ul>
		</div>
		<div class="uf_options_vendor<?php if ($modHelper->params->options_qnt && $modHelper->params->options_qnt < $count) { echo ' uf_overflow'; } ?><?php if ($modHelper->params->show_vendors == 1) { echo ' uf_hidecheckbox'; } ?>">
			<?php
			foreach($displayVendors as $v) {
				if ($enableVendors !== null && !in_array($v->id, $enableVendors)) {
					$inputClass = $modHelper->uf_class;
					$inputState = $modHelper->uf_disable;
				} else {
					$inputClass = '';
					if (in_array($v->id, $activeVendors)) {
						$inputState = 'checked="checked"';
					} else {
						$inputState = '';
					}
				}
			?>
			<div class="uf_input <?php echo $inputClass ?>">
				<input id="uf_vendor_<?php echo $v->id ?>" type="checkbox" name="vendors[]" value="<?php echo $v->id ?>" <?php echo $inputState ?> onclick="unijaxFilter.updateForm(this)" />
				<label class="uf_vendor" for="uf_vendor_<?php echo $v->id ?>"><span><?php echo $v->shop_name ?></span></label>
				<?php if ($modHelper->params->show_count_pos) { ?>
				<span class="uf_count">
					<?php
					if ($enableVendors !== null) {
						$pos = array_search($v->id, $enableVendors);
						$posCount = 0;
						if ($pos !== false) {
							$c_json = (array)$modHelper->json->result['c_vendors[]'];
							if ($c_json[$pos]) {
								$posCount = $c_json[$pos];
							}
						}
					?>
					(<?php echo $posCount ?>)
					<?php } ?>
				</span>
				<?php } ?>
				<?php if ($modHelper->params->show_vendors_link){ ?>
				<a class="uf_link" href="<?php echo SEFLink('index.php?option=com_jshopping&controller=vendor&task=products&vendor_id=' . $v->id, 1) ?>" title="<?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_VENDOR_PRODUCTS').' '.htmlspecialchars($v->shop_name) ?>"></a>
				<?php }?>    
				<?php if ($modHelper->params->show_vendors_desc && $v->additional_information){?>
				<div class="uf_tooltip" onmouseover="unijaxFilter.tip(this,'show')" onmouseout="unijaxFilter.tip(this,'hide')"></div>
				<div class="uf_preview"><?php echo $v->additional_information?></div>
				<?php }?>    
			</div>
			<?php } ?>
			<input type="hidden" name="vendors[]" value="" />
		</div>
	<?php } else { ?>
		<select name="vendors[]" data-placeholder="<?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_SELECT_FILTER'.($modHelper->params->show_vendors == 3 ? '_MULTI' : '')) ?>" class="uf_chosen" <?php if ($modHelper->params->show_vendors == 3) { ?>multiple="multiple"<?php } ?> onchange="unijaxFilter.updateForm(this)">
			<?php if ($modHelper->params->show_vendors == 2) { ?>
			<option value=""></option>
			<?php } ?>
			<?php
			foreach($displayVendors as $v) {
				if ($enableVendors !== null && !in_array($v->id, $enableVendors)) {
					$inputClass = $modHelper->uf_class;
					$inputState = $modHelper->uf_disable;
				} else {
					$inputClass = '';
					if (in_array($v->id, $activeVendors)) {
						$inputState = 'selected="selected"';
					} else {
						$inputState = '';
					}
				}
			?>
			<option class="<?php echo $inputClass ?>" value="<?php echo $v->id ?>" <?php echo $inputState ?>><?php echo $v->shop_name ?></option>
			<?php } ?>
		</select>
		<?php if ($modHelper->params->show_vendors == 3) { ?>
		<input type="hidden" name="vendors[]" value="" />
		<?php } ?>
	<?php } ?>
	</div>
</div>