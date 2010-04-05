/**
 *
 * Copyright (C) 2004 by Dobrica Pavlinusic (www.rot13.org/~dpavlin)
 *
 * @licence GNU General Public License
 * @author Dobrica Pavlinusic
 * @see http://svn.rot13.org/index.cgi/js_locale/view/trunk/locale.js
 *
 * @package webtrees
 * @subpackage Display
 * @version $Id$
 */


// locale in which to sort (basically a alphabet in correct dictionary sort order)
if (typeof _lc_locale == "undefined") {
	var _lc_locale = '_0123456789aAáÁãÃâÂäÄ±¡bBcCçÇèÈæÆdDïÏğĞeEéÉìÌëËêÊfFgGhHiIíÍîÎjJkKlLåÅµ¥³£mMnNñÑòÒoOóÓôÔöÖõÕpPqQrRàÀøØsS¶¦ºªß¹©tT»«şŞuUúÚùÙüÜûÛvVwWxXyYıİzZ¼¬¿¯¾®';
}
// produce equivavlent of alphabet in native JavaScript sort order
var _lc_native = _lc_locale.split("").sort().join("");

function lc_debug(msg) {
	// comment out to disable debug
	return;
	document.write('<div style="color: gray; font-size: small;">'+msg+'</div>');
}

// create character remapping array
var _lc_l2n_arr = new Array();
var r = 0;
for (var i=0; i < _lc_locale.length; i++) {
	if (_lc_locale.charAt(i) != _lc_native.charAt(i)) {
		_lc_l2n_arr[_lc_locale.charAt(i)] = _lc_native.charAt(i);
		r++;
	}
}

lc_debug(
	"_lc_native:"+_lc_native+"<br>"+
	"_lc_locale:"+_lc_locale+"<br>"+
	"remapped "+r+" characters from table of "+_lc_locale.length+"/"+_lc_native.length+" locale/native characters<br>"
);

// comment out following line to disable caching of locale terms
var _lc_cache = new Array();

// convert string to correct sort order according to locale
function _lc(str) {
	if (_lc_cache && _lc_cache[str]) {
		return _lc_cache[str];
	} else {
		var out = '';
		for (var i=0; i <= str.length; i++) {
			var c = str.charAt(i);
			if (_lc_l2n_arr[c]) {
				out += _lc_l2n_arr[c];
			} else {
				out += c;
			}
		}
		if (_lc_cache) _lc_cache[str] = out;
		return out;
	}
}

// sort function with locale support
function _lc_sort(a,b) {
	// Checking for @N.N. and @P.N. would be better, but slower.
	if (a[0]=='@' && b[0]!='@') {
		return 1;
	}
	if (a[0]!='@' && b[0]=='@') {
		return -1;
	}

	var a_l = _lc(a);
	var b_l = _lc(b);

	//lc_debug(a+' '+( a_l < b_l ? '<' : ( a_l == b_l ? '==' : '>' ) )+' '+b+' [ '+a_l+' '+b_l+' ]');

	if (a_l < b_l) {
		return -1;
	} else if (a_l == b_l) {
		return 0;
	} else {
		return 1;
	}
}
