<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Nevigen.com
* @website http://nevigen.com/
* @email support@nevigen.com
* @copyright Copyright © Nevigen.com. All rights reserved.
* @license Proprietary. Copyrighted Commercial Software
* @license agreement http://nevigen.com/license-agreement.html
**/

defined('_JEXEC') or die;
?>
<script type="text/javascript">
//<![CDATA[
var unijaxFilter = unijaxFilter || {};
unijaxFilter.use_ajax = <?php echo $modHelper->params->use_ajax ?>;
unijaxFilter.show_immediately = <?php echo $modHelper->params->show_immediately ?>;
unijaxFilter.options_qnt = <?php echo $modHelper->params->options_qnt ?>;
unijaxFilter.need_redirect = <?php echo ($modHelper->type == 'all' && $modHelper->context == '') ? 1 : 0 ?>;
unijaxFilter.priceRangeFrom = <?php echo $modHelper->priceRange->from ? $modHelper->priceRange->from : 0 ?>;
unijaxFilter.priceRangeTo = <?php echo $modHelper->priceRange->to ? $modHelper->priceRange->to : 0 ?>;
unijaxFilter.priceDelay = <?php echo $modHelper->params->price_delay ?>;
<?php if (!$modHelper->params->use_ajax && $modHelper->params->show_prices && $modHelper->params->show_trackbar) { ?>
unijaxFilter.priceLimitFrom = <?php echo ($modHelper->json && $modHelper->json->pricefrom) ? $modHelper->json->pricefrom : 0 ?>;
unijaxFilter.priceLimitTo = <?php echo ($modHelper->json && $modHelper->json->priceto) ? $modHelper->json->priceto : 0 ?>;
<?php } ?>
//]]>
</script>