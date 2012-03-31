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
	minLength: 2,
	html: true
});

// INDI ASSOciate
$(".ASSO").autocomplete({
	// Is this the right way to add the option parameters?
	source: "autocomplete.php?field=INDI&option="+($("input[name=pid]").val())+"|"+($("input[id$=_DATE]").val()),
	minLength: 2,
	html: true
});;

// FAM
$(".FAM, input[id*=famid], input[id*=FAMC], #famid").autocomplete({
	source: "autocomplete.php?field=FAM",
	minLength: 2,
	html: true
});

// NOTE
$(".NOTE").autocomplete({
	source: "autocomplete.php?field=NOTE",
	minLength: 2
});

// SOUR
$(".SOUR, input[id*=sid]").autocomplete({
	source: "autocomplete.php?field=SOUR",
	minLength: 2
});

// SOUR:TITL
$("#TITL").autocomplete({
	source: "autocomplete.php?field=SOUR_TITL",
	minLength: 2
});

// REPO
$(".REPO, #REPO").autocomplete({
	source: "autocomplete.php?field=REPO",
	minLength: 2
});

// OBJE
$(".OBJE, #OBJE, #mediaid, #filter").autocomplete({
	source: "autocomplete.php?field=OBJE",
	minLength: 2,
	html: true
});

// INDI or FAM or SOUR or REPO or NOTE or OBJE
$("input[id$=xref], input[name^=gid], #cart_item_id").autocomplete({
	source: "autocomplete.php?field=IFSRO",
	minLength: 2,
	html: true
});

// PLAC : full [City, County, State/Province, Country]
$(".PLAC, #place, input[id=place], input[name*=PLACS], input[name*=PLAC3], input[name^=PLAC], input[name$=PLAC]").autocomplete({
	source: "autocomplete.php?field=PLAC",
	minLength: 2
});

// PLAC : splitted (mainly for search.php)
$("input[name=place], input[id=birthplace], input[id=marrplace], input[id=deathplace]").autocomplete({
	source: "autocomplete.php?field=PLAC&option=split",
	minLength: 2
});

// INDI:BURI:CEME
$("input[id^=CEME]").autocomplete({
	source: "autocomplete.php?field=INDI_BURI_CEME",
	minLength: 2
});

// GIVN
$("#GIVN, input[name*=GIVN], input[name*=firstname]").autocomplete({
	source: "autocomplete.php?field=GIVN",
	minLength: 2
});

// SURN
$("#SURN, input[name*=SURN], input[name*=lastname]").autocomplete({
	source: "autocomplete.php?field=SURN",
	minLength: 2
});
