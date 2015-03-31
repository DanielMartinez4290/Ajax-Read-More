(function($) { 
	$('.more-link').click(function(){
		var link = this;
		$(link).html('loading...');
		var post_id = $(link).attr('href').replace(/^.*#more-/, '');
		var data = {action:'dm_arm_ajax',post_id:post_id};
		$.get(dm_arm.ajaxurl, data, function(data){ 
			$(link).after(data).remove();
		});
		return false; 
	});
})(jQuery);