/**
 * Common strings functions
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2003  John Finlay and Others
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Display
 * @version $Id$
 */
	function trim(str) {
		return str.replace(/(^\s*)|(\s*$)/g,'');
	}
	function strclean(s) {
		if (s=='') return s;
		// Latin-1 Supplement
		// See http://www.unicode.org/charts/PDF/U0080.pdf
		s=s.replace(/[\u00C0-\u00C5]/g,'A');
		s=s.replace(/[\u00C6]/g,'AE');
		s=s.replace(/[\u00C7]/g,'C');
		s=s.replace(/[\u00C8-\u00CB]/g,'E');
		s=s.replace(/[\u00CC-\u00CF]/g,'I');
		s=s.replace(/[\u00D0\u00DE]/g,'TH');
		s=s.replace(/[\u00D1]/g,'N');
		s=s.replace(/[\u00D2-\u00D6]/g,'O');
		s=s.replace(/[\u00D8]/g,'O');
		s=s.replace(/[\u00D9-\u00DC]/g,'U');
		s=s.replace(/[\u00DD]/g,'Y');
		s=s.replace(/[\u00DF]/g,'ss');
		s=s.replace(/[\u00E0-\u00E5]/g,'a');
		s=s.replace(/[\u00E6-\u00E6]/g,'ae');
		s=s.replace(/[\u00E7]/g,'c');
		s=s.replace(/[\u00E8-\u00EB]/g,'e');
		s=s.replace(/[\u00EC-\u00EF]/g,'i');
		s=s.replace(/[\u00F0\u00FE]/g,'th');
		s=s.replace(/[\u00F1]/g,'n');
		s=s.replace(/[\u00F2-\u00F6]/g,'o');
		s=s.replace(/[\u00F8]/g,'o');
		s=s.replace(/[\u00F9-\u00FC]/g,'u');
		s=s.replace(/[\u00FD\u00FF]/g,'y');
		// Latin Extended-A
		// See http://www.unicode.org/charts/PDF/U0100.pdf  
		s=s.replace(/[\u0100\u0102\u0104]/g,'A');
		s=s.replace(/[\u0101\u0103\u0105]/g,'a');
		s=s.replace(/[\u0106\u0108\u010A\u010C]/g,'C');
		s=s.replace(/[\u0107\u0109\u010B\u010D]/g,'c');
		s=s.replace(/[\u010E\u0110]/g,'D');
		s=s.replace(/[\u010F\u0111]/g,'d');
		s=s.replace(/[\u0112\u0114\u0116\u0118\u011A]/g,'E');
		s=s.replace(/[\u0113\u0115\u0117\u0119\u011B]/g,'e');
		s=s.replace(/[\u011C\u011E\u0120\u0122]/g,'G');
		s=s.replace(/[\u011D\u011F\u0121\u0123]/g,'g');
		s=s.replace(/[\u0124\u0126]/g,'H');
		s=s.replace(/[\u0125\u0127]/g,'h');
		s=s.replace(/[\u0128\u012A\u012C\u012E\u0130]/g,'I');
		s=s.replace(/[\u0129\u012B\u012D\u012F\u0131]/g,'i');
		s=s.replace(/[\u0132]/g,'IJ');
		s=s.replace(/[\u0133]/g,'ij');
		s=s.replace(/[\u0134]/g,'J');
		s=s.replace(/[\u0135]/g,'j');
		s=s.replace(/[\u0136]/g,'K');
		s=s.replace(/[\u0137\u0138]/g,'k');
		s=s.replace(/[\u0139\u013B\u013D\u013F\u0141]/g,'L');
		s=s.replace(/[\u013A\u013C\u013E\u0140\u0142]/g,'l');
		s=s.replace(/[\u0143\u0145\u0147\u014A]/g,'N');
		s=s.replace(/[\u0144\u0146\u0148\u0149\u014B]/g,'n');
		s=s.replace(/[\u014C\u014E\u0150]/g,'O');
		s=s.replace(/[\u014D\u014F\u0151]/g,'o');
		s=s.replace(/[\u0152]/g,'OE');
		s=s.replace(/[\u0153]/g,'oe');
		s=s.replace(/[\u0154\u0156\u0158]/g,'R');
		s=s.replace(/[\u0155\u0157\u0159]/g,'r');
		s=s.replace(/[\u015A\u015C\u015E\u0160]/g,'S');
		s=s.replace(/[\u015B\u015D\u015F\u0161]/g,'s');
		s=s.replace(/[\u0162\u0164\u0166]/g,'T');
		s=s.replace(/[\u0163\u0165\u0167]/g,'t');
		s=s.replace(/[\u0168\u016A\u016C\u016E\u0170\u0172]/g,'U');
		s=s.replace(/[\u0169\u016B\u016D\u016F\u0171\u0173]/g,'u');
		s=s.replace(/[\u0174]/g,'W');
		s=s.replace(/[\u0175]/g,'w');
		s=s.replace(/[\u0176\u0178]/g,'Y');
		s=s.replace(/[\u0177]/g,'y');
		s=s.replace(/[\u0179\u017B\u017D]/g,'Z');
		s=s.replace(/[\u017A\u017C\u017E]/g,'z');
		s=s.replace(/[\u017F]/g,'s');
		s=s.replace(/[\s\']/g,'-');
		s=s.replace(/<[^>]+>/g,''); // remove html tags
		return s;
	}
