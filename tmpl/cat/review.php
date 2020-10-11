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
	$enableReviews = (array)$modHelper->json->result['reviews[]'];
	$ajax = false;
} else {
	$enableReviews = $modHelper->getEnableReviews();
	$ajax = true;
}
if (count($enableReviews)==1) {
	$optionsClass = $inputClass = $modHelper->uf_class;
	$inputState = $modHelper->uf_disable;
	$posCount = 0;
} else {
	$optionsClass = $inputClass = '';
	if ($activeReviews) {
		$inputState = 'checked="checked"';
	} else {
		$inputState = '';
	}
	$posCount = $enableReviews[2];
}
?>
<div id="uf_reviews" class="uf_options <?php echo $optionsClass ?>">
	<div><span></span></div>
	<div class="uf_options_review">
		<input type="hidden" name="reviews[]" value="0" />
		<div class="uf_input <?php echo $inputClass ?>">
			<input id="uf_review" type="checkbox" name="reviews[]" value="1" <?php echo $inputState ?> onclick="unijaxFilter.updateForm(this)" />
			<label class="uf_review" for="uf_review"><span><?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_WITH_REVIEWS') ?></span><span class="uf_close"></span></label>
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