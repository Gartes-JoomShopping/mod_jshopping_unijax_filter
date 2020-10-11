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




// print_r($modHelper->trackbarExtraField);
foreach($displayCharacteristics as $extraFieldId=>$extraFieldValues) {
	if (in_array($extraFieldId, $modHelper->params->show_characteristics_1)) {
		$show_characteristics = 1;
	} else if (in_array($extraFieldId, $modHelper->params->show_characteristics_2)) {
		$show_characteristics = 2;
	} else if (in_array($extraFieldId, $modHelper->params->show_characteristics_3)) {
		$show_characteristics = 3;
	} else if (in_array($extraFieldId, $modHelper->params->show_characteristics_4)) {
		$show_characteristics = 4;
	} else if (in_array($extraFieldId, $modHelper->params->show_characteristics_5)) {
		$show_characteristics = 5;
	}
	if (is_array($modHelper->allProductExtraFieldValueDetail[$extraFieldId]) && $modHelper->allProductExtraField[$extraFieldId]->type != 1) {
		$tmpExtraFieldsValue = array();
		foreach ($modHelper->allProductExtraFieldValueDetail[$extraFieldId] as $k=>$v) {
			if (in_array($k, $extraFieldValues)) {
				$tmpExtraFieldsValue[$k] = $k;
			}
		}
		if ($modHelper->params->sort_characteristics == 'name') {
			$tmpArr = $tmpRows = array();
			foreach ($tmpExtraFieldsValue as $key=>$row) {
				$tmpArr[$key] = $modHelper->allProductExtraFieldValueDetail[$extraFieldId][$row];
			}
			natsort($tmpArr);
			foreach ($tmpArr as $key=>$row) {
				$tmpRows[] = $tmpExtraFieldsValue[$key];
			}
			$tmpExtraFieldsValue = $tmpRows;
		}
		$extraFieldValues = $tmpExtraFieldsValue;
	} else {
		natsort($extraFieldValues);
		if (current($extraFieldValues) == '') {
			array_shift($extraFieldValues);
		}
	}
	$count = count($extraFieldValues);
	if ($count <= $modHelper->params->once_option) {
		continue;
	}


    if ( urlencode ($modHelper->json ) && is_array($modHelper->json->result)) {
        $enableCharacteristics = (array)$modHelper->json->result['characteristics[' . $extraFieldId . '][]'];
    } else {
        $enableCharacteristics = null;
    }
	
	

	
	if ($enableCharacteristics !== null && !count($enableCharacteristics)) {
		$labelClass = $optionsClass = $modHelper->uf_class;
	} else {
		$labelClass = (is_array($activeCharacteristics[$extraFieldId]) && count($activeCharacteristics[$extraFieldId])) ? ' uf_label_selected' : '';
		$optionsClass = '';
	}
	if ((in_array($extraFieldId, $modHelper->params->close_characteristics_id) || ($modHelper->params->qty_characteristics && $modHelper->params->qty_characteristics < $count)) && !count($activeCharacteristics[$extraFieldId])) {
		$labelClass .= ' uf_close';
	}
?>
<div class="uf_wrapper uf_wrapper_characteristics_<?php echo $extraFieldId ?>">
	<div id="uf_characteristics_<?php echo $extraFieldId ?>_label" class="uf_label_characteristics_<?php echo $extraFieldId ?> <?php echo $labelClass ?>">
		<?php if ($show_characteristics == 1 || $show_characteristics == 4) { ?>
		<span class="uf_trigon"></span>
		<?php } ?>
		<span><?php echo $modHelper->allProductExtraField[$extraFieldId]->name ?></span>
		<?php if ($modHelper->params->show_characteristics_desc && $modHelper->allProductExtraField[$extraFieldId]->description) { ?>
		<div class="uf_tooltip" onmouseover="unijaxFilter.tip(this,'show')" onmouseout="unijaxFilter.tip(this,'hide')"></div>
		<div class="uf_preview"><?php echo $modHelper->allProductExtraField[$extraFieldId]->description ?></div>
		<?php } ?>    
	</div>
	<div id="uf_characteristics_<?php echo $extraFieldId ?>" class="<?php if ($show_characteristics == 5) { ?>uf_options_input<?php } else { ?>uf_options <?php echo $optionsClass ?><?php } ?>">
	<?php if ($show_characteristics == 1 || $show_characteristics == 4) { ?>    
		<div class="uf_select_options chzn-container-multi">
			<ul id="uf_characteristics_<?php echo $extraFieldId ?>_select_options" class="chzn-choices"></ul>
		</div>
		<div class="uf_options_characteristic_<?php echo $extraFieldId ?><?php if ($modHelper->params->options_qnt && $modHelper->params->options_qnt < $count) { echo ' uf_overflow'; } ?><?php if ($show_characteristics == 1) { echo ' uf_hidecheckbox'; } ?>">
			<input type="hidden" name="characteristics[<?php echo $extraFieldId ?>][]" value="" />
			<?php
            
//            echo'<pre>';print_r( $extraFieldValues );echo'</pre>'.__FILE__.' '.__LINE__;
            
            
			foreach($extraFieldValues as $extraFieldValue) {
				if ($modHelper->allProductExtraField[$extraFieldId]->type==0 || $modHelper->allProductExtraField[$extraFieldId]->type==2) {
					if (!$extraFieldValueName = $modHelper->allProductExtraFieldValueDetail[$extraFieldId][$extraFieldValue]) {
						continue;
					}
				} else if ($modHelper->allProductExtraField[$extraFieldId]->type==3) {
					$extraFieldValueName = JText::_('JYES');
				} else {
					$extraFieldValueName = $extraFieldValue;
				}



                 



				if ($enableCharacteristics !== null && !in_array($extraFieldValue, $enableCharacteristics)) {
                    $extraFieldValue = urlencode($extraFieldValue);
					$inputClass = $modHelper->uf_class;
					$inputState = $modHelper->uf_disable;
				} else {

				     


//					$extraFieldValue = urlencode($extraFieldValue);
					
//					echo'<pre>';print_r( $extraFieldValue );echo'</pre>'.__FILE__.' '.__LINE__;
//					echo'<pre>';print_r( $activeCharacteristics[$extraFieldId] );echo'</pre>'.__FILE__.' '.__LINE__;
//					echo'<pre>';print_r( in_array($extraFieldValue, $activeCharacteristics[$extraFieldId]) );echo'</pre>'.__FILE__.' '.__LINE__;


					
					$inputClass = '';
					if (is_array($activeCharacteristics[$extraFieldId]) && in_array($extraFieldValue, $activeCharacteristics[$extraFieldId])) {
						$inputState = 'checked="checked"';
					} else {
						$inputState = '';
					}
				}

				$extraFieldValueId = 'uf_characteristic_'.$extraFieldId.'_'.str_replace('%','-',str_replace('+','_',$extraFieldValue));
			?>
			<div class="uf_input <?php echo $inputClass ?>">
				<input id="<?php echo $extraFieldValueId ?>" type="checkbox" name="characteristics[<?php echo $extraFieldId ?>][]" value="<?php echo $extraFieldValue ?>" <?php echo $inputState ?> onclick="unijaxFilter.updateForm(this)" />
				<label class="uf_input_label" for="<?php echo $extraFieldValueId ?>"><span><?php echo $extraFieldValueName ?></span></label>
				<?php if ($modHelper->params->show_count_pos) { ?>
				<span class="uf_count">
					<?php
					if ($enableCharacteristics !== null) {
						$pos = array_search($extraFieldValue, $enableCharacteristics);
						$posCount = 0;
						if ($pos !== false) {
							$c_json = (array)$modHelper->json->result['c_characteristics['.$extraFieldId.'][]'];
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
			<?php }


//            die(__FILE__ .' '. __LINE__ );




			?>
		</div>
	<?php } else if ($show_characteristics == 2 || $show_characteristics == 3) { ?>
		<?php if ($show_characteristics == 3) { ?>
		<input type="hidden" name="characteristics[<?php echo $extraFieldId ?>][]" value="" />
		<?php } ?>
		<select name="characteristics[<?php echo $extraFieldId ?>][]" data-placeholder="<?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_SELECT_FILTER'.($show_characteristics == 3 ? '_MULTI' : '')) ?>" class="uf_chosen" <?php if ($show_characteristics == 3) { ?>multiple="multiple"<?php } ?> onchange="unijaxFilter.updateForm(this)">
			<?php if ($show_characteristics == 2) { ?>
			<option value=""></option>
			<?php } ?>
		<?php
		foreach($extraFieldValues as $extraFieldValue) {
			if ($modHelper->allProductExtraField[$extraFieldId]->type==0 || $modHelper->allProductExtraField[$extraFieldId]->type==2) {
				$extraFieldValueName = $modHelper->allProductExtraFieldValueDetail[$extraFieldId][$extraFieldValue];
			} else if ($modHelper->allProductExtraField[$extraFieldId]->type==3) {
				$extraFieldValueName = JText::_('JYES');
			} else {
				$extraFieldValueName = $extraFieldValue;
			}
			if ($enableCharacteristics !== null && !in_array($extraFieldValue, $enableCharacteristics)) {
				$extraFieldValue = urlencode($extraFieldValue);
				$inputClass = $modHelper->uf_class;
				$inputState = $modHelper->uf_disable;
			} else {
				$extraFieldValue = urlencode($extraFieldValue);
				$inputClass = '';
				if (is_array($activeCharacteristics[$extraFieldId]) && in_array($extraFieldValue, $activeCharacteristics[$extraFieldId])) {
					$inputState = 'selected="selected"';
				} else {
					$inputState = '';
				}
			}
			$extraFieldValueId = 'uf_characteristic_'.$extraFieldId.'_'.str_replace('%','-',str_replace('+','_',$extraFieldValue));
			?>
			<option class="<?php echo $inputClass ?>" value="<?php echo $extraFieldValue ?>" <?php echo $inputState ?>><?php echo $extraFieldValueName ?></option>
		<?php } ?>
		</select>
	<?php } else {
		if (isset($activeCharacteristics[$extraFieldId])) {
			$leftValue = $activeCharacteristics[$extraFieldId][0];
			$rightValue = $activeCharacteristics[$extraFieldId][1];
		} else {
			$leftValue = '';
			$rightValue = '';
		}
	?>
		<div class="uf_trackbar_inputs input-prepend input-append">
			<input type="text" name="characteristics[<?php echo $extraFieldId ?>][]" id="uf_characteristics_<?php echo $extraFieldId ?>_from" placeholder="<?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_FROM')?>" value="<?php echo $leftValue ?>" onkeyup="unijaxFilter.updateForm(this, <?php echo $modHelper->params->input_delay ?>)" <?php if ($modHelper->params->show_trackbar_characteristics){ ?>data-limit-from="<?php echo $trackbarParams['characteristics_'.$extraFieldId]->leftLimit ?>"<?php } ?> />
			<span class="uf_inputreset" onclick="unijaxFilter.clearInput('characteristics_<?php echo $extraFieldId ?>')"><?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_PRICE_SEPARATOR') ?></span>
			<input type="text" name="characteristics[<?php echo $extraFieldId ?>][]" id="uf_characteristics_<?php echo $extraFieldId ?>_to" placeholder="<?php echo JText::_('MOD_JSHOPPING_UNIJAXFILTER_TO') ?>" value="<?php echo $rightValue ?>" onkeyup="unijaxFilter.updateForm(this, <?php echo $modHelper->params->input_delay ?>)" <?php if ($modHelper->params->show_trackbar_characteristics){ ?>data-limit-to="<?php echo $trackbarParams['characteristics_'.$extraFieldId]->rightLimit ?>"<?php } ?> />
		</div>
		<?php if ($modHelper->params->show_trackbar_characteristics){ ?>
		<div id="uf_characteristics_<?php echo $extraFieldId ?>_trackbar" class="uf_trackbar"></div>
		<?php } ?>    
	<?php } ?>
	</div>
</div>
<?php } ?>