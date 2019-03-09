/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

// "use strict";

// Specifications for various types of popup edit window.
var edit_window_specs = 'width=620,height=600,left=75,top=50,resizable=1,scrollbars=1'; // edit_interface.php, add_media.php, gedrecord.php
var indx_window_specs = 'width=600,height=600,left=75,top=50,resizable=1,scrollbars=1'; // module configuration
var news_window_specs = 'width=620,height=600,left=75,top=50,resizable=1,scrollbars=1'; // edit_news.php
var find_window_specs = 'width=550,height=600,left=75,top=50,resizable=1,scrollbars=1'; // find.php, inverse_link.php
var mesg_window_specs = 'width=620,height=600,left=75,top=50,resizable=1,scrollbars=1'; // message.php
var chan_window_specs = 'width=600,height=600,left=75,top=50,resizable=1,scrollbars=1'; // edit_changes.php
var mord_window_specs = 'width=500,height=600,left=75,top=50,resizable=1,scrollbars=1'; // edit_interface.php, media reorder
var assist_window_specs = 'width=800,height=600,left=75,top=50,resizable=1,scrollbars=1'; // edit_interface.php, used for census assistant
var gmap_window_specs = 'width=650,height=600,left=75,top=50,resizable=1,scrollbars=1'; // googlemap module place editing
var fam_nav_specs = 'width=350,height=550,left=25,top=75,resizable=1,scrollbars=1'; // media_0_inverselink.php

var pastefield, nameElement, remElement; // Elements to paste to

// "rtl" on right-to-left pages.
var textDirection = jQuery('html').attr('dir');

// Get a help message.
function helpDialog(topic, module) {
    jQuery.getJSON('help_text.php?help=' + topic + '&mod=' + module, function (json) {
        modalNotes(json.content, json.title);
    });
}

// Create a modal dialog to display notes & help
function modalNotes(content, title) {
    jQuery('<div title="' + title + '"></div>')
        .html(content)
        .dialog({
            modal: true,
            width: 500,
            open:  function () {
                // Close the window when we click outside it.
                var self = this;
                jQuery('.ui-widget-overlay').on('click', function () {
                    jQuery(self).dialog('close');
                });
            }
        });

    return false;
}

function closePopupAndReloadParent(url) {
    if (parent.opener) {
        if (url) {
            parent.opener.location = url;
        } else {
            parent.opener.location.reload();
        }
    }
    window.close();
}

function expand_layer(sid) {
    jQuery("#" + sid + "_img").toggleClass("icon-plus icon-minus");
    jQuery('#' + sid).slideToggle("fast");
    jQuery('#' + sid + '-alt').toggle(); // hide something when we show the layer - and vice-versa
    return false;
}

// Open the "edit interface" popup window
function edit_interface(params, windowspecs, pastefield) {
    var features = windowspecs || edit_window_specs;
    window.pastefield = pastefield;
    var url = 'edit_interface.php?' + jQuery.param(params) + '&ged=' + WT_GEDCOM;
    window.open(url, '_blank', features);
    return false;
}

function edit_record(xref, fact_id) {
    return edit_interface({
        "action":  "edit",
        "xref":    xref,
        "fact_id": fact_id
    });
}

function add_fact(xref, fact) {
    return edit_interface({
        "action": "add",
        "xref":   xref,
        "fact":   fact
    });
}

function edit_raw(xref) {
    return edit_interface({
        "action": "editraw",
        "xref":   xref
    });
}

function edit_note(xref) {
    return edit_interface({
        "action": "editnote",
        "xref":   xref
    });
}

function add_record(xref, fact_field) {
    var fact = jQuery('#' + fact_field).val();
    if (fact) {
        if (fact === "OBJE") {
            window.open('addmedia.php?action=showmediaform&linkid=' + encodeURIComponent(xref) + '&ged=' + encodeURIComponent(WT_GEDCOM), '_blank', edit_window_specs);
        } else {
            return add_fact(xref, fact);
        }
    }
    return false;
}

function reorder_media(xref) {
    return edit_interface({
        "action": "reorder_media",
        "xref":   xref
    }, mord_window_specs);
}

function add_new_record(xref, fact) {
    return edit_interface({
        "action": "add",
        "xref":   xref,
        "fact":   fact
    });
}

// Add a child to an existing family
function add_child_to_family(xref, gender) {
    return edit_interface({
        "action": "add_child_to_family",
        "gender": gender,
        "xref":   xref
    });
}

// Add a child to an existing individual (creating a one-parent family)
function add_child_to_individual(xref, gender) {
    return edit_interface({
        "action": "add_child_to_individual",
        "gender": gender,
        "xref":   xref
    });
}

// Add a new parent to an existing individual (creating a one-parent family)
function add_parent_to_individual(xref, gender) {
    return edit_interface({
        "action": "add_parent_to_individual",
        "xref":   xref,
        "gender": gender
    });
}

// Add a spouse to an existing family
function add_spouse_to_family(xref, famtag) {
    return edit_interface({
        "action": "add_spouse_to_family",
        "xref":   xref,
        "famtag": famtag
    });
}

function add_unlinked_indi() {
    return edit_interface({
        "action": "add_unlinked_indi"
    });
}

// Add a spouse to an existing individual (creating a new family)
function add_spouse_to_individual(xref, famtag) {
    return edit_interface({
        "action": "add_spouse_to_individual",
        "xref":   xref,
        "famtag": famtag
    });
}

function linkspouse(xref, famtag) {
    return edit_interface({
        "action": "linkspouse",
        "xref":   xref,
        "famtag": famtag,
        "famid":  "new"
    });
}

function add_famc(xref) {
    return edit_interface({
        "action": "addfamlink",
        "xref":   xref
    });
}

function edit_name(xref, fact_id) {
    return edit_interface({
        "action":  "editname",
        "xref":    xref,
        "fact_id": fact_id
    });
}

function add_name(xref) {
    return edit_interface({
        "action": "addname",
        "xref":   xref
    });
}

// Accept the changes to a record - and reload the page
function accept_changes(xref) {
    jQuery.post('action.php', {
        action: 'accept-changes',
        xref:   xref,
        ged:    WT_GEDCOM,
        csrf:   WT_CSRF_TOKEN
        },
        function () {
            location.reload();
        });
    return false;
}

// Reject the changes to a record - and reload the page
function reject_changes(xref) {
    jQuery.post('action.php', {
        action: 'reject-changes',
        xref:   xref,
        ged:    WT_GEDCOM,
        csrf:   WT_CSRF_TOKEN
        },
        function () {
            location.reload();
        });
    return false;
}

// Delete a record - and reload the page
function delete_record(message, xref, gedcom) {
    if (confirm(message)) {
        jQuery.post('action.php', {
            action: 'delete-record',
            xref:   xref,
            ged:    typeof gedcom === 'undefined' ? WT_GEDCOM : gedcom,
            csrf:   WT_CSRF_TOKEN
            },
            function () {
                location.reload();
            });
    }
    return false;
}

// Delete a fact - and reload the page
function delete_fact(message, xref, fact_id) {
    if (confirm(message)) {
        jQuery.post('action.php', {
            action:  'delete-fact',
            xref:    xref,
            fact_id: fact_id,
            ged:     WT_GEDCOM,
            csrf:    WT_CSRF_TOKEN
            },
            function () {
                location.reload();
            });
    }
    return false;
}

// Remove links from one record to another - and reload the page
function unlink_media(message, source, target) {
    if (confirm(message)) {
        jQuery.post('action.php', {
            action: 'unlink-media',
            source: source,
            target: target,
            ged:    WT_GEDCOM,
            csrf:   WT_CSRF_TOKEN
            },
            function () {
                location.reload();
            });
    }
    return false;
}

// Copy a fact to the clipboard
function copy_fact(xref, fact_id) {
    jQuery.post('action.php', {
        action:  'copy-fact',
        xref:    xref,
        fact_id: fact_id,
        ged:     WT_GEDCOM,
        csrf:    WT_CSRF_TOKEN
        },
        function () {
            location.reload();
        });
    return false;
}

// Paste a fact from the clipboard
function paste_fact(xref, element) {
    jQuery.post('action.php', {
        action:  'paste-fact',
        xref:    xref,
        fact_id: jQuery(element).val(), // element is the <select> containing the option
        ged:     WT_GEDCOM,
        csrf:    WT_CSRF_TOKEN
        },
        function () {
            location.reload();
        });
    return false;
}

// Delete a user - and reload the page
function delete_user(message, user_id) {
    if (confirm(message)) {
        jQuery.post('action.php', {
            action:  'delete-user',
            user_id: user_id,
            csrf:    WT_CSRF_TOKEN
            },
            function () {
                location.reload();
            });
    }
    return false;
}

// Masquerade as another user - and reload the page.
function masquerade(user_id) {
    jQuery.post('action.php', {
        action:  'masquerade',
        user_id: user_id,
        csrf:    WT_CSRF_TOKEN
        },
        function () {
            location.reload();
        });
    return false;
}

function reorder_children(xref) {
    return edit_interface({
        "action": "reorder_children",
        "xref":   xref
    });
}

function reorder_families(xref) {
    return edit_interface({
        "action": "reorder_fams",
        "xref":   xref
    });
}

function reply(username, subject) {
    window.open('message.php?to=' + encodeURIComponent(username) + '&subject=' + encodeURIComponent(subject) + '&ged=' + encodeURIComponent(WT_GEDCOM), '_blank', mesg_window_specs);
    return false;
}

function delete_message(id) {
    window.open('message.php?action=delete&id=' + encodeURIComponent(id) + '&ged=' + encodeURIComponent(WT_GEDCOM), '_blank', mesg_window_specs);
    return false;
}

function change_family_members(xref) {
    return edit_interface({
        "action": "changefamily",
        "xref":   xref
    });
}

function addnewsource(field) {
    return edit_interface({
        "action": "addnewsource",
        "xref":   "newsour"
    }, null, field);
}

function addnewrepository(field) {
    return edit_interface({
        "action": "addnewrepository",
        "xref":   "newrepo"
    }, null, field);
}

function addnewnote(field) {
    return edit_interface({
        "action": "addnewnote",
        "noteid": "newnote"
    }, null, field);
}

function addnewnote_assisted(field, xref, census) {
    return edit_interface({
        "action": "addnewnote_assisted",
        "noteid": "newnote",
        "xref":   xref,
        "census": census
    }, assist_window_specs, field);
}

function addmedia_links(field, iid, iname) {
    pastefield = field;
    insertRowToTable(iid, iname);
    return false;
}

function valid_date(datefield) {
    var months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
    var hijri_months = ["MUHAR", "SAFAR", "RABIA", "RABIT", "JUMAA", "JUMAT", "RAJAB", "SHAAB", "RAMAD", "SHAWW", "DHUAQ", "DHUAH"];
    var hebrew_months = ["TSH", "CSH", "KSL", "TVT", "SHV", "ADR", "ADS", "NSN", "IYR", "SVN", "TMZ", "AAV", "ELL"];
    var french_months = ["VEND", "BRUM", "FRIM", "NIVO", "PLUV", "VENT", "GERM", "FLOR", "PRAI", "MESS", "THER", "FRUC", "COMP"];
    var jalali_months = ["FARVA", "ORDIB", "KHORD", "TIR", "MORDA", "SHAHR", "MEHR", "ABAN", "AZAR", "DEY", "BAHMA", "ESFAN"];

    var datestr = datefield.value;
    // if a date has a date phrase marked by () this has to be excluded from altering
    var datearr = datestr.split("(");
    var datephrase = "";
    if (datearr.length > 1) {
        datestr = datearr[0];
        datephrase = datearr[1];
    }

    // Gedcom dates are upper case
    datestr = datestr.toUpperCase();
    // Gedcom dates have no leading/trailing/repeated whitespace
    datestr = datestr.replace(/\s+/, " ");
    datestr = datestr.replace(/(^\s)|(\s$)/, "");
    // Gedcom dates have spaces between letters and digits, e.g. "01JAN2000" => "01 JAN 2000"
    datestr = datestr.replace(/(\d)([A-Z])/, "$1 $2");
    datestr = datestr.replace(/([A-Z])(\d)/, "$1 $2");

    // Shortcut for quarter format, "Q1 1900" => "BET JAN 1900 AND MAR 1900". See [ 1509083 ]
    if (datestr.match(/^Q ([1-4]) (\d\d\d\d)$/)) {
        datestr = "BET " + months[RegExp.$1 * 3 - 3] + " " + RegExp.$2 + " AND " + months[RegExp.$1 * 3 - 1] + " " + RegExp.$2;
    }

    // Shortcut for @#Dxxxxx@ 01 01 1400, etc.
    if (datestr.match(/^(@#DHIJRI@|HIJRI)( \d?\d )(\d?\d)( \d?\d?\d?\d)$/)) {
        datestr = "@#DHIJRI@" + RegExp.$2 + hijri_months[parseInt(RegExp.$3, 10) - 1] + RegExp.$4;
    }
    if (datestr.match(/^(@#DJALALI@|JALALI)( \d?\d )(\d?\d)( \d?\d?\d?\d)$/)) {
        datestr = "@#DJALALI@" + RegExp.$2 + jalali_months[parseInt(RegExp.$3, 10) - 1] + RegExp.$4;
    }
    if (datestr.match(/^(@#DHEBREW@|HEBREW)( \d?\d )(\d?\d)( \d?\d?\d?\d)$/)) {
        datestr = "@#DHEBREW@" + RegExp.$2 + hebrew_months[parseInt(RegExp.$3, 10) - 1] + RegExp.$4;
    }
    if (datestr.match(/^(@#DFRENCH R@|FRENCH)( \d?\d )(\d?\d)( \d?\d?\d?\d)$/)) {
        datestr = "@#DFRENCH R@" + RegExp.$2 + french_months[parseInt(RegExp.$3, 10) - 1] + RegExp.$4;
    }

    // e.g. 17.11.1860, 03/04/2005 or 1999-12-31. Use locale settings where DMY order is ambiguous.
    var qsearch = /^([^\d]*)(\d+)[^\d](\d+)[^\d](\d+)$/i;
    if (qsearch.exec(datestr)) {
        var f0 = RegExp.$1;
        var f1 = parseInt(RegExp.$2, 10);
        var f2 = parseInt(RegExp.$3, 10);
        var f3 = parseInt(RegExp.$4, 10);
        var dmy = 'DMY';
        if (typeof(locale_date_format) !== 'undefined') {
            if (locale_date_format === 'MDY' || locale_date_format === 'YMD') {
                dmy = locale_date_format;
            }
        }
        var yyyy = new Date().getFullYear();
        var yy = yyyy % 100;
        var cc = yyyy - yy;
        if (dmy === 'DMY' && f1 <= 31 && f2 <= 12 || f1 > 13 && f1 <= 31 && f2 <= 12 && f3 > 31) {
            datestr = f0 + f1 + " " + months[f2 - 1] + " " + (f3 >= 100 ? f3 : (f3 <= yy ? f3 + cc : f3 + cc - 100));
        } else {
            if (dmy === 'MDY' && f1 <= 12 && f2 <= 31 || f2 > 13 && f2 <= 31 && f1 <= 12 && f3 > 31) {
                datestr = f0 + f2 + " " + months[f1 - 1] + " " + (f3 >= 100 ? f3 : (f3 <= yy ? f3 + cc : f3 + cc - 100));
            } else {
                if (dmy === 'YMD' && f2 <= 12 && f3 <= 31 || f3 > 13 && f3 <= 31 && f2 <= 12 && f1 > 31) {
                    datestr = f0 + f3 + " " + months[f2 - 1] + " " + (f1 >= 100 ? f1 : (f1 <= yy ? f1 + cc : f1 + cc - 100));
                }
            }
        }
    }

    // Shortcuts for date ranges
    datestr = datestr.replace(/^[>]([\w ]+)$/, "AFT $1");
    datestr = datestr.replace(/^[<]([\w ]+)$/, "BEF $1");
    datestr = datestr.replace(/^([\w ]+)[-]$/, "FROM $1");
    datestr = datestr.replace(/^[-]([\w ]+)$/, "TO $1");
    datestr = datestr.replace(/^[~]([\w ]+)$/, "ABT $1");
    datestr = datestr.replace(/^[*]([\w ]+)$/, "EST $1");
    datestr = datestr.replace(/^[#]([\w ]+)$/, "CAL $1");
    datestr = datestr.replace(/^([\w ]+) ?- ?([\w ]+)$/, "BET $1 AND $2");
    datestr = datestr.replace(/^([\w ]+) ?~ ?([\w ]+)$/, "FROM $1 TO $2");

    // Convert full months to short months
    datestr = datestr.replace(/(JANUARY)/, "JAN");
    datestr = datestr.replace(/(FEBRUARY)/, "FEB");
    datestr = datestr.replace(/(MARCH)/, "MAR");
    datestr = datestr.replace(/(APRIL)/, "APR");
    datestr = datestr.replace(/(MAY)/, "MAY");
    datestr = datestr.replace(/(JUNE)/, "JUN");
    datestr = datestr.replace(/(JULY)/, "JUL");
    datestr = datestr.replace(/(AUGUST)/, "AUG");
    datestr = datestr.replace(/(SEPTEMBER)/, "SEP");
    datestr = datestr.replace(/(OCTOBER)/, "OCT");
    datestr = datestr.replace(/(NOVEMBER)/, "NOV");
    datestr = datestr.replace(/(DECEMBER)/, "DEC");

    // Americans frequently enter dates as SEP 20, 1999
    // No need to internationalise this, as this is an english-language issue
    datestr = datestr.replace(/(JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)\.? (\d\d?)[, ]+(\d\d\d\d)/, "$2 $1 $3");

    // Apply leading zero to day numbers
    datestr = datestr.replace(/(^| )(\d [A-Z]{3,5} \d{4})/, "$10$2");

    if (datephrase) {
        datestr = datestr + " (" + datephrase;
    }
    // Only update it if is has been corrected - otherwise input focus
    // moves to the end of the field unnecessarily
    if (datefield.value !== datestr) {
        datefield.value = datestr;
    }
}

var menutimeouts = [];

function show_submenu(elementid, parentid) {
    var pagewidth = document.body.scrollWidth + document.documentElement.scrollLeft;
    var element = document.getElementById(elementid);

    if (element && element.style) {
        if (document.all) {
            pagewidth = document.body.offsetWidth;
        } else {
            pagewidth = document.body.scrollWidth + document.documentElement.scrollLeft - 55;
            if (textDirection === "rtl") {
                boxright = element.offsetLeft + element.offsetWidth + 10;
            }
        }

        //-- make sure the submenu is the size of the largest child
        var maxwidth = 0;
        var count = element.childNodes.length;
        for (var i = 0; i < count; i++) {
            var child = element.childNodes[i];
            if (child.offsetWidth > maxwidth + 5) {
                maxwidth = child.offsetWidth;
            }
        }
        if (element.offsetWidth < maxwidth) {
            element.style.width = maxwidth + "px";
        }
        var pelement, boxright;
        pelement = document.getElementById(parentid);
        if (pelement) {
            element.style.left = pelement.style.left;
            boxright = element.offsetLeft + element.offsetWidth + 10;
            if (boxright > pagewidth) {
                var menuleft = pagewidth - element.offsetWidth;
                element.style.left = menuleft + "px";
            }
        }

        if (element.offsetLeft < 0) {
            element.style.left = "0px";
        }

        //-- put scrollbars on really long menus
        if (element.offsetHeight > 500) {
            element.style.height = '400px';
            element.style.overflow = 'auto';
        }

        element.style.visibility = 'visible';
    }
    clearTimeout(menutimeouts[elementid]);
    menutimeouts[elementid] = null;
}

function hide_submenu(elementid) {
    if (typeof menutimeouts[elementid] !== "number") {
        return;
    }
    var element = document.getElementById(elementid);
    if (element && element.style) {
        element.style.visibility = 'hidden';
    }
    clearTimeout(menutimeouts[elementid]);
    menutimeouts[elementid] = null;
}

function timeout_submenu(elementid) {
    if (typeof menutimeouts[elementid] !== "number") {
        menutimeouts[elementid] = setTimeout("hide_submenu('" + elementid + "')", 100);
    }
}

function statusDisable(sel) {
    var cbox = document.getElementById(sel);
    cbox.checked = false;
    cbox.disabled = true;
}

function statusEnable(sel) {
    var cbox = document.getElementById(sel);
    cbox.disabled = false;
}

function statusChecked(sel) {
    var cbox = document.getElementById(sel);
    cbox.checked = true;
}

var monthLabels = [];
monthLabels[1] = "January";
monthLabels[2] = "February";
monthLabels[3] = "March";
monthLabels[4] = "April";
monthLabels[5] = "May";
monthLabels[6] = "June";
monthLabels[7] = "July";
monthLabels[8] = "August";
monthLabels[9] = "September";
monthLabels[10] = "October";
monthLabels[11] = "November";
monthLabels[12] = "December";

var monthShort = [];
monthShort[1] = "JAN";
monthShort[2] = "FEB";
monthShort[3] = "MAR";
monthShort[4] = "APR";
monthShort[5] = "MAY";
monthShort[6] = "JUN";
monthShort[7] = "JUL";
monthShort[8] = "AUG";
monthShort[9] = "SEP";
monthShort[10] = "OCT";
monthShort[11] = "NOV";
monthShort[12] = "DEC";

var daysOfWeek = [];
daysOfWeek[0] = "S";
daysOfWeek[1] = "M";
daysOfWeek[2] = "T";
daysOfWeek[3] = "W";
daysOfWeek[4] = "T";
daysOfWeek[5] = "F";
daysOfWeek[6] = "S";

var weekStart = 0;

function cal_setMonthNames(jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, dec) {
    monthLabels[1] = jan;
    monthLabels[2] = feb;
    monthLabels[3] = mar;
    monthLabels[4] = apr;
    monthLabels[5] = may;
    monthLabels[6] = jun;
    monthLabels[7] = jul;
    monthLabels[8] = aug;
    monthLabels[9] = sep;
    monthLabels[10] = oct;
    monthLabels[11] = nov;
    monthLabels[12] = dec;
}

function cal_setDayHeaders(sun, mon, tue, wed, thu, fri, sat) {
    daysOfWeek[0] = sun;
    daysOfWeek[1] = mon;
    daysOfWeek[2] = tue;
    daysOfWeek[3] = wed;
    daysOfWeek[4] = thu;
    daysOfWeek[5] = fri;
    daysOfWeek[6] = sat;
}

function cal_setWeekStart(day) {
    if (day >= 0 && day < 7) {
        weekStart = day;
    }
}

function cal_toggleDate(dateDivId, dateFieldId) {
    var dateDiv = document.getElementById(dateDivId);
    if (!dateDiv) {
        return false;
    }

    if (dateDiv.style.visibility === 'visible') {
        dateDiv.style.visibility = 'hidden';
        return false;
    }
    if (dateDiv.style.visibility === 'show') {
        dateDiv.style.visibility = 'hide';
        return false;
    }

    var dateField = document.getElementById(dateFieldId);
    if (!dateField) {
        return false;
    }

    /* Javascript calendar functions only work with precise gregorian dates "D M Y" or "Y" */
    var greg_regex = /((\d+ (JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC) )?\d+)/;
    var date;
    if (greg_regex.exec(dateField.value)) {
        date = new Date(RegExp.$1);
    } else {
        date = new Date();
    }

    dateDiv.innerHTML = cal_generateSelectorContent(dateFieldId, dateDivId, date);
    if (dateDiv.style.visibility === 'hidden') {
        dateDiv.style.visibility = 'visible';
        return false;
    }
    if (dateDiv.style.visibility === 'hide') {
        dateDiv.style.visibility = 'show';
        return false;
    }
    return false;
}

function cal_generateSelectorContent(dateFieldId, dateDivId, date) {
    var i, j;
    var content = '<table border="1"><tr>';
    content += '<td><select name="' + dateFieldId + '_daySelect" id="' + dateFieldId + '_daySelect" onchange="return cal_updateCalendar(\'' + dateFieldId + '\', \'' + dateDivId + '\');">';
    for (i = 1; i < 32; i++) {
        content += '<option value="' + i + '"';
        if (date.getDate() === i) {
            content += ' selected="selected"';
        }
        content += '>' + i + '</option>';
    }
    content += '</select></td>';
    content += '<td><select name="' + dateFieldId + '_monSelect" id="' + dateFieldId + '_monSelect" onchange="return cal_updateCalendar(\'' + dateFieldId + '\', \'' + dateDivId + '\');">';
    for (i = 1; i < 13; i++) {
        content += '<option value="' + i + '"';
        if (date.getMonth() + 1 === i) {
            content += ' selected="selected"';
        }
        content += '>' + monthLabels[i] + '</option>';
    }
    content += '</select></td>';
    content += '<td><input type="text" name="' + dateFieldId + '_yearInput" id="' + dateFieldId + '_yearInput" size="5" value="' + date.getFullYear() + '" onchange="return cal_updateCalendar(\'' + dateFieldId + '\', \'' + dateDivId + '\');" /></td></tr>';
    content += '<tr><td colspan="3">';
    content += '<table width="100%">';
    content += '<tr>';
    j = weekStart;
    for (i = 0; i < 7; i++) {
        content += '<td ';
        content += 'class="descriptionbox"';
        content += '>';
        content += daysOfWeek[j];
        content += '</td>';
        j++;
        if (j > 6) {
            j = 0;
        }
    }
    content += '</tr>';

    var tdate = new Date(date.getFullYear(), date.getMonth(), 1);
    var day = tdate.getDay();
    day = day - weekStart;
    var daymilli = 1000 * 60 * 60 * 24;
    tdate = tdate.getTime() - (day * daymilli) + (daymilli / 2);
    tdate = new Date(tdate);

    for (j = 0; j < 6; j++) {
        content += '<tr>';
        for (i = 0; i < 7; i++) {
            content += '<td ';
            if (tdate.getMonth() === date.getMonth()) {
                if (tdate.getDate() === date.getDate()) {
                    content += 'class="descriptionbox"';
                } else {
                    content += 'class="optionbox"';
                }
            } else {
                content += 'style="background-color:#EAEAEA; border: solid #AAAAAA 1px;"';
            }
            content += '><a href="#" onclick="return cal_dateClicked(\'' + dateFieldId + '\', \'' + dateDivId + '\', ' + tdate.getFullYear() + ', ' + tdate.getMonth() + ', ' + tdate.getDate() + ');">';
            content += tdate.getDate();
            content += '</a></td>';
            var datemilli = tdate.getTime() + daymilli;
            tdate = new Date(datemilli);
        }
        content += '</tr>';
    }
    content += '</table>';
    content += '</td></tr>';
    content += '</table>';

    return content;
}

function cal_setDateField(dateFieldId, year, month, day) {
    var dateField = document.getElementById(dateFieldId);
    if (!dateField) {
        return false;
    }
    if (day < 10) {
        day = "0" + day;
    }
    dateField.value = day + ' ' + monthShort[month + 1] + ' ' + year;
    return false;
}

function cal_updateCalendar(dateFieldId, dateDivId) {
    var dateSel = document.getElementById(dateFieldId + '_daySelect');
    if (!dateSel) {
        return false;
    }
    var monthSel = document.getElementById(dateFieldId + '_monSelect');
    if (!monthSel) {
        return false;
    }
    var yearInput = document.getElementById(dateFieldId + '_yearInput');
    if (!yearInput) {
        return false;
    }

    var month = parseInt(monthSel.options[monthSel.selectedIndex].value, 10);
    month = month - 1;

    var date = new Date(yearInput.value, month, dateSel.options[dateSel.selectedIndex].value);
    cal_setDateField(dateFieldId, date.getFullYear(), date.getMonth(), date.getDate());

    var dateDiv = document.getElementById(dateDivId);
    if (!dateDiv) {
        alert('no dateDiv ' + dateDivId);
        return false;
    }
    dateDiv.innerHTML = cal_generateSelectorContent(dateFieldId, dateDivId, date);

    return false;
}

function cal_dateClicked(dateFieldId, dateDivId, year, month, day) {
    cal_setDateField(dateFieldId, year, month, day);
    cal_toggleDate(dateDivId, dateFieldId);
    return false;
}

function findWindow(ged, type, pastefield, queryParams) {
    queryParams = queryParams || {};
    queryParams.type = type;
    queryParams.ged = typeof ged === 'undefined' ? WT_GEDCOM : ged;
    window.pastefield = pastefield;
    window.open('find.php?' + jQuery.param(queryParams), '_blank', find_window_specs);
    return false;
}

function findIndi(field, indiname, ged) {
    window.nameElement = indiname;
    return findWindow(ged, "indi", field);
}

function findPlace(field, ged) {
    return findWindow(ged, "place", field);
}

function findFamily(field, ged) {
    return findWindow(ged, "fam", field);
}

function findMedia(field, choose, ged) {
    return findWindow(ged, "media", field, {
        "choose": choose || "0all"
    });
}

function findSource(field, sourcename, ged) {
    window.nameElement = sourcename;
    return findWindow(ged, "source", field);
}

function findnote(field, notename, ged) {
    window.nameElement = notename;
    return findWindow(ged, "note", field);
}

function findRepository(field, ged) {
    return findWindow(ged, "repo", field);
}

function findSpecialChar(field) {
    return findWindow(undefined, "specialchar", field);
}

function findFact(field_id, field_type) {
    return findWindow(undefined, "fact" + field_type, document.getElementById(field_id), {
        "tags": document.getElementById(field_id).value
    });
}

function openerpasteid(id) {
    if (window.opener.paste_id) {
        window.opener.paste_id(id);
    }
    window.close();
}

function paste_id(value) {
    pastefield.value = value;
}

function pastename(name) {
    if (nameElement) {
        nameElement.innerHTML = name;
    }
    if (remElement) {
        remElement.style.display = "block";
    }
}

function paste_char(value) {
    if (document.selection) {
        // IE
        pastefield.focus();
        document.selection.createRange().text = value;
    } else if (pastefield.selectionStart || pastefield.selectionStart === 0) {
        // Mozilla/Chrome/Safari
        pastefield.value =
            pastefield.value.substring(0, pastefield.selectionStart) +
            value +
            pastefield.value.substring(pastefield.selectionEnd, pastefield.value.length);
        pastefield.selectionStart = pastefield.selectionEnd = pastefield.selectionStart + value.length;
    } else {
        // Fallback? - just append
        pastefield.value += value;
    }

    if (pastefield.id === "NPFX" || pastefield.id === "GIVN" || pastefield.id === "SPFX" || pastefield.id === "SURN" || pastefield.id === "NSFX") {
        updatewholename();
    }
}

function ilinkitem(mediaid, type, ged) {
    ged = (typeof ged === "undefined") ? WT_GEDCOM : ged;
    window.open("inverselink.php?mediaid=" + encodeURIComponent(mediaid) + "&linkto=" + encodeURIComponent(type) + "&ged=" + encodeURIComponent(ged), "_blank", find_window_specs);
    return false;
}

function message(username, method, url) {
    window.open("message.php?to=" + encodeURIComponent(username) + "&method=" + encodeURIComponent(method) + "&url=" + encodeURIComponent(url), "_blank", mesg_window_specs);
    return false;
}

/**
 * Persistant checkbox options to hide/show extra data.

 * @param checkbox_id
 * @param data_selector
 */
function persistent_toggle(checkbox_id, data_selector) {
    var checkbox = document.getElementById(checkbox_id);
    var elements = document.querySelectorAll(data_selector);
    var display  = localStorage.getItem(checkbox_id);

    if (!checkbox) {
        return;
    }

    if (display !== "") {
        display = "none";
    }

    checkbox.checked = (display === "");
    for (var i = 0; i < elements.length; ++i) {
        elements[i].style.display = display;
    }

    checkbox.addEventListener("click", function () {
        console.log(display);
        display = (display === "" ? "none" : "");
        localStorage.setItem(checkbox_id, display);
        for (var i = 0; i < elements.length; ++i) {
            elements[i].style.display = display;
        }
    });
}

function valid_lati_long(field, pos, neg) {
    // valid LATI or LONG according to Gedcom standard
    // pos (+) : N or E
    // neg (-) : S or W
    var txt = field.value.toUpperCase();
    txt = txt.replace(/(^\s*)|(\s*$)/g, ''); // trim
    txt = txt.replace(/ /g, ':'); // N12 34 ==> N12.34
    txt = txt.replace(/\+/g, ''); // +17.1234 ==> 17.1234
    txt = txt.replace(/-/g, neg); // -0.5698 ==> W0.5698
    txt = txt.replace(/,/g, '.'); // 0,5698 ==> 0.5698
    // 0°34'11 ==> 0:34:11
    txt = txt.replace(/\u00b0/g, ':'); // °
    txt = txt.replace(/\u0027/g, ':'); // '
    // 0:34:11.2W ==> W0.5698
    txt = txt.replace(/^([0-9]+):([0-9]+):([0-9.]+)(.*)/g, function ($0, $1, $2, $3, $4) {
        var n = parseFloat($1);
        n += ($2 / 60);
        n += ($3 / 3600);
        n = Math.round(n * 1E4) / 1E4;
        return $4 + n;
    });
    // 0:34W ==> W0.5667
    txt = txt.replace(/^([0-9]+):([0-9]+)(.*)/g, function ($0, $1, $2, $3) {
        var n = parseFloat($1);
        n += ($2 / 60);
        n = Math.round(n * 1E4) / 1E4;
        return $3 + n;
    });
    // 0.5698W ==> W0.5698
    txt = txt.replace(/(.*)([N|S|E|W]+)$/g, '$2$1');
    // 17.1234 ==> N17.1234
    if (txt && txt.charAt(0) !== neg && txt.charAt(0) !== pos) {
        txt = pos + txt;
    }
    field.value = txt;
}

// This is the default way for webtrees to show image galleries.
// Custom themes may use a different viewer.
function activate_colorbox(config) {
    jQuery.extend(jQuery.colorbox.settings, {
        // Don't scroll window with document
        fixed:          true,
        // Simple I18N - the text will need to come from PHP
        current:        '',
        previous:       textDirection === 'rtl' ? '\u25b6' : '\u25c0', // ▶ ◀
        next:           textDirection === 'rtl' ? '\u25c0' : '\u25b6', // ◀ ▶
        slideshowStart: '\u25cb', // ○
        slideshowStop:  '\u25cf', // ●
        close:          '\u2715'  // ×
    });
    if (config) {
        jQuery.extend(jQuery.colorbox.settings, config);
    }

    // Trigger an event when we click on an (any) image
    jQuery('body').on('click', 'a.gallery', function () {
        // Remove colorbox from hidden media (e.g. on other tabs)
        // (not needed unless we add :visible to our selectors - which may not
        // work on all browsers?)
        //jQuery.colorbox.remove();

        // Enable colorbox for images
        jQuery('a[type^=image].gallery').colorbox({
            photo:         true,
            maxWidth:      '95%',
            maxHeight:     '95%',
            rel:           'gallery', // Turn all images on the page into a slideshow
            slideshow:     true,
            slideshowAuto: false,
            // Add wheelzoom to the displayed image
            onComplete:    function () {
                jQuery('.cboxPhoto').wheelzoom();
            }
        });

        // Enable colorbox for audio using <audio></audio>, where supported
        //jQuery('html.video a[type^=video].gallery').colorbox({
        //  rel:         'nofollow' // Slideshows are just for images
        //});

        // Enable colorbox for video using <video></video>, where supported
        //jQuery('html.audio a[type^=audio].gallery').colorbox({
        //  rel:         'nofollow', // Slideshows are just for images
        //});

        // Allow all other media types remain as download links
    });
}

// Initialize autocomplete elements.
function autocomplete(selector) {
    if (typeof(selector) === "undefined") {
        selector = "input[data-autocomplete-type]";
    }

    jQuery(selector).each(function () {
        var type = jQuery(this).data("autocomplete-type"); // What type of field
        var ged = jQuery(this).data("autocomplete-ged"); // Which family tree
        if (typeof(type) === "undefined") {
            alert("Missing data-autocomplete-type attribute");
        }

        // Default to the current tree
        if (typeof(ged) === "undefined") {
            jQuery(this).data("autocomplete-ged", WT_GEDCOM);
        }

        var self = jQuery(this);
        self.autocomplete({
            // Cannot use a simple URL, as the data-autocomplete-xxxx parameters may change.
            source: function (request, response) {
                // Some autocomplete fields require the current value of an earlier field
                var extra = null;
                if (self.data("autocomplete-extra")) {
                    extra = jQuery(self.data("autocomplete-extra")).val();
                }

                jQuery.getJSON("autocomplete.php", {
                    field: self.data("autocomplete-type"),
                    ged:   self.data("autocomplete-ged"),
                    extra: extra,
                    term:  request.term
                }, response);
            },
            html:   true
        });
    });
}

// Add LTR/RTL support for jQueryUI Accordions
jQuery.extend($.ui.accordion.prototype.options, {
    icons: {
        header:       textDirection === "rtl" ? "ui-icon-triangle-1-w" : "ui-icon-triangle-1-e",
        activeHeader: "ui-icon-triangle-1-s"
    }
});

jQuery.widget("ui.dialog", jQuery.ui.dialog, {
    /*! jQuery UI - v1.10.2 - 2013-12-12
     *  http://bugs.jqueryui.com/ticket/9087#comment:27 - bugfix
     *  http://bugs.jqueryui.com/ticket/4727#comment:23 - bugfix
     *  allowInteraction fix to accommodate windowed editors
     */
    _allowInteraction: function (event) {
        if (this._super(event)) {
            return true;
        }

        // address interaction issues with general iframes with the dialog
        if (event.target.ownerDocument !== this.document[0]) {
            return true;
        }

        // address interaction issues with dialog window
        if (jQuery(event.target).closest(".cke_dialog").length) {
            return true;
        }

        // address interaction issues with iframe based drop downs in IE
        if (jQuery(event.target).closest(".cke").length) {
            return true;
        }
    },
    /*! jQuery UI - v1.10.2 - 2013-10-28
     *  http://dev.ckeditor.com/ticket/10269 - bugfix
     *  moveToTop fix to accommodate windowed editors
     */
    _moveToTop:        function (event, silent) {
        if (!event || !this.options.modal) {
            this._super(event, silent);
        }
    }
});

/* Show / Hide event data for boxes used on charts and elsewhere */
jQuery('body').on ('click', '.iconz', function (e) {
    "use strict";
    e.stopPropagation ();

    var wrapper = jQuery(this).closest (".person_box_template"),
        inout = wrapper.find (".inout"),
        inout2 = wrapper.find (".inout2"),
        namedef = wrapper.find (".namedef"),
        basestyle = wrapper.attr ("class").match (/(box-style[0-2])/)[1];

    function showDetails() {
        wrapper.parent().css("z-index", 100);
        toggleExpanded();
        namedef.addClass ("nameZoom");
        inout2.hide (0, function () {
            inout.slideDown ();
        });
    }

    function hideDetails() {
        inout.slideUp (function () {
            inout2.show (0);
            namedef.removeClass ("nameZoom");
            toggleExpanded();
            wrapper.parent().css("z-index", '');
        });
    }

    function toggleExpanded() {
        wrapper.toggleClass(function () {
            return basestyle + " " + basestyle + "-expanded";
        });
    }

    if (!inout.text().length) {
        wrapper.css("cursor", "progress");
        inout.load ("expand_view.php?pid=" + wrapper.data("pid"), function () {
            wrapper.css("cursor", "");
            showDetails();
        });
    } else {
        if (wrapper.hasClass(basestyle)) {
            showDetails();
        } else {
            hideDetails();
        }
    }
    wrapper.find ('.iconz').toggleClass ("icon-zoomin icon-zoomout");
});

// Activate the langauge selection menu.
jQuery(".menu-language").on("click", "li a", function () {
    jQuery.post("action.php", {
        action:   "language",
        language: $(this).data("language"),
        csrf:     WT_CSRF_TOKEN
    }, function () {
        location.reload();
    });
});

// Activate the theme selection menu.
jQuery(".menu-theme").on("click", "li a", function () {
    jQuery.post("action.php", {
        action: "theme",
        theme:  $(this).data("theme"),
        csrf:   WT_CSRF_TOKEN
    }, function () {
        location.reload();
    });
});

// Locale-aware functions for sorting user-data.
function textCompareAsc(x, y) {
    return x.localeCompare(y, WT_LOCALE, {'sensitivity': 'base'});
}
function textCompareDesc(x, y) {
    return y.localeCompare(x, WT_LOCALE, {'sensitivity': 'base'});
}
