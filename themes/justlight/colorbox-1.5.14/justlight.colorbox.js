/*
 *  Colorbox javascript for the JustLight theme
 *
 *  webtrees: Web based Family History software
 *  Copyright (C) 2014 webtrees development team.
 *  Copyright (C) 2014 JustCarmen.
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; either version 2
 *  of the License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 */

function qstring(key, url) {
	'use strict';
	var KeysValues, KeyValue, i;
	if (url === null || url === undefined) {
		url = window.location.href;
	}
	KeysValues = url.split(/[\?&]+/);
	for (i = 0; i < KeysValues.length; i++) {
		KeyValue = KeysValues[i].split("=");
		if (KeyValue[0] === key) {
			return KeyValue[1];
		}
	}
}

function get_imagetype() {
	var xrefs = [];
	jQuery('a[type^=image].gallery').each(function() {
		var xref = qstring('mid', jQuery(this).attr('href'));
		jQuery(this).attr('id', xref);
		xrefs.push(xref);
	});
	jQuery.ajax({
		url: JL_COLORBOX_URL + 'action.php?action=imagetype',
		type: 'POST',
		async: false,
		data: {
			'csrf': WT_CSRF_TOKEN,
			'xrefs': xrefs
		},
		success: function(data) {
			jQuery.each(data, function(index, value) {
				jQuery('a[id=' + index + ']').attr('data-obje-type', value);
			});
		}
	});
}

function longTitles() {
	var tClass = jQuery("#cboxTitle .title");
	var tID = jQuery("#cboxTitle");
	if (tClass.width() > tID.width() - 100) { // 100 because the width of the 4 buttons is 25px each
		tClass.css({
			"width": tID.width() - 100,
			"margin-left": "75px"
		});
	}
	if (tClass.height() > 25) { // 26 is 2 lines
		tID.css({
			"bottom": 0
		});
		tClass.css({
			"height": "26px"
		}); // max 2 lines.
	} else {
		tID.css({
			"bottom": "6px"
		}); // set the value to vertically center a 1 line title.
		tClass.css({
			"height": "auto"
		}); // set the value back;
	}
}

function resizeImg() {
	jQuery("#cboxLoadedContent").css('overflow-x', 'hidden');
	var outerW = parseInt(jQuery("#cboxLoadedContent").css("width"), 10);
	var innerW = parseInt(jQuery(".cboxPhoto").css("width"), 10);
	if (innerW > outerW) {
		var innerH = parseInt(jQuery(".cboxPhoto").css("height"), 10);
		var ratio = innerH / innerW;
		var outerH = outerW * ratio;
		jQuery(".cboxPhoto").css({
			"width": outerW + "px",
			"height": outerH + "px"
		});
	}
}

// add colorbox function to all images on the page when first clicking on an image.
jQuery("body").one('click', 'a.gallery', function() {
	get_imagetype();

	// General (both images and pdf)
	jQuery("a[type^=image].gallery, a[type$=pdf].gallery").colorbox({
		rel: "gallery",
		current: "",
		slideshow: true,
		slideshowAuto: false,
		slideshowSpeed: 3000,
		fixed: true
	});

	// Image settings
	jQuery("a[type^=image].gallery").colorbox({
		photo: true,
		scalePhotos: function() {
			if (jQuery(this).data('obje-type') === 'photo') {
				return true;
			}
		},
		maxWidth: "90%",
		maxHeight: "90%",
		title: function() {
			var img_title = jQuery(this).data("title");
			return "<div class=\"title\">" + img_title + "</div>";
		},
		onComplete: function() {
			if (jQuery(this).data('obje-type') !== 'photo') {
				resizeImg();
			}
			jQuery(".cboxPhoto").wheelzoom();
			jQuery(".cboxPhoto img").on("click", function(e) {
				e.preventDefault();
			});
			longTitles();
		}
	});

	// PDF settings
	jQuery("a[type$=pdf].gallery").colorbox({
		width: "75%",
		height: "90%",
		iframe: true,
		title: function() {
			var pdf_title = jQuery(this).data("title");
			return '<div class="title">' + pdf_title + '</div>';
		},
		onComplete: function() {
			longTitles();
		}
	});

	// Do not open the gallery when clicking on the mainimage on the individual page
	jQuery('a.gallery').each(function() {
		if (jQuery(this).parents("#indi_mainimage").length > 0) {
			jQuery(this).colorbox({
				rel: "nofollow"
			});
		}
	});
});