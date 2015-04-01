/*
 *  Bootstrap javascript for the JustLight theme
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

// Bootstrap fixed navbar. Keep the content in relation to the navbar always in position.
jQuery("#wrap").css('padding-top', jQuery('#nav-container').outerHeight() + 10);
jQuery(window).resize(function() {
	jQuery("#wrap").css('padding-top', jQuery('#nav-container').outerHeight() + 10);
});

// Bootstrap multilevel menu
jQuery(".dropdown-menu > li > a.dropdown-submenu-toggle").on("click", function(e) {
	e.preventDefault();
	var current = jQuery(this).next();
	var grandparent = jQuery(this).parent().parent();
	grandparent.find(".sub-menu:visible").not(current).hide();
	current.toggle();
	e.stopPropagation();
});
jQuery(".dropdown-menu > li > a:not(.dropdown-submenu-toggle)").on("click", function() {
	var root = jQuery(this).closest('.dropdown');
	root.find('.sub-menu:visible').hide();
});

// Bootstrap active tab in navbar 
var url_parts = location.href.split('/');
var last_segment = url_parts[url_parts.length - 1];
jQuery('.nav-pills a[href="' + last_segment + '"]').parents('li').addClass('active');

// Bootstrap vertical menu for smaller screens
function getSmallMenu() {
	if (jQuery(window).width() < 450) {
		jQuery('.nav').removeClass('nav-pills');
		jQuery('.nav').addClass('nav-stacked');
	} else {
		jQuery('.nav').addClass('nav-pills');
		jQuery('.nav').removeClass('nav-stacked');
	}
}

getSmallMenu();
jQuery(window).resize(function() {
	getSmallMenu();
});

// Bootstrap table layout
jQuery("table").waitUntilExists(function() {
	if (jQuery(this).hasClass("table-census-assistant")) {
		jQuery(this).addClass("table table-condensed table-striped width100");
		jQuery(this).find("tbody tr:first td:first").attr("colspan", jQuery(this).find("th").length);
	} else {
		var table = jQuery(this).not("#tabs table, table.tv_tree, [id*=chart] table, #place-hierarchy > table, #place-hierarchy > table table, #family-page table, #branches-page table, .gedcom_block_block table, .user_welcome_block table, .cens_search table, .cens_data table");
		table.addClass("table");
		jQuery(this).parents(".gedcom_stats_block > table").addClass("table-striped");
	}
});

jQuery(".markdown").waitUntilExists(function() {
	jQuery(this).find("table").each(function() {
		jQuery(this).addClass("table table-condensed table-striped width100");
		var colspan = jQuery(this).find("th").length;
		jQuery(this).find("tbody").prepend("<tr><td colspan=\"" + colspan + "\">");
	});

});

jQuery("#sb_content_family_nav").each(function() {
	jQuery(this).find("table").addClass("table-striped");
	jQuery(this).find("td").removeClass("person_box person_boxF person_boxNN center");
});

// Manual popover trigger function
function manualTrigger(obj, click, hover) {

	if (click === true) {
		obj.on("click", function(event) { // click is neccessary for touchscreen devices.
			event.preventDefault();
			event.stopPropagation();
			jQuery('.popover').not(obj).hide();
			obj.popover("show");
			jQuery('.popover-content').addClass(obj.data("class"))
		});
	}

	if (hover === true) {
		obj.on("mouseenter", function() {
			jQuery('.popover').not(obj).hide();
			obj.popover("show");
			jQuery('.popover-content').addClass(obj.data("class"))
			obj.siblings(".popover").on("mouseleave", function() {
				obj.popover('hide');
			});
		});

		obj.on("mouseleave", function() {
			setTimeout(function() {
				if (!jQuery(".popover:hover").length) {
					obj.popover("hide");
				}
			}, 100);
		});
	}
}

// Prepare webtrees popup lists for bootstrap popovers
jQuery(".popup > ul > li").waitUntilExists(function() {
	var text = jQuery.trim(jQuery(this).children().text());
	if (!text.length) {
		jQuery(this).remove();
	}
	jQuery(this).find(">ul").parent().css("list-style-type", "none");
});

// Bootstrap popovers and/or tooltips
jQuery("a.icon-pedigree").waitUntilExists(function() {
	var title = jQuery(this).parents(".person_box_template").find(".chart_textbox .NAME").parents("a").clone().wrap('<p>').parent().html();
	var content = jQuery(this).parents(".itr").find(".popup > ul");
	content = content.removeClass().remove();
	if (jQuery(this).parents("#index_small_blocks")) {
		placement = 'left';
	} else {
		placement = 'auto right';
	}
	jQuery(this).attr("data-toggle", "popover");
	jQuery(this).popover({
		title: title,
		content: content,
		html: true,
		trigger: 'manual',
		placement: placement,
		container: 'body'
	}).on(manualTrigger(jQuery(this), true, true));
});

jQuery("#medialist-page .lb-menu").each(function() {
	jQuery(this).find(".lb-image_edit, .lb-image_view").each(function() {
		var title = jQuery(this).text();
		jQuery(this).text("");
		jQuery(this).attr({
			"data-toggle": "tooltip",
			"data-placement": "top",
			"title": title
		});
		jQuery(this).tooltip();
	});
	jQuery(this).find(".lb-image_link").each(function() {
		var title = jQuery(this).text();
		var content = jQuery(this).next("ul").html();
		jQuery(this).text("").next("ul").remove();
		jQuery(this).attr("data-toggle", "popover");
		jQuery(this).popover({
			title: title,
			content: content,
			html: true,
			trigger: 'manual',
			placement: 'bottom',
			container: '#medialist-page'
		}).on(manualTrigger(jQuery(this), false, true));
	});
	jQuery(this).css("display", "inline-block");
});

// Bootstrap popover for fanchart page
if (WT_SCRIPT_NAME === "fanchart.php") {

	jQuery("#fan_chart #fanmap area").each(function() {
		var id = jQuery(this).attr("href").split("#");
		var obj = jQuery("#fan_chart > div[id=" + id[1] + "]");
		obj.find(".person_box").addClass("fan-chart-list");
		var title = obj.find(".name1:first").remove();
		var content = obj.html();
		jQuery(this).attr("data-toggle", "popover").attr("title", title.clone().wrap('<p>').parent().html()).removeAttr("href");
		jQuery(this).popover({
			content: content,
			html: true,
			trigger: 'manual',
			container: 'body'
		}).on(manualTrigger(jQuery(this), true, false));
	});
}

// Childbox popover
jQuery("#childarrow a").waitUntilExists(function() {
	content = jQuery(this).parent().find("#childbox").remove();
	jQuery(this).attr({
		"data-toggle": "popover",
		"data-class": "childbox"
	});
	jQuery(this).popover({
		content: content.html(),
		html: true,
		trigger: 'manual',
		placement: 'bottom',
		container: 'body'
	}).on(manualTrigger(jQuery(this), true, true));
});

// close popover when clicking outside (anywhere in the page);
jQuery('body').on('click', function(e) {
	if (jQuery(e.target).data('toggle') !== 'popover' && jQuery(e.target).parents('.popover.in').length === 0) {
		jQuery('[data-toggle="popover"]').popover('hide');
	}
});

// add bootstrap buttons
jQuery("#edit_interface-page .save, #edit_interface-page .cancel").addClass("btn btn-default btn-sm");
jQuery("#find-page button").addClass("btn btn-default btn-xs");
jQuery("input[type=submit], input[type=button]").addClass("btn btn-default btn-xs");
jQuery("#personal_facts_content").waitUntilExists(function() {
	jQuery("input[type=button]").addClass("btn btn-default btn-xs").css("visibility", "visible");
});