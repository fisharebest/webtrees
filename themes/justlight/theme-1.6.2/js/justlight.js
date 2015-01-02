/*
 * Javascript for the JustLight theme
 *  
 * webtrees: Web based Family History software
 * Copyright (C) 2014 webtrees development team.
 * Copyright (C) 2014 JustCarmen.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * use waitUntilExists plugin on pages with dynamic content - https://gist.github.com/md55/6565078
 */

/* General functions (fired on every page */

// responsive page
var $responsive = jQuery("#responsive").is(":visible");
jQuery(window).resize(function() {
	$responsive = jQuery("#responsive").is(":visible");
});

// Modal dialog boxes
function jl_modalDialog(url, title) {
	var $dialog = jQuery('<div id="config-dialog" style="max-height:550px; overflow-y:auto"><div title="' + title + '"><div></div>').load(url, function() {
		jQuery(this).dialog("option", "position", ['center', 'center']);
	}).dialog({
		title: title,
		width: 'auto',
		maxWidth: '90%',
		height: 'auto',
		maxHeight: 500,
		fluid: true,
		modal: true,
		resizable: false,
		autoOpen: false,
		open: function() {
			jQuery('.ui-widget-overlay').on('click', function() {
				$dialog.dialog('close');
			});
		}
	});

	// open the dialog box after some time. This is neccessary for the dialogbox to load in center position without page flickering.
	setTimeout(function() {
		$dialog.dialog('open');
	}, 500);
	return false;
}

function jl_helpDialog(topic, module) {
	jQuery.getJSON('help_text.php?help=' + topic + '&mod=' + module, function(json) {
		jl_modalHelp(json.content, json.title);
	});
}

function jl_modalHelp(content, title) {
	var $dialog = jQuery('<div style="max-height:375px; overflow-y:auto"><div></div></div>').html(content).dialog({
		width: 'auto',
		maxWidth: 500,
		height: 'auto',
		maxHeight: 500,
		modal: true,
		fluid: true,
		resizable: false,
		open: function() {
			jQuery('.ui-widget-overlay').on('click', function() {
				$dialog.dialog('close');
			});
		}
	});

	jQuery('.ui-dialog-title').html(title);
	return false;
}

jQuery(document).on("dialogopen", ".ui-dialog", function(event, ui) {
	fluidDialog();
});

// remove window resize namespace
jQuery(document).on("dialogclose", ".ui-dialog", function(event, ui) {
	jQuery(window).off("resize.responsive");
});

jQuery(window).resize(function() {
	jQuery(".ui-dialog-content").dialog("option", "position", ['center', 'center']);
});

function fluidDialog() {
	var $visible = jQuery(".ui-dialog:visible");
	$visible.each(function() {
		var $this = jQuery(this);
		var dialog = $this.find(".ui-dialog-content");
		var maxWidth = dialog.dialog("option", "maxWidth");
		var width = dialog.dialog("option", "width");
		var fluid = dialog.dialog("option", "fluid");
		// if fluid option == true
		if (maxWidth && width) {
			// fix maxWidth bug
			$this.css("max-width", maxWidth);
			//reposition dialog
			dialog.dialog("option", "position", ['center', 'center']);
		}

		if (fluid) {
			// namespace window resize
			jQuery(window).on("resize.responsive", function() {
				var wWidth = jQuery(window).width();
				// check window width against dialog width
				if (wWidth < maxWidth + 50) {
					// keep dialog from filling entire screen
					$this.css("width", "90%");

				}
				//reposition dialog
				dialog.dialog("option", "position", ['center', 'center']);
			});
		}
	});
}

function jl_dialogBox() {
	jQuery('[onclick^="modalDialog"], [onclick^="return modalDialog"]').each(function() {
		jQuery(this).attr('onclick', function(index, attr) {
			return attr.replace('modalDialog', 'jl_modalDialog');
		});
	});

	jQuery('[onclick^="helpDialog"]').each(function() {
		jQuery(this).attr('onclick', function(index, attr) {
			return attr.replace('helpDialog', 'jl_helpDialog');
		});
	});
}


jl_dialogBox();
jQuery(document).ajaxComplete(function() {
	jl_dialogBox();
});

// personboxes
function personbox_default() {
	var obj = jQuery(".person_box_template .inout2");
	modifybox(obj);
}

function modifybox(obj) {
	obj.find(".field").contents().filter(function() {
		return (this.nodeType === 3);
	}).remove();
	obj.find(".field span").filter(function() {
		return jQuery(this).text().trim().length === 0;
	}).remove();
	obj.find("div[class^=fact_]").each(function() {
		var div = jQuery(this);
		div.find(".field").each(function() {
			if (jQuery.trim(jQuery(this).text()) === '') {
				div.remove();
			}
		});
	});

}

personbox_default();

jQuery(document).ajaxComplete(function() {
	setTimeout(function() {
		personbox_default();
	}, 500);
	var obj = jQuery(".person_box_zoom");
	modifybox(obj);
});

/* page specific functions */

// Sticky footer - correction needed for pedigree page
if (jQuery("#pedigree-page").length > 0) {
	jQuery("#content").css("margin-bottom", "50px");
}

// Move link to change blocks to the footer area.
if (WT_SCRIPT_NAME === "index.php") {
	jQuery("#link_change_blocks").appendTo(jQuery("footer .top"));

	// journal-box correction - remove br's from content. Adjust layout to the news-box layout.
	jQuery(".user_blog_block > br, .journal_box > br").remove();
	jQuery(".journal_box > a[onclick*=editnews]").before('<hr>');
}

// Styling of the individual page
if (WT_SCRIPT_NAME === "individual.php") {
	// Remove personboxNN class from header layout
	jQuery("#indi_header H3").removeClass("person_boxNN");

	// relatives tab
	jQuery("#relatives_content").waitUntilExists(function() {
		jQuery(this).find(".subheaders").parents("table").css("margin-top", "15px");
	});

	// When in responsive state hide the indi_left part when sidebar is open.
	var responsiveSidebar = false;

	// Hide sidebar by default on smaller screens
	if ($responsive) {
		jQuery.cookie("hide-sb", true);
		responsiveSidebar = true;
	}

	jQuery(window).resize(function() {
		jQuery("#indi_left").show();
		if ($responsive) {
			responsiveSidebar = true;
		} else {
			responsiveSidebar = false;
		}

		if ($responsive || jQuery.cookie("hide-sb") === "true") {
			jQuery("#sidebar").hide();
		} else {
			jQuery("#sidebar").show();
		}
	});

	// extend webtrees click function
	jQuery("#main").on("click", "#separator", function() {
		if (responsiveSidebar) {
			jQuery("#indi_left").toggle();
		}
	});

	// paging tabs on the indi page
	function tabsPaging() {
		var maxWidth = jQuery("#tabs").width() - jQuery(".tab-prev").width() - jQuery(".tab-next").width() - 20;
		var tWidth = 0;

		jQuery(".tab-visible").each(function() {
			tWidth = tWidth + jQuery(this).width() + 5;
			if (tWidth > maxWidth) {
				jQuery(this).removeClass("tab-visible").addClass("tab-hidden").hide();
			}
		});

		if (tWidth + jQuery(".tab-hidden:first").width() < maxWidth) {
			if (jQuery(".tab-first").is(":hidden")) {
				jQuery(".tab-visible:first").prev(".tab-hidden").removeClass("tab-hidden").addClass("tab-visible").show();
			} else {
				jQuery(".tab-visible:last").next(".tab-hidden").removeClass("tab-hidden").addClass("tab-visible").show();
			}
		}

		toggleArrows();
	}

	function toggleTabs($tabToShow, $tabToHide) {
		$tabToShow.animate({
			width: "show"
		}, {
			duration: 300,
			done: function() {
				jQuery(this).removeClass("tab-hidden").addClass("tab-visible");
				if (jQuery(this).hasClass("tab-first")) {
					jQuery(".tab-prev").hide();
				}
				if (jQuery(this).hasClass("tab-last")) {
					jQuery(".tab-next").hide();
				}
			}
		});

		$tabToHide.animate({
			width: "hide"
		}, {
			duration: 300,
			done: function() {
				jQuery(this).removeClass("tab-visible").addClass("tab-hidden");
				if (jQuery(this).hasClass("tab-first")) {
					jQuery(".tab-prev").show();
				}
				if (jQuery(this).hasClass("tab-last")) {
					jQuery(".tab-next").show();
				}
			}
		});
	}

	function toggleArrows() {
		if (jQuery(".tab-first").is(":hidden")) {
			jQuery(".tab-prev").show();
		} else {
			jQuery(".tab-prev").hide();
		}

		if (jQuery(".tab-last").is(":hidden")) {
			jQuery(".tab-next").show();
		} else {
			jQuery(".tab-next").hide();
		}
	}

	jQuery("#tabs").on("click", ".tab-next", function() {
		$tabToShow = jQuery(".tab-visible:last").next(".tab-hidden");
		$tabToHide = jQuery(".tab-visible:first");
		toggleTabs($tabToShow, $tabToHide);
	});

	jQuery("#tabs").on("click", ".tab-prev", function() {
		$tabToShow = jQuery(".tab-visible:first").prev(".tab-hidden");
		$tabToHide = jQuery(".tab-visible:last");
		toggleTabs($tabToShow, $tabToHide);
	});

	jQuery(".ui-tabs-nav").waitUntilExists(function() {
		jQuery("li[role=tab]").addClass("tab-visible");
		jQuery("li[role=tab]").first().addClass("tab-first");
		jQuery("li[role=tab]").last().addClass("tab-last");

		jQuery(".tab-first").before('<li class="tab-prev"><i class="icon-larrow"></i></li>');
		jQuery(".tab-last").after('<li class="tab-next"><i class="icon-rarrow"></i></li>');
		tabsPaging();
	});

	jQuery(window).resize(function() {
		tabsPaging();
	});
}

// Styling of the family page
if (WT_SCRIPT_NAME === "family.php") {
	// consistent styling (like indi page)
	jQuery(".facts_table").addClass("ui-widget-content");

	// add some classes to style particular elements
	jQuery("#family-table td:first").addClass("left-table").next("td").addClass("right-table");
	jQuery('.right-table > table tr:eq(1)').addClass("parents-table");
}


// media list - don't list filenames
if (jQuery(".media-list").length > 0) {
	jQuery(".list_item.name2").each(function() {
		jQuery(this).next("br").remove();
		jQuery(this).next("a").remove();
	});
}

if (WT_SCRIPT_NAME === "edit_interface.php") {
	// census assistant module
	// replace delete button with our own
	jQuery(".census-assistant button").waitUntilExists(function() {
		jQuery(this).parent("td").html("<i class=\"deleteicon\">");
	});

	jQuery(".deleteicon").waitUntilExists(function() {
		jQuery(this).on("click", function() {
			jQuery(this).parents("tr").remove();
		});
	});

	// use same style for submenu flyout as in the individual sidebar
	jQuery(".census-assistant").waitUntilExists(function() {
		jQuery(this).find(".ltrnav").removeClass().addClass("submenu flyout").find(".name2").removeAttr("style");
	});
}