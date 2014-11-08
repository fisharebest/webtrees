<?php
// Census Assistant Control module for webtrees
//
// Census information about an individual
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.
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

// modified from tabledeleterow.js version 1.2 2006-02-21
// mredkj.com

// CONFIG notes. Below are some comments that point to where this script can be customized.
// Note: Make sure to include a <tbody></tbody> in your table’s HTML

echo '<script src="', WT_STATIC_URL, WT_MODULES_DIR, 'GEDFact_assistant/_CENS/js/chapman_codes.js"></script>';
?>
<script>

//Load Language variables for Edit header and tooltip ============================
var HeaderName        = 'Name';
var TTEditName        = 'Full Name or Married name if married';
var HeaderRela        = 'Relation';
var TTEditRela        = 'Relationship to Head of Household - Head, Wife, Son etc';
var HeaderMCond       = 'MC';
var TTEditMCond       = 'Marital Condition - M,S,U,W,D - Married, Single, Unmarried, Widowed or Divorced';
var HeaderAsset       = 'Assets';
var TTEditAsset       = 'Assets = O,R - value,rent - Y,N,R - Y,N,F  =  Owned,Rented - Value,Rent - Radio - Farm';
var HeaderAge         = 'Age';
var TTEditAge         = 'Age at last birthday';
var HeaderRace        = 'Race';
var TTEditRace        = 'Race or Color - B.W,M,A,I,C - Black, White, Mulatto, Asian, Indian, Chinese etc';
var HeaderSex         = 'Sex';
var TTEditSex         = 'Male(M) or Female(F)';
var HeaderYOB         = 'DOB';
var TTEditYOB         = 'Date of Birth - mmm yyyy';
var HeaderBmth        = 'Bmth';
var TTEditBmth        = 'If born within Census year - mmm - Month of birth';
var HeaderYrsM        = 'YrsM';
var TTEditYrsM        = 'Years Married or if married in Census Year - yy or Y';
var HeaderChilB       = 'ChB';
var TTEditChilB       = 'Children born alive - nn';
var HeaderChilL       = 'ChL';
var TTEditChilL       = 'Children still living - nn';
var HeaderChilD       = 'ChD';
var TTEditChilD       = 'Children who have died - nn';
var HeaderAgeM        = 'AgM';
var TTEditAgeM        = 'Age at first marriage - yy';
var HeaderOccu        = 'Occupation';
var TTEditOccu        = 'Occupation';
var HeaderBplace      = 'Birthplace';    // Full format
var TTEditBplace      = 'Birthplace (Full format)';   // Full format
var HeaderBP          = 'BP';        // Chapman format
var TTEditBP          = 'Birthplace - xx or xxx - State/Country (Chapman format)';       // Chapman format
var HeaderFBP         = 'FBP';       // Chapman format
var TTEditFBP         = 'Father\'s Birthplace - xx or xxx - State or Country (Chapman format)';      // Chapman format
var HeaderMBP         = 'MBP';       // Chapman format
var TTEditMBP         = 'Mother\'s Birthplace - xx or xxx - State or Country (Chapman format)';      // Chapman format
var HeaderNL          = 'NL';
var TTEditNL          = 'If Foreign Born - Native Language';
var HeaderHealth      = 'Health';
var TTEditHealth      = 'Health - 12345 = 1.Blind, 2.Deaf&amp;Dumb, 3.Idiotic, 4.Insane, 5.Disabled etc';
var HeaderYrsUS       = 'YUS';
var TTEditYrsUS       = 'If Foreign Born - yy -Years in the USA';
var HeaderYOI         = 'YOI';
var TTEditYOI         = 'If Foreign Born - yyyy - Year of immigration';
var HeaderNA          = 'N/A';
var TTEditNA          = 'If Foreign Born - N,A - Naturalized, Alien';
var HeaderYON         = 'YON';
var TTEditYON         = 'If Foreign Born - yyyy - Year of naturalization';
var HeaderEngL        = 'EngL';
var TTEditEngL        = 'English spoken?, if not, Native Language';
var HeaderEng         = 'Eng?';
var TTEditEng         = 'English spoken? - Y/N';
var HeaderInd         = 'Industry';
var TTEditInd         = 'Industry';
var HeaderEmp         = 'Employ';
var TTEditEmp         = 'Employment - Yes, No, Worker, Employer etc';
var HeaderEmR         = 'EmR';
var TTEditEmR         = 'Employer? - Y/N';
var HeaderEmD         = 'EmD';
var TTEditEmD         = 'Employed? - Y/N';
var HeaderEmH         = 'WH';
var TTEditEmH         = 'Working at Home? - Y/N';
var HeaderEmN         = 'EmN';
var TTEditEmN         = 'UnEmployed? - Y/N';
var HeaderEduc        = 'Edu';
var TTEditEduc        = 'Education - xxx - At School? Y/N, Can Read? Y/N, Can Write? Y/N';
var HeaderBIC         = 'BIC';
var TTEditBIC         = 'Born in County - Y/N - (UK 1841 only)';
var HeaderBOE         = 'BOE';
var TTEditBOE         = 'Born outside England - SCO,IRE,WAL,FOReign - (UK 1841 only)';
var HeaderInfirm      = 'Infirm';
var TTEditInfirm      = 'Infirmaties - 1234 - 1.Deaf&amp;Dumb, 2.Blind, 3.Lunatic, 4.Imbecile/feeble-minded';
var HeaderVet         = 'Vet';
var TTEditVet         = 'War Veteran? - Y/N';
var HeaderTenure      = 'Ten';
var TTEditTenure      = 'Tenure - xx - Owned/Rented, (if owned)Free/Mortgaged - eg OM, or R-, or OF';
var HeaderParent      = 'Par';
var TTEditParent      = 'Parentage - xx = Father if foreign born Y/N/-, Mother if foreign born Y/N/- = eg YY, YN, NY, or -';
var HeaderMmth        = 'Mmth';
var TTEditMmth        = 'Marriage month - mmm = If married within Census year - Month of marriage';
var HeaderMnse        = 'MnsE';
var TTEditMnse        = 'Months Employed - xx = Months employed during Census Year';
var HeaderWksu        = 'WksU';
var TTEditWksu        = 'Weeks Unemployed - xx = Weeks unemployed during Census Year';
var HeaderMnsu        = 'MnsU';
var TTEditMnsu        = 'Months Unemployed - xx = Months unemployed during Census Year';
var HeaderHome        = 'Home';
var TTEditHome        = 'Home Ownership - x-x-x-xxxx = O/R-F/M-F/H-#### = Owned/Rented-Free/Mortgaged-Farm/House-Farm Schedule number';
var HeaderSitu        = 'Situ';
var TTEditSitu        = 'Situation - 3 parameters - Diseases, Infimaties, Convict/Pauper etc';
var HeaderWar         = 'War';
var TTEditWar         = 'War or Expedition';
var HeaderInfirm1910  = 'Infirm';
var TTEditInfirm1910  = 'Infirmaties - xx = Whether blind (both eyes) Y/N, Whether Deaf and Dumb Y/N';
var HeaderEducpre1890 = 'Edu';
var TTEditEducpre1890 = 'Education - xxx = At School, Cannot Read, Cannot Write = eg x--, xxx, or -xx etc';

var HeaderLang        = 'Lang';
var TTEditLang        = 'If Foreign Born - Native Language';

// Load Edit Table variables =====================================================
var INPUT_NAME_PREFIX = 'InputCell_'; // this is being set via script
var RADIO_NAME = "totallyrad"; // this is being set via script
var TABLE_NAME = 'tblSample'; // this should be named in the HTML
var ROW_BASE = 0; // first number (for display)
var hasLoaded = false;


// Load Other variables =======================================================
var NoteCtry = document.getElementById('censCtry');
var NoteYear = document.getElementById('censYear');
var NoteTitl = document.getElementById('Titl');

// Functions ==================================================================
function caSave() {
	preview();
	pastedate();
}

function pastedate() {
	window.opener.pasteAsstDate(document.getElementById('censCtry').value, document.getElementById('censYear').value);
}

function preview() {
	NoteCtry = document.getElementById('censCtry');
	NoteYear = document.getElementById('censYear');
	Citation = document.getElementById('citation');
	Locality = document.getElementById('locality');
	Notes    = document.getElementById('notes');

	str = NoteYear.value + " " + NoteCtry.value + " " + NoteTitl.value;
	str += "\n";
	if (Citation.value!="" && Citation.value!=null) {
		str += Citation.value + "\n";
	}
	if (Locality.value!="" && Locality.value!=null) {
		str += Locality.value + "\n";
	}
	str += "\n";
	str += ".start_formatted_area.";

	iid = "";

	var tbl = document.getElementById('tblSample');


	for (var i=0; i<tbl.rows.length; i++) {
		var tr = tbl.rows[i];
		var strRow = '';

		var pidList = '';

		// ---------------------------------------------

		// Extract Indi IDs from created list --------------------------------------
		for (var y=1; y<tr.cells.length-3; y++) {
			if (y>=2 && y<=73) {
					continue;
			} else {
				if (i!=0) {
					// pidList += '\'' + (pidList==''?'':' ') + tr.cells[1].childNodes[0].value + '\'';
					pidList += (pidList==''?'':' ') + tr.cells[1].childNodes[0].value;
				}
			}
		}

		// Extract required columns for display based on Country and Year -----------
		if (NoteCtry.value=="UK") {
			// UK 1911 or 1921 or 1931 ===============
			if (NoteYear.value=="1911" || NoteYear.value=="1921" || NoteYear.value=="1931") {
				for (var j=2; j<tr.cells.length-3; j++) {
					if (j==5 || j==6 || j==8 || (j>=10 && j<=15) || (j>=20 && j<=34) || j==36 || (j>=39 && j<=41) || (j>=43 && j<=49) || (j>=51 && j<=62) || (j>=64 && j<=73) ) {
							continue;
					} else {
						if (i==0) {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].id;
						} else {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].value;
						}
					}
				}
			// UK 1901 ===============
			} else if (NoteYear.value=="1901") {
				for (var j=2; j<tr.cells.length-3; j++) { // == j=2 means miss out cols 0 and 1 (# and pid), cells.length-3 means miss out del, ins and item #
					if (j==5 || j==6 || j==8 || (j>=10 && j<=34) || j==36 || j==37 || (j>=39 && j<=41) || (j>=43 && j<=49) || (j>=51 && j<=62) || (j>=64 && j<=73)) {
							continue;
					} else {
						if (i==0) {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].id;
						} else {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].value;
						}
					}
				}
			// UK 1891 ===============
			} else if (NoteYear.value=="1891") {
				for (var j=2; j<tr.cells.length-3; j++) { // == j=2 means miss out cols 0 and 1 (# and pid), cells.length-3 means miss out del, ins and item #
					if (j==5 || j==6 || j==8 || (j>=10 && j<=34) || (j>=36 && j<=38) || j==41 || j==42 || (j>=44 && j<=49) || (j>=51 && j<=62) || (j>=64 && j<=73)) {
							continue;
					} else {
						if (i==0) {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].id;
						} else {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].value;
						}
					}
				}
			// UK 1951-1881 ============
			} else if (NoteYear.value=="1851" || NoteYear.value=="1861" || NoteYear.value=="1871" || NoteYear.value=="1881") {
				for (var j=2; j<tr.cells.length-3; j++) { // == j=2 means miss out cols 0 and 1 (# and pid), cells.length-3 means miss out del, ins and item #
					if (j==5 || j==6 || j==8 || (j>=10 && j<=34) || (j>=36 && j<=49) || (j>=51 && j<=62) || (j>=64 && j<=73)) {
							continue;
					} else {
						if (i==0) {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].id;
						} else {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].value;
						}
					}
				}
			// UK 1841 ===============
			} else if (NoteYear.value=="1841") {
				for (var j=2; j<tr.cells.length-3; j++) { // == j=2 means miss out cols 0 and 1 (# and pid), cells.length-3 means miss out del, ins and item #
					if ((j>=3 && j<=6) || j==8 || (j>=10 && j<=34) || (j>=36 && j<=51) || (j>=54 && j<=73)) {
							continue;
					} else {
						if (i==0) {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].id;
						} else {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].value;
						}
					}
				}
			}

		} else if (NoteCtry.value=="USA") {
			// USA 1940 ===============
			if (NoteYear.value=="1940") {
				for (var j=2; j<tr.cells.length-3; j++) { // == j=2 means miss out cols 0 and 1 (# and pid), cells.length-3 means miss out del, ins and item #
					if ( (j>=4 && j<=8) || j==11 || (j>=13 && j<=50) || (j>=52 && j<=59) || j>=62 && j<=73) {
							continue;
					} else {
						if (i==0) {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].id;
						} else {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].value;
						}
					}
				}
			}

			// USA 1930 ===============
			else if (NoteYear.value=="1930") {
				for (var j=2; j<tr.cells.length-3; j++) { // == j=2 means miss out cols 0 and 1 (# and pid), cells.length-3 means miss out del, ins and item #
					if (j==4 || j==5 || j==7 || j==8 || j==11 || j==13 || j==14 || (j>=16 && j<=19) || (j>=21 && j<=45) || (j>=47 && j<=50) || j==52 || j==53 || j==63 || j==64 || j>=67 && j<=73) {
							continue;
					} else {
						if (i==0) {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].id;
						} else {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].value;
						}
					}
				}
			}

			// USA 1920 ===============
			else if (NoteYear.value=="1920") {
				for (var j=2; j<tr.cells.length-3; j++) { // == j=2 means miss out cols 0 and 1 (# and pid), cells.length-3 means miss out del, ins and item #
					if (j==4 || (j>=6 && j<=8) || j==11 || j==13 || j==14 || (j>=16 && j<=30) || (j>=34 && j<=45) || (j>=47 && j<=50) || j==52 || j==53 || j==57 || j==58 || (j>=63 && j<=73)) {
							continue;
					} else {
						if (i==0) {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].id;
						} else {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].value;
						}
					}
				}
			}

			// USA 1910 ===============
			else if (NoteYear.value=="1910") {
				for (var j=2; j<tr.cells.length-3; j++) { // == j=2 means miss out cols 0 and 1 (# and pid), cells.length-3 means miss out del, ins and item #
					if ((j>=4 && j<=8) || j==11 || j==13 || j==14 || (j>=19 && j<=26) || j==30 || j==33 || j==36 || (j>=39 && j<=42) || j==45 || j==47 || j==48 || (j>=50 && j<=64) || j==66 || j>=68 && j<=73) {
							continue;
					} else {
						if (i==0) {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].id;
						} else {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].value;
						}
					}
				}
			}
			// USA 1900 ===============
			else if (NoteYear.value=="1900") {
				for (var j=2; j<tr.cells.length-3; j++) { // == j=2 means miss out cols 0 and 1 (# and pid), cells.length-3 means miss out del, ins and item #
					if ((j>=4 && j<=7) || j==10 || j==13 || j==14 || (j>=19 && j<=26) || j==33 || j==34 || (j>=36 && j<=44) || j==47 || (j>=50 && j<=73)) {
							continue;
					} else {
						if (i==0) {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].id;
						} else {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].value;
						}
					}
				}
			}
			// USA 1890 ===============
			else if (NoteYear.value=="1890") {
				for (var j=2; j<tr.cells.length-3; j++) { // == j=2 means miss out cols 0 and 1 (# and pid), cells.length-3 means miss out del, ins and item #
					if ((j>=4 && j<=7) || j==10 || j==11 || j==13 || j==14 || (j>=19 && j<=26) || j==31 || j==33 || j==34 || j==36 || j==37 || j==39 || j==40 || (j>=42 && j<=45) || j==47 || (j>=49 && j<=63) || (j>=65 && j<=73)) {
							continue;
					} else {
						if (i==0) {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].id;
						} else {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].value;
						}
					}
				}
			}
			// USA 1880 ===============
			else if (NoteYear.value=="1880") {
				for (var j=2; j<tr.cells.length-3; j++) { // == j=2 means miss out cols 0 and 1 (# and pid), cells.length-3 means miss out del, ins and item #
					if ((j>=3 && j<=7) || j==10 || j==11 || (j>=17 && j<=34) || (j>=37 && j<=44) || (j>=40 && j<=42) || j==46 || j==49 || j==50 || j==52 || j==53 || (j>=56 && j<=73)) {
							continue;
					} else {
						if (i==0) {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].id;
						} else {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].value;
						}
					}
				}
			}
			// USA 1870 ===============
			else if (NoteYear.value=="1870") {
				for (var j=2; j<tr.cells.length-3; j++) { // == j=2 means miss out cols 0 and 1 (# and pid), cells.length-3 means miss out del, ins and item #
					if ((j>=3 && j<=6) || j==8 || (j>=11 && j<=20) || (j>=27 && j<=46) || (j>=48 && j<=62) || (j>=64 && j<=73)) {
							continue;
					} else {
						if (i==0) {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].id;
						} else {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].value;
						}
					}
				}
			}
			// USA 1860 or 1850 ===============
			else if (NoteYear.value=="1860" || NoteYear.value=="1850") {
				for (var j=2; j<tr.cells.length-3; j++) { // == j=2 means miss out cols 0 and 1 (# and pid), cells.length-3 means miss out del, ins and item #
					if ((j>=3 && j<=6) || j==8 || (j>=11 && j<=20) || j==24 || j==25 || (j>=27 && j<=46) || (j>=48 && j<=62) || (j>=64 && j<=73)) {
							continue;
					} else {
						if (i==0) {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].id;
						} else {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].value;
						}
					}
				}
			}

		} else if (NoteCtry.value=="FR") {
			// FR  ==========Modele FR  AD 2012=====
			// Faire figurer les champs qui ne doivent pas apparaitre dans la zone texte
			if (NoteYear.value !="1930") {
				for (var j=2; j<tr.cells.length-3; j++) { // == j=2 means miss out cols 0 and 1 (# and pid), cells.length-3 means miss out del, ins and item #
					if ( j==5 || j==6 || j==8  || j==10 || j==11 || (j>=12 &&  j<16) || (j>=17 &&  j<21) || (j>=22 &&  j<=29) || (j>=30 &&  j<33)|| (j>=34 &&  j<46) || (j>=47 &&  j<49)|| (j>=50 && j<=73) ) {
							continue;
					} else {
						if (i==0) {
							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].id;
						} else {

							strRow += (strRow==''?'':'|') + tr.cells[j].childNodes[0].value;
						}
					}
				}
			}
			// Other country stuff
		}

		str += (str==''?'':'\n') + strRow;
		if (i!=0) {
			iid += (iid==''?'':'') + pidList + ', ';
		}

		// Reset/Check the Pink Highlighting of the Name field and Age fields for any person not born ------------------
		for (var j=2; j<tr.cells.length-3; j++) { // == j=2 means miss out cols 0 and 1 (# and pid), cells.length-3 means miss out del, ins and item #
			if (i==0) {
				// Do nothing as this is the header row.
			} else {
				if (tr.cells[7].childNodes[0].value != "-" || tr.cells[12].childNodes[0].value != "-") {
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

	var mem = document.getElementById('NOTE');
	if (Notes.value!="" && Notes.value!=null) {
		mem.value = str + "\n.end_formatted_area.\n\nNotes:\n"+Notes.value;
	} else {
		mem.value = str + "\n.end_formatted_area.\n";
	}

	// ---- Create an array of Indi IDs ----------
	var mem21 = document.getElementById('pid_array');
		mem21.value = iid.slice(0, -2);

} // ---- end function preview() -----


window.onload=fillInRows;

// fillInRows - can be used to pre-load a table with a header, row, or rows
function fillInRows() {
	hasLoaded = true;
	if (TheCenYear!='') {
		changeYear(TheCenYear);
	}
}

// myRowObject - an object for storing information about the table rows
function myRowObject( zero, one, two, three, four, five, six, seven, eight, nine,
						ten, eleven, twelve, thirteen, fourteen, fifteen, sixteen, seventeen, eighteen, nineteen,
						twenty, twentyone, twentytwo, twentythree, twentyfour, twentyfive, twentysix, twentyseven, twentyeight, twentynine,
						thirty, thirtyone, thirtytwo, thirtythree, thirtyfour, thirtyfive, thirtysix, thirtyseven, thirtyeight, thirtynine,
						forty, fortyone, fortytwo, fortythree, fortyfour, fortyfive, fortysix, fortyseven, fortyeight, fortynine,
						fifty, fiftyone, fiftytwo, fiftythree, fiftyfour, fiftyfive, fiftysix, fiftyseven, fiftyeight, fiftynine,
						sixty, sixtyone, sixtytwo, sixtythree, sixtyfour, sixtyfive, sixtysix, sixtyseven, sixtyeight, sixtynine,
						seventy, seventyone, seventytwo, seventythree,
						cb, ra, index2
					)
{

	this.zero = zero; // text object
	this.one = one; // input text object
	this.two = two; // input text object
	this.three = three; // input text object
	this.four = four; // input text object
	this.five = five; // input text object
	this.six = six; // input text object
	this.seven = seven; // input text object
	this.eight = eight; // input text object
	this.nine = nine; // input text object
	this.ten = ten; // input text object
	this.eleven = eleven; // input text object
	this.twelve = twelve; // input text object
	this.thirteen = thirteen; // input text object
	this.fourteen = fourteen; // input text object
	this.fifteen = fifteen; // input text object
	this.sixteen = sixteen; // input text object
	this.seventeen = seventeen; // input text object
	this.eighteen = eighteen; // input text object
	this.nineteen = nineteen; // input text object
	this.twenty = twenty; // input text object
	this.twentyone = twentyone; // input text object
	this.twentytwo = twentytwo; // input text object
	this.twentythree = twentythree; // input text object
	this.twentyfour = twentyfour; // input text object
	this.twentyfive = twentyfive; // input text object
	this.twentysix = twentysix; // input text object
	this.twentyseven = twentyseven; // input text object
	this.twentyeight = twentyeight; // input text object
	this.twentynine = twentynine; // input text object
	this.thirty = thirty; // input text object
	this.thirtyone = thirtyone; // input text object
	this.thirtytwo = thirtytwo; // input text object
	this.thirtythree = thirtythree; // input text object
	this.thirtyfour = thirtyfour; // input text object
	this.thirtyfive = thirtyfive; // input text object
	this.thirtysix = thirtysix; // input text object
	this.thirtyseven = thirtyseven; // input text object
	this.thirtyeight = thirtyeight; // input text object
	this.thirtynine = thirtynine; // input text object
	this.forty = forty; // input text object
	this.fortyone = fortyone; // input text object
	this.fortytwo = fortytwo; // input text object
	this.fortythree = fortythree; // input text object
	this.fortyfour = fortyfour; // input text object
	this.fortyfive = fortyfive; // input text object
	this.fortysix = fortysix; // input text object
	this.fortyseven = fortyseven; // input text object
	this.fortyeight = fortyeight; // input text object
	this.fortynine = fortynine; // input text object
	this.fifty = fifty; // input text object
	this.fiftyone = fiftyone; // input text object
	this.fiftytwo = fiftytwo; // input text object
	this.fiftythree = fiftythree; // input text object
	this.fiftyfour = fiftyfour; // input text object
	this.fiftyfive = fiftyfive; // input text object
	this.fiftysix = fiftysix; // input text object
	this.fiftyseven = fiftyseven; // input text object
	this.fiftyeight = fiftyeight; // input text object
	this.fiftynine = fiftynine; // input text object
	this.sixty = sixty; // input text object
	this.sixtyone = sixtyone; // input text object
	this.sixtytwo = sixtytwo; // input text object
	this.sixtythree = sixtythree; // input text object
	this.sixtyfour = sixtyfour; // input text object
	this.sixtyfive = sixtyfive; // input text object
	this.sixtysix = sixtysix; // input text object
	this.sixtyseven = sixtyseven; // input text object
	this.sixtyeight = sixtyeight; // input text object
	this.sixtynine = sixtynine; // input text object
	this.seventy = seventy; // input text object
	this.seventyone = seventyone; // input text object
	this.seventytwo = seventytwo; // input text object
	this.seventythree = seventythree; // input text object

	this.cb = cb; // input checkbox object
	this.ra = ra; // input radio object
	this.index2 = index2; // text object
}

function create_header() {
		addRowToTable();
}

// insertRowToTable - inserts a row into the table (and reorders)
function insertRowToTable(pid, nam, mnam, label, gend, cond, dom, dob, age, dod, occu, birthpl, fbirthpl, mbirthpl, chilBLD) {

	if (hasLoaded) {

		// calculate marriage status -----------------------
		var cenyr = document.getElementById('censYear').value;

		var tbl = document.getElementById(TABLE_NAME);
		var rowToInsertAt = tbl.tBodies[0].rows.length;
		for (var i=1; i<tbl.tBodies[0].rows.length; i++) {  // i set to 1 to avoid header row of number 0
			if (tbl.tBodies[0].rows[i].myRow && tbl.tBodies[0].rows[i].myRow.ra.getAttribute('type') == 'radio' && tbl.tBodies[0].rows[i].myRow.ra.checked) {
				rowToInsertAt = i;
				break;
			}
		}

		addRowToTable(rowToInsertAt, pid, nam, mnam, label, gend, cond, dom, dob, age, dod, occu, birthpl, fbirthpl, mbirthpl, chilBLD);

		reorderRows(tbl, rowToInsertAt);
		currcenyear = document.getElementById('censYear').value;

		changeCols(currcenyear);
		changeMC(currcenyear);
		changeAge(currcenyear);
		preview();

	}
}

// addRowToTable - Inserts at row 'num', or appends to the end if no arguments are passed in. Don't pass in empty strings.
function addRowToTable(num, pid, nam, mnam, label, gend, cond, dom, dob, age2, dod, occu, birthpl, fbirthpl, mbirthpl, chilBLD, cb, ra) {

	// Check if Census year filled in -------------
	// var cctry = document.getElementById('censCtry').value;
	var cyear = document.getElementById('censYear').value;
	if (cyear == "choose") {
		alert("You must choose a Census year first");
		return;
	}

	if (num==0) {
		var  birthpl = '';
		var fbirthpl = '';
		var mbirthpl = '';
	}

	// Calculate various Dates and Places for input ===========================
	currcenyear = document.getElementById('censYear').value;
	currcenctry = document.getElementById('censCtry').value;
	if (num>0) {


		// Calculate Children Born, Living, Died --------------------------
		var chBLDarray = chilBLD.split('::');
		var cendat = getCenDate(currcenyear);
		var cdat   = calculateJD(cendat);
		// Variables to be used ---------
		var ChilBorn = chBLDarray.length;
		var ChilLivg = "-";
		var ChilDied = "-";
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
			BORN  = "-";
			ALIVE = "-";
			DEAD  = "-";
		}
		if (ALIVE == 0) {
			ALIVE = "-";
		}
		if (DEAD == 0) {
			DEAD = "-";
		}

		// Calculate birth places -----------------------------------------
		 birthpl =  birthpl.split(", ");
		ibirthpl =  birthpl.reverse();
		fbirthpl = fbirthpl.split(", ");
		fbirthpl = fbirthpl.reverse();
		mbirthpl = mbirthpl.split(", ");
		mbirthpl = mbirthpl.reverse();

		// get Chapman Code for US ----------------------------------------
		if (birthpl[0] == "United States" || birthpl[0] == "United States Of America" || birthpl[0] == "USA") {
			var ibirthpl = getChapmanCode(ibirthpl[1]);
		} else {
			var ibirthpl = getChapmanCode(ibirthpl[0]);
		}

		if (fbirthpl[0] == "UNK") {
			var fbirthpl = getChapmanCode(fbirthpl[0]);
		} else if (fbirthpl[0] == "United States" || fbirthpl[0] == "United States Of America" || fbirthpl[0] == "USA") {
			var fbirthpl = getChapmanCode(fbirthpl[1]);
		} else {
			var fbirthpl = getChapmanCode(fbirthpl[0]);
		}

		if (mbirthpl[0] == "UNK") {
			var mbirthpl = getChapmanCode(mbirthpl[0]);
		} else if (mbirthpl[0] == "United States" || mbirthpl[0] == "United States Of America" || mbirthpl[0] == "USA") {
			var mbirthpl = getChapmanCode(mbirthpl[1]);
		} else {
			var mbirthpl = getChapmanCode(mbirthpl[0]);
		}

		// get birthplace for UK (check all countries in UK) --------------
		if (birthpl==null || birthpl=='') {
			birthpl = '-';
		} else if (birthpl[0]!="England" && birthpl[0]!="Scotland" && birthpl[0]!="Wales" && birthpl[0]!="Northern Ireland" && birthpl[0]!="UK") {
			birthpl = birthpl[0]+", "+birthpl[1];
		} else {
			birthpl = birthpl[1]+", "+birthpl[2];
		}

		// Calculate/Format Birth, Marriage and Death dates ==========================
		var one_day   = 1000*60*60*24;
		var one_month = (365.26*one_day)/12;
		var one_year  = 365.26*one_day;

		// Date of Birth (dob) - passed as Julian Date String -----------------
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
			yob = I;
		}

		// Date of Marriage (dom) - passed as Julian Date String --------------
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

		// Create Date of Birth object and Age at First Marriage from passed string dob
		var jsdob = Date.parseString(dob, 'y, M, d');
		// US Census - Create dob (month year) and Age at first marriage ------
		if (jsdob != "Invalid Date" && jsdob != "" && jsdob != null) {
			usdob = jsdob.format("NNN "+yob);
			agemarr = Math.floor((jsdom-jsdob)/one_year);
		} else {
			usdob = '-';
			agemarr = '-';
		}

		// Date of Death (dod) - passed as Julian Date String --------------
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

	}


	if (hasLoaded) {

		var tbl = document.getElementById(TABLE_NAME);
		var nextRow = tbl.tBodies[0].rows.length;
		var iteration = nextRow + ROW_BASE;
		if (num == null) {
			num = nextRow;
			num2 = num;
		} else {
			iteration = num + ROW_BASE;
		}

		// Initial Age Calculation based on 1901 Census Year input ============
		// *** NOTE ***
		// *** This is then corrected when ChangeYear() function is run
		// *** ChangeYear() is run each time Census Year is selected or changed
		if (age2 != "Age" && age2 != null) {
			// Check if Census year filled in -------------
			if (cyear == "choose") {
				alert("You must choose a Census year first");
				return;
			}
			var diffage = 1901 - cyear;
			age = age2 - diffage;
		} else {
			age = "-";
		}

		// add the row =======================================================
		var row = tbl.tBodies[0].insertRow(num);

		// **A** Define Cells ===============================================
		var cell_ = new Array(73);
		for (var i=0; i<=73; i++) {
				cell_[i] = row.insertCell(i);
				cell_[i].setAttribute('id', 'col_'+i);
				cell_[i].setAttribute('name', 'col_'+i);
		}

		if (iteration == 0) {
			var cell_tdel = row.insertCell(74); // text Del
			var cell_tra  = row.insertCell(75); // text Radio
		} else {
			var cell_del = row.insertCell(74); // Onclick = Delete Row
				cell_del.setAttribute('align', 'center');
			var cell_ra = row.insertCell(75); // Radio button used for inserting a row, rather than adding at end of table)
		}

		var cell_index2 = row.insertCell(76); // Item Number (#2)
			cell_index2.setAttribute('id', 'index2');
			cell_index2.setAttribute('name', 'index2');
			cell_index2.setAttribute('align', 'center');


		// **B** SHOW/HIDE Header Cell elements ===============================
			// ---- Basic Hidden Columns (miss out 0,1,2 and >68)
			for (var i=3; i<=69; i++) {
				cell_[i].style.display = "none";
			}

			// ---- Show Cell Columns =========================================

		// **C** Define Header Cell elements ==================================
		if (iteration == 0) {
		// 0. Item Number -----------------------------------------------------
			var txt_itemNo = document.createElement('div');
				txt_itemNo.setAttribute('class', 'descriptionbox');
				txt_itemNo.className= 'descriptionbox'; //Required for IE
				txt_itemNo.style.border='0px';
				txt_itemNo.innerHTML = '#';
				txt_itemNo.setAttribute('id', '.b.Item');
				txt_itemNo.style.fontSize="10px";
				txt_itemNo.style.display="none";
		// 1. Indi ID ---------------------------------------------------------
			var txtInp_pid = document.createElement('div');
				txtInp_pid.setAttribute('class', 'descriptionbox');
				txtInp_pid.className= 'descriptionbox'; //Required for IE
				txtInp_pid.style.fontSize="10px";
				txtInp_pid.style.border='0px';
				txtInp_pid.innerHTML = '<a href="#" title="ID"> ID </a>';
		// 2. Name ------------------------------------------------------------
			var txtInp_nam = document.createElement('div');
				txtInp_nam.setAttribute('class', 'descriptionbox');
				txtInp_nam.className= 'descriptionbox'; //Required for IE
				txtInp_nam.style.fontSize="10px";
				txtInp_nam.style.border='0px';
				txtInp_nam.innerHTML = '<a href="#" title="'+TTEditName+'">'+HeaderName+'</a>';
				txtInp_nam.setAttribute('id', '.b.'+HeaderName);
		// 3. Relationship_1 --------------------------------------------------
			var txtInp_label = document.createElement('div');
				txtInp_label.setAttribute('class', 'descriptionbox');
				txtInp_label.className= 'descriptionbox'; //Required for IE
				txtInp_label.style.fontSize="10px";
				txtInp_label.style.border='0px';
				txtInp_label.innerHTML = '<a href="#" title="'+TTEditRela+'">'+HeaderRela+'</a>';
				txtInp_label.setAttribute('id', '.b.'+HeaderRela);
		// 4. Conditition_1 ---------------------------------------------------
			var txtInp_cond = document.createElement('div');
				txtInp_cond.setAttribute('class', 'descriptionbox');
				txtInp_cond.className= 'descriptionbox'; //Required for IE
				txtInp_cond.style.fontSize="10px";
				txtInp_cond.style.border='0px';
				txtInp_cond.innerHTML = '<a href="#" title="'+TTEditMCond+'">'+HeaderMCond+'</a>';
				txtInp_cond.setAttribute('id', '.b.'+HeaderMCond);
		// 5. Tenure ----------------------------------------------------------
			var txtInp_tenure = document.createElement('div');
				txtInp_tenure.setAttribute('class', 'descriptionbox');
				txtInp_tenure.className= 'descriptionbox'; //Required for IE
				txtInp_tenure.style.fontSize="10px";
				txtInp_tenure.style.border='0px';
				txtInp_tenure.innerHTML = '<a href="#" title="'+TTEditTenure+'">'+HeaderTenure+'</a>';
				txtInp_tenure.setAttribute('id', '.b.'+HeaderTenure);
		// 6. Assets ----------------------------------------------------------
			var txtInp_assets = document.createElement('div');
				txtInp_assets.setAttribute('class', 'descriptionbox');
				txtInp_assets.className= 'descriptionbox'; //Required for IE
				txtInp_assets.style.fontSize="10px";
				txtInp_assets.style.border='0px';
				txtInp_assets.innerHTML = '<a href="#" title="'+TTEditAsset+'">'+HeaderAsset+'</a>';
				txtInp_assets.setAttribute('id', '.b.'+HeaderAsset);
		// 7. Age_1 -----------------------------------------------------------
			var txtInp_age = document.createElement('div');
				txtInp_age.setAttribute('class', 'descriptionbox');
				txtInp_age.className= 'descriptionbox'; //Required for IE
				txtInp_age.style.fontSize="10px";
				txtInp_age.style.border='0px';
				txtInp_age.innerHTML = '<a href="#" title="'+TTEditAge+'">'+HeaderAge+'</a>';
				txtInp_age.setAttribute('id', '.b.'+HeaderAge);
		// 8. Race_1 ----------------------------------------------------------
			var txtInp_race = document.createElement('div');
				txtInp_race.setAttribute('class', 'descriptionbox');
				txtInp_race.className= 'descriptionbox'; //Required for IE
				txtInp_race.style.fontSize="10px";
				txtInp_race.style.border='0px';
				txtInp_race.innerHTML = '<a href="#" title="'+TTEditRace+'">'+HeaderRace+'</a>';
				txtInp_race.setAttribute('id', '.b.'+HeaderRace);
		// 9. Sex -------------------------------------------------------------
			var txtInp_gend = document.createElement('div');
				txtInp_gend.setAttribute('class', 'descriptionbox');
				txtInp_gend.className= 'descriptionbox'; //Required for IE
				txtInp_gend.style.fontSize="10px";
				txtInp_gend.style.border='0px';
				txtInp_gend.innerHTML = '<a href="#" title="'+TTEditSex+'">'+HeaderSex+'</a>';
				txtInp_gend.setAttribute('id', '.b.'+HeaderSex);
		// 10. Race_2 ----------------------------------------------------------
			var txtInp_race2 = document.createElement('div');
				txtInp_race2.setAttribute('class', 'descriptionbox');
				txtInp_race2.className= 'descriptionbox'; //Required for IE
				txtInp_race2.style.fontSize="10px";
				txtInp_race2.style.border='0px';
				txtInp_race2.innerHTML = '<a href="#" title="'+TTEditRace+'">'+HeaderRace+'</a>';
				txtInp_race2.setAttribute('id', '.b.'+HeaderRace);
		// 11. DOB/YOB ---------------------------------------------------------
			var txtInp_yob = document.createElement('div');
				txtInp_yob.setAttribute('class', 'descriptionbox');
				txtInp_yob.className= 'descriptionbox'; //Required for IE
				txtInp_yob.style.fontSize="10px";
				txtInp_yob.style.border='0px';
				txtInp_yob.innerHTML = '<a href="#" title="'+TTEditYOB+'">'+HeaderYOB+'</a>';
				txtInp_yob.setAttribute('id', '.b.'+HeaderYOB);
		// 12. Age_2 -----------------------------------------------------------
			var txtInp_age2 = document.createElement('div');
				txtInp_age2.setAttribute('class', 'descriptionbox');
				txtInp_age2.className= 'descriptionbox'; //Required for IE
				txtInp_age2.style.fontSize="10px";
				txtInp_age2.style.border='0px';
				txtInp_age2.innerHTML = '<a href="#" title="'+TTEditAge+'">'+HeaderAge+'</a>';
				txtInp_age2.setAttribute('id', '.b.'+HeaderAge);
		// 13. MthB (if born within census year) -------------------------------
			var txtInp_bmth = document.createElement('div');
				txtInp_bmth.setAttribute('class', 'descriptionbox');
				txtInp_bmth.className= 'descriptionbox'; //Required for IE
				txtInp_bmth.style.fontSize="10px";
				txtInp_bmth.style.border='0px';
				txtInp_bmth.innerHTML = '<a href="#" title="'+TTEditBmth+'">'+HeaderBmth+'</a>';
				txtInp_bmth.setAttribute('id', '.b.'+HeaderBmth);
		// 14. Relationship_2 --------------------------------------------------
			var txtInp_label2 = document.createElement('div');
				txtInp_label2.setAttribute('class', 'descriptionbox');
				txtInp_label2.className= 'descriptionbox'; //Required for IE
				txtInp_label2.style.fontSize="10px";
				txtInp_label2.style.border='0px';
				txtInp_label2.innerHTML = '<a href="#" title="'+TTEditRela+'">'+HeaderRela+'</a>';
				txtInp_label2.setAttribute('id', '.b.'+HeaderRela);
		// 15. Conditition_2 ---------------------------------------------------
			var txtInp_cond2 = document.createElement('div');
				txtInp_cond2.setAttribute('class', 'descriptionbox');
				txtInp_cond2.className= 'descriptionbox'; //Required for IE
				txtInp_cond2.style.fontSize="10px";
				txtInp_cond2.style.border='0px';
				txtInp_cond2.innerHTML = '<a href="#" title="'+TTEditMCond+'">'+HeaderMCond+'</a>';
				txtInp_cond2.setAttribute('id', '.b.'+HeaderMCond);
		// 16. Years Married ---------------------------------------------------
			var txtInp_yrsm = document.createElement('div');
				txtInp_yrsm.setAttribute('class', 'descriptionbox');
				txtInp_yrsm.className= 'descriptionbox'; //Required for IE
				txtInp_yrsm.style.fontSize="10px";
				txtInp_yrsm.style.border='0px';
				txtInp_yrsm.innerHTML = '<a href="#" title="'+TTEditYrsM+'">'+HeaderYrsM+'</a>';
				txtInp_yrsm.setAttribute('id', '.b.'+HeaderYrsM);
		// 17. Children Born Alive ---------------------------------------------
			var txtInp_chilB = document.createElement('div');
				txtInp_chilB.setAttribute('class', 'descriptionbox');
				txtInp_chilB.className= 'descriptionbox'; //Required for IE
				txtInp_chilB.style.fontSize="10px";
				txtInp_chilB.style.border='0px';
				txtInp_chilB.innerHTML = '<a href="#" title="'+TTEditChilB+'">'+HeaderChilB+'</a>';
				txtInp_chilB.setAttribute('id', '.b.'+HeaderChilB);
		// 18. Children Still Living -------------------------------------------
			var txtInp_chilL = document.createElement('div');
				txtInp_chilL.setAttribute('class', 'descriptionbox');
				txtInp_chilL.className= 'descriptionbox'; //Required for IE
				txtInp_chilL.style.fontSize="10px";
				txtInp_chilL.style.border='0px';
				txtInp_chilL.innerHTML = '<a href="#" title="'+TTEditChilL+'">'+HeaderChilL+'</a>';
				txtInp_chilL.setAttribute('id', '.b.'+HeaderChilL);
		// 19. Children who have Died ------------------------------------------
			var txtInp_chilD = document.createElement('div');
				txtInp_chilD.setAttribute('class', 'descriptionbox');
				txtInp_chilD.className= 'descriptionbox'; //Required for IE
				txtInp_chilD.style.fontSize="10px";
				txtInp_chilD.style.border='0px';
				txtInp_chilD.innerHTML = '<a href="#" title="'+TTEditChilD+'">'+HeaderChilD+'</a>';
				txtInp_chilD.setAttribute('id', '.b.'+HeaderChilD);
		// 20. Age at first Marriage -------------------------------------------
			var txtInp_ageM = document.createElement('div');
				txtInp_ageM.setAttribute('class', 'descriptionbox');
				txtInp_ageM.className= 'descriptionbox'; //Required for IE
				txtInp_ageM.style.fontSize="10px";
				txtInp_ageM.style.border='0px';
				txtInp_ageM.innerHTML = '<a href="#" title="'+TTEditAgeM+'">'+HeaderAgeM+'</a>';
				txtInp_ageM.setAttribute('id', '.b.'+HeaderAgeM);
		// 21. Occupation_1 ----------------------------------------------------
			var txtInp_occu = document.createElement('div');
				txtInp_occu.setAttribute('class', 'descriptionbox');
				txtInp_occu.className= 'descriptionbox'; //Required for IE
				txtInp_occu.style.fontSize="10px";
				txtInp_occu.style.border='0px';
				txtInp_occu.innerHTML = '<a href="#" title="'+TTEditOccu+'">'+HeaderOccu+'</a>';
				txtInp_occu.setAttribute('id', '.b.'+HeaderOccu);
		// 22. Assets_2 --------------------------------------------------------
			var txtInp_assets2 = document.createElement('div');
				txtInp_assets2.setAttribute('class', 'descriptionbox');
				txtInp_assets2.className= 'descriptionbox'; //Required for IE
				txtInp_assets2.style.fontSize="10px";
				txtInp_assets2.style.border='0px';
				txtInp_assets2.innerHTML = '<a href="#" title="'+TTEditAsset+'">'+HeaderAsset+'</a>';
				txtInp_assets2.setAttribute('id', '.b.'+HeaderAsset);
		// 23. Birth Place_1 -----------------------------------------------
			var txtInp_birthpl = document.createElement('div');
				txtInp_birthpl.setAttribute('class', 'descriptionbox');
				txtInp_birthpl.className= 'descriptionbox'; //Required for IE
				txtInp_birthpl.style.fontSize="10px";
				txtInp_birthpl.style.border='0px';
				txtInp_birthpl.innerHTML = '<a href="#" title="'+TTEditBplace+'">'+HeaderBplace+'</a>';
				txtInp_birthpl.setAttribute('id', '.b.'+HeaderBplace);
		// 24. Parentage -----------------------------------------------
			var txtInp_parent = document.createElement('div');
				txtInp_parent.setAttribute('class', 'descriptionbox');
				txtInp_parent.className= 'descriptionbox'; //Required for IE
				txtInp_parent.style.fontSize="10px";
				txtInp_parent.style.border='0px';
				txtInp_parent.innerHTML = '<a href="#" title="'+TTEditParent+'">'+HeaderParent+'</a>';
				txtInp_parent.setAttribute('id', '.b.'+HeaderParent);
		// 25. MthB_2 (if born within census year) -------------------------------
			var txtInp_bmth2 = document.createElement('div');
				txtInp_bmth2.setAttribute('class', 'descriptionbox');
				txtInp_bmth2.className= 'descriptionbox'; //Required for IE
				txtInp_bmth2.style.fontSize="10px";
				txtInp_bmth2.style.border='0px';
				txtInp_bmth2.innerHTML = '<a href="#" title="'+TTEditBmth+'">'+HeaderBmth+'</a>';
				txtInp_bmth2.setAttribute('id', '.b.'+HeaderBmth);
		// 26. MthM (if married within census year) -------------------------------
			var txtInp_mmth = document.createElement('div');
				txtInp_mmth.setAttribute('class', 'descriptionbox');
				txtInp_mmth.className= 'descriptionbox'; //Required for IE
				txtInp_mmth.style.fontSize="10px";
				txtInp_mmth.style.border='0px';
				txtInp_mmth.innerHTML = '<a href="#" title="'+TTEditMmth+'">'+HeaderMmth+'</a>';
				txtInp_mmth.setAttribute('id', '.b.'+HeaderMmth);
		// 27. Indi Birth Place_1 -----------------------------------------------
			var txtInp_ibirthpl = document.createElement('div');
				txtInp_ibirthpl.setAttribute('class', 'descriptionbox');
				txtInp_ibirthpl.className= 'descriptionbox'; //Required for IE
				txtInp_ibirthpl.style.fontSize="10px";
				txtInp_ibirthpl.style.border='0px';
				txtInp_ibirthpl.innerHTML = '<a href="#" title="'+TTEditBP+'">'+HeaderBP+'</a>';
				txtInp_ibirthpl.setAttribute('id', '.b.'+HeaderBP);
		// 28. Fathers Birth Place_1 ---------------------------------------------
			var txtInp_fbirthpl = document.createElement('div');
				txtInp_fbirthpl.setAttribute('class', 'descriptionbox');
				txtInp_fbirthpl.className= 'descriptionbox'; //Required for IE
				txtInp_fbirthpl.style.fontSize="10px";
				txtInp_fbirthpl.style.border='0px';
				txtInp_fbirthpl.innerHTML = '<a href="#" title="'+TTEditFBP+'">'+HeaderFBP+'</a>';
				txtInp_fbirthpl.setAttribute('id', '.b.'+HeaderFBP);
		// 29. Mothers Birth Place_1 ---------------------------------------------
			var txtInp_mbirthpl = document.createElement('div');
				txtInp_mbirthpl.setAttribute('class', 'descriptionbox');
				txtInp_mbirthpl.className= 'descriptionbox'; //Required for IE
				txtInp_mbirthpl.style.fontSize="10px";
				txtInp_mbirthpl.style.border='0px';
				txtInp_mbirthpl.innerHTML = '<a href="#" title="'+TTEditMBP+'">'+HeaderMBP+'</a>';
				txtInp_mbirthpl.setAttribute('id', '.b.'+HeaderMBP);
		// 30. Years in USA ----------------------------------------------------
			var txtInp_yrsUS = document.createElement('div');
				txtInp_yrsUS.setAttribute('class', 'descriptionbox');
				txtInp_yrsUS.className= 'descriptionbox'; //Required for IE
				txtInp_yrsUS.style.fontSize="10px";
				txtInp_yrsUS.style.border='0px';
				txtInp_yrsUS.innerHTML = '<a href="#" title="'+TTEditYrsUS+'">'+HeaderYrsUS+'</a>';
				txtInp_yrsUS.setAttribute('id', '.b.'+HeaderYrsUS);
		// 31. Year of immigration YOI_1 ----------------------------------------
			var txtInp_yoi1 = document.createElement('div');
				txtInp_yoi1.setAttribute('class', 'descriptionbox');
				txtInp_yoi1.className= 'descriptionbox'; //Required for IE
				txtInp_yoi1.style.fontSize="10px";
				txtInp_yoi1.style.border='0px';
				txtInp_yoi1.innerHTML = '<a href="#" title="'+TTEditYOI+'">'+HeaderYOI+'</a>';
				txtInp_yoi1.setAttribute('id', '.b.'+HeaderYOI);
		// 32. Natualized or Alien_1 ----------------------------------------
			var txtInp_na1 = document.createElement('div');
				txtInp_na1.setAttribute('class', 'descriptionbox');
				txtInp_na1.className= 'descriptionbox'; //Required for IE
				txtInp_na1.style.fontSize="10px";
				txtInp_na1.style.border='0px';
				txtInp_na1.innerHTML = '<a href="#" title="'+TTEditNA+'">'+HeaderNA+'</a>';
				txtInp_na1.setAttribute('id', '.b.'+HeaderNA);
		// 33. Year of naturalization YON_1 ----------------------------------------
			var txtInp_yon = document.createElement('div');
				txtInp_yon.setAttribute('class', 'descriptionbox');
				txtInp_yon.className= 'descriptionbox'; //Required for IE
				txtInp_yon.style.fontSize="10px";
				txtInp_yon.style.border='0px';
				txtInp_yon.innerHTML = '<a href="#" title="'+TTEditYON+'">'+HeaderYON+'</a>';
				txtInp_yon.setAttribute('id', '.b.'+HeaderYON);
		// 34. English if spoken, or if not, Language spoken Eng/Lang ---------------
			var txtInp_englang = document.createElement('div');
				txtInp_englang.setAttribute('class', 'descriptionbox');
				txtInp_englang.className= 'descriptionbox'; //Required for IE
				txtInp_englang.style.fontSize="10px";
				txtInp_englang.style.border='0px';
				txtInp_englang.innerHTML = '<a href="#" title="'+TTEditEngL+'">'+HeaderEngL+'</a>';
				txtInp_englang.setAttribute('id', '.b.'+HeaderEngL);
		// 35. Occupation_2 ---------------------------------------------------------
			var txtInp_occu2 = document.createElement('div');
				txtInp_occu2.setAttribute('class', 'descriptionbox');
				txtInp_occu2.className= 'descriptionbox'; //Required for IE
				txtInp_occu2.style.fontSize="10px";
				txtInp_occu2.style.border='0px';
				txtInp_occu2.innerHTML = '<a href="#" title="'+TTEditOccu+'">'+HeaderOccu+'</a>';
				txtInp_occu2.setAttribute('id', '.b.'+HeaderOccu);
		// 36. Health --------------------------------------------------------------
			var txtInp_health = document.createElement('div');
				txtInp_health.setAttribute('class', 'descriptionbox');
				txtInp_health.className= 'descriptionbox'; //Required for IE
				txtInp_health.style.fontSize="10px";
				txtInp_health.style.border='0px';
				txtInp_health.innerHTML = '<a href="#" title="'+TTEditHealth+'">'+HeaderHealth+'</a>';
				txtInp_health.setAttribute('id', '.b.'+HeaderHealth);
		// 37. Industry ind_1 ------------------------------------------------------
			var txtInp_ind1 = document.createElement('div');
				txtInp_ind1.setAttribute('class', 'descriptionbox');
				txtInp_ind1.className= 'descriptionbox'; //Required for IE
				txtInp_ind1.style.fontSize="10px";
				txtInp_ind1.style.border='0px';
				txtInp_ind1.innerHTML = '<a href="#" title="'+TTEditInd+'">'+HeaderInd+'</a>';
				txtInp_ind1.setAttribute('id', '.b.'+HeaderInd);
		// 38. Employ_1 ------------------------------------------------------------
			var txtInp_emp1 = document.createElement('div');
				txtInp_emp1.setAttribute('class', 'descriptionbox');
				txtInp_emp1.className= 'descriptionbox'; //Required for IE
				txtInp_emp1.style.fontSize="10px";
				txtInp_emp1.style.border='0px';
				txtInp_emp1.innerHTML = '<a href="#" title="'+TTEditEmp+'">'+HeaderEmp+'</a>';
				txtInp_emp1.setAttribute('id', '.b.'+HeaderEmp);
		// 39. Employer - EmR-----------------------------------------------------------
			var txtInp_emR = document.createElement('div');
				txtInp_emR.setAttribute('class', 'descriptionbox');
				txtInp_emR.className= 'descriptionbox'; //Required for IE
				txtInp_emR.style.fontSize="10px";
				txtInp_emR.style.border='0px';
				txtInp_emR.innerHTML = '<a href="#" title="'+TTEditEmR+'">'+HeaderEmR+'</a>';
				txtInp_emR.setAttribute('id', '.b.'+HeaderEmR);
		// 40. Employed EmD ------------------------------------------------------------
			var txtInp_emD = document.createElement('div');
				txtInp_emD.setAttribute('class', 'descriptionbox');
				txtInp_emD.className= 'descriptionbox'; //Required for IE
				txtInp_emD.style.fontSize="10px";
				txtInp_emD.style.border='0px';
				txtInp_emD.innerHTML = '<a href="#" title="'+TTEditEmD+'">'+HeaderEmD+'</a>';
				txtInp_emD.setAttribute('id', '.b.'+HeaderEmD);
		// 41. Months employed during Census Year ---------------------------------------
			var txtInp_mnsE = document.createElement('div');
				txtInp_mnsE.setAttribute('class', 'descriptionbox');
				txtInp_mnsE.className= 'descriptionbox'; //Required for IE
				txtInp_mnsE.style.fontSize="10px";
				txtInp_mnsE.style.border='0px';
				txtInp_mnsE.innerHTML = '<a href="#" title="'+TTEditMnse+'">'+HeaderMnse+'</a>';
				txtInp_mnsE.setAttribute('id', '.b.'+HeaderMnse);
		// 42. Working at Home WH ----------------------------------------------------
			var txtInp_emH = document.createElement('div');
				txtInp_emH.setAttribute('class', 'descriptionbox');
				txtInp_emH.className= 'descriptionbox'; //Required for IE
				txtInp_emH.style.fontSize="10px";
				txtInp_emH.style.border='0px';
				txtInp_emH.innerHTML = '<a href="#" title="'+TTEditEmH+'">'+HeaderEmH+'</a>';
				txtInp_emH.setAttribute('id', '.b.'+HeaderEmH);
		// 43. Not Employed EmN --------------------------------------------------------
			var txtInp_emN = document.createElement('div');
				txtInp_emN.setAttribute('class', 'descriptionbox');
				txtInp_emN.className= 'descriptionbox'; //Required for IE
				txtInp_emN.style.fontSize="10px";
				txtInp_emN.style.border='0px';
				txtInp_emN.innerHTML = '<a href="#" title="'+TTEditEmN+'">'+HeaderEmN+'</a>';
				txtInp_emN.setAttribute('id', '.b.'+HeaderEmN);
		// 44. Weeks unemployed during Census Year ---------------------------------------
			var txtInp_wksU = document.createElement('div');
				txtInp_wksU.setAttribute('class', 'descriptionbox');
				txtInp_wksU.className= 'descriptionbox'; //Required for IE
				txtInp_wksU.style.fontSize="10px";
				txtInp_wksU.style.border='0px';
				txtInp_wksU.innerHTML = '<a href="#" title="'+TTEditWksu+'">'+HeaderWksu+'</a>';
				txtInp_wksU.setAttribute('id', '.b.'+HeaderWksu);
		// 45. Months unemployed during Census Year ---------------------------------------
			var txtInp_mnsU = document.createElement('div');
				txtInp_mnsU.setAttribute('class', 'descriptionbox');
				txtInp_mnsU.className= 'descriptionbox'; //Required for IE
				txtInp_mnsU.style.fontSize="10px";
				txtInp_mnsU.style.border='0px';
				txtInp_mnsU.innerHTML = '<a href="#" title="'+TTEditMnsu+'">'+HeaderMnsu+'</a>';
				txtInp_mnsU.setAttribute('id', '.b.'+HeaderMnsu);
		// 46. Education -----------------------------------------------------------
			var txtInp_educ = document.createElement('div');
				txtInp_educ.setAttribute('class', 'descriptionbox');
				txtInp_educ.className= 'descriptionbox'; //Required for IE
				txtInp_educ.style.fontSize="10px";
				txtInp_educ.style.border='0px';
				txtInp_educ.innerHTML = '<a href="#" title="'+TTEditEduc+'">'+HeaderEduc+'</a>';
				txtInp_educ.setAttribute('id', '.b.'+HeaderEduc);
		// 47. Education pre 1890 Census ---------------------------------------------
			var txtInp_educpre1890 = document.createElement('div');
				txtInp_educpre1890.setAttribute('class', 'descriptionbox');
				txtInp_educpre1890.className= 'descriptionbox'; //Required for IE
				txtInp_educpre1890.style.fontSize="10px";
				txtInp_educpre1890.style.border='0px';
				txtInp_educpre1890.innerHTML = '<a href="#" title="'+TTEditEducpre1890+'">'+HeaderEducpre1890+'</a>';
				txtInp_educpre1890.setAttribute('id', '.b.'+HeaderEducpre1890);
		// 48. English Spoken y/n eng_1 ----------------------------------------
			var txtInp_eng1 = document.createElement('div');
				txtInp_eng1.setAttribute('class', 'descriptionbox');
				txtInp_eng1.className= 'descriptionbox'; //Required for IE
				txtInp_eng1.style.fontSize="10px";
				txtInp_eng1.style.border='0px';
				txtInp_eng1.innerHTML = '<a href="#" title="'+TTEditEng+'">'+HeaderEng+'</a>';
				txtInp_eng1.setAttribute('id', '.b.'+HeaderEng);
		// 49. Home Ownership  -------------------------------------------------
			var txtInp_home = document.createElement('div');
				txtInp_home.setAttribute('class', 'descriptionbox');
				txtInp_home.className= 'descriptionbox'; //Required for IE
				txtInp_home.style.fontSize="10px";
				txtInp_home.style.border='0px';
				txtInp_home.innerHTML = '<a href="#" title="'+TTEditHome+'">'+HeaderHome+'</a>';
				txtInp_home.setAttribute('id', '.b.'+HeaderHome);
		// 50. Birth Place_2 -----------------------------------------------
			var txtInp_birthpl2 = document.createElement('div');
				txtInp_birthpl2.setAttribute('class', 'descriptionbox');
				txtInp_birthpl2.className= 'descriptionbox'; //Required for IE
				txtInp_birthpl2.style.fontSize="10px";
				txtInp_birthpl2.style.border='0px';
				txtInp_birthpl2.innerHTML = '<a href="#" title="'+TTEditBplace+'">'+HeaderBplace+'</a>';
				txtInp_birthpl2.setAttribute('id', '.b.'+HeaderBplace);
		// 51. Indi Birth Place_2 ---------------------------------------------
			var txtInp_ibirthpl2 = document.createElement('div');
				txtInp_ibirthpl2.setAttribute('class', 'descriptionbox');
				txtInp_ibirthpl2.className= 'descriptionbox'; //Required for IE
				txtInp_ibirthpl2.style.fontSize="10px";
				txtInp_ibirthpl2.style.border='0px';
				txtInp_ibirthpl2.innerHTML = '<a href="#" title="'+TTEditBP+'">'+HeaderBP+'</a>';
				txtInp_ibirthpl2.setAttribute('id', '.b.'+HeaderBP);
		// 52. Born in Same Country (ENG) -----------------------------------------------
			var txtInp_bic = document.createElement('div');
				txtInp_bic.setAttribute('class', 'descriptionbox');
				txtInp_bic.className= 'descriptionbox'; //Required for IE
				txtInp_bic.style.fontSize="10px";
				txtInp_bic.style.border='0px';
				txtInp_bic.innerHTML = '<a href="#" title="'+TTEditBIC+'">'+HeaderBIC+'</a>';
				txtInp_bic.setAttribute('id', '.b.'+HeaderBIC);
		// 53. Born outside England (SCO, IRE, WAL, FOReign ----------------------------
			var txtInp_boe = document.createElement('div');
				txtInp_boe.setAttribute('class', 'descriptionbox');
				txtInp_boe.className= 'descriptionbox'; //Required for IE
				txtInp_boe.style.fontSize="10px";
				txtInp_boe.style.border='0px';
				txtInp_boe.innerHTML = '<a href="#" title="'+TTEditBOE+'">'+HeaderBOE+'</a>';
				txtInp_boe.setAttribute('id', '.b.'+HeaderBOE);
		// 54. Fathers Birth Place_2 ---------------------------------------------
			var txtInp_fbirthpl2 = document.createElement('div');
				txtInp_fbirthpl2.setAttribute('class', 'descriptionbox');
				txtInp_fbirthpl2.className= 'descriptionbox'; //Required for IE
				txtInp_fbirthpl2.style.fontSize="10px";
				txtInp_fbirthpl2.style.border='0px';
				txtInp_fbirthpl2.innerHTML = '<a href="#" title="'+TTEditFBP+'">'+HeaderFBP+'</a>';
				txtInp_fbirthpl2.setAttribute('id', '.b.'+HeaderFBP);
		// 55. Mothers Birth Place_2 ---------------------------------------------
			var txtInp_mbirthpl2 = document.createElement('div');
				txtInp_mbirthpl2.setAttribute('class', 'descriptionbox');
				txtInp_mbirthpl2.className= 'descriptionbox'; //Required for IE
				txtInp_mbirthpl2.style.fontSize="10px";
				txtInp_mbirthpl2.style.border='0px';
				txtInp_mbirthpl2.innerHTML = '<a href="#" title="'+TTEditMBP+'">'+HeaderMBP+'</a>';
				txtInp_mbirthpl2.setAttribute('id', '.b.'+HeaderMBP);
		// 56. Native Language ----------------------------------------------------
			var txtInp_lang = document.createElement('div');
				txtInp_lang.setAttribute('class', 'descriptionbox');
				txtInp_lang.className= 'descriptionbox'; //Required for IE
				txtInp_lang.style.fontSize="10px";
				txtInp_lang.style.border='0px';
				txtInp_lang.innerHTML = '<a href="#" title="'+TTEditNL+'">'+HeaderNL+'</a>';
				txtInp_lang.setAttribute('id', '.b.'+HeaderNL);
		// 57. Year of immigration YOI_2 ----------------------------------------
			var txtInp_yoi2 = document.createElement('div');
				txtInp_yoi2.setAttribute('class', 'descriptionbox');
				txtInp_yoi2.className= 'descriptionbox'; //Required for IE
				txtInp_yoi2.style.fontSize="10px";
				txtInp_yoi2.style.border='0px';
				txtInp_yoi2.innerHTML = '<a href="#" title="'+TTEditYOI+'">'+HeaderYOI+'</a>';
				txtInp_yoi2.setAttribute('id', '.b.'+HeaderYOI);
		// 58. Natualized or Alien_2 ----------------------------------------
			var txtInp_na2 = document.createElement('div');
				txtInp_na2.setAttribute('class', 'descriptionbox');
				txtInp_na2.className= 'descriptionbox'; //Required for IE
				txtInp_na2.style.fontSize="10px";
				txtInp_na2.style.border='0px';
				txtInp_na2.innerHTML = '<a href="#" title="'+TTEditNA+'">'+HeaderNA+'</a>';
				txtInp_na2.setAttribute('id', '.b.'+HeaderNA);
		// 59. English Spoken y/n eng_2 ----------------------------------------
			var txtInp_eng2 = document.createElement('div');
				txtInp_eng2.setAttribute('class', 'descriptionbox');
				txtInp_eng2.className= 'descriptionbox'; //Required for IE
				txtInp_eng2.style.fontSize="10px";
				txtInp_eng2.style.border='0px';
				txtInp_eng2.innerHTML = '<a href="#" title="'+TTEditEng+'">'+HeaderEng+'</a>';
				txtInp_eng2.setAttribute('id', '.b.'+HeaderEng);
		// 60. Occupation_3 -----------------------------------------------------
			var txtInp_occu3 = document.createElement('div');
				txtInp_occu3.setAttribute('class', 'descriptionbox');
				txtInp_occu3.className= 'descriptionbox'; //Required for IE
				txtInp_occu3.style.fontSize="10px";
				txtInp_occu3.style.border='0px';
				txtInp_occu3.innerHTML = '<a href="#" title="'+TTEditOccu+'">'+HeaderOccu+'</a>';
				txtInp_occu3.setAttribute('id', '.b.'+HeaderOccu);
		// 61. Industry ind_2 ------------------------------------------------------
			var txtInp_ind2 = document.createElement('div');
				txtInp_ind2.setAttribute('class', 'descriptionbox');
				txtInp_ind2.className= 'descriptionbox'; //Required for IE
				txtInp_ind2.style.fontSize="10px";
				txtInp_ind2.style.border='0px';
				txtInp_ind2.innerHTML = '<a href="#" title="'+TTEditInd+'">'+HeaderInd+'</a>';
				txtInp_ind2.setAttribute('id', '.b.'+HeaderInd);
		// 62. Employ_2 ------------------------------------------------------------
			var txtInp_emp2 = document.createElement('div');
				txtInp_emp2.setAttribute('class', 'descriptionbox');
				txtInp_emp2.className= 'descriptionbox'; //Required for IE
				txtInp_emp2.style.fontSize="10px";
				txtInp_emp2.style.border='0px';
				txtInp_emp2.innerHTML = '<a href="#" title="'+TTEditEmp+'">'+HeaderEmp+'</a>';
				txtInp_emp2.setAttribute('id', '.b.'+HeaderEmp);
		/*
		// 63. Nationality (UK 1911) ----------------------------------------------------
			var txtInp_infirm = document.createElement('div');
				txtInp_infirm.setAttribute('class', 'descriptionbox');
				txtInp_infirm.className= 'descriptionbox'; //Required for IE
				txtInp_infirm.style.fontSize="10px";
				txtInp_infirm.style.border='0px';
				txtInp_infirm.innerHTML = '<a href="#" title="'+TTEditNality+'">'+HeaderN-ality+'</a>';
				txtInp_infirm.setAttribute('id', '.b.'+HeaderN-ality);
		*/
		// 63. Infirmaties Infirm -------------------------------------------------------
			var txtInp_infirm = document.createElement('div');
				txtInp_infirm.setAttribute('class', 'descriptionbox');
				txtInp_infirm.className= 'descriptionbox'; //Required for IE
				txtInp_infirm.style.fontSize="10px";
				txtInp_infirm.style.border='0px';
				txtInp_infirm.innerHTML = '<a href="#" title="'+TTEditInfirm+'">'+HeaderInfirm+'</a>';
				txtInp_infirm.setAttribute('id', '.b.'+HeaderInfirm);
		// 64. Situation (1890)  ------------------------------------------------------
			var txtInp_situ = document.createElement('div');
				txtInp_situ.setAttribute('class', 'descriptionbox');
				txtInp_situ.className= 'descriptionbox'; //Required for IE
				txtInp_situ.style.fontSize="10px";
				txtInp_situ.style.border='0px';
				txtInp_situ.innerHTML = '<a href="#" title="'+TTEditSitu+'">'+HeaderSitu+'</a>';
				txtInp_situ.setAttribute('id', '.b.'+HeaderSitu);
		// 65. Veteran  ------------------------------------------------------
			var txtInp_vet = document.createElement('div');
				txtInp_vet.setAttribute('class', 'descriptionbox');
				txtInp_vet.className= 'descriptionbox'; //Required for IE
				txtInp_vet.style.fontSize="10px";
				txtInp_vet.style.border='0px';
				txtInp_vet.innerHTML = '<a href="#" title="'+TTEditVet+'">'+HeaderVet+'</a>';
				txtInp_vet.setAttribute('id', '.b.'+HeaderVet);
		// 66. War or Expedition ---------------------------------------------
			var txtInp_war = document.createElement('div');
				txtInp_war.setAttribute('class', 'descriptionbox');
				txtInp_war.className= 'descriptionbox'; //Required for IE
				txtInp_war.style.fontSize="10px";
				txtInp_war.style.border='0px';
				txtInp_war.innerHTML = '<a href="#" title="'+TTEditWar+'">'+HeaderWar+'</a>';
				txtInp_war.setAttribute('id', '.b.'+HeaderWar);
		// 67. Infirm1910 (1910) -----------------------------------------------
			var txtInp_infirm1910 = document.createElement('div');
				txtInp_infirm1910.setAttribute('class', 'descriptionbox');
				txtInp_infirm1910.className= 'descriptionbox'; //Required for IE
				txtInp_infirm1910.style.fontSize="10px";
				txtInp_infirm1910.style.border='0px';
				txtInp_infirm1910.innerHTML = '<a href="#" title="'+TTEditInfirm1910+'">'+HeaderInfirm1910+'</a>';
				txtInp_infirm1910.setAttribute('id', '.b.'+HeaderInfirm1910);

		// Hidden Items ------------------------------------------------------
		// 68. DOB date of Birth (Julian) actual or rough (ABT, BET)
		// 69. DOM date of Marriage (Julian) actual or rough (ABT, BET)
		// 70. Fullname
		// 71. Married Name
		// 72. DOD date of Death (Julian) actual or rough (ABT, BET)
		// 73. Textual Array of Chil (Nam DOB DOD) for ChilB, ChilL, ChilD
		//

		// 74. Extra 1. Text Del Button -------------------------------------------------
			var txtInp_tdel = document.createElement('div');
				txtInp_tdel.setAttribute('class', 'descriptionbox');
				txtInp_tdel.className= 'descriptionbox'; //Required for IE
				txtInp_tdel.style.fontSize="10px";
				txtInp_tdel.style.border='0px';
				txtInp_tdel.innerHTML = 'Del';
				txtInp_tdel.setAttribute('id', this);
		// 75. Extra 2. Text Radio Button -----------------------------------------------
			var txtInp_tra = document.createElement('div');
				txtInp_tra.setAttribute('class', 'descriptionbox');
				txtInp_tra.className= 'descriptionbox'; //Required for IE
				txtInp_tra.style.fontSize="10px";
				txtInp_tra.style.border='0px';
				txtInp_tra.innerHTML = 'Ins';
		// 76. Extra 3. Item Number 2 -------------------------------------------------
			var txt_itemNo2 = document.createElement('div');
				txt_itemNo2.setAttribute('class', 'descriptionbox');
				txt_itemNo2.className= 'descriptionbox'; //Required for IE
				txt_itemNo2.style.border='0px';
				txt_itemNo2.innerHTML = '#';
				txt_itemNo2.setAttribute('id', '.b.Item2');
				txt_itemNo2.style.fontSize="10px";

		} else {

		// **D** Define Cell Elements =======================================
			var txtcolor = "#0000FF";
		// 0. Item Number ---------------------------------------------------
			// var txt_itemNo = document.createTextNode(iteration);
			var txt_itemNo = document.createElement('div');
			// txt_itemNo.style.display="none";
		// 1. Indi ID -------------------------------------------------------
				if (pid=='') {
					var txtcolor = "#000000";
					// This adds a checkbox for adding an indi id  .... to be implemented later
						var txtInp_pid = document.createElement('input');
						txtInp_pid.setAttribute('type', 'checkbox');
						if (txtInp_pid.checked!='') {
							txtInp_pid.setAttribute('value', 'no');
						} else {
							txtInp_pid.setAttribute('value', 'add');
						}
					// -------------------------------------------------------------------------
					txtInp_pid.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_1');
					txtInp_pid.setAttribute('size', '1');
					txtInp_pid.style.fontSize="11px";
				} else {
					var txtInp_pid = document.createElement('input');
						//txtInp_pid.style.border='0px';
						txtInp_pid.style.background='#bbddff';
						// txtInp_pid.style.display='none'
						txtInp_pid.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_1');
						txtInp_pid.setAttribute('value', pid);
						txtInp_pid.setAttribute('size', '4');
						txtInp_pid.setAttribute('readOnly','true');
						txtInp_pid.style.fontSize="10px";
				}
		// 2. Full Name -----------------------------------------------------
			var txtInp_nam = document.createElement('input');
				txtInp_nam.setAttribute('type', 'text');
				txtInp_nam.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_2');
				txtInp_nam.setAttribute('size', '30');
				txtInp_nam.setAttribute('value', nam);
				txtInp_nam.style.color=txtcolor;
				txtInp_nam.style.fontSize="10px";
				txtInp_nam.style.width="14em";
		// 3. Relationship_1 --------------------------------------------------
			var txtInp_label = document.createElement('input');
				txtInp_label.setAttribute('type', 'text');
				txtInp_label.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_3');
				txtInp_label.setAttribute('size', '15');
				txtInp_label.setAttribute('value', label);
				txtInp_label.style.color=txtcolor;
				txtInp_label.style.fontSize="10px";
				txtInp_label.style.width="7em";
		// 4. Conditition_1 ---------------------------------------------------
			var txtInp_cond = document.createElement('input');
				txtInp_cond.setAttribute('type', 'text');
				txtInp_cond.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_4');
				txtInp_cond.setAttribute('size', '1');
				txtInp_cond.setAttribute('value', cond);
				txtInp_cond.style.color=txtcolor;
				txtInp_cond.style.fontSize="10px";
				txtInp_cond.style.width="1em";
		// 5. Tenure ----------------------------------------------------------
			var txtInp_tenure = document.createElement('input');
				txtInp_tenure.setAttribute('type', 'text');
				txtInp_tenure.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_5');
				txtInp_tenure.setAttribute('size', '1');
				txtInp_tenure.setAttribute('maxlength', '2');
				txtInp_tenure.setAttribute('value', '');
				txtInp_tenure.style.color=txtcolor;
				txtInp_tenure.style.fontSize="10px";
				txtInp_tenure.style.width="1.6em";
		// 6. Assets_1 --------------------------------------------------------
			var txtInp_assets = document.createElement('input');
				txtInp_assets.setAttribute('type', 'text');
				txtInp_assets.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_6');
				txtInp_assets.setAttribute('size', '11');
				txtInp_assets.setAttribute('maxlength', '15');
				txtInp_assets.setAttribute('value', '');
				txtInp_assets.style.color=txtcolor;
				txtInp_assets.style.fontSize="10px";
		// 7. Age_1 -----------------------------------------------------------
			var txtInp_age = document.createElement('input');
				txtInp_age.setAttribute('type', 'text');
				txtInp_age.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_7');
				txtInp_age.setAttribute('size', '2');
				txtInp_age.setAttribute('maxlength', '4');
				txtInp_age.setAttribute('value', age);
				if (txtInp_age.value>=0) {
					txtInp_age.style.color=txtcolor;
				} else {
					//txtInp_age.style.color="red";
					txtInp_age.style.color=txtcolor;
				}
				txtInp_age.style.fontSize="10px";
				txtInp_age.style.width="2.2em";
		// 8. Race_1 -----------------------------------------------------------
			var txtInp_race = document.createElement('input');
				txtInp_race.setAttribute('type', 'text');
				txtInp_race.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_8');
				txtInp_race.setAttribute('size', '1');
				txtInp_race.setAttribute('maxlength', '1');
				txtInp_race.setAttribute('value', '');
				txtInp_race.style.color=txtcolor;
				txtInp_race.style.fontSize="10px";
				txtInp_race.style.width="1em";
		// 9. Sex -----------------------------------------------------------
			var txtInp_gend = document.createElement('input');
				txtInp_gend.setAttribute('type', 'text');
				txtInp_gend.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_9');
				txtInp_gend.setAttribute('size', '1');
				txtInp_gend.setAttribute('maxlength', '1');
				txtInp_gend.setAttribute('value', gend);
				txtInp_gend.style.color=txtcolor;
				txtInp_gend.style.fontSize="10px";
				txtInp_gend.style.width="1em";
		// 10. Race_2 -----------------------------------------------------------
			var txtInp_race2 = document.createElement('input');
				txtInp_race2.setAttribute('type', 'text');
				txtInp_race2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_10');
				txtInp_race2.setAttribute('size', '1');
				txtInp_race2.setAttribute('maxlength', '1');
				txtInp_race2.setAttribute('value', '');
				txtInp_race2.style.color=txtcolor;
				txtInp_race2.style.fontSize="10px";
				txtInp_race2.style.width="1em";
		// 11. DOB/YOB ---------------------------------------------------------
			var txtInp_yob = document.createElement('input');
				txtInp_yob.setAttribute('type', 'text');
				txtInp_yob.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_11');
				txtInp_yob.setAttribute('size', '3');
				txtInp_yob.setAttribute('maxlength', '8');
				txtInp_yob.setAttribute('value', usdob);
				txtInp_yob.style.color=txtcolor;
				txtInp_yob.style.fontSize="10px";
				txtInp_yob.style.width="5em";
		// 12. Age_2 -----------------------------------------------------------
			var txtInp_age2 = document.createElement('input');
				txtInp_age2.setAttribute('type', 'text');
				txtInp_age2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_12');
				txtInp_age2.setAttribute('size', '2');
				txtInp_age2.setAttribute('maxlength', '4');
				txtInp_age2.setAttribute('value', age);
				if (txtInp_age2.value>=0) {
					txtInp_age2.style.color=txtcolor;
				} else {
					// txtInp_age2.style.color="red";
					txtInp_age2.style.color=txtcolor;
				}
				txtInp_age2.style.fontSize="10px";
				txtInp_age2.style.width="2.0em";
		// 13. Birth month if born in Census Year ------------------------------
			var txtInp_bmth = document.createElement('input');
				txtInp_bmth.setAttribute('type', 'text');
				txtInp_bmth.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_13');
				txtInp_bmth.setAttribute('size', '1');
				txtInp_bmth.setAttribute('maxlength', '3');
				txtInp_bmth.setAttribute('value', '');
				txtInp_bmth.style.color=txtcolor;
				txtInp_bmth.style.fontSize="10px";
				txtInp_bmth.style.width="2.4em";
		// 14. Relationship_2 --------------------------------------------------
			var txtInp_label2 = document.createElement('input');
				txtInp_label2.setAttribute('type', 'text');
				txtInp_label2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_14');
				txtInp_label2.setAttribute('size', '15');
				txtInp_label2.setAttribute('value', label);
				txtInp_label2.style.color=txtcolor;
				txtInp_label2.style.fontSize="10px";
		// 15. Conditition_2 ---------------------------------------------------
			var txtInp_cond2 = document.createElement('input');
				txtInp_cond2.setAttribute('type', 'text');
				txtInp_cond2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_15');
				txtInp_cond2.setAttribute('size', '1');
				txtInp_cond2.setAttribute('maxlength', '1');
				txtInp_cond2.setAttribute('value', cond);
				txtInp_cond2.style.color=txtcolor;
				txtInp_cond2.style.fontSize="10px";
				txtInp_cond2.style.width="1em";
		// 16. Years Married (or Yes if married in Census Year) ----------------
			var txtInp_yrsm = document.createElement('input');
				txtInp_yrsm.setAttribute('type', 'text');
				txtInp_yrsm.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_16');
				txtInp_yrsm.setAttribute('size', '1');
				txtInp_yrsm.setAttribute('maxlength', '2');
				txtInp_yrsm.setAttribute('value', '');
				txtInp_yrsm.style.color=txtcolor;
				txtInp_yrsm.style.fontSize="10px";
				txtInp_yrsm.style.width="1.4em";
		// 17. Children Born Alive --------------------------------------------
			var txtInp_chilB = document.createElement('input');
				txtInp_chilB.setAttribute('type', 'text');
				txtInp_chilB.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_17');
				txtInp_chilB.setAttribute('size', '1');
				txtInp_chilB.setAttribute('maxlength', '2');
				txtInp_chilB.setAttribute('value', BORN);
				txtInp_chilB.style.color=txtcolor;
				txtInp_chilB.style.fontSize="10px";
				txtInp_chilB.style.width="1.4em";
				if (gend=='M') {
					txtInp_chilB.type = "hidden";
				}
		// 18. Children Still Living ------------------------------------------
			var txtInp_chilL = document.createElement('input');
				txtInp_chilL.setAttribute('type', 'text');
				txtInp_chilL.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_18');
				txtInp_chilL.setAttribute('size', '1');
				txtInp_chilL.setAttribute('maxlength', '2');
				txtInp_chilL.setAttribute('value', ALIVE);
				txtInp_chilL.style.color=txtcolor;
				txtInp_chilL.style.fontSize="10px";
				txtInp_chilL.style.width="1.4em";
				if (gend=='M') {
					txtInp_chilL.type = "hidden";
				}
		// 19. Children who have Died ==---------------------------------------
			var txtInp_chilD = document.createElement('input');
				txtInp_chilD.setAttribute('type', 'text');
				txtInp_chilD.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_19');
				txtInp_chilD.setAttribute('size', '1');
				txtInp_chilD.setAttribute('maxlength', '2');
				txtInp_chilD.setAttribute('value', DEAD);
				txtInp_chilD.style.color=txtcolor;
				txtInp_chilD.style.fontSize="10px";
				txtInp_chilD.style.width="1.4em";
				if (gend=='M') {
					txtInp_chilD.type = "hidden";
				}
		// 20. Age at first marriage -------------------------------------------
			var txtInp_ageM = document.createElement('input');
				txtInp_ageM.setAttribute('type', 'text');
				txtInp_ageM.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_20');
				txtInp_ageM.setAttribute('size', '1');
				txtInp_ageM.setAttribute('maxlength', '2');
				txtInp_ageM.setAttribute('value', agemarr);
				txtInp_ageM.style.color=txtcolor;
				txtInp_ageM.style.fontSize="10px";
				txtInp_ageM.style.width="1.4em";
		// 21. Occupation_1 ----------------------------------------------------
			var txtInp_occu = document.createElement('input');
				txtInp_occu.setAttribute('type', 'text');
				txtInp_occu.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_21');
				txtInp_occu.setAttribute('size', '22');
				txtInp_occu.setAttribute('value', '');
				txtInp_occu.style.color=txtcolor;
				txtInp_occu.style.fontSize="10px";
				txtInp_occu.style.width="11em";
		// 22. Assets_2 -------------------------------------------
			var txtInp_assets2 = document.createElement('input');
				txtInp_assets2.setAttribute('type', 'text');
				txtInp_assets2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_22');
				txtInp_assets2.setAttribute('size', '7');
				txtInp_assets2.setAttribute('maxlength', '9');
				txtInp_assets2.setAttribute('value', '');
				txtInp_assets2.style.color=txtcolor;
				txtInp_assets2.style.fontSize="10px";
		// 23. Birth Place_1 (Full format) ---------------------------------------
			var txtInp_birthpl = document.createElement('input');
				txtInp_birthpl.setAttribute('type', 'text');
				txtInp_birthpl.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_23');
				txtInp_birthpl.setAttribute('size', '25');
				txtInp_birthpl.setAttribute('value', birthpl);
				txtInp_birthpl.style.color=txtcolor;
				txtInp_birthpl.style.fontSize="10px";
				txtInp_birthpl.style.width="13em";
		// 24. Parentage - x-x = Father foreign born Y/N and Mother foreign born Y/N --
			var txtInp_parent = document.createElement('input');
				txtInp_parent.setAttribute('type', 'text');
				txtInp_parent.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_24');
				txtInp_parent.setAttribute('size', '1');
				txtInp_parent.setAttribute('maxlength', '3');
				txtInp_parent.setAttribute('value', '');
				txtInp_parent.style.color=txtcolor;
				txtInp_parent.style.fontSize="10px";
				txtInp_parent.style.width="2em";
		// 25. Birth month Bmth_2) (if born in Census Year) ----------------------
			var txtInp_bmth2 = document.createElement('input');
				txtInp_bmth2.setAttribute('type', 'text');
				txtInp_bmth2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_25');
				txtInp_bmth2.setAttribute('size', '1');
				txtInp_bmth2.setAttribute('maxlength', '3');
				txtInp_bmth2.setAttribute('value', '');
				txtInp_bmth2.style.color=txtcolor;
				txtInp_bmth2.style.fontSize="10px";
				txtInp_bmth2.style.width="2.4em";
		// 26. Married month if married in Census Year ---------------------------
			var txtInp_mmth = document.createElement('input');
				txtInp_mmth.setAttribute('type', 'text');
				txtInp_mmth.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_26');
				txtInp_mmth.setAttribute('size', '1');
				txtInp_mmth.setAttribute('maxlength', '3');
				txtInp_mmth.setAttribute('value', '');
				txtInp_mmth.style.color=txtcolor;
				txtInp_mmth.style.fontSize="10px";
				txtInp_mmth.style.width="2.4em";
		// 27. POB_1 Indi Birth Place_1 (Chapman format) ------------------------
			var txtInp_ibirthpl = document.createElement('input');
				txtInp_ibirthpl.setAttribute('type', 'text');
				txtInp_ibirthpl.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_27');
				txtInp_ibirthpl.setAttribute('size', '1');
				txtInp_ibirthpl.setAttribute('maxlength', '3');
				txtInp_ibirthpl.setAttribute('value', ibirthpl);
				txtInp_ibirthpl.style.color=txtcolor;
				txtInp_ibirthpl.style.fontSize="10px";
				txtInp_ibirthpl.style.width="2.4em";
		// 28. FPOB_1 Fathers Birth Place_1 (Chapman format) ---------------------
			var txtInp_fbirthpl = document.createElement('input');
				txtInp_fbirthpl.setAttribute('type', 'text');
				txtInp_fbirthpl.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_28');
				txtInp_fbirthpl.setAttribute('size', '1');
				txtInp_fbirthpl.setAttribute('maxlength', '3');
				txtInp_fbirthpl.setAttribute('value', fbirthpl);
				txtInp_fbirthpl.style.color=txtcolor;
				txtInp_fbirthpl.style.fontSize="10px";
				txtInp_fbirthpl.style.width="2.4em";
		// 29. FPOB_1 Mothers Birth Place_1 (Chapman format )---------------------
			var txtInp_mbirthpl = document.createElement('input');
				txtInp_mbirthpl.setAttribute('type', 'text');
				txtInp_mbirthpl.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_29');
				txtInp_mbirthpl.setAttribute('size', '1');
				txtInp_mbirthpl.setAttribute('maxlength', '3');
				txtInp_mbirthpl.setAttribute('value', mbirthpl);
				txtInp_mbirthpl.style.color=txtcolor;
				txtInp_mbirthpl.style.fontSize="10px";
				txtInp_mbirthpl.style.width="2.4em";
		// 30. Years in USA ----------------------------------------------------
			var txtInp_yrsUS = document.createElement('input');
				txtInp_yrsUS.setAttribute('type', 'text');
				txtInp_yrsUS.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_30');
				txtInp_yrsUS.setAttribute('size', '1');
				txtInp_yrsUS.setAttribute('maxlength', '2');
				txtInp_yrsUS.setAttribute('value', '');
				txtInp_yrsUS.style.color=txtcolor;
				txtInp_yrsUS.style.fontSize="10px";
				txtInp_yrsUS.style.width="1.4em";
		// 31. Year of immigration YOI_1 ----------------------------------------
			var txtInp_yoi1 = document.createElement('input');
				txtInp_yoi1.setAttribute('type', 'text');
				txtInp_yoi1.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_31');
				txtInp_yoi1.setAttribute('size', '2');
				txtInp_yoi1.setAttribute('maxlength', '4');
				txtInp_yoi1.setAttribute('value', '');
				txtInp_yoi1.style.color=txtcolor;
				txtInp_yoi1.style.fontSize="10px";
		// 32. Naturalized or Alien N-A_1 ---------------------------------------
			var txtInp_na1 = document.createElement('input');
				txtInp_na1.setAttribute('type', 'text');
				txtInp_na1.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_32');
				txtInp_na1.setAttribute('size', '1');
				txtInp_na1.setAttribute('maxlength', '1');
				txtInp_na1.setAttribute('value', '');
				txtInp_na1.style.color=txtcolor;
				txtInp_na1.style.fontSize="10px";
				txtInp_na1.style.width="1em";
		// 33. Year of naturalization YON ---------------------------------------
			var txtInp_yon = document.createElement('input');
				txtInp_yon.setAttribute('type', 'text');
				txtInp_yon.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_33');
				txtInp_yon.setAttribute('size', '2');
				txtInp_yon.setAttribute('maxlength', '4');
				txtInp_yon.setAttribute('value', '');
				txtInp_yon.style.color=txtcolor;
				txtInp_yon.style.fontSize="10px";
		// 34. English spoken, or if not, other Language spoken Eng/Lang --------
			var txtInp_englang = document.createElement('input');
				txtInp_englang.setAttribute('type', 'text');
				txtInp_englang.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_34');
				txtInp_englang.setAttribute('size', '8');
				txtInp_englang.setAttribute('maxlength', '10');
				txtInp_englang.setAttribute('value', '');
				txtInp_englang.style.color=txtcolor;
				txtInp_englang.style.fontSize="10px";
		// 35. Occupation_2 ----------------------------------------------------
			var txtInp_occu2 = document.createElement('input');
				txtInp_occu2.setAttribute('type', 'text');
				txtInp_occu2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_35');
				txtInp_occu2.setAttribute('size', '22');
				txtInp_occu2.setAttribute('value', '');
				txtInp_occu2.style.color=txtcolor;
				txtInp_occu2.style.fontSize="10px";
				txtInp_occu2.style.width="11em";
		// 36. Health ----------------------------------------------------------
			var txtInp_health = document.createElement('input');
				txtInp_health.setAttribute('type', 'text');
				txtInp_health.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_36');
				txtInp_health.setAttribute('size', '3');
				txtInp_health.setAttribute('maxlength', '5');
				txtInp_health.setAttribute('value', '');
				txtInp_health.style.color=txtcolor;
				txtInp_health.style.fontSize="10px";
		// 37. Industry_1 ------------------------------------------------------
			var txtInp_ind1 = document.createElement('input');
				txtInp_ind1.setAttribute('type', 'text');
				txtInp_ind1.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_37');
				txtInp_ind1.setAttribute('size', '22');
				txtInp_ind1.setAttribute('value', '');
				txtInp_ind1.style.color=txtcolor;
				txtInp_ind1.style.fontSize="10px";
		// 38. Employ_1 --------------------------------------------------------
			var txtInp_emp1 = document.createElement('input');
				txtInp_emp1.setAttribute('type', 'text');
				txtInp_emp1.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_38');
				txtInp_emp1.setAttribute('size', '12');
				txtInp_emp1.setAttribute('value', '');
				txtInp_emp1.style.color=txtcolor;
				txtInp_emp1.style.fontSize="10px";
				txtInp_emp1.style.width="7em";
		// 39. Employer EmR ----------------------------------------------------
			var txtInp_emR = document.createElement('input');
				txtInp_emR.setAttribute('type', 'text');
				txtInp_emR.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_39');
				txtInp_emR.setAttribute('size', '1');
				txtInp_emR.setAttribute('maxlength', '1');
				txtInp_emR.setAttribute('value', '');
				txtInp_emR.style.color=txtcolor;
				txtInp_emR.style.fontSize="10px";
				txtInp_emR.style.width="1em";
		// 40. Employed EmD ----------------------------------------------------
			var txtInp_emD = document.createElement('input');
				txtInp_emD.setAttribute('type', 'text');
				txtInp_emD.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_40');
				txtInp_emD.setAttribute('size', '1');
				txtInp_emD.setAttribute('maxlength', '1');
				txtInp_emD.setAttribute('value', '');
				txtInp_emD.style.color=txtcolor;
				txtInp_emD.style.fontSize="10px";
				txtInp_emD.style.width="1em";
		// 41. Months employed -------------------------------------------------
			var txtInp_mnsE = document.createElement('input');
				txtInp_mnsE.setAttribute('type', 'text');
				txtInp_mnsE.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_41');
				txtInp_mnsE.setAttribute('size', '1');
				txtInp_mnsE.setAttribute('maxlength', '2');
				txtInp_mnsE.setAttribute('value', '');
				txtInp_mnsE.style.color=txtcolor;
				txtInp_mnsE.style.fontSize="10px";
				txtInp_mnsE.style.width="1.4em";
		// 42. Working at Home WH ----------------------------------------------
			var txtInp_emH = document.createElement('input');
				txtInp_emH.setAttribute('type', 'text');
				txtInp_emH.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_42');
				txtInp_emH.setAttribute('size', '1');
				txtInp_emH.setAttribute('maxlength', '1');
				txtInp_emH.setAttribute('value', '');
				txtInp_emH.style.color=txtcolor;
				txtInp_emH.style.fontSize="10px";
				txtInp_emH.style.width="1em";
		// 43. Not Employed EmN ------------------------------------------------
			var txtInp_emN = document.createElement('input');
				txtInp_emN.setAttribute('type', 'text');
				txtInp_emN.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_43');
				txtInp_emN.setAttribute('size', '1');
				txtInp_emN.setAttribute('maxlength', '1');
				txtInp_emN.setAttribute('value', '');
				txtInp_emN.style.color=txtcolor;
				txtInp_emN.style.fontSize="10px";
				txtInp_emN.style.width="1em";
		// 44. Weeks unemployed ------------------------------------------------
			var txtInp_wksU = document.createElement('input');
				txtInp_wksU.setAttribute('type', 'text');
				txtInp_wksU.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_44');
				txtInp_wksU.setAttribute('size', '1');
				txtInp_wksU.setAttribute('maxlength', '2');
				txtInp_wksU.setAttribute('value', '');
				txtInp_wksU.style.color=txtcolor;
				txtInp_wksU.style.fontSize="10px";
				txtInp_wksU.style.width="1.4em";
		// 45. Months unemployed -----------------------------------------------
			var txtInp_mnsU = document.createElement('input');
				txtInp_mnsU.setAttribute('type', 'text');
				txtInp_mnsU.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_45');
				txtInp_mnsU.setAttribute('size', '1');
				txtInp_mnsU.setAttribute('maxlength', '2');
				txtInp_mnsU.setAttribute('value', '');
				txtInp_mnsU.style.color=txtcolor;
				txtInp_mnsU.style.fontSize="10px";
				txtInp_mnsU.style.width="1.4em";
		// 46. Education - xxx = School/Able to Read/Able to Write -------------
			var txtInp_educ = document.createElement('input');
				txtInp_educ.setAttribute('type', 'text');
				txtInp_educ.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_46');
				txtInp_educ.setAttribute('size', '1');
				txtInp_educ.setAttribute('maxlength', '3');
				txtInp_educ.setAttribute('value', '');
				txtInp_educ.style.color=txtcolor;
				txtInp_educ.style.fontSize="10px";
				txtInp_educ.style.width="1.8em";
		// 47. Education pre 1890 Census - xxx = School/Cannot Read/Cannot Write ----
			var txtInp_educpre1890 = document.createElement('input');
				txtInp_educpre1890.setAttribute('type', 'text');
				txtInp_educpre1890.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_47');
				txtInp_educpre1890.setAttribute('size', '1');
				txtInp_educpre1890.setAttribute('maxlength', '3');
				txtInp_educpre1890.setAttribute('value', '');
				txtInp_educpre1890.style.color=txtcolor;
				txtInp_educpre1890.style.fontSize="10px";
				txtInp_educpre1890.width="1.8em";
		// 48. English Spoken?_1 eng_1 -----------------------------------------
			var txtInp_eng1 = document.createElement('input');
				txtInp_eng1.setAttribute('type', 'text');
				txtInp_eng1.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_48');
				txtInp_eng1.setAttribute('size', '1');
				txtInp_eng1.setAttribute('maxlength', '1');
				txtInp_eng1.setAttribute('value', '');
				txtInp_eng1.style.color=txtcolor;
				txtInp_eng1.style.fontSize="10px";
				txtInp_eng1.style.width="1em";
		// 49. Home Ownership - x-x-x-xxxx = O/R-F/M-F/H-#### = Owned/Rented-Free/Mortgaged-Farm/House-Farm Schedule number ----
			var txtInp_home = document.createElement('input');
				txtInp_home.setAttribute('type', 'text');
				txtInp_home.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_49');
				txtInp_home.setAttribute('size', '7');
				txtInp_home.setAttribute('maxlength', '12');
				txtInp_home.setAttribute('value', '');
				txtInp_home.style.color=txtcolor;
				txtInp_home.style.fontSize="10px";
		// 50. Birth Place_2 (full format) -------------------------------------
			var txtInp_birthpl2 = document.createElement('input');
				txtInp_birthpl2.setAttribute('type', 'text');
				txtInp_birthpl2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_50');
				txtInp_birthpl2.setAttribute('size', '25');
				txtInp_birthpl2.setAttribute('value', birthpl);
				txtInp_birthpl2.style.color=txtcolor;
				txtInp_birthpl2.style.fontSize="10px";
				txtInp_birthpl2.style.width="13em";
		// 51. POB_2 Indi Birth Place_2 ----------------------------------------
			var txtInp_ibirthpl2 = document.createElement('input');
				txtInp_ibirthpl2.setAttribute('type', 'text');
				txtInp_ibirthpl2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_51');
				txtInp_ibirthpl2.setAttribute('size', '1');
				txtInp_ibirthpl2.setAttribute('maxlength', '3');
				txtInp_ibirthpl2.setAttribute('value', ibirthpl);
				txtInp_ibirthpl2.style.color=txtcolor;
				txtInp_ibirthpl2.style.fontSize="10px";
				txtInp_ibirthpl2.style.width="2.4em";
		// 52. Born in Same Country BIC ----------------------------------------
			var txtInp_bic = document.createElement('input');
				txtInp_bic.setAttribute('type', 'text');
				txtInp_bic.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_52');
				txtInp_bic.setAttribute('size', '1');
				txtInp_bic.setAttribute('maxlength', '1');
				txtInp_bic.setAttribute('value', '');
				txtInp_bic.style.color=txtcolor;
				txtInp_bic.style.fontSize="10px";
				txtInp_bic.style.width="1em";
		// 53. Born outside England BOE ----------------------------------------
			var txtInp_boe = document.createElement('input');
				txtInp_boe.setAttribute('type', 'text');
				txtInp_boe.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_53');
				txtInp_boe.setAttribute('size', '1');
				txtInp_boe.setAttribute('maxlength', '3');
				txtInp_boe.setAttribute('value', '');
				txtInp_boe.style.color=txtcolor;
				txtInp_boe.style.fontSize="10px";
				txtInp_boe.style.width="2.4em";
		// 54. FPOB_2 Birth Place_2 --------------------------------------------
			var txtInp_fbirthpl2 = document.createElement('input');
				txtInp_fbirthpl2.setAttribute('type', 'text');
				txtInp_fbirthpl2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_54');
				txtInp_fbirthpl2.setAttribute('size', '1');
				txtInp_fbirthpl2.setAttribute('maxlength', '3');
				txtInp_fbirthpl2.setAttribute('value', fbirthpl);
				txtInp_fbirthpl2.style.color=txtcolor;
				txtInp_fbirthpl2.style.fontSize="10px";
				txtInp_fbirthpl2.style.width="2.4em";
		// 55. MPOB_2 Birth Place_2 --------------------------------------------
			var txtInp_mbirthpl2 = document.createElement('input');
				txtInp_mbirthpl2.setAttribute('type', 'text');
				txtInp_mbirthpl2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_55');
				txtInp_mbirthpl2.setAttribute('size', '1');
				txtInp_mbirthpl2.setAttribute('maxlength', '3');
				txtInp_mbirthpl2.setAttribute('value', mbirthpl);
				txtInp_mbirthpl2.style.color=txtcolor;
				txtInp_mbirthpl2.style.fontSize="10px";
				txtInp_mbirthpl2.style.width="2.4em";
		// 56. Native Language -------------------------------------------------
			var txtInp_lang = document.createElement('input');
				txtInp_lang.setAttribute('type', 'text');
				txtInp_lang.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_56');
				txtInp_lang.setAttribute('size', '8');
				txtInp_lang.setAttribute('maxlength', '10');
				txtInp_lang.setAttribute('value', '');
				txtInp_lang.style.color=txtcolor;
				txtInp_lang.style.fontSize="10px";
		// 57. Year of immigration YOI_2 ---------------------------------------
			var txtInp_yoi2 = document.createElement('input');
				txtInp_yoi2.setAttribute('type', 'text');
				txtInp_yoi2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_57');
				txtInp_yoi2.setAttribute('size', '2');
				txtInp_yoi2.setAttribute('maxlength', '4');
				txtInp_yoi2.setAttribute('value', '');
				txtInp_yoi2.style.color=txtcolor;
				txtInp_yoi2.style.fontSize="10px";
		// 58. Naturalized or Alien N-A_2 --------------------------------------
			var txtInp_na2 = document.createElement('input');
				txtInp_na2.setAttribute('type', 'text');
				txtInp_na2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_58');
				txtInp_na2.setAttribute('size', '1');
				txtInp_na2.setAttribute('maxlength', '1');
				txtInp_na2.setAttribute('value', '');
				txtInp_na2.style.color=txtcolor;
				txtInp_na2.style.fontSize="10px";
				txtInp_na2.style.width="1em";
		// 59. English Spoken?_2 eng_2 -----------------------------------------
			var txtInp_eng2 = document.createElement('input');
				txtInp_eng2.setAttribute('type', 'text');
				txtInp_eng2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_59');
				txtInp_eng2.setAttribute('size', '1');
				txtInp_eng2.setAttribute('maxlength', '1');
				txtInp_eng2.setAttribute('value', '');
				txtInp_eng2.style.color=txtcolor;
				txtInp_eng2.style.fontSize="10px";
				txtInp_eng2.style.width="1em";
		// 60. Occupation_3 ----------------------------------------------------
			var txtInp_occu3 = document.createElement('input');
				txtInp_occu3.setAttribute('type', 'text');
				txtInp_occu3.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_60');
				txtInp_occu3.setAttribute('size', '22');
				txtInp_occu3.setAttribute('value', '');
				txtInp_occu3.style.color=txtcolor;
				txtInp_occu3.style.fontSize="10px";
				txtInp_occu3.style.width="11em";
		// 61. Industry_2 -----------------------------------------------------
			var txtInp_ind2 = document.createElement('input');
				txtInp_ind2.setAttribute('type', 'text');
				txtInp_ind2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_61');
				txtInp_ind2.setAttribute('size', '22');
				txtInp_ind2.setAttribute('value', '');
				txtInp_ind2.style.color=txtcolor;
				txtInp_ind2.style.fontSize="10px";
		// 62. Employ_2 -------------------------------------------------------
			var txtInp_emp2 = document.createElement('input');
				txtInp_emp2.setAttribute('type', 'text');
				txtInp_emp2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_62');
				txtInp_emp2.setAttribute('size', '12');
				txtInp_emp2.setAttribute('value', '');
				txtInp_emp2.style.color=txtcolor;
				txtInp_emp2.style.fontSize="10px";
				txtInp_emp2.style.width="7em";
		/*
		// 63. Nationality (UK 1911) ------------------------------------------
			var txtInp_emp2 = document.createElement('input');
				txtInp_emp2.setAttribute('type', 'text');
				txtInp_emp2.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_63');
				txtInp_emp2.setAttribute('size', '22');
				txtInp_emp2.setAttribute('value', '');
				txtInp_emp2.style.color=txtcolor;
				txtInp_emp2.style.fontSize="10px";
		*/
		// 63. Infirmaties ----------------------------------------------------
			var txtInp_infirm = document.createElement('input');
				txtInp_infirm.setAttribute('type', 'text');
				txtInp_infirm.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_63');
				txtInp_infirm.setAttribute('size', '3');
				txtInp_infirm.setAttribute('maxlength', '4');
				txtInp_infirm.setAttribute('value', '');
				txtInp_infirm.style.color=txtcolor;
				txtInp_infirm.style.fontSize="10px";
				txtInp_infirm.style.width="2.3em";
		// 64. Health / Situation = Disease-Infirmaties-Convict,Pauper etc ----
			var txtInp_situ = document.createElement('input');
				txtInp_situ.setAttribute('type', 'text');
				txtInp_situ.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_64');
				txtInp_situ.setAttribute('size', '12');
				txtInp_situ.setAttribute('value', '');
				txtInp_situ.style.color=txtcolor;
				txtInp_situ.style.fontSize="10px";
		// 65. Veteran ? ------------------------------------------------------
			var txtInp_vet = document.createElement('input');
				txtInp_vet.setAttribute('type', 'text');
				txtInp_vet.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_65');
				txtInp_vet.setAttribute('size', '1');
				txtInp_vet.setAttribute('maxlength', '1');
				txtInp_vet.setAttribute('value', '');
				txtInp_vet.style.color=txtcolor;
				txtInp_vet.style.fontSize="10px";
				txtInp_vet.style.width="1em";
		// 66. War or Expedition ----------------------------------------------
			var txtInp_war = document.createElement('input');
				txtInp_war.setAttribute('type', 'text');
				txtInp_war.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_66');
				txtInp_war.setAttribute('size', '8');
				txtInp_war.setAttribute('value', '');
				txtInp_war.style.color=txtcolor;
				txtInp_war.style.fontSize="10px";
		// 67. Infirmaties (Census 1910) - x-x = Blind (both eyes) Y/N - Deaf and dumb Y/N ----
			var txtInp_infirm1910 = document.createElement('input');
				txtInp_infirm1910.setAttribute('type', 'text');
				txtInp_infirm1910.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_67');
				txtInp_infirm1910.setAttribute('size', '1');
				txtInp_infirm1910.setAttribute('maxlength', '2');
				txtInp_infirm1910.setAttribute('value', '');
				txtInp_infirm1910.style.color=txtcolor;
				txtInp_infirm1910.style.fontSize="10px";
				txtInp_infirm1910.style.width="1.4em";

		// Hidden =============================================================
		// 68. DOB ------------------------------------------------------------
			var txtInp_DOB = document.createElement('input');
				txtInp_DOB.setAttribute('type', 'text');
				txtInp_DOB.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_68');
				txtInp_DOB.setAttribute('size', '9');
				txtInp_DOB.setAttribute('maxlength', '20');
				txtInp_DOB.setAttribute('value', dob);
				txtInp_DOB.style.color=txtcolor;
				txtInp_DOB.style.fontSize="10px";
				txtInp_DOB.style.width="5.6em";
				txtInp_DOB.type = "hidden";
		// 69. DOM ------------------------------------------------------------
			var txtInp_DOM = document.createElement('input');
				txtInp_DOM.setAttribute('type', 'text');
				txtInp_DOM.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_69');
				txtInp_DOM.setAttribute('size', '8');
				txtInp_DOM.setAttribute('maxlength', '20');
				txtInp_DOM.setAttribute('value', dom);
				txtInp_DOM.style.color=txtcolor;
				txtInp_DOM.style.fontSize="10px";
				txtInp_DOM.style.width="1.4em";
				txtInp_DOM.type = "hidden";
		// 70. Full Name ------------------------------------------------------
			var txtInp_FullName = document.createElement('input');
				txtInp_FullName.setAttribute('type', 'text');
				txtInp_FullName.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_70');
				txtInp_FullName.setAttribute('size', '8');
				txtInp_FullName.setAttribute('maxlength', '40');
				txtInp_FullName.setAttribute('value', nam);
				txtInp_FullName.style.color=txtcolor;
				txtInp_FullName.style.fontSize="10px";
				txtInp_FullName.style.width="5.4em";
				txtInp_FullName.type = "hidden";
		// 71. Married Name ---------------------------------------------------
			var txtInp_MarrName = document.createElement('input');
				txtInp_MarrName.setAttribute('type', 'text');
				txtInp_MarrName.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_71');
				txtInp_MarrName.setAttribute('size', '8');
				txtInp_MarrName.setAttribute('maxlength', '40');
				txtInp_MarrName.setAttribute('value', mnam);
				txtInp_MarrName.style.color=txtcolor;
				txtInp_MarrName.style.fontSize="10px";
				txtInp_MarrName.style.width="1.4em";
				txtInp_MarrName.type = "hidden";
		// 72. DOD ------------------------------------------------------------
			var txtInp_DOD = document.createElement('input');
				txtInp_DOD.setAttribute('type', 'text');
				txtInp_DOD.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_72');
				txtInp_DOD.setAttribute('size', '8');
				txtInp_DOD.setAttribute('maxlength', '20');
				txtInp_DOD.setAttribute('value', dod);
				txtInp_DOD.style.color=txtcolor;
				txtInp_DOD.style.fontSize="10px";
				txtInp_DOD.style.width="1.4em";
				txtInp_DOD.type = "hidden";
		// 73. DOD ------------------------------------------------------------
			var txtInp_ChBLD = document.createElement('input');
				txtInp_ChBLD.setAttribute('type', 'text');
				txtInp_ChBLD.setAttribute('id', INPUT_NAME_PREFIX + iteration + '_73');
				txtInp_ChBLD.setAttribute('size', '8');
				txtInp_ChBLD.setAttribute('maxlength', '20');
				txtInp_ChBLD.setAttribute('value', chilBLD);
				txtInp_ChBLD.style.color=txtcolor;
				txtInp_ChBLD.style.fontSize="10px";
				txtInp_ChBLD.style.width="1.4em";
				txtInp_ChBLD.type = "hidden";

		// Extra Cells for Navigation =========================================
		// 74. Extra 1. Delete Row Button -----------------------------------------
			var btnEl = document.createElement('button');
			  var btnEltext = document.createTextNode('X');
				btnEl.style.color='red';
				btnEl.appendChild(btnEltext);
				btnEl.onclick = function () {deleteCurrentRow(this)};
		// 75. Extra 2. Insert row Radio button -----------------------------------
			var raEl;
				try {
					raEl = document.createElement('<input type="radio" name="' + RADIO_NAME + '" value="' + iteration + '">');
					var failIfNotIE = raEl.name.length;
				} catch(ex) {
					raEl = document.createElement('input');
					raEl.setAttribute('type', 'radio');
					raEl.setAttribute('name', RADIO_NAME );
					raEl.setAttribute('value', iteration);
				}
		// 76. Extra 3. Item Number -----------------------------------------------
			var txt_itemNo2 = document.createTextNode(iteration);
		}

		// Cells Not visible but used for row re-order process =================
			var cbEl = document.createElement('input');
				cbEl.type = "hidden";


		// **E** Append appropriate Cell elements to each cell ================
		cell_[0].appendChild(txt_itemNo); // Item Number
		cell_[1].appendChild(txtInp_pid); // Indi ID
		cell_[2].appendChild(txtInp_nam); // Name
		cell_[3].appendChild(txtInp_label); // Relationship_1
		cell_[4].appendChild(txtInp_cond); // Condition_1
		cell_[5].appendChild(txtInp_tenure); // Tenure
		cell_[6].appendChild(txtInp_assets); // Assets_1
		cell_[7].appendChild(txtInp_age); // Age_1
		cell_[8].appendChild(txtInp_race); // Race_1
		cell_[9].appendChild(txtInp_gend); // Sex

		cell_[10].appendChild(txtInp_race2); // Race_2
		cell_[11].appendChild(txtInp_yob); // DOB/YOB
		cell_[12].appendChild(txtInp_age2); // Age_2
		cell_[13].appendChild(txtInp_bmth); // Birth Month
		cell_[14].appendChild(txtInp_label2); // Relationship_2
		cell_[15].appendChild(txtInp_cond2); // Condition_2
		cell_[16].appendChild(txtInp_yrsm); // Years Married
		cell_[17].appendChild(txtInp_chilB); // Children Born Alive
		cell_[18].appendChild(txtInp_chilL); // Children Still Living
		cell_[19].appendChild(txtInp_chilD); // Children who have Died

		cell_[20].appendChild(txtInp_ageM); // Age st first marriage
		cell_[21].appendChild(txtInp_occu); // Occupation_1
		cell_[22].appendChild(txtInp_assets2); // Assets_2
		cell_[23].appendChild(txtInp_birthpl); // Place of Birth_1
		cell_[24].appendChild(txtInp_parent); // Parentage
		cell_[25].appendChild(txtInp_bmth2); // Month if born in Census Year - bmth2
		cell_[26].appendChild(txtInp_mmth); // Month if married in Census Year
		cell_[27].appendChild(txtInp_ibirthpl); // Indis POB_1
		cell_[28].appendChild(txtInp_fbirthpl); // Father FPOB_1
		cell_[29].appendChild(txtInp_mbirthpl); // Mother MPOB_1

		cell_[30].appendChild(txtInp_yrsUS); // Years in USA
		cell_[31].appendChild(txtInp_yoi1); // Year of immigration YOI_1
		cell_[32].appendChild(txtInp_na1); // Naturalized or Alien N-A_1
		cell_[33].appendChild(txtInp_yon); // Year of naturalization YON
		cell_[34].appendChild(txtInp_englang); // English spoken, if not, Other Language spoken Eng/Lang
		cell_[35].appendChild(txtInp_occu2); // Occupation_2
		cell_[36].appendChild(txtInp_health); // Health - 5 parameters x--xx etc
		cell_[37].appendChild(txtInp_ind1); // Industry ind_1
		cell_[38].appendChild(txtInp_emp1); // Employ_1
		cell_[39].appendChild(txtInp_emR); // Employer EmR

		cell_[40].appendChild(txtInp_emD); // Employed EmD
		cell_[41].appendChild(txtInp_mnsE); // Months employed during Census Year
		cell_[42].appendChild(txtInp_emH); // Working At Home WH
		cell_[43].appendChild(txtInp_emN); // Not Employed EmN
		cell_[44].appendChild(txtInp_wksU); // Weeks unemployed during Census Year
		cell_[45].appendChild(txtInp_mnsU); // Months unemployed during Census Year
		cell_[46].appendChild(txtInp_educ); // Education 3 parameters Sch-Read-Write  -xx
		cell_[47].appendChild(txtInp_educpre1890); // Education (pre 1890 Census) - 3 parameters = Sch, Cannot Read, Cannot Write  -xx
		cell_[48].appendChild(txtInp_eng1); // English spoken Y/N  eng_1
		cell_[49].appendChild(txtInp_home); // Home Ownership x-x-x-xxxx = Owned/Rented - Free/Morgaged - Farm/House - Farm Sched #

		cell_[50].appendChild(txtInp_birthpl2); // Birth Place_2
		cell_[51].appendChild(txtInp_ibirthpl2); // Indis POB_2
		cell_[52].appendChild(txtInp_bic); // Born in County (UK)
		cell_[53].appendChild(txtInp_boe); // Born outside England (UK)
		cell_[54].appendChild(txtInp_fbirthpl2); // Fathers FPOB_2
		cell_[55].appendChild(txtInp_mbirthpl2); // Mothers MPOB_2
		cell_[56].appendChild(txtInp_lang); // Mother Tongue lang
		cell_[57].appendChild(txtInp_yoi2); // Year of immigration YOI_2
		cell_[58].appendChild(txtInp_na2); // Naturalized or Alien N-A_2
		cell_[59].appendChild(txtInp_eng2); // English spoken Y/N  eng_2

		cell_[60].appendChild(txtInp_occu3); // Occupation_3
		cell_[61].appendChild(txtInp_ind2); // Industry ind_2
		cell_[62].appendChild(txtInp_emp2); // Employ_2
		//cell_[63].appendChild(txtInp_N_ality); // Nationality (UK 1911) - British, OR Naturalised, OR(French, German, Russian etc)
		cell_[63].appendChild(txtInp_infirm); // Infirmaties - up to 5 parameters x--xx etc
		cell_[64].appendChild(txtInp_situ); // Health Situation 1890 - Disease, Infimaties, Convict, Pauper etc
		cell_[65].appendChild(txtInp_vet); // Veteran ?
		cell_[66].appendChild(txtInp_war); // War or expedition
		cell_[67].appendChild(txtInp_infirm1910); // Infirmaties - xx = Blind (both eyes) Y/N/-, Deaf and Dumb Y/N/-

		// Hidden Cells =======================================================
		if (iteration > 0) {
		cell_[68].appendChild(txtInp_DOB); // Date of Birth
		cell_[69].appendChild(txtInp_DOM); // Date of Marriage
		cell_[70].appendChild(txtInp_FullName); // Full Name
		cell_[71].appendChild(txtInp_MarrName); // Married Name
		cell_[72].appendChild(txtInp_DOD); // Date of Death
		cell_[73].appendChild(txtInp_ChBLD); // Text Array - Children Born/Living/Died
		}

		// Extra Cells ========================================================
		if (iteration == 0) {
			cell_tdel.appendChild(txtInp_tdel); // Text Del
			cell_tra.appendChild(txtInp_tra); // Text Ins
		} else {
			cell_del.appendChild(btnEl); // Onclick = Delete Row
			cell_ra.appendChild(raEl); // Radio button used for inserting a row, rather than adding at end of table)
		}

		// cell_index2.appendChild(txt_itemNo2); // Text Item Number

		// **F** Pass in the elements to be referenced later ==================
		// Store the myRow object in each row
		row.myRow = new myRowObject( txt_itemNo, txtInp_pid, txtInp_nam, txtInp_label, txtInp_cond, txtInp_tenure, txtInp_assets, txtInp_age, txtInp_race, txtInp_gend,
										txtInp_race2, txtInp_yob, txtInp_age2, txtInp_bmth, txtInp_label2, txtInp_cond2, txtInp_yrsm, txtInp_chilB, txtInp_chilL, txtInp_chilD,
										txtInp_ageM, txtInp_occu, txtInp_assets2, txtInp_birthpl, txtInp_parent, txtInp_bmth2, txtInp_mmth, txtInp_ibirthpl, txtInp_fbirthpl, txtInp_mbirthpl,
										txtInp_yrsUS, txtInp_yoi1, txtInp_na1, txtInp_yon, txtInp_englang, txtInp_occu2, txtInp_health, txtInp_ind1, txtInp_emp1, txtInp_emR,
										txtInp_emD, txtInp_mnsE, txtInp_emH, txtInp_emN, txtInp_wksU, txtInp_mnsU, txtInp_educ, txtInp_educpre1890, txtInp_eng1, txtInp_home,
										txtInp_birthpl2, txtInp_ibirthpl2, txtInp_bic, txtInp_boe, txtInp_fbirthpl2, txtInp_mbirthpl2, txtInp_lang, txtInp_yoi2, txtInp_na2, txtInp_eng2,
										txtInp_occu3, txtInp_ind2, txtInp_emp2, txtInp_infirm, txtInp_situ, txtInp_vet, txtInp_war, txtInp_infirm1910, txtInp_DOB, txtInp_DOM,
										txtInp_FullName, txtInp_MarrName, txtInp_DOD, txtInp_ChBLD,
										cbEl, raEl, txt_itemNo2
									);
	}
}

// deleteCurrentRow - function to delete a row
function deleteCurrentRow(obj) {
	if (hasLoaded) {
		var delRow = obj.parentNode.parentNode;
		var tbl = delRow.parentNode.parentNode;
		var rIndex = delRow.sectionRowIndex;
		var rowArray = new Array(delRow);
		deleteRows(rowArray);
		reorderRows(tbl, rIndex);
		preview();
	}
}

function deleteHeaderRow(obj) {
	if (hasLoaded) {
		var delRow = obj.parentNode.parentNode;
		var tbl = delRow.parentNode.parentNode;
		var rIndex = delRow.sectionRowIndex;
		var rowArray = new Array(delRow);
		deleteRows(rowArray);
	}
}

function deleteRows(rowObjArray) {
	if (hasLoaded) {
		for (var i=0; i<rowObjArray.length; i++) {  // i set to 1 to avoid table header row of number 0
			var rIndex = rowObjArray[i].sectionRowIndex;
			rowObjArray[i].parentNode.deleteRow(rIndex);
		}
	}
}

// reorderRows - used to reorder rows after an insert or delete
function reorderRows(tbl, startingIndex) {
	if (hasLoaded) {
		if (tbl.tBodies[0].rows[startingIndex]) {
			var count = startingIndex + ROW_BASE;
			for (var i=startingIndex; i<tbl.tBodies[0].rows.length; i++) {
				// CONFIG: next line is affected by myRowObject settings
				tbl.tBodies[0].rows[i].myRow.zero.data = count; // text - (left column item number)

				// ------------------------------------------------------------
				tbl.tBodies[0].rows[i].myRow.one.id = INPUT_NAME_PREFIX + count + '_1';  // input text
				tbl.tBodies[0].rows[i].myRow.two.id  = INPUT_NAME_PREFIX + count + '_2';  // input text
				tbl.tBodies[0].rows[i].myRow.three.id = INPUT_NAME_PREFIX + count + '_3';  // input text
				tbl.tBodies[0].rows[i].myRow.four.id = INPUT_NAME_PREFIX + count + '_4';  // input text
				tbl.tBodies[0].rows[i].myRow.five.id = INPUT_NAME_PREFIX + count + '_5';  // input text
				tbl.tBodies[0].rows[i].myRow.six.id = INPUT_NAME_PREFIX + count + '_6';  // input text
				tbl.tBodies[0].rows[i].myRow.seven.id = INPUT_NAME_PREFIX + count + '_7';  // input text
				tbl.tBodies[0].rows[i].myRow.eight.id = INPUT_NAME_PREFIX + count + '_8';  // input text
				tbl.tBodies[0].rows[i].myRow.nine.id = INPUT_NAME_PREFIX + count + '_9';  // input text

				tbl.tBodies[0].rows[i].myRow.ten.id = INPUT_NAME_PREFIX + count + '_10'; // input text
				tbl.tBodies[0].rows[i].myRow.eleven.id = INPUT_NAME_PREFIX + count + '_11'; // input text
				tbl.tBodies[0].rows[i].myRow.twelve.id = INPUT_NAME_PREFIX + count + '_12'; // input text
				tbl.tBodies[0].rows[i].myRow.thirteen.id = INPUT_NAME_PREFIX + count + '_13';  // input text
				tbl.tBodies[0].rows[i].myRow.fourteen.id = INPUT_NAME_PREFIX + count + '_14';  // input text
				tbl.tBodies[0].rows[i].myRow.fifteen.id = INPUT_NAME_PREFIX + count + '_15';  // input text
				tbl.tBodies[0].rows[i].myRow.sixteen.id = INPUT_NAME_PREFIX + count + '_16';  // input text
				tbl.tBodies[0].rows[i].myRow.seventeen.id = INPUT_NAME_PREFIX + count + '_17';  // input text
				tbl.tBodies[0].rows[i].myRow.eighteen.id = INPUT_NAME_PREFIX + count + '_18';  // input text
				tbl.tBodies[0].rows[i].myRow.nineteen.id = INPUT_NAME_PREFIX + count + '_19';  // input text

				tbl.tBodies[0].rows[i].myRow.twenty.id = INPUT_NAME_PREFIX + count + '_20'; // input text
				tbl.tBodies[0].rows[i].myRow.twentyone.id = INPUT_NAME_PREFIX + count + '_21';  // input text
				tbl.tBodies[0].rows[i].myRow.twentytwo.id  = INPUT_NAME_PREFIX + count + '_22';  // input text
				tbl.tBodies[0].rows[i].myRow.twentythree.id = INPUT_NAME_PREFIX + count + '_23';  // input text
				tbl.tBodies[0].rows[i].myRow.twentyfour.id = INPUT_NAME_PREFIX + count + '_24';  // input text
				tbl.tBodies[0].rows[i].myRow.twentyfive.id = INPUT_NAME_PREFIX + count + '_25';  // input text
				tbl.tBodies[0].rows[i].myRow.twentysix.id = INPUT_NAME_PREFIX + count + '_26';  // input text
				tbl.tBodies[0].rows[i].myRow.twentyseven.id = INPUT_NAME_PREFIX + count + '_27';  // input text
				tbl.tBodies[0].rows[i].myRow.twentyeight.id = INPUT_NAME_PREFIX + count + '_28';  // input text
				tbl.tBodies[0].rows[i].myRow.twentynine.id = INPUT_NAME_PREFIX + count + '_29';  // input text

				tbl.tBodies[0].rows[i].myRow.thirty.id = INPUT_NAME_PREFIX + count + '_30'; // input text
				tbl.tBodies[0].rows[i].myRow.thirtyone.id = INPUT_NAME_PREFIX + count + '_31';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtytwo.id = INPUT_NAME_PREFIX + count + '_32';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtythree.id = INPUT_NAME_PREFIX + count + '_33';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtyfour.id = INPUT_NAME_PREFIX + count + '_34';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtyfive.id = INPUT_NAME_PREFIX + count + '_35';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtysix.id = INPUT_NAME_PREFIX + count + '_36';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtyseven.id = INPUT_NAME_PREFIX + count + '_37';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtyeight.id = INPUT_NAME_PREFIX + count + '_38';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtynine.id = INPUT_NAME_PREFIX + count + '_39';  // input text

				tbl.tBodies[0].rows[i].myRow.forty.id = INPUT_NAME_PREFIX + count + '_40'; // input text
				tbl.tBodies[0].rows[i].myRow.fortyone.id = INPUT_NAME_PREFIX + count + '_41';  // input text
				tbl.tBodies[0].rows[i].myRow.fortytwo.id = INPUT_NAME_PREFIX + count + '_42';  // input text
				tbl.tBodies[0].rows[i].myRow.fortythree.id = INPUT_NAME_PREFIX + count + '_43';  // input text
				tbl.tBodies[0].rows[i].myRow.fortyfour.id = INPUT_NAME_PREFIX + count + '_44';  // input text
				tbl.tBodies[0].rows[i].myRow.fortyfive.id = INPUT_NAME_PREFIX + count + '_45';  // input text
				tbl.tBodies[0].rows[i].myRow.fortysix.id = INPUT_NAME_PREFIX + count + '_46';  // input text
				tbl.tBodies[0].rows[i].myRow.fortyseven.id = INPUT_NAME_PREFIX + count + '_47';  // input text
				tbl.tBodies[0].rows[i].myRow.fortyeight.id = INPUT_NAME_PREFIX + count + '_48';  // input text
				tbl.tBodies[0].rows[i].myRow.fortynine.id = INPUT_NAME_PREFIX + count + '_49';  // input text

				tbl.tBodies[0].rows[i].myRow.fifty.id = INPUT_NAME_PREFIX + count + '_50';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftyone.id = INPUT_NAME_PREFIX + count + '_51';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftytwo.id = INPUT_NAME_PREFIX + count + '_52';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftythree.id = INPUT_NAME_PREFIX + count + '_53';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftyfour.id = INPUT_NAME_PREFIX + count + '_54';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftyfive.id = INPUT_NAME_PREFIX + count + '_55';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftysix.id = INPUT_NAME_PREFIX + count + '_56';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftyseven.id = INPUT_NAME_PREFIX + count + '_57';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftyeight.id = INPUT_NAME_PREFIX + count + '_58';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftynine.id = INPUT_NAME_PREFIX + count + '_59';  // input text

				tbl.tBodies[0].rows[i].myRow.sixty.id = INPUT_NAME_PREFIX + count + '_60';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtyone.id = INPUT_NAME_PREFIX + count + '_61';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtytwo.id = INPUT_NAME_PREFIX + count + '_62';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtythree.id = INPUT_NAME_PREFIX + count + '_63';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtyfour.id = INPUT_NAME_PREFIX + count + '_64';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtyfive.id = INPUT_NAME_PREFIX + count + '_65';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtysix.id = INPUT_NAME_PREFIX + count + '_66';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtyseven.id = INPUT_NAME_PREFIX + count + '_67';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtyeight.id = INPUT_NAME_PREFIX + count + '_68';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtynine.id = INPUT_NAME_PREFIX + count + '_69';  // input text
				tbl.tBodies[0].rows[i].myRow.seventy.id = INPUT_NAME_PREFIX + count + '_70';  // input text
				tbl.tBodies[0].rows[i].myRow.seventyone.id = INPUT_NAME_PREFIX + count + '_71';  // input text
				tbl.tBodies[0].rows[i].myRow.seventytwo.id = INPUT_NAME_PREFIX + count + '_72';  // input text
				tbl.tBodies[0].rows[i].myRow.seventythree.id = INPUT_NAME_PREFIX + count + '_73';  // input text
				// ------------------------------------------------------------

				// ------------------------------------------------------------
				tbl.tBodies[0].rows[i].myRow.one.name = INPUT_NAME_PREFIX + count + '_1';  // input text
				tbl.tBodies[0].rows[i].myRow.two.name  = INPUT_NAME_PREFIX + count + '_2';  // input text
				tbl.tBodies[0].rows[i].myRow.three.name = INPUT_NAME_PREFIX + count + '_3';  // input text
				tbl.tBodies[0].rows[i].myRow.four.name = INPUT_NAME_PREFIX + count + '_4';  // input text
				tbl.tBodies[0].rows[i].myRow.five.name = INPUT_NAME_PREFIX + count + '_5';  // input text
				tbl.tBodies[0].rows[i].myRow.six.name = INPUT_NAME_PREFIX + count + '_6';  // input text
				tbl.tBodies[0].rows[i].myRow.seven.name = INPUT_NAME_PREFIX + count + '_7';  // input text
				tbl.tBodies[0].rows[i].myRow.eight.name = INPUT_NAME_PREFIX + count + '_8';  // input text
				tbl.tBodies[0].rows[i].myRow.nine.name = INPUT_NAME_PREFIX + count + '_9';  // input text

				tbl.tBodies[0].rows[i].myRow.ten.name = INPUT_NAME_PREFIX + count + '_10'; // input text
				tbl.tBodies[0].rows[i].myRow.eleven.name = INPUT_NAME_PREFIX + count + '_11'; // input text
				tbl.tBodies[0].rows[i].myRow.twelve.name = INPUT_NAME_PREFIX + count + '_12'; // input text
				tbl.tBodies[0].rows[i].myRow.thirteen.name = INPUT_NAME_PREFIX + count + '_13';  // input text
				tbl.tBodies[0].rows[i].myRow.fourteen.name = INPUT_NAME_PREFIX + count + '_14';  // input text
				tbl.tBodies[0].rows[i].myRow.fifteen.name = INPUT_NAME_PREFIX + count + '_15';  // input text
				tbl.tBodies[0].rows[i].myRow.sixteen.name = INPUT_NAME_PREFIX + count + '_16';  // input text
				tbl.tBodies[0].rows[i].myRow.seventeen.name = INPUT_NAME_PREFIX + count + '_17';  // input text
				tbl.tBodies[0].rows[i].myRow.eighteen.name = INPUT_NAME_PREFIX + count + '_18';  // input text
				tbl.tBodies[0].rows[i].myRow.nineteen.name = INPUT_NAME_PREFIX + count + '_19';  // input text

				tbl.tBodies[0].rows[i].myRow.twenty.name = INPUT_NAME_PREFIX + count + '_20'; // input text
				tbl.tBodies[0].rows[i].myRow.twentyone.name = INPUT_NAME_PREFIX + count + '_21';  // input text
				tbl.tBodies[0].rows[i].myRow.twentytwo.name = INPUT_NAME_PREFIX + count + '_22';  // input text
				tbl.tBodies[0].rows[i].myRow.twentythree.name = INPUT_NAME_PREFIX + count + '_23';  // input text
				tbl.tBodies[0].rows[i].myRow.twentyfour.name = INPUT_NAME_PREFIX + count + '_24';  // input text
				tbl.tBodies[0].rows[i].myRow.twentyfive.name = INPUT_NAME_PREFIX + count + '_25';  // input text
				tbl.tBodies[0].rows[i].myRow.twentysix.name = INPUT_NAME_PREFIX + count + '_26';  // input text
				tbl.tBodies[0].rows[i].myRow.twentyseven.name = INPUT_NAME_PREFIX + count + '_27';  // input text
				tbl.tBodies[0].rows[i].myRow.twentyeight.name = INPUT_NAME_PREFIX + count + '_28';  // input text
				tbl.tBodies[0].rows[i].myRow.twentynine.name = INPUT_NAME_PREFIX + count + '_29';  // input text

				tbl.tBodies[0].rows[i].myRow.thirty.name = INPUT_NAME_PREFIX + count + '_30'; // input text
				tbl.tBodies[0].rows[i].myRow.thirtyone.name = INPUT_NAME_PREFIX + count + '_31';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtytwo.name  = INPUT_NAME_PREFIX + count + '_32';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtythree.name = INPUT_NAME_PREFIX + count + '_33';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtyfour.name = INPUT_NAME_PREFIX + count + '_34';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtyfive.name = INPUT_NAME_PREFIX + count + '_35';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtysix.name = INPUT_NAME_PREFIX + count + '_36';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtyseven.name = INPUT_NAME_PREFIX + count + '_37';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtyeight.name = INPUT_NAME_PREFIX + count + '_38';  // input text
				tbl.tBodies[0].rows[i].myRow.thirtynine.name = INPUT_NAME_PREFIX + count + '_39';  // input text

				tbl.tBodies[0].rows[i].myRow.forty.name = INPUT_NAME_PREFIX + count + '_40'; // input text
				tbl.tBodies[0].rows[i].myRow.fortyone.name = INPUT_NAME_PREFIX + count + '_41';  // input text
				tbl.tBodies[0].rows[i].myRow.fortytwo.name  = INPUT_NAME_PREFIX + count + '_42';  // input text
				tbl.tBodies[0].rows[i].myRow.fortythree.name = INPUT_NAME_PREFIX + count + '_43';  // input text
				tbl.tBodies[0].rows[i].myRow.fortyfour.name = INPUT_NAME_PREFIX + count + '_44';  // input text
				tbl.tBodies[0].rows[i].myRow.fortyfive.name = INPUT_NAME_PREFIX + count + '_45';  // input text
				tbl.tBodies[0].rows[i].myRow.fortysix.name = INPUT_NAME_PREFIX + count + '_46';  // input text
				tbl.tBodies[0].rows[i].myRow.fortyseven.name = INPUT_NAME_PREFIX + count + '_47';  // input text
				tbl.tBodies[0].rows[i].myRow.fortyeight.name = INPUT_NAME_PREFIX + count + '_48';  // input text
				tbl.tBodies[0].rows[i].myRow.fortynine.name = INPUT_NAME_PREFIX + count + '_49';  // input text

				tbl.tBodies[0].rows[i].myRow.fifty.name = INPUT_NAME_PREFIX + count + '_50';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftyone.name = INPUT_NAME_PREFIX + count + '_51';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftytwo.name = INPUT_NAME_PREFIX + count + '_52';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftythree.name = INPUT_NAME_PREFIX + count + '_53';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftyfour.name = INPUT_NAME_PREFIX + count + '_54';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftyfive.name = INPUT_NAME_PREFIX + count + '_55';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftysix.name = INPUT_NAME_PREFIX + count + '_56';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftyseven.name = INPUT_NAME_PREFIX + count + '_57';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftyeight.name = INPUT_NAME_PREFIX + count + '_58';  // input text
				tbl.tBodies[0].rows[i].myRow.fiftynine.name = INPUT_NAME_PREFIX + count + '_59';  // input text

				tbl.tBodies[0].rows[i].myRow.sixty.name = INPUT_NAME_PREFIX + count + '_60';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtyone.name = INPUT_NAME_PREFIX + count + '_61';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtytwo.name = INPUT_NAME_PREFIX + count + '_62';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtythree.name = INPUT_NAME_PREFIX + count + '_63';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtyfour.name = INPUT_NAME_PREFIX + count + '_64';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtyfive.name = INPUT_NAME_PREFIX + count + '_65';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtysix.name = INPUT_NAME_PREFIX + count + '_66';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtyseven.name = INPUT_NAME_PREFIX + count + '_67';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtyeight.name = INPUT_NAME_PREFIX + count + '_68';  // input text
				tbl.tBodies[0].rows[i].myRow.sixtynine.name = INPUT_NAME_PREFIX + count + '_69';  // input text

				tbl.tBodies[0].rows[i].myRow.seventy.name = INPUT_NAME_PREFIX + count + '_70';  // input text
				tbl.tBodies[0].rows[i].myRow.seventyone.name = INPUT_NAME_PREFIX + count + '_71';  // input text
				tbl.tBodies[0].rows[i].myRow.seventytwo.name = INPUT_NAME_PREFIX + count + '_72';  // input text
				tbl.tBodies[0].rows[i].myRow.seventythree.name = INPUT_NAME_PREFIX + count + '_73';  // input text

				// ------------------------------------------------------------
				tbl.tBodies[0].rows[i].myRow.ra.value = count; // input radio
				count++;
			}
		}
	}
}

</script>
