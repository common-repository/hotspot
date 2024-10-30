(function($){
	$(document).ready(function(){
		$('.hotspot_hastooltop').each(function(){
			$(this).data('powertip', function(){
				var htmlThis = $(this).parents('.hotspot_tooltop_html').attr('data-html');
				return htmlThis;
			});
			var thisPlace = $(this).parents('.hotspot_tooltop_html').data('placement');
			var dataDisplay = $(this).parents('.hotspot_tooltop_html').data('id');
			if (dataDisplay == 'active') {
				$(this).powerTip({
					placement: thisPlace,
					openEvents: ['mouseenter'],
					closeEvents: ['mouseenter']
				});
			} else {
				$(this).powerTip({
					placement: thisPlace,
					smartPlacement: dataDisplay == 'mouseover' ? true : false,
					mouseOnToPopup: dataDisplay == 'mouseover' ? true : false,
					openEvents: dataDisplay == 'click' ? ['click'] : ['mouseover'],
					closeEvents: dataDisplay == 'click' ? ['click'] : ['mouseover']
				});
			}
			$(document).on('click','.close_hp',function(){
				$.powerTip.hide();
            });
		})
    });
})(jQuery)