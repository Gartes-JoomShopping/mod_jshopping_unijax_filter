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

;var unijaxFilter = unijaxFilter || {};

unijaxFilter.$ = jQuery;

unijaxFilter.clearForm = function() {
	var $ = unijaxFilter.$;
    $('form[name=jshop_unijax_filter] input[type=checkbox]:checked').prop('checked',false);
    $('form[name=jshop_unijax_filter] select option:selected').prop('selected',false);
    $('form[name=jshop_unijax_filter] input[type=radio][value=0]').prop('checked',true);
    $('form[name=jshop_unijax_filter] input[type=text]').val('');
	$('#jshop_unijax_filter').submit();
}

unijaxFilter.getSelectInputHtml = function(elem) {
	return '<li class="search-choice"><span>' + unijaxFilter.$(elem).next('label').text() + '</span><a href="javascript:void(0)" class="search-choice-close" onclick="unijaxFilter.removeSelectInput(\''+elem.id+'\', this)"></a></li>';
}

unijaxFilter.hideSelectInput = function(elem) {
	if (elem.type == 'checkbox' && elem.checked && unijaxFilter.$(elem).parent().parent().hasClass('uf_hidecheckbox') && elem.id != 'uf_sale' && elem.id != 'uf_review' && elem.id != 'uf_additional_price') {
		unijaxFilter.$(elem.parentNode).addClass('uf_hide');
		return elem.parentNode.parentNode.parentNode.id;
	} else {
		return false;
	}
}

unijaxFilter.setSelectInputHtml = function(elem) {
	var parentId = unijaxFilter.hideSelectInput(elem);
	if (parentId) {
		unijaxFilter.$('#'+parentId+'_select_options').append(unijaxFilter.getSelectInputHtml(elem));
	}
}

unijaxFilter._updateForm = function(elem) {
	var $ = unijaxFilter.$,
		options_id,
		form = $('#jshop_unijax_filter'),
		prepareUnijaxFilter = 1;
	if (typeof(elem) == 'object') {
		if (elem.type == 'select-multiple') {
			$(elem).next().removeClass('chzn-container-active');
			options_id = elem.parentNode.id;
		} else if (elem.type == 'checkbox' && elem.checked) {
			unijaxFilter.setSelectInputHtml(elem);
			options_id = elem.parentNode.parentNode.parentNode.id;
		} else if (elem.type == 'text') {
			options_id = elem.parentNode.parentNode.id;
			if (elem.value != '') {
				elem.value = parseFloat(elem.value.replace(',','.'));
			}
		} else {
			options_id = elem.parentNode.id;
		}
		$('#'+options_id+'_label').addClass('uf_label_selected');
		if (unijaxFilter.show_immediately && (!unijaxFilter.use_ajax || unijaxFilter.need_redirect)) {
			form.submit();
			return false;
		} else if (!unijaxFilter.use_ajax) {
			return false;
		}
		prepareUnijaxFilter = 2;
		if (unijaxFilter.preload_el) {
			$(unijaxFilter.preload_el).addClass('unijax_filter_preload');
		}
		$('body').trigger('onBeforeUnijaxFilterUpdateProductList');
		if (typeof(unijaxFilter.beforeUpdateProductList) === 'function') {
			unijaxFilter.beforeUpdateProductList();
		}
	} else if (!unijaxFilter.use_ajax) {
		return false;
	}
	var count = $('#uf_count_product');
	count.html('<span class="uf_count_loader"></span>');
	unijaxFilter.xhr = $.ajax({
		type: 'POST',
		dataType: 'text',
		url: form.prop('action'), 
		data : form.serialize()+'&prepareUnijaxFilter='+prepareUnijaxFilter,
		cache: false,  
		success: function(json){
			var indexJSON = json.indexOf('{"hide_non_active"');
			if (indexJSON > 0) {
				json = json.substring(indexJSON);
			} else if (indexJSON < 0) {
				console.warn('Json not found');
				form.submit();
				return false;
			}
			json = $.parseJSON(json);
			count.html(json['total']);
			if (unijaxFilter.preload_el) {
				$(unijaxFilter.preload_el).removeClass('unijax_filter_preload');
			}
			if (prepareUnijaxFilter == 2 && unijaxFilter.show_immediately) {
				var viewTarget, viewTargetAttr, viewJson = $(json['view']);
				if (viewTargetAttr = viewJson.prop('id')) {
					viewTarget = $(viewJson.prop('tagName')+'#'+viewTargetAttr);
				} else if (viewTargetAttr = viewJson.prop('class')) {
					viewTarget = $(viewJson.prop('tagName')+'.'+viewTargetAttr+':first');
				}
				if (typeof(viewTarget) == 'object' && viewTarget[0] !== undefined) {
					viewTarget.html(viewJson.html());
					$('body').trigger('onAfterUnijaxFilterUpdateProductList');
					if (typeof(unijaxFilter.afterUpdateProductList) === 'function') {
						unijaxFilter.afterUpdateProductList();
					}
				} else {
					console.warn('View target not found');
					form.submit();
					return false;
				}
			}
			if (json['hide_non_active'] != 2) {
				if (json['hide_non_active'] == 0) {
					var uf_class = 'uf_hide';
				} else {
					var uf_class = 'uf_disabled';
				}
				$.each(json['range'], function(el, range) {
					var inputFrom = $('#uf_'+el+'_from');
					var inputTo = $('#uf_'+el+'_to');
					var valueFrom = inputFrom.val();
					var valueTo = inputTo.val();
					if (valueFrom != '' || valueTo != '') {
						$('#uf_'+el+'_label').addClass('uf_label_selected');
					} else {
						$('#uf_'+el+'_label').removeClass('uf_label_selected');
					}
					if (valueFrom == '') {
						valueFrom = range['from'];
					} else if (valueFrom < range['from']) {
						valueFrom = range['from'];
					}
					if (valueTo == '') {
						valueTo = range['to'];
					} else if (valueTo > range['to']) {
						valueTo = range['to'];
					}
					inputFrom.data('limit-from', range['from']);
					inputTo.data('limit-to', range['to']);
					if (range['from'] === false  || range['to'] === false) {
						inputFrom.attr('readonly','readonly');
						inputTo.attr('readonly','readonly');
						$('#uf_'+el+', #uf_'+el+'_label').addClass(uf_class);
					} else {
						inputFrom.attr('readonly',false);
						inputTo.attr('readonly',false);
						$('#uf_'+el+', #uf_'+el+'_label').removeClass(uf_class);
					}
				});
				unijaxFilter.trackbar(0);

				var uf_options_id, inArray, qty, selectedObj = {}, options_select = [], options_enabled = [];
				$('#jshop_unijax_filter input[type=checkbox], #jshop_unijax_filter select').each(function(i,el) {
					uf_options_id = $(el).closest('.uf_options').attr('id');
					if (el.type == 'checkbox') {
						inArray = $.inArray(el.value, json['result'][el.name] );
						if (inArray < 0) {
							el.disabled = true;
							el.checked = false;
							$(el.parentNode).removeClass('uf_hide');
							$(el.parentNode).addClass(uf_class);
							$(el).parent().find('.uf_count').html('(0)');
						} else {
							if (json['result']['c_'+el.name]) {
								$(el).parent().find('.uf_count').html('('+json['result']['c_'+el.name][inArray]+')');
							}
							if (!el.checked) {
								el.disabled = false;
								$(el.parentNode).removeClass(uf_class);
								options_enabled[uf_options_id] = 1;
							} else {
								options_select[uf_options_id] = 1;
							}
						}
						var parentId = unijaxFilter.hideSelectInput(el);
						if (parentId) {
							if (typeof(selectedObj[parentId]) === 'undefined') {
								selectedObj[parentId] = unijaxFilter.getSelectInputHtml(el);
							} else {
								selectedObj[parentId] += unijaxFilter.getSelectInputHtml(el);
							}
						}
					} else {
						$(el).find('option[value!=""]').each(function() {
							if ($.inArray(this.value, json['result'][el.name] )<0) {
								this.disabled = true;
								this.selected = false;
								$(this).addClass(uf_class);
							} else if (!this.selected) {
								this.disabled = false;
								$(this).removeClass(uf_class);
								options_enabled[uf_options_id] = 1;
							} else if ((uf_options_id != 'uf_photos' && uf_options_id != 'uf_availabilitys') || this.value != 0) {
								options_select[uf_options_id] = 1;
							}
						});
					}
				});
				$('.uf_options').each(function() {
					var label = $('#'+this.id+'_label');
					var options = $(this);
					if (this.id in options_select) {
						label.addClass('uf_label_selected');
						label.removeClass(uf_class);
						options.removeClass(uf_class);
					} else if (this.id in options_enabled) {
						label.removeClass('uf_label_selected');
						label.removeClass(uf_class);
						options.removeClass(uf_class);
					} else {
						label.removeClass('uf_label_selected');
						label.addClass(uf_class);
						options.addClass(uf_class);
					}
				});
				for (var id in selectedObj) {
					$('#'+id+'_select_options').html(selectedObj[id]);
				}
				unijaxFilter.setOverflowHeight();
				$('.uf_chosen').trigger('liszt:updated');
			}
		}  
	});  
}

unijaxFilter.updateForm = function(el, timeout) {
	clearTimeout(unijaxFilter.timeout);
	if (unijaxFilter.xhr) {
		unijaxFilter.xhr.abort();
	}
	if (parseInt(timeout) > 0) {
		unijaxFilter.timeout = setTimeout(function() {
			unijaxFilter._updateForm(el);
		}, timeout);
	} else {
		unijaxFilter._updateForm(el);
	}
}		

unijaxFilter.clearInput = function(el) {
	var needReset = 0;
	var inputs = unijaxFilter.$('#uf_'+el).find('input[type=text]');
	inputs.each(function(){
		if (this.value != '') {
			this.value = '';
			needReset = 1;
		}
	});
	if (needReset) {
		unijaxFilter._updateForm(inputs[0]);
	}
}

unijaxFilter.removeSelectInput = function(id, el) {
	var $ = unijaxFilter.$;
	var input = document.getElementById(id);
	$(input).prop('checked',false).parent().removeClass('uf_hide');
	$(el.parentNode).remove();
	unijaxFilter._updateForm(input);
}

unijaxFilter.setOverflowHeight = function() {
	var $ = unijaxFilter.$;
	$('.uf_overflow').each(function() {
		var el = $(this), overflow = 0, el_height = 0;
		el.children('.uf_input:not(.uf_hide)').each(function(i) {
			if (unijaxFilter.options_qnt < i+1) {
				overflow = 1;
				return false;
			}
			el_height += $(this).outerHeight(true);
		});
		if (overflow) {
			el.removeClass('uf_no_overflow');
			el.height(el_height);
		} else {
			el.addClass('uf_no_overflow');
			el.css('height','');
		}
	});
}		

unijaxFilter.tip = function(elem, mode) {
	var $ = unijaxFilter.$;
	if (mode=='show') {
		$('#uf_tooltip').remove();
		$('body').append('<div id="uf_tooltip">'+$(elem).next().html()+'</div>');
		$('#uf_tooltip')
			.css('top',($(elem).offset().top - 5) + 'px')
			.css('left',($(elem).offset().left + 20) + 'px')
			.fadeIn('fast');
    } else {
		$('#uf_tooltip').remove();
    }
}

unijaxFilter.trackbar = function(clearLimits) {
	var $ = unijaxFilter.$;
	$('.uf_trackbar').each(function(){
		$(this).trackbar({'clearLimits' : clearLimits});
	});
}

unijaxFilter.toggleOptions = function(elem) {
	var $ = unijaxFilter.$;
	$(elem).toggleClass('uf_close');
}

unijaxFilter.showCanvas = function() {
	var $ = unijaxFilter.$;
	$('#unijax_filter_container').addClass('unijax_filter_container_open');
	$(document).on('click touchstart touchmove', unijaxFilter.hideCanvas);
	$('body').addClass('unijax_filter_container_open');
}

unijaxFilter.hideCanvas = function(e) {
	var $ = unijaxFilter.$,
		touchstartX;
	if ($(e.target).closest('#unijax_filter_block').length) {
		return;
	}
	if (e.type == 'click') {
		e.stopPropagation();
		e.preventDefault();
	} else if (e.type == 'touchstart') {
		touchstartX = e.originalEvent.changedTouches[0].clientX;
	} else {
		if (!touchstartX || e.originalEvent.changedTouches[0].clientX > touchstartX) {
			return;
		}
		touchstartX = 0;
	}
	$('#unijax_filter_container').removeClass('unijax_filter_container_open');
	$(document).off('click', unijaxFilter.hideCanvas);
	$('body').removeClass('unijax_filter_container_open');
}

unijaxFilter.$(function($) {
	var params = $('#jshop_unijax_filter').data('params'),
		touchstartX;
	for(var key in params) {
		unijaxFilter[key] = params[key];
	}
	unijaxFilter.trackbar(unijaxFilter.use_ajax);
	$('#jshop_unijax_filter input[type=checkbox]:checked').each(function() {
		unijaxFilter.setSelectInputHtml(this);
	});
	$(document).on('click', '.uf_wrapper [class^="uf_label_"]', function(){
		if ($(this).find('.uf_trigon').length) {
			unijaxFilter.toggleOptions(this);
		}
	});
	$(document).on('touchstart touchmove', function(e){
		if (e.type == 'touchstart') {
			touchstartX = e.originalEvent.changedTouches[0].clientX;
		} else {
			if (e.originalEvent.changedTouches[0].clientX >= touchstartX && touchstartX <= 44) {
				touchstartX = 1000;
				unijaxFilter.showCanvas();
			}
		}
	});
	$('#unijax_filter_offcanvas_button').on('click', function(e){
		e.stopPropagation();
		e.preventDefault();
		unijaxFilter.showCanvas();
	});
	unijaxFilter.setOverflowHeight();
	if($().chosen) {
		$('.uf_chosen').chosen({
			disable_search_threshold : 10,
			allow_single_deselect : true
		});
	}
	unijaxFilter.updateForm();
});

(function($) {
$.fn.trackbar = function(op, id){
	var inputFrom = $('#'+this.attr('id').replace('trackbar', 'from'));
	var inputTo = $('#'+this.attr('id').replace('trackbar', 'to'));
	var leftLimit = parseFloat(inputFrom.data('limit-from'));
	var rightLimit = parseFloat(inputTo.data('limit-to'));
	var leftValue = inputFrom.val() == '' ? '' : parseFloat(inputFrom.val());
	var rightValue = inputTo.val() == '' ? '' : parseFloat(inputTo.val());
	if (leftValue && (leftValue < leftLimit || leftValue > rightLimit)) {
		leftValue = '';
	}
	if (rightValue && (rightValue > rightLimit || rightValue < leftLimit)) {
		rightValue = '';
	}
	if (leftValue && rightValue && leftValue > rightValue) {
		leftValue = rightValue = '';
	}
	op = $.extend({
		onMove: function(){
			inputFrom.val(parseFloat(this.leftValue.toFixed(10)));
			inputTo.val(parseFloat(this.rightValue.toFixed(10)));
			unijaxFilter.updateForm(inputFrom[0], unijaxFilter.input_delay);
		},
		onMoveLeft: function(leftOut){
			inputFrom.val(parseFloat(this.leftValue.toFixed(10)));
			unijaxFilter.updateForm(inputFrom[0], unijaxFilter.input_delay);
		},
		onMoveRight: function(rightOut){
			inputTo.val(parseFloat(this.rightValue.toFixed(10)));
			unijaxFilter.updateForm(inputTo[0], unijaxFilter.input_delay);
		},
		width: this.outerWidth(true),
		leftLimit: leftLimit,
		leftValue: leftValue,
		leftValueLimit: leftLimit,
		rightLimit: rightLimit,
		rightValue: rightValue,
		rightValueLimit: rightLimit,
		jq: this
	},op);
	$.trackbar.getObject(id).init(op);
}
$.trackbar = {
	archive : [],
	getObject : function(id) {
		if(typeof id == 'undefined')id = this.archive.length;
		if(typeof this.archive[id] == "undefined"){
			this.archive[id] = new this.hotSearch(id);
		}
		return this.archive[id];
	}
};
$.trackbar.hotSearch = function(id) {
	this.id = id;
	this.leftWidth = 0;
	this.leftLimitWidth = 0;
	this.rightWidth = 0;
	this.rightLimitWidth = 0;
	this.width = 0;
	this.intervalWidth = 0;
	this.leftLimit = 0;
	this.leftValue = 0;
	this.leftValueLimit = 0;
	this.rightLimit = 0;
	this.rightValue = 0;
	this.rightValueLimit = 0;
	this.valueInterval = 0;
	this.widthRem = 10;
	this.valueWidth = 0;
	this.roundUp = 0;
	this.x0 = 0; this.y0 = 0;
	this.blockX0 = 0; 
	this.rightX0 = 0; 
	this.leftX0 = 0;
	this.moveState = false;
	this.moveIntervalState = false;
	this.debugMode = false;
	this.clearLimits = false;
	this.clearValues = true;
	this.onMove = null;
	this.onMoveLeft = null;
	this.onMoveRight = null;
	this.leftBlock = null;
	this.leftLimitBlock = null;
	this.rightBlock = null;
	this.rightLimitBlock = null;
	this.leftBegun = null;
	this.rightBegun = null;
	this.centerBlock = null;
	this.itWasMove = false;
}
$.trackbar.hotSearch.prototype = {
	ERRORS : {
		1 : "Ошибка при инициализации объекта",
		2 : "Левый бегунок не найден",
		3 : "Правый бегунок не найден",
		4 : "Левая область ресайза не найдена",
		5 : "Правая область ресайза не найдена",
		6 : "Не задана ширина области бегунка",
		7 : "Не указано максимальное изменяемое значение",
		8 : "Не указана функция-обработчик значений",
		9 : "Не указана область клика"
	},
	LEFT_BLOCK_PREFIX : "leftBlock",
	RIGHT_BLOCK_PREFIX : "rightBlock",
	LEFT_BEGUN_PREFIX : "leftBegun",
	RIGHT_BEGUN_PREFIX : "rightBegun",
	CENTER_BLOCK_PREFIX : "centerBlock",

	gebi : function(id) {
		return this.jq.find('.'+id)[0];
	},
	addHandler : function(object, event, handler, useCapture) {
		$(object).bind(event, handler);
	},
	defPosition : function(event) {
		return {x:(event.pageX?event.pageX:event.originalEvent.touches[0].pageX), y:(event.pageY?event.pageY:event.originalEvent.touches[0].pageY)}; 
	},
	absPosition : function(obj) { 
		var x = y = 0; 
		while(obj) { 
			x += obj.offsetLeft; 
			y += obj.offsetTop; 
			obj = obj.offsetParent; 
		} 
		return {x:x, y:y}; 
	},

	debug : function(keys) {
		if (!this.debugMode) return;
		var mes = "";
		for (var i = 0; i < keys.length; i++) mes += this.ERRORS[keys[i]] + " : ";
		mes = mes.substring(0, mes.length - 3);
		alert(mes);
	},
	init : function(hash) {
		this.isTouchDevice = 'ontouchstart' in document.documentElement;
		this.leftLimit = hash.leftLimit || this.leftLimit;
		this.rightLimit = hash.rightLimit || this.rightLimit;
		this.width = hash.width || this.width;
		this.onMove = hash.onMove || this.onMove;
		this.onMoveLeft = hash.onMoveLeft || this.onMoveLeft;
		this.onMoveRight = hash.onMoveRight || this.onMoveRight;
		this.clearLimits = hash.clearLimits || this.clearLimits;
		this.clearValues = hash.clearValues || this.clearValues;
		this.roundUp = hash.roundUp || this.roundUp;
		this.jq = hash.jq;

		this.jq.html('<table' + (this.width ? ' style="width:'+this.width+'px;"' : '') + 'class="trackbar" onSelectStart="return false">\
			<tr>\
				<td class="l"><div class="leftBlock"><span></span><span class="limit"></span><div class="leftBegun" ondragstart="return false"></div></div></td>\
				<td class="centerBlock"><div class="c"></div></td>\
				<td class="r"><div class="rightBlock"><span></span><span class="limit"></span><div class="rightBegun" ondragstart="return false"></div></div></td>\
			</tr>\
		</table>');

		if (this.onMove == null || this.onMoveLeft == null || this.onMoveRight == null) {
			this.debug([1,8]);
				return;
		}
		this.leftBegun = this.gebi(this.LEFT_BEGUN_PREFIX);
		if (this.leftBegun == null) {
			this.debug([1,2]);
				return;
		}
		this.rightBegun = this.gebi(this.RIGHT_BEGUN_PREFIX);
		if (this.rightBegun == null) {
			this.debug([1,3]);
				return;
		}
		this.leftBlock = this.gebi(this.LEFT_BLOCK_PREFIX);
		if (this.leftBlock == null) {
			this.debug([1,4]);
				return;
		}
		this.rightBlock = this.gebi(this.RIGHT_BLOCK_PREFIX);
		if (this.rightBlock == null) {
			this.debug([1,5]);
				return;
		}
		this.centerBlock = this.gebi(this.CENTER_BLOCK_PREFIX);
		if (this.centerBlock == null) {
			this.debug([1,9]);
				return;
		}
		if (!this.width) {
			this.debug([1,6]);
				return;
		}
		if (!this.rightLimit) {
			this.debug([1,7]);
				return;
		}
		
		this.widthRem = $(this.leftBegun).outerWidth();

		this.valueWidth = this.width - 2 * this.widthRem;
		this.rightValue = hash.rightValue || this.rightLimit;
		this.rightValueLimit = hash.rightValueLimit || this.rightLimit;
		this.rightValue = this.rightValue > this.rightValueLimit ? this.rightValueLimit : this.rightValue;
		this.leftValue = hash.leftValue || this.leftLimit;
		this.leftValueLimit = hash.leftValueLimit || this.leftLimit;
		this.leftValue = this.leftValue < this.leftValueLimit ? this.leftValueLimit : this.leftValue;
		this.valueInterval = this.rightLimit - this.leftLimit ? this.rightLimit - this.leftLimit : 1;
		this.leftWidth = parseInt((this.leftValue - this.leftLimit) / this.valueInterval * this.valueWidth) + this.widthRem;
		this.leftWidthLimit = parseInt((this.leftValueLimit - this.leftLimit) / this.valueInterval * this.valueWidth) + this.widthRem;
		this.rightWidth = this.valueWidth - parseInt((this.rightValue - this.leftLimit) / this.valueInterval * this.valueWidth) + this.widthRem;
		this.rightWidthLimit = this.valueWidth - parseInt((this.rightValueLimit - this.leftLimit) / this.valueInterval * this.valueWidth) + this.widthRem;

		if (!this.clearLimits) {
			this.leftBlock.firstChild.nextSibling.innerHTML = this.leftLimit;
			this.rightBlock.firstChild.nextSibling.innerHTML = this.rightLimit;
		}

		this.setCurrentState();

		var _this = this;
		this.addHandler (
			document,
			this.isTouchDevice ? "touchmove" : "mousemove",
			function(evt) {
				if (_this.moveState) {
					if (parent.xhr) {
						parent.xhr.abort();
						parent.xhr = null;
					}
					_this.moveHandler(evt);
				}
				if (_this.moveIntervalState) _this.moveIntervalHandler(evt);
			}
		);
		this.addHandler (
			document,
			this.isTouchDevice ? "touchend" : "mouseup",
			function() {
				_this.moveState = false;
				_this.moveIntervalState = false;
			}
		);
		this.addHandler (
			this.leftBegun,
			this.isTouchDevice ? "touchstart" : "mousedown",
			function(evt) {
				if (parent.xhr) {
					parent.xhr.abort();
					parent.xhr = null;
				}
				evt = evt || window.event;
				if (evt.preventDefault) evt.preventDefault();
				evt.returnValue = false;
				_this.moveState = "left";
				_this.x0 = _this.defPosition(evt).x;
				_this.blockX0 = _this.leftWidth;
				_this.moveHandler(evt);
			}
		);
		this.addHandler (
			this.rightBegun,
			this.isTouchDevice ? "touchstart" : "mousedown",
			function(evt) {
				if (parent.xhr) {
					parent.xhr.abort();
					parent.xhr = null;
				}
				evt = evt || window.event;
				if (evt.preventDefault) evt.preventDefault();
				evt.returnValue = false;
				_this.moveState = "right";
				_this.x0 = _this.defPosition(evt).x;
				_this.blockX0 = _this.rightWidth;
				_this.moveHandler(evt);
			}
		);
		this.addHandler (
			this.centerBlock,
			this.isTouchDevice ? "touchstart" : "mousedown",
			function(evt) {
				if (parent.xhr) {
					parent.xhr.abort();
					parent.xhr = null;
				}
				evt = evt || window.event;
				if (evt.preventDefault) evt.preventDefault();
				evt.returnValue = false;
				_this.moveIntervalState = true;
				_this.moveState = "center";
				_this.intervalWidth = _this.width - _this.rightWidth - _this.leftWidth;
				_this.x0 = _this.defPosition(evt).x;
				_this.rightX0 = _this.rightWidth; 
				_this.leftX0 = _this.leftWidth;
			}
		),
		this.addHandler (
			this.centerBlock,
			'click',
			function(evt) {
				if (parent.xhr) {
					parent.xhr.abort();
					parent.xhr = null;
				}
				if (!_this.itWasMove) _this.clickMove(evt);
				_this.itWasMove = false;
			}
		);
		this.addHandler (
			this.leftBlock,
			'click',
			function(evt) {
				if (parent.xhr) {
					parent.xhr.abort();
					parent.xhr = null;
				}
				if (!_this.itWasMove)_this.clickMoveLeft(evt);
				_this.itWasMove = false;
			}
		);
		this.addHandler (
			this.rightBlock,
			'click',
			function(evt) {
				if (parent.xhr) {
					parent.xhr.abort();
					parent.xhr = null;
				}
				if (!_this.itWasMove)_this.clickMoveRight(evt);
				_this.itWasMove = false;
			}
		);
	},
	clickMoveRight : function(evt) {
		evt = evt || window.event;
		if (evt.preventDefault) evt.preventDefault();
		evt.returnValue = false;
		var x = this.defPosition(evt).x - this.absPosition(this.rightBlock).x;
		var w = this.rightBlock.offsetWidth;
		if (x <= 0 || w <= 0 || w < x || (w - x) < this.widthRem) return;
		this.rightWidth = (w - x);
		this.rightCounter();

		this.setCurrentState();
		this.onMoveRight();
	},
	clickMoveLeft : function(evt) {
		evt = evt || window.event;
		if (evt.preventDefault) evt.preventDefault();
		evt.returnValue = false;
		var x = this.defPosition(evt).x - this.absPosition(this.leftBlock).x;
		var w = this.leftBlock.offsetWidth;
		if (x <= 0 || w <= 0 || w < x || x < this.widthRem) return;
		this.leftWidth = x;
		this.leftCounter();

		this.setCurrentState();
		this.onMoveLeft();
	},
	clickMove : function(evt) {
		evt = evt || window.event;
		if (evt.preventDefault) evt.preventDefault();
		evt.returnValue = false;
		var x = this.defPosition(evt).x - this.absPosition(this.centerBlock).x;
		var w = this.centerBlock.offsetWidth;
		if (x <= 0 || w <= 0 || w < x) return;
		if (x >= w / 2) {
			this.rightWidth += (w - x);
			this.rightCounter();
			this.setCurrentState();
			this.onMoveRight();
		} else {
			this.leftWidth += x;
			this.leftCounter();
			this.setCurrentState();
			this.onMoveLeft();
		}
	},
	setCurrentState : function() {
		this.leftBlock.style.width = this.leftWidth + 'px';
		if (!this.clearValues) this.leftBlock.firstChild.innerHTML = this.leftValue;
		this.rightBlock.style.width = this.rightWidth + 'px';
		if (!this.clearValues) this.rightBlock.firstChild.innerHTML = this.rightValue;
	},
	moveHandler : function(evt) {
		this.itWasMove = true;
		evt = evt || window.event;
		if (evt.preventDefault) evt.preventDefault();
		evt.returnValue = false;
		if (this.moveState == 'left') {
			this.leftWidth = this.blockX0 + this.defPosition(evt).x - this.x0;
			this.leftCounter();
			this.setCurrentState();
			this.onMoveLeft(this.leftOut);
		}
		if (this.moveState == 'right') {
			this.rightWidth = this.blockX0 + this.x0 - this.defPosition(evt).x;
			this.rightCounter();
			this.setCurrentState();
			this.onMoveRight(this.rightOut);
		}
	},
	moveIntervalHandler : function(evt) {
		this.itWasMove = true;
		evt = evt || window.event;
		if (evt.preventDefault) evt.preventDefault();
		evt.returnValue = false;
		var dX = this.defPosition(evt).x - this.x0;
		if (dX > 0) {
			this.rightWidth = this.rightX0 - dX > this.rightWidthLimit ? this.rightX0 - dX : this.rightWidthLimit;
			this.leftWidth = this.width - this.rightWidth - this.intervalWidth;
		} else {
			this.leftWidth = this.leftX0 + dX > this.leftWidthLimit ? this.leftX0 + dX : this.leftWidthLimit;
			this.rightWidth = this.width - this.leftWidth - this.intervalWidth;
		}
		this.rightCounter();
		this.leftCounter();
		this.setCurrentState();
		this.onMove();
	},
	updateRightValue : function(rightValue) {
		try {
			this.rightValue = parseInt(rightValue);
			this.rightValue = this.rightValue < this.leftLimit ? this.leftLimit : this.rightValue;
			this.rightValue = this.rightValue > this.rightLimit ? this.rightLimit : this.rightValue;
			this.rightValue = this.rightValue < this.leftValue ? this.leftValue : this.rightValue;
			this.rightWidth = this.valueWidth - parseInt((this.rightValue - this.leftLimit) / this.valueInterval * this.valueWidth) + this.widthRem;
			this.rightWidth = isNaN(this.rightWidth) ? this.widthRem : this.rightWidth;
			this.setCurrentState();
		} catch(e) {}
	},
	rightCounter : function() {
		this.rightWidth = (this.rightWidth > this.width - this.leftWidth ? this.width - this.leftWidth : this.rightWidth) - 1;
		if (this.rightWidth < this.rightWidthLimit) {
			this.rightWidth = this.rightWidthLimit;
			this.rightValue = this.rightValueLimit;
			this.rightOut = true;
		} else {
			this.rightValue = this.leftLimit + this.valueInterval - parseInt((this.rightWidth - this.widthRem) / this.valueWidth * this.valueInterval);
			this.rightOut = false;
		}
		if (this.roundUp) this.rightValue = parseInt(this.rightValue / this.roundUp) * this.roundUp;
		if (this.leftWidth + this.rightWidth >= this.width) this.rightValue = this.leftValue*1+1;
		if (this.rightValue > this.rightLimit) this.rightValue = this.rightLimit;
	},
	updateLeftValue : function(leftValue) {
		try {
			this.leftValue = parseInt(leftValue);
			this.leftValue = this.leftValue < this.leftLimit ? this.leftLimit : this.leftValue;
			this.leftValue = this.leftValue > this.rightLimit ? this.rightLimit : this.leftValue;
			this.leftValue = this.rightValue < this.leftValue ? this.rightValue : this.leftValue;
			this.leftWidth = parseInt((this.leftValue - this.leftLimit) / this.valueInterval * this.valueWidth) + this.widthRem;
			this.leftWidth = isNaN(this.leftWidth) ? this.widthRem : this.leftWidth;
			this.setCurrentState();
		} catch(e) {}
	},
	leftCounter : function() {
		this.leftWidth = (this.leftWidth > this.width - this.rightWidth ? this.width - this.rightWidth : this.leftWidth) - 1;
		if (this.leftWidth < this.leftWidthLimit) {
			this.leftWidth = this.leftWidthLimit;
			this.leftValue = this.leftValueLimit;
			this.leftOut = true;
		} else {
			this.leftValue = this.leftLimit + parseInt((this.leftWidth - this.widthRem) / this.valueWidth * this.valueInterval);
			this.leftOut = false;
		}
		if (this.roundUp) this.leftValue = parseInt(this.leftValue / this.roundUp) * this.roundUp;
		if (this.leftWidth + this.rightWidth >= this.width) this.leftValue = this.rightValue*1-1;
		if (this.leftValue < this.leftLimit) this.leftValue = this.leftLimit;
	}
}

})(unijaxFilter.$);  	        	      	    	        	          	