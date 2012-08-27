<?php
// Census Assistant Control module for webtrees
//
// Census information about an individual
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

?>

	<script>
	function addDate(theCensDate) {
		var ddate = theCensDate.split(', ');
		document.getElementById('setctry').value = ddate[3];
		document.getElementById('setyear').value = ddate[0];
		cal_setDateField('<?php echo $element_id; ?>', parseInt(ddate[0]), parseInt(ddate[1]), parseInt(ddate[2])); return false;
	}
	function pasteAsstDate(setcy, setyr) {
		document.getElementById(setcy+setyr).selected = true;
		addDate(document.getElementById('selcensdate').options[document.getElementById('selcensdate').selectedIndex].value);
	}
	</script>

	<select id="selcensdate" name="selcensdate" onchange = "if (this.options[this.selectedIndex].value!='') {
							addDate(this.options[this.selectedIndex].value);
						}">
		<option id="defdate" name="defdate" value='' SELECTED><?php echo WT_I18N::translate('Census date'); ?></option>
		<option value=""></option>
		<option id="UK1911" name="UK1911" class="UK"  value='1911, 3, 02, UK'>UK 1911</option>
		<option id="UK1901" name="UK1901" class="UK"  value="1901, 2, 31, UK">UK 1901</option>
		<option id="UK1891" name="UK1891" class="UK"  value="1891, 3, 05, UK">UK 1891</option>
		<option id="UK1881" name="UK1881" class="UK"  value="1881, 3, 03, UK">UK 1881</option>
		<option id="UK1871" name="UK1871" class="UK"  value="1871, 3, 02, UK">UK 1871</option>
		<option id="UK1861" name="UK1861" class="UK"  value="1861, 3, 07, UK">UK 1861</option>
		<option id="UK1851" name="UK1851" class="UK"  value="1851, 2, 30, UK">UK 1851</option>
		<option id="UK1841" name="UK1841" class="UK"  value="1841, 5, 06, UK">UK 1841</option>
		<option value=""></option>
		<option id="USA1940" name="USA1940" class="USA" value="1940, 3, 01, USA">US 1940</option>
		<option id="USA1930" name="USA1930" class="USA" value="1930, 3, 01, USA">US 1930</option>
		<option id="USA1920" name="USA1920" class="USA" value="1920, 0, 01, USA">US 1920</option>
		<option id="USA1910" name="USA1910" class="USA" value="1910, 3, 15, USA">US 1910</option>
		<option id="USA1900" name="USA1900" class="USA" value="1900, 5, 01, USA">US 1900</option>
		<option id="USA1890" name="USA1890" class="USA" value="1890, 5, 01, USA">US 1890</option>
		<option id="USA1880" name="USA1880" class="USA" value="1880, 5, 01, USA">US 1880</option>
		<option id="USA1870" name="USA1870" class="USA" value="1870, 5, 01, USA">US 1870</option>
		<option id="USA1860" name="USA1860" class="USA" value="1860, 5, 01, USA">US 1860</option>
		<option id="USA1850" name="USA1850" class="USA" value="1850, 5, 01, USA">US 1850</option>
		<option id="USA1840" name="USA1840" class="USA" value="1840, 5, 01, USA">US 1840</option>
		<option id="USA1830" name="USA1830" class="USA" value="1830, 5, 01, USA">US 1830</option>
		<option id="USA1820" name="USA1820" class="USA" value="1820, 7, 07, USA">US 1820</option>
		<option id="USA1810" name="USA1810" class="USA" value="1810, 7, 06, USA">US 1810</option>
		<option id="USA1800" name="USA1800" class="USA" value="1800, 7, 04, USA">US 1800</option>
		<option id="USA1790" name="USA1790" class="USA" value="1790, 7, 02, USA">US 1790</option>
		<option value=""></option>
		<option id="FR1951" name="FR1951" class="FR" value="1951, 0, 01, FR">FR 1951</option>
		<option id="FR1946" name="FR1946" class="FR" value="1946, 0, 01, FR">FR 1946</option>
		<option id="FR1941" name="FR1941" class="FR" value="1941, 0, 01, FR">FR 1941</option>
		<option id="FR1936" name="FR1936" class="FR" value="1936, 0, 01, FR">FR 1936</option>>
		<option id="FR1931" name="FR1931" class="FR" value="1931, 0, 01, FR">FR 1931</option>
		<option id="FR1926" name="FR1926" class="FR" value="1926, 0, 01, FR">FR 1926</option>
		<option id="FR1921" name="FR1921" class="FR" value="1921, 0, 01, FR">FR 1921</option>
		<option id="FR1916" name="FR1916" class="FR" value="1916, 0, 01, FR">FR 1916</option>
		<option id="FR1911" name="FR1911" class="FR" value="1911, 0, 01, FR">FR 1911</option>
		<option id="FR1906" name="FR1906" class="FR" value="1906, 0, 01, FR">FR 1906</option>
		<option id="FR1901" name="FR1901" class="FR" value="1901, 0, 01, FR">FR 1901</option>
		<option id="FR1896" name="FR1896" class="FR" value="1896, 0, 01, FR">FR 1896</option>
		<option id="FR1891" name="FR1891" class="FR" value="1891, 0, 01, FR">FR 1891</option>
		<option id="FR1886" name="FR1886" class="FR" value="1886, 0, 01, FR">FR 1886</option>
		<option id="FR1881" name="FR1881" class="FR" value="1881, 0, 01, FR">FR 1881</option>
		<option id="FR1876" name="FR1876" class="FR" value="1876, 0, 01, FR">FR 1876</option>
		<option value=""></option>
	</select>

	<input type="hidden" id="setctry" name="setctry" value="">
	<input type="hidden" id="setyear" name="setyear" value="">
