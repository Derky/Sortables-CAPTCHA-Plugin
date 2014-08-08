function createdata(listnameobject, column, resultid)
{
	// Let's delete all the current input type="hidden" fields, this is easier to find out which were changed
	var data = document.getElementById(column);

	if ( data.hasChildNodes() )	{
		while ( data.childNodes.length >= 1 ) {
			data.removeChild( data.firstChild );       
		} 
	}
	
	// Run through all childs
	jQuery.each( listnameobject, function(){
	
		// We only want the ID of the answer
		var answer = $( this ).attr("id");
		answer = answer.replace(/answer_/g, '');
		
		// And add a hidden input field
		inputbox = document.createElement("input"); 
		inputbox.type = 'hidden';
		inputbox.name = column + '[]';
		inputbox.value = answer;
		data.appendChild(inputbox);
	});
}

$(function() {
	// Javascript nubs
	document.getElementById('enable_js').style.display = 'block';

	$("#sortable1, #sortable2").sortable({
		connectWith: '.connectedSortable',
		items: 'li',
		forcePlaceholderSize: true,
		placeholder: 'bg3'
		
	}).disableSelection();
	
	$("#sortable1, #sortable2").bind('sortreceive', function(event, ui) {
		// If the left or right column receive an item, put the child nodes in an array
		var arrSortableListItemsLeft = $( "#sortable1" ).children();
		var arrSortableListItemsRight = $( "#sortable2" ).children();
		
		// And create hidden input fields
		createdata(arrSortableListItemsLeft, 'sortables_options_left', '#sortables_options_left');
		createdata(arrSortableListItemsRight, 'sortables_options_right', '#sortables_options_right');
	});
});