/**
* On page load functions
*/
$(document).ready(function(){
	
	// Add event listeners to all buttons
	$('.button').click(function(){veil()});
	
	// Add event listener to window close buttons
	$('.window-close-button').click(function(){
		window.opener.location.reload();
		window.close();
	});
	
	// Add event listener to notifications dismiss button
	$('#notifications-dismiss').click(function(){$('#notifications').fadeOut()});
	
	// Add event listener to back buttons
	$('.back-button').click(function(){window.location.href='.'});
	
	// Add event listeners to form submit buttons
	$('.form-submit-button').click(function(){
		$('#' + $(this).attr('id') + '-form').submit();
	});
	
	// Add event listeners to confirm buttons
	$('.confirm-button').click(function(e){
		if(!confirm('Are you sure?'))
		{
			e.preventDefault();
			unveil();
		}
	});
	
	$('.confirm-button-td').click(function(){
		
	});
	
	// Add event listeners to on-change submit buttons
	$('.submit-parent-change').change(function(){
		veil();
		$(this).parent().parent().submit();
	});
	
	// Add event listeners to search form additional fields buttons
	$('.search-additional-field-toggle').click(function(e){
		var additionalFields = $(this).parent().parent().find('.additional-fields');
		$.each(additionalFields, function(i, item){
			if($(item).is(':hidden'))
			{
				$(item).show();
				$(e.target).html("Show Less");
			}
			else
			{
				$(item).hide();
				$(e.target).html("Show More");
			}
		});
	});
	
	// Add listeners to region expand buttons
	$('.region-expand').click(function(e){
		if($(this).hasClass("region-expand-collapsed"))
		{
			// Change indicator
			$(this).addClass("region-expand-expanded");
			$(this).removeClass("region-expand-collapsed");
			
			// Show region
			$(this).next().show();
		}
		else
		{
			$(this).removeClass("region-expand-expanded");
			$(this).addClass("region-expand-collapsed");
			
			$(this).next().hide();
		}
	});
	
	// Initialize date pickers
	$('.date-input').datepicker({dateFormat:'yy-mm-dd'});

	// Display notifications if any are present
	$('#notifications').fadeIn();
});

function veil()
{
	$('#veil').fadeIn();
}

function unveil()
{
	$('#veil').fadeOut();
}