/** form */
$(document).ready(function(){
	
	var ajaxLoad = $('.ajax-load');
	ajaxLoad.length && (function($, ajaxLoad){

		renderContent();

		// create form action
		$('#fancybox-content .form-area').live('submit', function(e){
			e.preventDefault();

			$.fancybox.showActivity();

			$.ajax({
				url: $(this).attr('action'),
				type: 'post',
				dataType: 'json',
				data: $(this).serialize(),
				success: function(response){
					$.fancybox.hideActivity();

					if( response.success ){
						$.fancybox.close();
						renderContent();
					}else{
						alert( response.error );
					}
				}
			});
		});
		
		// load ajax
		function renderContent()
		{
			$.ajax({
				url: ajaxLoad.data('action'),
				type: 'get',
				success: function(data){
					// register action
					ajaxLoad.html(data)
						.find('a').removeAttr('onclick').click(function(e){
							e.preventDefault();

							var action = $(this).data('action'),
								target = $(this).attr('href');
							switch(action){
								case 'add':
								case 'edit':
									if( $(this).attr('href')=='#submit-first' ){
										if( confirm('Save menu before insert option.') ){
											$('.form-area').append('<input type="hidden" name="action" value="reload" />').trigger("submit");
										}
									}else{
										$.fancybox({
											href: target
										});
									}
									break;
								case 'remove':
									// makeRemove(target);
									break;
								default:
									break;
							}
						});
				}
			});
		}

		function makeRemove(target)
		{
			if( confirm('Remove this row ?') ){
				$.fancybox.showActivity();

				// makeRequest
				$.ajax({
					url: target,
					type: 'get',
					dataType: 'json',
					success: function(response){
						$.fancybox.hideActivity();

						if( response.success ){
							renderContent();
						}else{
							alert( response.error );
						}
					}
				});
			}
		}

	})(jQuery, ajaxLoad);

});