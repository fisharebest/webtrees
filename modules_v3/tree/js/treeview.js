/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

function TreeViewHandler(treeview_instance) {
    var tv = this; // Store "this" for usage within jQuery functions where "this" is not this ;-)

    this.treeview = jQuery("#" + treeview_instance + "_in");
    this.loadingImage = jQuery("#" + treeview_instance + "_loading");
    this.toolbox = jQuery("#tv_tools");
    this.buttons = jQuery(".tv_button:first", this.toolbox);
    this.zoom = 100; // in percent
    this.boxWidth = 180; // default family box width
    this.boxExpandedWidth = 250; // default expanded family box width
    this.cookieDays = 3; // lifetime of preferences memory, in days
    this.ajaxUrl = "module.php?mod=tree&ged=" + encodeURIComponent(WT_GEDCOM) + "&instance=" + treeview_instance + "&mod_action=";

    this.container = this.treeview.parent(); // Store the container element ("#" + treeview_instance + "_out")
    this.auto_box_width = false;
    this.updating = false;

    // Restore user preferences
    if (readCookie("compact") === "true") {
        tv.compact();
    }

    // Define the draggables
    tv.treeview.draggable({
        cursor: "move",
        stop: function () {
            tv.updateTree();
        }
    });

    // Add click handlers to buttons
    tv.toolbox.find("#tvbCompact").each(function (index, tvCompact) {
        tvCompact.onclick = function () {
            tv.compact();
        };
    });
    // If we click the "hide/show all partners" button, toggle the setting before reloading the page
    tv.toolbox.find("#tvbAllPartners").each(function (index, tvAllPartners) {
        tvAllPartners.onclick = function () {
            createCookie("allPartners", readCookie("allPartners") === "true" ? "false" : "true", tv.cookieDays);
            document.location = document.location;
        };
    });
    tv.toolbox.find("#tvbOpen").each(function (index, tvbOpen) {
        var b = jQuery(tvbOpen, tv.toolbox);
        tvbOpen.onclick = function () {
            b.addClass("tvPressed");
            tv.setLoading();
            var e = jQuery.Event("click");
            tv.treeview.find(".tv_box:not(.boxExpanded)").each(function (index, box) {
                var pos = jQuery(box, tv.treeview).offset();
                if (pos.left >= tv.leftMin && pos.left <= tv.leftMax && pos.top >= tv.topMin && pos.top <= tv.topMax) {
                    tv.expandBox(box, e);
                }
            });
            b.removeClass("tvPressed");
            tv.setComplete();
        };
    });
    tv.toolbox.find("#tvbClose").each(function (index, tvbClose) {
        var b = jQuery(tvbClose, tv.toolbox);
        tvbClose.onclick = function () {
            b.addClass("tvPressed");
            tv.setLoading();
            tv.treeview.find(".tv_box.boxExpanded").each(function (index, box) {
                jQuery(box).css("display", "none").removeClass("boxExpanded").parent().find(".tv_box.collapsedContent").css("display", "block");
            });
            b.removeClass("tvPressed");
            tv.setComplete();
        };
    });

    tv.centerOnRoot(); // fire ajax update if needed, which call setComplete() when all is loaded
}
/**
 * Class TreeView setLoading method
 */
TreeViewHandler.prototype.setLoading = function () {
    this.treeview.css("cursor", "wait");
    this.loadingImage.css("display", "block");
};
/**
 * Class TreeView setComplete  method
 */
TreeViewHandler.prototype.setComplete = function () {
    this.treeview.css("cursor", "move");
    this.loadingImage.css("display", "none");
};

/**
 * Class TreeView getSize  method
 * Store the viewport current size
 */
TreeViewHandler.prototype.getSize = function () {
    var tv = this;
    // retrieve the current container bounding box
    var container = tv.container.parent();
    var offset = container.offset();
    tv.leftMin = offset.left;
    tv.leftMax = tv.leftMin + container.innerWidth();
    tv.topMin = offset.top;
    tv.topMax = tv.topMin + container.innerHeight();
    /*
     var frm = jQuery("#tvTreeBorder");
     tv.treeview.css("width", frm.width());
     tv.treeview.css("height", frm.height());*/
};

/**
 * Class TreeView updateTree  method
 * Perform ajax requests to complete the tree after drag
 * param boolean @center center on root person when done
 */
TreeViewHandler.prototype.updateTree = function (center, button) {
    var tv = this; // Store "this" for usage within jQuery functions where "this" is not this ;-)
    var to_load = [];
    var elts = [];
    this.getSize();

    // check which td with datafld attribute are within the container bounding box
    // and therefore need to be dynamically loaded
    tv.treeview.find("td[abbr]").each(function (index, el) {
        el = jQuery(el, tv.treeview);
        var pos = el.offset();
        if (pos.left >= tv.leftMin && pos.left <= tv.leftMax && pos.top >= tv.topMin && pos.top <= tv.topMax) {
            to_load.push(el.attr("abbr"));
            elts.push(el);
        }
    });
    // if some boxes need update, we perform an ajax request
    if (to_load.length > 0) {
        tv.updating = true;
        tv.setLoading();
        jQuery.ajax({
            url: tv.ajaxUrl + "getPersons",
            dataType: "json",
            data: "q=" + to_load.join(";"),
            success: function (ret) {
                var nb = elts.length;
                var root_element = jQuery(".rootPerson", this.treeview);
                var l = root_element.offset().left;
                for (var i = 0; i < nb; i++) {
                    elts[i].removeAttr("abbr").html(ret[i]);
                }
                // repositionning
                tv.treeview.offset({left: tv.treeview.offset().left - root_element.offset().left + l});
                // we now ajust the draggable treeview size to its content size
                tv.getSize();
            },
            complete: function () {
                if (tv.treeview.find("td[abbr]").length) {
                    tv.updateTree(center, button); // recursive call
                }
                // the added boxes need that in mode compact boxes
                if (tv.auto_box_width) {
                    tv.treeview.find(".tv_box").css("width", "auto");
                }
                tv.updating = true; // avoid an unuseful recursive call when all requested persons are loaded
                if (center) {
                    tv.centerOnRoot();
                }
                if (button) {
                    button.removeClass("tvPressed");
                }
                tv.setComplete();
                tv.updating = false;
            },
            timeout: function () {
                if (button) {
                    button.removeClass("tvPressed");
                }
                tv.updating = false;
                tv.setComplete();
            }
        });
    } else {
        if (button) {
            button.removeClass("tvPressed");
        }
        tv.setComplete();
    }
    return false;
};

/**
 * Class TreeView compact method
 */
TreeViewHandler.prototype.compact = function () {
    var tv = this;
    var b = jQuery("#tvbCompact", tv.toolbox);
    tv.setLoading();
    if (tv.auto_box_width) {
        var w = tv.boxWidth * (tv.zoom / 100) + "px";
        var ew = tv.boxExpandedWidth * (tv.zoom / 100) + "px";
        tv.treeview.find(".tv_box:not(boxExpanded)", tv.treeview).css("width", w);
        tv.treeview.find(".boxExpanded", tv.treeview).css("width", ew);
        tv.auto_box_width = false;
        if (readCookie("compact")) {
            createCookie("compact", false, tv.cookieDays);
        }
        b.removeClass("tvPressed");
    } else {
        tv.treeview.find(".tv_box").css("width", "auto");
        tv.auto_box_width = true;
        if (!readCookie("compact")) {
            createCookie("compact", true, tv.cookieDays);
        }
        if (!tv.updating) {
            tv.updateTree();
        }
        b.addClass("tvPressed");
    }
    tv.setComplete();
    return false;
};

/**
 * Class TreeView centerOnRoot method
 */
TreeViewHandler.prototype.centerOnRoot = function () {
    this.loadingImage.css("display", "block");
    var tvc = this.container;
    var tvc_width = tvc.innerWidth() / 2;
    if (isNaN(tvc_width)) {
        return false;
    }
    var tvc_height = tvc.innerHeight() / 2;
    var root_person = jQuery(".rootPerson", this.treeview);
    this.treeview.offset({
        left: tvc.offset().left + this.treeview.offset().left + tvc_width - root_person.offset().left - root_person.outerWidth() / 2,
        top: tvc.offset().top + this.treeview.offset().top + tvc_height - root_person.offset().top - root_person.outerHeight() / 2
    });

    if (!this.updating) {
        this.updateTree(true);
    }
    return false;
};

/**
 * Class TreeView expandBox method
 * param string @box the person box element
 * param string @event the call event
 * param string @pid the person id
 *
 * called ONLY for elements which have NOT the class tv_link to avoid unuseful requests to the server
 */
TreeViewHandler.prototype.expandBox = function (box, event) {
    var t = jQuery(event.target);
    if (t.hasClass("tv_link")) {
        return false;
    }

    var box = jQuery(box, this.treeview);
    var bc = box.parent(); // bc is Box Container
    var pid = box.attr("abbr");
    var tv = this; // Store "this" for usage within jQuery functions where "this" is not this ;-)
    var expanded;
    var collapsed;

    if (bc.hasClass("detailsLoaded")) {
        collapsed = bc.find(".collapsedContent");
        expanded = bc.find(".tv_box:not(.collapsedContent)");
    } else {
        // Cache the box content as an hidden person's box in the box's parent element
        expanded = box;
        collapsed = box.clone();
        bc.append(collapsed.addClass("collapsedContent").css("display", "none"));
        // we add a waiting image at the right side of the box
        // TODO : manage rtl (left side instead of right in rtl mode)
        var loading_image = this.loadingImage.find("img").clone().addClass("tv_box_loading").css("display", "block");
        box.prepend(loading_image);
        tv.updating = true;
        tv.setLoading();
        // perform the Ajax request and load the result in the box
        box.load(tv.ajaxUrl + "getDetails&pid=" + pid, function () {
            // If Lightbox module is active, we reinitialize it for the new links
            if (typeof CB_Init === "function") {
                CB_Init();
            }
            box.css("width", tv.boxExpandedWidth * (tv.zoom / 100) + "px");
            loading_image.remove();
            bc.addClass("detailsLoaded");
            tv.setComplete();
            tv.updating = false;
        });
    }
    if (box.hasClass("boxExpanded")) {
        expanded.css("display", "none");
        collapsed.css("display", "block");
        box.removeClass("boxExpanded");
    } else {
        expanded.css("display", "block");
        collapsed.css("display", "none");
        expanded.addClass("boxExpanded");
    }
    // we must ajust the draggable treeview size to its content size
    this.getSize();
    return false;
};

function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = name + "=" + value + "; expires=" + date.toGMTString() + "; path=/";
    } else {
        document.cookie = name + "=" + value + "; path=/";
    }
}

function readCookie(name) {
    var name_equals = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1, c.length);
        }
        if (c.indexOf(name_equals) === 0) {
            return c.substring(name_equals.length, c.length);
        }
    }
    return null;
}
