/*
* jQuery UI Autocomplete HTML Extension
*
* Copyright 2010, Scott González (http://scottgonzalez.com)
* Dual licensed under the MIT or GPL Version 2 licenses.
*
* http://github.com/scottgonzalez/jquery-ui-extensions
*/
(function( $ ) {

var proto = $.ui.autocomplete.prototype,
initSource = proto._initSource;

function filter( array, term ) {
var matcher = new RegExp( $.ui.autocomplete.escapeRegex(term), "i" );
return $.grep( array, function(value) {
return matcher.test( $( "<div>" ).html( value.label || value.value || value ).text() );
});
}

$.extend( proto, {
_initSource: function() {
if ( this.options.html && $.isArray(this.options.source) ) {
this.source = function( request, response ) {
response( filter( this.options.source, request.term ) );
};
} else {
initSource.call( this );
}
},

_renderItem: function( ul, item) {
return $( "<li></li>" )
.data( "item.autocomplete", item )
.append( $( "<a></a>" )[ this.options.html ? "html" : "text" ]( item.label ) )
.appendTo( ul );
}
});

})( jQuery );

/*
webtrees: Web based Family History software
Copyright (C) 2012 webtrees development team.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

$Id$
*/

// INDI
jQuery("#spouseid, input[id*=pid], input[id*=PID], input[id^=gedcomid], input[id^=rootid], input[id$=ROOT_ID], input[name^=FATHER], input[name^=MOTHER], input[name^=CHIL]").autocomplete({
	source: "autocomplete.php?field=INDI",
	html: true
});

// ASSO
jQuery(".ASSO").autocomplete({
	source: function(request, response) {jQuery.getJSON("autocomplete.php?field=ASSO", {term:request.term, pid:jQuery("input[name=pid]").val(), event_date:jQuery("input[id$=_DATE]").val()}, response);},
	html: true
});

// FAM
jQuery(".FAM, input[id*=famid], input[id*=FAMC], #famid").autocomplete({
	source: "autocomplete.php?field=FAM",
	html: true
});

// NOTE
jQuery(".NOTE").autocomplete({
	source: "autocomplete.php?field=NOTE",
	html: true
});

// SOUR
jQuery(".SOUR, input[id*=sid]").autocomplete({
	source: "autocomplete.php?field=SOUR"
});

// SOUR:PAGE
jQuery(".PAGE").autocomplete({
	source: function(request, response) {jQuery.getJSON("autocomplete.php?field=SOUR_PAGE", {term:request.term, sid:jQuery("input[class^=SOUR]").val()}, response);}
});

// SOUR:TITL
jQuery("#TITL").autocomplete({
	source: "autocomplete.php?field=SOUR_TITL"
});

// REPO
jQuery(".REPO, #REPO").autocomplete({
	source: "autocomplete.php?field=REPO"
});

// REPO:NAME
jQuery("#REPO_NAME").autocomplete({
	source: "autocomplete.php?field=REPO_NAME"
});

// OBJE
jQuery(".OBJE, #OBJE, #mediaid, #filter").autocomplete({
	source: "autocomplete.php?field=OBJE",
	html: true
});

// INDI or FAM or SOUR or REPO or NOTE or OBJE
jQuery("input[id$=xref], input[name^=gid], #cart_item_id").autocomplete({
	source: "autocomplete.php?field=IFSRO",
	html: true
});

// PLAC : with hierarchy
jQuery(".PLAC, #place, input[name=place], input[id=place], input[name*=PLACS], input[name*=PLAC3], input[name^=PLAC], input[name$=PLAC]").autocomplete({
	source: "autocomplete.php?field=PLAC"
});

// PLAC : without hierarchy
jQuery("input[name=place2], input[id=birthplace], input[id=marrplace], input[id=deathplace], input[id=bdmplace]").autocomplete({
	source: "autocomplete.php?field=PLAC2"
});

// INDI:BURI:CEME
jQuery("input[id=BURI_CEME]").autocomplete({
	source: "autocomplete.php?field=CEME"
});

// GIVN
jQuery("#GIVN, input[name*=GIVN], input[name*=firstname]").autocomplete({
	source: "autocomplete.php?field=GIVN"
});

// SURN
jQuery("#SURN, input[name*=SURN], input[name*=lastname], #NAME, input[id=name]").autocomplete({
	source: "autocomplete.php?field=SURN"
});

// SPFX
jQuery("#SPFX, input[name*=SPFX]").autocomplete({
	source: "autocomplete.php?field=SPFX"
});

// NPFX
jQuery("#NPFX, input[name*=NPFX]").autocomplete({
	source: "autocomplete.php?field=NPFX"
});

// NSFX
jQuery("#NSFX, input[name*=NSFX]").autocomplete({
	source: "autocomplete.php?field=NSFX"
});
