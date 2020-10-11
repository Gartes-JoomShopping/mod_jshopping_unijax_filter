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
	$enableManufacturers = (array)$modHelper->json->result['manufacturers[]'];
} else {
	$enableManufacturers = null;
}
if ($enableManufacturers !== null && !count($enableManufacturers)) {
	$labelClass = $optionsClass = $modHelper->uf_class;
} else {
	$labelClass = count($activeManufacturers) ? ' uf_label_selected' : '';
	$optionsClass = '';
}
if ($modHelper->params->qty_manufacturers && $modHelper->params->qty_manufacturers < $count && !count($activeManufacturers)) {
	$labelClass .= ' uf_close';
} 
?>
<div class="uf_wrapper uf_wrapper_manufacturers">/mod_jshopping_unijax_filter/tmpl/my/manufacturer.php
	<div id="uf_manufacturers_label" class="uf_label_manufacturers <?php echo $labelClass ?>">
		<?php if ($modHelper->params->show_manufacturers == 1 || $modHelper->params->show_manufacturers == 4) { ?>
		<span class="uf_trigon"></span>
		<?php } ?>
		<span><?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_MANUFACTURER') ?></span>
	</div>
	<div id="uf_manufacturers" class="uf_options <?php echo $optionsClass ?>">
	<?php if ($modHelper->params->show_manufacturers == 1 || $modHelper->params->show_manufacturers == 4) { ?>
		<div class="uf_select_options chzn-container-multi">
			<ul id="uf_manufacturers_select_options" class="chzn-choices"></ul>
		</div>
		<div class="uf_options_manufacturer<?php if ($modHelper->params->options_qnt && $modHelper->params->options_qnt < $count) { echo ' uf_overflow'; } ?><?php if ($modHelper->params->show_manufacturers == 1) { echo ' uf_hidecheckbox'; } ?>">
			<?php
			foreach($displayManufacturers as $v){
				if ($enableManufacturers !== null && !in_array($v->id, $enableManufacturers)) {
					$inputClass = $modHelper->uf_class;
					$inputState = $modHelper->uf_disable;
				} else {
					$inputClass = '';
					if (in_array($v->id, $activeManufacturers)) {
						$inputState = 'checked="checked"';
					} else {
						$inputState = '';
					}
				}
			?>
			<div class="uf_input <?php echo $inputClass ?>">/mod_jshopping_unijax_filter/tmpl/my/manufacturer.php
				<input id="uf_manufacturer_<?php echo $v->id ?>" type="checkbox" name="manufacturers[]" value="<?php echo $v->id ?>" <?php echo $inputState ?> onclick="unijaxFilter.updateForm(this)" />
				<label class="uf_manufacturer" for="uf_manufacturer_<?php echo $v->id ?>"><span><?php echo $v->name ?></span></label>
				<?php if ($modHelper->params->show_count_pos) { ?>
				<span class="uf_count">
					<?php
					if ($enableManufacturers !== null) {
						$pos = array_search($v->id, $enableManufacturers);
						$posCount = 0;
						if ($pos !== false) {
							$c_json = (array)$modHelper->json->result['c_manufacturers[]'];
							if ($c_json[$pos]) {
								$posCount = $c_json[$pos];
							}
						}
					?>
					(<?php echo $posCount ?>)
					<?php } ?>
				</span>
				<?php } ?>
				<?php if ($modHelper->params->show_manufacturers_link){ ?>
				<a class="uf_link" href="<?php echo SEFLink('index.php?option=com_jshopping&controller=manufacturer&task=view&manufacturer_id=' . $v->id, 1) ?>" title="<?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_MANUFACTURER_PRODUCTS').' '.htmlspecialchars($v->name) ?>"></a>
				<?php } ?>    
				<?php if ($modHelper->params->show_manufacturers_desc && $v->short_desc){ ?>
				<div class="uf_tooltip" onmouseover="unijaxFilter.tip(this,'show')" onmouseout="unijaxFilter.tip(this,'hide')"></div>
				<div class="uf_preview"><?php echo $v->short_desc?></div>
				<?php } ?>    
			</div>
			<?php }?>
			<input type="hidden" name="manufacturers[]" value="" />
		</div>
	<?php } else { ?>
		<select name="manufacturers[]" data-placeholder="<?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_SELECT_FILTER'.($modHelper->params->show_manufacturers == 3 ? '_MULTI' : '')) ?>" class="uf_chosen" <?php if ($modHelper->params->show_manufacturers == 3) { ?>multiple="multiple"<?php } ?> onchange="unijaxFilter.updateForm(this)">
			<?php if ($modHelper->params->show_manufacturers == 2) { ?>
			<option value=""></option>
			<?php } ?>
			<?php
			foreach($displayManufacturers as $v) {
				if ($enableManufacturers !== null && !in_array($v->id, $enableManufacturers)) {
					$inputClass = $modHelper->uf_class;
					$inputState = $modHelper->uf_disable;
				} else {
					$inputClass = '';
					if (in_array($v->id, $activeManufacturers)) {
						$inputState = 'selected="selected"';
					} else {
						$inputState = '';
					}
				}
			?>
			<option class="<?php echo $inputClass ?>" value="<?php echo $v->id ?>" <?php echo $inputState ?>><?php echo $v->name ?></option>
			<?php } ?>
		</select>
		<?php if ($modHelper->params->show_manufacturers == 3) { ?>
		<input type="hidden" name="manufacturers[]" value="" />
		<?php } ?>
	<?php } ?>
	</div>
</div>