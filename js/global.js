jQuery(document).ready(function(){
	jQuery("#suggest label").inFieldLabels();

	var ul = jQuery('ul.suggestions');

	// Listening of a click on a UP or DOWN arrow:

	jQuery('div.vote span').live('click',function(){

		var elem		= jQuery(this),
			parent		= elem.parent(),
			li			= elem.closest('li'),
			ratingDiv	= li.find('.rating'),
			id			= li.attr('id').replace('s',''),
			v			= 1;

		// If the user's already voted:

		if(parent.hasClass('inactive')){
			return false;
		}

		parent.removeClass('active').addClass('inactive');

		if(elem.hasClass('down')){
			v = -1;
		}

		// Incrementing the counter on the right:
		ratingDiv.text(v + +ratingDiv.text());

		// Turning all the LI elements into an array
		// and sorting it on the number of votes:

		var arr = jQuery.makeArray(ul.find('li')).sort(function(l,r){
			return +jQuery('.rating',r).text() - +jQuery('.rating',l).text();
		});

		// Adding the sorted LIs to the UL
		ul.html(arr);

		// Sending an AJAX request
		jQuery.get(
		FsAjax.ajaxurl,
		{
			action : 'fs_ajax',
			type : 'vote',
			fsNonce : FsAjax.fsNonce,
			vote : v,
			'id' : id});
	});

	jQuery('#suggest').submit(function(){
		var form			= jQuery(this),
			titleField		= jQuery('#suggestionTitle'),
			contentField	= jQuery('#suggestionContent');

		// Preventing double submits:
		if(form.hasClass('working') || titleField.val().length<3){
			return false;
		}

		form.addClass('working');

		jQuery.getJSON(
		FsAjax.ajaxurl,
		{
			action : 'fs_ajax',
			type : 'suggest',
			fsNonce : FsAjax.fsNonce,
			title : titleField.val(),
			content : contentField.val()
		},
		function(msg){
			titleField.val('');
			contentField.val('');
			form.removeClass('working');

			if(msg.html){
				// Appending the markup of the newly created LI to the page:
				msg.html = msg.html.replace(/\\\'/g,'\''); // pesky single quotes
				jQuery(msg.html).hide().appendTo(ul).slideDown();
			}
		});

		return false;
	});
});