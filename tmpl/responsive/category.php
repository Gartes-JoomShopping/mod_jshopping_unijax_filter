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
	$enableCategorys = (array)$modHelper->json->result['categorys[]'];
} else {
	$enableCategorys = null;
}
if ($enableCategorys !== null && !count($enableCategorys)) {
	$labelClass = $optionsClass = $modHelper->uf_class;
} else {
	$labelClass = count($activeCategorys) ? ' uf_label_selected' : '';
	$optionsClass = '';
}
if ($modHelper->params->qty_categorys && $modHelper->params->qty_categorys < $count && !count($activeCategorys)) {
	$labelClass .= ' uf_close';
} 
?>
<div class="uf_wrapper uf_wrapper_categorys">
	<div id="uf_categorys_label" class="uf_label_categorys <?php echo $labelClass ?>">
		<?php if ($modHelper->params->show_categorys == 1 || $modHelper->params->show_categorys == 4) { ?>
		<span class="uf_trigon"></span>
		<?php } ?>
		<span><?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_CATEGORY') ?></span>
	</div>
	<div id="uf_categorys" class="uf_options <?php echo $optionsClass ?>">    
	<?php if ($modHelper->params->show_categorys == 1 || $modHelper->params->show_categorys == 4) { ?>
		<div class="uf_select_options chzn-container-multi">
			<ul id="uf_categorys_select_options" class="chzn-choices"></ul>
		</div>
		<div class="uf_options_category<?php if ($modHelper->params->options_qnt && $modHelper->params->options_qnt < $count) { echo ' uf_overflow'; } ?><?php if ($modHelper->params->show_categorys == 1) { echo ' uf_hidecheckbox'; } ?>">
			<?php
			foreach($displayCategorys as $v) {
				if (isset($v->level)) {
					$category_level = $v->level - $modHelper->minCategoryLevel;
				} else {
					$category_level = 0;
				}
				if ($enableCategorys !== null && !in_array($v->id, $enableCategorys)) {
					$inputClass = $modHelper->uf_class;
					$inputState = $modHelper->uf_disable;
				} else {
					$inputClass = '';
					if (in_array($v->id, $activeCategorys)) {
						$inputState = 'checked="checked"';
					} else {
						$inputState = '';
					}
				}
			?>
			<div class="uf_input uf_level_<?php echo $category_level ?> <?php echo $inputClass ?>">
				<input id="uf_category_<?php echo $v->id ?>" type="checkbox" name="categorys[]" value="<?php echo $v->id ?>" <?php echo $inputState ?> onclick="unijaxFilter.updateForm(this)" />
				<label class="uf_category" for="uf_category_<?php echo $v->id ?>"><span><?php echo $v->name ?></span></label>
				<?php if ($modHelper->params->show_count_pos) { ?>
				<span class="uf_count">
					<?php
					if ($enableCategorys !== null) {
						$pos = array_search($v->id, $enableCategorys);
						$posCount = 0;
						if ($pos !== false) {
							$c_json = (array)$modHelper->json->result['c_categorys[]'];
							if ($c_json[$pos]) {
								$posCount = $c_json[$pos];
							}
						}
					?>
					(<?php echo $posCount ?>)
					<?php } ?>
				</span>
				<?php } ?>
				<?php if ($modHelper->params->show_categorys_link){?>
				<a class="uf_link" href="<?php echo SEFLink('index.php?option=com_jshopping&controller=category&task=view&category_id='.$v->id, 1) ?>" title="<?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_CATEGORY_PRODUCTS').' '.htmlspecialchars($v->name) ?>"></a>
				<?php }?>    
				<?php if ($modHelper->params->show_categorys_desc && $v->short_desc){?>
				<div class="uf_tooltip" onmouseover="unijaxFilter.tip(this,'show')" onmouseout="unijaxFilter.tip(this,'hide')"></div>
				<div class="uf_preview"><?php echo $v->short_desc?></div>
				<?php }?>    
			</div>
			<?php } ?>
			<input type="hidden" name="categorys[]" value="" />
		</div>
	<?php } else { ?>
		<select name="categorys[]" data-placeholder="<?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_SELECT_FILTER'.($modHelper->params->show_categorys == 3 ? '_MULTI' : '')) ?>" class="uf_chosen" <?php if ($modHelper->params->show_categorys == 3) { ?>multiple="multiple"<?php } ?> onchange="unijaxFilter.updateForm(this)">
			<?php if ($modHelper->params->show_categorys == 2) { ?>
			<option value=""></option>
			<?php } ?>
			<?php
			foreach($displayCategorys as $v) {
				if (isset($v->level)) {
					$category_level = $v->level;
				} else {
					$category_level = 0;
				}
				if ($enableCategorys !== null && !in_array($v->id, $enableCategorys)) {
					$inputClass = $modHelper->uf_class;
					$inputState = $modHelper->uf_disable;
				} else {
					$inputClass = '';
					if (in_array($v->id, $activeCategorys)) {
						$inputState = 'selected="selected"';
					} else {
						$inputState = '';
					}
				}
			?>
			<option value="<?php echo $v->id ?>" <?php echo $inputState ?> class="uf_level_<?php echo $category_level ?> <?php echo $inputClass ?>"><?php echo $v->name ?></option>
			<?php } ?>
		</select>
		<?php if ($modHelper->params->show_categorys == 3) { ?>
		<input type="hidden" name="categorys[]" value="" />
		<?php } ?>
	<?php } ?>
	</div>
</div>