<?php
// Census Assistant Control module for webtrees
//
// Census and Souce Input Area File File
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2007 to 2010 PGV Development Team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

?>

<script>
	function getCenDate(cenyear) {
		// Calculate census date from the census year selected
		// UK CENSUS DATES
		if        (cenyear == 1841) { var cendate = new Date(1841,  5,  6);  // 06 JUN 1841
		} else if (cenyear == 1851) { var cendate = new Date(1851,  2, 30);  // 30 MAR 1851
		} else if (cenyear == 1861) { var cendate = new Date(1861,  3,  7);  // 07 APR 1861
		} else if (cenyear == 1871) { var cendate = new Date(1871,  3,  2);  // 02 APR 1871
		} else if (cenyear == 1881) { var cendate = new Date(1881,  3,  3);  // 03 APR 1881
		} else if (cenyear == 1891) { var cendate = new Date(1891,  3,  5);  // 05 APR 1891
		} else if (cenyear == 1901) { var cendate = new Date(1901,  2, 31);  // 31 MAR 1901
		} else if (cenyear == 1911) { var cendate = new Date(1911,  3,  2);  // 02 APR 1911
		} else if (cenyear == 1921) { var cendate = new Date(1921,  3,  2);  // 02 APR 1921   // For Test Purposes
		} else if (cenyear == 1931) { var cendate = new Date(1931,  3,  2);  // 02 APR 1931   // For Test Purposes
		// USA CENSUS DATES
		} else if (cenyear == 1790) { var cendate = new Date(1790,  7,  2);  // 02 AUG 1790
		} else if (cenyear == 1800) { var cendate = new Date(1800,  7,  4);  // 04 AUG 1800
		} else if (cenyear == 1810) { var cendate = new Date(1810,  7,  6);  // 06 AUG 1810
		} else if (cenyear == 1820) { var cendate = new Date(1820,  7,  7);  // 07 AUG 1820
		} else if (cenyear == 1830) { var cendate = new Date(1830,  5,  1);  // 01 JUN 1830
		} else if (cenyear == 1840) { var cendate = new Date(1840,  5,  1);  // 01 JUN 1840
		} else if (cenyear == 1850) { var cendate = new Date(1850,  5,  1);  // 01 JUN 1850
		} else if (cenyear == 1860) { var cendate = new Date(1860,  5,  1);  // 01 JUN 1860
		} else if (cenyear == 1870) { var cendate = new Date(1870,  5,  1);  // 01 JUN 1870
		} else if (cenyear == 1880) { var cendate = new Date(1880,  5,  1);  // 01 JUN 1880
		} else if (cenyear == 1890) { var cendate = new Date(1890,  5,  1);  // 01 JUN 1890
		} else if (cenyear == 1900) { var cendate = new Date(1900,  5,  1);  // 01 JUN 1900
		} else if (cenyear == 1910) { var cendate = new Date(1910,  3, 15);  // 15 APR 1910
		} else if (cenyear == 1920) { var cendate = new Date(1920,  1,  1);  // 01 JAN 1920
		} else if (cenyear == 1930) { var cendate = new Date(1930,  3,  1);  // 01 APR 1930
		} else if (cenyear == 1940) { var cendate = new Date(1940,  3,  1);  // 01 APR 1940
		// FR CENSUS DATES
		} else if (cenyear == 1876) { var cendate = new Date(1876, 31, 12);  // 02 AUG 1790
		} else if (cenyear == 1881) { var cendate = new Date(1881, 31, 12);  // 04 AUG 1800
		} else if (cenyear == 1886) { var cendate = new Date(1886, 31, 12);  // 06 AUG 1810
		} else if (cenyear == 1891) { var cendate = new Date(1891, 31, 12);  // 07 AUG 1820
		} else if (cenyear == 1896) { var cendate = new Date(1896, 31, 12);  // 01 JUN 1830
		} else if (cenyear == 1901) { var cendate = new Date(1901, 31, 12);  // 01 JUN 1840
		} else if (cenyear == 1906) { var cendate = new Date(1906, 31, 12);  // 01 JUN 1850
		} else if (cenyear == 1911) { var cendate = new Date(1911, 31, 12);  // 01 JUN 1860
		} else if (cenyear == 1916) { var cendate = new Date(1916, 31, 12);  // 01 JUN 1870
		} else if (cenyear == 1921) { var cendate = new Date(1921, 31, 12);  // 01 JUN 1880
		} else if (cenyear == 1926) { var cendate = new Date(1926, 31, 12);  // 01 JUN 1890
		} else if (cenyear == 1931) { var cendate = new Date(1931, 31, 12);  // 01 JUN 1900
		} else if (cenyear == 1936) { var cendate = new Date(1936, 31, 12);  // 15 APR 1910
		} else if (cenyear == 1941) { var cendate = new Date(1941, 31, 12);  // 01 JAN 1920
		} else if (cenyear == 1946) { var cendate = new Date(1946, 31, 12);  // 01 APR 1930
		} else if (cenyear == 1951) { var cendate = new Date(1951, 31, 12);  // 01 APR 1940

		// Default Date
		} else {
			var cendate = new Date(1901, 2, 31);
		}
		return cendate;
	}

	function changeCtry() {
		// Change Year field Colour ----------------------------------------------
		if (document.getElementById('censYear').value=="") {
			document.getElementById('censYear').style.backgroundColor = "#ffaaaa";
		} else {
			document.getElementById('censYear').style.backgroundColor = "#ffffff";
		}
	}

	function changeYear(cenyear) {

		var cenctry = document.getElementById('censCtry').value;
		var tbl = document.getElementById('tblSample');
		if (tbl.rows.length === 0) {
			create_header();
		}
		// Change Date field -----------------------------------------------------
		var cendate = getCenDate(cenyear);
		document.getElementById('censDate').value = cendate.format("dd NNN yyyy");
		// Change Year field Colour ----------------------------------------------
		if (document.getElementById('censYear').value=="") {
			document.getElementById('censYear').style.backgroundColor = "#ffaaaa";
		} else {
			document.getElementById('censYear').style.backgroundColor = "#ffffff";
		}
		changeAge(cenyear);
		changeCols(cenyear);
		changeMC(cenyear);
		changeChBorn(cenyear);
		preview();

		// Toggle Countries when the preset UK Cens Date has been been used -------------
		if (TheCenCtry=="UK") {
			if (cenyear!=TheCenYear && cenctry!=TheCenCtry) {
				censyear = new DynamicOptionList();
				censyear.addDependentFields("censCtry","censYear");
				censyear.forValue("UK").addOptions("", "1841", "1851", "1861", "1871", "1881", "1891", "1901", "1911", "1921", "1931");
				censyear.forValue("USA").addOptions("", "1790", "1800", "1810", "1820", "1830", "1840", "1850", "1860", "1870", "1880", "1890", "1900", "1910", "1920", "1930", "1940", cenyear);
				censyear.forValue("FR").addOptions( "", "1876", "1881", "1886", "1891", "1896", "1901", "1906", "1911", "1914", "1921", "1926","1931", "1936", "1941", "1946", "1951");
				censyear.forValue("FR").setDefaultOptions("");
				censyear.forValue("UK").setDefaultOptions("");
				censyear.forValue("USA").setDefaultOptions(cenyear);
				initDynamicOptionLists();
				TheCenYear='';
			}
			if (TheCenYear=='' && cenctry==TheCenCtry) {
				censyear = new DynamicOptionList();
				censyear.addDependentFields("censCtry","censYear");
				censyear.forValue("UK").addOptions("", "1841", "1851", "1861", "1871", "1881", "1891", "1901", "1911", "1921", "1931", cenyear);
				censyear.forValue("USA").addOptions("", "1790", "1800", "1810", "1820", "1830", "1840", "1850", "1860", "1870", "1880", "1890", "1900", "1910", "1920", "1930", "1940");
				censyear.forValue("FR").addOptions( "", "1876", "1881", "1886", "1891", "1896", "1901", "1906", "1911", "1914", "1921", "1926","1931", "1936", "1941", "1946", "1951");
				censyear.forValue("FR").setDefaultOptions("");
				censyear.forValue("UK").setDefaultOptions(cenyear);
				censyear.forValue("USA").setDefaultOptions("");
				initDynamicOptionLists();
				TheCenYear='';
			}
			if (TheCenYear=='' && cenctry==TheCenCtry) {
				censyear = new DynamicOptionList();
				censyear.addDependentFields("censCtry","censYear");
				censyear.forValue("UK").addOptions("", "1841", "1851", "1861", "1871", "1881", "1891", "1901", "1911", "1921", "1931", cenyear);
				censyear.forValue("USA").addOptions("", "1790", "1800", "1810", "1820", "1830", "1840", "1850", "1860", "1870", "1880", "1890", "1900", "1910", "1920", "1930", "1940");
				censyear.forValue("FR").addOptions( "", "1876", "1881", "1886", "1891", "1896", "1901", "1906", "1911", "1914", "1921", "1926","1931", "1936", "1941", "1946", "1951");
				censyear.forValue("UK").setDefaultOptions("");
				censyear.forValue("FR").setDefaultOptions(cenyear);
				censyear.forValue("USA").setDefaultOptions("");
				initDynamicOptionLists();
				TheCenYear='';
			}
		}
		// Toggle Countries when the preset US Cens Date has been been used -------------
		if (TheCenCtry=="USA") {
			if (cenyear!=TheCenYear && cenctry!=TheCenCtry) {
				censyear = new DynamicOptionList();
				censyear.addDependentFields("censCtry","censYear");
				censyear.forValue("UK").addOptions("", "1841", "1851", "1861", "1871", "1881", "1891", "1901", "1911", "1921", "1931", cenyear);
				censyear.forValue("USA").addOptions("", "1790", "1800", "1810", "1820", "1830", "1840", "1850", "1860", "1870", "1880", "1890", "1900", "1910", "1920", "1930", "1940");
				censyear.forValue("FR").addOptions( "", "1876", "1881", "1886", "1891", "1896", "1901", "1906", "1911", "1914", "1921", "1926","1931", "1936", "1941", "1946", "1951");
				censyear.forValue("FR").setDefaultOptions("");
				censyear.forValue("UK").setDefaultOptions(cenyear);
				censyear.forValue("USA").setDefaultOptions("");
				initDynamicOptionLists();
				TheCenYear='';
			}
			if (TheCenYear=='' && cenctry==TheCenCtry) {
				censyear = new DynamicOptionList();
				censyear.addDependentFields("censCtry","censYear");
				censyear.forValue("UK").addOptions("", "1841", "1851", "1861", "1871", "1881", "1891", "1901", "1911", "1921", "1931");
				censyear.forValue("USA").addOptions("", "1790", "1800", "1810", "1820", "1830", "1840", "1850", "1860", "1870", "1880", "1890", "1900", "1910", "1920", "1930", "1940", cenyear);
				censyear.forValue("FR").addOptions( "", "1876", "1881", "1886", "1891", "1896", "1901", "1906", "1911", "1914", "1921", "1926","1931", "1936", "1941", "1946", "1951");
				censyear.forValue("FR").setDefaultOptions("");
				censyear.forValue("UK").setDefaultOptions("");
				censyear.forValue("USA").setDefaultOptions(cenyear);
				initDynamicOptionLists();
				TheCenYear='';
			}
			if (TheCenYear=='' && cenctry==TheCenCtry) {
				censyear = new DynamicOptionList();
				censyear.addDependentFields("censCtry","censYear");
				censyear.forValue("UK").addOptions("", "1841", "1851", "1861", "1871", "1881", "1891", "1901", "1911", "1921", "1931");
				censyear.forValue("USA").addOptions("", "1790", "1800", "1810", "1820", "1830", "1840", "1850", "1860", "1870", "1880", "1890", "1900", "1910", "1920", "1930", cenyear);
				censyear.forValue("FR").addOptions( "", "1876", "1881", "1886", "1891", "1896", "1901", "1906", "1911", "1914", "1921", "1926","1931", "1936", "1941", "1946", "1951");
				censyear.forValue("USA").setDefaultOptions("");
				censyear.forValue("UK").setDefaultOptions("");
				censyear.forValue("FR").setDefaultOptions(cenyear);
				initDynamicOptionLists();
				TheCenYear='';
			}
		}


	}

	// Change Marital Condition and Years Married based on Census Year ======================
	function changeMC(cenyear) {
		var cendate = getCenDate(cenyear);
		// Get Married Date from Input Fields and re-calculate Marital Condition ============
		var tbl = document.getElementById('tblSample');
		for (var i=1; i<tbl.rows.length; i++) { // start at i=1 because we need to avoid header
			var tr = tbl.rows[i];
			for (var j=2; j<tr.cells.length; j++) {
				if (j!=4 && j!=15) {
					// 4 and 15 are the marital condition columns (fields)
					// therefore miss out all cols except these marital condition cols
					continue;
				} else {
					var marrcond = (tr.cells[j].childNodes[0].value);
					var dob      = (tr.cells[11].childNodes[0].value); // DOB Birth date in Julian Format
					var yrsmarr  = (tr.cells[16].childNodes[0].value); // Years married
					var agemarr  = (tr.cells[20].childNodes[0].value); // Age at 1st marriage
					var dom      = (tr.cells[69].childNodes[0].value); // DOM Marriage date in Julian format
					var fullnam  = (tr.cells[70].childNodes[0].value); // Full Name
					var marrnam  = (tr.cells[71].childNodes[0].value); // Married Name
					var dod      = (tr.cells[72].childNodes[0].value); // DOD Death date in Julian format

					var one_day   = 1000*60*60*24;
					var one_month = (365.26*one_day)/12;
					var one_year  = 365.26*one_day;

					// Date of Birth (dob) - passed as Julian Date String
					if (dob>1721060) {
						IJD = Math.floor(dob);
						L = Math.floor(IJD + 68569);
						N = Math.floor(4 * L / 146097);
						L = L - Math.floor((146097*N + 3)/4);
						I = Math.floor(4000*(L + 1)/1461001);
						L = L - Math.floor(1461 * I / 4) + 31;
						J = Math.floor(80 * L / 2447);
						K = L - Math.floor(2447 * J / 80);
						L = Math.floor(J/11);
						J = J + 2 - 12*L;
						I = 100*(N - 49) + I + L;
						dob = (I+', '+J+', '+K);
					}
					// Create Date of Birth object from passed string dob
					var jsdob = Date.parseString(dob, 'y, M, d');

					// Date of Marriage (dom) - passed as Julian Date String
					if (dom>1721060) {
						IJD = Math.floor(dom);
						L = Math.floor(IJD + 68569);
						N = Math.floor(4 * L / 146097);
						L = L - Math.floor((146097*N + 3)/4);
						I = Math.floor(4000*(L + 1)/1461001);
						L = L - Math.floor(1461 * I / 4) + 31;
						J = Math.floor(80 * L / 2447);
						K = L - Math.floor(2447 * J / 80);
						L = Math.floor(J/11);
						J = J + 2 - 12*L;
						I = 100*(N - 49) + I + L;
						dom = (I+', '+J+', '+K);
					}
					// Create Date of Marriage object from passed string dom
					var jsdom = Date.parseString(dom, 'y, M, d');

					// Date of Death (dod) - passed as Julian Date String
					if (dod>1721060) {
						IJD = Math.floor(dod);
						L = Math.floor(IJD + 68569);
						N = Math.floor(4 * L / 146097);
						L = L - Math.floor((146097*N + 3)/4);
						I = Math.floor(4000*(L + 1)/1461001);
						L = L - Math.floor(1461 * I / 4) + 31;
						J = Math.floor(80 * L / 2447);
						K = L - Math.floor(2447 * J / 80);
						L = Math.floor(J/11);
						J = J + 2 - 12*L;
						I = 100*(N - 49) + I + L;
						dod = (I+', '+J+', '+K);
					}
					// Create Date of Marriage object from passed string dom
					var jsdod = Date.parseString(dod, 'y, M, d');

					if (cendate > jsdom) {
						yrsmarr = Math.floor((cendate-jsdom)/one_year);
						agemarr = Math.floor((jsdom-jsdob)/one_year);
						marrcond = "M";
						// if married in census year, use "<1" as "under 1" ---
						if (yrsmarr == 0) {
							yrsmarr = "<1";
						}
					} else {
						yrsmarr = "-";
						agemarr = "-";
						marrcond = "S";
					}
				}

				tr.cells[j].childNodes[0].value = marrcond;
				tr.cells[16].childNodes[0].value = yrsmarr;

				//-- If single and USA 1930 or 1940, set Years married to "-" ------------------
				if (marrcond=="S" && (cenyear=="1930" || cenyear=="1940")) {
					tr.cells[20].childNodes[0].value = "-";
				}
				//-- If married or widowed set name to married name --------------------
				if (marrcond=="M" || marrcond=="W") {
					tr.cells[2].childNodes[0].value = marrnam;
				}
				//-- If single or unmarried set name to birth (full) name --------------
				if (marrcond=="S" || marrcond=="U") {
					tr.cells[2].childNodes[0].value = fullnam;
				}

				var age = (tr.cells[7].childNodes[0].value);  // Age

			}
		}
	}

	function changeChBorn(cenyear) {
		var cendate = getCenDate(cenyear);
		// Get Children born Array from Input Fields and re-calculate Born Alive Died Condition ============
		var tbl = document.getElementById('tblSample');
		for (var i=1; i<tbl.rows.length; i++) { // start at i=1 because we need to avoid header
			var tr = tbl.rows[i];
			for (var j=2; j<tr.cells.length; j++) {
				if (j!=73) {
					// 73 is the Chil born array column
					// therefore miss out all cols except this column’s cells
					continue;
				} else {
					// Calculate Children Born, Living, Died --------------------------
					var chilBLD = (tr.cells[73].childNodes[0].value);
					chBLDarray = chilBLD.split('::');
					var cdat   = calculateJD(cendate);
					// Variables to be used ---------
					var ChilBorn = chBLDarray.length;
					//var ChilLivg = "-";
					//var ChilDied = "-";
					var chBLD = new Array();
					var x;
					var ALIVE=0;
					var DEAD=0;
					var NOTBORN=0;
					for (x = 0; x<ChilBorn; x++) {
						chBLD[x] = new Array();  // This declares each column in turn
						chBLDarray2 = chBLDarray[x].split(', ');
						var y;
						for (y = 0; y<chBLDarray2.length; y++) {
							chBLD[x][y] = chBLDarray2[y];
							if (y==2 && (cdat>(chBLD[x][1]) && chBLD[x][1]!=0) && (chBLD[x][2]==0 || cdat<(chBLD[x][2]))) {
								ALIVE=ALIVE+1;
							} else if (y==2 && cdat>(chBLD[x][2]) && (chBLD[x][2])!=0) {
								DEAD=DEAD+1;
							} else if (y==2) {
								NOTBORN=NOTBORN+1;
							}
						}
					}
					var BORN = ALIVE+DEAD;
					if (BORN == 0) {
						tr.cells[17].childNodes[0].value = "-"; // BORN
						tr.cells[18].childNodes[0].value = "-"; // ALIVE
						tr.cells[19].childNodes[0].value = "-"; // DEAD
					} else {
						tr.cells[17].childNodes[0].value = BORN; // BORN
						tr.cells[18].childNodes[0].value = ALIVE; // ALIVE
						tr.cells[19].childNodes[0].value = DEAD; // DEAD
					}
					if (ALIVE == 0) {
						tr.cells[18].childNodes[0].value = "-"; // ALIVE
					}
					if (DEAD == 0) {
						tr.cells[19].childNodes[0].value = "-"; // DEAD
					}
				}
			}
		}
	}

	// Change Age based on Census Year =====================================================
	function changeAge(cenyear) {
		var base1901 = "<?php echo $censyear; ?>";
		var prevyr = document.getElementById('prevYear');
		if (prevyr.value != "") {
			var prevcenyear=prevyr.value;
		}

		var cendate = getCenDate(cenyear);

		var one_day   = 1000*60*60*24;
		var one_month = (365.26*one_day)/12;
		var one_year  = 365.26*one_day;

		// Get Age from Input Fields and re-calculate =======================================
		var tbl = document.getElementById('tblSample');

		for (var i=1; i<tbl.rows.length; i++) { // start at i=1 because we need to avoid header
			var tr = tbl.rows[i];
			for (var j=2; j<tr.cells.length; j++) {
				if (j!=7 && j!=12) {
					// miss out all cols except age cols
					continue;
				} else if (tr.cells[j].childNodes[0].value=="") {
					tr.cells[j].childNodes[0].value=null
				} else {
					// Calculate Birth Year =======================================
					var bage  = (tr.cells[68].childNodes[0].value);
					// If valid Julian date used, then use this instead -----------
					if (bage>1721060) {
						var IJD = Math.floor(bage);
						var L = Math.floor(IJD + 68569);
						var N = Math.floor(4 * L / 146097);
						L = L - Math.floor((146097*N + 3)/4);
						var I = Math.floor(4000*(L + 1)/1461001);
						L = L - Math.floor(1461 * I / 4) + 31;
						var J = Math.floor(80 * L / 2447);
						var K = L - Math.floor(2447 * J / 80);
						L = Math.floor(J/11);
						J = J + 2 - 12*L;
						I = 100*(N - 49) + I + L;
						bage = (I+', '+J+', '+K);
					}
					// Calculate Death Year =======================================
					var dage = (tr.cells[72].childNodes[0].value);
					// If valid Julian date used, then use this instead -----------
					if (dage>1721060) {
						var IJD = Math.floor(dage);
						var L = Math.floor(IJD + 68569);
						var N = Math.floor(4 * L / 146097);
						L = L - Math.floor((146097*N + 3)/4);
						var I = Math.floor(4000*(L + 1)/1461001);
						L = L - Math.floor(1461 * I / 4) + 31;
						var J = Math.floor(80 * L / 2447);
						var K = L - Math.floor(2447 * J / 80);
						L = Math.floor(J/11);
						J = J + 2 - 12*L;
						I = 100*(N - 49) + I + L;
						dage = (I+', '+J+', '+K);
					}

					// Caculate Age (or if Dead) on the selected Census Date ===================
					var bage2 = Date.parseString(bage, 'y, M, d');
					var dage2 = Date.parseString(dage, 'y, M, d');

					if (cendate > dage2) {
						newage = "-";
					} else if (bage2 != "Invalid Date") {
						var newage = (cendate-bage2);
						if (Math.floor(newage/one_year) < 0) {
							newage = "-";
						} else if (Math.floor(newage/one_year) > 0) {
							newage = Math.floor(newage/one_year);
						} else if (Math.floor(newage/one_day) > 31) {
							newage = Math.floor(newage/one_month)+"m";
						} else if (Math.floor(newage/one_day) < 31) {
							newage = Math.floor(newage/one_day)+"d";
						} else if (Math.floor(newage/one_day) < 0)  {
							newage = "-";
						} else {
							newage = "nn";
						}
						if (newage == "nn") {
							newage = Math.floor(cendate-bage2/one_year);
						}
					} else {
						newage = "-";
					}
					tr.cells[j].childNodes[0].value=newage;

					// Highlight in Pink the Name field and Age fields for any person not born or who died before census date -----
					if (newage != "-") {
						tr.cells[2].childNodes[0].style.background  = '#ffffff';
						tr.cells[7].childNodes[0].style.background  = '#ffffff';
						tr.cells[12].childNodes[0].style.background = '#ffffff';
					} else {
						// alert(tr.cells[2].childNodes[0].value+" - Not Born Yet");
						tr.cells[2].childNodes[0].style.background  = '#ffaaaa';
						tr.cells[7].childNodes[0].style.background  = '#ffaaaa';
						tr.cells[12].childNodes[0].style.background = '#ffaaaa';
					}

				}
			}
		}
		var cens_ctry_a = document.getElementById('censCtry');
		var cens_ctry = cens_ctry_a.value;
		document.getElementById('Titl').value = '<?php echo WT_I18N::translate('Census transcript'),' - ', WT_Filter::escapeJs($wholename), ' - ', WT_I18N::translate('Household'); ?>';
		var prev = document.getElementById('prevYear');
		prev.value = cenyear;
	}

	// Add or Remove columns ===========================
	function changeCols(cenyear) {
		var cens_ctry = document.getElementById('censCtry').value;

		var cols_0 = document.getElementsByName('col_0');
		var cols_1 = document.getElementsByName('col_1');
		var cols_2 = document.getElementsByName('col_2');
		var cols_3 = document.getElementsByName('col_3');
		var cols_4 = document.getElementsByName('col_4');
		var cols_5 = document.getElementsByName('col_5');
		var cols_6 = document.getElementsByName('col_6');
		var cols_7 = document.getElementsByName('col_7');
		var cols_8 = document.getElementsByName('col_8');
		var cols_9 = document.getElementsByName('col_9');
		var cols_10 = document.getElementsByName('col_10');
		var cols_11 = document.getElementsByName('col_11');
		var cols_12 = document.getElementsByName('col_12');
		var cols_13 = document.getElementsByName('col_13');
		var cols_14 = document.getElementsByName('col_14');
		var cols_15 = document.getElementsByName('col_15');
		var cols_16 = document.getElementsByName('col_16');
		var cols_17 = document.getElementsByName('col_17');
		var cols_18 = document.getElementsByName('col_18');
		var cols_19 = document.getElementsByName('col_19');
		var cols_20 = document.getElementsByName('col_20');
		var cols_21 = document.getElementsByName('col_21');
		var cols_22 = document.getElementsByName('col_22');
		var cols_23 = document.getElementsByName('col_23');
		var cols_24 = document.getElementsByName('col_24');
		var cols_25 = document.getElementsByName('col_25');
		var cols_26 = document.getElementsByName('col_26');
		var cols_27 = document.getElementsByName('col_27');
		var cols_28 = document.getElementsByName('col_28');
		var cols_29 = document.getElementsByName('col_29');
		var cols_30 = document.getElementsByName('col_30');
		var cols_31 = document.getElementsByName('col_31');
		var cols_32 = document.getElementsByName('col_32');
		var cols_33 = document.getElementsByName('col_33');
		var cols_34 = document.getElementsByName('col_34');
		var cols_35 = document.getElementsByName('col_35');
		var cols_36 = document.getElementsByName('col_36');
		var cols_37 = document.getElementsByName('col_37');
		var cols_38 = document.getElementsByName('col_38');
		var cols_39 = document.getElementsByName('col_39');
		var cols_40 = document.getElementsByName('col_40');
		var cols_41 = document.getElementsByName('col_41');
		var cols_42 = document.getElementsByName('col_42');
		var cols_43 = document.getElementsByName('col_43');
		var cols_44 = document.getElementsByName('col_44');
		var cols_45 = document.getElementsByName('col_45');
		var cols_46 = document.getElementsByName('col_46');
		var cols_47 = document.getElementsByName('col_47');
		var cols_48 = document.getElementsByName('col_48');
		var cols_49 = document.getElementsByName('col_49');
		var cols_50 = document.getElementsByName('col_50');
		var cols_51 = document.getElementsByName('col_51');
		var cols_52 = document.getElementsByName('col_52');
		var cols_53 = document.getElementsByName('col_53');
		var cols_54 = document.getElementsByName('col_54');
		var cols_55 = document.getElementsByName('col_55');
		var cols_56 = document.getElementsByName('col_56');
		var cols_57 = document.getElementsByName('col_57');
		var cols_58 = document.getElementsByName('col_58');
		var cols_59 = document.getElementsByName('col_59');
		var cols_60 = document.getElementsByName('col_60');
		var cols_61 = document.getElementsByName('col_61');
		var cols_62 = document.getElementsByName('col_62');
		var cols_63 = document.getElementsByName('col_63');
		var cols_64 = document.getElementsByName('col_64');
		var cols_65 = document.getElementsByName('col_65');
		var cols_66 = document.getElementsByName('col_66');
		var cols_67 = document.getElementsByName('col_67');
		// var cols_68 = document.getElementsByName('col_68');


		var flip_3 = "none";
		var flip_4 = "none";
		var flip_5 = "none";
		var flip_6 = "none";
		var flip_7 = "none";
		var flip_8 = "none";
		var flip_9 = "none";
		var flip_10 = "none";
		var flip_11 = "none";
		var flip_12 = "none";
		var flip_13 = "none";
		var flip_14 = "none";
		var flip_15 = "none";
		var flip_16 = "none";
		var flip_17 = "none";
		var flip_18 = "none";
		var flip_19 = "none";
		var flip_20 = "none";
		var flip_21 = "none";
		var flip_22 = "none";
		var flip_23 = "none";
		var flip_24 = "none";
		var flip_25 = "none";
		var flip_26 = "none";
		var flip_27 = "none";
		var flip_28 = "none";
		var flip_29 = "none";
		var flip_30 = "none";
		var flip_31 = "none";
		var flip_32 = "none";
		var flip_33 = "none";
		var flip_34 = "none";
		var flip_35 = "none";
		var flip_36 = "none";
		var flip_37 = "none";
		var flip_38 = "none";
		var flip_39 = "none";
		var flip_40 = "none";
		var flip_41 = "none";
		var flip_42 = "none";
		var flip_43 = "none";
		var flip_44 = "none";
		var flip_45 = "none";
		var flip_46 = "none";
		var flip_47 = "none";
		var flip_48 = "none";
		var flip_49 = "none";
		var flip_50 = "none";
		var flip_51 = "none";
		var flip_52 = "none";
		var flip_53 = "none";
		var flip_54 = "none";
		var flip_55 = "none";
		var flip_56 = "none";
		var flip_57 = "none";
		var flip_58 = "none";
		var flip_59 = "none";
		var flip_60 = "none";
		var flip_61 = "none";
		var flip_62 = "none";
		var flip_63 = "none";
		var flip_64 = "none";
		var flip_65 = "none";
		var flip_66 = "none";
		var flip_67 = "none";
		// var flip_68 = "none";

		if (cens_ctry=="UK") {

			if (cenyear=="1911" || cenyear=="1921" || cenyear=="1931") {
				flip_3 = "";
				flip_4 = "";
				flip_7 = "";
				flip_9 = "";
				flip_16 = "";
				flip_17 = "";
				flip_18 = "";
				flip_19 = "";
				flip_35 = "";
				flip_37 = "";
				flip_38 = "";
				flip_42 = "";
				flip_50 = "";
				flip_63 = "";
			} else
			if (cenyear=="1901") {
				flip_3 = "";
				flip_4 = "";
				flip_7 = "";
				flip_9 = "";
				flip_35 = "";
				flip_38 = "";
				flip_42 = "";
				flip_50 = "";
				flip_63 = "";
			} else
			if (cenyear=="1891") {
				flip_3 = "";
				flip_4 = "";
				flip_7 = "";
				flip_9 = "";
				flip_35 = "";
				flip_39 = "";
				flip_40 = "";
				flip_43 = "";
				flip_50 = "";
				flip_63 = "";
			} else
			if (cenyear=="1881" || cenyear=="1871" || cenyear=="1861" || cenyear=="1851") {
				flip_3 = "";
				flip_4 = "";
				flip_7 = "";
				flip_9 = "";
				flip_35 = "";
				flip_50 = "";
				flip_63 = "";
			} else
			if (cenyear=="1841") {
				flip_7 = "";
				flip_9 = "";
				flip_35 = "";
				flip_52 = "";
				flip_53 = "";
			}

		} else if (cens_ctry=="USA") {
			if (cenyear=="1940") {
				flip_3 = "";
				flip_9 = "";
				flip_10 = "";
				flip_12 = "";
				flip_51 = "";
				flip_60 = "";
				flip_61 = "";
			} else
			if (cenyear=="1930") {
				flip_3 = "";
				flip_6 = "";
				flip_9 = "";
				flip_10 = "";
				flip_12 = "";
				flip_15 = "";
				flip_20 = "";
				flip_46 = "";
				flip_51 = "";
				flip_54 = "";
				flip_55 = "";
				flip_56 = "";
				flip_57 = "";
				flip_58 = "";
				flip_59 = "";
				flip_60 = "";
				flip_61 = "";
				flip_62 = "";
				flip_65 = "";
				flip_66 = "";
			} else
			if (cenyear=="1920") {
				flip_3 = "";
				flip_5 = "";
				flip_9 = "";
				flip_10 = "";
				flip_12 = "";
				flip_15 = "";
				flip_31 = "";
				flip_32 = "";
				flip_33 = "";
				flip_46 = "";
				flip_51 = "";
				flip_54 = "";
				flip_55 = "";
				flip_56 = "";
				flip_59 = "";
				flip_60 = "";
				flip_61 = "";
				flip_62 = "";
			} else
			if (cenyear=="1910") {
				flip_3 = "";
				flip_9 = "";
				flip_10 = "";
				flip_12 = "";
				flip_15 = "";
				flip_16 = "";
				flip_17 = "";
				flip_18 = "";
				//flip_22 = "";
				flip_27 = "";
				flip_28 = "";
				flip_29 = "";
				//flip_30 = "";
				flip_31 = "";
				flip_32 = "";
				flip_34 = "";
				flip_35 = "";
				flip_37 = "";
				flip_38 = "";
				flip_43 = "";
				flip_44 = "";
				flip_46 = "";
				flip_49 = "";
				flip_65 = "";
				flip_67 = "";
			} else
			if (cenyear=="1900") {
				flip_3 = "";
				flip_8 = "";
				flip_9 = "";
				flip_11 = "";
				flip_12 = "";
				flip_15 = "";
				flip_16 = "";
				flip_17 = "";
				flip_18 = "";
				//flip_22 = "";
				flip_27 = "";
				flip_28 = "";
				flip_29 = "";
				flip_30 = "";
				flip_31 = "";
				flip_32 = "";
				flip_35 = "";
				flip_45 = "";
				flip_46 = "";
				flip_48 = "";
				flip_49 = "";
			} else
			if (cenyear=="1890") {
				flip_3 = "";
				flip_8 = "";
				flip_9 = "";
				//flip_11 = "";
				flip_12 = "";
				flip_15 = "";
				flip_16 = "";
				flip_17 = "";
				flip_18 = "";
				//flip_22 = "";
				flip_27 = "";
				flip_28 = "";
				flip_29 = "";
				flip_30 = "";
				flip_32 = "";
				flip_35 = "";
				flip_38 = "";
				flip_41 = "";
				flip_46 = "";
				flip_48 = "";
				flip_64 = "";
			} else
			if (cenyear=="1880") {
				flip_8 = "";
				flip_9 = "";
				flip_12 = "";
				flip_13 = "";
				flip_14 = "";
				flip_15 = "";
				flip_16 = "";
				flip_35 = "";
				flip_36 = "";
				//flip_42 = "";
				flip_45 = "";
				flip_47= "";
				flip_48 = "";
				flip_51 = "";
				flip_54= "";
				flip_55 = "";
			} else
			if (cenyear=="1870" ) {
				flip_7 = "";
				flip_9 = "";
				flip_10 = "";
				flip_21 = "";
				flip_22 = "";
				flip_23 = "";
				flip_24 = "";
				flip_25 = "";
				flip_26 = "";
				flip_47 = "";
				flip_63 = "";
			} else
			if (cenyear=="1860" || cenyear=="1850") {
				flip_7 = "";
				flip_9 = "";
				flip_10 = "";
				flip_21 = "";
				flip_22 = "";
				flip_23 = "";
				flip_26 = "";
				flip_47 = "";
				flip_63 = "";
			}
		 } else if (cens_ctry=="FR") {
			if (cenyear != "1930") {
			//Faire figurer les champs qui doivent apparaitre dans la zone addition id  AD 2012
				//flip_1 = "";
				flip_3 = "";
				flip_4 = ""; //MC
				flip_7 = ""; //Age
				flip_9 = ""; //Age
				//flip_10 = ""; //MC AD 2012
				flip_11 = "";
				flip_16 = ""; //YoM
				flip_21 = ""; //BithPlace
				flip_23 = "";
				//flip_27 = "";
				//flip_28 = "";
				//flip_29 = "";
				flip_33 = "";
				//flip_43 = "";
				flip_46 = "";
				flip_49 = "";
			}
		}

		// Hide or show ===============
		for (var i=0; i<cols_0.length; i++) {
			cols_3[i].style.display = flip_3;
			cols_4[i].style.display = flip_4;
			cols_5[i].style.display = flip_5;
			cols_6[i].style.display = flip_6;
			cols_7[i].style.display = flip_7;
			cols_8[i].style.display = flip_8;
			cols_9[i].style.display = flip_9;
			cols_10[i].style.display = flip_10;
			cols_11[i].style.display = flip_11;
			cols_12[i].style.display = flip_12;
			cols_13[i].style.display = flip_13;
			cols_14[i].style.display = flip_14;
			cols_15[i].style.display = flip_15;
			cols_16[i].style.display = flip_16;
			cols_17[i].style.display = flip_17;
			cols_18[i].style.display = flip_18;
			cols_19[i].style.display = flip_19;
			cols_20[i].style.display = flip_20;
			cols_21[i].style.display = flip_21;
			cols_22[i].style.display = flip_22;
			cols_23[i].style.display = flip_23;
			cols_24[i].style.display = flip_24;
			cols_25[i].style.display = flip_25;
			cols_26[i].style.display = flip_26;
			cols_27[i].style.display = flip_27;
			cols_28[i].style.display = flip_28;
			cols_29[i].style.display = flip_29;
			cols_30[i].style.display = flip_30;
			cols_31[i].style.display = flip_31;
			cols_32[i].style.display = flip_32;
			cols_33[i].style.display = flip_33;
			cols_34[i].style.display = flip_34;
			cols_35[i].style.display = flip_35;
			cols_36[i].style.display = flip_36;
			cols_37[i].style.display = flip_37;
			cols_38[i].style.display = flip_38;
			cols_39[i].style.display = flip_39;
			cols_40[i].style.display = flip_40;
			cols_41[i].style.display = flip_41;
			cols_42[i].style.display = flip_42;
			cols_43[i].style.display = flip_43;
			cols_44[i].style.display = flip_44;
			cols_45[i].style.display = flip_45;
			cols_46[i].style.display = flip_46;
			cols_47[i].style.display = flip_47;
			cols_48[i].style.display = flip_48;
			cols_49[i].style.display = flip_49;
			cols_50[i].style.display = flip_50;
			cols_51[i].style.display = flip_51;
			cols_52[i].style.display = flip_52;
			cols_53[i].style.display = flip_53;
			cols_54[i].style.display = flip_54;
			cols_55[i].style.display = flip_55;
			cols_56[i].style.display = flip_56;
			cols_57[i].style.display = flip_57;
			cols_58[i].style.display = flip_58;
			cols_59[i].style.display = flip_59;
			cols_60[i].style.display = flip_60;
			cols_61[i].style.display = flip_61;
			cols_62[i].style.display = flip_62;
			cols_63[i].style.display = flip_63;
			cols_64[i].style.display = flip_64;
			cols_65[i].style.display = flip_65;
			cols_66[i].style.display = flip_66;
			cols_67[i].style.display = flip_67;
			//cols_68[i].style.display = flip_68;
		}
	}

</script>

<div class="optionbox cens_sour">
	<div class="cens_sour_country">
		<span><?php echo WT_I18N::translate('Country'); ?><br></span>
		<select id="censCtry" name="censCtry" >
			<option id="UKOPT" value="UK">UK</option>
			<option id="USOPT" value="USA">USA</option>
			<option id="FROPT" value="FR">FR</option>
		</select>

		<script>
		if (TheCenYear=='') {
			var censyear = new DynamicOptionList();
			censyear.addDependentFields("censCtry","censYear");
			censyear.forValue("UK").addOptions( "", "1841", "1851", "1861", "1871", "1881", "1891", "1901", "1911", "1921", "1931");
			censyear.forValue("USA").addOptions( "", "1790", "1800", "1810", "1820", "1830", "1840", "1850", "1860", "1870", "1880", "1890", "1900", "1910", "1920", "1930", "1940");
			censyear.forValue("FR").addOptions( "", "1876", "1881", "1886", "1891", "1896", "1901", "1906", "1911", "1914", "1921", "1926","1931", "1936", "1941", "1946", "1951");
			censyear.forValue("UK").setDefaultOptions("");
			censyear.forValue("UK").setDefaultOptions("");
			censyear.forValue("USA").setDefaultOptions("");
		}
		else if (TheCenYear!='' && TheCenCtry=='UK') {
			var censyear = new DynamicOptionList();
			censyear.addDependentFields("censCtry","censYear");
			censyear.forValue("UK").addOptions( "", "1841", "1851", "1861", "1871", "1881", "1891", "1901", "1911", "1921", "1931", TheCenYear);
			censyear.forValue("USA").addOptions( "", "1790", "1800", "1810", "1820", "1830", "1840", "1850", "1860", "1870", "1880", "1890", "1900", "1910", "1920", "1930", "1940");
			censyear.forValue("FR").addOptions( "", "1876", "1881", "1886", "1891", "1896", "1901", "1906", "1911", "1914", "1921", "1926","1931", "1936", "1941", "1946", "1951");
			censyear.forValue("FR").setDefaultOptions("");
			censyear.forValue("UK").setDefaultOptions(TheCenYear);
			censyear.forValue("USA").setDefaultOptions("");
			document.getElementById("UKOPT").selected = true;
			document.getElementById("USOPT").selected = false;
			document.getElementById("FROPT").selected = false;
		}
		else if (TheCenYear!='' && TheCenCtry=='USA') {
			var censyear = new DynamicOptionList();
			censyear.addDependentFields("censCtry","censYear");
			censyear.forValue("UK").addOptions( "", "1841", "1851", "1861", "1871", "1881", "1891", "1901", "1911", "1921", "1931");
			censyear.forValue("USA").addOptions( "", "1790", "1800", "1810", "1820", "1830", "1840", "1850", "1860", "1870", "1880", "1890", "1900", "1910", "1920", "1930", "1940", TheCenYear);
			censyear.forValue("FR").addOptions( "", "1876", "1881", "1886", "1891", "1896", "1901", "1906", "1911", "1914", "1921", "1926","1931", "1936", "1941", "1946", "1951");
			censyear.forValue("FR").setDefaultOptions("");
			censyear.forValue("UK").setDefaultOptions("");
			censyear.forValue("USA").setDefaultOptions(TheCenYear);
			document.getElementById("UKOPT").selected = false;
			document.getElementById("USOPT").selected = true;
			document.getElementById("FROPT").selected = false;
		}
		else if (TheCenYear!='' && TheCenCtry=='FR') {
			var censyear = new DynamicOptionList();
			censyear.addDependentFields("censCtry","censYear");
			censyear.forValue("UK").addOptions( "", "1841", "1851", "1861", "1871", "1881", "1891", "1901", "1911", "1921", "1931");
			censyear.forValue("USA").addOptions( "", "1790", "1800", "1810", "1820", "1830", "1840", "1850", "1860", "1870", "1880", "1890", "1900", "1910", "1920", "1930", "1940", TheCenYear);
			censyear.forValue("FR").addOptions( "", "1876", "1881", "1886", "1891", "1896", "1901", "1906", "1911", "1914", "1921", "1926","1931", "1936", "1941", "1946", "1951");
			censyear.forValue("USA").setDefaultOptions("");
			censyear.forValue("UK").setDefaultOptions("");
			censyear.forValue("FR").setDefaultOptions(TheCenYear);
			document.getElementById("UKOPT").selected = false;
			document.getElementById("FROPT").selected = true;
			document.getElementById("USOPT").selected = false;
		}
		</script>

		<input type="hidden" id="censDate" name="censDate" value="">

	</div>

	<div class="cens_sour_year">
		<span><?php echo WT_I18N::translate('Year'); ?><br></span>
		<select style = "background:#ffaaaa;"
				onchange = "if (this.options[this.selectedIndex].value!='') {
								changeYear(this.options[this.selectedIndex].value);
							}"
				id="censYear" name="censYear">
		</select>
		<input type="hidden" id="prevYear" name="prevYear" value="">
	</div>

	<div class="cens_sour_scs">
		<div class="cens_sour_1">
			<div class="cens_sour_2"><?php echo WT_I18N::translate('Title'); ?></div>
			<input id="Titl" name="Titl" type="text" value="">
		</div>
		<div class="cens_sour_1">
			<div class="cens_sour_2"><?php echo WT_Gedcom_Tag::getLabel('PAGE'); ?></div>
			<input id="citation" name="citation" type="text" value="">
		</div>
		<div class="cens_sour_1">
			<div class="cens_sour_2"><?php echo WT_I18N::translate('Place'); ?></div>
			<input id="locality" name="locality" type="text" value="">
		</div>
		<div class="cens_sour_1">
			<div class="cens_sour_2"><?php echo WT_I18N::translate('Notes'); ?></div>
			<input id="notes" name="notes" type="text" value="">
		</div>
	</div>
</div>
