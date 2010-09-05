jQuery(document).ready(function(){

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


/*
 * In-Field Label jQuery Plugin
 * http://fuelyourcoding.com/scripts/infield.html
 * Copyright (c) 2009 Doug Neiner
 * Dual licensed under the MIT and GPL licenses.
 * Uses the same license as jQuery, see: http://docs.jquery.com/License
 * @version 0.1
 */
(function($){$.InFieldLabels=function(b,c,d){var f=this;f.$label=$(b);f.label=b;f.$field=$(c);f.field=c;f.$label.data("InFieldLabels",f);f.showing=true;f.init=function(){f.options=$.extend({},$.InFieldLabels.defaultOptions,d);if(f.$field.val()!=""){f.$label.hide();f.showing=false};f.$field.focus(function(){f.fadeOnFocus()}).blur(function(){f.checkForEmpty(true)}).bind('keydown.infieldlabel',function(e){f.hideOnChange(e)}).change(function(e){f.checkForEmpty()}).bind('onPropertyChange',function(){f.checkForEmpty()})};f.fadeOnFocus=function(){if(f.showing){f.setOpacity(f.options.fadeOpacity)}};f.setOpacity=function(a){f.$label.stop().animate({opacity:a},f.options.fadeDuration);f.showing=(a>0.0)};f.checkForEmpty=function(a){if(f.$field.val()==""){f.prepForShow();f.setOpacity(a?1.0:f.options.fadeOpacity)}else{f.setOpacity(0.0)}};f.prepForShow=function(e){if(!f.showing){f.$label.css({opacity:0.0}).show();f.$field.bind('keydown.infieldlabel',function(e){f.hideOnChange(e)})}};f.hideOnChange=function(e){if((e.keyCode==16)||(e.keyCode==9))return;if(f.showing){f.$label.hide();f.showing=false};f.$field.unbind('keydown.infieldlabel')};f.init()};$.InFieldLabels.defaultOptions={fadeOpacity:0.5,fadeDuration:300};$.fn.inFieldLabels=function(c){return this.each(function(){var a=$(this).attr('for');if(!a)return;var b=$("input#"+a+"[type='text'],"+"input#"+a+"[type='password'],"+"textarea#"+a);if(b.length==0)return;(new $.InFieldLabels(this,b[0],c))})}})(jQuery);