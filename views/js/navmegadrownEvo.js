$(document).ready(function() {
	function megaHoverOver(){
		$(this).find(".sub").stop().fadeTo('fast', 1).show();
		(function($) { 
			jQuery.fn.calcSubWidth = function() {
				rowWidth = 0;
				$(this).find("ul").each(function() {		
					rowWidth += $(this).width(); 
				});	
			};
		})(jQuery); 

		if ( $(this).find(".row").length > 0 ) {
			var biggestRow = 0;	
			$(this).find(".row").each(function() {							   
				$(this).calcSubWidth();
				if(rowWidth > biggestRow) {
					biggestRow = rowWidth;
				}
			});
			//$(this).find(".sub").css({'width' :biggestRow});
			$(this).find(".row:last").css({'margin':'0'});
		} else {
			$(this).calcSubWidth();
			//$(this).find(".sub").css({'width' : rowWidth});
		}
	}

	function megaHoverOut(){ 
	  $(this).find(".sub").stop().fadeTo('fast', 0, function() {
		  $(this).hide(); 
	  });
	}

	var config = {    
		 sensitivity: 2,
		 interval: 100,
		 over: megaHoverOver,
		 timeout: 300,
		 out: megaHoverOut
	};
	var widthTotal = 0;
	$("ul#topnavEvo li.liBouton .sub").css({'opacity':'0'});
	$("ul#topnavEvo li.liBouton").each(function() {
		$(this).find(".sub").css({'margin-left': ((widthTotal*-1) - parseInt($("ul#topnavEvo").css('padding-left').replace('px','')) ) + 'px'});
		widthTotal = widthTotal + $(this).width(); 
	});
	$("ul#topnavEvo li.liBouton .sub ul").css({'list-style':'none'});
	$("ul#topnavEvo li.liBouton").hoverIntent(config);
});

