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

if (!JComponentHelper::isEnabled('com_jshopping')){
    return false;
}
require_once __DIR__.'/helper.php';

 

$modHelper = modJshopping_Unijax_FilterHelper::getInstance($params);
$dataParams = array(
	'"use_ajax":' . $modHelper->params->use_ajax,
	'"show_immediately":' . $modHelper->params->show_immediately,
	'"options_qnt":"' . $modHelper->params->options_qnt . '"',
	'"need_redirect":' . $modHelper->needRedirect,
	'"input_delay":"' . $modHelper->params->price_delay . '"'
);
$trackbarParams = array();



if (!$modHelper->enable) {
    return false;
}

if ($modHelper->params->use_ajax == 2 && $modHelper->json) {
	if ($modHelper->json->view->prepareUnijaxFilter > 1) {
		$modHelper->json->view = $modHelper->json->view->loadTemplate();
	}
	ob_clean();
	echo json_encode($modHelper->json);
	die;
}

if (!$modHelper->params->use_ajax && $modHelper->params->show_count) {
	$countProducts = $modHelper->getCountProducts();
}

$displayFilters = array();

$price_from = $modHelper->app->input->getString('price_from');
$price_to = $modHelper->app->input->getString('price_to');
if ($modHelper->params->show_prices) {
	$priceFrom = saveAsPrice($modHelper->app->getUserStateFromRequest($modHelper->contextfilter.'pricefrom', 'pricefrom','','int+'));
	if (!$priceFrom) {
		$priceFrom = saveAsPrice($price_from);
	}
	$priceTo = saveAsPrice($modHelper->app->getUserStateFromRequest($modHelper->contextfilter.'priceto', 'priceto','','int+'));
	if (!$priceTo) {
		$priceTo = saveAsPrice($price_to);
	}
	
	$modHelper->getDisplayPrices();
	if ($modHelper->params->once_option) {
		if ($modHelper->priceRange->from != $modHelper->priceRange->to) {
			$displayFilters['price'] = 1;
		}
	} else if ($modHelper->priceRange->from || $modHelper->priceRange->to) {
		$displayFilters['price'] = 1;
	}
	if ($modHelper->params->show_trackbar) {
		$trackbarParams['prices'] = new stdClass;
		if ($modHelper->params->use_ajax) {
			$trackbarParams['prices']->leftLimit = $modHelper->priceRange->from ? $modHelper->priceRange->from : 0;
			$trackbarParams['prices']->rightLimit = $modHelper->priceRange->to ? $modHelper->priceRange->to : 0;
		} else {
			$trackbarParams['prices']->leftLimit = ($modHelper->json && $modHelper->json->range['prices']) ? $modHelper->json->range['prices']['from'] : 0;
			$trackbarParams['prices']->rightLimit = ($modHelper->json && $modHelper->json->range['prices']) ? $modHelper->json->range['prices']['to'] : 0;
		}
	}
}
if (!isset($displayFilters['price'])) {
	if ($price_from) {
		$modHelper->hiddenFilters['price_from'] = $price_from;
	}
	if ($price_to) {
		$modHelper->hiddenFilters['price_to'] = $price_to;
	}
}

$displayCategorys = array();
$category_id = $modHelper->app->input->getString('category_id');
if ($modHelper->params->show_categorys || ($modHelper->params->output_product_qty && $modHelper->type == 'vendor')) {
	$displayCategorys = $modHelper->getDisplayCategorys($modHelper->params->sort_categorys);
	$count = count($displayCategorys);
	if (!$count && $modHelper->params->output_product_qty && $modHelper->type == 'vendor') {
		return false;
	} else if ($modHelper->params->show_categorys && $count > $modHelper->params->once_option) {
		$activeCategorys = filterAllowValue($modHelper->app->getUserStateFromRequest($modHelper->contextfilter.'categorys', 'categorys', array()), 'int+');
		if ($modHelper->type != 'category' && $modHelper->controller != 'product' && !$activeCategorys) {
			$categoryId = getListFromStr($category_id);
			if (is_array($categoryId)) {
				$activeCategorys = $categoryId;
			} else if ($modHelper->category_id) {
				$activeCategorys[] = $modHelper->category_id;
			}
		}
		$displayFilters['category'] = $count;
	}
}
if ($modHelper->type != 'category' && !isset($displayFilters['category']) && $category_id) {
	$modHelper->hiddenFilters['category_id'] = $category_id;
}

$displayManufacturers = array();
$manufacturer_id = $modHelper->app->input->getString('manufacturer_id');
if ($modHelper->params->show_manufacturers && $modHelper->type != 'manufacturer') {
	$displayManufacturers = $modHelper->getDisplayManufacturers($modHelper->params->sort_manufacturers);
	$count = count($displayManufacturers);
	if ($count > $modHelper->params->once_option) {
		$activeManufacturers = filterAllowValue($modHelper->app->getUserStateFromRequest($modHelper->contextfilter.'manufacturers', 'manufacturers', array()), 'int+');   
		if ($modHelper->type != 'manufacturer' && !$activeManufacturers) {
			$manufacturerId = getListFromStr($manufacturer_id);
			if (is_array($manufacturerId)) {
				$activeManufacturers = $manufacturerId;
			} else if ($modHelper->manufacturer_id) {
				$activeManufacturers[] = $modHelper->manufacturer_id;
			}
		}
		$displayFilters['manufacturer'] = $count;
	}
}
if ($modHelper->type != 'manufacturer' && !isset($displayFilters['manufacturer']) && $manufacturer_id) {
	$modHelper->hiddenFilters['manufacturer_id'] = $manufacturer_id;
}

$displayVendors = array();
$vendor_id = $modHelper->app->input->getString('vendor_id');
if ($modHelper->type != 'vendor') {
	$displayVendors = $modHelper->getDisplayVendors($modHelper->params->sort_vendors);
	$count = count($displayVendors);
	if (!$count && $modHelper->params->output_product_qty) {
		return false;
	} else if ($modHelper->params->show_vendors && $count > $modHelper->params->once_option) {
		$activeVendors = filterAllowValue($modHelper->app->getUserStateFromRequest($modHelper->contextfilter.'vendors', 'vendors', array()), 'int+');
		if ($modHelper->type != 'vendor' && !$activeVendors) {
			$vendorId = getListFromStr($vendor_id);
			if (is_array($vendorId)) {
				$activeVendors = $vendorId;
			} else if ($modHelper->vendor_id) {
				$activeVendors[] = $modHelper->vendor_id;
			}
		}
		$displayFilters['vendor'] = $count;
	}
}
if ($modHelper->type != 'vendor' && !isset($displayFilters['vendor']) && $vendor_id) {
	$modHelper->hiddenFilters['vendor'] = $vendor_id;
}

$displayCharacteristics = array();
if ($modHelper->params->show_characteristics) {
	$displayCharacteristics = $modHelper->getDisplayCharacteristics();
	$count = count($displayCharacteristics);
	if ($count > 0) {
		$activeCharacteristics = $modHelper->filterAllowValue($modHelper->app->getUserStateFromRequest($modHelper->contextfilter.'characteristics', 'characteristics', array()));
		$displayFilters['characteristic'] = $count;
		if ($modHelper->params->show_trackbar_characteristics) {
			foreach ($modHelper->trackbarExtraField as $key=>$value) {
				if (isset($activeCharacteristics[$key])) {
					$leftValue = $activeCharacteristics[$key][0];
					$rightValue = $activeCharacteristics[$key][1];
				} else {
					$leftValue = '';
					$rightValue = '';
				}
				$trackbarParams['characteristics_'.$key] = new stdClass;
				if ($modHelper->params->use_ajax) {
					if (count($value)) {
						$leftLimit = min($value);
						$rightLimit = max($value);
					} else {
						$leftLimit = $rightLimit = 0;
					}
					$trackbarParams['characteristics_'.$key]->leftLimit = $leftLimit ? $leftLimit : 0;
					$trackbarParams['characteristics_'.$key]->rightLimit = $rightLimit ? $rightLimit : 0;
				} else {
					$trackbarParams['characteristics_'.$key]->leftLimit = ($modHelper->json && $modHelper->json->range['characteristics_'.$key]) ? $modHelper->json->range['characteristics_'.$key]['from'] : 0;
					$trackbarParams['characteristics_'.$key]->rightLimit = ($modHelper->json && $modHelper->json->range['characteristics_'.$key]) ? $modHelper->json->range['characteristics_'.$key]['to'] : 0;
				}
			}
		}
	}
}

$displayAttributes = array();
if ($modHelper->params->show_attributes) {
	$displayAttributes = $modHelper->getDisplayAttributes();
	$count = count($displayAttributes);
	if ($count > 0) {
		$activeAttributes =  filterAllowValue($modHelper->app->getUserStateFromRequest($modHelper->contextfilter.'d_attributes', 'd_attributes', array()), "array_int_k_v+") + filterAllowValue($modHelper->app->getUserStateFromRequest($modHelper->contextfilter.'attributes', 'attributes', array()), "array_int_k_v+");
		$displayFilters['attribute'] = $count;
	}
}  

$displayLabels = array();
$label_id = $modHelper->app->input->getString('label_id');
if ($modHelper->params->show_labels) {
	$displayLabels = $modHelper->getDisplayLabels();
	$count = count($displayLabels);
	if ($count > $modHelper->params->once_option) {
		$activeLabels = filterAllowValue($modHelper->app->getUserStateFromRequest($modHelper->contextfilter.'labels', 'labels', array()), 'int+');
		if (!$activeLabels) {
			$labelId = getListFromStr($label_id);
			if (is_array($labelId)) {
				$activeLabels = $labelId;
			} else if ($modHelper->label_id) {
				$activeLabels[] = $modHelper->label_id;
			}
		}
		$displayFilters['label'] = $count;
	}
}
if (!isset($displayFilters['label']) && $label_id) {
	$modHelper->hiddenFilters['label_id'] = $label_id;
}

$displayDeliveryTimes = array();
if ($modHelper->params->show_delivery_times) {
	$displayDeliveryTimes = $modHelper->getDisplayDeliveryTimes();
	$count = count($displayDeliveryTimes);
	if ($count > $modHelper->params->once_option) {
		$activeDeliveryTimes = filterAllowValue($modHelper->app->getUserStateFromRequest($modHelper->contextfilter.'delivery_times', 'delivery_times', array()), 'int+');
		$displayFilters['delivery_time'] = $count;
	}
}

if ($modHelper->params->show_photos) {
	$activePhoto = $modHelper->app->getUserStateFromRequest($modHelper->contextfilter.'photo', 'photo');
	$displayFilters['photo'] = 1;
}

if ($modHelper->params->show_availabilitys && !$modHelper->jshopConfig->hide_product_not_avaible_stock) {
	$activeAvailability = $modHelper->app->getUserStateFromRequest($modHelper->contextfilter.'availability', 'availability');
	$displayFilters['availability'] = 1;
}

if ($modHelper->params->show_sales) {
	$activeSales = filterAllowValue($modHelper->app->getUserStateFromRequest($modHelper->contextfilter.'sales', 'sales'), 'int+');
	$displayFilters['sale'] = 1;
}

if ($modHelper->params->show_additional_prices) {
	$activeAdditionalPrices = filterAllowValue($modHelper->app->getUserStateFromRequest($modHelper->contextfilter.'additional_prices', 'additional_prices'), 'int+');
	$displayFilters['additional_price'] = 1;
}

if ($modHelper->params->show_reviews) {
	$activeReviews = filterAllowValue($modHelper->app->getUserStateFromRequest($modHelper->contextfilter.'reviews', 'reviews'), 'int+');
	$displayFilters['review'] = 1;
}

if (!count($displayFilters)) {
	return false;
}

JSFactory::loadCssFiles();
JSFactory::loadLanguageFile();

$document = JFactory::getDocument();
if (file_exists(__DIR__.'/js/'.$modHelper->params->template.'.js')) {
	$document->addScript(JURI::base(true).'/modules/'.$module->module.'/js/'.$modHelper->params->template.'.js');
}
if (file_exists(__DIR__.'/js/'.$modHelper->params->template.'.custom.js')) {
	$document->addScript(JURI::base(true).'/modules/'.$module->module.'/js/'.$modHelper->params->template.'.custom.js');
}
if (file_exists(__DIR__.'/css/'.$modHelper->params->template.'.css')) {
	$document->addStyleSheet(JURI::base(true).'/modules/'.$module->module.'/css/'.$modHelper->params->template.'.css');
}
if (file_exists(__DIR__.'/css/'.$modHelper->params->template.'.custom.css')) {
	$document->addStyleSheet(JURI::base(true).'/modules/'.$module->module.'/css/'.$modHelper->params->template.'.custom.css');
}
if ($modHelper->params->load_scripts == 1) {
	JHtml::_('script', 'jui/chosen.jquery.min.js', false, true, false, false, false);
	JHtml::_('stylesheet', 'jui/chosen.css', false, true);
}




require JModuleHelper::getLayoutPath($module->module, $modHelper->params->layout);
