(function($){
	var initLayout = function() {
		var hash = window.location.hash.replace('#', '');
		var currentTab = $('ul.navigationTabs a')
							.bind('click', showTab)
							.filter('a[rel=' + hash + ']');
		if (currentTab.size() == 0) {
			currentTab = $('ul.navigationTabs a:first');
		}
		showTab.apply(currentTab.get(0));
		$('#colorpickerHolder').ColorPicker({flat: true});
		$('#colorpickerHolder2').ColorPicker({
			flat: true,
			color: $('#HiddenColor2').val(),
			onSubmit: function(hsb, hex, rgb, el) {
				$('#colorSelector2 div').css('backgroundColor', '#' + hex);
				$('#HiddenColor2').val(hex);
				$('#colorpickerHolder2').stop().animate({height: widt ? 0 : 173}, 500);
				widt = !widt;				
			}
		});
		$('#colorpickerHolder3').ColorPicker({
			flat: true,
			color: $('#HiddenColor3').val(),
			onSubmit: function(hsb, hex, rgb, el) {
				$('#colorSelector3 div').css('backgroundColor', '#' + hex);
				$('#HiddenColor3').val(hex);
				$('#colorpickerHolder3').stop().animate({height: widt ? 0 : 173}, 500);
				widt = !widt;
			}
		});		
		$('#colorpickerHolder4').ColorPicker({
			flat: true,
			color: $('#HiddenColor4').val(),
			onSubmit: function(hsb, hex, rgb, el) {
				$('#colorSelector4 div').css('backgroundColor', '#' + hex);
				$('#HiddenColor4').val(hex);
				$('#colorpickerHolder4').stop().animate({height: widt ? 0 : 173}, 500);
				widt = !widt;
			}
		});				
		$('#colorpickerHolder5').ColorPicker({
			flat: true,
			color: $('#HiddenColor5').val(),
			onSubmit: function(hsb, hex, rgb, el) {
				$('#colorSelector5 div').css('backgroundColor', '#' + hex);
				$('#HiddenColor5').val(hex);
				$('#colorpickerHolder5').stop().animate({height: widt ? 0 : 173}, 500);
				widt = !widt;
			}
		});		
		$('#colorpickerHolder6').ColorPicker({
			flat: true,
			color: $('#HiddenColor6').val(),
			onSubmit: function(hsb, hex, rgb, el) {
				$('#colorSelector6 div').css('backgroundColor', '#' + hex);
				$('#HiddenColor6').val(hex);
				$('#colorpickerHolder6').stop().animate({height: widt ? 0 : 173}, 500);
				widt = !widt;
			}
		});		
		$('#colorpickerHolder7').ColorPicker({
			flat: true,
			color: $('#HiddenColor7').val(),
			onSubmit: function(hsb, hex, rgb, el) {
				$('#colorSelector7 div').css('backgroundColor', '#' + hex);
				$('#HiddenColor7').val(hex);
				$('#colorpickerHolder7').stop().animate({height: widt ? 0 : 173}, 500);
				widt = !widt;
			}
		});		
		$('#colorpickerHolder8').ColorPicker({
			flat: true,
			color: $('#HiddenColor8').val(),
			onSubmit: function(hsb, hex, rgb, el) {
				$('#colorSelector8 div').css('backgroundColor', '#' + hex);
				$('#HiddenColor8').val(hex);
				$('#colorpickerHolder8').stop().animate({height: widt ? 0 : 173}, 500);
				widt = !widt;
			}
		});		
		$('#colorpickerHolder9').ColorPicker({
			flat: true,
			color: $('#HiddenColor9').val(),
			onSubmit: function(hsb, hex, rgb, el) {
				$('#colorSelector9 div').css('backgroundColor', '#' + hex);
				$('#HiddenColor9').val(hex);
				$('#colorpickerHolder9').stop().animate({height: widt ? 0 : 173}, 500);
				widt = !widt;
			}
		});		
		$('#colorpickerHolder10').ColorPicker({
			flat: true,
			color: $('#HiddenColor10').val(),
			onSubmit: function(hsb, hex, rgb, el) {
				$('#colorSelector10 div').css('backgroundColor', '#' + hex);
				$('#HiddenColor10').val(hex);
				$('#colorpickerHolder10').stop().animate({height: widt ? 0 : 173}, 500);
				widt = !widt;
			}
		});		
		$('#colorpickerHolder11').ColorPicker({
			flat: true,
			color: $('#HiddenColor11').val(),
			onSubmit: function(hsb, hex, rgb, el) {
				$('#colorSelector11 div').css('backgroundColor', '#' + hex);
				$('#HiddenColor11').val(hex);
				$('#colorpickerHolder11').stop().animate({height: widt ? 0 : 173}, 500);
				widt = !widt;
			}
		});		
		$('#colorpickerHolder12').ColorPicker({
			flat: true,
			color: $('#HiddenColor12').val(),
			onSubmit: function(hsb, hex, rgb, el) {
				$('#colorSelector12 div').css('backgroundColor', '#' + hex);
				$('#HiddenColor12').val(hex);
				$('#colorpickerHolder12').stop().animate({height: widt ? 0 : 173}, 500);
				widt = !widt;
			}
		});		
		$('#colorpickerHolder13').ColorPicker({
			flat: true,
			color: $('#HiddenColor13').val(),
			onSubmit: function(hsb, hex, rgb, el) {
				$('#colorSelector13 div').css('backgroundColor', '#' + hex);
				$('#HiddenColor13').val(hex);
				$('#colorpickerHolder13').stop().animate({height: widt ? 0 : 173}, 500);
				widt = !widt;
			}
		});		
		$('#colorpickerHolder2>div, #colorpickerHolder3>div, #colorpickerHolder4>div, #colorpickerHolder5>div, #colorpickerHolder6>div, #colorpickerHolder7>div, #colorpickerHolder8>div, #colorpickerHolder9>div, #colorpickerHolder10>div, #colorpickerHolder11>div, #colorpickerHolder12>div, #colorpickerHolder13>div').css('position', 'absolute');
		var widt = false;
		$('#colorSelector2').bind('click', function() {
			$('#colorpickerHolder2').stop().animate({height: widt ? 0 : 173}, 500);
			widt = !widt;
		});
		$('#colorSelector3').bind('click', function() {
			$('#colorpickerHolder3').stop().animate({height: widt ? 0 : 173}, 500);
			widt = !widt;
		});		
		$('#colorSelector4').bind('click', function() {
			$('#colorpickerHolder4').stop().animate({height: widt ? 0 : 173}, 500);
			widt = !widt;
		});				
		$('#colorSelector5').bind('click', function() {
			$('#colorpickerHolder5').stop().animate({height: widt ? 0 : 173}, 500);
			widt = !widt;
		});		
		$('#colorSelector6').bind('click', function() {
			$('#colorpickerHolder6').stop().animate({height: widt ? 0 : 173}, 500);
			widt = !widt;
		});		
		$('#colorSelector7').bind('click', function() {
			$('#colorpickerHolder7').stop().animate({height: widt ? 0 : 173}, 500);
			widt = !widt;
		});		
		$('#colorSelector8').bind('click', function() {
			$('#colorpickerHolder8').stop().animate({height: widt ? 0 : 173}, 500);
			widt = !widt;
		});		
		$('#colorSelector9').bind('click', function() {
			$('#colorpickerHolder9').stop().animate({height: widt ? 0 : 173}, 500);
			widt = !widt;
		});		
		$('#colorSelector10').bind('click', function() {
			$('#colorpickerHolder10').stop().animate({height: widt ? 0 : 173}, 500);
			widt = !widt;
		});		
		$('#colorSelector11').bind('click', function() {
			$('#colorpickerHolder11').stop().animate({height: widt ? 0 : 173}, 500);
			widt = !widt;
		});		
		$('#colorSelector12').bind('click', function() {
			$('#colorpickerHolder12').stop().animate({height: widt ? 0 : 173}, 500);
			widt = !widt;
		});		
		$('#colorSelector13').bind('click', function() {
			$('#colorpickerHolder13').stop().animate({height: widt ? 0 : 173}, 500);
			widt = !widt;
		});		
		$('#colorpickerField2, #colorpickerField3, #colorpickerField4, #colorpickerField5, #colorpickerField6, #colorpickerField7, #colorpickerField8, #colorpickerField9, #colorpickerField10, #colorpickerField11, #colorpickerField12, #colorpickerField13').ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) {
				$(el).val(hex);
				$(el).ColorPickerHide();
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
			}
		})
		.bind('keyup', function(){
			$(this).ColorPickerSetColor(this.value);
		});
		$('#colorSelector').ColorPicker({
			color: '#0000ff',
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#colorSelector div').css('backgroundColor', '#' + hex);
			}
		});
	};
	
	var showTab = function(e) {
		var tabCIndex = $('ul.navigationTabs a')
							.removeClass('active')
							.index(this);
		$(this)
			.addClass('active')
			.blur();
		$('div.tab')
			.hide()
				.eq(tabCIndex)
				.show();
	};
	
	EYE.register(initLayout, 'init');
})(jQuery)