var sortablescaptcha = {
	
	init: function() {
		
		// JavaScript is enabled, so show the normal version
		$('#enable_js').css('display', 'block');

		// Make divs sortable
		$("#sortable1, #sortable2").sortable({
			connectWith: '.sortables_captcha_list',
			items: 'li',
			forcePlaceholderSize: true,
			placeholder: 'bg3',
			tolerance: 'pointer',
		}).disableSelection();

		$("#sortable1, #sortable2").bind('sortreceive', function(event, ui) {
			
			// If the left or right column receive an item, put the child nodes in an array
			var arrSortableListItemsLeft = $("#sortable1").children();
			var arrSortableListItemsRight = $("#sortable2").children();

			// And create hidden input fields
			sortablescaptcha.createData(arrSortableListItemsLeft, 'sortables_options_left', '#sortables_options_left');
			sortablescaptcha.createData(arrSortableListItemsRight, 'sortables_options_right', '#sortables_options_right');
		});
	},
	
	createData: function(listnameobject, column, resultid)
	{
		// Let's delete all the current input type="hidden" fields, this is easier to find out which were changed
		var data = document.getElementById(column);

		if ( data.hasChildNodes() )	{
			while ( data.childNodes.length >= 1 ) {
				data.removeChild( data.firstChild );       
			}
		}

		// Run through all childs
		$.each(listnameobject, function(){

			// We only want the ID of the answer
			var answer = $(this).attr("id");
			answer = answer.replace(/answer_/g, '');

			// And add a hidden input field
			inputbox = document.createElement("input"); 
			inputbox.type = 'hidden';
			inputbox.name = column + '[]';
			inputbox.value = answer;
			data.appendChild(inputbox);
		});
	},
}

$(function() {
	sortablescaptcha.init();
});