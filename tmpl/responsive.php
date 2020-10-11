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
<a id="unijax_filter_offcanvas_button"></a>
<div id="unijax_filter_container">
	<div id="unijax_filter_block">
		<form action="<?php echo $modHelper->action ?>" id="jshop_unijax_filter" name="jshop_unijax_filter" method="post" data-params="<?php echo htmlspecialchars(json_encode($moduleParams)) ?>">
			<?php if ($modHelper->params->show_count) { ?>
			<div id="uf_finded_products">
				<?php echo JText::_( 'MOD_JSHOPPING_UNIJAXFILTER_SELECTED_PRODUCTS' ) ?>
				<span id="uf_count_product">
					<?php if ($modHelper->params->use_ajax) { ?>
					<span class="uf_count_loader"></span>
					<?php
					} else {
						echo $countProducts;
					}
					?>
				</span>
			</div>
			<?php } ?>    
			<?php if ($modHelper->params->show_top_buttons) { ?>
			<div class="uf_buttons">
				<button type="submit" class="groupbtnleft"> <?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_YES') ?></button>
				<button type="button" class="groupbtnright" onclick="unijaxFilter.clearForm()"> <?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_RESET_FILTER') ?> </button>
			</div>
			<?php } ?>
			<div class="uf_wrappers">
			<?php
			$folder = dirname(__FILE__).'/'.$modHelper->params->template.'/';
			foreach ($modHelper->params->order_table as $filename) {
				if (isset($displayFilters[$filename])) {
					$count = $displayFilters[$filename];
					@include $folder.$filename.'.php';
				}
			}
			?>
			</div>
			<?php if ($modHelper->params->show_bottom_buttons) { ?>
			<div class="uf_buttons">
				<button type="submit" class="groupbtnleft"> <?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_YES') ?></button>
				<button type="button" class="groupbtnright" onclick="unijaxFilter.clearForm()"> <?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_RESET_FILTER') ?> </button>
			</div>
			<?php
			}
			foreach ($modHelper->hiddenFilters as $filter=>$values) {
				if (is_array($values)) {
					foreach ($values as $key=>$value) {
						if (is_array($value)) {
							foreach ($value as $k=>$v) {
			?>
			<input type="hidden" name="<?php echo $filter ?>[<?php echo $key ?>][]" value="<?php echo $v ?>" />
			<?php
							}
						} else {
			?>
			<input type="hidden" name="<?php echo $filter ?>[]" value="<?php echo $value ?>" />
			<?php
						}
					}
				} else {
			?>
			<input type="hidden" name="<?php echo $filter ?>" value="<?php echo $values ?>" />
			<?php
				}
			}
			?>
			<input type="hidden" name="urlUnijaxFilter" value="<?php echo base64_encode($modHelper->urlUnijaxFilter)?>" />
			<input type="hidden" name="limitstart" value="0" />
		</form>
	</div>
</div>
<?php if (trim($modHelper->params->pre_processing) != '' || trim($modHelper->params->post_processing) != '') { ?>
<script type="text/javascript">
;var unijaxFilter = unijaxFilter || {};
<?php if (trim($modHelper->params->pre_processing) != '') { ?>
unijaxFilter.beforeUpdateProductList = function() {
<?php echo trim($modHelper->params->pre_processing) ?>
};
<?php } ?>
<?php if (trim($modHelper->params->post_processing) != '') { ?>
unijaxFilter.afterUpdateProductList = function() {
<?php echo trim($modHelper->params->post_processing) ?>
};
<?php } ?>
</script>
<?php } ?>