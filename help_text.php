<?php
/**
 * Show help text in a popup window.
 *
 * This file also serves as a database of fact and label descriptions,
 * allowing them to be discovered by xgettext, so we may use them dynamically
 * in the rest of the code.
 * Help links are generated using help_link('help_topic')
 *
 * Help text for modules belongs in modules/XXX/help_text.php
 * Module help links are generated using help_link('help_topic', 'module')
 *
 * Copyright (C) 2010 Greg Roach
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
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'help_text.php');
require './includes/session.php';

$help=safe_GET('help');
switch ($help) {
	//////////////////////////////////////////////////////////////////////////////
	// This is a list of all known gedcom tags.  We list them all here so that
	// xgettext() may find them.
	//
	// Tags such as BIRT:PLAC are only used as labels, and do not require help
	// text.  These are only used for translating labels.
	//
	// Tags such as _BIRT_CHIL are pseudo-tags, used to create family events.
	//////////////////////////////////////////////////////////////////////////////

case 'ABBR':
	$title=i18n::translate('Abbreviation');
	$text=i18n::translate('Use this field for storing an abbreviated version of a title.  This field is used in conjunction with the title field on sources.  By default <b>webtrees</b> will first use the title and then the abbreviated title.<br /><br />According to the GEDCOM 5.5 specification, "this entry is to provide a short title used for sorting, filing, and retrieving source records (pg 62)."<br /><br />In <b>webtrees</b> the abbreviated title is optional, but in other genealogical programs it is required.');
	break;

case 'ADDR':
	$title=i18n::translate('Address');
	$text=i18n::translate('Enter the address into the field just as you would write it on an envelope.<br /><br />Leave this field blank if you do not want to include an address.');
	break;

case 'ADR1':	
	$title=i18n::translate('Address line 1');
	$text='';
	break;

case 'ADR2':	
	$title=i18n::translate('Address line 2');
	$text='';
	break;

case 'ADOP':
	$title=i18n::translate('Adoption');
	$text='';
	break;

case 'AFN':	
	$title=i18n::translate('Ancestral File Number');
	$text='';
	break;

case 'AGE':	
	$title=i18n::translate('Age');
	$text='';
	break;

case 'AGNC':
	$title=i18n::translate('Agency');
	$text=i18n::translate('The organization, institution, corporation, person, or other entity that has authority.<br /><br />For example, an employer of a person, or a church that administered rites or events, or an organization responsible for creating and/or archiving records.');
	break;

case 'ALIA':	
	$title=i18n::translate('Alias');
	$text='';
	break;

case 'ANCE':	
	$title=i18n::translate('Generations of ancestors');
	$text='';
	break;

case 'ANCI':	
	$title=i18n::translate('Ancestors interest');
	$text='';
	break;

case 'ANUL':	
	$title=i18n::translate('Annulment');
	$text='';
	break;

case 'ASSO':
	$title=i18n::translate('Associate');
	$text=i18n::translate('Enter associate GEDCOM ID.');
	break;

case 'AUTH':	
	$title=i18n::translate('Author');
	$text='';
	break;

case 'BAPL':	
	$title=i18n::translate('LDS baptism');
	$text='';
	break;

case 'BAPM':	
	$title=i18n::translate('Baptism');
	// I18N: This is a very short abbreviation for the label "Baptism", to be used on genealogy charts
	$abbrev=i18n::translate('ABBREV_BAPM');
	$text='';
	break;

case 'BAPM:DATE':	
	$title=i18n::translate('Date of baptism');
	$text='';
	break;

case 'BAPM:PLAC':	
	$title=i18n::translate('Place of baptism');
	$text='';
	break;

case 'BAPM:SOUR':	
	$title=i18n::translate('Source for baptism');
	$text='';
	break;

case 'BARM':	
	$title=i18n::translate('Bar mitzvah');
	$text='';
	break;

case 'BARM:DATE':	
	$title=i18n::translate('Date of bar mitzvah');
	$text='';
	break;

case 'BARM:PLAC':	
	$title=i18n::translate('Place of bar mitzvah');
	$text='';
	break;

case 'BARM:SOUR':	
	$title=i18n::translate('Source for bar mitzvah');
	$text='';
	break;

case 'BASM':	
	$title=i18n::translate('Bas mitzvah');
	$text='';
	break;

case 'BASM:DATE':	
	$title=i18n::translate('Date of bas mitzvah');
	$text='';
	break;

case 'BASM:PLAC':	
	$title=i18n::translate('Place of bas mitzvah');
	$text='';
	break;

case 'BASM:SOUR':	
	$title=i18n::translate('Source for bas mitzvah');
	$text='';
	break;

case 'BIRT':	
	$title=i18n::translate('Birth');
	// I18N: This is a very short abbreviation for the label "Birth", to be used on genealogy charts
	$abbr=i18n::translate('ABBREV_BIRT');
	$text='';
	break;

case 'BIRT:DATE':	
	$title=i18n::translate('Date of birth');
	$text='';
	break;

case 'BIRT:PLAC':	
	$title=i18n::translate('Place of birth');
	$text='';
	break;

case 'BIRT:SOUR':	
	$title=i18n::translate('Source for birth');
	$text='';
	break;

case 'BLES':	
	$title=i18n::translate('Blessing');
	$text='';
	break;

case 'BLOB':	
	$title=i18n::translate('Binary Data Object');
	$text='';
	break;

case 'BURI':	
	$title=i18n::translate('Burial');
	// I18N: This is a very short abbreviation for the label "Burial", to be used on genealogy charts
	$abbr=i18n::translate('ABBREV_BURI');
	$text='';
	break;

case 'BURI:DATE':	
	$title=i18n::translate('Date of burial');
	$text='';
	break;

case 'BURI:PLAC':	
	$title=i18n::translate('Place of burial');
	$text='';
	break;

case 'BURI:SOUR':	
	$title=i18n::translate('Source for burial');
	$text='';
	break;

case 'CALN':	
	$title=i18n::translate('Call number');
	$text='The number used by a repository to identify the specific items in its collections.';
	break;

case 'CAST':	
	$title=i18n::translate('Caste');
	$text='The name of an individual\'s rank or status in society which is sometimes based on racial or religious differences, or differences in wealth, inherited rank, profession, occupation, etc.';
	break;

case 'CAUS':	
	$title=i18n::translate('Cause');
	$text='A description of the cause of the associated event or fact, such as the cause of death.';
	break;

case 'CEME':
	$title=i18n::translate('Cemetery');
	$text=i18n::translate('Enter the name of the cemetery or other resting place where individual is buried.');
	break;

case 'CENS':	
	$title=i18n::translate('Census');
	$text='';
	break;

case 'CHAN':	
	$title=i18n::translate('Last change');
	$text='';
	break;

case 'CHAR':	
	$title=i18n::translate('Character set');
	$text='';
	break;

case 'CHIL':	
	$title=i18n::translate('Child');
	$text='';
	break;

case 'CHR':	
	$title=i18n::translate('Christening');
	// I18N: This is a very short abbreviation for the label "Christening", to be used on genealogy charts
	$abbr=i18n::translate('ABBREV_CHR');
	$text='';
	break;

case 'CHR:DATE':	
	$title=i18n::translate('Date of christening');
	$text='';
	break;

case 'CHR:PLAC':	
	$title=i18n::translate('Place of christening');
	$text='';
	break;

case 'CHR:SOUR':	
	$title=i18n::translate('Source for christening');
	$text='';
	break;

case 'CHRA':	
	$title=i18n::translate('Adult christening');
	$text='';
	break;

case 'CITN':	
	$title=i18n::translate('Citizenship');
	$text='';
	break;

case 'CITY':	
	$title=i18n::translate('City');
	$text='';
	break;

case 'COMM':	
	$title=i18n::translate('Comment');
	$text='';
	break;

case 'CONC':	
	$title=i18n::translate('Concatenation');
	$text='';
	break;

case 'CONT':	
	$title=i18n::translate('Continued');
	$text='';
	break;

case 'CONF':	
	$title=i18n::translate('Confirmation');
	$text='';
	break;

case 'CONF:DATE':	
	$title=i18n::translate('Date of confirmation');
	$text='';
	break;

case 'CONF:PLAC':	
	$title=i18n::translate('Place of confirmation');
	$text='';
	break;

case 'CONF:SOUR':	
	$title=i18n::translate('Source for confirmation');
	$text='';
	break;

case 'CONL':	
	$title=i18n::translate('LDS confirmation');
	$text='';
	break;

case 'COPR':	
	$title=i18n::translate('Copyright');
	$text='';
	break;

case 'CORP':	
	$title=i18n::translate('Corporation');
	$text='A name of an institution, agency, corporation, or company.';
	break;

case 'CREM':	
	$title=i18n::translate('Cremation');
	$text='Disposal of the remains of a person\'s body by fire.';
	break;

case 'CTRY':	
	$title=i18n::translate('Country');
	$text='';
	break;

case 'DATA':	
	$title=i18n::translate('Data');
	$text='';
	break;

case 'DATA:DATE':	
	$title=i18n::translate('Date of entry in original source');
	$text='';
	break;

case 'DATE':	
	$title=i18n::translate('Date');
	$text='';
	break;

case 'DEAT':	
	$title=i18n::translate('Death');
	// I18N: This is a very short abbreviation for the label "Death", to be used on genealogy charts
	$abbr=i18n::translate('ABBREV_DEAT');
	$text='';
	break;

case 'DEAT:DATE':	
	$title=i18n::translate('Date of death');
	$text='';
	break;

case 'DEAT:PLAC':	
	$title=i18n::translate('Place of death');
	$text='';
	break;

case 'DEAT:SOUR':	
	$title=i18n::translate('Source for death');
	$text='';
	break;

case 'DESC':	
	$title=i18n::translate('Descendants');
	$text='Pertaining to offspring of an individual.';
	break;

case 'DESI':	
	$title=i18n::translate('Descendants interest');
	$text='';
	break;

case 'DEST':	
	$title=i18n::translate('Destination');
	$text='';
	break;

case 'DIV':	
	$title=i18n::translate('Divorce');
	$text='';
	break;

case 'DIVF':	
	$title=i18n::translate('Divorce filed');
	$text='';
	break;

case 'DSCR':	
	$title=i18n::translate('Description');
	$text='';
	break;

case 'EDUC':	
	$title=i18n::translate('Education');
	$text='';
	break;

case 'EMAI':
	$title=i18n::translate('Email address');
	$text='';
	break;

case 'EMAIL':
	$title=i18n::translate('Email address');
	$text=i18n::translate('Enter the email address.<br /><br />An example email address looks like this: <b>name@hotmail.com</b>  Leave this field blank if you do not want to include an email address.');
	break;

case 'EMAL':	
	$title=i18n::translate('Email address');
	$text='';
	break;

case 'EMIG':	
	$title=i18n::translate('Emigration');
	$text='';
	break;

case 'ENDL':	
	$title=i18n::translate('LDS endowment');
	$text='';
	break;

case 'ENGA':	
	$title=i18n::translate('Engagement');
	$text='';
	break;

case 'ENGA:DATE':	
	$title=i18n::translate('Date of engagement');
	$text='';
	break;

case 'ENGA:PLAC':	
	$title=i18n::translate('Place of engagement');
	$text='';
	break;

case 'ENGA:SOUR':	
	$title=i18n::translate('Source for engagement');
	$text='';
	break;

case 'EVEN':	
	$title=i18n::translate('Event');
	$text='';
	break;

case 'FACT':	
	$title=i18n::translate('Fact');
	$text='';
	break;

case 'FAM':	
	$title=i18n::translate('Family');
	$text='';
	break;

case 'FAMC':	
	$title=i18n::translate('Family as a child');
	$text='';
	break;

case 'FAMC:HUSB:BIRT:PLAC':	
	$title=i18n::translate('Father\'s birthplace');
	$text='';
	break;

case 'FAMC:HUSB:FAMC:HUSB:GIVN':	
	$title=i18n::translate('Paternal grandfather\'s given name');
	$text='';
	break;

case 'FAMC:HUSB:FAMC:WIFE:GIVN':	
	$title=i18n::translate('Paternal grandmother\'s given name');
	$text='';
	break;

case 'FAMC:HUSB:GIVN':	
	$title=i18n::translate('Father\'s given name');
	$text='';
	break;

case 'FAMC:HUSB:OCCU':	
	$title=i18n::translate('Father\'s occupation');
	$text='';
	break;

case 'FAMC:HUSB:OCCU':	
	$title=i18n::translate('Father\'s surname');
	$text='';
	break;

case 'FAMC:MARR:PLAC':	
	$title=i18n::translate('Parents\' marriage place');
	$text='';
	break;

case 'FAMC:MARR:PLAC':	
	$title=i18n::translate('Mother\'s birthplace');
	$text='';
	break;

case 'FAMC:WIFE:FAMC:HUSB:GIVN':	
	$title=i18n::translate('Maternal grandfather\'s given name');
	$text='';
	break;

case 'FAMC:WIFE:FAMC:WIFE:GIVN':	
	$title=i18n::translate('Maternal grandmother\'s Given Name');
	$text='';
	break;

case 'FAMC:WIFE:GIVN':	
	$title=i18n::translate('Mother\'s given name');
	$text='';
	break;

case 'FAMC:WIFE:SURN':	
	$title=i18n::translate('Mother\'s surname');
	$text='';
	break;

case 'FAMF':	
	$title=i18n::translate('Family file');
	$text='';
	break;

case 'FAMS':	
	$title=i18n::translate('Family as a spouse');
	$text='';
	break;

case 'FAMS:CENS:DATE':	
	$title=i18n::translate('Spouse census date');
	$text='';
	break;

case 'FAMS:CENS:PLAC':	
	$title=i18n::translate('Spouse census place');
	$text='';
	break;

case 'FAMS:CHIL:BIRT:PLAC':	
	$title=i18n::translate('Child\'s birth place');
	$text='';
	break;

case 'FAMS:DIV:DATE':	
	$title=i18n::translate('Spouse divorce date');
	$text='';
	break;

case 'FAMS:DIV:PLAC':	
	$title=i18n::translate('Spouse divorce place');
	$text='';
	break;

case 'FAMS:MARR:DAT':	
	$title=i18n::translate('Marriage date');
	$text='';
	break;

case 'FAMS:MARR:PLAC':	
	$title=i18n::translate('Marriage place');
	$text='';
	break;

case 'FAMS:NOTE':	
	$title=i18n::translate('Spouse note');
	$text='';
	break;

case 'FAMS:SLGS:DATE':	
	$title=i18n::translate('LDS spouse sealing date');
	$text='';
	break;

case 'FAMS:SLGS:PLAC':	
	$title=i18n::translate('LDS spouse sealing place');
	$text='';
	break;

case 'FAMS:SLGS:TEMP':	
	$title=i18n::translate('LDS spouse sealing temple');
	$text='';
	break;

case 'FAMS:SPOUSE:BIRT:PLAC':	
	$title=i18n::translate('Spouse\'s birth place');
	$text='';
	break;

case 'FAMS:SPOUSE:DEAT:PLAC':	
	$title=i18n::translate('Spouse\'s death place');
	$text='';
	break;

case 'FAX':
	$title=i18n::translate('Fax');
	$text=i18n::translate('Enter the FAX number including the country and area code.<br /><br />Leave this field blank if you do not want to include a FAX number.  For example, a number in Germany might be +49 25859 56 76 89 and a number in USA or Canada might be +1 888 555-1212.');
	break;

case 'FCOM':	
	$title=i18n::translate('First communion');
	$text='';
	break;

case 'FCOM:DATE':	
	$title=i18n::translate('Date of first communion');
	$text='';
	break;

case 'FCOM:PLAC':	
	$title=i18n::translate('Place of first communion');
	$text='';
	break;

case 'FCOM:SOUR':	
	$title=i18n::translate('Source for first communion');
	$text='';
	break;

case 'FILE':
	$title=i18n::translate('Filename');
	$text=i18n::translate('This is the most important field in the multimedia object record.  It indicates which file to use. At the very minimum, you need to enter the file\'s name.  Depending on your settings, more information about the file\'s location may be helpful.<br /><br />You can use the <b>Find Media</b> link to help you locate media items that have already been uploaded to the site.<br /><br />See <a href="readme.txt" target="_blank"><b>Readme.txt</b></a> for more information.');
	break;

case 'FONE':	
	$title=i18n::translate('Phonetic');
	$text='';
	break;

case 'FORM':
	$title=i18n::translate('Format');
	$text=i18n::translate('This is an optional field that can be used to enter the file format of the multimedia object.  Some genealogy programs may look at this field to determine how to handle the item.  However, since media do not transfer across computer systems very well, this field is not very important.');
	break;

case 'GEDC':	
	$title=i18n::translate('Gedcom');
	$text='';
	break;

case 'GIVN':
	$title=i18n::translate('Given names');
	$text=i18n::translate('In this field you should enter the given names for the person.  As an example, in the name "John Robert Finlay", the given names that should be entered here are "John Robert"');
	break;

case 'GRAD':	
	$title=i18n::translate('Graduation');
	$text='';
	break;

case 'HEAD':	
	$title=i18n::translate('Header');
	$text='';
	break;

case 'HUSB':	
	$title=i18n::translate('Husband');
	$text='';
	break;

case 'IDNO':	
	$title=i18n::translate('Identification number');
	$text='';
	break;

case 'IMMI':	
	$title=i18n::translate('Immigration');
	$text='';
	break;

case 'INDI':	
	$title=i18n::translate('Individual');
	$text='';
	break;

case 'INFL':	
	$title=i18n::translate('Infant');
	$text='';
	break;

case 'LANG':	
	$title=i18n::translate('Language');
	$text='';
	break;

case 'LATI':	
	$title=i18n::translate('Latitude');
	$text='';
	break;

case 'LEGA':	
	$title=i18n::translate('Legatee');
	$text='';
	break;

case 'LONG':	
	$title=i18n::translate('Longitude');
	$text='';
	break;

case 'MAP':	
	$title=i18n::translate('Map');
	$text='';
	break;

case 'MARB':	
	$title=i18n::translate('Marriage banns');
	$text='';
	break;

case 'MARB:DATE':	
	$title=i18n::translate('Date of marriage banns');
	$text='';
	break;

case 'MARB:PLAC':	
	$title=i18n::translate('Place of marriage banns');
	$text='';
	break;

case 'MARB:SOUR':	
	$title=i18n::translate('Source for marriage banns');
	$text='';
	break;

case 'MARC':	
	$title=i18n::translate('Marriage contract');
	$text='';
	break;

case 'MARL':	
	$title=i18n::translate('Marriage licence');
	$text='';
	break;

case 'MARR':	
	$title=i18n::translate('Marriage');
	// I18N: This is a very short abbreviation for the label "Marriage", to be used on genealogy charts
	$abbr=i18n::translate('ABBREV_MARR');
	$text='';
	break;

case 'MARR:':	
	$title=i18n::translate('Date of marriage date');
	$text='';
	break;

case 'MARR:PLAC':	
	$title=i18n::translate('Place of marriage');
	$text='';
	break;

case 'MARR:SOUR':	
	$title=i18n::translate('Source for marriage');
	$text='';
	break;

case 'MARR_CIVIL':	
	$title=i18n::translate('Civil marriage');
	$text='';
	break;

case 'MARR_PARTNERS':	
	$title=i18n::translate('Registered partnership');
	$text='';
	break;

case 'MARR_RELIGIOUS':	
	$title=i18n::translate('Religious marriage');
	$text='';
	break;

case 'MARR_UNKNOWN':	
	$title=i18n::translate('Marriage type unknown');
	$text='';
	break;

case 'MARS':	
	$title=i18n::translate('Marriage settlement');
	$text='';
	break;

case 'MEDI':	
	$title=i18n::translate('Media type');
	$text='';
	break;

case 'NAME':
	$title=i18n::translate('Name');
	$text=i18n::translate('This is the most important field in a person\'s Name record.<br /><br />This field should be filled automatically as the other fields are filled in, but it is provided so that you can edit the information according to your personal preference.<br /><br />The name in this field should be entered according to the GEDCOM 5.5.1 standards with the surname surrounded by forward slashes "/".  As an example, the name "John Robert Finlay Jr." should be entered like this: "John Robert /Finlay/ Jr.".');
	break;

case 'NAME:FONE':	
	$title=i18n::translate('Phonetic name');
	$text='';
	break;

case 'NAME:_HEB':	
	$title=i18n::translate('Name in Hebrew');
	$text='';
	break;

case 'NATI':	
	$title=i18n::translate('Nationality');
	$text='';
	break;

case 'NATU':	
	$title=i18n::translate('Naturalization');
	$text='';
	break;

case 'NCHI':
	$title=i18n::translate('Number of children');
	$text=i18n::translate('Enter the number of children for this individual or family. This is an optional field.');
	break;

case 'NICK':
	$title=i18n::translate('Nickname');
	$text=i18n::translate('In this field you should enter any nicknames for the person.<br />This is an optional field.<br /><br />Ways to add a nickname:<ul><li>Select <b>modify name</b> then enter nickname and save</li><li>Select <b>add new name</b> then enter nickname AND name and save</li><li>Select <b>edit GEDCOM record</b> to add multiple [2&nbsp;NICK] records subordinate to the main [1&nbsp;NAME] record.</li></ul>');
	break;

case 'NMR':	
	$title=i18n::translate('Number of marriages');
	$text='';
	break;

case 'NOTE':
	$title=i18n::translate('Note');
	$text=i18n::translate('Notes are free-form text and will appear in the Fact Details section of the page.');
	break;

case 'NPFX':
	$title=i18n::translate('Name prefix');
	$text=i18n::translate('This optional field allows you to enter a name prefix such as "Dr." or "Adm."');
	break;

case 'NSFX':
	$title=i18n::translate('Name suffix');
	$text=i18n::translate('In this optional field you should enter the name suffix for the person.  Examples of name suffixes are "Sr.", "Jr.", and "III".');
	break;

case 'OBJE':	
	$title=i18n::translate('Multimedia object');
	$text='';
	break;

case 'OCCU':	
	$title=i18n::translate('Occupation');
	$text='';
	break;

case 'ORDI':	
	$title=i18n::translate('Ordinance');
	$text='';
	break;

case 'ORDN':	
	$title=i18n::translate('Ordination');
	$text='';
	break;

case 'PAGE':
	$title=i18n::translate('Citation details');
	$text=i18n::translate('In the Citation Details field you would enter the page number or other information that might help someone find the information in the source.');
	break;

case 'PEDI':
	$title=i18n::translate('Pedigree');
	$text=i18n::translate('This field describes the relationship of the child to its family.  The possibilities are:<ul><li><b>unknown</b>&nbsp;&nbsp;&nbsp;The child\'s relationship to its family cannot be determined.  When this option is selected, the Pedigree field will not be copied into the database.<br /><br /></li><li><b>Birth</b>&nbsp;&nbsp;&nbsp;This option indicates that the child is related to its family by birth.<br /><br /></li><li><b>Adopted</b>&nbsp;&nbsp;&nbsp;This option indicates that the child was adopted by its family.  This does <i>not</i> indicate that there is no blood relationship between the child and its family; it shows that the child was adopted by the family in question sometime after the child\'s birth.<br /><br /></li><li><b>Foster</b>&nbsp;&nbsp;&nbsp;This option indicates that the child is a foster child of the family.  Usually, there is no blood relationship between the child and its family.<br /><br /></li><li><b>Sealing</b>&nbsp;&nbsp;&nbsp;The child was sealed to its family in an LDS <i>sealing</i> ceremony.  A child sealing is performed when the parents were sealed to each other after the birth of the child.  Children born after the parents\' sealing are automatically sealed to the family.<br /><br /></li></ul>');
	break;

case 'PHON':
	$title=i18n::translate('Phone');
	$text=i18n::translate('Enter the phone number including the country and area code.<br /><br />Leave this field blank if you do not want to include a phone number.  For example, a number in Germany might be +49 25859 56 76 89 and a number in USA or Canada might be +1 888 555-1212.');
	break;

case 'PLAC':
	$title=i18n::translate('Place');
	$text=i18n::translate('Places should be entered according to the standards for genealogy.  In genealogy, places are recorded with the most specific information about the place first and then working up to the least specific place last, using commas to separate the different place levels.  The level at which you record the place information should represent the levels of government or church where vital records for that place are kept.<br /><br />For example, a place like Salt Lake City would be entered as "Salt Lake City, Salt Lake, Utah, USA".<br /><br />Let\'s examine each part of this place.  The first part, "Salt Lake City," is the city or township where the event occurred.  In some countries, there may be municipalities or districts inside a city which are important to note.  In that case, they should come before the city.  The next part, "Salt Lake," is the county.  "Utah" is the state, and "USA" is the country.  It is important to note each place because genealogical records are kept by the governments of each level.<br /><br />If a level of the place is unknown, you should leave a space between the commas.  Suppose, in the example above, you didn\'t know the county for Salt Lake City.  You should then record it like this: "Salt Lake City, , Utah, USA".  Suppose you only know that a person was born in Utah.  You would enter the information like this: ", , Utah, USA".  <br /><br />You can use the <b>Find Place</b> link to help you find places that already exist in the database.');
	break;

case 'PLAC:FONE':	
	$title=i18n::translate('Phonetic place');
	$text='';
	break;

case 'PLAC:ROMN':	
	$title=i18n::translate('Romanized place');
	$text='';
	break;

case 'PLAC:_HEB':	
	$title=i18n::translate('Place in Hebrew');
	$text='';
	break;

case 'POST':	
	$title=i18n::translate('Postal code');
	$text='';
	break;

case 'PROB':	
	$title=i18n::translate('Probate');
	$text='';
	break;

case 'PROP':	
	$title=i18n::translate('Property');
	$text='';
	break;

case 'PUBL':	
	$title=i18n::translate('Publication');
	$text='';
	break;

case 'QUAY':
	$title=i18n::translate('Quality of data');
	$text=i18n::translate('You would use this field to record the quality or reliability of the data found in this source.  Many genealogy applications use a number in the field. <b>3</b> might mean that the data is a primary source, <b>2</b> might mean that it was a secondary source, <b>1</b> might mean the information is questionable, and <b>0</b> might mean that the source is unreliable.');
	break;

case 'REFN':	
	$title=i18n::translate('Reference number');
	$text='';
	break;

case 'RELA':
	$title=i18n::translate('Relationship');
	$text=i18n::translate('Select a relationship name from the list. Selecting <b>Godfather</b> means: <i>This associate is the Godfather of the current individual</i>.');
	break;

case 'RELI':	
	$title=i18n::translate('Religion');
	$text='';
	break;

case 'REPO':	
	$title=i18n::translate('Repository');
	$text='';
	break;

case 'RESI':	
	$title=i18n::translate('Residence');
	$text='';
	break;

case 'RESN':
	$title=i18n::translate('Restriction');
	$text=
		i18n::translate('Apart from general privacy settings, <b>webtrees</b> has the ability to set restrictions on viewing and editing fact information for individuals and families. The restrictions can be set by anyone who is allowed to edit the information, unless privacy or formerly set restrictions prohibit this.').
		'<br /><br />'.i18n::translate('The following values can be used:').
		'<br /><ul><li><b>'.i18n::translate('None').'</b><br />'.i18n::translate('Site administrators, GEDCOM administrators, and users who have rights to edit can change the information. Fact information can be viewed according to privacy settings as applied by the administrator.').
		'</li><li><b>'.i18n::translate('Do not change').'</b><br />'.i18n::translate('This setting has no influence on the visibility of the fact data. It restricts editing rights to site administrators and GEDCOM administrators. If the information applies to the user himself, he can also view and, assuming he has editing rights, edit it.').
		'</li><li><b>'.i18n::translate('Privacy').'</b><br />'.i18n::translate('Site administrators and GEDCOM administrators can view and edit the information. If the information applies to the user himself, he can also view and, assuming he has editing rights, edit it. It will be hidden from all other users regardless of their login status.').
		'</li><li><b>'.i18n::translate('Confidential').'</b><br />'.i18n::translate('Only site administrators and GEDCOM administrators can view and edit the information. It will be hidden from all other users regardless of their login status.').
		'</li></ul><br /><table><tr><th></th><th colspan="2">'.i18n::translate('Admin').'</th><th colspan="2">'.i18n::translate('Owner').'</th><th colspan="2">'.i18n::translate('Others').
		'</th></tr><tr><th></th><th>R</th><th>W</th><th>R</th><th>W</th><th>R</th><th>W</th></tr>'.
		'<tr><td><img src="images/RESN_none.gif" alt="" /> '.i18n::translate('None').'</td><th><img src="images/checked.gif" alt="" /></th><th><img src="images/checked.gif" alt="" /></th><th><img src="images/checked_qm.gif" alt="" /></th><th><img src="images/checked_qm.gif" alt="" /></th><th><img src="images/checked_qm.gif" alt="" /></th><th><img src="images/checked_qm.gif" alt="" /></th></tr><tr><td><img src="images/RESN_locked.gif" alt="" /> '.
		i18n::translate('Do not change').'</td><th><img src="images/checked.gif" alt="" /></th><th><img src="images/checked.gif" alt="" /></th><th><img src="images/checked_qm.gif" alt="" /></th><th><img src="images/checked_qm.gif" alt="" /></th><th><img src="images/checked_qm.gif" alt="" /></th><th><img src="images/forbidden.gif" alt="" /></th></tr><tr><td><img src="images/RESN_privacy.gif" alt="" /> '.
		i18n::translate('Privacy').'</td><th><img src="images/checked.gif" alt="" /></th><th><img src="images/checked.gif" alt="" /></th><th><img src="images/checked_qm.gif" alt="" /></th><th><img src="images/checked_qm.gif" alt="" /></th><th><img src="images/forbidden.gif" alt="" /></th><th><img src="images/forbidden.gif" alt="" /></th></tr><tr><td><img src="images/RESN_confidential.gif" alt="" /> '.
		i18n::translate('Confidential').'</td><th><img src="images/checked.gif" alt="" /></th><th><img src="images/checked.gif" alt="" /></th><th><img src="images/forbidden.gif" alt="" /></th><th><img src="images/forbidden.gif" alt="" /></th><th><img src="images/forbidden.gif" alt="" /></th><th><img src="images/forbidden.gif" alt="" /></th></tr></table>'.
		'<ul><li>R : '.i18n::translate('can read').'</li><li>W : '.i18n::translate('can edit').'</li><li><img src="images/checked_qm.gif" alt="" /> : '.i18n::translate('depends on global privacy settings').'</li></ul>';
	break;

case 'RETI':	
	$title=i18n::translate('Retirement');
	$text='';
	break;

case 'RFN':	
	$title=i18n::translate('Record file number');
	$text='';
	break;

case 'RIN':	
	$title=i18n::translate('Record ID number');
	$text='';
	break;

case 'ROLE':	
	$title=i18n::translate('Role');
	$text='';
	break;

case 'ROMN':
	$title=i18n::translate('Romanized');
	$text=i18n::translate('In many cultures it is customary to have a traditional name spelled in the traditional characters and also a romanized version of the name as it would be spelled or pronounced in languages based on the Latin alphabet, such as English.<br /><br />If you prefer to use a non-Latin alphabet such as Hebrew, Greek, Russian, Chinese, or Arabic to enter the name in the standard name fields, then you can use this field to enter the same name using the Latin alphabet.  Both versions of the name will appear in lists and charts.<br /><br />Although this field is labelled "Romanized", it is not restricted to containing only characters based on the Latin alphabet.  This might be of use with Japanese names, where three different alphabets may occur.');
	break;

case 'SERV':	
	$title=i18n::translate('Remote server');
	$text='';
	break;

case 'SEX':
	$title=i18n::translate('Gender');
	$text=i18n::translate('Choose the appropriate gender from the drop-down list.  The <b>unknown</b> option indicates that the gender is unknown.');
	break;

case 'SHARED_NOTE':
	$title=i18n::translate('Shared note');
	$text=i18n::translate('Shared Notes are free-form text and will appear in the Fact Details section of the page.<br /><br />Each shared note can be linked to more than one person, family, source, or event.');
	break;

case 'SLGC':	
	$title=i18n::translate('LDS child sealing');
	$text='';
	break;

case 'SLGS':	
	$title=i18n::translate('LDS spouse sealing');
	$text='';
	break;

case 'SOUR':
	$title=i18n::translate('Source');
	$text=i18n::translate('This field allows you to change the source record that this fact\'s source citation links to.  This field takes a Source ID.  Beside the field will be listed the title of the current source ID.  Use the <b>Find ID</b> link to look up the source\'s ID number.  To remove the entire citation, make this field blank.');
	break;

case 'SPFX':
	$title=i18n::translate('Surname prefix');
	$text=i18n::translate('Enter or select from the list words that precede the main part of the Surname.  Examples of such words are <b>von</b> Braun, <b>van der</b> Kloot, <b>de</b> Graaf, etc.');
	break;

case 'SSN':	
	$title=i18n::translate('Social Security Number');
	$text='';
	break;

case 'STAE':	
	$title=i18n::translate('State');
	$text='';
	break;

case 'STAT':
	$title=i18n::translate('Status');
	$text=i18n::translate('This is an optional status field and is used mostly for LDS ordinances as they are run through the TempleReady program.');
	break;

case 'STAT:DATE':	
	$title=i18n::translate('Status change date');
	$text='';
	break;

case 'SUBM':	
	$title=i18n::translate('Submitter');
	$text='';
	break;

case 'SUBN':	
	$title=i18n::translate('Submission');
	$text='';
	break;

case 'SURN':
	$title=i18n::translate('Surname');
	$text=i18n::translate('In this field you should enter the surname for the person.  As an example, in the name "John Robert Finlay", the surname that should be entered here is "Finlay"<br /><br />Individuals with multiple surnames, common in Spain and Portugal, should separate the surnames with a comma.  This indicates that the person is to be listed under each of the names.  For example, the surname "Cortes,Vega" will be listed under both <b>C</b> and <b>V</b>, whereas the surname "Cortes Vega" will only be listed under <b>C</b>.');
	break;

case 'TEMP':
	$title=i18n::translate('Temple');
	$text=i18n::translate('For LDS ordinances, this field records the Temple where it was performed.');
	break;

case 'TEXT':
	$title=i18n::translate('Text');
	$text=i18n::translate('In this field you would enter the citation text for this source.  Examples of data may be a transcription of the text from the source, or a description of what was in the citation.');
	break;

case 'TIME':
	$title=i18n::translate('Time');
	$text=i18n::translate('Enter the time for this event in 24-hour format with leading zeroes. Midnight is 00:00. Examples: 04:50 13:00 20:30.');
	break;

case 'TITL':
	$title=i18n::translate('Title');
	$text=i18n::translate('Enter a title for the item you are editing.  If this is a title for a multimedia item, enter a descriptive title that will identify that item to the user.');
	break;

case 'TITL:FONE':	
	$title=i18n::translate('Phonetic title');
	$text='';
	break;

case 'TITL:ROMN':	
	$title=i18n::translate('Romanized title');
	$text='';
	break;

case 'TITL:_HEB':	
	$title=i18n::translate('Title in Hebrew');
	$text='';
	break;

case 'TRLR':	
	$title=i18n::translate('Trailer');
	$text='';
	break;

case 'TYPE':
	$title=i18n::translate('Type');
	$text=i18n::translate('The Type field is used to enter additional information about the item.  In most cases, the field is completely free-form, and you can enter anything you want.');
	break;

case 'URL':
	$title=i18n::translate('Web URL');
	$text=i18n::translate('Enter the URL address including the http://.<br /><br />An example URL looks like this: <b>http://www.webtrees.net/</b> Leave this field blank if you do not want to include a URL.');
	break;

case 'VERS':	
	$title=i18n::translate('Version');
	$text='';
	break;

case 'WIFE':	
	$title=i18n::translate('Wife');
	$text='';
	break;

case 'WILL':	
	$title=i18n::translate('Will');
	$text='';
	break;

case 'WWW':	
	$title=i18n::translate('Web home page');
	$text='';
	break;

case '_ADOP_CHIL':	
	$title=i18n::translate('Adoption of a child');
	$text='';
	break;

case '_ADOP_COUS':	
	$title=i18n::translate('Adoption of a first cousin');
	$text='';
	break;

case '_ADOP_FSIB':	
	$title=i18n::translate('Adoption of father\'s sibling');
	$text='';
	break;

case '_ADOP_GCHI':	
	$title=i18n::translate('Adoption of a grandchild');
	$text='';
	break;

case '_ADOP_GGCH':	
	$title=i18n::translate('Adoption of a great-grandchild');
	$text='';
	break;

case '_ADOP_HSIB':	
	$title=i18n::translate('Adoption of half-sibling');
	$text='';
	break;

case '_ADOP_MSIB':	
	$title=i18n::translate('Adoption of mother\'s sibling');
	$text='';
	break;

case '_ADOP_NEPH':	
	$title=i18n::translate('Adoption of a nephew or niece');
	$text='';
	break;

case '_ADOP_SIBL':	
	$title=i18n::translate('Adoption of sibling');
	$text='';
	break;

case '_ADPF':	
	$title=i18n::translate('Adopted by father');
	$text='';
	break;

case '_ADPM':	
	$title=i18n::translate('Adopted by mother');
	$text='';
	break;

case '_AKA':	
	$title=i18n::translate('Also known as');
	$text='';
	break;

case '_AKAN':	
	$title=i18n::translate('Also known as');
	$text='';
	break;

case '_BAPM_CHIL':	
	$title=i18n::translate('Baptism of a child');
	$text='';
	break;

case '_BAPM_COUS':	
	$title=i18n::translate('Baptism of a first cousin');
	$text='';
	break;

case '_BAPM_FSIB':	
	$title=i18n::translate('Baptism of father\'s sibling');
	$text='';
	break;

case '_BAPM_GCHI':	
	$title=i18n::translate('Baptism of a grandchild');
	$text='';
	break;

case '_BAPM_GGCH':	
	$title=i18n::translate('Baptism of a great-grandchild');
	$text='';
	break;

case '_BAPM_HSIB':	
	$title=i18n::translate('Baptism of half-sibling');
	$text='';
	break;

case '_BAPM_MSIB':	
	$title=i18n::translate('Baptism of mother\'s sibling');
	$text='';
	break;

case '_BAPM_NEPH':	
	$title=i18n::translate('Baptism of a nephew or niece');
	$text='';
	break;

case '_BAPM_SIBL':	
	$title=i18n::translate('Baptism of sibling');
	$text='';
	break;

case '_BIBL':	
	$title=i18n::translate('Bibliography');
	$text='';
	break;

case '_BIRT_CHIL':
	$tag_name=i18n::translate('_BIRT_CHIL');
	$title=i18n::translate('Birth of a child');
	$text='';
	break;

case '_BIRT_COUS':	
	$title=i18n::translate('Birth of a first cousin');
	$text='';
	break;

case '_BIRT_FSIB':	
	$title=i18n::translate('Birth of father\'s sibling');
	$text='';
	break;

case '_BIRT_GCHI':	
	$title=i18n::translate('Birth of a grandchild');
	$text='';
	break;

case '_BIRT_GGCH':	
	$title=i18n::translate('Birth of a great-grandchild');
	$text='';
	break;

case '_BIRT_HSIB':	
	$title=i18n::translate('Birth of half-sibling');
	$text='';
	break;

case '_BIRT_MSIB':	
	$title=i18n::translate('Birth of mother\'s sibling');
	$text='';
	break;

case '_BIRT_NEPH':	
	$title=i18n::translate('Birth of a nephew or niece');
	$text='';
	break;

case '_BIRT_SIBL':	
	$title=i18n::translate('Birth of sibling');
	$text='';
	break;

case '_BRTM':	
	$title=i18n::translate('Brit mila');
	$text='';
	break;

case '_BRTM:DATE':	
	$title=i18n::translate('Date of brit mila');
	$text='';
	break;

case '_BRTM:PLAC':	
	$title=i18n::translate('Place of brit mila');
	$text='';
	break;

case '_BRTM:SOUR':	
	$title=i18n::translate('Source for brit mila');
	$text='';
	break;

case '_BURI_CHIL':	
	$title=i18n::translate('Burial of a child');
	$text='';
	break;

case '_BURI_COUS':	
	$title=i18n::translate('Burial of a first cousin');
	$text='';
	break;

case '_BURI_FATH':	
	$title=i18n::translate('Burial of father');
	$text='';
	break;

case '_BURI_FSIB':	
	$title=i18n::translate('Burial of father\'s sibling');
	$text='';
	break;

case '_BURI_GCHI':	
	$title=i18n::translate('Burial of a grandchild');
	$text='';
	break;

case '_BURI_GGCH':	
	$title=i18n::translate('Burial of a great-grandchild');
	$text='';
	break;

case '_BURI_GGPA':	
	$title=i18n::translate('Burial of a great-grand-parent');
	$text='';
	break;

case '_BURI_GPAR':	
	$title=i18n::translate('Burial of a grand-parent');
	$text='';
	break;

case '_BURI_HSIB':	
	$title=i18n::translate('Burial of half-sibling');
	$text='';
	break;

case '_BURI_MOTH':	
	$title=i18n::translate('Burial of mother');
	$text='';
	break;

case '_BURI_MSIB':	
	$title=i18n::translate('Burial of mother\'s sibling');
	$text='';
	break;

case '_BURI_NEPH':	
	$title=i18n::translate('Burial of a nephew or niece');
	$text='';
	break;

case '_BURI_SIBL':	
	$title=i18n::translate('Burial of sibling');
	$text='';
	break;

case '_BURI_SPOU':	
	$title=i18n::translate('Burial of spouse');
	$text='';
	break;

case '_CHR_CHIL':	
	$title=i18n::translate('Christening of a child');
	$text='';
	break;

case '_CHR_COUS':	
	$title=i18n::translate('Christening of a first cousin');
	$text='';
	break;

case '_CHR_FSIB':	
	$title=i18n::translate('Christening of father\'s sibling');
	$text='';
	break;

case '_CHR_GCHI':	
	$title=i18n::translate('Christening of a grandchild');
	$text='';
	break;

case '_CHR_GGCH':	
	$title=i18n::translate('Christening of a great-grandchild');
	$text='';
	break;

case '_CHR_HSIB':	
	$title=i18n::translate('Christening of half-sibling');
	$text='';
	break;

case '_CHR_MSIB':	
	$title=i18n::translate('Christening of mother\'s sibling');
	$text='';
	break;

case '_CHR_NEPH':	
	$title=i18n::translate('Christening of a nephew or niece');
	$text='';
	break;

case '_CHR_SIBL':	
	$title=i18n::translate('Christening of sibling');
	$text='';
	break;

case '_COML':	
	$title=i18n::translate('Common law marriage');
	$text='';
	break;

case '_CREM_CHIL':	
	$title=i18n::translate('Cremation of a child');
	$text='';
	break;

case '_CREM_COUS':	
	$title=i18n::translate('Cremation of a first cousin');
	$text='';
	break;

case '_CREM_FATH':	
	$title=i18n::translate('Cremation of father');
	$text='';
	break;

case '_CREM_FSIB':	
	$title=i18n::translate('Cremation of father\'s sibling');
	$text='';
	break;

case '_CREM_GCHI':	
	$title=i18n::translate('Cremation of a grandchild');
	$text='';
	break;

case '_CREM_GGCH':	
	$title=i18n::translate('Cremation of a great-grandchild');
	$text='';
	break;

case '_CREM_GGPA':	
	$title=i18n::translate('Cremation of a great-grand-parent');
	$text='';
	break;

case '_CREM_GPAR':	
	$title=i18n::translate('Cremation of a grand-parent');
	$text='';
	break;

case '_CREM_HSIB':	
	$title=i18n::translate('Cremation of half-sibling');
	$text='';
	break;

case '_CREM_MOTH':	
	$title=i18n::translate('Cremation of mother');
	$text='';
	break;

case '_CREM_MSIB':	
	$title=i18n::translate('Cremation of mother\'s sibling');
	$text='';
	break;

case '_CREM_NEPH':	
	$title=i18n::translate('Cremation of a nephew or niece');
	$text='';
	break;

case '_CREM_SIBL':	
	$title=i18n::translate('Cremation of sibling');
	$text='';
	break;

case '_CREM_SPOU':	
	$title=i18n::translate('Cremation of spouse');
	$text='';
	break;

case '_DBID':	
	$title=i18n::translate('Linked database ID');
	$text='';
	break;

case '_DEAT_CHIL':	
	$title=i18n::translate('Death of a child');
	$text='';
	break;

case '_DEAT_COUS':	
	$title=i18n::translate('Death of a first cousin');
	$text='';
	break;

case '_DEAT_FATH':	
	$title=i18n::translate('Death of father');
	$text='';
	break;

case '_DEAT_FSIB':	
	$title=i18n::translate('Death of father\'s sibling');
	$text='';
	break;

case '_DEAT_GCHI':	
	$title=i18n::translate('Death of a grandchild');
	$text='';
	break;

case '_DEAT_GGCH':	
	$title=i18n::translate('Death of a great-grandchild');
	$text='';
	break;

case '_DEAT_GGPA':	
	$title=i18n::translate('Death of a great-grand-parent');
	$text='';
	break;

case '_DEAT_GPAR':	
	$title=i18n::translate('Death of a grand-parent');
	$text='';
	break;

case '_DEAT_HSIB':	
	$title=i18n::translate('Death of half-sibling');
	$text='';
	break;

case '_DEAT_MOTH':	
	$title=i18n::translate('Death of mother');
	$text='';
	break;

case '_DEAT_MSIB':	
	$title=i18n::translate('Death of mother\'s sibling');
	$text='';
	break;

case '_DEAT_NEPH':	
	$title=i18n::translate('Death of a nephew or niece');
	$text='';
	break;

case '_':	
	$title=i18n::translate('Death of sibling');
	$text='';
	break;

case '_DEAT_SPOU':	
	$title=i18n::translate('Death of spouse');
	$text='';
	break;

case '_DEG':	
	$title=i18n::translate('Degree');
	$text='';
	break;

case '_DETS':	
	$title=i18n::translate('Death of one spouse');
	$text='';
	break;

case '_EMAIL':	
	$title=i18n::translate('Email address');
	$text='';
	break;

case '_EYEC':	
	$title=i18n::translate('Eye color');
	$text='';
	break;

case '_FA1':	
	$title=i18n::translate('Fact 1');
	$text='';
	break;

case '_FA2':	
	$title=i18n::translate('Fact 2');
	$text='';
	break;

case '_FA3':	
	$title=i18n::translate('Fact 3');
	$text='';
	break;

case '_FA4':	
	$title=i18n::translate('Fact 4');
	$text='';
	break;

case '_FA5':	
	$title=i18n::translate('Fact 5');
	$text='';
	break;

case '_FA6':	
	$title=i18n::translate('Fact 6');
	$text='';
	break;

case '_FA7':	
	$title=i18n::translate('Fact 7');
	$text='';
	break;

case '_FA8':	
	$title=i18n::translate('Fact 8');
	$text='';
	break;

case '_FA9':	
	$title=i18n::translate('Fact 9');
	$text='';
	break;

case '_FA10':	
	$title=i18n::translate('Fact 10');
	$text='';
	break;

case '_FA11':	
	$title=i18n::translate('Fact 11');
	$text='';
	break;

case '_FA12':	
	$title=i18n::translate('Fact 12');
	$text='';
	break;

case '_FA13':	
	$title=i18n::translate('Fact 13');
	$text='';
	break;

case '_FAMC_EMIG':	
	$title=i18n::translate('Emigration of parents');
	$text='';
	break;

case '_FAMC_RESI':	
	$title=i18n::translate('Residence of parents');
	$text='';
	break;

case '_FNRL':	
	$title=i18n::translate('Funeral');
	$text='';
	break;

case '_FREL':	
	$title=i18n::translate('Relationship to father');
	$text='';
	break;

case '_GEDF':	
	$title=i18n::translate('GEDCOM file');
	$text='';
	break;

case '_HAIR':	
	$title=i18n::translate('Hair color');
	$text='';
	break;

case '_HEB':
	$title=i18n::translate('Hebrew');
	$text=i18n::translate('In many cultures it is customary to have a traditional name spelled in the traditional characters and also a romanized version of the name as it would be spelled or pronounced in languages based on the Latin alphabet, such as English.<br /><br />If you prefer to use the Latin alphabet to enter the name in the standard name fields, then you can use this field to enter the same name in the non-Latin alphabet such as Greek, Hebrew, Russian, Arabic, or Chinese.  Both versions of the name will appear in lists and charts.<br /><br />Although this field is labelled "Hebrew", it is not restricted to containing only Hebrew characters.');
	break;

case '_HEIG':	
	$title=i18n::translate('Height');
	$text='';
	break;

case '_HNM':	
	$title=i18n::translate('Hebrew name');
	$text='';
	break;

case '_HOL':	
	$title=i18n::translate('Holocaust');
	$text='';
	break;

case '_INTE':	
	$title=i18n::translate('Interred');
	$text='';
	break;

case '_MARB_CHIL':	
	$title=i18n::translate('Marriage bann of a child');
	$text='';
	break;

case '_MARB_COUS':	
	$title=i18n::translate('Marriage bann of a first cousin');
	$text='';
	break;

case '_MARB_FAMC':	
	$title=i18n::translate('Marriage bann of parents');
	$text='';
	break;

case '_MARB_FATH':	
	$title=i18n::translate('Marriage bann of father');
	$text='';
	break;

case '_MARB_FSIB':	
	$title=i18n::translate('Marriage bann of father\'s sibling');
	$text='';
	break;

case '_MARB_GCHI':	
	$title=i18n::translate('Marriage bann of a grandchild');
	$text='';
	break;

case '_MARB_GGCH':	
	$title=i18n::translate('Marriage bann of a great-grandchild');
	$text='';
	break;

case '_MARB_HSIB':	
	$title=i18n::translate('Marriage bann of half-sibling');
	$text='';
	break;

case '_MARB_MOTH':	
	$title=i18n::translate('Marriage bann of mother');
	$text='';
	break;

case '_MARB_MSIB':	
	$title=i18n::translate('Marriage bann of mother\'s sibling');
	$text='';
	break;

case '_MARB_NEPH':	
	$title=i18n::translate('Marriage bann of a nephew or niece');
	$text='';
	break;

case '_MARB_SIBL':	
	$title=i18n::translate('Marriage bann of sibling');
	$text='';
	break;

case '_MARI':	
	$title=i18n::translate('Marriage intention');
	$text='';
	break;

case '_MARNM':
	$title=i18n::translate('Married name');
	$text=i18n::translate('Enter the married name for this person, using the same formatting rules that apply to the Name field.  This field is optional.<br /><br />For example, if Mary Jane Brown married John White, you might enter (without the quotation marks, of course)<ul><li>American usage:&nbsp;&nbsp;"Mary Jane Brown /White/"</li><li>European usage:&nbsp;&nbsp;"Mary Jane /White/"</li><li>Alternate European usage:&nbsp;&nbsp;"Mary Jane /White-Brown/" or "Mary Jane /Brown-White/"</li></ul>You should do this only if Mary Brown began calling herself by the new name after marrying John White.  In some places, Quebec (Canada) for example, it\'s illegal for names to be changed in this way.<br /><br />Men sometimes change their name after marriage, most often using the hyphenated form but occasionally taking the wife\'s surname.');
	break;

case '_PRIM':
	$title=i18n::translate('Highlighted image');
	$text=i18n::translate('Use this field to signal that this media item is the highlighted or primary item for the person it is attached to.  The highlighted image is the one that will be used on charts and on the Individual page.');
	break;

case '_MARNM_SURN':	
	$title=i18n::translate('Married surname');
	$text='';
	break;

case '_MARR_CHIL':	
	$title=i18n::translate('Marriage of a child');
	$text='';
	break;

case '_MARR_COUS':	
	$title=i18n::translate('Marriage of a first cousin');
	$text='';
	break;

case '_MARR_FAMC':	
	$title=i18n::translate('Marriage of parents');
	$text='';
	break;

case '_MARR_FATH':	
	$title=i18n::translate('Marriage of father');
	$text='';
	break;

case '_MARR_FSIB':	
	$title=i18n::translate('Marriage of father\'s sibling');
	$text='';
	break;

case '_MARR_GCHI':	
	$title=i18n::translate('Marriage of a grandchild');
	$text='';
	break;

case '_MARR_GGCH':	
	$title=i18n::translate('Marriage of a great-grandchild');
	$text='';
	break;

case '_MARR_HSIB':	
	$title=i18n::translate('Marriage of half-sibling');
	$text='';
	break;

case '_MARR_MOTH':	
	$title=i18n::translate('Marriage of mother');
	$text='';
	break;

case '_MARR_MSIB':	
	$title=i18n::translate('Marriage of mother\'s sibling');
	$text='';
	break;

case '_MARR_NEPH':	
	$title=i18n::translate('Marriage of a nephew or niece');
	$text='';
	break;

case '_MARR_SIBL':	
	$title=i18n::translate('Marriage of sibling');
	$text='';
	break;

case '_MBON':	
	$title=i18n::translate('Marriage bond');
	$text='';
	break;

case '_MDCL':	
	$title=i18n::translate('Medical');
	$text='';
	break;

case '_MEDC':	
	$title=i18n::translate('Medical condition');
	$text='';
	break;

case '_MEND':	
	$title=i18n::translate('Marriage ending status');
	$text='';
	break;

case '_MILI':	
	$title=i18n::translate('Military');
	$text='';
	break;

case '_MILT':	
	$title=i18n::translate('Military service');
	$text='';
	break;

case '_MREL':	
	$title=i18n::translate('Relationship to mother');
	$text='';
	break;

case '_MSTAT':	
	$title=i18n::translate('Marriage beginning status');
	$text='';
	break;

case '_NAME':	
	$title=i18n::translate('Mailing name');
	$text='';
	break;

case '_NAMS':	
	$title=i18n::translate('Namesake');
	$text='';
	break;

case '_NLIV':	
	$title=i18n::translate('Not living');
	$text='';
	break;

case '_NMAR':	
	$title=i18n::translate('Never married');
	$text='';
	break;

case '_NMR':	
	$title=i18n::translate('Not married');
	$text='';
	break;

case '_PGVU':	
	$title=i18n::translate('Last changed by');
	$text='';
	break;

case '_PRMN':	
	$title=i18n::translate('Permanent number');
	$text='';
	break;

case '_SCBK':	
	$title=i18n::translate('Scrapbook');
	$text='';
	break;

case '_SEPR':	
	$title=i18n::translate('Separated');
	$text='';
	break;

case '_SSHOW':	
	$title=i18n::translate('Slide show');
	$text='';
	break;

case '_STAT':	
	$title=i18n::translate('Marriage status');
	$text='';
	break;

case '_SUBQ':	
	$title=i18n::translate('Short version');
	$text='';
	break;

case '_THUM':
	$title=i18n::translate('Always use main image?');
	$text=i18n::translate('This option lets you override the usual selection for a thumbnail image.<br /><br />The GEDCOM has a configuration option that specifies whether <b>webtrees</b> should send the large or the small image to the browser whenever the current page requires a thumbnail.  The &laquo;Always use main image?&raquo; option, when set to <b>Yes</b>, temporarily overrides the setting of the GEDCOM configuration option, so that <b>webtrees</b> will always send the large image.  You cannot force <b>webtrees</b> to send the small image when the GEDCOM configuration specifies that large images should always be used.<br /><br /><b>webtrees</b> does not re-size the image being sent; the browser does this according to the page specifications it has also received.  This can have undesirable consequences when the image being sent is not truly a thumbnail where <b>webtrees</b> is expecting to send a small image.  This is not an error:  There are occasions where it may be desirable to display a large image in places where one would normally expect to see a thumbnail-sized picture.<br /><br />You should avoid setting the &laquo;Always use main image?&raquo; option to <b>Yes</b>.  This choice will cause excessive amounts of image-related data to be sent to the browser, only to have the browser discard the excess.  Page loads, particularly of charts with many images, can be seriously slowed.');
	break;

case '_TODO':	
	$title=i18n::translate('To do item');
	$text='';
	break;

case '_TYPE':	
	$title=i18n::translate('Media type');
	$text='';
	break;

case '_UID':	
	$title=i18n::translate('Globally unique identifier');
	$text='';
	break;

case '_URL':	
	$title=i18n::translate('Web URL');
	$text='';
	break;

case '_WEIG':	
	$title=i18n::translate('Weight');
	$text='';
	break;

case '_YART':	
	$title=i18n::translate('Yahrzeit');
	$text='';
	break;

case '__BRTM_CHIL':	
	$title=i18n::translate('Brit mila of a child');
	$text='';
	break;

case '__BRTM_COUS':	
	$title=i18n::translate('Brit mila of a first cousin');
	$text='';
	break;

case '__BRTM_FSIB':	
	$title=i18n::translate('Brit mila of father\'s sibling');
	$text='';
	break;

case '__BRTM_GCHI':	
	$title=i18n::translate('Brit mila of a grandchild');
	$text='';
	break;

case '__BRTM_GGCH':	
	$title=i18n::translate('Brit mila of a great-grandchild');
	$text='';
	break;

case '__BRTM_HSIB':	
	$title=i18n::translate('Brit mila of half-sibling');
	$text='';
	break;

case '__BRTM_MSIB':	
	$title=i18n::translate('Brit mila of mother\'s sibling');
	$text='';
	break;

case '__BRTM_NEPH':	
	$title=i18n::translate('Brit mila of a nephew');
	$text='';
	break;

case '__BRTM_SIBL':	
	$title=i18n::translate('Brit mila of sibling');
	$text='';
	break;


	//////////////////////////////////////////////////////////////////////////////
	// This section contains an entry for every configuration item
	//////////////////////////////////////////////////////////////////////////////

case 'ABBREVIATE_CHART_LABELS':
	$title=i18n::translate('Abbreviate chart labels');
	$text=i18n::translate('This option controls whether or not to abbreviate labels like <b>Birth</b> on charts with just the first letter like <b>B</b>.<br /><br />You can customize the abbreviations by supplying overriding values in the <i>languages/extra.xx.php</i> file for each language.  For example, if you want to use <b>*</b> instead of <b>N</b> to abbreviate the BIRT fact in the French language, you should put the following entry into the <i>languages/extra.fr.php</i> file:<br /><br /><code>$factAbbrev["BIRT"]&nbsp;=&nbsp;"*";</code><br /><br />The lengths of abbreviations specified this way are not limited to 1 character.');
	break;

case 'ADVANCED_NAME_FACTS':
	$title=i18n::translate('Advanced name facts');
	$text=i18n::translate('This is a comma separated list of GEDCOM fact tags that will be shown on the add/edit name form.  If you use non-Latin alphabets such as Hebrew, Greek, Cyrillic or Arabic, you may want to add tags such as _HEB, ROMN, FONE, etc. to allow you to store names in several different alphabets.');
	break;

case 'ADVANCED_PLAC_FACTS':
	$title=i18n::translate('Advanced place name facts');
	$text=i18n::translate('This is a comma separated list of GEDCOM fact tags that will be shown when you add or edit place names.  If you use non-Latin alphabets such as Hebrew, Greek, Cyrillic or Arabic, you may want to add tags such as _HEB, ROMN, FONE, etc. to allow you to store place names in several different alphabets.');
	break;

case 'ALLOW_CHANGE_GEDCOM':
	$title=i18n::translate('Allow GEDCOM switching');
	$text=i18n::translate('If you have an environment with multiple GEDCOMs, setting this value to <b>Yes</b> allows your site visitors <u>and</u> users to have the option of changing GEDCOMs.  Setting it to <b>No</b> disables GEDCOM switching for both visitors <u>and</u> logged in users.');
	break;

case 'ALLOW_EDIT_GEDCOM':
	$title=i18n::translate('Enable online editing');
	$text=i18n::translate('This option enables online editing features for this database so that users with Edit privileges may update data online.');
	break;

case 'ALLOW_THEME_DROPDOWN':
	$title=i18n::translate('Display theme dropdown selector for theme changes');
	$text=i18n::translate('Gives users the option of selecting their own theme from a menu.<br /><br />Even with this option set, the theme currently in effect may not provide for such a menu.  To be effective, this option requires the <b>Allow users to select their own theme</b> option to be set as well.');
	break;

case 'ALLOW_USER_THEMES':
	$title=i18n::translate('Allow users to select their own theme');
	$text=i18n::translate('Gives users the option of selecting their own theme.');
	break;

case 'AUTO_GENERATE_THUMBS':
	$title=i18n::translate('Automatically generated thumbnails');
	$text=i18n::translate('Should the system automatically generate thumbnails for images that do not have them.  Your PHP installation might not support this functionality.');
	break;

case 'BOM_detected':
	$title=i18n::translate('A Byte Order Mark (BOM) was detected at the beginning of the file. On cleanup, this special code will be removed.');
	$text=i18n::translate('The GEDCOM file you are importing has a special 3-byte code at the beginning.  This special code is used by some programs to indicate that the file has been recorded in the UTF-8 character set.<br /><br />Although this special code is not really an error, <b>webtrees</b> will not work properly when the input file contains the code.  You should let <b>webtrees</b> remove the code.');
	break;

case 'CALENDAR_FORMAT':
	$title=i18n::translate('Calendar format');
	$text=i18n::translate('Dates can be recorded in various calendars such as Gregorian, Julian, or the Jewish Calendar.  This option allows you to convert dates to a preferred calendar.  For example, you could select Gregorian to convert Julian and Hebrew dates to Gregorian.  The converted date is shown in parentheses after the regular date.<br /><br />Dates are only converted if they are valid for the calendar.  For example, only dates between 22&nbsp;SEP&nbsp;1792 and 31&nbsp;DEC&nbsp;1805 will be converted to the French Republican calendar and only dates after 15&nbsp;OCT&nbsp;1582 will be converted to the Gregorian calendar.<br /><br />Hebrew is the same as Jewish, but using Hebrew characters.  Arabic is the same as Hijri, but using Arabic characters.<br /><br />Note: Since the Jewish and Hijri calendar day starts at dusk, any event taking place from dusk till midnight will display as one day prior to the correct date.  The display of Hebrew and Arabic can be problematic in old browsers, which may display text backwards (left to right) or not at all.');
	break;

case 'CHANGELOG_CREATE':
	$title=i18n::translate('Archive changelog files');
	$text=i18n::translate('How often should the program archive Changelog files.');
	break;

case 'CHARACTER_SET':
	$title=i18n::translate('Character set encoding');
	$text=i18n::translate('This is the character set of your GEDCOM file.  UTF-8 is the default and should work for almost all sites.  If you export your GEDCOM using IBM Windows encoding, you should put WINDOWS here.<br /><br />NOTE: <b>webtrees</b> can\'t support UNICODE (UTF-16) because the support is missing in PHP.');
	break;

case 'CHART_BOX_TAGS':
	$title=i18n::translate('Other facts to show in charts');
	$text=i18n::translate('This should be a comma or space separated list of facts, in addition to Birth and Death, that you want to appear in chart boxes such as the Pedigree chart.  This list requires you to use fact tags as defined in the GEDCOM 5.5.1 Standard.  For example, if you wanted the occupation to show up in the box, you would add "OCCU" to this field.');
	break;

case 'CHECK_CHILD_DATES':
	$title=i18n::translate('Check child dates');
	$text=i18n::translate('Check children\'s dates when determining whether a person is dead.  On older systems and large GEDCOMs this can slow down the response time of your site.');
	break;

case 'CHECK_MARRIAGE_RELATIONS':
	$title=i18n::translate('Check marriage relations');
	$text=i18n::translate('Check relationships that are related by marriage.');
	break;

case 'COMMON_NAMES_ADD':
	$title=i18n::translate('Names to add to common surnames (comma separated)');
	$text=i18n::translate('If the number of times that a certain surname occurs is lower than the threshold, it will not appear in the list.  It can be added here manually.  If more than one surname is entered, they must be separated by a comma.  <b>Surnames are case-sensitive.</b>');
	break;

case 'COMMON_NAMES_REMOVE':
	$title=i18n::translate('Names to remove from common surnames (comma separated)');
	$text=i18n::translate('If you want to remove a surname from the Common Surname list without increasing the threshold value, you can do that by entering the surname here.  If more than one surname is entered, they must be separated by a comma. <b>Surnames are case-sensitive.</b>  Surnames entered here will also be removed from the Top-10 list on the Home Page.');
	break;

case 'COMMON_NAMES_THRESHOLD':
	$title=i18n::translate('Min. no. of occurrences to be a "common surname"');
	$text=i18n::translate('This is the number of times that a surname must occur before it shows up in the Common Surname list on the Home Page.');
	break;

case 'CONTACT_EMAIL':
	$title=i18n::translate('Genealogy contact');
	$text=i18n::translate('The person to contact about the genealogical data on this site.');
	break;

case 'CONTACT_METHOD':
	$title=i18n::translate('Contact method');
	$text=i18n::translate('The method to be used to contact the Genealogy contact about genealogy questions.<ul><li>The <b>Mailto link</b> option will create a "mailto" link that can be clicked to send an email using the mail client on the user\'s PC.</li><li>The <b><b>webtrees</b> internal messaging</b> option will use a messaging system internal to <b>webtrees</b>, and no emails will be sent.</li><li>The <b>Internal messaging with emails</b> option is the default.  It will use the <b>webtrees</b> messaging system and will also send copies of the messages via email.</li><li>The <b>webtrees sends emails with no storage</b> option allows <b>webtrees</b> to handle the messaging and will send the messages as emails, but will not store the messages internally.  This option is similar to the <b>Mailto link</b> option, except that the message will be sent by <b>webtrees</b> instead of the user\'s workstation.</li><li>The <b>No contact method</b> option results in your users having no way of contacting you.</li></ul>');
	break;

case 'DAYS_TO_SHOW_LIMIT':
	$title=i18n::translate('Upcoming events block day limit');
	$text=i18n::translate('Enter the maximum number of days to show in Upcoming Events blocks.  This number cannot be greater than 30. If you enter a larger value, 30 will be used.<br /><br />The value you enter here determines how far ahead <b>webtrees</b> looks when searching for upcoming events.  The results of this search, done once daily, are copied into a temporary file.<br /><br />No Upcoming Events blocks on Index or Portal pages can request more days than this value.  The larger you make this, the longer it will take to build the daily database extract, and the longer it will take to display the block, even when you request to display a number of days less than this setting.');
	break;

case 'DEFAULT_PEDIGREE_GENERATIONS':
	$title=i18n::translate('Pedigree generations');
	$text=i18n::translate('Set the default number of generations to display on Descendancy and Pedigree charts.');
	break;

case 'DISPLAY_JEWISH_GERESHAYIM':
	$title=i18n::translate('Display Hebrew gershayim');
	$text=i18n::translate('Show single and double quotes when displaying Hebrew dates.<br /><br />Setting this to <b>Yes</b> will display February 8 1969 as <span lang=\'he-IL\' dir=\'rtl\'>&#1499;\'&#160;&#1513;&#1489;&#1496;&#160;&#1514;&#1513;&#1499;&quot;&#1496;</span>&lrm; while setting it to <b>No</b> will display it as <span lang=\'he-IL\' dir=\'rtl\'>&#1499;&#160;&#1513;&#1489;&#1496;&#160;&#1514;&#1513;&#1499;&#1496;</span>&lrm;.  This has no impact on the Jewish year setting since quotes are not used in Jewish dates displayed with Latin characters.<br /><br />Note: This setting is similar to the PHP 5.0 Calendar constants CAL_JEWISH_ADD_ALAFIM_GERESH and CAL_JEWISH_ADD_GERESHAYIM.  This single setting affects both.');
	break;

case 'DISPLAY_JEWISH_THOUSANDS':
	$title=i18n::translate('Display Hebrew thousands');
	$text=i18n::translate('Show Alafim in Hebrew calendars.<br /><br />Setting this to <b>Yes</b> will display the year 1969 as <span lang="he-IL" dir=\'rtl\'>&#1492;\'&#160;&#1514;&#1513;&#1499;&quot;&#1496;</span>&lrm; while setting it to <b>No</b> will display the year as <span lang="he-IL" dir=\'rtl\'>&#1514;&#1513;&#1499;&quot;&#1496;</span>&lrm;.  This has no impact on the Jewish year setting.  The year will display as 5729 regardless of this setting.<br /><br />Note: This setting is similar to the PHP 5.0 Calendar constant CAL_JEWISH_ADD_ALAFIM.');
	break;

case 'EDIT_AUTOCLOSE':
	$title=i18n::translate('Autoclose edit window');
	$text=i18n::translate('This option controls whether or not to automatically close the Edit window after a successful update.');
	break;

case 'ENABLE_AUTOCOMPLETE':
	$title=i18n::translate('Enable autocomplete');
	$text=i18n::translate('This option determines whether Autocomplete should be active while information is being entered into certain fields on input forms.  When this option is set to <b>Yes</b>, text input fields for which Autocomplete is possible are indicated by a differently colored background.<br /><br />When Autocomplete is active, <b>webtrees</b> will search its database for possible matches according to what you have already entered.  As you enter more information, the list of possible matches is refined.  When you see the desired input in the list of matches, you can move the mouse cursor to that line of the list and then click the left mouse button to complete the input.<br /><br />The disadvantages of Autocomplete are that it slows the program, entails significant database activity, and also results in more data being sent to the browser.');
	break;

case 'ENABLE_CLIPPINGS_CART':
	$title=i18n::translate('Enable clippings cart');
	$text=i18n::translate('The clippings cart allows users to add people to a temporary file that they can download in GEDCOM format for subsequent import into their genealogy software.');
	break;

case 'ENABLE_MULTI_LANGUAGE':
	$title=i18n::translate('Allow user to change language');
	$text=i18n::translate('Set to <b>Yes</b> to allow users to override the site\'s default language.  They can do this through their browser\'s preferred language configuration, configuration options on their Account page, or through links or buttons on most <b>webtrees</b> pages.');
	break;

case 'ENABLE_RSS':
	$title=i18n::translate('Enable RSS');
	$text=i18n::translate('This option lets you disable the RSS feature.<br /><br />RSS lets users monitor your site for changes to the Index page without actually visiting your site periodically.  If too many users make use of this feature or if the refresh frequency set by these users is too high, RSS can use up too much bandwidth or server capacity.<br /><br />This <a href=\'http://en.wikipedia.org/wiki/RSS\' target=\'_blank\' title=\'Wikipedia article\'><b>Wikipedia article</b></a> contains comprehensive information and links about RSS and the various RSS formats.');
	break;

case 'EXPAND_NOTES':
	$title=i18n::translate('Automatically expand notes');
	$text=i18n::translate('This option controls whether or not to automatically display content of a <i>Note</i> record on the Individual page.');
	break;

case 'EXPAND_RELATIVES_EVENTS':
	$title=i18n::translate('Automatically expand list of events of close relatives');
	$text=i18n::translate('This option controls whether or not to automatically expand the <i>Events of close relatives</i> list.');
	break;

case 'EXPAND_SOURCES':
	$title=i18n::translate('Automatically expand sources');
	$text=i18n::translate('This option controls whether or not to automatically display content of a <i>Source</i> record on the Individual page.');
	break;

case 'FAM_FACTS_ADD':
	$title=i18n::translate('Family add facts');
	$text=i18n::translate('This is the list of GEDCOM facts that your users can add to families.  You can modify this list by removing or adding fact names, even custom ones, as necessary.  Fact names that appear in this list must not also appear in the <i>Unique Family Facts</i> list.');
	break;

case 'FAM_FACTS_QUICK':
	$title=i18n::translate('Quick family facts');
	$text=i18n::translate('This is the short list of GEDCOM family facts that appears next to the full list and can be added with a single click.');
	break;

case 'FAM_FACTS_UNIQUE':
	$title=i18n::translate('Unique family facts');
	$text=i18n::translate('This is the list of GEDCOM facts that your users can only add <u>once</u> to families.  For example, if MARR is in this list, users will not be able to add more than one MARR record to a family.  Fact names that appear in this list must not also appear in the <i>Family Add Facts</i> list.');
	break;

case 'FAM_ID_PREFIX':
	$title=i18n::translate('Family ID prefix');
	$text=i18n::translate('When a new family record is added online in <b>webtrees</b>, a new ID for that family will be generated automatically. The family ID will have this prefix.');
	break;

case 'FAVICON':
	$title=i18n::translate('Favorites icon');
	$text=i18n::translate('Change this to point to the icon you want to display in peoples\' favorites menu when they bookmark your site.');
	break;

case 'FULL_SOURCES':
	$title=i18n::translate('Use full source citations');
	$text=i18n::translate('Source citations can include fields to record the quality of the data (primary, secondary, etc.) and the date the event was recorded in the source.  If you don\'t use these fields, you can disable them when creating new source citations.');
	break;

case 'GEDCOM_DEFAULT_TAB':
	$title=i18n::translate('Default tab to show on individual page');
	$text=i18n::translate('This option allows you to choose which tab opens automatically on the Individual page when that page is accessed.');
	break;

case 'GEDCOM_ID_PREFIX':
	$title=i18n::translate('Individual ID prefix');
	$text=i18n::translate('When a new individual record is added online in <b>webtrees</b>, a new ID for that individual will be generated automatically. The individual ID will have this prefix.');
	break;

case 'GENERATE_GUID':
	$title=i18n::translate('Automatically create globally unique IDs');
	$text=i18n::translate('<b>GUID</b> in this context is an acronym for «Globally Unique ID».<br /><br />GUIDs are intended to help identify each individual in a manner that is repeatable, so that central organizations such as the Family History Center of the LDS Church in Salt Lake City, or even compatible programs running on your own server, can determine whether they are dealing with the same person no matter where the GEDCOM originates.  The goal of the Family History Center is to have a central repository of genealogical data and expose it through web services. This will enable any program to access the data and update their data within it.<br /><br />If you do not intend to share this GEDCOM with anyone else, you do not need to let <b>webtrees</b> create these GUIDs; however, doing so will do no harm other than increasing the size of your GEDCOM.');
	break;

case 'HIDE_GEDCOM_ERRORS':
	$title=i18n::translate('Hide GEDCOM errors');
	$text=i18n::translate('Setting this to <b>Yes</b> will hide error messages produced by <b>webtrees</b> when it doesn\'t understand a tag in your GEDCOM file.  <b>webtrees</b> makes every effort to conform to the GEDCOM 5.5.1 standard, but many genealogy software programs include their own custom tags.  See the <a href="readme.txt">readme.txt</a> file for more information.');
	break;

case 'HIDE_LIVE_PEOPLE':
	$title=i18n::translate('Enable privacy');
	$text=i18n::translate('This option will enable all privacy settings and hide the details of living people.<br /><br />Living people are defined to be those who do not have an event more recent than the number of years specified in variable $MAX_ALIVE_AGE.  For this purpose, births of children are considered to be such events as well.');
	break;

case 'HOME_SITE_TEXT':
	$title=i18n::translate('Main website text');
	$text=i18n::translate('The legend used to identify the link to your main Home page.');
	break;

case 'HOME_SITE_URL':
	$title=i18n::translate('Main website URL');
	$text=i18n::translate('Each <b>webtrees</b> page includes a link to your main Home page.  The appearance of this link is controlled by the theme being used.  You enter the actual URL to your Home site here.');
	break;

case 'INDEX_DIRECTORY':
	$title=i18n::translate('Index file directory');
	$text=i18n::translate('The path to a readable and writable directory where <b>webtrees</b> should store index files (include the trailing "/").  <b>webtrees</b> does not require this directory\'s name to be "index".  You can choose any name you like.<br /><br />For security, this directory should be placed somewhere in the server\'s file space that is not accessible from the Internet. An example of such a structure follows:<br /><b>webtrees:</b> dir1/dir2/dir3/webtrees<br /><b>Index:</b> dir1/dir4/dir5/dir6/index<br /><br />For the example shown, you would enter <b>../../dir4/dir5/dir6/index/</b> in this field.');
	break;

case 'INDI_FACTS_ADD':
	$title=i18n::translate('Individual add facts');
	$text=i18n::translate('This is the list of GEDCOM facts that your users can add to individuals.  You can modify this list by removing or adding fact names, even custom ones, as necessary.  Fact names that appear in this list must not also appear in the <i>Unique Individual Facts</i> list.');
	break;

case 'INDI_FACTS_QUICK':
	$title=i18n::translate('Quick individual facts');
	$text=i18n::translate('This is the short list of GEDCOM individual facts that appears next to the full list and can be added with a single click.');
	break;

case 'INDI_FACTS_UNIQUE':
	$title=i18n::translate('Unique individual facts');
	$text=i18n::translate('This is the list of GEDCOM facts that your users can only add <u>once</u> to individuals.  For example, if BIRT is in this list, users will not be able to add more than one BIRT record to an individual.  Fact names that appear in this list must not also appear in the <i>Individual Add Facts</i> list.');
	break;

case 'LANGUAGE':
	$title=i18n::translate('Language');
	$text=i18n::translate('Assign the default language for the site.<br /><br />When the <b>Allow user to change language</b> option is set, users can override this setting through their browser\'s preferred language configuration, configuration options on their Account page, or through links or buttons on most <b>webtrees</b> pages.');
	break;

case 'LANG_SELECTION':
	$title=i18n::translate('Supported languages');
	$text=i18n::translate('You can change the list of languages supported by your <b>webtrees</b> site by adding or removing checkmarks as appropriate.  This changes the language choices available to your users.<br /><br />You can achieve the same thing through the <b>Configure supported languages</b> link on the Admin menu, where you can also change things such as the language\'s flag icon, the date format, or whether the surname should always be printed first.');
	break;

case 'LINK_ICONS':
	$title=i18n::translate('PopUp links on charts');
	$text=i18n::translate('Allows the user to select links to other charts and close relatives of the person.<br /><br />Set to <b>Disabled</b> to disable this feature.  Set to <b>On Mouse Over</b> to popup the links when the user mouses over the icon in the box.  Set to <b>On Mouse Click</b> to popup the links when the user clicks on the icon in the box.');
	break;

case 'LOG_LANG_ERROR':
	$title=i18n::translate('Logfile for language errors');
	$text='';
	break;

case 'LOGFILE_CREATE':
	$title=i18n::translate('Archive log files');
	$text=i18n::translate('How often should the program archive log files.');
	break;

case 'LOGIN_URL':
	$title=i18n::translate('Login URL');
	$text=i18n::translate('You only need to enter a Login URL if you want to redirect to a different site or location when your users login.  This is very useful if you need to switch from http to https when your users login.  Include the full URL to <i>login.php</i>.  For example, https://www.yourserver.com/webtrees/login.php .');
	break;

case 'MAX_ALIVE_AGE':
	$title=i18n::translate('Age at which to assume a person is dead');
	$text=i18n::translate('If this person has any events other than Death, Burial, or Cremation more recent than this number of years, he is considered to be "alive".  Children\'s birth dates are considered to be such events for this purpose.');
	break;

case 'MAX_DESCENDANCY_GENERATIONS':
	$title=i18n::translate('Maximum descendancy generations');
	$text=i18n::translate('Set the maximum number of generations to display on Descendancy charts.');
	break;

case 'MAX_PEDIGREE_GENERATIONS':
	$title=i18n::translate('Maximum pedigree generations');
	$text=i18n::translate('Set the maximum number of generations to display on Pedigree charts.');
	break;

case 'MAX_RELATION_PATH_LENGTH':
	$title=i18n::translate('Maximum relationship path length');
	$text=i18n::translate('If the <i>Use relationship privacy</i> option is enabled, logged-in users will only be able to see or edit individuals within this number of relationship steps.<br /><br />This option sets the default for all users who have access to this genealogical database.  The Administrator can override this option for individual users by editing the user\'s account details.');
	break;

case 'MAX_VIEW_RATE':
	$title=i18n::translate('Maximum page view rate');
	$text=i18n::translate('This option limits the rate at which a user can view pages.<br /><br />If that rate is exceeded, <b>webtrees</b> treats the session as a hacking attempt;  the session will be terminated with a suitable message.  These two values should place a reasonable limit on the amount of bandwith and downloaded bytes from the server.  This feature can be switched off by setting the time interval to 0.');
	break;

case 'MEDIA_DIRECTORY_LEVELS':
	$title=i18n::translate('Multimedia directory levels to keep');
	$text=i18n::translate('A value of 0 will ignore all directories in the file path for the media object.  A value of 1 will retain the first directory containing this image.  Increasing the numbers increases number of parent directories to retain in the path.<br /><br />For example, if you link an image in your GEDCOM with a path like <b>C:\Documents&nbsp;and&nbsp;Settings\User\My&nbsp;Documents\My&nbsp;Pictures\Genealogy\Surname&nbsp;Line\grandpa.jpg</b>, a value of 0 will translate this path to <b>./media/grandpa.jpg</b>.  A value of 1 will translate this to <b>./media/Surname&nbsp;Line/grandpa.jpg</b>, etc.  Most people will only need to use a 0.  However, it is possible that some media objects kept in different directories have identical names and would overwrite each other when this option is set to 0.  Non-zero settings allow you to keep some organization in your media thereby preventing name collisions.');
	break;

case 'MEDIA_DIRECTORY':
	$title=i18n::translate('Multimedia directory');
	$text=i18n::translate('The path to a readable and writable directory where <b>webtrees</b> should store media files (include the trailing "/").  <b>webtrees</b> does not require this directory\'s name to be "media".  You can choose any name you like.<br /><br />Even though the Media Firewall feature lets you store media files in an area of the server\'s file space that is not accessible from the Internet, the directory named here must still exist and must be readable from the Internet and writable by <b>webtrees</b>.  For more information, please refer to the Media Firewall configuration options in the Multimedia section of the GEDCOM configuration page.');
	break;

case 'MEDIA_EXTERNAL':
	$title=i18n::translate('Keep links');
	$text=i18n::translate('When a multimedia link is found starting with for example http://, ftp://, mms:// it will not be altered when set to <b>Yes</b>. For example, http://www.myfamily.com/photo/dad.jpg will stay http://www.myfamily.com/photo/dad.jpg.  When set to <b>No</b>, the link will be handled as a standard reference and the media depth will be used.  For example: http://www.myfamily.com/photo/dad.jpg will be changed to ./media/dad.jpg');
	break;

case 'MEDIA_FIREWALL_ROOTDIR':
	$title=i18n::translate('Media firewall root directory');
	$text=i18n::translate('Directory in which the protected Media directory can be created.  When this field is empty, the <b>%s</b> directory will be used.', $INDEX_DIRECTORY);
	break;

case 'MEDIA_FIREWALL_THUMBS':
	$title=i18n::translate('Protect thumbnails of protected images');
	$text=i18n::translate('When an image is in the protected Media directory, should its thumbnail be protected as well?');
	break;

case 'MEDIA_ID_PREFIX':
	$title=i18n::translate('Media ID prefix');
	$text=i18n::translate('When a new media record is added online in <b>webtrees</b>, a new ID for that media will be generated automatically. The media ID will have this prefix.');
	break;

case 'META_AUDIENCE':
	$title=i18n::translate('Audience META tag');
	$text=i18n::translate('The value to place in the Audience meta tag in the HTML page header.');
	break;

case 'META_AUTHOR':
	$title=i18n::translate('Author META tag');
	$text=i18n::translate('The value to place in the Author meta tag in the HTML page header.  Leave this field empty to use the full name of the Genealogy contact.');
	break;

case 'META_COPYRIGHT':
	$title=i18n::translate('Copyright META tag');
	$text=i18n::translate('The value to place in the Copyright meta tag in the HTML page header.  Leave this field empty to use the full name of the Genealogy contact.');
	break;

case 'META_DESCRIPTION':
	$title=i18n::translate('Description META tag');
	$text=i18n::translate('The value to place in the Description meta tag in the HTML page header.  Leave this field empty to use the title of the currently active database.');
	break;

case 'META_KEYWORDS':
	$title=i18n::translate('Keywords META tag');
	$text=i18n::translate('The value to place in the Keywords meta tag in the HTML page header.  Some search engines will use the Keywords meta tag to help index your page.<br /><br />The Most Common Surnames list that appears in the GEDCOM Statistics block on your Home Page can also be added to anything you enter here.');
	break;

case 'META_PAGE_TOPIC':
	$title=i18n::translate('Page-topic META tag');
	$text=i18n::translate('The value to place in the Page-topic meta tag in the HTML page header.  Leave this field empty to use the title of the currently active database.');
	break;

case 'META_PAGE_TYPE':
	$title=i18n::translate('Page-type META tag');
	$text=i18n::translate('The value to place in the Page-type meta tag in the HTML page header.');
	break;

case 'META_PUBLISHER':
	$title=i18n::translate('Publisher META tag');
	$text=i18n::translate('The value to place in the Publisher meta tag in the HTML page header.  Leave this field empty to use the full name of the Genealogy contact.');
	break;

case 'META_REVISIT':
	$title=i18n::translate('How often should crawlers revisit META tag');
	$text=i18n::translate('The value to place in the Revisit meta tag in the HTML page header.  Some web crawlers ignore this value.');
	break;

case 'META_ROBOTS':
	$title=i18n::translate('Robots META tag');
	$text=i18n::translate('The value to place in the Robots meta tag in the HTML page header.  Some robots or web crawlers ignore this value.');
	break;

case 'META_TITLE':
	$title=i18n::translate('Add to TITLE header tag');
	$text=i18n::translate('Anything on this line will be added to the TITLE tag in the HTML page header after the regular page title and before the <b>webtrees</b> credit.');
	break;

case 'MULTI_MEDIA':
	$title=i18n::translate('Enable multimedia features');
	$text=i18n::translate('GEDCOM 5.5.1 allows you to link pictures, videos, and other multimedia objects into your GEDCOM.  If you do not include multimedia objects in your GEDCOM, you can disable the multimedia features by setting this value to <b>No</b>.<br /><br />See the Multimedia section in the <a href="readme.txt">readme.txt</a> file for more information about including media in your site.');
	break;

case 'PAGE_AFTER_LOGIN':
	$title=i18n::translate('Page to show after login');
	$text=i18n::translate('Which page should users see after they have logged in?<br /><br />The choice made here determines whether a successful Login causes My Page or the Home Page to appear when the login is done from the Home Page.<br /><br />A Login done from the link at the top of every other page will return the user to that page.');
	break;

case 'PEDIGREE_FULL_DETAILS':
	$title=i18n::translate('Show birth and death details on charts');
	$text=i18n::translate('This option controls whether or not to show the Birth and Death details of an individual on charts.');
	break;

case 'PEDIGREE_GENERATIONS':
	$title=i18n::translate('Number of generations');
	$text=i18n::translate('Here you can set the number of generations to display on this page.<br /><br />The right number for you depends of the size of your screen and whether you show details or not.  Processing time will increase as you increase the number of generations.');
	break;

case 'PEDIGREE_MAP_clustersize':
	$title=i18n::translate('Cluster size');
	$text=i18n::translate('The number of markers to be placed at one point before a trail of pins is started in a north east line behind the younger generations. The \'trail\' is usually only visable at high zoom values.');
	break;

case 'PEDIGREE_MAP_hideflags':
	$title=i18n::translate('Hide flags');
	$text=i18n::translate('Hide the flags that are configured in the googlemap module. Ususally these are for countries and states.  This serves as a visual queue that the markers around the flag are from the general area, and not the specific spot.');
	break;

case 'PEDIGREE_MAP_hidelines':
	$title=i18n::translate('Hide lines');
	$text=i18n::translate('Hide the lines connecting the child to each parent if they exist on the map.');
	break;

case 'PEDIGREE_LAYOUT':
	$title=i18n::translate('Default pedigree chart layout');
	$text=i18n::translate('This option indicates whether the Pedigree chart should be generated in landscape or portrait mode.');
	break;

case 'PEDIGREE_ROOT_ID':
	$title=i18n::translate('Default person for pedigree and descendancy charts');
	$text=i18n::translate('Set the ID of the default person to display on Pedigree and Descendancy charts.');
	break;

case 'PEDIGREE_SHOW_GENDER':
	$title=i18n::translate('Show gender icon on charts');
	$text=i18n::translate('This option controls whether or not to show the individual\'s gender icon on charts.<br /><br />Since the gender is also indicated by the color of the box, this option doesn\'t conceal the gender. The option simply removes some duplicate information from the box.');
	break;

case 'WT_MEMORY_LIMIT':
	$title=i18n::translate('Memory limit');
	$text=i18n::translate('The maximum amount of memory that can be consumed by <b>webtrees</b> scripts.  The default is 32 Mb.  Many hosts disable this option in their PHP configuration; changing this value may not actually affect the current maximum memory setting.');
	break;

case 'WT_SESSION_SAVE_PATH':
	$title=i18n::translate('Session save path');
	$text=i18n::translate('The path to store <b>webtrees</b> session files.<br /><br />Some hosts do not have PHP configured properly and sessions are not maintained between page requests.  This option lets site administrators overcome that problem by saving files in one of their local directories.  The ./index/ directory is a good choice if you need to change this.  The default is to leave the field empty, which will use the Save path as configured in <i>php.ini</i>.');
	break;

case 'WT_SESSION_TIME':
	$title=i18n::translate('Session timeout');
	$text=i18n::translate('The time in seconds that a <b>webtrees</b> session remains active before requiring a login.  The default is 7200, which is 2 hours.');
	break;

case 'WT_SIMPLE_MAIL':
	$title=i18n::translate('Use simple mail headers in external mails');
	$text=i18n::translate('In normal mail headers for external mails, the email address as well as the name are used. Some mail systems will not accept this. When set to <b>Yes</b>, only the email address will be used.');
	break;

case 'WT_SMTP_ACTIVE':
	$title=i18n::translate('Use SMTP to send external mails');
	$text=i18n::translate('Use SMTP to send e-mails from <b>webtrees</b>.<br /><br />This option requires access to an SMTP mail server.  When set to <b>No</b> <b>webtrees</b> will use the e-mail system built into PHP on this server.');
	break;

case 'WT_SMTP_AUTH_PASS':
	$title=i18n::translate('Password');
	$text=i18n::translate('The password required for authentication with the SMTP server.');
	break;

case 'WT_SMTP_AUTH_USER':
	$title=i18n::translate('Username');
	$text=i18n::translate('The user name required for authentication with the SMTP server.');
	break;

case 'WT_SMTP_AUTH':
	$title=i18n::translate('Username and password');
	$text=i18n::translate('Use name and password authentication to connect to the SMTP server.<br /><br />Some SMTP servers require all connections to be authenticated before they will accept outbound e-mails.');
	break;

case 'WT_SMTP_FROM_NAME':
	$title=i18n::translate('Sender name');
	$text=i18n::translate('Enter the name to be used in the &laquo;From:&raquo; field of e-mails originating at this site.<br /><br />For example, if your name is <b>John Smith</b> and you are the site administrator for a site that is  known as <b>Jones Genealogy</b>, you could enter something like <b>John Smith</b> or <b>Jones Genealogy</b> or even <b>John Smith, Administrator: Jones Genealogy</b>.  You may enter whatever you wish, but HTML is not permitted.');
	break;

case 'WT_SMTP_HELO':
	$title=i18n::translate('Sending domain name');
	$text=i18n::translate('This is the domain part of a valid e-mail address on the SMTP server.<br /><br />For example, if you have an e-mail account such as <b>yourname@abc.xyz.com</b>, you would enter <b>abc.xyz.com</b> here.');
	break;

case 'WT_SMTP_HOST':
	$title=i18n::translate('Outgoing server (SMTP) name');
	$text=i18n::translate('This is the name of the SMTP mail server.  Example: <b>smtp.foo.bar.com</b>.<br /><br />Configuration values for some e-mail providers:<br /><br /><b>Gmail<br /></b><br /><b>Outgoing server (SMTP) name:</b> smtp.gmail.com<br /><b>SMTP Port:</b> 465 or 587<br /><b>Secure connection:</b> SSL<br /><br /><b>Hotmail<br /></b><br /><b>Outgoing server (SMTP) name:</b> smtp.live.com<br /><b>SMTP Port:</b> 25 or 587<br /><b>Secure connection:</b> TLS<br /><br /><b>Yahoo Mail Plus (currently a paid service)<br /></b><br /><b>Outgoing server (SMTP) name:</b> smtp.mail.yahoo.com<br /><b>SMTP Port:</b> 25');
	break;

case 'WT_SMTP_PORT':
	$title=i18n::translate('SMTP port');
	$text=i18n::translate('The port number to be used for connections to the SMTP server.  Generally, this is port <b>25</b>.');
	break;

case 'WT_SMTP_SSL':
	$title=i18n::translate('Secure connection');
	$text=i18n::translate('Transport Layer Security (TLS) and Secure Sockets Layer (SSL) are Internet data encryption protocols.<br /><br />TLS 1.0, 1.1 and 1.2 are standardized developments of SSL 3.0. TLS 1.0 and SSL 3.1 are equivalent. Further work on SSL is now done under the new name, TLS.<br /><br />If your SMTP Server requires the SSL protocol during login, you should select the <b>SSL</b> option. If your SMTP Server requires the TLS protocol during login, you should select the <b>TLS</b> option.');
	break;

case 'WT_STORE_MESSAGES':
	$title=i18n::translate('Allow messages to be stored online');
	$text=i18n::translate('Specifies whether messages sent through <b>webtrees</b> can be stored in the database.  If set to <b>Yes</b> users will be able to retrieve their messages when they login to <b>webtrees</b>.  If set to <b>No</b> messages will only be emailed.');
	break;

case 'WEBTREES_EMAIL':
	$title=i18n::translate('<b>webtrees</b> reply address');
	$text=i18n::translate('E-mail address to be used in the &laquo;From:&raquo; field of e-mails that <b>webtrees</b> creates automatically.<br /><br /><b>webtrees</b> can automatically create e-mails to notify administrators of changes that need to be reviewed.  <b>webtrees</b> also sends notification e-mails to users who have requested an account.<br /><br />Usually, the &laquo;From:&raquo; field of these automatically created e-mails is something like <i>From: webtrees-noreply@yoursite</i> to show that no response to the e-mail is required.  To guard against spam or other e-mail abuse, some e-mail systems require each message\'s &laquo;From:&raquo; field to reflect a valid e-mail account and will not accept messages that are apparently from account <i>webtrees-noreply</i>.');
	break;

case 'POSTAL_CODE':
	$title=i18n::translate('Postal code position');
	$text=i18n::translate('Different countries use different ways to write the address. This option will enable you to place the postal code either before or after the city name.');
	break;

case 'PREFER_LEVEL2_SOURCES':
	$title=i18n::translate('Source type');
	$text=i18n::translate('When adding new close relatives, you can add source citations to the records (e.g. INDI, FAM) or the facts (BIRT, MARR, DEAT).  This option controls which checkboxes are ticked by default.');
	break;

case 'PRIVACY_BY_RESN':
	$title=i18n::translate('Use GEDCOM (RESN) privacy restriction');
	$text=i18n::translate('The GEDCOM 5.5.1 specification includes the option of using RESN tags to set Privacy options for people and facts in the GEDCOM file.  Enabling this option will tell the program to look for level 1 RESN tags in GEDCOM records.  Level 2+ RESN tags are automatically applied and will not be affected by this setting.  Note that this might slow down some of the functions of <b>webtrees</b> such as the Individual list.');
	break;

case 'PRIVACY_BY_YEAR':
	$title=i18n::translate('Limit privacy by age of event');
	$text=i18n::translate('The <b>Limit Privacy by age of event</b> setting will hide the details of people based on how old they were at specific events regardless of whether they are dead or alive.<br /><br />Use this setting along with the <b>Age at which to assume a person is dead</b> setting.  For example, if you made the Age setting 100 and set this option to <b>Yes</b>, all persons, alive or dead, born less than 100 years ago would be set to private.  People who were married less than 85 years ago and people who died less than 75 years ago would also be marked as private.  Please note that using this option will slow down your performance somewhat.');
	break;

case 'QUICK_REQUIRED_FACTS':
	$title=i18n::translate('Facts to always show on quick update');
	$text=i18n::translate('This is a comma separated list of GEDCOM fact tags that will always be shown on the Quick Update form whether or not they already exist in the individual\'s record.  For example, if BIRT is in the list, fields for birth date and birth place will always be shown on the form.');
	break;

case 'QUICK_REQUIRED_FAMFACTS':
	$title=i18n::translate('Facts for families to always show on quick update');
	$text=i18n::translate('This is a comma separated list of GEDCOM fact tags that will always be shown on the Family tabs of the Quick Update form whether or not they already exist in the family\'s record.  For example, if MARR is in the list, then fields for marriage date and marriage place will always be shown on the form.');
	break;

case 'REPO_FACTS_ADD':
	$title=i18n::translate('Repository add facts');
	$text=i18n::translate('This is the list of GEDCOM facts that your users can add to repositories.  You can modify this list by removing or adding fact names, even custom ones, as necessary.  Fact names that appear in this list must not also appear in the <i>Unique Repository Facts</i> list.');
	break;

case 'REPO_FACTS_QUICK':
	$title=i18n::translate('Quick repository facts');
	$text=i18n::translate('This is the short list of GEDCOM repository facts that appears next to the full list and can be added with a single click.');
	break;

case 'REPO_FACTS_UNIQUE':
	$title=i18n::translate('Unique repository facts');
	$text=i18n::translate('This is the list of GEDCOM facts that your users can only add <u>once</u> to repositories.  For example, if NAME is in this list, users will not be able to add more than one NAME record to a repository.  Fact names that appear in this list must not also appear in the <i>Repository Add Facts</i> list.');
	break;

case 'REPO_ID_PREFIX':
	$title=i18n::translate('Repository ID prefix');
	$text=i18n::translate('When a new repository record is added online in <b>webtrees</b>, a new ID for that repository will be generated automatically. The repository ID will have this prefix.');
	break;

case 'REQUIRE_ADMIN_AUTH_REGISTRATION':
	$title=i18n::translate('Require an administrator to approve new user registrations');
	$text=i18n::translate('If the option <b>Allow visitors to request account registration</b> is enabled this setting controls whether the admin must approve the registration.<br /><br />Setting this to <b>Yes</b> will require that all new users first verify themselves and then be approved by an admin before they can login.  With this setting on <b>No</b>, the <b>User approved by Admin</b> checkbox will be checked automatically when users verify their account, thus allowing an immediate login afterwards without admin intervention.');
	break;

case 'REQUIRE_AUTHENTICATION':
	$title=i18n::translate('Require visitor authentication');
	$text=i18n::translate('Enabling this option will force all visitors to login before they can view any data on the site.');
	break;

case 'RSS_FORMAT':
	$title=i18n::translate('RSS Format');
	$text=i18n::translate('The format to be used as the default feed format for the site. The numeric suffixes <u>do not</u> indicate version: they identify formats.  For example, RSS 2.0 is not newer than RSS 1.0, but a different format. Feed readers should be able to read any format.<br /><br />This <a href=\'http://en.wikipedia.org/wiki/RSS\' target=\'_blank\'title=\'Wikipedia article\'><b>Wikipedia article</b></a> contains comprehensive information and links about RSS and the various RSS formats.');
	break;

case 'SAVE_WATERMARK_IMAGE':
	$title=i18n::translate('Store watermarked full size images on server?');
	$text=i18n::translate('If the Media Firewall is enabled, should copies of watermarked full size images be stored on the server in addition to the same images without watermarks?<br /><br />When set to <b>Yes</b>, full-sized watermarked images will be produced more quickly at the expense of higher server disk space requirements.');
	break;

case 'SAVE_WATERMARK_THUMB':
	$title=i18n::translate('Store watermarked thumbnails on server?');
	$text=i18n::translate('If the Media Firewall is enabled, should copies of watermarked thumbnails be stored on the server in addition to the same thumbnails without watermarks?<br /><br />When set to <b>Yes</b>, media lists containing watermarked thumbnails will be produced more quickly at the expense of higher server disk space requirements.');
	break;

case 'SEARCHLOG_CREATE':
	$title=i18n::translate('Archive searchLog files');
	$text=i18n::translate('How often should the program archive Searchlog files.');
	break;

case 'SECURITY_CHECK_GEDCOM_DOWNLOADABLE':
	$title=i18n::translate('Check if GEDCOM files are downloadable');
	$text=i18n::translate('For security reasons, GEDCOM files should not be in a location where they can be directly downloaded, thus bypassing privacy checks. Clicking this link will check if your GEDCOM files can be downloaded over the network.<br /><br />On some systems this check has been known to take a really long time or not even complete.  If that is the case for you, then you should try to point your browser directly at your GEDCOM to see if it can be downloaded.');
	break;

case 'SERVER_URL':
	$title=i18n::translate('<b>webtrees</b> URL');
	$text=i18n::translate('If you use https or a port other than the default, you will need to enter the URL to access your server here.');
	break;

case 'SHOW_AGE_DIFF':
	$title=i18n::translate('Show date differences');
	$text=i18n::translate('This option controls whether or not the Close Relatives tab should show differences between birth dates of spouses, between marriage date and birth date of first child, and between birth dates of children.');
	break;

case 'SHOW_CONTEXT_HELP':
	$title=i18n::translate('Show contextual <b>?</b> help links');
	$text=i18n::translate('This option will enable links, identified by question marks, next to items on many pages.  These links allow users to get information or help about those items.');
	break;

case 'SHOW_COUNTER':
	$title=i18n::translate('Show hit counters');
	$text=i18n::translate('Show hit counters on Portal and Individual pages.');
	break;

case 'SHOW_DEAD_PEOPLE':
	$title=i18n::translate('Show dead people');
	$text=i18n::translate('Set the privacy access level for all dead people.');
	break;

case 'SHOW_EMPTY_BOXES':
	$title=i18n::translate('Show empty boxes on pedigree charts');
	$text=i18n::translate('This option controls whether or not to show empty boxes on Pedigree charts.');
	break;

case 'SHOW_EST_LIST_DATES':
	$title=i18n::translate('Show estimated dates for birth and death');
	$text=i18n::translate('This option controls whether or not to show estimated dates for birth and death instead of leaving blanks on individual lists and charts for individuals whose dates are not known.');
	break;

case 'SHOW_FACT_ICONS':
	$title=i18n::translate('Show fact icons');
	$text=i18n::translate('Set this to <b>Yes</b> to display icons near Fact names on the Personal Facts and Details page.  Fact icons will be displayed only if they exist in the <i>images/facts</i> directory of the current theme.');
	break;

case 'SHOW_GEDCOM_RECORD':
	$title=i18n::translate('Allow users to see raw GEDCOM records');
	$text=i18n::translate('Setting this to <b>Yes</b> will place links on individuals, sources, and families to let users bring up another window containing the raw data taken right out of the GEDCOM file.');
	break;

case 'SHOW_HIGHLIGHT_IMAGES':
	$title=i18n::translate('Show highlight images in people boxes');
	$text=i18n::translate('If you have enabled multimedia in your site, you can have <b>webtrees</b> display a thumbnail image next to the person\'s name in charts and boxes.<br /><br />Currently, <b>webtrees</b> uses the first multimedia object listed in the GEDCOM record as the highlight image.  For people with multiple images, you should arrange the multimedia objects such that the one you wish to be highlighted appears first, before any others.<br /><br />See the Multimedia section in the <a href="readme.txt">readme.txt</a> file for more information about including media in your site.');
	break;

case 'SHOW_ID_NUMBERS':
	$title=i18n::translate('Show ID numbers next to names');
	$text=i18n::translate('This option controls whether or not to show ID numbers in parentheses after names on charts and lists.');
	break;

case 'SHOW_LAST_CHANGE':
	$title=i18n::translate('Show GEDCOM record last change date on lists');
	$text=i18n::translate('This option controls whether or not to show GEDCOM record last change date on lists.');
	break;

case 'SHOW_LDS_AT_GLANCE':
	$title=i18n::translate('Show LDS ordinance codes in chart boxes');
	$text=i18n::translate('Setting this option to <b>Yes</b> will show status codes for LDS ordinances in chart boxes.<ul><li><b>B</b> - Baptism</li><li><b>E</b> - Endowed</li><li><b>S</b> - Sealed to spouse</li><li><b>P</b> - Sealed to parents</li></ul>A person who has all of the ordinances done will have <b>BESP</b> printed after their name.  Missing ordinances are indicated by <b>_</b> in place of the corresponding letter code.  For example, <b>BE__</b> indicates missing <b>S</b> and <b>P</b> ordinances.');
	break;

case 'SHOW_LEVEL2_NOTES':
	$title=i18n::translate('Show all notes and source references on notes and sources tabs');
	$text=i18n::translate('This option controls whether Notes and Source references that are attached to Facts should be shown on the Notes and Sources tabs of the Individual page.<br /><br />Ordinarily, the Notes and Sources tabs show only Notes and Source references that are attached directly to the individual\'s database record.  These are <i>level 1</i> Notes and Source references.<br /><br />The <b>Yes</b> option causes these tabs to also show Notes and Source references that are part of the various Facts in the individual\'s database record.  These are <i>level 2</i> Notes and Source references because the various Facts are at level 1.');
	break;

case 'SHOW_LIST_PLACES':
	$title=i18n::translate('Place levels to show on lists');
	$text=i18n::translate('This determines how much of the Place information is shown in the Place fields on lists.<br /><br />Setting the value to <b>9</b> will ensure that all Place information will be shown.  Setting the value to <b>0</b> (zero) will hide places completely.  Setting the value to <b>1</b> will show the topmost level, which is normally the country.  Setting it to <b>2</b> will show the topmost two levels.  The second topmost level, below the country, is often the state, province, or territory. Etc.');
	break;

case 'SHOW_LIVING_NAMES':
	$title=i18n::translate('Show living names');
	$text=i18n::translate('Should the names of living people be shown to the public?');
	break;

case 'SHOW_MARRIED_NAMES':
	$title=i18n::translate('Show married names on individual list');
	$text=i18n::translate('This option will show the married names of females on the Individual list.  This option requires that you calculate the married names when you import the GEDCOM file.');
	break;

case 'SHOW_MEDIA_DOWNLOAD':
	$title=i18n::translate('Show download link in media viewer');
	$text=i18n::translate('The Media Viewer can show a link which, when clicked, will download the Media file to the local PC.<br /><br />You may want to hide the download link for security reasons.');
	break;

case 'SHOW_MEDIA_FILENAME':
	$title=i18n::translate('Show file name in media viewer');
	$text=i18n::translate('The Media Viewer can show the name of the Media file being viewed.  This option determines whether that file name is shown to users or not.<br /><br />You may want to hide the file name for security reasons.');
	break;

case 'SHOW_MULTISITE_SEARCH':
	$title=i18n::translate('Show multi-site search');
	$text=i18n::translate('Multi-site search allows users to search across multiple <b>webtrees</b> websites which you have setup in the Manage Sites administration area or remotely linked to.  This option controls whether the Multi-site Search feature is available to everyone or only to authenticated users.');
	break;

case 'SHOW_NO_WATERMARK':
	$title=i18n::translate('Who can view non-watermarked images?');
	$text=i18n::translate('If the Media Firewall is enabled, users will see watermarks if they do not have the privilege level specified here.');
	break;

case 'SHOW_PARENTS_AGE':
	$title=i18n::translate('Show age of parents next to child\'s birthdate');
	$text=i18n::translate('This option controls whether or not to show age of father and mother next to child\'s birthdate on charts.');
	break;

case 'SHOW_PEDIGREE_PLACES':
	$title=i18n::translate('Place levels to show in person boxes');
	$text=i18n::translate('This sets how much of the place information is shown in the person boxes on charts.<br /><br />Setting the value to 9 will guarantee to show all place levels.  Setting the value to 0 will hide places completely.  Setting the value to 1 will show the first level, setting it to 2 will show the first two levels, etc.');
	break;

case 'SHOW_PRIVATE_RELATIONSHIPS':
	$title=i18n::translate('Show private relationships');
	$text=i18n::translate('This option will retain family links in privatized records.  This means that you will see empty "private" boxes on the pedigree chart and on other charts with private people.<br /><br />This is similar to the behavior of <b>webtrees</b> versions prior to v4.0.<br /><br />This setting is off by default.  It is recommended instead of turning this on, to point your pedigree root person in your GEDCOM configuration to a person who is not private.');
	break;

case 'SHOW_REGISTER_CAUTION':
	$title=i18n::translate('Show acceptable use agreement on «Request new user account» page');
	$text=i18n::translate('When set to <b>Yes</b>, the following message will appear above the input fields on the «Request new user account» page:<div class="list_value_wrap"><div class="largeError">Notice:</div><div class="error">By completing and submitting this form, you agree:<ul><li>to protect the privacy of living people listed on our site;</li><li>and in the text box below, to explain to whom you are related, or to provide us with information on someone who should be listed on our site.</li></ul></div></div>');
	break;

case 'SHOW_RELATIVES_EVENTS':
	$title=i18n::translate('Show events of close relatives on individual page');
	$text=i18n::translate('Births, marriages, and deaths of relatives are important events in one\'s life. This option controls whether or not to show these events on the <i>Personal facts and details</i> tab on the Individual page.<br /><br />The events affected by this option are:<ul><li>Death of spouse</li><li>Birth and death of children</li><li>Death of parents</li><li>Birth and death of siblings</li><li>Death of grand-parents</li><li>Birth and death of parents\' siblings</li></ul>');
	break;

case 'SHOW_SOURCES':
	$title=i18n::translate('Show sources');
	$text=i18n::translate('Set the privacy access level for all Sources.  If the user does not have access to Sources, the Source list will be removed from the Lists menu and the Sources tab will not be shown on the Individual Details page.');
	break;

case 'SHOW_SPIDER_TAGLINE':
	$title=i18n::translate('Show spider tagline');
	$text=i18n::translate('On pages generated for search engines, display as the last line the particular search engine the page detected.  If this option is on, it can bias Google&reg; AdSense towards search engine optimization tools.');
	break;

case 'SHOW_STATS':
	$title=i18n::translate('Show execution statistics');
	$text=i18n::translate('Show runtime statistics and database queries at the bottom of every page.');
	break;

case 'SOURCE_ID_PREFIX':
	$title=i18n::translate('Source ID prefix');
	$text=i18n::translate('When a new source record is added online in <b>webtrees</b>, a new ID for that source will be generated automatically.  The source ID will have this prefix.');
	break;

case 'SOUR_FACTS_ADD':
	$title=i18n::translate('Source add facts');
	$text=i18n::translate('This is the list of GEDCOM facts that your users can add to sources.  You can modify this list by removing or adding fact names, even custom ones, as necessary.  Fact names that appear in this list must not also appear in the <i>Unique Source Facts</i> list.');
	break;

case 'SOUR_FACTS_QUICK':
	$title=i18n::translate('Quick source facts');
	$text=i18n::translate('This is the short list of GEDCOM source facts that appears next to the full list and can be added with a single click.');
	break;

case 'SOUR_FACTS_UNIQUE':
	$title=i18n::translate('Unique source facts');
	$text=i18n::translate('This is the list of GEDCOM facts that your users can only add <u>once</u> to sources.  For example, if TITL is in this list, users will not be able to add more than one TITL record to a source.  Fact names that appear in this list must not also appear in the <i>Source Add Facts</i> list.');
	break;

case 'SPLIT_PLACES':
	$title=i18n::translate('Split places in edit mode');
	$text=i18n::translate('Set this to <b>Yes</b> to split each place name by commas into subfields for easier editing.  Example :<br /><ol><li>Default mode<br /><u>Place</u>: Half Moon Bay, San Mateo, California, USA<br /><br /></li><li>Split mode<br /><u>Country</u>: USA<br /><u>State</u>: California<br/><u>County</u>: San Mateo<br/><u>City</u>: Half Moon Bay</li></ol>');
	break;

case 'SUBLIST_TRIGGER_F':
	$title=i18n::translate('Maximum number of family names');
	$text=i18n::translate('Long lists of families with the same name can be broken into smaller sub-lists according to the first letter of the given name.<br /><br />This option determines when sub-listing of family names will occur.  To disable sub-listing completely, set this option to zero.');
	break;

case 'SUBLIST_TRIGGER_I':
	$title=i18n::translate('Maximum number of surnames');
	$text=i18n::translate('Long lists of persons with the same surname can be broken into smaller sub-lists according to the first letter of the individual\'s given name.<br /><br />This option determines when sub-listing of surnames will occur.  To disable sub-listing completely, set this option to zero.');
	break;

case 'SUPPORT_METHOD':
	$title=i18n::translate('Support method');
	$text=i18n::translate('The method to be used to contact the Support contact about technical questions.<ul><li>The <b>Mailto link</b> option will create a "mailto" link that can be clicked to send an email using the mail client on the user\'s PC.</li><li>The <b>webtrees internal messaging</b> option will use a messaging system internal to <b>webtrees</b>, and no emails will be sent.</li><li>The <b>Internal messaging with emails</b> option is the default.  It will use the <b>webtrees</b> messaging system and will also send copies of the messages via email.</li><li>The <b>webtrees sends emails with no storage</b> option allows <b>webtrees</b> to handle the messaging and will send the messages as emails, but will not store the messages internally.  This option is similar to the <b>Mailto link</b> option, except that the message will be sent by <b>webtrees</b> instead of the user\'s workstation.</li><li>The <b>No contact method</b> option results in your users having no way of contacting you.</li></ul>');
	break;

case 'SURNAME_LIST_STYLE':
	$title=i18n::translate('Surname list style');
	$text=i18n::translate('Lists of surnames, as they appear in the Top 10 Surnames block, the Individuals, and the Families, can be shown in different styles.<ul><li><b>Table</b>&nbsp;&nbsp;&nbsp;In this style, the surnames are shown in a table that can be sorted either by surname or by count.</li><li><b>Tagcloud</b>&nbsp;&nbsp;&nbsp;In this style, the surnames are shown in a list, and the font size used for each name depends on the number of occurrences of that name in the database.  The list is not sortable.</li></ul>');
	break;

case 'SURNAME_TRADITION':
	$title=i18n::translate('Surname tradition');
	$text=i18n::translate('When you add new members to a family, <b>webtrees</b> can supply default values for surnames according to regional custom.<br /><br /><ul><li>In the <b>Paternal</b> tradition, all family members share the father\'s surname.</li><li>In the <b>Spanish</b> and <b>Portuguese</b> tradition, children receive a surname from each parent.</li><li>In the <b>Icelandic</b> tradition, children receive their male parent\'s given name as a surname, with a suffix that denotes gender.</li><li>In the <b>Polish</b> tradition, all family members share the father\'s surname. For some surnames, the suffix indicates gender.  The suffixes <i>ski</i>, <i>cki</i>, and <i>dzki</i> indicate male, while the corresponding suffixes <i>ska</i>, <i>cka</i>, and <i>dzka</i> indicate female.</li></ul>');
	break;

case 'SYNC_GEDCOM_FILE':
	$title=i18n::translate('Synchronize edits into GEDCOM file');
	$text=i18n::translate('In past versions of <b>webtrees</b> the pending edits were stored in the GEDCOM file and the changed records were then "accepted" into the database.  Starting with v4.1 pending changes are no longer stored in the GEDCOM file but in the changes file.  <br /><br />Setting this value to true will update the GEDCOM file when changes are accepted into the database.  This will keep the GEDCOM file synchronized with the database.  For greater compatibility with previous versions the default value of this field is on.<br /><br />You may want to turn it off to conserve memory when accepting changes.');
	break;

case 'THEME_DIR':
	$title=i18n::translate('Theme directory');
	$text=i18n::translate('The directory where your <b>webtrees</b> theme files are kept.<br /><br />You may customize any of the standard themes that come with <b>webtrees</b> to give your site a unique look and feel.  See the Theme Customization section of the <a href="readme.txt">readme.txt</a> file for more information.');
	break;

case 'THUMBNAIL_WIDTH':
	$title=i18n::translate('Width of generated thumbnails');
	$text=i18n::translate('This is the width (in pixels) that the program will use when automatically generating thumbnails.  The default setting is 100.');
	break;

case 'TIME_LIMIT':
	$title=i18n::translate('PHP time limit');
	$text=i18n::translate('The maximum time in seconds that <b>webtrees</b> should be allowed to run.<br /><br />The default is 1 minute.  Depending on the size of your GEDCOM file, you may need to increase this time limit when you need to build the indexes.  Set this value to 0 to allow PHP to run forever.<br /><br />CAUTION: Setting this to 0 or setting it too high could cause your site to hang on certain operating systems until the script finishes.  Setting it to 0 means it may never finish until a server administrator kills the process or restarts the server.  A large Pedigree chart can take a very long time to run; leaving this value as low as possible ensures that someone cannot crash your server by requesting an excessively large chart.');
	break;

case 'UNDERLINE_NAME_QUOTES':
	$title=i18n::translate('Underline names in quotes');
	$text=i18n::translate('Many programs will place the preferred given name in "quotes" in the GEDCOM.  The usual convention for this is to underline the preferred given name.  Enabling this option will convert any names surrounded by quotes to &lt;span&gt; with a CSS class of "starredname".<br /><br />For example, if the name in the GEDCOM were 1&nbsp;NAME&nbsp;Gustave&nbsp;"Jean&nbsp;Paul"&nbsp;Charles&nbsp;/Wilson/ enabling this option would change the part of the name enclosed in quotes to &lt;span&nbsp;class="starredname"&gt;Jean&nbsp;Paul&lt;/span&gt; for printing purposes.  Depending on other settings, the browser would then display that name as <b>Gustave&nbsp;<u>Jean&nbsp;Paul</u>&nbsp;Charles&nbsp;Wilson</b> or <b>Wilson,&nbsp;Gustave&nbsp;<u>Jean&nbsp;Paul</u> Charles</b>');
	break;

case 'USE_GEONAMES':
	$title=i18n::translate('Use GeoNames database');
	$text=i18n::translate('Should the GeoNames database be used to provide more suggestions for place names?<br /><br />When this option is set to <b>Yes</b>, the GeoNames database will be queried to supply suggestions for the place name being entered.  When set to <b>No</b>, only the current genealogical database will be searched.  As you enter more of the place name, the suggestion will become more precise.  This option can slow down data entry, particularly if your Internet connection is slow.<br /><br />The GeoNames geographical database is accessible free of charge. It currently contains over 8,000,000 geographical names.');
	break;

case 'USE_MEDIA_FIREWALL':
	$title=i18n::translate('Use media firewall');
	$text=i18n::translate('See the Wiki for a description of how to use the Media Firewall. <a href="#WT_WEBTREES_WIKI#/en/index.php?title=Media_Firewall" target="_blank">#WT_WEBTREES_WIKI#</a>');
	break;

case 'USE_MEDIA_VIEWER':
	$title=i18n::translate('Use media viewer');
	$text=i18n::translate('When this option is <b>Yes</b>, clicking on images will produce the Media Viewer page.  This page shows the details of the image.  If you have sufficient rights, you can also edit these details.<br /><br />When this option is <b>No</b>, clicking on images will produce a full-size image in a new window.');
	break;

case 'USE_REGISTRATION_MODULE':
	$title=i18n::translate('Allow visitors to request account registration');
	$text=i18n::translate('Gives visitors the option of registering themselves for an account on the site.<br /><br />The visitor will receive an email message with a code to verify his application for an account.  After verification, the Administrator will have to approve the registration before it becomes active.');
	break;

case 'USE_RELATIONSHIP_PRIVACY':
	$title=i18n::translate('Use relationship privacy');
	$text=i18n::translate('<b>No</b> means that authenticated users can see the details of all living people.  <b>Yes</b> means that users can only see the private information of living people they are related to.<br /><br />This option sets the default for all users who have access to this genealogical database.  The Administrator can override this option for individual users by editing the user\'s account details.');
	break;

case 'USE_RIN':
	$title=i18n::translate('Use RIN number instead of GEDCOM ID');
	$text=i18n::translate('Set to <b>Yes</b> to use the RIN number instead of the GEDCOM ID when asked for Individual IDs in configuration files, user settings, and charts.  This is useful for genealogy programs that do not consistently export GEDCOMs with the same ID assigned to each individual but always use the same RIN.');
	break;

case 'USE_SILHOUETTE':
	$title=i18n::translate('Use silhouettes');
	$text=i18n::translate('Use silhouette images when no highlighted image for that person has been specified.  The images used are specific to the gender of the person in question.<br /><br /><table><tr><td wrap valign="middle">This image might be used when the gender of the person is unknown:')." </td><td><img src=\"$WT_IMAGE_DIR/".$WT_IMAGES["default_image_U"]["other"]."\" width=\"40\" alt=\"Silhouette image\" title=\"Silhouette image\" /></td></tr></table>";
	break;
	
case 'USE_THUMBS_MAIN':
	$title=i18n::translate('Use thumbnail');
	$text=i18n::translate('This option determines whether <b>webtrees</b> should send the large or the small image to the browser whenever a chart or the Personal Details page requires a thumbnail.<br /><br />The <b>No</b> choice will cause <b>webtrees</b> to send the large image, while the <b>Yes</b> choice will cause the small image to be sent.  Each individual image also has the &laquo;Always use main image?&raquo; option which, when set to <b>Yes</b>, will cause the large image to be sent regardless of the setting of the &laquo;Use thumbnail&raquo; option in the GEDCOM configuration.  You cannot force <b>webtrees</b> to send small images when the GEDCOM configuration specifies that large images should always be used.<br /><br /><b>webtrees</b> does not re-size the image being sent; the browser does this according to the page specifications it has also received.  This can have undesirable consequences when the image being sent is not truly a thumbnail where <b>webtrees</b> is expecting to send a small image.  This is not an error:  There are occasions where it may be desirable to display a large image in places where one would normally expect to see a thumbnail-sized picture.<br /><br />You should avoid setting the &laquo;Use thumbnail&raquo; option to <b>No</b>.  This choice will cause excessive amounts of image-related data to be sent to the browser, only to have the browser discard the excess.  Page loads, particularly of charts with many images, can be seriously slowed.');
	break;

case 'WATERMARK_THUMB':
	$title=i18n::translate('Add watermarks to thumbnails?');
	$text=i18n::translate('If the Media Firewall is enabled, should thumbnails be watermarked? Your media lists will load faster if you don\'t watermark the thumbnails.');
	break;

case 'WEBMASTER_EMAIL':
	$title=i18n::translate('Support contact');
	$text=i18n::translate('The person to be contacted about technical questions or errors encountered on your site.');
	break;

case 'WELCOME_TEXT_AUTH_MODE_CUST_HEAD':
	$title=i18n::translate('Standard header for custom welcome text');
	$text=i18n::translate('Choose to display a standard header for your custom Welcome text.  When your users change language, this header will appear in the new language.<br /><br />If set to <b>Yes</b>, the header will look look this:<div class="list_value_wrap"><center><b>Welcome to this Genealogy website</b></center><br />Access is permitted to users who have an account and a password for this website.<br /></div>');
	break;

case 'WELCOME_TEXT_AUTH_MODE_CUST':
	$title=i18n::translate('Custom welcome text');
	$text=i18n::translate('If you have opted for custom Welcome text, you can type that text here.  The text will NOT be translated into the language of the visitor, but will be shown exactly as you typed it.  However, if your custom text contains references to language variables that you can define in the various <i>languages/extra.xx.php</i> files, your site can show translated text.<br /><br />You can insert HTML tags into your custom Welcome text.<br /><br />The following description, taken from the Help text for the FAQ list, is equally applicable to the custom Welcome text.<br /><br />HTML entities are a very easy way to add special characters to your FAQ titles and text.  You can use symbolic names, decimal numbers, or hexadecimal numbers.  A complete list of HTML entities, their coding, and their representation by your browser can be found here:  <a href="http://htmlhelp.com/reference/html40/entities/" target="_blank">HTML entity lists</a><br /><br />On occasion, you may need to show a Tilde character&nbsp;&nbsp;<b>&#x7E;</b>&nbsp;&nbsp;or a Number Sign&nbsp;&nbsp;<b>&#x23;</b>&nbsp;&nbsp;in your URLs or text.  These characters have a special meaning to the <b>webtrees</b> Help system and can only be entered in their hexadecimal or decimal form.  Similarly, the&nbsp;&nbsp;<b>&lt;</b>&nbsp;&nbsp;and&nbsp;&nbsp;<b>&gt;</b>&nbsp;&nbsp;characters that usually enclose HTML tags must be entered in their hexadecimal or decimal forms if they are to be treated as normal text instead of signalling an HTML tag.<ul><li><b>&amp;&#x23;35;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x23;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x23;</b></li><li><b>&amp;&#x23;60;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x3C;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x3C;</b></li><li><b>&amp;&#x23;62;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x3E;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x3E;</b></li><li><b>&amp;&#x23;126;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x7E;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x7E;</b></li></ul>There is a&nbsp;&nbsp;<b>&amp;tilde;</b>&nbsp;&nbsp;HTML entity, but this symbol is not interpreted as a Tilde when coded in URLs.<br /><br />You can insert references to entries in the language files or to values of global variables.  Examples: <ul><li><b>&#x23;pgv_lang[add_to_cart]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the language variable "Add to Clippings Cart", and if it were to appear in this field, would show as <b>Add to Clippings Cart</b> when the FAQ list is viewed in the current language.</li><li><b>&#x23;factarray[AFN]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the Fact name $factarray["AFN"], and if it were to appear in this field, would show as <b>Ancestral File Number (AFN)</b> when the FAQ list is viewed in the current language. </li><li><b>&#x23;WT_VERSION&#x23;&nbsp;&#x23;WT_VERSION_RELEASE&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the constant WT_VERSION, a space, and a reference to the constant WT_VERSION_RELEASE, and if they were to appear in this field, would show as <b>#WT_VERSION#&nbsp;#WT_VERSION_RELEASE#</b> when the FAQ list is viewed in the current language.</li><li><b>&#x23;GLOBALS[GEDCOM]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the global variable $GEDCOM, which is the name of the current GEDCOM file.  If it were to appear in this field, it would show as <b>#GLOBALS[GEDCOM]#</b>.</li><li><b>&#x23;GLOBALS[GEDCOM_TITLE]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the global variable $GEDCOM_TITLE, which is the title of the current GEDCOM file.  If it were to appear in this field, it would show as <b>#GLOBALS[GEDCOM_TITLE]#</b>.</li></ul><br />This feature is useful when you wish to create FAQ lists that are different for each language your site supports.  You should put your customized FAQ list titles and entries into the <i>languages/extra.xx.php</i> files (<i>xx</i> is the code for each language), using the following format:<br />$pgv_lang["faq_title1"] = "This is a sample FAQ title";<br />$pgv_lang["faq_body1"] = "This is a sample FAQ body.";');
	break;

case 'WELCOME_TEXT_AUTH_MODE':
	$title=i18n::translate('Welcome text on login page');
	$text=i18n::translate('Here you can choose text to appear on the login screen. You must determine which predefined text is most appropriate.<br /><br />You can also choose to enter your own custom Welcome text, but the text you enter will not be translated when your users change language.  However, if your custom text contains references to language variables that you can define in the various <i>languages/extra.xx.php</i> files, your site can show translated text.  Please refer to the Help text associated with the <b>Custom Welcome text</b> field for more information.<br /><br />The predefined texts are:<ul><li><b>Predefined text that states all users can request a user account:</b><div class="list_value_wrap"><center><b>Welcome to this Genealogy website</b></center><br />Access to this site is permitted to every visitor who has a user account.<br /><br />If you have a user account, you can login on this page.  If you don\'t have a user account, you can apply for one by clicking on the appropriate link below.<br /><br />After verifying your application, the site administrator will activate your account.  You will receive an email when your application has been approved.</div><br/></li><li><b>Predefined text that states admin will decide on each request for a user account:</b><div class="list_value_wrap"><center><b>Welcome to this Genealogy website</b></center><br />Access to this site is permitted to <u>authorized</u> users only.<br /><br />If you have a user account you can login on this page.  If you don\'t have a user account, you can apply for one by clicking on the appropriate link below.<br /><br />After verifying your information, the administrator will either approve or decline your account application.  You will receive an email message when your application has been approved.</div><br/></li><li><b>Predefined text that states only family members can request a user account:</b><div class="list_value_wrap"><center><b>Welcome to this Genealogy website</b></center><br />Access to this site is permitted to <u>family members only</u>.<br /><br />If you have a user account you can login on this page.  If you don\'t have a user account, you can apply for one by clicking on the appropriate link below.<br /><br />After verifying the information you provide, the administrator will either approve or decline your request for an account.  You will receive an email when your request is approved.</div></li></ul>');
	break;

case 'WORD_WRAPPED_NOTES':
	$title=i18n::translate('Add spaces where notes were wrapped');
	$text=i18n::translate('Some genealogy programs wrap notes at word boundaries while others wrap notes anywhere.  This can cause <b>webtrees</b> to run words together.  Setting this to <b>Yes</b> will add a space between words where they are wrapped in the original GEDCOM.');
	break;

case 'ZOOM_BOXES':
	$title=i18n::translate('Zoom boxes on charts');
	$text=i18n::translate('Allows a user to zoom boxes on charts to get more information.<br /><br />Set to <b>Disabled</b> to disable this feature.  Set to <b>On Mouse Over</b> to zoom boxes when the user mouses over the icon in the box.  Set to <b>On Mouse Click</b> to zoom boxes when the user clicks on the icon in the box.');
	break;

	//////////////////////////////////////////////////////////////////////////////
	// This section should contain an entry for every page.
	//////////////////////////////////////////////////////////////////////////////

case 'addmedia.php':
	// no help link
	$title=i18n::translate('Add a new media item');
	$text='';
	break;

case 'addremotelink.php':
	// not called from pop up function. see link_remote
	$title=i18n::translate('Add a remote link');
	$text=i18n::translate('Use this form to link people to other people either from another site or another genealogical database accessible to your copy of webtrees.<br /><br />To add such a link, you must first select the relationship type, then choose a site already known to webtrees or define a new site, and then enter that site\'s ID of the person you want to link to.  <b>webtrees</b> will then automatically download information from the remote site as necessary.  The downloaded information does <u>not</u> become part of your genealogical database; it remains on the original site but is incorporated into the various pages where this remotely linked person is displayed.<br /><br />Refer to the Help link next to each element on the page for more information about that element.  You can also check the online English tutorial for more information: <a href=\"#WT_WEBTREES_WIKI#/en/index.php?title=How_To:Remote_Link_Individuals_Across_Websites_And_Databases\" target=\"_blank\">#WT_WEBTREES_WIKI#</a>.');
	break;

case 'addsearchlink.php':
	$title=i18n::translate('Add a local link');
	$text='';
	break;

case 'admin.php':
	$title=i18n::translate('Administration');
	$text=i18n::translate('On this page you will find links to the configuration pages, administration pages, documentation, and log files.<br /><br /><b>Current Server Time:</b>, just below the page title, shows the time of the server on which your site is hosted. This means that if the server is located in New York while you\'re in France, the time shown will be six hours less than your local time, unless, of course, the server is running on Greenwich Mean Time (GMT).  The time shown is the server time when you opened or refreshed this page.<br /><br /><b>WARNING</b><br />When you see a red warning message under the system time, it means that your <i>config.php</i> is still writeable.  After configuring your site, you should, for <b>security</b>, set the permissions of this file back to read-only.  You have to do this <u>manually</u>, since <b>webtrees</b> cannot do this for you.<br /><br /><br /><br />See <a href="readme.txt" target="_blank"><b>Readme.txt</b></a> for more information.');
	break;

case 'ancestry.php':
	$title=i18n::translate('Ancestry chart');
	$text=i18n::translate('The Ancestry page is very similar to the <a href="?help=pedigree.php">Pedigree Tree</a>, but with more details and alternate <a href="?help=chart_style">Chart style</a> displays.<br /><br />Each ancestry is shown with a unique number, calculated according to the <i>Sosa-Stradonitz</i> system:<div style="padding-left:30px;"><b>Even</b> numbers for men (child*2)<br /><b>Odd</b> numbers for women (husband+1) except for <b>1</b></div><br />Example:<br /><div style="padding-left:30px;">The root person is <b>1</b>, regardless of gender.<br /><b>1</b>\'s father is <b>2</b> (<b>1</b> * 2), mother is <b>3</b> (<b>2</b> + 1).<br /><b>2</b>\'s father is <b>4</b> (<b>2</b> * 2), mother is <b>5</b> (<b>4</b> + 1).<br /><b>3</b>\'s father is <b>6</b> (<b>3</b> * 2), mother is <b>7</b> (<b>6</b> + 1).<br /><b>7</b>\'s father is <b>14</b> (<b>7</b> * 2), mother is <b>15</b> (<b>14</b> +1).<br />etc.');
	break;

case 'branches.php':
	// no help text
	$title=i18n::translate('Branches');
	$text='';
	break;

case 'calendar.php':
	// menu
	$title=i18n::translate('Anniversary calendar');
	$text=i18n::translate('The anniversary calendar shows the persons and families who are linked to an event at a certain day or month or during a certain period of time. It has an advanced filtering system to select the right date, period, and events for you.<ul><li><a href="?help=annivers_date_select"><b>Day:</b></a></li><li><a href="?help=annivers_month_select"><b>Month:</b></a></li><li><a href="?help=annivers_year_select"><b>Year:</b></a></li><li><a href="?help=annivers_show"><b>Show / Show events of:</b></a></li><li><a href="?help=annivers_sex"><b>Gender</b></a></li><li><a href="?help=annivers_event"><b>Event</b></a></li><li><a href="?help=day_month"><b>View day / View month / View year</b></a></li><li><a href="?help=annivers_tip"><b>Tip</b></a></li></ul>');
	break;

case 'clippings.php':
	$title=i18n::translate('Clippings cart');
	$text=i18n::translate('The Clippings Cart allows you to take extracts ("clippings") from this family tree and bundle them up into a single file for downloading and subsequent importing into your own genealogy program.  The downloadable file is recorded in GEDCOM format.<br /><ul><li>How to take clippings?<br />This is really simple. Whenever you see a clickable name (individual, family, or source) you can go to the Details page of that name. There you will see the <b>Add to Clippings Cart</b> option.  When you click that link you will be offered several options to download.</li><li>How to download?<br />Once you have items in your cart, you can download them just by clicking the <b>Download Now</b> link.  Follow the instructions and links.</li></ul>');
	break;

case 'compact.php':
	// no help text
	$title=i18n::translate('Compact chart');
	$text='';
	break;

case 'descendancy.php':
	$title=i18n::translate('Descendancy chart');
	$text=i18n::translate('This page will show the descendants of a person.<br /><br />You can choose a starting (root) person for this Descendancy chart or you can be linked to this page by clicking the <b>Descendancy Chart</b> link on another page.  Click on Arrow icons to navigate this tree in the direction of the arrow.  Click on the Chart icon in any Person box to change the root of the tree to that person.');
	break;

case 'edit_changes.php':
	// no help link
	$title=i18n::translate('Review GEDCOM changes');
	$text='';
	break;

case 'edit_interface.php':
	// no help link
	$title=i18n::translate('Edit interface');
	$text='';
	break;

case 'edit_merge.php':
	$title=i18n::translate('Merge records');
	$text=i18n::translate('This page will allow you to merge two GEDCOM records from the same GEDCOM file.<br /><br />This is useful for people who have merged GEDCOMs and now have many people, families, and sources that are the same.<br /><br />The page consists of three steps.<br /><ol><li>You enter two GEDCOM IDs.  The IDs <u>must</u> be of the same type.  You cannot merge an individual and a family or family and source, for example.<br />In the <b>Merge To ID:</b> field enter the ID of the record you want to be the new record after the merge is complete.<br />In the <b>Merge From ID:</b> field enter the ID of the record whose information will be merged into the Merge To ID: record.  This record will be deleted after the Merge.</li><li>You select what facts you want to keep from the two records when they are merged.  Just click the checkboxes next to the ones you want to keep.</li><li>You inspect the results of the merge, just like with all other changes made online.</li></ol>Someone with Accept rights will have to authorize your changes to make them permanent.');
	break;

case 'edit_privacy.php':
	$title=i18n::translate('Edit GEDCOM privacy settings');
	$text=i18n::translate('On this page you can make all the Privacy settings for the selected GEDCOM.<br /><br />You can check under the page title to see that you are editing the correct privacy file.  It is displayed like this: (path/nameofyourgedcom_priv.php)<br /><br />If you need more settings, you can make changes to the privacy file manually. You can read more about this on the <b>webtrees</b> web site.');
	break;
	
case 'editconfig_gedcom.php':
	$title=i18n::translate('GEDCOM configuration');
	$text=i18n::translate('Every genealogical database used with <b>webtrees</b> has its own <b>Configuration file</b>.<br /><br />On this form you configure many options such as database title, language, calendar format, email options, logging of database searches, HTML META headers, removal of surnames from the database\'s Frequent Surnames list, etc.<br /><br /><br /><b>More help</b><br />More help is available by clicking the <b>?</b> next to items on the page.<br /><br /><br />See <a href="readme.txt" target="_blank"><b>Readme.txt</b></a> for more information.');
	break;

case 'editgedcoms.php':
	$title=i18n::translate('GEDCOM administration');
	$text=i18n::translate('The GEDCOM Administration page is the control center for administering all of your genealogical databases.<br /><br /><b>Current GEDCOMs</b><br />At the head of the <b>Current GEDCOMs</b> table, you see an action bar with four links.<ul><li>Add GEDCOM</li><li>Upload GEDCOM</li><li>Create a new GEDCOM</li><li>Return to the Admin menu</li></ul>In the <b>Current GEDCOMs</b> table each genealogical database is listed separately, and you have the following options for each of them:<ul><li>Import</li><li>Delete</li><li>Download</li><li>Edit configuration</li><li>Edit privacy</li><li>SearchLog files</li></ul>Edit privacy appears here because every GEDCOM has its own privacy file.<br /><br />Each line in this table should be self-explanatory.  <b>webtrees</b> can be configured to log all database searches.  The SearchLog files can be inspected through links found on this page.');
	break;

case 'editnews.php':
	// no help link
	$title=i18n::translate('Add/edit journal/news entry');
	$text='';
	break;

case 'edituser.php':
	$title=i18n::translate('My account');
	$text=i18n::translate('Here you can change your settings and preferences.<br /><br />You can change your user name, full name, password, language, email address, theme of the site, and preferred contact method.<br /><br />You cannot change the GEDCOM INDI record ID; that has to be done by an administrator.');
	break;

case 'export_gedcom.php':
	// no help link
	$title=i18n::translate('Export');
	$text='';
	break;

case 'family.php':
	$title=i18n::translate('Family details page');
	$text=i18n::translate('This page will show you an overview of the family that you chose on a previous page.<br /><br />From top to bottom you will see the Personal Details boxes of the husband and his parents, the wife and her parents, and the children.<br /><br />The layout and contents of the Person Boxes are the same as the boxes that you already know from the Pedigree and Descendancy pages.<br /><br />To the right of the Parent boxes you may see an arrow if more ancestors exist in the file. When you click that arrow, you will move up a generation to show you a new family page with the previous parents now listed as the husband and wife.<br /><br />At the right side within the name box you may see a Zoom (magnifying glass) icon which you can click to reveal more details about the individual. When you click the name you will be taken to the Individual Information page of that person.<br /><br />Also at the right side you will find a small menu to take you to pages with charts or more information.  Some of these menu items also have sub-menus which will appear when your mouse pointer approaches the parent menu item.<br /><br />The Family Group Information box shows all known facts and information about this family, such as marriage, multimedia objects, and notes. In the Fact Information box, clicking a place will take you to the Place list, where all other families and individuals who are connected to that place are shown. Clicking a date will jump to the Day calendar, which will show all events that happened on that day and month in history. Multi-media objects can be clicked; this will open a new window in which the object is viewed. When you click on the picture caption, you will see the picture on the MultiMedia page. If you click on a Source link, the details of that source will be displayed on the Source page.<br /><br />Below the name boxes of the children you find the <b>Add a child to this Family</b> link.  Next to the name boxes of the children you see the <b>Family Group Information</b> link.  If you have enough rights, you can edit, delete, and add data and facts here.<br /><br />As with the Individual Information page, you will see a menu at the top right of the page.  Entries in this menu take you to other pages where you can get information about this family or perform other tasks related to this family.<br />');
	break;

case 'familybook.php':
	$title=i18n::translate('Family book chart');
	$text=i18n::translate('This chart is very similar to the Hourglass chart.  It will show the ancestors and descendants of the selected root person on the same chart.  It will also show the descendants of the root person in the same Hourglass format.<br /><br />The root person is centered in the middle of the page with his descendants listed to the left and his ancestors listed to the right.  In this view, each generation is lined up across the page starting with the earliest generation and ending with the latest.<br /><br />Each descendant of the root person will become the root person of an additional hourglass chart, printed on the same page.  This process repeats until the specified number of descendant generations have been printed.');
	break;

case 'famlist.php':
	$title=i18n::translate('Familiy list page');
	$text=i18n::translate('On this page you can display a list of families.  The names will be displayed with surnames first and sorted into alphabetical order.<br /><br />The output of the Name list depends on:<ol><li>The letter you clicked in the Alphabetical index.</li><li>Whether you clicked "Skip" or "Show" Surname List.</li></ol>You can search on the husband\'s or the wife\'s surname;  both are included in the list.<br /><br />More help is available by clicking the <b>?</b> next to items on the page.');
	break;

case 'fanchart.php':
	$title=i18n::translate('Circle diagram page');
	$text=i18n::translate('The Circle Diagram is very similar to the <a href="?help=pedigree.php">Pedigree Tree</a>, but in a more graphical way.<br /><br />The Root person is shown in the center, his parents on the first ring, grandparents on the second ring, and so on.<br /><br />Years of birth and death are printed under the name when known.<br /><br />Clicking on a name on the chart will open a links menu specific to that person.  From this menu you can choose to center the diagram on that person or on one of that person\'s close relatives, or you can jump to that person\'s Individual Information page or a different chart for that person.');
	break;

case 'faq.php':
	$title=i18n::translate('Frequently Asked Questions');
	$text=i18n::translate('The FAQ (Frequently Asked Questions) page can contain an overview or a list of questions and answers on the use of this genealogy site.<br /><br />The use to which the FAQ page is put is entirely up to the site administrator. The site administrator controls the content of each item and also the order in which the items are shown on the page.');
	break;
	
case 'find.php':
	// no help link
	$title=i18n::translate('Find individual ID');
	$text='';
	break;

case 'gedcheck.php':
	// no help text
	$title=i18n::translate('GEDCOM checker');
	$text='';
	break;

case 'gedrecord.php':
	// no help link
	$title=i18n::translate('GEDCOM record');
	$text='';
	break;

case 'help_text.php':
	// no help link
	$title=i18n::translate('Information');
	$text='';
	break;

case 'hourglass.php':
	$title=i18n::translate('Hourglass chart');
	$text=i18n::translate('The Hourglass chart will show the ancestors and descendants of the selected root person on the same chart.  This chart is a mix between the Descendancy chart and the Pedigree chart.<br /><br />The root person is centered in the middle of the page with his descendants listed to the left and his ancestors listed to the right.  In this view, each generation is lined up across the page starting with the earliest generation and ending with the latest.<br /><br />If there is a downwards arrow on the screen under the root person, clicking on it will display a list of the root person\'s close family members that you can use the navigate down the chart.  Selecting a name from this list will reload the chart with the selected person as the new root person.');
	break;

case 'imageview.php':
	// no help link
	$title=i18n::translate('Image viewer');
	$text='';
	break;

case 'index.php':
	$title=i18n::translate('Home page description');
	$text=i18n::translate('This page welcomes you to the selected <a href=?help=def_gedcom">GEDCOM</a> file. You can return to this page by selecting \'Home page\' from the top menu. If there are multiple GEDCOMs on this site, you can select a GEDCOM from the drop-down menu.<br /><br />This help page contains information about:<ul><li><a href="?help=index_portal"><b>Home page</b></a></li><li><a href="?help=header"><b>Header area</b></a></li><li><a href="?help=menu"><b>Menus</b></a></li><li><a href="?help=def"><b>Definitions</b></a></li></ul>');
	break;

case 'indilist.php':
	$title=i18n::translate('Individuals list page');
	$text=i18n::translate('On this page you can display a list of individuals.  The names will be displayed with surnames first and sorted into alphabetical order.<br /><br />The output of the Name list depends on:<ol><li>The letter you clicked in the Alphabetical index.</li><li>Whether you clicked "Skip" or "Show" Surname List.</li></ol>More help is available by clicking the <b>?</b> next to items on the page.');
	break;

case 'individual.php':
	$title=i18n::translate('Individual information');
	$text=i18n::translate('All details of a person are displayed on this page.<br /><br />If there is a picture available, you will see it at the top left side.  You will see the names of the person next to the picture.<br /><br />Names can have notes and sources attached to them. If any of the names have notes or sources, you will see them listed under the names they relate to.<br /><br />A person might have an AKA (maybe he\'s known under another name). If that is the case, it will be displayed.<br /><br />If you have Edit rights to this person, you will also see <b>Edit</b> and <b>Delete</b> links next to the items that you can edit.<br /><br />On this page you see tab sheets for <b>Personal Facts and Details</b>, <b>Notes</b>, <b>Sources</b>, <b>Media</b>, and <b>Close Relatives</b>.  These tab sheets show you all the information about this individual that is stored in the database.<br /><ul><li>The <b>Personal Facts and Details</b> tab will show you the facts and details about this person and any fact from their marriages. Clicking on any date on this tab will take you to the Anniversary Calendar for that date, so that you can see other events that happened on the same day. Clicking on a place will take you to the Place Hierarchy where you can view other people who had events in the same place. For marriage and other family related facts, the name of the person\'s spouse is available so that you can view the spouse and a link to the family record is also provided.</li><li>The <b>Notes</b> tab will show you any general notes relating to this person.</li><li>The <b>Sources</b> tab will show you all of the <u>general</u> sources for this person. These sources are <u>not</u> linked to individual facts, not even the person\'s name; they are associated with the individual himself.  Clicking on the title of a source will take you to a more detailed Source Information page that will display other people who are also linked to the same source.</li><li>The <b>Media</b> tab will list all of the pictures and other media items that are attached to this individual. Clicking on a thumbnail of the picture will open up a larger view of the image. Clicking on the picture caption will show you the picture on the MultiMedia page.</li><li>The <b>Close Relatives</b> tab lists this person\'s parents and siblings as well as all of the spouses and children that this person has had. These persons will be listed in boxes similar to the charts that you may have already seen.</li></ul>On the right of the screen you will find a box with links.  Many of the links in the box are the same as the links in the menus. For example, clicking on the <b>Pedigree Chart</b> link on the side links will take you to the Pedigree chart for this person. This is different from the menu links, because clicking on the <b>Pedigree Chart</b> link in the menu will take you back to the default Pedigree chart for this database.<br /><br />One of the links that might appear in this list if it has been enabled by the admin, is the <b>View GEDCOM Record</b> link. This link will show you the raw GEDCOM record of this individual.<br /><br />If the Clippings Cart has been enabled by the site admin, you will also have a link that will allow you to add this person to your Clippings Cart.<br /><br />The <b>Relationship to me</b> link will only appear if you are logged in and have been assigned an ID in the GEDCOM. This link will take you to the Pedigree chart and show you how you are related to this person.<br /><br />More help is available by clicking the <b>?</b> next to items on the page.');
	break;

case 'install.php':
	// no help link
	$title=i18n::translate('Installation wizard');
	$text='';
	break;

case 'inverselink.php':
	$title=i18n::translate('Link media');
	$text=i18n::translate('Each media item should be associated with one or more person, family, or source records in your database.<br /><br />To establish such a link, you can enter or search for the ID of the person, family, or source at the same time as you create the media item.  You can also establish the link later through editing options on the Manage MultiMedia page, or by adding media items through the Add Media link available on the Individual, Family, or Source Details pages.');
	break;

case 'lifespan.php':
	$title=i18n::translate('Lifespan chart');
	$text=i18n::translate('On this chart you can display one or more persons along a horizontal timeline.  This chart allows you to see how the lives of different people overlapped.<br /><br />You can add people to the chart individually or by family groups by their IDs.  The previous list will be remembered as you add more people to the chart.  You can clear the chart at any time with the <b>Clear Chart</b> button.<br /><br />You can also add people to the chart by searching for them by date range or locality.');
	break;

case 'login.php':
	$title=i18n::translate('The login page');
	$text=i18n::translate('On this page you can login, request a new password, or request a new user account.<br /><br />In order to access \'My page\', you must be a registered user on the system.  On \'My page\' you can bookmark your favorite people, keep a user journal, manage messages, see other logged in users, and customize various aspects of <b>webtrees</b> pages.<br /><br />Enter your username and password in the appropriate fields to login to \'My page\'.');
	break;
	
case 'login_register.php':
	$title=i18n::translate('Request new user account');
	$text=i18n::translate('The amount of data that can be publicly viewed on this website may be limited due to applicable law concerning privacy protection. Many people do not want their personal data publicly available on the Internet. Personal data could be misused for spam or identity theft.<br /><br />Access to this site is permitted to every visitor who has a user account. After the administrator has verified and approved your account application, you will be able to login.<br /><br />If Relationship Privacy has been activated you will only be able to access your own close relatives\' private information after logging in. The administrator can also allow database editing for certain users, so that they can change or add information.<br /><br />If you need any further support, please use the link below to contact the administrator.');
	break;

case 'manageservers.php':
	$title=i18n::translate('Manage sites');
	$text=i18n::translate('On this page you can add remote sites and ban IP addresses.<br /><br />Remote sites can be added by providing the site title, URL, database id(optional), username, and password for the remote web service.<br /><br />IP address banning is accomplished by supplying any valid IP address range. For example, 212.10.*.*  Remote sites within the IP address ranges in the Banned list will not be able to access your web service.  You can ban specific IP addresses too.');
	break;

case 'media.php':
	// no help text
	$title=i18n::translate('Manage multimedia');
	$text='';
	break;

case 'medialist.php':
	$title=i18n::translate('Multimedia object list');
	$text=i18n::translate('This page lists all of the Multimedia Objects (MMO) that can be found in this database.<br /><br />For each of the media items you see the title or filename of the item, names of the individuals or families connected to the item, and notes about the item.<br /><br />Clicking the title or filename of the item has the same effect as clicking its thumbnail.  The item will be opened in the image viewer built into <b>webtrees</b> or in the viewer specified in your browser\'s configuration.<br /><br />When you click on the "View" link next to the person or family, you will be taken to the relevant Details page.');
	break;
	
case 'message.php':
	// no help text
	$title=i18n::translate('<b>webtrees</b> message');
	$text='';
	break;

case 'module_admin.php':
	// no help text
	$title=i18n::translate('Module administration');
	$text='';
	break;

case 'notelist.php':
	// no help text
	$title=i18n::translate('Shared notes');
	$text='';
	break;

case 'pedigree.php':
	$title=i18n::translate('The pedigree page');
	$text=i18n::translate('A pedigree is an enumeration of all ancestors of the starting person.  Users who are not logged in see the pedigree of the starting (root) person chosen by the site administrator.  Logged in users can select their own starting (root) person.<br /><br />In this context, "All ancestors" means the father and mother, their parents, and so on.  The pedigree is displayed graphically; you don\'t have to struggle through pages of text to determine your ancestors.<br /><br />All individuals are displayed in Name boxes on the screen.<br /><ul><li><b>Name boxes on the pedigree</b><br />If the Pedigree page is set to show details, you will see the person\'s name and birth and death dates.  You can click on a person\'s name to take you directly to the Individual Information page of that person.<br /><br />When <b>Show details</b> is on there are two icons inside the name box.</li><li><b>Pedigree icon inside the Name box</b><br />When the option <b>Show Details</b> is on, you see a Pedigree icon in the Name box. Depending on the site settings, you have to hover over the icon or click on it.  When you click on or hover over this icon, a small sub-menu appears.<br /><br />The items <b>Pedigree Tree</b> and <b>Descendancy Chart</b> are similar to those items in the main menu, but the difference is that the starting person is now the individual of mentioned in the Name box.  You also see <b>Family with Spouse</b>. Underneath that you see the name of the spouse followed by the names of the children.  All names are clickable.</li><li><b>Magnifying glass inside the Name box</b><br />Depending on the site settings, you have to hover over the icon or click on it.  This magnifies the Name box so that more details will be displayed.  You will see more dates and events. Names are clickable.</li><li><b>Arrows</b><br />On the left or right of the leftmost or rightmost Name boxes you may see arrows.  When you click on these arrows the screen display will shift in the direction of the arrow.</li></ul>');
	break;

case 'placelist.php':
	$title=i18n::translate('Place hierarchy');
	$text=i18n::translate('(or Persons Per Place)<br /><br />This page will show you a hierarchy of the places in the GEDCOM and which individuals or families are connected to a location.<br /><br />If there is any connection between an individual or family and an event at a certain location, <b>webtrees</b> will find it.<br /><br />The results are displayed in a two-column list, one column for individuals and one for families.');
	break;

case 'printlog.php':
	// no help text
	$title=i18n::translate('Print logfile');
	$text='';
	break;

case 'relationship.php':
	$title=i18n::translate('Relationship chart');
	$text=i18n::translate('On this page you can display the relationship between any two people.  These people do not have to be directly related by blood line;  any relation will be found.');
	break;
	
case 'repo.php':
	$title=i18n::translate('Repository information');
	$text=i18n::translate('The details of the Repository are displayed here. Together with Sources, Repositories are very important to genealogical researchers.  With accurate Source and Repository information, you can follow the trail another researcher used to find the information.  You should be able to find that same information again.<br /><br />On this page you may see information about the Repository\'s title, address, email and webpage.<br /><br />After the repository details, will be a list of all sources that are linked to this repository. This allows you to see all of the information that was obtained from a particular repository.<br /><br />If enabled by the site admin, you will have one or two more menu icons on this page:<br /><b>View GEDCOM Record</b>, which shows the information in GEDCOM format.<br /><b>Add to Clippings Cart</b>, which enables you to store this information in your Clippings Cart. From there you can download the information in GEDCOM file format and import it into your own genealogy program.<br /><br />More help is available by clicking the <b>?</b> next to items on the page.');
	break;

case 'repolist.php':
	$title=i18n::translate('Repositories');
	$text=i18n::translate('A list of repositories is displayed on this page.<br /><br />The names of the repositories are sorted into alphabetical order.<br /><br /><b>REPOSITORIES</b><br />Without repositories we cannot build our database. There is a source for all information stored in the database, and that source is kept in a repository. Repositories can be the personal archive of a person, an institution, a public database, a government or church records office, an Internet resource, etc. To get access to a source we will want to know where and in what place it is. All necessary information to find a source should be stored in the Repository record.<br /><br />A repository can be linked to many sources.');
	break;

case 'reportengine.php':
	$title=i18n::translate('Reports');
	$text=i18n::translate('The items in the reports menu will generate PDF files for printing.<br /><br />The first step is to choose a report to run.  After you have selected a report to run, you will be asked to provide some information specific to that report, such as which individual or family to start with and whether or not to show photos.  When you are ready to run the report, click the <b>Download report</b> button to download the report to your computer.<br /><br /><br />~Reporting Engine~<br />The <b>webtrees</b> Reporting Engine uses XML template files to automatically generate PDF reports.<br /><br />The reports available in the <b>Select report</b> list are generated from the report XML files found in the "reports" directory.  You can create your own reports by making a copy of any of the templates provided and modifying the template XML.  To add your custom report, just put it in the \"reports\" directory and <b>webtrees</b> will automatically detect it and make it available in the <b>Select report</b> drop-down list.<br /><br /><br />~PDF FILE FORMAT~<br />The <b>webtrees</b> Reporting Engine produces downloadable reports in Adobe&reg; PDF format.  The GEDCOM 5.5.1 Standard specification, mentioned elsewhere in this Help file, is also downloadable as a PDF file.  PDF is an acronym for <b>P</b>ortable <b>D</b>ocument <b>F</b>ormat.<br /><br />PDF files are not viewable or printable by the standard software on your PC.  If you already have Acrobat Reader installed (it\'s often packaged with other softwares), you do not need to replace or upgrade it to deal with report files produced by <b>webtrees</b>.<br /><br />Acrobat Reader, the viewing and printing program for these files, is available free of charge from Adobe Systems Inc.  The free Adobe&reg; Acrobat Reader can be downloaded from the <a href=\"http://www.adobe.com/products/acrobat/readstep2.html\" target=\"_blank\"><b>Adobe Systems Inc.</b></a> web site.  You may find copies of "Acrobat Reader" available for download from other Internet sites, but we strongly advise you to trust <u>only</u> the Adobe Systems Inc. site.<br /><br />Acrobat Reader is available for many different systems, including Microsoft&reg; Windows and Apple&reg; Macintosh, in many languages other than English.  If you have a Windows 95 system, be sure to download Acrobat Reader version 5.0.5.  Versions more recent than this will not install correctly on Windows 95 systems.<br /><br /><a href=\"http://www.adobe.com/products/acrobat/readstep2.html\" target=\"_blank\"><b>Download Adobe Reader here</b></a><br /><br /><br />~Ahnentafel Report~<br />This is a report of the selected person and his ancestors, printed in booklet format.  It starts with the first person and then continues with his or her parents, grand-parents, etc.<br /><br />Note that the ahnentafel report is only available in English at this time.<br /><br /><br />~Birth Date and Place Report~<br />With this report you can list all of the people who were born at a certain time or place.<br /><br /><br />~Relatives Report~<br />This report will list all of the relatives of the selected individual.  You can choose which of the person\'s relatives to show on the report.<ul><li><b>Parents and siblings</b> will show the selected person, his parents, and his brothers and sisters.</li><li><b>Spouse and children</b> will list the person with his or her spouses and their children.</li><li><b>Direct line ancestors</b> will list the person, his parents, grand-parents, great-grand-parents, and continue up the tree listing all of the people who are parents in the person\'s lineage.</li><li><b>Direct line ancestors and their families</b> will list all of the people from the Direct line ancestors list but also include aunts and uncles and great-aunts and great-uncles, so it will include the siblings of all of the ancestors in this person\'s family tree.  It will not list the children of the siblings (cousins).</li><li><b>Descendants</b> will list all of this person\'s descendants (children, grand-children, great-grand children, etc).</li><li><b>ALL</b> this option is a combination of the Descendants and the Direct line ancestors and their families in a single report.</li></ul>');
	break;

case 'rss.php':
	// no help text
	$title=i18n::translate('RSS feed');
	$text='';
	break;

case 'search.php':
	$title=i18n::translate('Search');
	$text=i18n::translate('Although this page looks very simple, there is a very powerful and complicated search engine behind the two forms.  Most genealogy web sites just let you search for a name.  <b>webtrees</b> lets you search for almost anything.<br /><br />The Search box on the left of the screen is the same as the Search box in each page header.<br /><br />If you are looking for people in connection to a certain year, just type the year. The program will find all connections for you.<br /><br />Looking for a name, or place?  Just type in the name or place, completely or just a part of it, and <b>webtrees</b> does the rest.<br /><br /><b>Soundex search method</b><br />With the search boxes on the right, you can search for names of persons and places, even if you don\'t know precisely how to write the name.<br /><br />When there are several genealogical databases on one site and the administrator has enabled switching between them, your search will return the results for all of them.<br /><br />You will find more help about these two boxes by clicking the <b>?</b> above the boxes.');
	break;

case 'search_advanced.php':
	// no help text
	$title=i18n::translate('Advanced search');
	$text='';
	break;

case 'search_engine.php':
	$title=i18n::translate('Search engine spider');
	$text=i18n::translate('<b>webtrees</b> automatically provides search engines with smaller data files with fewer links.  The data is limited to the individual and immediate family, without adding information about grand parents or grand children.  Many reports and server-intensive pages like the calendar are off limits to the spiders.<br /><br />Attempts by the spiders to go to those pages result in showing this page.  If you are seeing this text, the software believes you are a search engine spider.  Below is the list of pages that are allowed to be spidered and will provide the abbreviated data.<br /><br />Real users who follow search engine links into this site will see the full pages and data, and not this page.');
	break;

case 'source.php':
	$title=i18n::translate('Sources details page');
	$text=i18n::translate('The details of the source are displayed on this page. Sources are very important to genealogical researchers and will allow you to follow the trail another researcher used to find the information.<br /><br />You can see information about the source\'s title, author, publication, and the repository where the source was looked up. Because of the many different types of sources, some sources may have more information than others.<br /><br />If a multimedia object such as a scan of a document is connected to the source, you can view that object by clicking the object. When you click on the object name, you will see the object on the MultiMedia page.<br /><br />Following the source details there is a list of all individuals and families who are connected to this source. This allows you to identify all items that were obtained from that data source.<br /><br />When the administrator has enabled these features, you will have one or two additional menu icons on this page:<ol><li><b>View GEDCOM Record</b><br />which shows the information in GEDCOM format.</li><li><b>Add to Clippings Cart</b><br />which enables you to store this information in your Clippings Cart for later downloading and importing into your own genealogy program.</li></ol><br />More help is available by clicking the <b>?</b> next to items on the page.');
	break;

case 'sourcelist.php':
	$title=i18n::translate('Sources list page');
	$text=i18n::translate('A list of sources is displayed on this page.<br /><br />Unlike the Individual Information and Family pages, there is no alphabetical index.<br /><br />A source can be an individual, a public database, an institution, an Internet resource, etc.  Because of the completely random nature of genealogical sources, it is impossible to find a sort order that is meaningful in all cases. However, <b>webtrees</b> <u>does</u> sort the Source names into alphabetical order.<br /><br /><b>SOURCES</b><br />Without sources we cannot build our database. There is a source for every item of information in the database. The source can be a relative, an institution, a public database, government or private records, an Internet resource, etc.<br /><br />A source can be linked to many persons. One person can also be linked to many sources. You can have different sources for every event, whether it is birth date, profession, marriage, children, etc.');
	break;

case 'statistics.php':
	$title=i18n::translate('Statistics page');
	$text=i18n::translate('This page lets you determine the criteria for producing a graphical display of various statistics from your database.');
	break;

case 'statisticsplot.php':
	$title=i18n::translate('Statistics plot');
	$text=i18n::translate('This page lets you determine the criteria for producing a graphical display of various statistics from your database.');
	break;

case 'timeline.php':
	$title=i18n::translate('Timeline chart');
	$text=i18n::translate('On this chart you can display one or more persons along a timeline.  You can, for example, visualize the status of two or more persons at a certain moment.<br /><br />If you click the <b>Time Line</b> link on an other page you will already see one person on the Time Line.  If you clicked the <b>Time Line</b> menu item in a page header, you have to supply the starting person\'s ID.');
	break;

case 'treenav.php':
	$title=i18n::translate('Interactive tree');
	$text=
		i18n::translate('Use the Interactive Tree to view the entire family tree of a person in both directions.  This view is similar to the Hourglass view in that it shows both ancestors and descendants of a given root person.  This chart gives you a more compact view by showing boxes for couples or families instead of just individuals.').
		'<br /><ul><li><b>'.i18n::translate('Scrolling').'</b><br />'.
		i18n::translate('Whenever your mouse cursor changes to a Move icon, you can click and drag the tree to view other portions of the tree.  As you drag the tree future generations will automatically expand until there are no more generations left to view in that direction.').
		'<br /></li><li><b>'.i18n::translate('Zoom').'</b><br />'.
		i18n::translate('You can use the icons on the left of the tree to zoom in and out.  Zooming out will allow you to see more of the tree on the screen at a time.  As you zoom out the text can become difficult to read; when your mouse hovers over a box you will get an enlarged view of what is inside it.').
		'<br /></li><li><b>'.i18n::translate('Expanding Details').'</b><br />'.
		i18n::translate('Clicking on any box will expand the box and display a more detailed view.  While in expanded mode, clicking on a person\'s name will open their Individual Information page.').
		'<br />'.
		i18n::translate('Clicking %s will redraw the tree with that person as the new root.', '<img src="'.$WT_IMAGE_DIR.'/'.$WT_IMAGES['gedcom']['small'].'" width="15px" height="15px" alt="">').
		'<br />'.
		i18n::translate('Clicking %s will take you to that family\'s detail page.', '<img src="'.$WT_IMAGE_DIR.'/'.$WT_IMAGES['family']['button'].'" width="15px" height="15px" alt="">').
		'<br /></li><li><b>'.i18n::translate('Toggle Spouses').'</b><br />'.
		i18n::translate('The %s icon directly under the Zoom buttons will toggle the display of all spouses on or off on the descendancy side.  When the display is set to show spouses, all of a person\'s spouses will appear in the box with them.  All of the person\'s children will be shown as well.  When the option to show spouses is off, only the person\'s last spouse and children with that spouse will be shown.', '<img src="'.$WT_IMAGE_DIR.'/'.$WT_IMAGES['sfamily']['small'].'" width="15px" height="15px" alt="">').
		'<br /></li><li><b>'.i18n::translate('Large Tree').'</b>'.
		'<br />'.
		i18n::translate('The Interactive Tree is available from many different pages including the Tree tab on the Individual Information page and the Charts block on the Home Page.  When viewing the tree from one of these other pages, you will also have a Tree icon under the Zoom icons.').
		'<br />'.
		i18n::translate('Clicking %s will take you to the Interactive Tree page.', '<img src="'.$WT_IMAGE_DIR.'/'.$WT_IMAGES['gedcom']['small'].'" width="15px" height="15px" alt="">').
		'</li></ul>';
	break;

case 'uploadgedcom.php':
	$title=i18n::translate('Upload GEDCOM');
	$text=i18n::translate('Unlike the <b>Add GEDCOM</b> function, the GEDCOM file you wish to add to your database does not have to be on your server.<br /><br />In Step 1 you select a GEDCOM file from your local computer. Type the complete path and file name in the text box or use the <b>Browse</b> button on the page.<br /><br />You can also use this function to upload a ZIP file containing the GEDCOM file. <b>webtrees</b> will recognize the ZIP file and extract the file and the filename automatically.<br /><br />If a GEDCOM file with the same name already exists in <b>webtrees</b>, it will, after your confirmation, be overwritten. However, all GEDCOM settings made previously will be preserved.<br /><br />You will find more help on other pages of the procedure.');
	break;

case 'uploadmedia.php':
	$title=i18n::translate('Upload media files');
	$text=i18n::translate('Uploading media files is quite straightforward.  Here is a little additional information.<br /><br /><b>Thumbnails</b><br />Thumbnails should have a size somewhere around 100px width.  The thumbnail <u>must</u> be named identically to the full-size version.  If your system can generate thumbnails automatically, you will see a notice to that effect on the Upload Media page.<br /><br /><b>Uploading</b><br />Files will be uploaded automatically to the directory <b>#GLOBALS[MEDIA_DIRECTORY]#</b> for the full-sized version and to <b>#GLOBALS[MEDIA_DIRECTORY]#thumbs/</b> for the thumbnails.<br /><br />See <a href=\"readme.txt\" target=\"_blank\"><b>Readme.txt</b></a> for more information.');
	break;

case 'useradmin.php':
	$title=i18n::translate('User administration');
	$text=i18n::translate('On this page you can administer the current users and add new users.<br /><br /><b>User List</b><br />In this table the current users, their status, and their rights are displayed.  You can <b>delete</b> or <b>edit</b> users.<br /><br /><b>Add a new user</b><br />This form is almost the same as the one users see on the  <b>My Account</b> page.<br /><br />For several subjects we did not make special Help text for the administrator. In those cases you will see the following message:');
	break;

	//////////////////////////////////////////////////////////////////////////////
	// This section contains all the other help items.
	//////////////////////////////////////////////////////////////////////////////

case 'add_child':
	$title=i18n::translate('Add a child to this family');
	$text=i18n::translate('You can add a child to this family by clicking this link.<br /><br />Adding a child is simple: Just click the link, fill out the boxes in the pop up screen, and that\'s all.');
	break;

case 'add_custom_facts':
	$title=i18n::translate('Add a custom fact');
	$text=i18n::translate('If you can\'t find the fact that you want to add in the list of GEDCOM facts, you can enter a <b>custom fact</b> as well.<br /><br />Entering a custom fact is just as simple as entering one of the pre-defined ones.  The only difference is that you have to name the fact instead of picking its name from a list. You have to do this in the top field: <b>Type</b>');
	break;

case 'add_facts_general':
	$title=i18n::translate('General information about adding');
	$text=i18n::translate('When you have added a fact, note, source, or multimedia file to a record in the database, the addition still has to be approved by a user who has Accept rights.<br /><br />Until the changes have been Accepted, they are identified as "pending" by a differently colored border.  All users with Edit rights can see these changes as well as the original information.  Users who do not have Edit rights will only see the original information. When the addition has been Accepted, the borders will disappear and the new data will display normally, replacing the old.  At that time, users without Edit rights will see the new data too.');
	break;

case 'add_facts':
	$title=i18n::translate('Add a fact');
	$text=i18n::translate('Here you can add a fact to the record being edited.<br /><br />First choose a fact from the drop-down list, then click the <b>Add</b> button.  All possible facts that you can add to the database are in that drop-down list.');
	break;

case 'add_fam_clip':
	$title=i18n::translate('Add family to clippings cart');
	$text=i18n::translate('You can add all or some of this family\'s information to your Clippings Cart. On the next page you can choose precisely how much information you wish to add:<ol><li>Add just this family record.</li><li>Add parents\' records together with this family record.</li><li>Add parents\' and children\'s records together with this family record.</li><li>Add parents\' and all descendants\' records together with this family record.</li></ol>');
	break;

case 'add_faq_body':
	$title=i18n::translate('FAQ body');
	$text=i18n::translate('The text of the FAQ item is entered here.<br /><br />The text can be formatted. HTML tags such as &lt;b&gt; and &lt;br /&gt; are allowed, as are HTML entities such as &amp;amp; and &amp;nbsp;.<br /><br />HTML entities are a very easy way to add special characters to your FAQ titles and text.  You can use symbolic names, decimal numbers, or hexadecimal numbers.  A complete list of HTML entities, their coding, and their representation by your browser can be found here:  <a href="http://htmlhelp.com/reference/html40/entities/" target="_blank">HTML entity lists</a><br /><br />On occasion, you may need to show a Tilde character&nbsp;&nbsp;<b>&#x7E;</b>&nbsp;&nbsp;or a Number Sign&nbsp;&nbsp;<b>&#x23;</b>&nbsp;&nbsp;in your URLs or text.  These characters have a special meaning to the <b>webtrees</b> Help system and can only be entered in their hexadecimal or decimal form.  Similarly, the&nbsp;&nbsp;<b>&lt;</b>&nbsp;&nbsp;and&nbsp;&nbsp;<b>&gt;</b>&nbsp;&nbsp;characters that usually enclose HTML tags must be entered in their hexadecimal or decimal forms if they are to be treated as normal text instead of signalling an HTML tag.<ul><li><b>&amp;&#x23;35;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x23;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x23;</b></li><li><b>&amp;&#x23;60;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x3C;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x3C;</b></li><li><b>&amp;&#x23;62;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x3E;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x3E;</b></li><li><b>&amp;&#x23;126;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x7E;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x7E;</b></li></ul>There is a&nbsp;&nbsp;<b>&amp;tilde;</b>&nbsp;&nbsp;HTML entity, but this symbol is not interpreted as a Tilde when coded in URLs.<br /><br />You can insert references to entries in the language files or to values of global variables.  Examples: <ul><li><b>&#x23;pgv_lang[add_to_cart]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the language variable "Add to Clippings Cart", and if it were to appear in this field, would show as <b>Add to Clippings Cart</b> when the FAQ list is viewed in the current language.</li><li><b>&#x23;factarray[AFN]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the Fact name $factarray["AFN"], and if it were to appear in this field, would show as <b>Ancestral File Number (AFN)</b> when the FAQ list is viewed in the current language. </li><li><b>&#x23;WT_VERSION&#x23;&nbsp;&#x23;WT_VERSION_RELEASE&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the constant WT_VERSION, a space, and a reference to the constant WT_VERSION_RELEASE, and if they were to appear in this field, would show as <b>#WT_VERSION#&nbsp;#WT_VERSION_RELEASE#</b> when the FAQ list is viewed in the current language.</li><li><b>&#x23;GLOBALS[GEDCOM]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the global variable $GEDCOM, which is the name of the current GEDCOM file.  If it were to appear in this field, it would show as <b>#GLOBALS[GEDCOM]#</b>.</li><li><b>&#x23;GLOBALS[GEDCOM_TITLE]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the global variable $GEDCOM_TITLE, which is the title of the current GEDCOM file.  If it were to appear in this field, it would show as <b>#GLOBALS[GEDCOM_TITLE]#</b>.</li></ul><br />This feature is useful when you wish to create FAQ lists that are different for each language your site supports.  You should put your customized FAQ list titles and entries into the <i>languages/extra.xx.php</i> files (<i>xx</i> is the code for each language), using the following format:<br />$pgv_lang["faq_title1"] = "This is a sample FAQ title";<br />$pgv_lang["faq_body1"] = "This is a sample FAQ body.";');
	break;

case 'add_faq_header':
	$title=i18n::translate('FAQ header');
	$text=i18n::translate('This is the title or subject of the FAQ item.<br /><br />What you enter here can be formatted. HTML tags such as &lt;b&gt; and &lt;br /&gt; are allowed, as are HTML entities such as &amp;amp; and &amp;nbsp;.  HTML tags other than &lt;br /&gt; are probably not very useful in the FAQ title and should be avoided.<br /><br />HTML entities are a very easy way to add special characters to your FAQ titles and text.  You can use symbolic names, decimal numbers, or hexadecimal numbers.  A complete list of HTML entities, their coding, and their representation by your browser can be found here:  <a href="http://htmlhelp.com/reference/html40/entities/" target="_blank">HTML entity lists</a><br /><br />On occasion, you may need to show a Tilde character&nbsp;&nbsp;<b>&#x7E;</b>&nbsp;&nbsp;or a Number Sign&nbsp;&nbsp;<b>&#x23;</b>&nbsp;&nbsp;in your URLs or text.  These characters have a special meaning to the <b>webtrees</b> Help system and can only be entered in their hexadecimal or decimal form.  Similarly, the&nbsp;&nbsp;<b>&lt;</b>&nbsp;&nbsp;and&nbsp;&nbsp;<b>&gt;</b>&nbsp;&nbsp;characters that usually enclose HTML tags must be entered in their hexadecimal or decimal forms if they are to be treated as normal text instead of signalling an HTML tag.<ul><li><b>&amp;&#x23;35;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x23;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x23;</b></li><li><b>&amp;&#x23;60;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x3C;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x3C;</b></li><li><b>&amp;&#x23;62;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x3E;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x3E;</b></li><li><b>&amp;&#x23;126;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x7E;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x7E;</b></li></ul>There is a&nbsp;&nbsp;<b>&amp;tilde;</b>&nbsp;&nbsp;HTML entity, but this symbol is not interpreted as a Tilde when coded in URLs.<br /><br />You can insert references to entries in the language files or to values of global variables.  Examples: <ul><li><b>&#x23;pgv_lang[add_to_cart]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the language variable "Add to Clippings Cart", and if it were to appear in this field, would show as <b>Add to Clippings Cart</b> when the FAQ list is viewed in the current language.</li><li><b>&#x23;factarray[AFN]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the Fact name $factarray["AFN"], and if it were to appear in this field, would show as <b>Ancestral File Number (AFN)</b> when the FAQ list is viewed in the current language. </li><li><b>&#x23;WT_VERSION&#x23;&nbsp;&#x23;WT_VERSION_RELEASE&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the constant WT_VERSION, a space, and a reference to the constant WT_VERSION_RELEASE, and if they were to appear in this field, would show as <b>#WT_VERSION#&nbsp;#WT_VERSION_RELEASE#</b> when the FAQ list is viewed in the current language.</li><li><b>&#x23;GLOBALS[GEDCOM]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the global variable $GEDCOM, which is the name of the current GEDCOM file.  If it were to appear in this field, it would show as <b>#GLOBALS[GEDCOM]#</b>.</li><li><b>&#x23;GLOBALS[GEDCOM_TITLE]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the global variable $GEDCOM_TITLE, which is the title of the current GEDCOM file.  If it were to appear in this field, it would show as <b>#GLOBALS[GEDCOM_TITLE]#</b>.</li></ul><br />This feature is useful when you wish to create FAQ lists that are different for each language your site supports.  You should put your customized FAQ list titles and entries into the <i>languages/extra.xx.php</i> files (<i>xx</i> is the code for each language), using the following format:<br />$pgv_lang["faq_title1"] = "This is a sample FAQ title";<br />$pgv_lang["faq_body1"] = "This is a sample FAQ body.";');
	break;

case 'add_faq_item':
	$title=i18n::translate('Add FAQ item');
	$text=i18n::translate('This option will let you add an item to the FAQ page.');
	break;

case 'add_faq_order':
	$title=i18n::translate('FAQ position');
	$text=i18n::translate('This field controls the order in which the FAQ items are displayed.<br /><br />You do not have to enter the numbers sequentially.  If you leave holes in the numbering scheme, you can insert other items later.  For example, if you use the numbers 1, 6, 11, 16, you can later insert items with the missing sequence numbers.  Negative numbers and zero are allowed, and can be used to insert items in front of the first one.<br /><br />When more than one FAQ item has the same position number, only one of these items will be visible.');
	break;

case 'add_faq_visibility':
	$title=i18n::translate('FAQ visibility');
	$text=i18n::translate('You can determine whether this FAQ will be visible regardless of GEDCOM, or whether it will be visible only to the current GEDCOM.<br /><ul><li><b>ALL</b>&nbsp;&nbsp;&nbsp;The FAQ will appear in all FAQ lists, regardless of GEDCOM.</li><li><b>%s</b>&nbsp;&nbsp;&nbsp;The FAQ will appear only in the currently active GEDCOM\'s FAQ list.</li></ul>', $GEDcom);
	break;

case 'add_from_clipboard':
	$title=i18n::translate('Add from clipboard');
	$text=i18n::translate('<b>webtrees</b> allows you to copy up to 10 facts, with all their details, to a clipboard.  This clipboard is different from the Clippings Cart that you can use to export portions of your database.<br /><br />You can select any of the facts from the clipboard and copy the selected fact to the Individual, Family, Media, Source, or Repository record currently being edited.  However, you cannot copy facts of dissimilar record types.  For example, you cannot copy a Marriage fact to a Source or an Individual record since the Marriage fact is associated only with Family records.<br /><br />This is very helpful when entering similar facts, such as census facts, for many individuals or families.');
	break;

case 'add_gedcom':
	// duplicate text. see 'help_addgedcom.php'
	$title=i18n::translate('Add GEDCOM');
	$text=i18n::translate('When you use the <b>Add GEDCOM</b> function, it is assumed that you have already uploaded the GEDCOM file to your server using a program or method <u>external</u> to <b>webtrees</b>, for example, <i>ftp</i> or <i>network connection</i>.  The file you wish to add could also have been left over from a previous <b>Upload GEDCOM</b> procedure.<br /><br />If the input GEDCOM file is not yet on your server, you <u>have to</u> get it there first, before you can start with Adding.<br /><br />Instead of uploading a GEDCOM file, you can also upload a ZIP file containing the GEDCOM file, either with <b>webtrees</b>, or using an external program. <b>webtrees</b> will recognize the ZIP file automatically and will extract the GEDCOM file and filename from the ZIP file.<br /><br />If a GEDCOM file with the same name already exists in <b>webtrees</b>, it will be overwritten. However, all GEDCOM settings made previously will be preserved.<br /><br />You are guided step by step through the procedure.');
	break;

case 'add_husband':
	$title=i18n::translate('Add a new husband');
	$text=i18n::translate('By clicking this link, you can add a <u>new</u> male person and link this person to the principal individual as a new husband.<br /><br />Just click the link, and you will get a pop up window to add the new person.  Fill out as many boxes as you can and click the <b>Save</b> button.<br /><br />That\'s all.');
	break;

case 'add_media':
	$title=i18n::translate('Add a new media item');
	$text=i18n::translate('Adding multimedia files (MM) to the GEDCOM is a very nice feature.  Although this program already has a great look without media, if you add pictures or other MM to your relatives, it will only get better.<br /><br /><b>What you should understand about MM.</b><br />There are many formats of MM. Although <b>webtrees</b> can handle most of them, there some things to consider.<br /><ul><li><b>Formats</b><br />Pictures can be edited and saved in many formats.  For example, .jpg, .png, .bmp, .gif, etc.  If the same original picture was used to create each of the formats, the viewed image will appear to be the same size no matter which format is used.  However, the image files stored in the database will vary considerably in size.  Generally, .jpg images are considered to the most efficient in terms of storage space.</li><li><b>Image size</b><br />The larger the original image, the larger will be the resultant file\'s size. The picture should fit on the screen without scrolling; the maximum width or height should not be more than the width or height of the screen. <b>webtrees</b> is designed for screens of 1024x768 pixels but not all of this space is available for viewing pictures; the picture\'s size should be set accordingly.  To reduce file sizes, smaller pictures are more desirable.</li><li><b>Resolution</b><br />The resolution of a picture is usually measured in "dpi" (dots/inch), but this is valid only for printed pictures.  When considering pictures shown on screen, the only correct way is to use total dots or pixels. When printed, the picture could have a resolution of 150 - 300 dpi or more depending on the printer. Screen resolutions are rarely better than 50 pixels per inch.  If your picture will never be printed, you can safely lower its resolution (and consequently its file size) without affecting picture quality.  If a low-resolution picture is printed with too great a magnification, its quality will suffer; it will have a grainy appearance.</li><li><b>Color depth</b><br />Another way to keep a file small is to decrease the number of colors that you use.  The number of colors can differ from pure black and white (two colors) to true colors (millions of colors) and anything in between.  You can see that the more colors are used, the bigger the size of the files.</li></ul><b>Why is it important to keep the file size small?</b><br /><ul><li>First of all: Our webspace is limited.  The more large files there are, the more web space we need on the server. The more space we need, the higher our costs.</li><li>Bandwidth.  The more data our server has to send to the remote location (your location), the more we have to pay.  This is because the carrying capacity of the server\'s connection to the Internet is limited, and the link has to be shared (and paid for) by all of the applications running on the server.  <b>webtrees</b> is one of many applications that share the server.  The cost is normally apportioned according to the amount of data each application sends and receives.</li><li>Download time. If you have large files, the user (also you) will have to wait long for the page to download from the server.  Not everybody is blessed with a cable connection, broadband or DSL.</li></ul><b>How to upload your MM</b><br />There are two ways to upload media to the site.  If you have a lot of media items to upload you should contact the site administrator to discuss the best ways.  If it has been enabled by your site administrator, you can use the Upload Media form under your My Page menu.  You can also use the Upload option on the Multimedia form to upload media items.');
	break;

case 'add_media_linkid':
	$title=i18n::translate('Link ID');
	$text=i18n::translate('Each media item should be associated with one or more person, family, or source records in your database.<br /><br />To establish such a link, you can enter or search for the ID of the person, family, or source at the same time as you create the media item.  You can also establish the link later through editing options on the Manage MultiMedia page, or by adding media items through the Add Media link available on the Individual, Family, or Source Details pages.');
	break;
	
case 'add_name':
	$title=i18n::translate('Add a new name');
	$text=i18n::translate('This link will allow you to add another name to this individual.  Sometimes people are known by other names or aliases.  This link allows you to add new names to a person without changing the old name.');
	break;

case 'add_new_facts':
	// This is a general help text for multiple pages
	$title=i18n::translate('Add a new fact');
	$text=i18n::translate('<ul><li><a href="?help=add_facts">Add Fact</a></li><li><a href="?help=add_custom_facts">Add Custom Fact</a></li><li><a href="?help=add_from_clipboard">Add from Clipboard</a></li><li><a href="?help=def_gedcom_date">Dates in a GEDCOM File</a></li><li><a href="?help=add_facts_general">General Information about Adding</a></li></ul>');
	break;
	
case 'add_new_gedcom':
	// duplicate text. see 'help_addnewgedcom.php'
	$title=i18n::translate('Create a new GEDCOM');
	$text=i18n::translate('You can start a new genealogical database from scratch.<br /><br />This procedure requires only a few simple steps. Step 1 is different from what you know already about uploading and adding. The other steps will be familiar.<ol><li><b>Naming the new GEDCOM</b><br />Type the name of the new GEDCOM <u>without</u> the extension <b>.ged</b>. The new file will be created in the directory named above the box where you enter the name.  Click <b>Add</b>.</li><li><b>Configuration page</b><br />You already know this page;  you configure the settings for your new GEDCOM file.</li><li><b>Validate</b><br />You already know this page;  the new GEDCOM is checked.  Since there is nothing in it, it will be ok.</li><li><b>Importing Records</b><br />Since there will be only one record to import, this will be finished very fast.</li></ol>That\'s it.  Now you can go to the Pedigree chart to see your first person in the new GEDCOM. Click the name of the person and start editing. After that, you can link new individuals to the first person.');
	break;

case 'add_new_parent':
	// This is a general help text for multiple pages
	$title=i18n::translate('Add a new parent');
	$text=i18n::translate('There are certainly many individuals in the GEDCOM without a record of a father or mother.<br /><br />In that case, on the <b>Individual Information</b> page, tab sheet <b>Close Relatives</b>, table <b>Family with Parents</b>, you will find links to add a <u>new</u> father or mother to the individual.<br /><br />Please keep in mind that these links are for adding a <u>new</u> father or mother.  If the father or mother already has a record in this database, you have to use the link <b>Link this person to an existing family as a child</b>, which you will find on that <b>Individual Information</b> page below the last table.');
	break;

case 'add_note':
	// This is a general help text for multiple pages
	$title=i18n::translate('Add a new note');
	$text=i18n::translate('If you have a note to add to this record, this is the place to do so.<br /><br />Just click the link, a window will open, and you can type your note.  When you are finished typing, just click the button below the box, close the window, and that\'s all.<br /><br />~General info about adding~<br />When you have added a fact, note, source, or multimedia file to a record in the database, the addition still has to be approved by a user who has Accept rights.<br /><br />Until the changes have been Accepted, they are identified as "pending" by a differently colored border.  All users with Edit rights can see these changes as well as the original information.  Users who do not have Edit rights will only see the original information. When the addition has been Accepted, the borders will disappear and the new data will display normally, replacing the old.  At that time, users without Edit rights will see the new data too.');
	break;

case 'add_opf_child':
	$title=i18n::translate('Add a new child to create one-parent family');
	$text=i18n::translate('By clicking this link, you can add a <u>new</u> child to this person, creating a one-parent family.<br /><br />Just click the link, and you will get a pop up window to add the new person.  Fill out as many boxes as you can and click the <b>Save</b> button.<br /><br />That\'s all.');
	break;

case 'add_person':
	$title=i18n::translate('Add a new person to the chart');
	$text=i18n::translate('You can have several persons on the timeline.<br /><br />Use this box to supply each person\'s ID.  If you don\'t know the ID of the person, you can click the <b>Find ID</b> link next to the box.');
	break;

case 'add_repository_clip':
	$title=i18n::translate('Add repository to clippings cart');
	$text=i18n::translate('When you click this link you can add the repository, as it is stored in the GEDCOM, to your Clippings Cart.');
	break;

case 'add_shared_note':
	// This is a general help text for multiple pages
	$title=i18n::translate('Add a new shared note');
	$text=i18n::translate('When you click the <b>Add a new Shared Note</b> link, a new window will open.  You can choose to link to an existing shared note, or you can create a new shared note and at the same time create a link to it.<br /><br />~General info about adding~<br />When you have added a fact, note, source, or multimedia file to a record in the database, the addition still has to be approved by a user who has Accept rights.<br /><br />Until the changes have been Accepted, they are identified as "pending" by a differently colored border.  All users with Edit rights can see these changes as well as the original information.  Users who do not have Edit rights will only see the original information. When the addition has been Accepted, the borders will disappear and the new data will display normally, replacing the old.  At that time, users without Edit rights will see the new data too.');
	break;

case 'add_sibling':
	$title=i18n::translate('Add a new brother or sister');
	$text=i18n::translate('You can add a child to this family when you click this link.  "This Family", in this case, is the father and mother of the principal person of this screen.<br /><br />Keep in mind that you are going to add a sibling of that person.  Adding a brother or sister is simple: Just click the link, fill out the boxes in the pop up screen and that\'s all.<br /><br />If you have to add a son or daughter of the principal person, scroll down a little and click the link in "Family with Spouse".');
	break;

case 'add_son_daughter':
	$title=i18n::translate('Add a new son or daughter');
	$text=i18n::translate('You can add a child to this family when you click this link.  "This Family", in this case, is the principal person of this screen and his or her spouse.<br /><br />Keep in mind that you are going to add a son or daughter of that person.  Adding a son or daughter is simple: Just click the link, fill out the boxes in the popup screen and that\'s all.<br /><br />If you have to add a brother or sister of the principal person, scroll up a little and click the link in "Family with Parents".');
	break;

case 'add_source_clip':
	$title=i18n::translate('Add source to clippings cart');
	$text=i18n::translate('When you click this link, you can add the source\'s information to your Clippings Cart for later downloading and importing into your own genealogy program.');
	break;

case 'add_source':
	// This is a general help text for multiple pages
	$title=i18n::translate('Add a new source citation');
	$text=i18n::translate('Here you can add a source <b>Citation</b> to this record.<br /><br />Just click the link, a window will open, and you can choose the source from the list (Find ID) or create a new source and then add the Citation.<br /><br />Adding sources is an important part of genealogy because it allows other researchers to verify where you obtained your information.<br /><br />~General info about adding~<br />When you have added a fact, note, source, or multimedia file to a record in the database, the addition still has to be approved by a user who has Accept rights.<br /><br />Until the changes have been Accepted, they are identified as "pending" by a differently colored border.  All users with Edit rights can see these changes as well as the original information.  Users who do not have Edit rights will only see the original information. When the addition has been Accepted, the borders will disappear and the new data will display normally, replacing the old.  At that time, users without Edit rights will see the new data too.');
	break;

case 'add_upload_gedcom':
	$title=i18n::translate('Adding versus uploading GEDCOM');
	$text=i18n::translate('<dl><dt><b>Uploading GEDCOM Files</b></dt><dd>Uploading files can be done on line.  You can upload from anywhere without needing an ftp program.</dd><dt><b>Adding GEDCOM Files</b></dt><dd>If a previously uploaded file is still present in your GEDCOM directory, you can use it again without uploading.  Sometimes, because of file or upload size limitations, you need to use Add.</dd></dl>The Add and the Upload procedure can be finished in four simple steps.  In either procedure, only Step 1 is different.');
	break;

case 'add_wife':
	$title=i18n::translate('Add a new wife');
	$text=i18n::translate('By clicking this link, you can add a <u>new</u> female person and link this person to the principal individual as a new wife.<br /><br />Just click the link, and you will get a pop up window to add the new person.  Fill out as many boxes as you can and click the <b>Save</b> button.<br /><br />That\'s all.');
	break;

case 'admin':
	$title=i18n::translate('Administration');
	$text=i18n::translate('On this page you will find links to the configuration pages, administration pages, documentation, and log files.<br /><br /><b>Current Server Time:</b>, just below the page title, shows the time of the server on which your site is hosted. This means that if the server is located in New York while you\'re in France, the time shown will be six hours less than your local time, unless, of course, the server is running on Greenwich Mean Time (GMT).  The time shown is the server time when you opened or refreshed this page.<br /><br /><b>WARNING</b><br />When you see a red warning message under the system time, it means that your <i>config.php</i> is still writeable.  After configuring your site, you should, for <b>security</b>, set the permissions of this file back to read-only.  You have to do this <u>manually</u>, since <b>webtrees</b> cannot do this for you.');
	break;

case 'admin_help_contents_head':
	$title=i18n::translate('Help contents');
	$text=i18n::translate('<b>Administrator Help Items</b> added to the beginning of the list.');
	break;

case 'age_differences':
	$title=i18n::translate('Show date differences');
	$text=i18n::translate('When this option box is checked, the «Close Relatives» tab will show date differences as follows:<br /><ul><li>birth dates of partners.<br />A negative value indicates that the second partner is older than the first.<br /><br /></li><li>marriage date and birth date of the first child.<br />A negative value here indicates that the child was born before the marriage date or that either the birth date or the marriage date is wrong.<br /><br /></li><li>birth dates of siblings.<br />A negative value here indicates that either the order of the children is wrong or that one of the birth dates is wrong.</li></ul>');
	break;

case 'alpha':
	$title=i18n::translate('Alphabetical index');
	$text=i18n::translate('Clicking a letter in the Alphabetical index will display a list of the names that start with the letter you clicked.<br /><br />The second to last item in the Alphabetical index can be <b>(unknown)</b>.  This entry will be present when there are people in the database whose surname has not been recorded or does not contain any recognizable letters.  Unknown surnames are often recorded as <b>?</b>, and these will be recognized as <b>(unknown)</b>.  This will also happen if the person is unknown.<br /><br /><b>Note:</b><br />Surnames entered as, for example, <b>Nn</b>, <b>NN</b>, <b>Unknown</b>, or even <b>N.N.</b> will <u>not</u> be found in the <b>(unknown)</b> list. Instead, you will find these persons by clicking <b>N</b> or <b>U</b> because these are the initial letters of those names.  <b>webtrees</b> cannot possibly account for all possible ways of entering unknown surnames;  there is no recognized convention for this.<br /><br />At the end of the Alphabetical index you see <b>ALL</b>. When you click on this item, you will see a list of all surnames in the database.<br /><br /><b>Missing letters?</b><br />If your Alphabetical index appears to be incomplete, with missing letters, your database doesn\'t contain any surnames that start with that missing letter.');
	break;

case 'alphabet_lower':
	$title=i18n::translate('Alphabet lower case');
	$text=i18n::translate('Lower case alphabet letters in this language.  This alphabet is used while sorting lists of names.');
	break;

case 'alphabet_upper':
	$title=i18n::translate('Alphabet upper case');
	$text=i18n::translate('Upper case alphabet letters in this language.  This alphabet is used while sorting lists of names.');
	break;

case 'annivers_date_select':
	$title=i18n::translate('Day selector');
	$text=i18n::translate('The top row of the Selector table is the <b>Day</b> selector.  Its meaning is obvious: You select a <u>day</u>.<br /><br />The result of clicking on a certain day depends of whether you are in <b>Day</b> or in <b>Month</b> mode.<br /><dl><dt><b>Day mode</b></dt><dd>In this mode, you click a day, the screen will refresh, and the list for that day will be displayed.</dd><dt><b>Month mode</b></dt><dd>You have the calendar of a certain month on the screen.  You click a day and the screen will refresh, but you will still see the month that you had on the screen before.  The reason for this is that you can still decide to select another month, year, or event before you either click the <b>View Day</b> or <b>View Month</b> button.<br /><br />At the end of the Day row you will see a <b>Quick Link</b> with today\'s date.  Clicking that <b>Quick Link</b> will display the list for today in <b>Day</b> mode, no matter whether you are in <b>Month</b> or in <b>Day</b> mode.</dd></dl>');
	break;

case 'annivers_event':
	$title=i18n::translate('Event selector');
	$text=i18n::translate('Here you choose whether you want all events for individuals and families displayed or just a selected event.  You cannot select more than one event category.<br /><br />When you click on an option, the events of your choice will be displayed.<br /><br />The settings of day, month, and year, as well as <b>Day</b> or <b>Month</b> mode, remain as they were.');
	break;

case 'annivers_month_select':
	$title=i18n::translate('Month selector');
	$text=i18n::translate('The middle row of the Selector table is the <b>Month</b> selector.  Its meaning is obvious: You select a <u>month</u>.<br /><br />The result of clicking on a certain month depends of whether you are in <b>Day</b> or in <b>Month</b> mode.<br /><dl><dt><b>Day mode</b></dt><dd>In this mode, you click a month, the screen will refresh, and the list for that month will be displayed.  All other selections like day, year, and events will be unchanged.</dd><dt><b>Month mode</b></dt><dd>When you have the calendar on the screen and click a month in the <b>Month</b> row, the calendar for that new month will be displayed.<br /><br />At the end of the Month row you will see a <b>Quick Link</b> with today\'s month and year.  Clicking that <b>Quick Link</b> will display the list for that month in <b>Month</b> mode, no matter whether you are in <b>Month</b> or in <b>Day</b> mode.</dd></dl>');
	break;

case 'annivers_sex':
	$title=i18n::translate('Gender selector');
	$text=i18n::translate('When you are logged in or when the admin has not enabled the Privacy option, you can select one of these options:<ol><li><b>All</b> icon<br />This is the default option. The events of all individuals and families are displayed.</li><li><b>Male</b> icon<br />Only events of male individuals are displayed. Only male members of families will be displayed with Family events.</li><li><b>Female</b> icon<br />Only events of female individuals are displayed. Only female members of families will be displayed with Family events.</li></ol>When you click on an option, the events of your choice will be displayed.<br /><br />The settings of day, month, and year, as well as <b>Day</b> or <b>Month</b> mode, remain as they were.');
	break;

case 'annivers_show':
	$title=i18n::translate('Show events of:');
	$text=i18n::translate('The following options are available:<br /><ol><li><b>All People</b><br />With this option, all individuals and families are displayed.</li><li><b>Recent Years (&lt;100 yrs)</b><br />With this option you will see all events for the chosen day or month, but no events older than 100 years will be shown.</li><li><b>Living People</b><br />Unless the administrator has configured <b>webtrees</b> so that living people are visible to anyone, this option will only be available to you after you have logged in.<br /><br />With this option, only the events of living persons will be displayed.</li></ol>When you click on an option, the events of your choice will be displayed.<br /><br />The settings of day, month, and year, as well as <b>Day</b> or <b>Month</b> mode, remain as they were.');
	break;

case 'annivers_tip':
	$title=i18n::translate('Tip');
	$text=i18n::translate('Adjust the Date selector to any date in the past.<br /><br />When you click on one of the View buttons you will see a list or calendar for that date.  All the ages, anniversaries, etc. have been recalculated and now count from the date you set in the Date selector.  You are now seeing the calendar or list that your ancestor would have seen on that date, years ago.');
	break;

case 'annivers_year_select':
	$title=i18n::translate('Year input box');
	$text=i18n::translate('This input box lets you change that year of the calendar.  Type a year into the box and press <b>Enter</b> to change the calendar to that year.<br /><br /><b>Advanced features</b> for <b>View Year</b><dl><dt><b>More than one year</b></dt><dd>You can search for dates in a range of years.<br /><br />Year ranges are <u>inclusive</u>.  This means that the date range extends from 1 January of the first year of the range to 31 December of the last year mentioned.  Here are a few examples of year ranges:<br /><br /><b>1992-5</b> for all events from 1992 to 1995.<br /><b>1972-89</b> for all events from 1972 to 1989.<br /><b>1610-759</b> for all events from 1610 to 1759.<br /><b>1880-1905</b> for all events from 1880 to 1905.<br /><b>880-1105</b> for all events from 880 to 1105.<br /><br />To see all the events in a given decade or century, you can use <b>?</b> in place of the final digits. For example, <b>197?</b> for all events from 1970 to 1979 or <b>16??</b> for all events from 1600 to 1699.<br /><br/>Selecting a range of years will change the calendar to the year view.</dd></dl>');
	break;
	
case 'apply_privacy':
	$title=i18n::translate('Apply privacy settings?');
	$text=i18n::translate('When this option is checked, the output file will pass through privacy checks according to the selected option.  This can result in the removal of certain information.  The output file will contain only the information that is normally visible to a user with the indicated privilege level.<br /><br />If you only have GEDCOM administrator rights, you cannot specify that the output file should be privatized according to the Site administrator privilege level.');
	break;

case 'autoContinue':
	$title=i18n::translate('Automatically press «continue» button');
	$text=i18n::translate('When <b>webtrees</b> detects that the GEDCOM Import requires more time than is permitted by the time limit, it will display a <b>Continue</b> button that you must press to continue the Import.<br /><br />When this option is set to <b>Yes</b>, <b>webtrees</b> will automatically press the <b>Continue</b> button for you.  This should relieve the tedium of having to press the button repeatedly for lengthy Imports.');
	break;

case 'basic_or_all':
	$title=i18n::translate('Show only births, deaths and marriages?');
	$text=i18n::translate('This option lets you eliminate some dated events.  For example, Divorce, Cremation, Graduation, Bar Mitzvah, First Communion, etc. should all be dated.<br /><br />When you select <b>Yes</b>, only Births, Deaths, and Marriages will be shown. When you select <b>No</b>, all dated events will be shown.');
	break;

case 'best_display':
	$title=i18n::translate('Screen display');
	$text=i18n::translate('<b>webtrees</b> is designed for a screen size of 1024x768 pixels.  This should be the minimum size to have everything displayed properly.<br /><br />If you set the size to a lower value (for example 800x600), you may need to do horizontal scrolling on some pages.');
	break;

case 'block_move_right':
	$title=i18n::translate('Move list entries');
	$text=i18n::translate('Use these buttons to move an entry from one list to another.<br /><br />Highlight the entry to be moved, and then click a button to move or copy that entry in the direction of the arrow.  Use the <b>&raquo;</b> and <b>&laquo;</b> buttons to move the highlighted entry from the leftmost to the rightmost list or vice-versa.  Use the <b>&gt;</b> and <b>&lt;</b> buttons to move the highlighted entry between the Available Blocks list and the list to its right or left.<br /><br />The entries in the Available Blocks list do not change, regardless of what you do with the Move Right and Move Left buttons.  This is so because the same block can appear several times on the same page.  The HTML block is a good example of why you might want to do this.');
	break;

case 'block_move_up':
	$title=i18n::translate('Move list entries');
	$text=i18n::translate('Use these buttons to re-arrange the order of the entries within the list.  The blocks will be printed in the order in which they are listed.<br /><br />Highlight the entry to be moved, and then click a button to move that entry up or down.');
	break;

case 'bom_check':
	$title=i18n::translate('Byte Order Mark (BOM) check');
	$text=i18n::translate('This check will analyze all the language files for the BOM (Byte Order Mark). If found, it will remove the BOM from the affected file. These special codes can cause malfunctions in some parts of <b>webtrees</b>.');
	break;

case 'box_width':
	$title=i18n::translate('Box width');
	$text=i18n::translate('Here you can change the box width from 50 percent to 300 percent.  At 100 percent each box is about 270 pixels wide.');
	break;

case 'cache_life':
	$title=i18n::translate('Cache file life');
	$text=i18n::translate('To improve performance, this webtrees Home Page block is saved as a cache file.  You can control how often this block\'s cache file is refreshed.<br /><br /><ul><li><b>-1</b> means that the cache file is never refreshed automatically.  To get a fresh copy, you need to delete all cache files.  You can do this on the Customize Home Page screen.</li><li><b>0</b> (Zero) means that this block is never cached, and every time the block is displayed on the webtrees Home Page, you see a fresh copy.  This setting is used automatically for blocks that change frequently, such as the Logged In Users and the Random Media blocks.</li><li><b>1</b> (One) means that a fresh copy of this block\'s cache file is created daily, <b>2</b> means that a fresh copy is created every two days, <b>7</b> means that a fresh copy is created weekly, etc.</li></ul>');
	break;

case 'cal_dowload':
	$title=i18n::translate('Download calendar');
	$text=i18n::translate('This option controls whether the button for downloading calendar events is shown to logged-in users. The downloaded calendar file can be imported into compatible programs such as Microsoft Outlook to, for example, generate automatic e-mail reminders of anniversaries.<br /><br />When set to <b>No</b>, the logged-in user will not be able to download the calendar file.  When set to <b>Yes</b>, the Download button will be shown.  This button is never shown when the user is not logged in.');
	break;

case 'change_indi2id':
	$title=i18n::translate('Change individual ID to ....');
	$text=i18n::translate('This tool was designed for users whose Genealogy programs use a different GEDCOM ID for the individuals every time the GEDCOM is exported. For example, the first time the GEDCOM is exported some person\'s ID might be I100 but the next time the GEDCOM is exported that same person\'s ID is changed to I234. These changing IDs make it difficult to administer <b>webtrees</b> because the ID is how people are referenced.<br /><br />Most genealogy programs also use the RIN or REFN tag to give each person a unique identifier that can be used to reference the individual. This tool will replace all of the individual IDs in the GEDCOM file with the whatever field (RIN or REFN) you specify.');
	break;

case 'chart_area':
	$title=i18n::translate('Chart area');
	$text=i18n::translate('Select the geographical area that you want to see on the map. You can choose:<p style="padding-left: 25px"><b>World</b>&nbsp;&nbsp;shows all continents.<br /><b>Europe</b>&nbsp;&nbsp;shows Europe.<br /><b>South America</b>&nbsp;&nbsp;shows South America.<br /><b>Asia</b>&nbsp;&nbsp;shows Asia.<br /><b>Middle East</b>&nbsp;&nbsp;shows the Middle East.<br /><b>Africa</b>&nbsp;&nbsp;shows Africa.</p>');
	break;

case 'chart_style':
	$title=i18n::translate('Chart style');
	$text=i18n::translate('Two chart styles are available:<ul><li><b>List</b><br />Vertical tree, with collapsible/expandable families.</li><li><b>Booklet</b><br />One family per page, with parents, grandparents, and all recorded children.<br /><br />This format is easy to print to give to your relatives.</li></ul>');
	break;

case 'chart_type':
	$title=i18n::translate('Chart type');
	$text=i18n::translate('Select what you want to see on the map chart. You can choose:<p style="padding-left: 25px"><b>Individual distribution chart</b>&nbsp;&nbsp;shows the countries in which persons from this database occur.<br /><b>Surname distribution chart</b>&nbsp;&nbsp;shows the countries in which the specified surname occurs.</p>');
	break;

case 'cleanup_places':
	$title=i18n::translate('Cleanup places');
	$text=i18n::translate('<b>webtrees</b> detected that your GEDCOM file uses places on GEDCOM tags that should not have places.<br /><br />Many genealogy programs, such as Family Tree Maker, will create this type of GEDCOM file. <b>webtrees</b> will work with these GEDCOM files, but some invalid places will show up in your place hierarchy. <br /><br />For example, your GEDCOM might have the following encoding<br />1 SSN<br />2 PLAC 123-45-6789<br />1 OCCU<br />2 PLAC Computer Programmer<br /><br />According to the GEDCOM 5.5.1 Standard this should really be shown as<br />1 SSN 123-45-6789<br />1 OCCU Computer Programmer<br /><br />If you select <b>Yes</b>, <b>webtrees</b> will automatically correct these encoding errors.');
	break;

case 'clear_cache':
	$title=i18n::translate('Clear cache files');
	$text=i18n::translate('In order to improve performance, several of the blocks on the <b>webtrees</b> Home Page are saved as cache files in the index directory.  The cache files for most blocks are refreshed once each day, but there may be times when you want to refresh them manually.<br /><br />This button allows you to refresh the cache files when necessary.');
	break;

case 'click_here':
	$title=i18n::translate('Click here to continue');
	$text=i18n::translate('Click this button to save your changes.<br /><br />You will be returned to My Page or the Home Page, but your changes may not be shown.  You may need to use the Page Reload function of your browser to view your changes properly.');
	break;

case 'clip_download':
	$title=i18n::translate('Download clippings cart');
	$text=i18n::translate('When you click this link you will be taken to the next page.  If any of the clippings in your cart refer to multimedia items, these items will also be displayed on that page.<br /><br />Simply follow the instructions.');
	break;

case 'collation':
	$title=i18n::translate('Database collation sequence');
	$text=i18n::translate('If you are using the database\'s built-in collation rules, this option specifies the collation sequence to use for this language.  You should ensure that your database supports all the collation sequences you intend to use.<br /><br />The use of database collation is controlled in the site configuration settings.');
	break;

case 'config':
	$title=i18n::translate('Configuration');
	$text=i18n::translate('Configuration help');
	break;

case 'config_help':
	$title=i18n::translate('Configuration help');
	$text=i18n::translate('This page collects all of the major topics of Configuration Help into one place.  You can view the information on your screen, or you can print it for later use.');
	break;

case 'config_lang_utility':
	$title=i18n::translate('Configuration of supported languages');
	$text=i18n::translate('This page is used to control what language choices are available to your users.  For example, you can set things up so that only German and French are available.  This might be useful if, for example, you are not able to communicate with your users in Hungarian.<br /><br />You also use this page to alter certain aspects of <b>webtrees</b> that depend on the selected language.  For example, here is where you tell <b>webtrees</b> how to format date and time fields.<br /><br />The languages that are active and greyed out cannot be disabled because they are in use. Look at the bottom table to see where the language is used. When a language is no longer used by the GEDCOM or user you will be able to disable it.<br /><br />All of your changes will be recorded in a new file called <b>lang_settings.php</b> created in the <b>#INDEX_DIRECTORY#</b> directory.  All of your further changes will be made to this new file and <b>webtrees</b> will use only <u>this</u> file.  You can revert to the original default language settings by deleting this file.<br /><br />If you must report problems with your language settings, please tell the <b>webtrees</b> support team whether this new file is present or not.');
	break;

case 'context':
	$title=i18n::translate('Context');
	$text=i18n::translate('More help is available by clicking the <b>?</b> next to items on the page.');
	break;

case 'convertPath':
	$title=i18n::translate('Convert media path to');
	$text=i18n::translate('This option defines a constant path to be prefixed to all media paths in the output file.<br /><br />For example, if the media directory has been configured to be "/media" and if the media file being exported has a path "/media/pictures/xyz.jpg" and you have entered "c:\my pictures\my family" into this field, the resultant media path will be "c:\my pictures\my family/pictures/xyz.jpg".<br /><br />You will notice in this example:<ul><li>the current media directory name is stripped from the path</li><li>and the resultant path will not have correct folder name separators.</li></ul><br />If you wish to retain the media directory in media file paths of the output file, you will need to include that name in the <b>Convert media path to</b> field.<br /><br />You should also use the <b>Convert media folder separators to</b> option to ensure that the folder name separators are consistent and agree with the requirements of the receiving operating system.<br /><br />Media paths that are actually URLs will not be changed.');
	break;

case 'convertSlashes':
	$title=i18n::translate('Convert media folder separators to');
	$text=i18n::translate('This option determines whether folder names in the FILE specification of media objects should be separated by forward slashes or by backslashes.  Your choice depends on the requirements of the receiving operating system.<br /><br />The choice <b>Forward slashes : /</b> is appropriate for most operating systems other than Microsoft Windows.  The choice <b>Backslashes : \</b> should be used when the destination program is running on a Microsoft Windows system.<br /><br />Media paths that are actually URLs will not be changed.');
	break;

case 'convert_ansi2utf':
	$title=i18n::translate('Convert ANSI to UTF-8');
	$text=i18n::translate('To ensure that the information in your input GEDCOM files is processed and displayed correctly, these files should be encoded in UTF-8.<br /><br />Some of the more modern genealogy programs can export their data to a GEDCOM file in UTF-8 encoding.  Older programs often don\'t have this capability.  If your program does not offer you this option, <b>webtrees</b> can convert the file for you.<br /><br />When <b>webtrees</b> validates the input file, it will detect the file\'s encoding and advise you accordingly.');
	break;

case 'cookie':
	$title=i18n::translate('Cookies');
	$text=i18n::translate('This site uses cookies to keep track of your login status.<br /><br />Cookies do not appear to be enabled in your browser. You must enable cookies for this site before you can login.  You can consult your browser\'s help documentation for information on enabling cookies.');
	break;

case 'day_month_help':
	$title=i18n::translate('View day / View month / View year');
	$text=i18n::translate('<ul><li>The <b>View Day</b> button will display the events of the chosen date in a list. All years are scanned, so only the day and month can be set here. Changing the year will have no effect.  You can reduce the list by choosing the option <b>Recent years</b> or <b>Living people</b>.<br /><br />Ages in the list will be calculated from the current year.</li><li>The <b>View Month</b> button will display a calendar diagram of the chosen month and year. Here too you can reduce the lists by choosing the option <b>Recent years</b> or <b>Living people</b>.<br /><br />You will get a realistic impression of what a calendar on the wall of your ancestors looked like by choosing a year in the past in combination with <b>Recent years</b>. All ages on the calendar are shown relative to the year in the Year box.</li><li>The <b>View Year</b> button will show you a list of events of the chosen year.  Here too you can reduce the list by choosing the option <b>Recent years</b> or <b>Living people</b>.<br /><br />You can show events for a range of years.  Just type the beginning and ending years of the range, with a dash <b>-</b> between them.  Examples:<br /><b>1992-4</b> for all events from 1992 to 1994<br /><b>1976-1984</b> for all events from 1976 to 1984<br /><br />To see all the events in a given decade or century, you can use <b>?</b> in place of the final digits. For example, <b>197?</b> for all events from 1970 to 1979 or <b>16??</b> for all events from 1600 to 1699.</li></ul>When you want to <b>change the year</b> you <b>have</b> to press one of these three buttons.  All other settings remain as they were.');
	break;

case 'days_to_show':
	$title=i18n::translate('Number of days to show');
	$text=i18n::translate('Enter the number of days to show.  This number cannot be greater than <b>#DAYS_TO_SHOW_LIMIT#</b>.  If you enter a larger value, that limit will be used.<br /><br />The limit shown is set by the administrator in the GEDCOM configuration, Display and Layout section, Hide &amp; Show sub-section.');
	break;

case 'def_gedcom_date':
	$title=i18n::translate('Dates in a GEDCOM file');
	$text=i18n::translate('Although the date field allows for free-form entry (meaning you can type in whatever you want), there are some rules about how dates should be entered according to the GEDCOM 5.5.1 standard.<ol><li>A full date is entered in the form DD MMM YYYY.  For example, <b>01&nbsp;MAR&nbsp;1801</b> or <b>14&nbsp;DEC&nbsp;1950</b>.</li><li>If you are missing a part of the date, you can omit that part.  E.g. <b>MAR&nbsp;1801</b> or <b>14&nbsp;DEC</b>.</li><li>If you are not sure or the date is not confirmed, you could enter <b>ABT&nbsp;MAR&nbsp;1801</b> (abt = about), <b>BEF&nbsp;20&nbsp;DEC&nbsp;1950</b> (bef = before), <b>AFT&nbsp;1949</b> (aft = after)</li><li>Date ranges are entered as <b>FROM&nbsp;MAR&nbsp;1801&nbsp;TO&nbsp;20&nbsp;DEC&nbsp;1810</b> or as <b>BET&nbsp;MAR&nbsp;1801&nbsp;AND&nbsp;20&nbsp;DEC&nbsp;1810</b> (bet = between)<br /><br />The <b>FROM</b> form indicates that the event being described happened continuously between the stated dates and is used with events such as employment. The <b>BET</b> form indicates a single occurrence of the event, sometime between the stated dates and is used with events such as birth.<br /><br />Imprecise dates, where the day of the month or the month is missing, are always interpreted as the first or last possible date, depending on whether that imprecise date occurs before or after the separating keyword.  For example, <b>FEB&nbsp;1804</b> is interpreted as <b>01&nbsp;FEB&nbsp;1804</b> when it occurs before the TO or AND, and as <b>29&nbsp;FEB&nbsp;1804</b> when it occurs after the TO or AND.</li></ol><b>Be sure to enter dates and abbreviations in <u>English</u>,</b> because then the GEDCOM file is exchangeable and <b>webtrees</b> can translate all dates and abbreviations properly into the currently active language.  Furthermore, <b>webtrees</b> does calculations using these dates. If improper dates are entered into date fields, <b>webtrees</b> will not be able to calculate properly.<br /><br />You can click on the Calendar icon for help selecting a date.');
	break;

case 'def_gedcom':
	$title=i18n::translate('GEDCOM definition');
	$text=i18n::translate('A quote from the Introduction to the GEDCOM 5.5.1 Standard:<div class="list_value_wrap">GEDCOM was developed by the Family History Department of The Church of Jesus Christ of Latter-day Saints (LDS Church) to provide a flexible, uniform format for exchanging computerized genealogical data.&nbsp; GEDCOM is an acronym for <i><b>GE</b></i>nealogical <i><b>D</b></i>ata <i><b>Com</b></i>munication.&nbsp; Its purpose is to foster the sharing of genealogical information and the development of a wide range of inter-operable software products to assist genealogists, historians, and other researchers.</div><br />A copy of the GEDCOM 5.5.1 <u>draft</u> Standard, to which <b>webtrees</b> adheres, can be downloaded in PDF format here:&nbsp; <a href="http://www.phpgedview.net/ged551-5.pdf" target="_blank">GEDCOM 5.5.1 Standard</a>  This Standard is only available in English.<br /><br />The GEDCOM file contains all the information about the family. All facts, dates, events, etc. are stored here. GEDCOM files have to follow strict rules because they must be exchangeable between many programs, independent of platforms or operating systems.');
	break;

case 'def_gramps':
	$title=i18n::translate('GRAMPS definition');
	$text=i18n::translate('A quote from GRAMPS Project: <div class="list_value_wrap">GRAMPS helps you track your family tree. It allows you to store, edit, and research genealogical data. GRAMPS attempts to provide all of the common capabilities of other genealogical programs, but, more importantly, to provide an additional capability of integration not common to these programs. This is the ability to input any bits and pieces of information directly into GRAMPS and rearrange/manipulate any/all data events in the entire data base (in any order or sequence) to assist the user in doing research, analysis and correlation with the potential of filling relationship gaps.</div><br />A copy of the GRAMPS XML format v1.1.0 <a href="http://www.gramps-project.org/xml/1.1.0/" target="_blank">can be found here</a> in both RELAX NG Schema format and DTD format.<br /><br />For more information about the GRAMPS Project visit <a href="http://gramps-project.org/" target="_blank">http://gramps-project.org/</a>');
	break;

case 'def':
	$title=i18n::translate('Definitions');
	$text=i18n::translate('Here are some explanations of terms used in this Help text:<ul><li><a href="?help=def_gedcom"><b>GEDCOM</b></a><br /></li><li><a href="?help=def_gedcom_date"><b>Dates</b></a></li><li><a href="?help=def_pdf_format"><b>PDF file format</b></a></li><li><a href="?help=def_pgv"><b>webtrees</b></a></li><li><a href="?help=def_portal"><b>Portal</b></a></li><li><a href="?help=def_theme"><b>Theme</b></a></li></ul>');
	break;

case 'def_pdf_format':
	$title=i18n::translate('PDF definition');
	$text=i18n::translate('The <b>webtrees</b> Reporting Engine produces downloadable reports in Adobe&reg; PDF format.  The GEDCOM 5.5.1 Standard specification, mentioned elsewhere in this Help file, is also downloadable as a PDF file.  PDF is an acronym for <b>P</b>ortable <b>D</b>ocument <b>F</b>ormat.<br /><br />PDF files are not viewable or printable by the standard software on your PC.  If you already have Acrobat Reader installed (it\'s often packaged with other softwares), you do not need to replace or upgrade it to deal with report files produced by <b>webtrees</b>.<br /><br />Acrobat Reader, the viewing and printing program for these files, is available free of charge from Adobe Systems Inc.  The free Adobe&reg; Acrobat Reader can be downloaded from the <a href="http://www.adobe.com/products/acrobat/readstep2.html" target="_blank"><b>Adobe Systems Inc.</b></a> web site.  You may find copies of "Acrobat Reader" available for download from other Internet sites, but we strongly advise you to trust <u>only</u> the Adobe Systems Inc. site.<br /><br />Acrobat Reader is available for many different systems, including Microsoft&reg; Windows and Apple&reg; Macintosh, in many languages other than English.  If you have a Windows 95 system, be sure to download Acrobat Reader version 5.0.5.  Versions more recent than this will not install correctly on Windows 95 systems.<br /><br /><a href="http://www.adobe.com/products/acrobat/readstep2.html" target="_blank"><b>Download Adobe Reader here</b></a>');
	break;

case 'def_PGV':
	$title=i18n::translate('PGV definition');
	$text=i18n::translate('<b>PhpGedView</b> does not just put static pages on the Web; it is dynamic and can be customized in many ways.<br /><br /><b>PhpGedView</b> was created by John Finlay to view GEDCOM files online.  John started developing the program on his own.  An international team of developers and translators has since joined him and is working to improve the program.  Among the more significant features that have been added or improved in the program are its extensive support of languages other than English, and the ability to add and edit events online.');
	break;

case 'def_portal':
	$title=i18n::translate('Portal definition');
	$text=i18n::translate('This site\'s Portal is like the lobby of a restaurant or a public library. It is the place where you enter, but you can also find important information like explanations, menus etc.');
	break;

case 'def_theme':
	$title=i18n::translate('Theme definition');
	$text=i18n::translate('This site can have different "appearances", called Themes.<br /><br />The site administrator chooses a default Theme, which everybody who enters this site will initially see. When the administrator has enabled this feature, all users can select their own Themes.  <b>webtrees</b> remembers the last selected Theme for each logged-in user, so that that user will automatically see that Theme the next time he logs in.  Themes can be used as a way to distinguish between different databases on the same site.  Each database can have a different default Theme.');
	break;

case 'default_gedcom':
	$title=i18n::translate('Default GEDCOM');
	$text=i18n::translate('If you have more than one genealogical database, you can set here which of them will be the default.<br /><br />This default will be shown to all visitors and users who have not yet logged in.<br /><br />Users who can edit their account settings can override this default.  In that case, the user\'s preferred database will be shown after login.');
	break;

case 'delete_faq_item':
	$title=i18n::translate('Delete FAQ item');
	$text=i18n::translate('This option will let you delete an item from the FAQ page');
	break;

case 'delete_gedcom':
	$title=i18n::translate('Delete GEDCOM');
	$text=i18n::translate('<b>webtrees</b> creates its database from a GEDCOM file that was previously uploaded. When you select <b>Delete</b>, that section of the database will be erased.  You have to confirm your Delete request.<br /><br />Unless you have deliberately removed it outside <b>webtrees</b>, the original GEDCOM file will remain in the directory into which it was uploaded.  If you later want to work with that GEDCOM file again, you don\'t have to upload it again. You can choose the <b>Add GEDCOM</b> function.');
	break;

case 'delete_name':
	$title=i18n::translate('Delete name');
	$text=i18n::translate('<b>Edit name</b><br />When you click this link, another window will open.  There you can edit the name of the person.  Just type the changes into the boxes and click the button, close the window, and that\'s it.<br /><br /><b>DELETE NAME</b><br />By clicking this option you will mark this Name to be deleted from the database.  Note that deleting the name is completely different from deleting the individual.  Deleting the name just removes the name from the person. The person will <u>not</u> be deleted.  If it is an AKA that you want to delete, the person still has his other names.  If it is the <u>only</u> name that you want to remove, the person will still not be deleted, but will now be recorded as <b>(unknown)</b>.  The person will also not be disconnected from any other to relatives, sources, notes, etc.<br /><br />How does it work?<br />You will be asked to confirm your deletion request.  If you decide to continue, it can take a little time before you see a message that the name is deleted.<br /><br />When you continue with your visit, you will notice that the name is still visible and can be used as if the deletion had not occurred.<br /><br /><b>This is <u>not</u> an error.</b>  The site admin will get a message that a change has been made to the database, and that you removed the name.<br />The administrator can accept or reject your change. Only after the administrator has accepted your change will the deletion actually occur <u>irreversibly</u>.  If there is any doubt about your change, the administrator will contact you.');
	break;

case 'delete_person':
	$title=i18n::translate('Delete individual');
	$text=i18n::translate('When you click this option, you will mark this individual to be deleted from the database.<br /><br />What does that mean?<br />Let\'s suppose you have a good reason to remove this person from the database. You click the link.  You will be asked to confirm your deletion request.  If you decide to continue, it can take a little time before you see a message that the individual is deleted.<br /><br />When you continue with your visit, you will notice that the person is still visible and can be used as if the deletion had not occurred.<br /><br /><b>This is <u>not</u> an error.</b>  The site admin will get a message that a change has been made to the database, and that you removed the individual.<br />The administrator can accept or reject your change. Only after the administrator has accepted your change will the deletion actually occur <u>irreversibly</u>.  If there is any doubt about your change, the administrator will contact you.');
	break;

case 'delete_repo':
	$title=i18n::translate('Delete repository');
	$text=i18n::translate('When you click this option you mark this Repository to be deleted from the database.<br /><br />What does that mean?<br />Let\'s suppose you have a good reason to remove this Repository from the database. You click the link.  You will be asked to confirm your deletion request.  If you decide to continue, it can take a little time before you see a message that the Repository is deleted.<br /><br />When you continue with your visit, you will notice that the Repository is still visible and can be used as if the deletion had not occurred.<br /><br /><b>This is <u>not</u> an error.</b>  The site admin will get a message that a change has been made to the database, and that you removed the Repository.<br />The administrator can accept or reject your change. Only after the administrator has accepted your change will the deletion actually occur <u>irreversibly</u>.  If there is any doubt about your change, the administrator will contact you.');
	break;

case 'delete_source':
	$title=i18n::translate('Delete source');
	$text=i18n::translate('When you click this option, you will mark this Source to be deleted from the database.<br /><br />What does that mean?<br />Let\'s suppose you have a good reason to remove this source from the database. You click the link.  You will be asked to confirm your deletion request.  If you decide to continue, it can take a little time before you see a message that the source is deleted.<br /><br />When you continue with your visit, you will notice that the source is still visible and can be used as if the deletion had not occurred.<br /><br /><b>This is <u>not</u> an error.</b>  The site admin will get a message that a change has been made to the database, and that you removed the Source.<br />The administrator can accept or reject your change. Only after the administrator has accepted your change will the deletion actually occur <u>irreversibly</u>.  If there is any doubt about your change, the administrator will contact you.');
	break;

case 'desc_generations':
	$title=i18n::translate('Number of generations');
	$text=i18n::translate('Here you can set the number of generations to display on this page.<br /><br />The right number for you depends of the size of your screen and whether you show details or not.  Processing time will increase as you increase the number of generations.');
	break;

case 'desc_rootid':
	$title=i18n::translate('Root individual');
	$text=i18n::translate('If you want to display a chart with a new starting (root) person, the ID of that new starting person is typed here.<br /><br />If you don\'t know the ID of that person, use the <b>Find ID</b> link.<br /><br /><b>ID NUMBER</b><br />The ID numbers used inside <b>webtrees</b> are <u>not</u> the identification numbers issued by various governments (driving permit or passport numbers, for instance).  The ID number referred to here is simply a number used within the database to uniquely identify each individual; it was assigned by the ancestry program that created the GEDCOM file which was imported into <b>webtrees</b>.');
	break;

case 'detected_ansi2utf':
	$title=i18n::translate('ANSI character set');
	$text=i18n::translate('The GEDCOM file being validated now is encoded in the ANSI character set.  You are strongly advised to convert the file\'s encoding to UTF-8.<br /><br /><br />~CONVERT ANSI TO UTF-8~<br /><br />To ensure that the information in your input GEDCOM files is processed and displayed correctly, these files should be encoded in UTF-8.<br /><br />Some of the more modern genealogy programs can export their data to a GEDCOM file in UTF-8 encoding.  Older programs often don\'t have this capability.  If your program does not offer you this option, <b>webtrees</b> can convert the file for you.<br /><br />When <b>webtrees</b> validates the input file, it will detect the file\'s encoding and advise you accordingly.');
	break;

case 'detected_date':
	$title=i18n::translate('Date format will be changed');
	$text=i18n::translate('The date format that is standard for <b>webtrees</b> and also according to the GEDCOM 5.5.1 Standard is <b>DD&nbsp;MMM&nbsp;YYYY</b> (e.g. 01&nbsp;JAN&nbsp;2004)<br /><br />If, after your GEDCOM file has been validated, you see a message that a wrong date format has been detected, <b>webtrees</b> will convert the incorrectly formatted dates as prescribed by the Standard.<br /><br />You have, however, the option to choose either "<b>day</b> before month" (DD&nbsp;MMM&nbsp;YYYY), or "<b>month</b> before day" (MMM&nbsp;DD&nbsp;YYYY).<br /><br />We recommend that you use the first format (day before month).');
	break;

case 'dictionary_sort':
	$title=i18n::translate('Use dictionary rules while sorting');
	$text=i18n::translate('This option controls how characters with diacritic marks are handled when sorting lists of names and titles.<br /><br />When set to <b>Yes</b>, all characters with diacritic marks are treated as if they did not have any marks.  Diacritic marks are considered only when the two words being considered are otherwise identical.  When set to <b>No</b>, all letters are distinct, regardless of the presence or absence of diacritic marks.');
	break;

case 'download_gedcom':
	$title=i18n::translate('Download GEDCOM');
	$text=i18n::translate('From this page you can download your genealogical database in GEDCOM format.  You may want to import the data into another genealogical program, or you may want to share its information with others.<br /><br />~CONVERT FROM UTF-8 TO ANSI~<br /><br />For optimal display on the Internet, <b>webtrees</b> uses the UTF-8 character set.  Some programs, Family Tree Maker for example, do not support importing GEDCOM files encoded in UTF-8.  Checking this box will convert the file from <b>UTF-8</b> to <b>ANSI (ISO-8859-1)</b>.<br /><br />The format you need depends on the program you use to work with your downloaded GEDCOM file.  If you aren\'t sure, consult the documentation of that program.<br /><br />Note that for special characters to remain unchanged, you will need to keep the file in UTF-8 and convert it to your program\'s method for handling these special characters by some other means.  Consult your program\'s manufacturer or author.<br /><br />This <a href=\'http://en.wikipedia.org/wiki/UTF-8\' target=\'_blank\' title=\'Wikipedia article\'><b>Wikipedia article</b></a> contains comprehensive information and links about UTF-8.<br /><br /><br /><br />~REMOVE CUSTOM PGV TAGS~<br /><br />Checking this option will remove any custom tags that may have been added to the records by <b>webtrees</b>.<br /><br />Custom tags used by <b>webtrees</b> include the <b>_PGVU</b> tag which identifies the user who changed a record online and the <b>_THUM</b> tag which tells <b>webtrees</b> that the image should be used as a thumbnail.<br /><br />Custom tags may cause errors when importing the downloaded GEDCOM to another genealogy application.<br /><br /><br /><br />~DOWNLOAD GEDCOM AS ZIP FILE~<br /><br />When you check this option, a copy of the GEDCOM file will be compressed into ZIP format before the download begins. This will reduce its size considerably, but you will need to use a compatible Unzip program (WinZIP, for example) to decompress the transmitted GEDCOM file before you can use it.<br /><br />This is a useful option for downloading large GEDCOM files.  There is a risk that the download time for the uncompressed file may exceed the maximum allowed execution time, resulting in incompletely downloaded files.  The ZIP option should reduce the download time by 75 percent.');
	break;

case 'download_zipped':
	$title=i18n::translate('Download ZIP file');
	$text=i18n::translate('When you check this option, a copy of the GEDCOM file will be compressed into ZIP format before the download begins. This will reduce its size considerably, but you will need to use a compatible Unzip program (WinZIP, for example) to decompress the transmitted GEDCOM file before you can use it.<br /><br />This is a useful option for downloading large GEDCOM files.  There is a risk that the download time for the uncompressed file may exceed the maximum allowed execution time, resulting in incompletely downloaded files.  The ZIP option should reduce the download time by 75 percent.');
	break;

case 'edit_add_ASSO':
	$title=i18n::translate('Add a new associate');
	$text=i18n::translate('Add a new Associate allows you to link a fact with an associated person in the site.  This is one way in which you might record that someone was the Godfather of another person.');
	break;

case 'edit_add_GEDFact_ASSISTED':
	$title=i18n::translate('GEDFact shared note assistant');
	$text=i18n::translate('Clicking the "+" icon will open the GEDFact Shared Note Assistant window.<br />Specific help will be found there.<br /><br />When you click the "Save" button, the ID of the Shared Note will be pasted here.');
	break;

case 'edit_add_NOTE':
	$title=i18n::translate('Add a new note');
	$text=i18n::translate('This section allows you to add a new Note to the fact that you are currently editing.  Notes are free-form text and will appear in the Fact Details section of the page.');
	break;

case 'edit_add_SHARED_NOTE':
	$title=i18n::translate('Add a new shared note');
	$text=i18n::translate('Shared notes, like regular notes, are free-form text.  Unlike regular notes, each shared note can be linked to more than one person, family, source, or fact.<br /><br />By clicking the appropriate icon, you can establish a link to an existing shared note or create a new shared note and at the same time link to it.  If a link to an existing shared note has already been established, you can also edit that note\'s contents.<br /><ul><li><b>Link to an existing shared note</b><div style="padding-left:20px;">If you already know the ID number of the desired shared note, you can enter that number directly into the field.<br /><br />When you click the <b>Find Shared Note</b> icon, you will be able to search the text of all existing shared notes and then choose one of them.  The ID number of the chosen note will be entered into the field automatically.<br /><br />You must click the <b>Add</b> button to update the original record.</div><br /></li><li><b>Create a new shared note</b><div style="padding-left:20px;">When you click the <b>Create a new Shared Note</b> icon, a new window will open.  You can enter the text of the new note as you wish.  As with regular notes, you can enter URLs.<br /><br />When you click the <b>Save</b> button, you will see a message with the ID number of the newly created shared note.  You should click on this message to close the editing window and also copy that new ID number directly into the ID number field.  If you just close the window, the newly created ID number will not be copied automatically.<br /><br />You must click the <b>Add</b> button to update the original record.</div><br /></li><li><b>Edit an existing shared note</b><div style="padding-left:20px;">When you click the <b>Edit Shared Note</b> icon, a new window will open.  You can change the text of the existing shared note as you wish.  As with regular notes, you can enter URLs.<br /><br />When you click the <b>Save</b> button, the text of the shared note will be updated.  You can close the window and then click the <b>Save</b> button again.<br /><br />When you change the text of a shared note, your change will be reflected in all places to which that shared note is currently linked.  New links that you establish after having made your change will also use the updated text.</div></li></ul>');
	break;

case 'edit_add_SOUR':
	$title=i18n::translate('Add a new source citation');
	$text=i18n::translate('This section allows you to add a new source citation to the fact that you are currently editing.<br /><br />In the Source field you enter the ID for the source.  Click the <b>Create a new source</b> link if you need to enter a new source.  In the Citation Details field you would enter the page number or other information that might help someone find the information in the source.  In the Text field you would enter the text transcription from the source.');
	break;

case 'edit_add_child':
	$title=i18n::translate('Add a new child');
	$text=i18n::translate('With this page you can add a new child to the selected family.  Fill out the name of the child and the birth and death information if it is known.  If you don\'t know some information leave it blank.<br /><br />To add other facts besides birth and death, first add the new child to the database by saving the changes.  Then click on the child\'s name in the updated Family page or Close Relatives tab to view the child\'s Individual Information page.  From the Individual Information page you can add more detailed information.');
	break;

case 'edit_add_parent':
	$title=i18n::translate('Add a new parent');
	$text=i18n::translate('With this page you can add a new mother or father to the selected person.  Fill out the new person\'s name and the birth and death information if it is known.  If you don\'t know some information, leave it blank.<br /><br />To add other facts besides birth and death, first add the new person to the database by saving the changes.  Then click on the person\'s name in the updated Family page or Close Relatives tab to view the person\'s Individual Information page.  From the Individual Information page you can add more detailed information.');
	break;

case 'edit_add_spouse':
	$title=i18n::translate('Add a new spouse');
	$text=i18n::translate('With this page you can add a new husband or wife to the selected person.  Fill out the new person\'s name and the birth and death information if it is known.  If you don\'t know some information leave it blank.<br /><br />To add other facts besides birth and death, first add the new person to the database by saving the changes.  Then click on the person\'s name in the updated Family page or Close Relatives tab to view the person\'s Individual Information page.  From the Individual Information page you can add more detailed information.');
	break;

case 'edit_add_unlinked_note':
	$title=i18n::translate('Add an unlinked shared note');
	$text=i18n::translate('Use this link to add a new shared note to your database without linking the note to any record.<br /><br />The new note will appear in the Shared Note list, but will not appear on any charts or anywhere else in the program until it is linked to an individual, family or event.');
	break;

case 'edit_add_unlinked_person':
	$title=i18n::translate('Add an unlinked person');
	$text=i18n::translate('Use this form to add an unlinked person.<br /><br />When you add an unlinked person to your family tree, the person will not be linked to any other people until you link them.  Later, you can link people together from the Close Relatives tab on the Individual Information page.');
	break;

case 'edit_add_unlinked_source':
	$title=i18n::translate('Add an unlinked source');
	$text=i18n::translate('Use this link to add a new source to your database without linking the source to a source citation in another record.  The new source will appear in the source list, but will not appear on any charts or anywhere else in the program until it is linked up to a source citation.');
	break;

case 'edit_birth':
	$title=i18n::translate('Add birth');
	$text=i18n::translate('This area allows you to enter the birth information.  First enter the date when the person was born in the standard date format for genealogy (1 JAN 2004).  You can click on the Calendar icon for help selecting a date.  Then enter the place where the person was born.  You can use the <b>Find Place</b> link to select a place that already exists in the database.');
	break;

case 'edit_config_gedcom':
	$title=i18n::translate('Configure GEDCOM');
	$text=i18n::translate('Every genealogical database used with <b>webtrees</b> has its own <b>Configuration file</b>.<br /><br />On this form you configure many options such as database title, language, calendar format, email options, logging of database searches, HTML META headers, removal of surnames from the database\'s Frequent Surnames list, etc.');
	break;

case 'edit_death':
	$title=i18n::translate('Add death');
	$text=i18n::translate('This area allows you to enter Death information.  First enter the date when the person died in the standard date format for genealogy (1 JAN 2004).  You can click on the Calendar icon for help selecting a date.  Then enter the place where the person died.  You can use the <b>Find Place</b> link to select a place that already exists in the database.');
	break;

case 'edit_edit_raw':
	$title=i18n::translate('Edit raw GEDCOM record');
	$text=i18n::translate('This page allows you to edit the raw GEDCOM record.  You should use this page with caution; it requires a good understanding of the GEDCOM 5.5.1 Standard.  For more information on the GEDCOM 5.5.1 Standard, refer to Help topic <b>GEDCOM file</b>.<br /><br /><b>webtrees</b> provides many ways to add and edit information, but there could be occasions when you may want to edit the raw GEDCOM structure.  When possible, you should use the provided forms for adding information, but when that is impossible, you can use this form.  Upon submitting the form, your information will be checked for basic conformance to the Standard and the CHAN record will be updated.');
	break;

case 'edit_faq_item':
	$title=i18n::translate('Edit FAQ item');
	$text=i18n::translate('This option will let you edit an item on the FAQ page.');
	break;
	
case 'edit_gedcoms':
// duplicate text. see 'editgedcoms.php'
	$title=i18n::translate('GEDCOM administration');
	$text=i18n::translate('The GEDCOM Administration page is the control center for administering all of your genealogical databases.<br /><br /><b>Current GEDCOMs</b><br />At the head of the <b>Current GEDCOMs</b> table, you see an action bar with four links.<ul><li>Add GEDCOM</li><li>Upload GEDCOM</li><li>Create a new GEDCOM</li><li>Return to the Admin menu</li></ul>In the <b>Current GEDCOMs</b> table each genealogical database is listed separately, and you have the following options for each of them:<ul><li>Import</li><li>Delete</li><li>Download</li><li>Edit configuration</li><li>Edit privacy</li><li>SearchLog files</li></ul>Edit privacy appears here because every GEDCOM has its own privacy file.<br /><br />Each line in this table should be self-explanatory.  <b>webtrees</b> can be configured to log all database searches.  The SearchLog files can be inspected through links found on this page.');
	break;

case 'edit_given_name':
	$title=i18n::translate('Add given name');
	$text=i18n::translate('In this field you should enter the given names for the person.  As an example, in the name "John Robert Finlay", the given names that should be entered here are "John Robert"');
	break;

case 'edit_name':
	$title=i18n::translate('Edit name');
	$text=i18n::translate('This is the most important field in a person\'s Name record.<br /><br />This field should be filled automatically as the other fields are filled in, but it is provided so that you can edit the information according to your personal preference.<br /><br />The name in this field should be entered according to the GEDCOM 5.5.1 standards with the surname surrounded by forward slashes "/".  As an example, the name "John Robert Finlay Jr." should be entered like this: "John Robert /Finlay/ Jr.".');
	break;

case 'edit_privacy':
	$title=i18n::translate('Edit GEDCOM privacy settings');
	$text=i18n::translate('On this page you can make all the Privacy settings for the selected GEDCOM.<br /><br />You can check under the page title to see that you are editing the correct privacy file.  It is displayed like this: (path/nameofyourgedcom_priv.php)<br /><br />If you need more settings, you can make changes to the privacy file manually. You can read more about this on the <b>webtrees</b> web site.');
	break;

case 'edit_raw_gedcom':
	$title=i18n::translate('Edit raw gedcom');
	$text=i18n::translate('When you click this link, a new window will open containing the raw GEDCOM data of the details on this page.<br /><br />Here you can edit the GEDCOM data directly. Be sure to enter valid GEDCOM 5.5.1 data, as no further validity checks will be done.  The changed or added data will be displayed in <b>webtrees</b> as "changes", and have to be accepted by a user with Accept rights.');
	break;

case 'edit_sex':
	$title=i18n::translate('Edit gender');
	$text=i18n::translate('Choose the appropriate gender from the drop-down list.  The <b>unknown</b> option indicates that the gender is unknown.');
	break;

case 'edit_SOUR_EVEN':
	$title=i18n::translate('Edit source event');
	$text=i18n::translate('Each source records specific events, generally for a given date range and for a place jurisdiction.  For example a Census records census events and church records record birth, marriage, and death events.<br /><br />Select the events that are recorded by this source from the list of events provided. The date should be specified in a range format such as <i>FROM 1900 TO 1910</i>. The place jurisdiction is the name of the lowest jurisdiction that encompasses all lower-level places named in this source. For example, "Oneida, Idaho, USA" would be used as a source jurisdiction place for events occurring in the various towns within Oneida County. "Idaho, USA" would be the source jurisdiction place if the events recorded took place not only in Oneida County but also in other counties in Idaho.');
	break;

case 'edit_suffix':
	$title=i18n::translate('Edit suffix');
	$text=i18n::translate('In this optional field you should enter the name suffix for the person.  Examples of name suffixes are "Sr.", "Jr.", and "III".');
	break;

case 'edit_surname':
	$title=i18n::translate('Edit surname');
	$text=i18n::translate('In this field you should enter the surname for the person.  As an example, in the name "John Robert Finlay", the surname that should be entered here is "Finlay"<br /><br />Individuals with multiple surnames, common in Spain and Portugal, should separate the surnames with a comma.  This indicates that the person is to be listed under each of the names.  For example, the surname "Cortes,Vega" will be listed under both <b>C</b> and <b>V</b>, whereas the surname "Cortes Vega" will only be listed under <b>C</b>.');
	break;
	
case 'edit_TIME':
	$title=i18n::translate('Time');
	$text=i18n::translate('Enter the time for this event in 24-hour format with leading zeroes. Midnight is 00:00. Examples: 04:50 13:00 20:30.<br /><br />.');
	break;
	
case 'editlang':
	$title=i18n::translate('Edit');
	$text=i18n::translate('Edit message from language file.');
	break;

case 'edituser_change_lang':
	$title=i18n::translate('Language selector');
	$text=i18n::translate('Here you can change the language in which <b>webtrees</b> will display all its pages and messages after you have logged in.<br /><br />When you first access the site, <b>webtrees</b> assumes that you want to see everything in the language configured as the Preferred Language in your browser.  If that assumption is incorrect, you would override it here.  For example, your browser might be set to English because that is the most prevalent language on the Internet.  However, for genealogical purposes, you would prefer to see everything in Finnish or Hebrew.  Here\'s where you do that.<br /><br />The administrator controls what language choices are available to you.  If your preference isn\'t listed, you need to contact the administrator.<br /><br />Please remember that <b>webtrees</b> is very much a project staffed by an international team of unpaid volunteers.  Experts come and go.  Consequently, support for languages other than English is sometimes not as good as it should be.<br /><br />If you see something that has not been translated, has been translated incorrectly, or could be phrased better, let your administrator know.  The administrator will know how to get in touch with the <b>webtrees</b> developer team to have your concerns addressed.  Better still, volunteer some of your time.  We can use the help.');
	break;

case 'edituser_conf_password':
	$title=i18n::translate('Confirm password');
	$text=i18n::translate('If you have changed your password, you need to confirm it as well.  This is just to make sure that you did not make a typing error in the password field.<br /><br />If the password and its confirmation are not identical, you will get a suitable error message.  You will have to re-type both the original password and its confirmation.');
	break;

case 'edituser_contact_meth':
	$title=i18n::translate('Preferred contact method');
	$text=i18n::translate('<b>webtrees</b> has several different contact methods.  The administrator determines which method will be used to contact him.  You have control over the method to be used to contact <u>you</u>.  Depending on site configuration, some of the listed methods may not be available to you.');
	break;

case 'edituser_email':
	$title=i18n::translate('Email address');
	$text=i18n::translate('Your correct email address is important to us to keep in touch with you.<br /><br />If you get a new email address, as usually happens when you change your Internet provider, please do not forget to change the address here as well.  You won\'t get a confirmation message from this site when you change this address, but any future messages directed to you will go this new address.');
	break;

case 'edituser_gedcomid':
	$title=i18n::translate('GEDCOM individual record ID');
	$text=i18n::translate('This is an identification number that links you to your own data in the database.<br /><br />You cannot change this ID; it\'s set by the administrator.  If you think that this ID is not correct, you should contact the administrator to have it changed.');
	break;

case 'edituser_realname':
	$title=i18n::translate('Real name');
	$text=i18n::translate('In this box you can change your real name.  This is the name that other users see when you are logged in.<br /><br />Although the choice of what to put into this field is yours, you should inform the administrator when you change it.  When others see an unknown person on-line, they might wonder and ask questions.  The admin can find out without having received your notice, but you should save him that unnecessary work.');
	break;

case 'edituser_my_account':
	$title=i18n::translate('My account');
	$text=i18n::translate('Here you can change your settings and preferences.<br /><br />You can change your user name, full name, password, language, email address, theme of the site, and preferred contact method.<br /><br />You cannot change the GEDCOM INDI record ID; that has to be done by an administrator.');
	break;

case 'edituser_password':
	$title=i18n::translate('Password');
	$text=i18n::translate('It is a good practice to change your password regularly.  You have to keep in mind that anyone who knows your user name and your password will have access to your data.<br /><br />Make the password at least 6 characters long, the longer the better. You may use uppercase and lower case letters with or without diacritical marks, numbers, dash (-), and underscore (_). Do <u>not</u> use punctuation marks or spaces.  Use a combination of upper and lower case, numbers, and other characters. For example: <b>5Z_q$P4=r9</b>.<br /><br />Like the user name, the password is <u>case sensitive</u>.  That means that <b>Secret.Password!#13</b> is not the same as <b>secret.password!#13</b> or <b>SECRET.PASSWORD!#13</b>.');
	break;

case 'edituser_rootid':
	$title=i18n::translate('Pedigree chart root ID');
	$text=i18n::translate('This is the starting (Root) person of all your charts.<br /><br />If, for example, you were to click the link to the Pedigree, you would see this root person in the leftmost box.  This root person does not have to be you; you can start with any person (your grandfather or your mother\'s aunt, for instance), as long you have the rights to see that person.<br /><br />The changes the default Root person for most charts.  You can change the Root person on many charts, but that is just for that page at that particular invocation.');
	break;

case 'edituser_user_default_tab':
	$title=i18n::translate('Default tab setting');
	$text=i18n::translate('This setting allows you to specify which tab is opened automatically when you access the Individual Information page.');
	break;

case 'edituser_user_theme':
	$title=i18n::translate('Theme');
	$text=i18n::translate('This site can have several different looks or appearances.  Other programs may call them "skins", but here they\'re "themes".<br /><br />Every theme will display the same data, but its presentation or even its location on the screen may vary.  This is like putting a picture into a different frame and hanging it in a different room as well. The picture does not change, but the way you look at it is completely different.<br /><br />Just give it a try. Set it to another theme. Look at it, try another. Change back to the one that suits you the best. Whenever you log in, you will see the theme you last used; you don\'t even have to get to this configuration page to change your preferred theme.');
	break;

case 'edituser_username':
	$title=i18n::translate('Username');
	$text=i18n::translate('In this box you can change your user name.  If you no longer like your user name or if have other reasons to change it, you can do so using this form.<br /><br />The username is <u>case sensitive</u>. That means that <b>John</b> is not the same as <b>john</b> or <b>JOHN</b>.<br /><br />You should <u>only</u> use characters from the alphabets that <b>webtrees</b> supports.  You may use uppercase and lower case letters with or without diacritical marks, numbers, dash (-), and underscore (_). Do <u>not</u> use punctuation marks or spaces.');
	break;

case 'empty_lines_detected':
	$title=i18n::translate('Empty lines were detected in your GEDCOM file.    On cleanup, these empty lines will be removed.');
	$text=i18n::translate('<b>webtrees</b> has detected that there are empty lines in your input file. These lines may cause errors and will be removed from the file before it is imported.');
	break;

case 'fambook_descent':
	$title=i18n::translate('Descendant generations');
	$text=i18n::translate('This value determines the number of descendant generations of the root person that will be printed in Hourglass format.');
	break;

case 'fan_style':
	$title=i18n::translate('Fan style');
	$text=i18n::translate('This option controls the appearance of the diagram.<ul><li><b>1/2</b><br />Half circle 180&deg; diagram</li><li><b>3/4</b><br />Three-quarter 270&deg; diagram, sometimes called <i>Angel wing</i></li><li><b>4/4</b><br />Full circle 360&deg; diagram</li></ul>');
	break;

case 'fan_width':
	$title=i18n::translate('Width');
	$text=i18n::translate('Here you can change the diagram width from 50 percent to 300 percent.  At 100 percent the output image is about 640 pixels wide.');
	break;

case 'file_to_edit':
	$title=i18n::translate('Language file type to edit');
	$text=i18n::translate('<b>webtrees</b> has implemented support for many different languages.  This has been achieved by keeping all text that is visible to users in files completely separate from the main program.  There is a set of eight files for each supported language, and the various texts have been separated into one of these files according to function.  <b>Not all language files need to be present.</b>  When a given text is not yet available in translated form, <b>webtrees</b> will always use the English version.<br /><br />The files in each language set are:<br /><ul><li><b><i>admin.xx.php</i></b>&nbsp;&nbsp;This file contains terms and common expressions for use during the administration of <b>webtrees</b> and the genealogical databases.<br /><br /></li><li><b><i>configure_help.xx.php</i></b>&nbsp;&nbsp;This file contains Help text for use during configuration of <b>webtrees</b>.  The Help text is not intended to be viewed by ordinary users.<br /><br /></li><li><b><i>countries.xx.php</i></b>&nbsp;&nbsp;This is a list of country names, taken from the Web site of the Statistics Division, United Nations Department of Economic and Social Affairs.  This is the relevant <a href="http://unstats.un.org/unsd/methods/m49/m49alpha.htm" target="_blank"><b>link</b></a> to the English list.  The list is available in either English or French.<br /><br /></li><li><b><i>editor.xx.php</i></b>&nbsp;&nbsp;This file contains terms and common expressions for use during the editing of entries in the genealogical databases.<br /><br /></li><li><b><i>facts.xx.php</i></b>&nbsp;&nbsp;This file contains the textual equivalents of the GEDCOM Fact codes found in the GEDCOM 5.5.1 Standard.  It also contains additional Fact codes not found in the Standard but used by various genealogy programs.<br /><br />An English copy of the <a href="http://www.phpgedview.net/ged551-5.pdf" target="_blank"><b>GEDCOM 5.5.1 Standard</b></a> can be downloaded in PDF (Portable Document Format).<br /><br /></li><li><b><i>faqlist.xx.php</i></b>&nbsp;&nbsp;This file is a set of <b>f</b>requently <b>a</b>sked <b>q</b>uestions that have been collected by the <b>webtrees</b> development team.  Each FAQ has two entries in this file.  One entry is the FAQ heading (usually the question), and the other is the FAQ body (usually the answer).  Replacements for the <b><i>faqlist.xx.php</i></b> files, which are updated frequently, may be downloaded from the <b>webtrees</b> home site.<br /><br />The administrator can use the FAQs in this file to build an FAQ list that is specific to his site.<br /><br /></li><li><b><i>help_text.xx.php</i></b>&nbsp;&nbsp;This file contains Help text for ordinary users.  Some Help topics in this file address the needs of administrators, and are hidden from users who do not have Admin rights.<br /><br /></li><li><b><i>lang.xx.php</i></b>&nbsp;&nbsp;Many terms and common expressions are found in this file.</li></ul><br /><b>webtrees</b> also supports an optional ninth language file, <b><i>extra.xx.php</i></b>.  This file is always loaded after all the others and provides a means whereby a site administrator can override or alter any standard text in the selected language.  It can also be used to provide a title for the genealogical databases that varies according to the currently active language.<br /><br />The contents of this additional file are completely up to the site administrator;  this file will <b>never</b> be distributed with any version of <b>webtrees</b>.  The administrator should never make changes to the standard language files;  all local changes should be concentrated in this optional file.');
	break;

case 'file_type':
	$title=i18n::translate('File type');
	$text=i18n::translate('Choose the format in which the database export is to be created.  Your choice depends on the requirements and capabilities of the program into which you intend to import the newly downloaded file.  You can choose:<ul><li>~GEDCOM file~<br />A quote from the Introduction to the GEDCOM 5.5.1 Standard:<div class="list_value_wrap">GEDCOM was developed by the Family History Department of The Church of Jesus Christ of Latter-day Saints (LDS Church) to provide a flexible, uniform format for exchanging computerized genealogical data.&nbsp; GEDCOM is an acronym for <i><b>GE</b></i>nealogical <i><b>D</b></i>ata <i><b>Com</b></i>munication.&nbsp; Its purpose is to foster the sharing of genealogical information and the development of a wide range of inter-operable software products to assist genealogists, historians, and other researchers.</div><br />A copy of the GEDCOM 5.5.1 <u>draft</u> Standard, to which <b>webtrees</b> adheres, can be downloaded in PDF format here:&nbsp; <a href="http://www.phpgedview.net/ged551-5.pdf" target="_blank">GEDCOM 5.5.1 Standard</a>  This Standard is only available in English.<br /><br />The GEDCOM file contains all the information about the family. All facts, dates, events, etc. are stored here. GEDCOM files have to follow strict rules because they must be exchangeable between many programs, independent of platforms or operating systems.<br /><br /></li><li>~GRAMPS XML Database file~<br />A quote from GRAMPS Project: <div class="list_value_wrap">GRAMPS helps you track your family tree. It allows you to store, edit, and research genealogical data. GRAMPS attempts to provide all of the common capabilities of other genealogical programs, but, more importantly, to provide an additional capability of integration not common to these programs. This is the ability to input any bits and pieces of information directly into GRAMPS and rearrange/manipulate any/all data events in the entire data base (in any order or sequence) to assist the user in doing research, analysis and correlation with the potential of filling relationship gaps.</div><br />A copy of the GRAMPS XML format v1.1.0 <a href="http://www.gramps-project.org/xml/1.1.0/" target="_blank">can be found here</a> in both RELAX NG Schema format and DTD format.<br /><br />For more information about the GRAMPS Project visit <a href="http://gramps-project.org/" target="_blank">http://gramps-project.org/</a></li></ul>');
	break;

case 'find_media':
	$title=i18n::translate('Find media');
	$text=i18n::translate('This allows you to search the file structure to find the media item you wish to link to.');
	break;

case 'firstname_f':
	$title=i18n::translate('Family name error');
	$text=i18n::translate('The family name you have chosen has more than %s individuals.<br /><br />To help you find the family you want, the list has been broken into smaller lists according to the first letter of the person\'s given name.  This alphabetical sub-index works the same as the alphabetical index for names.<br /><ul><li>Click a letter to see all of the first names which start with that letter.</li><li>Choose <b>(unknown)</b> to list all of the people with unknown first names.</li><li>Choosing <b>ALL</b> will display a list of all families with the previously chosen surname.</li></ul>Because there are many names, it may take a long time for this list to appear on your screen.', $SUBLIST_TRIGGER_F);
	break;

case 'firstname_i':
	$title=i18n::translate('First name error');
	$text=i18n::translate('The surname you have chosen has more than %s individuals.<br /><br />To help you find the person you want, the list has been broken into smaller lists according to the first letter of the person\'s given name.  This alphabetical sub-index works the same as the alphabetical index for surnames.<br /><ul><li>Click a letter to see all of the first names which start with that letter.</li><li>Choose <b>(unknown)</b> to list all of the persons with unknown first names.</li><li>Choosing <b>ALL</b> will display a list of all persons with the previously chosen surname.</li></ul>Because there are many names, it may take a long time for this list to appear on your screen.', $SUBLIST_TRIGGER_I);
	break;

case 'follow_spouse':
	$title=i18n::translate('Check relationships by marriage');
	$text=i18n::translate('With this check box <b>un</b>checked, the relationships are only checked between blood relatives.  With this check box checked, relationships by marriage are also checked.  You will probably find more relationships by leaving this box checked.');
	break;

case 'ged_filter_description':
	$title=i18n::translate('Search option text');
	$text=i18n::translate('This option lets you search the text associated with configuration options.<br /><br />As you type letters, the search will find all configuration options that contain that letter sequence.  The search becomes more precise as you type more letters.');
	break;

case 'gedcom_configfile':
	$title=i18n::translate('GEDCOM configuration file');
	$text=i18n::translate('This is the file where all the basic settings related to the genealogical database are stored.  There is a separate file for each such database.<br /><br />You will find the path and name of each configuration file in the <b>Current GEDCOMs</b> table on the <b>GEDCOM Administration</b> page.');
	break;

case 'gedcom_info':
	$title=i18n::translate('GEDCOM information');
	$text=i18n::translate('<span class="helpstart">GEDCOM definition</span><br /><br />A quote from the Introduction to the GEDCOM 5.5.1 Standard:<div class="list_value_wrap">GEDCOM was developed by the Family History Department of The Church of Jesus Christ of Latter-day Saints (LDS Church) to provide a flexible, uniform format for exchanging computerized genealogical data.&nbsp; GEDCOM is an acronym for <i><b>GE</b></i>nealogical <i><b>D</b></i>ata <i><b>Com</b></i>munication.&nbsp; Its purpose is to foster the sharing of genealogical information and the development of a wide range of inter-operable software products to assist genealogists, historians, and other researchers.</div><br />A copy of the GEDCOM 5.5.1 <u>draft</u> Standard, to which <b>webtrees</b> adheres, can be downloaded in PDF format here:&nbsp; <a href="http://www.phpgedview.net/ged551-5.pdf" target="_blank">GEDCOM 5.5.1 Standard</a>  This Standard is only available in English.<br /><br />The GEDCOM file contains all the information about the family. All facts, dates, events, etc. are stored here. GEDCOM files have to follow strict rules because they must be exchangeable between many programs, independent of platforms or operating systems.<br /><br /><span class="helpstart">Dates in a GEDCOM file</span><br /><br />Although the date field allows for free-form entry (meaning you can type in whatever you want), there are some rules about how dates should be entered according to the GEDCOM 5.5.1 standard.<ol><li>A full date is entered in the form DD MMM YYYY.  For example, <b>01&nbsp;MAR&nbsp;1801</b> or <b>14&nbsp;DEC&nbsp;1950</b>.</li><li>If you are missing a part of the date, you can omit that part.  E.g. <b>MAR&nbsp;1801</b> or <b>14&nbsp;DEC</b>.</li><li>If you are not sure or the date is not confirmed, you could enter <b>ABT&nbsp;MAR&nbsp;1801</b> (abt = about), <b>BEF&nbsp;20&nbsp;DEC&nbsp;1950</b> (bef = before), <b>AFT&nbsp;1949</b> (aft = after)</li><li>Date ranges are entered as <b>FROM&nbsp;MAR&nbsp;1801&nbsp;TO&nbsp;20&nbsp;DEC&nbsp;1810</b> or as <b>BET&nbsp;MAR&nbsp;1801&nbsp;AND&nbsp;20&nbsp;DEC&nbsp;1810</b> (bet = between)<br /><br />The <b>FROM</b> form indicates that the event being described happened continuously between the stated dates and is used with events such as employment. The <b>BET</b> form indicates a single occurrence of the event, sometime between the stated dates and is used with events such as birth.<br /><br />Imprecise dates, where the day of the month or the month is missing, are always interpreted as the first or last possible date, depending on whether that imprecise date occurs before or after the separating keyword.  For example, <b>FEB&nbsp;1804</b> is interpreted as <b>01&nbsp;FEB&nbsp;1804</b> when it occurs before the TO or AND, and as <b>29&nbsp;FEB&nbsp;1804</b> when it occurs after the TO or AND.</li></ol><b>Be sure to enter dates and abbreviations in <u>English</u>,</b> because then the GEDCOM file is exchangeable and <b>webtrees</b> can translate all dates and abbreviations properly into the currently active language.  Furthermore, <b>webtrees</b> does calculations using these dates. If improper dates are entered into date fields, <b>webtrees</b> will not be able to calculate properly.<br /><br />You can click on the Calendar icon for help selecting a date.<br /><br /><span class="helpstart">Location levels</span><br /><br />This shows the levels that are displayed now.  The list box showing places is actually a sublist of the leftmost level.<br /><br />EXAMPLE:<br />The default order is City, County, State/Province, Country.<br />If the current level is "Top Level", the box will list all the countries in the database.<br />If the current level is "U.S.A., Top Level", the box will list all the states in the U.S.A.<br />etc.<br /><br />You can click a level to go back one or more steps.');
	break;
	
case 'gedcom_news_archive':
	$title=i18n::translate('View archive');
	$text=i18n::translate('To reduce the height of the News block, the administrator has hidden some articles.  You can reveal these hidden articles by clicking the <b>View archive</b> link.');
	break;

case 'gedcom_news_flag':
	$title=i18n::translate('Limit:');
	$text=i18n::translate('Enter the limiting value here.<br /><br />If you have opted to limit the News article display according to age, any article older than the number of days entered here will be hidden from view.  If you have opted to limit the News article display by number, only the specified number of recent articles, ordered by age, will be shown.  The remaining articles will be hidden from view.<br /><br />Zeros entered here will disable the limit, causing all News articles to be shown.');
	break;

case 'gedcom_news_limit':
	$title=i18n::translate('Limit display by:');
	$text=i18n::translate('You can limit the number of News articles displayed, thereby reducing the height of the GEDCOM News block.<br /><br />This option determines whether any limits should be applied or whether the limit should be according to the age of the article or according to the number of articles.');
	break;

case 'gedcom_path':
	$title=i18n::translate('Path and name of GEDCOM on server');
	$text=i18n::translate('There are two ways of importing your GEDCOM file into <b>webtrees</b>. They are:<ol><li>FTP the file to the server</li><li>Upload within <b>webtrees</b></li></ol>When your file already exists on the server, you engage the <i>Add GEDCOM</i> procedure and fill in the path and name of your GEDCOM file as they exist on the server. The name can be with or without extension. If no extension is given, .ged will be assumed. The path is optional. If no path is given, the value of the <i>Index file directory</i> option, as set in your <b>webtrees</b> site configuration, will be used.  Please note that on most servers, file and path names are case sensitive.<br /><br />When you engage the <i>Upload GEDCOM</i> procedure built into <b>webtrees</b>, you can use the <b>Browse</b> button to locate the desired file on your local computer. This can be a regular GEDCOM file or a ZIP file containing the GEDCOM file. <b>webtrees</b> will automatically extract and then use the GEDCOM file contained in that ZIP file.<br /><br />When uploading a file it is possible to specify an alternative path and/or filename to save it under on the server.<br /><br />See the <a href="readme.txt">Readme.txt</a> file for more information.');
	break;

case 'gedcom_title':
	$title=i18n::translate('GEDCOM title');
	$text=i18n::translate('Enter a descriptive title to be displayed when users are choosing among GEDCOM datasets at your site.');
	break;

case 'gen_missing_thumbs':
	$title=i18n::translate('Create missing thumbnails');
	$text=i18n::translate('This option will generate thumbnails for all files in the current directory which don\'t already have a thumbnail.  This is much more convenient than clicking the <b>Create thumbnail</b> link for each such file.<br /><br />If you wish to retain control over which files should have corresponding thumbnails, you should not use this option.  Instead, click the appropriate <b>Create thumbnail</b> links.');
	break;

case 'general_privacy':
	$title=i18n::translate('General privacy settings');
	$text=i18n::translate('You can have different Privacy settings for each GEDCOM on your <b>webtrees</b> web site.  Check under the page title whether you are editing the correct GEDCOM.<br /><br />You can override these general settings by using the other Privacy forms on the Edit GEDCOM privacy settings page.<br /><br /><b>More help</b><br />More help is available by clicking the <b>?</b> next to items on the page.');
	break;

case 'generate_thumb':
	$title=i18n::translate('Automatic thumbnail');
	$text=i18n::translate('Your system can generate thumbnails for certain types of images automatically.  There may be support for BMP, GIF, JPG, and PNG files.  The types that your system supports are listed beside the checkbox.<br /><br />By clicking this checkbox, you signal the system that you are uploading images of this type and that you want it to try to generate thumbnails for them.  Leave the box unchecked if you want to provide your own thumbnails.');
	break;

case 'global_facts':
	$title=i18n::translate('Global fact privacy settings');
	$text=i18n::translate('These settings define facts on a global level that should be hidden for all individuals in the GEDCOM.  This only applies to level 1 fact records such as BIRT or DEAT that will appear with their own headings on the personal facts and details tab of the individual page.<br /><ul><li>The <b>Name of fact</b> element determines which fact should be hidden.</li><li>The <b>Choice</b> element specifies the fact itself or related details.</li><li>The <b>Show to?</b> element determines at what access level the fact is shown.</li></ul><br />This feature is meant to hide certain facts, identified by GEDCOM tags, for all individuals alive or dead. By default the SSN tag is hidden to public users. This is to prevent people from stealing social security numbers and committing identity theft of dead persons.  This is probably mostly relevant for the USA.<br /><br />If you wanted to hide all marriages from public users in your GEDCOM you could set:<br /><br /><b>Name of fact</b> (MARR) - Marriage<br /><b>Choice</b> "Show fact"<br /><b>Show to?</b> "Show only to authenticated users"<br /><br /><b>Name of fact</b> (MARR) - Marriage<br /><b>Choice</b> "Show fact details"<br /><b>Show to?</b> "Show only to authenticated users"<br /><br />These settings would hide marriages and related details to everyone who wasn\'t an admin.<br /><br />Unlike all other settings, in <b>Edit existing settings for Global Fact Privacy</b> you can hide facts even from admin users. Unwanted facts are completely suppressed.');
	break;

case 'google_chart_surname':
	$title=i18n::translate('Surname');
	$text=i18n::translate('The number of occurrences of the specified name will be shown on the map. If you leave this field empty, the most common surname will be used.');
	break;

case 'header_favorites':
	$title=i18n::translate('Favorites');
	$text=i18n::translate('The Favorites drop-down list shows the favorites that you have selected on your personalized My Page.  It also shows the favorites that the site administrator has selected for the currently active GEDCOM.  Clicking on one of the favorites entries will take you directly to the Individual Information page of that person.<br /><br />More help about adding Favorites is available in your personalized My Page.');
	break;

case 'header_general':
	$title=i18n::translate('General information');
	$text='';
	break;

case 'header':
	$title=i18n::translate('Header area');
	$text=i18n::translate('The header is shown at the top of every page.  The header contains some useful links that you can use throughout the site.<br /><br />Since this site can have a different look depending on the selected <a href="?help=def_theme">theme</a>, headers can be affected and links may vary.<br /><br />The links that you might find are:<ul><li><a href="?help=header_search"><b>Search box</b></a></li><li><a href="?help=header_lang_select"><b>Language selector</b></a></li><li><a href="?help=header_user_links"><b>User links</b></a></li><li><a href="?help=header_favorites"><b>Favorites</b></a></li><li><a href="?help=header_theme"><b>Change theme</b></a></li></ul>');
	break;

case 'header_lang_select':
	$title=i18n::translate('Change language');
	$text=i18n::translate('One of the most important features of <b>webtrees</b> is that multiple languages are supported.<br /><br />The language in which <b>webtrees</b> displays all pages is determined automatically according to the Preferred Language setting of the browser.  However, the site administrator may have limited the availability of certain languages.<br /><br />Depending on site configuration, you may be able to change the language of <b>webtrees</b> by selecting a more suitable language from a drop-down list.  If you are a registered user, you can configure <b>webtrees</b> to switch to your preferred language after you login, regardless of what your browser is set to.');
	break;

case 'header_search':
	$title=i18n::translate('Search box');
	$text=i18n::translate('This Search box is small but powerful.  You can have <b>webtrees</b> search almost anything for you. When you click the <b>></b> or <b>Search</b> button, you will be linked to the Search page to see the results of your search.  You will find extensive help about searching options on the Search page.');
	break;

case 'header_theme':
	$title=i18n::translate('Change theme');
	$text=i18n::translate('When enabled by the site administrator, the Change Theme drop-down list shows you a list of the themes that you can use to view the site.<br /><br />You can change the appearance of the site by selecting a theme from the drop-down list.  If you are logged in, it will also change your user theme to the one that you have chosen so that your next login will automatically select that same theme.');
	break;

case 'header_user_links':
	$title=i18n::translate('User links');
	$text=i18n::translate('The User Links is a small block with useful links that can be found in the same place on every page.  The location of these links varies according to the theme currently in effect.<br /><br />When not logged in, you will only see the <b>Login</b> link.  After you have logged in, you will see:<ul><li><b>Logged in as (your user name)</b>. Clicking that link will take you to your Account page.</li><li>Click <b>Log out</b> to Log out.</li><li>If you have admin rights, you will also see <b>Admin</b>. Clicking this link will take you directly to the main Administration page.</li></ul>');
	break;
	
case 'help':
	$title=i18n::translate('Help');
	$text=i18n::translate('Of course, it would be ideal to create a program so simple and easy to use that it doesn\'t need any explanation at all; it should be as simple as reading a book.<br /><br />Although <b>webtrees</b> is very complicated, you should not notice that as you use it; almost everything can be used without explanation.  But, since we may have a lot of visitors and users who are not very experienced with the use of a computer or with the Internet, we offer you some help at certain places.<br /><br />You will find the following items in the Help menu:<dl><dt><b>Help with this page</b></dt></d1><br /><d1><dd>For all pages there is a general "Page Help" available.  You can click this item in the menu to get "Page Help", where you will be informed about the items on that very page.<br /><br />Page Help is often brief.  If you need more help or information about a certain item on the page than Page Help provides you can use the "Contextual Help" feature.</dd></dl><dl><dt><b>Help contents</b></dt></d1><br /><d1><dd>When you click this menu item, you will get a Help page that displays an index of the major Help topics.  The amount of Help information available will be increased as time permits.</dd></dl><d1><dt><b>FAQ list</b></dt></d1><br /><d1><dd>The FAQ (Frequently Asked Questions) page can contain an overview or a list of questions and answers on the use of this genealogy site.<br /><br />The use to which the FAQ page is put is entirely up to the site administrator. The site administrator controls the content of each item and also the order in which the items are shown on the page.</dd></dl><dl><dt><b>Search help text</b></dt></d1><br /><d1><dd>You can search <b>webtrees</b> help system.  The Search Help Text feature gives you a high degree of control over the way the search functions; you should be able to find what you are looking for easily.</dd></dl><dl><dt><b>Hide / show contextual help</b></dt></d1><br /><d1><dd>This last menu item could be the most useful for you. Clicking this link will either switch on or off the "Contextual Help".<br /><br />With Contextual Help switched on, you may find a Question Mark or similar icon beside some links, drop-down boxes, or buttons. When you click this icon, a Help screen will pop up.  This Help screen contains information about that object.<br /><br />Of course, when you click "Hide Contextual Help", all the Question Marks or icons will disappear until you click "Show...." again.</dd></dl>');
	break;

case 'help_addgedcom.php':
	$title=i18n::translate('Add GEDCOM');
	$text=i18n::translate('When you use the <b>Add GEDCOM</b> function, it is assumed that you have already uploaded the GEDCOM file to your server using a program or method <u>external</u> to <b>webtrees</b>, for example, <i>ftp</i> or <i>network connection</i>.  The file you wish to add could also have been left over from a previous <b>Upload GEDCOM</b> procedure.<br /><br />If the input GEDCOM file is not yet on your server, you <u>have to</u> get it there first, before you can start with Adding.<br /><br />Instead of uploading a GEDCOM file, you can also upload a ZIP file containing the GEDCOM file, either with <b>webtrees</b>, or using an external program. <b>webtrees</b> will recognize the ZIP file automatically and will extract the GEDCOM file and filename from the ZIP file.<br /><br />If a GEDCOM file with the same name already exists in <b>webtrees</b>, it will be overwritten. However, all GEDCOM settings made previously will be preserved.<br /><br />You are guided step by step through the procedure.');
	break;

case 'help_addnewgedcom.php':
	$title=i18n::translate('Create a new GEDCOM');
	$text=i18n::translate('You can start a new genealogical database from scratch.<br /><br />This procedure requires only a few simple steps. Step 1 is different from what you know already about uploading and adding. The other steps will be familiar.<ol><li><b>Naming the new GEDCOM</b><br />Type the name of the new GEDCOM <u>without</u> the extension <b>.ged</b>. The new file will be created in the directory named above the box where you enter the name.  Click <b>Add</b>.</li><li><b>Configuration page</b><br />You already know this page;  you configure the settings for your new GEDCOM file.</li><li><b>Validate</b><br />You already know this page;  the new GEDCOM is checked.  Since there is nothing in it, it will be ok.</li><li><b>Importing Records</b><br />Since there will be only one record to import, this will be finished very fast.</li></ol>That\'s it.  Now you can go to the Pedigree chart to see your first person in the new GEDCOM. Click the name of the person and start editing. After that, you can link new individuals to the first person.');
	break;

case 'help_admin.php':
	$title=i18n::translate('Administration');
	$text=i18n::translate('On this page you will find links to the configuration pages, administration pages, documentation, and log files.<br /><br /><b>Current Server Time:</b>, just below the page title, shows the time of the server on which your site is hosted. This means that if the server is located in New York while you\'re in France, the time shown will be six hours less than your local time, unless, of course, the server is running on Greenwich Mean Time (GMT).  The time shown is the server time when you opened or refreshed this page.<br /><br /><b>WARNING</b><br />When you see a red warning message under the system time, it means that your <i>config.php</i> is still writeable.  After configuring your site, you should, for <b>security</b>, set the permissions of this file back to read-only.  You have to do this <u>manually</u>, since <b>webtrees</b> cannot do this for you.');
	break;

case 'help_content':
	$title=i18n::translate('Help contents');
	$text=i18n::translate('<dl><dt><b>Help Contents</b></dt><dd>When you click this menu item, you will get a Help page that displays an index of the major Help topics.  The amount of Help information available will be increased as time permits.</dd></dl>');
	break;

case 'help_contents_head':
	$title=i18n::translate('Help contents');
	$text='';
	break;

case 'help_contents_help':
	$title=i18n::translate('Help contents');
	$text=
			'<table><tr><td><span class="helpstart">'.i18n::translate('Help items').'</span>
			<ul><li><a href="?help=add_media">'.i18n::translate('Add media').'</a></li><li><a href="?help=ancestry.php">'.i18n::translate('Ancestry chart').'</a></li><li><a href="?help=calendar.php">'.i18n::translate('Calendar').'</a></li><li><a href="?help=fanchart.php">'.i18n::translate('Circle diagram').'</a></li><li><a href="?help=clippings.php">'
			.i18n::translate('Clippings cart').'</a></li><li><a href="?help=def">'.i18n::translate('Definitions').'</a></li><li><a href="?help=descendancy.php">'.i18n::translate('Descendancy chart').'</a></li><li><a href="?help=famlist.php">'.i18n::translate('Families').'</a></li><li><a href="?help=familybook.php">'
			.i18n::translate('Family book chart').'</a></li><li><a href="?help=family.php">'.i18n::translate('Family information').'</a></li><li><a href="?help=faq.php">'.i18n::translate('FAQ list').'</a></li><li><a href="?help=gedcom_info">'.i18n::translate('GEDCOM information').'</a></li><li><a href="?help=header">'
			.i18n::translate('Header').'</a></li><li><a href="?help=help">'.i18n::translate('Help').'</a></li><li><a href="?help=index_portal">'.i18n::translate('Home Page').'</a></li><li><a href="?help=hourglass.php">'.i18n::translate('Hourglass chart').'</a></li><li><a href="?help=individual.php">'
			.i18n::translate('Individual information').'</a></li><li><a href="?help=indilist.php">'.i18n::translate('Individuals').'</a></li><li><a href="?help=treenav.php">'.i18n::translate('Interactive tree').'</a></li><li><a href="?help=accesskey_viewing_advice">'.i18n::translate('Keyboard shortcuts').'</a></li><li><a href="?help=login.php">'
			.i18n::translate('Login').'</a></li><li><a href="?help=pls_note11">'.i18n::translate('Lost password request').'</a></li><li><a href="?help=menu">'.i18n::translate('Menus').'</a></li><li><a href="?help=medialist.php">'.i18n::translate('Multimedia').'</a></li><li><a href="?help=edituser.php">'
			.i18n::translate('My account').'</a></li><li><a href="?help=mygedview_portal">'.i18n::translate('My Page').'</a></li><li><a href="?help=edituser_password">'.i18n::translate('Password').'</a></li><li><a href="?help=pedigree.php">'.i18n::translate('Pedigree Tree').'</a></li><li><a href="?help=placelist.php">'
			.i18n::translate('Place hierarchy').'</a></li><li><a href="?help=relationship.php">'.i18n::translate('Relationship chart').'</a></li><li><a href="?help=reportengine.php">'.i18n::translate('Reports').'</a></li><li><a href="?help=login_register.php">'.i18n::translate('Request new user account').'</a></li><li><a href="?help=best_display">'
			.i18n::translate('Screen resolution').'</a></li><li><a href="?help=search">'.i18n::translate('Search').'</a></li><li><a href="?help=hs_title">'.i18n::translate('Search help text').'</a></li><li><a href="?help=source.php">'.i18n::translate('Source').'</a></li><li><a href="?help=sourcelist.php">'
			.i18n::translate('Sources').'</a></li><li><a href="?help=timeline.php">'.i18n::translate('Timeline chart').'</a></li><li><a href="?help=edituser_username">'.i18n::translate('Username').'</a></li></ul></td>';	
		if (WT_USER_IS_ADMIN) {
			$text.='<td valign="top"><span class="helpstart">'.i18n::translate('Administrator help items').'</span><ul><li><a href="?help=admin.php">'.i18n::translate('Administration').'</a></li><li><a href="?help=help_editconfig.php">'.i18n::translate('Configure').'</a></li><li><a href="?help=help_faq.php">'
			.i18n::translate('FAQ List: Edit').'</a></li><li><a href="?help=add_gedcom">'.i18n::translate('GEDCOM: Add').'</li><li><a href="?help=add_upload_gedcom">'.i18n::translate('GEDCOM: Add vs Upload').'</a></li><li><a href="?help=edit_gedcoms">'.i18n::translate('GEDCOM: Administration page').'</a></li><li><a href="?help=change_indi2id">'
			.i18n::translate('GEDCOM: Change Individual ID to ...').'</a></li><li><a href="?help=gedcom_configfile">'.i18n::translate('GEDCOM: Configuration file').'</a></li><li><a href="?help=edit_config_gedcom">'.i18n::translate('GEDCOM: Configure').'</a></li><li><a href="?help=convert_ansi2utf">'
			.i18n::translate('GEDCOM: Convert ANSI to UTF-8').'</a></li><li><a href="?help=add_new_gedcom">'.i18n::translate('GEDCOM: Create new').'</a></li><li><a href="?help=default_gedcom">'.i18n::translate('GEDCOM: Default').'</a></li><li><a href="?help=delete_gedcom">'.i18n::translate('GEDCOM: Delete').'</a></li><li><a href="?help=download_gedcom">'
			.i18n::translate('GEDCOM: Download').'<a/></li><li><a href="?help=import_gedcom">'.i18n::translate('GEDCOM: Import').'</a></li><li><a href="?help=edit_privacy">'.i18n::translate('GEDCOM: Privacy settings').'</a></li><li><a href="?help=upload_gedcom">'.i18n::translate('GEDCOM: Upload').'</a></li><li><a href="?help=validate_gedcom">'
			.i18n::translate('GEDCOM: Validate').'</a></li><li><a href="?help=readmefile">'.i18n::translate('Readme File').'</a></li><li><a href="?help=help_useradmin.php">'.i18n::translate('User Administration').'</a></li></ul></td>';
		}
	$text.=('</tr></table>');
	break;

case 'help_editconfig.php':
	$title=i18n::translate('Configure webtrees');
	$text=i18n::translate('On this page you configure the global settings for <b>webtrees</b>.  You have to do this after you have installed <b>webtrees</b> and are running it for the first time.<br /><br />You should review the <a href=\"readme.txt\" target=\"_blank\">readme.txt</a> file before continuing to configure <b>webtrees</b>.<br /><br />As these settings are <b>global</b>, they are for the whole program and for all genealogical databases you use with <b>webtrees</b>.<br /><br />Each genealogical database also has additional configuration options that you set after clicking the <b>Click here to administer GEDCOMs</b> link on this page.<br /><br />You can also access the GEDCOM Administration function from the main Admin page, whose link is found under the My Page icon or in the header of most pages.  On the Admin page, the relevant link is called <b>Manage GEDCOMs and edit Privacy.</b>');
	break;
	
case 'help_edit_merge.php':
	$title=i18n::translate('Merge records');
	$text=i18n::translate('This page will allow you to merge two GEDCOM records from the same GEDCOM file.<br /><br />This is useful for people who have merged GEDCOMs and now have many people, families, and sources that are the same.<br /><br />The page consists of three steps.<br /><ol><li>You enter two GEDCOM IDs.  The IDs <u>must</u> be of the same type.  You cannot merge an individual and a family or family and source, for example.<br />In the <b>Merge To ID:</b> field enter the ID of the record you want to be the new record after the merge is complete.<br />In the <b>Merge From ID:</b> field enter the ID of the record whose information will be merged into the Merge To ID: record.  This record will be deleted after the Merge.</li><li>You select what facts you want to keep from the two records when they are merged.  Just click the checkboxes next to the ones you want to keep.</li><li>You inspect the results of the merge, just like with all other changes made online.</li></ol>Someone with Accept rights will have to authorize your changes to make them permanent.');
	break;
	
case 'help_faq.php':
	$title=i18n::translate('Frequently Asked Questions');
	$text=i18n::translate('<dt><b>FAQ List</b></dt>The FAQ (Frequently Asked Questions) page can contain an overview or a list of questions and answers on the use of this genealogy site.<br /><br />The use to which the FAQ page is put is entirely up to the site administrator. The site administrator controls the content of each item and also the order in which the items are shown on the page.');
	break;
	
case 'help_HS':
	$title=i18n::translate('Help text');
	$text=i18n::translate('<dl><dt><b>Search Help Text</b></dt><dd>You can search <b>webtrees</b> Help system.  The Search Help Text feature gives you a high degree of control over the way the search functions; you should be able to find what you are looking for easily.</dd></dl>');
	break;

case 'help_managesites':
	//not used? see manageservers.php
	$title=i18n::translate('Manage sites');
	$text=i18n::translate('On this page you can add remote sites and ban IP addresses.<br /><br />Remote sites can be added by providing the site title, URL, database id(optional), username, and password for the remote web service.<br /><br />IP address banning is accomplished by supplying any valid IP address range. For example, 212.10.*.*  Remote sites within the IP address ranges in the Banned list will not be able to access your web service.  You can ban specific IP addresses too.');
	break;

case 'help_page':
	$title=i18n::translate('Help with this page');
	$text=i18n::translate('<dl><dt><b>Help with this Page</b></dt><dd>For all pages there is a general "Page Help" available.  You can click this item in the menu to get "Page Help", where you will be informed about the items on that very page.<br /><br />Page Help is often brief.  If you need more help or information about a certain item on the page than Page Help provides you can use the "Contextual Help" feature.</dd></dl>');
	break;

case 'help_qm':
	$title=i18n::translate('Hide/show contextual help');
	$text=i18n::translate('<dl>dt><b>Hide / Show Contextual Help</b></dt><dd>This last menu item could be the most useful for you. Clicking this link will either switch on or off the "Contextual Help".<br /><br />With Contextual Help switched on, you may find a Question Mark or similar icon beside some links, drop-down boxes, or buttons. When you click this icon, a Help screen will pop up.  This Help screen contains information about that object.<br /><br />Of course, when you click "Hide Contextual Help", all the Question Marks or icons will disappear until you click "Show...." again.</dd></dl>');
	break;

case 'help_sourcelist.php':
	$title=i18n::translate('Sources list page');
	$text=i18n::translate('A list of sources is displayed on this page.<br /><br />Unlike the Individual Information and Family pages, there is no alphabetical index.<br /><br />A source can be an individual, a public database, an institution, an Internet resource, etc.  Because of the completely random nature of genealogical sources, it is impossible to find a sort order that is meaningful in all cases. However, <b>webtrees</b> <u>does</u> sort the Source names into alphabetical order.<br /><br /><b>SOURCES</b><br />Without sources we cannot build our database. There is a source for every item of information in the database. The source can be a relative, an institution, a public database, government or private records, an Internet resource, etc.<br /><br />A source can be linked to many persons. One person can also be linked to many sources. You can have different sources for every event, whether it is birth date, profession, marriage, children, etc.');
	break;

case 'help_uploadgedcom.php':
	$title=i18n::translate('Upload GEDCOM');
	$text=i18n::translate('Unlike the <b>Add GEDCOM</b> function, the GEDCOM file you wish to add to your database does not have to be on your server.<br /><br />In Step 1 you select a GEDCOM file from your local computer. Type the complete path and file name in the text box or use the <b>Browse</b> button on the page.<br /><br />You can also use this function to upload a ZIP file containing the GEDCOM file. <b>webtrees</b> will recognize the ZIP file and extract the file and the filename automatically.<br /><br />If a GEDCOM file with the same name already exists in <b>webtrees</b>, it will, after your confirmation, be overwritten. However, all GEDCOM settings made previously will be preserved.<br /><br />You will find more help on other pages of the procedure.');
	break;

case 'help_useradmin.php':
// duplicate text. see 'useradmin.php'
	$title=i18n::translate('User administration');
	$text=i18n::translate('On this page you can administer the current users and add new users.<br /><br /><b>User List</b><br />In this table the current users, their status, and their rights are displayed.  You can <b>delete</b> or <b>edit</b> users.<br /><br /><b>Add a new user</b><br />This form is almost the same as the one users see on the  <b>My Account</b> page.<br /><br />For several subjects we did not make special Help text for the administrator. In those cases you will see the following message:<br /><br />--- This help text is the same text that site visitors will read. --- <br />--- To save space, we did not make a special admin text for this item. ---<br /><br />Contextual help is available on every screen; make sure that the <b>Show Contextual Help</b> option in the Help menu is on, and click on a <b>?</b> next to the subject.');
	break;

case 'hide_context':
	$title=i18n::translate('Hide contextual help');
	$text=i18n::translate('Hide Contextual Help');
	break;

case 'hs_title':
	$title=i18n::translate('Search help text');
	$text=i18n::translate('You can search <b>webtrees</b> Help system.  The Search Help Text feature gives you a high degree of control over the way the search functions; you should be able to find what you are looking for easily.<br /><br /><span class="helpstart">Search for</span><br />You enter the words or the phrase you wish to find.<br /><br />The search does not pay attention to the case (upper or lower) of the search terms or the text being examined.  This means that if you search for <b>Individual</b>, you will find text containing <b>Individual</b>, <b>individual</b>, or <b>INDIVIDUAL</b>.  You will also find text containing <b>individuals</b>, etc. since the search is looking for sequences of characters rather than words.<br /><br />You can have the search look for several words at once.  Enter all of the words, separating each of them by a space, like this: <b>individual&nbsp;family&nbsp;child</b>.  When more than one word is entered, the meaning of what you have typed is clarified in the Search type field.<br /><br /><br /><span class="helpstart">Search type</span><br />You clarify the meaning of what you have entered into the Search for field by selecting among the possibilities presented here.<br /><dl><dt><b>Any word</b></dt><dd>If you have entered <b>individual&nbsp;family&nbsp;child</b>, this option will find Help text that contains one of the words listed.  The order of the words doesn\'t matter.  The meaning of the search is: "Find Help text containing <b>individual</b> <u>or</u> <b>family</b> <u>or</u> <b>child</b>".</dd><dt><b>All words</b></dt><dd>If you have entered <b>individual&nbsp;family&nbsp;child</b>, this option will find Help text that contains all of the words listed.  The order of the words doesn\'t matter.  The meaning of the search is: "Find Help text containing <b>individual</b> <u>and</u> <b>family</b> <u>and</u> <b>child</b>".</dd><dt><b>Exact phrase</b></dt><dd>If you have entered <b>individual&nbsp;family&nbsp;child</b>, this option will find Help text that contains all of the words listed in the order given.  The meaning of the search is: "Find Help text containing the words <b>individual&nbsp;family&nbsp;child</b> in exactly that order with no other words or characters between".  You probably won\'t find this particular phrase in any Help text.<br /><br />There are a few limitations on this type of search.  Certain special characters such as <b>&quot; &lt; &gt;</b> etc. are contained within the Help text in symbolic form and won\'t be found if they form part of the text you enter.  Some Help text contains a special kind of Space character represented by <b>&amp;nbsp;</b> and you won\'t find phrases containing this character.</dd></dl><br /><br /><span class="helpstart">Search in</span><br />You determine the scope of the search here.<br /><br />Administrators have the choice of searching User Help or Configuration Help or both.  Users do not have this choice; because they do not have access to any configuration features, they can only search the User Help file.<br /><br />The Help files contain not only Help text but also certain text strings used to build input forms and other material.  This option lets you control whether the entire Help file should be examined or whether only the Help text should be looked at.');
	break;

case 'import_gedcom':
	$title=i18n::translate('Import GEDCOM');
	$text=i18n::translate('In most cases importing of an externally created GEDCOM file is one step in procedures that result in bulk changes to the genealogical database.<br /><br />These steps are in a logical sequence and need to be completed in the prescribed order so that the genealogical database is usable.<br /><br />If, for some reason, you did not complete these steps in the correct order, you will see a <u>warning</u> message that the GEDCOM is not yet imported. To correct the problem, click the <b>Import GEDCOM</b> link to import the file.<br /><br />Existing GEDCOM configuration settings will not change when you re-import a GEDCOM.  Existing data will, however, be overwritten.');
	break;

case 'import_options':
	$title=i18n::translate('Import options');
	$text=i18n::translate('You can choose additional options to be used when importing the GEDCOM.');
	break;

case 'include_media':
	$title=i18n::translate('Include media (automatically zips files)');
	$text=i18n::translate('Select this option to include the media files associated with the records in your clippings cart.  Choosing this option will automatically zip the files during download.');
	break;

case 'index_add_favorites':
	$title=i18n::translate('Add a new favorite');
	$text=i18n::translate('This form allows you to add a new favorite item to your list of favorites.<br /><br />You must enter either an ID for the person, family, or source you want to store as a favorite, or you must enter a URL and a title.  The Note field is optional and can be used to describe the favorite.  Anything entered in the Note field will be displayed in the Favorites block after the item.');
	break;

case 'index_charts':
	$title=i18n::translate('Charts block');
	$text=i18n::translate('This block allows a pedigree, descendancy, or hourglass chart to appear on My Page or the Home Page.  Because of space limitations, the charts should be placed only on the left side of the page.<br /><br />When this block appears on the Home Page, the root person and the type of chart to be displayed are determined by the administrator.  When this block appears on the user\'s personalized My Page, these options are determined by the user.<br /><br />The behavior of these charts is identical to their behavior when they are called up from the menus.  Click on the box of a person to see more details about them.');
	break;

case 'index_common_given_names':
	$title=i18n::translate('Most common given names block');
	$text=i18n::translate('This block displays a list of frequently occurring given names from this database. You can configure how many given names should appear in the list.');
	break;

case 'index_common_names':
	$title=i18n::translate('Most common surnames block');
	$text=i18n::translate('This block displays a list of frequently occurring surnames from this database. A surname must occur at least %s times before it will appear in this list.  The administrator has control over this threshold.<br /><br />When you click on a surname in this list, you will be taken to the Individuals, where you will get more details about that name.', $COMMON_NAMES_THRESHOLD);
	break;

case 'index_events':
	$title=i18n::translate('Upcoming events block');
	$text=i18n::translate('This block shows you anniversaries of events that are coming up in the near future.<br /><br />The administrator determines how far ahead the block will look.  You can further refine the block\'s display of upcoming events through several configuration options.');
	break;

case 'index_favorites':
	$title=i18n::translate('GEDCOM favorites block');
	$text=i18n::translate('The GEDCOM Favorites block is much the same as the "My Favorites" block of My Page. Unlike the My Page configuration, only the administrator or a user with Admin rights can change the list of favorites in this block.<br /><br />The purpose of the GEDCOM Favorites block is to draw the visitor\'s attention to persons of special interest.  This GEDCOM\'s favorites are available for selection from a drop-down list in the header on every page.<br /><br />When you click on one of the listed site favorites, you will be taken to the Individual Information page of that person.');
	break;

case 'index_gedcom_news_adm':
	$title=i18n::translate('GEDCOM news block HTML');
	$text=i18n::translate('The GEDCOM News text allows the use of <b>HTML tags</b> and <b>HTML entities</b>.  HTML should not be used in News titles.<br /><br />Be sure to always use both start and end tags.  It may help to have an understanding of HTML appropriate for a web site administrator. This program uses <b>Cascading Style Sheets (CSS)</b> as well. A different CSS is implemented for each theme.  You can use classes from these style sheets to control the appearance of your messages.<br /><br />If you need more help with this, the <b>webtrees</b> web site has some examples of how to use these tags in your GEDCOM News block.<br /><br />As with the FAQ list, GEDCOM News titles and News text allow embedded references to $pgv_lang, $factarray, and $GLOBALS variables to provide complete flexibility in the creation of News items that are sensitive to the currently active language.<br /><br />The following description, taken from the Help text for the FAQ list, is equally applicable to GEDCOM News items.<br /><br />HTML entities are a very easy way to add special characters to your FAQ titles and text.  You can use symbolic names, decimal numbers, or hexadecimal numbers.  A complete list of HTML entities, their coding, and their representation by your browser can be found here:  <a href="http://htmlhelp.com/reference/html40/entities/" target="_blank">HTML entity lists</a><br /><br />On occasion, you may need to show a Tilde character&nbsp;&nbsp;<b>&#x7E;</b>&nbsp;&nbsp;or a Number Sign&nbsp;&nbsp;<b>&#x23;</b>&nbsp;&nbsp;in your URLs or text.  These characters have a special meaning to the <b>webtrees</b> Help system and can only be entered in their hexadecimal or decimal form.  Similarly, the&nbsp;&nbsp;<b>&lt;</b>&nbsp;&nbsp;and&nbsp;&nbsp;<b>&gt;</b>&nbsp;&nbsp;characters that usually enclose HTML tags must be entered in their hexadecimal or decimal forms if they are to be treated as normal text instead of signalling an HTML tag.<ul><li><b>&amp;&#x23;35;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x23;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x23;</b></li><li><b>&amp;&#x23;60;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x3C;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x3C;</b></li><li><b>&amp;&#x23;62;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x3E;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x3E;</b></li><li><b>&amp;&#x23;126;</b>&nbsp;&nbsp;or&nbsp;&nbsp;<b>&amp;&#x23;x7E;</b>&nbsp;&nbsp;will result in&nbsp;&nbsp;<b>&#x7E;</b></li></ul>There is a&nbsp;&nbsp;<b>&amp;tilde;</b>&nbsp;&nbsp;HTML entity, but this symbol is not interpreted as a Tilde when coded in URLs.<br /><br />You can insert references to entries in the language files or to values of global variables.  Examples: <ul><li><b>&#x23;pgv_lang[add_to_cart]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the language variable "Add to Clippings Cart", and if it were to appear in this field, would show as <b>Add to Clippings Cart</b> when the FAQ list is viewed in the current language.</li><li><b>&#x23;factarray[AFN]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the Fact name $factarray["AFN"], and if it were to appear in this field, would show as <b>Ancestral File Number (AFN)</b> when the FAQ list is viewed in the current language. </li><li><b>&#x23;WT_VERSION&#x23;&nbsp;&#x23;WT_VERSION_RELEASE&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the constant WT_VERSION, a space, and a reference to the constant WT_VERSION_RELEASE, and if they were to appear in this field, would show as <b>#WT_VERSION#&nbsp;#WT_VERSION_RELEASE#</b> when the FAQ list is viewed in the current language.</li><li><b>&#x23;GLOBALS[GEDCOM]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the global variable $GEDCOM, which is the name of the current GEDCOM file.  If it were to appear in this field, it would show as <b>#GLOBALS[GEDCOM]#</b>.</li><li><b>&#x23;GLOBALS[GEDCOM_TITLE]&#x23;</b>&nbsp;&nbsp;&nbsp;is a reference to the global variable $GEDCOM_TITLE, which is the title of the current GEDCOM file.  If it were to appear in this field, it would show as <b>#GLOBALS[GEDCOM_TITLE]#</b>.</li></uli></ul><br />This feature is useful when you wish to create FAQ lists that are different for each language your site supports.  You should put your customized FAQ list titles and entries into the <i>languages/extra.xx.php</i> files (<i>xx</i> is the code for each language), using the following format:<br />$pgv_lang["faq_title1"] = "This is a sample FAQ title";<br />$pgv_lang["faq_body1"] = "This is a sample FAQ body.";');
	break;

case 'index_gedcom_news':
	$title=i18n::translate('GEDCOM news block');
	$text=i18n::translate('The News block is like a bulletin board for this GEDCOM.  The site administrator can place important announcements or interesting news messages here.<br /><br />If you have something interesting to display, please contact the site administrator;  he can put your message on this bulletin board.');
	break;

case 'index_htmlplus_compat':
	$title=i18n::translate('Advanced HTML compatability mode');
	$text=i18n::translate('Enable compatibility with older versions of this block.  When checked, both old and new keywords will be recognized and acted upon.<br /><br />For example, the text <b>&#35;TOTAL_FAM&#35;</b> will be recognized as being equivalent to <b>&#35;totalFamilies&#35;</b>, <b>&#35;FIRST_DEATH_PLACE&#35;</b> to <b>&#35;firstDeathPlace&#35;</b>, <b>&#35;TOP10_BIGFAM&#35;</b> to <b>&#35;topTenLargestFamily&#35;</b>, etc.<br /><br />Unless absolutely necessary, you should not use Compatibility mode.');
	break;

case 'index_htmlplus_content':
	$title=i18n::translate('Advanced HTML content');
	$text=i18n::translate('Unlike the HTML, GEDCOM News, and GEDCOM Statistics blocks, you have full control over the appearance of your block.  You can use HTML tags, and the block uses the CSS style sheets from the currently active theme.  References to information from the currently active genealogical database can be included in the text.<br /><br />Database references are signalled in the text by enclosing keywords within paired <b>&#35;</b> symbols.  For example, <b>&#35;totalFamilies&#35;</b> represents the number of families in the database.  On occasion, you may wish to use a database reference as text instead of its true meaning.  To do so, you need to replace the <b>&#35;</b> symbols enclosing the keyword by their symbolic equivalent.  For example, if your text contains <b>&amp;&#35;35;totalFamilies&amp;&#35;35;</b> it will print as <b>&#35;totalFamilies&#35;</b> instead of becoming a database reference.<br /><br />For a full example of the use of this block, please examine the &quot;GEDCOM Statistics&quot; template found in the blocks/ directory, it uses most of the styles of tags, including language and help text links.<br /><br />The <b>Keyword Examples (English only)</b> template contains a full list of all supported keywords.');
	break;

case 'index_htmlplus_gedcom':
	$title=i18n::translate('Advanced HTML GEDCOM');
	$text=i18n::translate('Select the database to which the keywords apply.<br /><br />Your site supports several databases.  Keywords such as <b>&#35;totalFamilies&#35;</b> can only refer to one database.  You can identify the database that is to be consulted for all such keywords.  Each Advanced HTML block can only access one database.');
	break;

case 'index_htmlplus':
	$title=i18n::translate('Advanced HTML block');
	$text=i18n::translate('This block lets the administrator add information to My Page or the Home Page.  Its purpose is similar to the HTML, GEDCOM News, and GEDCOM Statistics blocks, but the administrator has more control over its appearance.');
	break;

case 'index_htmlplus_template':
	$title=i18n::translate('Advanced HTML template');
	$text=i18n::translate('To assist you in getting started with this block, we have created several standard templates.  When you select one of these templates, the text area will contain a copy that you can then alter to suit your site\'s requirements.');
	break;

case 'index_htmlplus_title':
	$title=i18n::translate('Advanced HTML title');
	$text=i18n::translate('This text should be blank or very brief.  When blank, the Advanced HTML block will show on the Index or Portal page as a plain block, just like the HTML block does.  When there is text, the Advanced HTML block will show like all the other blocks, complete with a block title bar containing the text you enter here.');
	break;

case 'index_loggedin':
	$title=i18n::translate('Logged in users block');
	$text=i18n::translate('This block will show you the users currently logged in.<br /><br />If you are not an administrator, your view of logged-in users is restricted to those who have elected to be visible while on-line.  For this to work, you must also elect to be visible while on-line.  On-line users who are invisible to you are counted as being anonymous.');
	break;

case 'index_login':
	$title=i18n::translate('Login block');
	$text=i18n::translate('You can login on almost every page of this program. You will usually do so on the first page, since you can only access privileged information when you are logged in.<br /><br />You can login by typing your <b>username</b> and <b>password</b> and then clicking the Login button.');
	break;

case 'index_media':
	$title=i18n::translate('Random picture block');
	$text=i18n::translate('In this block <b>webtrees</b> randomly chooses a media file to show you on each visit to this page.<br /><br />When you click on the picture, you will see its full-size version.  Below the picture you have a link to the person associated with the picture.  When you click on the picture caption, you will see the picture on the MultiMedia page. When you click on the person\'s name, you will be taken to the Individual Information page of that person.');
	break;

case 'index_myged_help':
	$title=i18n::translate('My page');
	$text=i18n::translate('This is your personal page.<br /><br />Here you will find easy links to access your personal data such as <b>My Account</b>, <b>My Indi</b> (this is your Individual Information page), and <b>My Pedigree</b>.  You can have blocks with <b>Messages</b>, a <b>Journal</b> (like a Notepad) and many more.<br /><br />The layout of this page is similar to the Home Page that you see when you first access this site.  While the parts of the Home Page are selected by the site administrator, you can select what parts to include on this personalized page.  You will find the link to customize this page in the Welcome block or separately when the Welcome block is not present.<br /><br />You can choose from the following blocks:<ul><li><a href="?help=mygedview_charts"><b>Charts</b></a></li><li><a href="?help=mygedview_customize"><b>Customize my page</b></a></li><li><a href="?help=mygedview_stats"><b>GEDCOM statistics</b></a></li><li><a href="?help=index_loggedin"><b>Logged in users</b></a></li><li><a href="?help=mygedview_message"><b>Messages</b></a></li><li><a href="?help=mygedview_favorites"><b>My favorites</b></a></li><li><a href="?help=mygedview_myjournal"><b>My journal</b></a></li><li><a href="?help=index_onthisday"><b>On this day in your history</b></a></li><li><a href="?help=index_media"><b>Random media</b></a></li><li><a href="?help=recent_changes"><b>Recent changes</b></a></li><li><a href="?help=index_events"><b>Upcoming events</b></a></li><li><a href="?help=mygedview_welcome"><b>Welcome</b></a></li></ul>');
	break;

case 'index_onthisday':
	$title=i18n::translate('On this day in your history block');
	$text=i18n::translate('This block is similar to the "Upcoming Events" block, except that it displays today\'s events.');
	break;
	
case 'index_portal':
	$title=i18n::translate('Home page');
	$text=i18n::translate('The Home page consists of several separate blocks, and can be customized. On sites that have more than one genealogical database, you may see a different Home page for each.  Depending on how the administrator customized the site, you may see any of the following blocks on the Home Page:<ul><li><a href="?help=index_charts"><b>Charts</b></a></li><li><a href="?help=index_favorites"><b>GEDCOM favorites</b></a></li><li><a href="?help=index_gedcom_news"><b>GEDCOM news</b></a></li><li><a href="?help=index_stats"><b>GEDCOM statistics</b></a></li><li><a href="?help=index_login"><b>Login</b></a></li><li><a href="?help=index_loggedin"><b>Logged in users</b></a></li><li><a href="?help=index_common_names"><b>Most common surnames</b></a></li><li><a href="?help=index_onthisday"><b>On this day in your history</b></a></li><li><a href="?help=index_media"><b>Random media</b></a></li><li><a href="?help=recent_changes"><b>Recent changes</b></a></li><li><a href="?help=index_events"><b>Upcoming events</b></a></li><li><a href="?help=index_welcome"><b>Welcome</b></a></li></ul>');
	break;

case 'index_stats':
	$title=i18n::translate('GEDCOM statistics block');
	$text=i18n::translate('In this block you will see some statistics about the current GEDCOM file.  If you need more information than is listed, send a message to the contact at the bottom of the page.');
	break;

case 'index_top10_pageviews':
	$title=i18n::translate('Most viewed items block');
	$text=i18n::translate('This block will list the top 10 individuals, families, or sources that have been viewed by visitors to this site.  In order for this block to appear the site administrator must have enabled the Item Hit counters.');
	break;

case 'index_welcome':
	$title=i18n::translate('Welcome block');
	$text=i18n::translate('The Welcome block shows you the current database title, the date and time, and, if enabled by the admin, the Hit Counter.<br /><br />The Hit Counter is only available in the Welcome block and on the Individual Information page.  The counter counts the "Hits" of these pages. That means it counts how many times these pages are visited.  The counter does not check the Internet address of a visitor; every visit to a page from <u>any</u> remote location counts as another Hit.');
	break;

case 'invalid_header':
	$title=i18n::translate('Detected lines before the GEDCOM header <b>0&nbsp;HEAD</b>.  On cleanup, these lines will be removed.');
	$text=i18n::translate('A GEDCOM file must begin with <b>0&nbsp;HEAD</b>. <b>webtrees</b> detected that the GEDCOM file you are importing does not have <b>0&nbsp;HEAD</b> as the first line. When you click the Cleanup button, any lines before the first <b>0&nbsp;HEAD</b> line will be removed.<br /><br />This error usually means that the program you used to create your GEDCOM did not create it properly or it is not a GEDCOM file. You should check to make sure that you uploaded the correct file, and that it starts with the line <b>0&nbsp;HEAD</b> and ends with the line <b>0&nbsp;TRLR</b>.');
	break;

case 'is_user':
	$title=i18n::translate('No admin text');
	$text=i18n::translate('--- This help text is the same text that site visitors will read. --- <br />--- To save space, we did not make a special admin text for this item. ---');
	break;

case 'keep_media':
	$title=i18n::translate('Keep media links');
	$text=i18n::translate('Should existing media links be retained in the database when a replacement GEDCOM is being uploaded. The <b>No</b> option removes existing media links from the database, while the <b>Yes</b> option keeps them.<br /><br />This option is useful when you export your GEDCOM from <b>webtrees</b> to an off-line GEDCOM maintenance program that does not handle embedded media pointers properly, and then subsequently re-import that changed GEDCOM into <b>webtrees</b>.  Under such circumstances, the media pointers within the GEDCOM you exported to your off-line editing program are destroyed, and you would have to re-link all of your media files to the proper Person, Family, and Source records after you re-import the GEDCOM into <b>webtrees</b>.<br /><br />The <b>Yes</b> option tells <b>webtrees</b> to keep the existing media links so that you do not have to re-create them after you import the changed GEDCOM, but this requires the off-line editing program to always produce the same Person, Family, and Source identification numbers.<br /><br /><i>Family Tree Maker</i> is one of several off-line editing programs that does <u>not</u> properly handle media object pointers within the GEDCOM.  <i>Legacy</i>, among many others, <u>does</u> handle these properly.');
	break;

case 'lang_debug':
	$title=i18n::translate('Help text debug option');
	$text=i18n::translate('When you enable this option, the names of the language variables used in help text will print in the help text popup window.  This will help translators determine the variable name when text needs to be adjusted.<br /><br />This setting will only be valid during your current <b>webtrees</b> session.');
	break;

case 'lang_edit':
	$title=i18n::translate('Edit language');
	$text=i18n::translate('This page is intended to be used by translators.  You can translate, compare, and export language files.  There is also an option to help translators determine the origin of text that is output by <b>webtrees</b>.<br /><br />You can use the following options and utilities:');
	break;

case 'lang_filenames':
	$title=i18n::translate('Language files');
	$text=i18n::translate('<b>webtrees</b> has implemented support for many different languages.  This has been achieved by keeping all text that is visible to users in files completely separate from the main program.  There is a set of eight files for each supported language, and the various texts have been separated into one of these files according to function.  <b>Not all language files need to be present.</b>  When a given text is not yet available in translated form, <b>webtrees</b> will always use the English version.<br /><br />The files in each language set are:<br /><ul><li><b><i>admin.xx.php</i></b>&nbsp;&nbsp;This file contains terms and common expressions for use during the administration of <b>webtrees</b> and the genealogical databases.<br /><br /></li><li><b><i>configure_help.xx.php</i></b>&nbsp;&nbsp;This file contains Help text for use during configuration of <b>webtrees</b>.  The Help text is not intended to be viewed by ordinary users.<br /><br /></li><li><b><i>countries.xx.php</i></b>&nbsp;&nbsp;This is a list of country names, taken from the Web site of the Statistics Division, United Nations Department of Economic and Social Affairs.  This is the relevant <a href="http://unstats.un.org/unsd/methods/m49/m49alpha.htm" target="_blank"><b>link</b></a> to the English list.  The list is available in either English or French.<br /><br /></li><li><b><i>editor.xx.php</i></b>&nbsp;&nbsp;This file contains terms and common expressions for use during the editing of entries in the genealogical databases.<br /><br /></li><li><b><i>facts.xx.php</i></b>&nbsp;&nbsp;This file contains the textual equivalents of the GEDCOM Fact codes found in the GEDCOM 5.5.1 Standard.  It also contains additional Fact codes not found in the Standard but used by various genealogy programs.<br /><br />An English copy of the <a href="http://www.phpgedview.net/ged551-5.pdf" target="_blank"><b>GEDCOM 5.5.1 Standard</b></a> can be downloaded in PDF (Portable Document Format).<br /><br /></li><li><b><i>faqlist.xx.php</i></b>&nbsp;&nbsp;This file is a set of <b>f</b>requently <b>a</b>sked <b>q</b>uestions that have been collected by the <b>webtrees</b> development team.  Each FAQ has two entries in this file.  One entry is the FAQ heading (usually the question), and the other is the FAQ body (usually the answer).  Replacements for the <b><i>faqlist.xx.php</i></b> files, which are updated frequently, may be downloaded from the <b>webtrees</b> home site.<br /><br />The administrator can use the FAQs in this file to build an FAQ list that is specific to his site.<br /><br /></li><li><b><i>help_text.xx.php</i></b>&nbsp;&nbsp;This file contains Help text for ordinary users.  Some Help topics in this file address the needs of administrators, and are hidden from users who do not have Admin rights.<br /><br /></li><li><b><i>lang.xx.php</i></b>&nbsp;&nbsp;Many terms and common expressions are found in this file.</li></ul><br /><b>webtrees</b> also supports an optional ninth language file, <b><i>extra.xx.php</i></b>.  This file is always loaded after all the others and provides a means whereby a site administrator can override or alter any standard text in the selected language.  It can also be used to provide a title for the genealogical databases that varies according to the currently active language.<br /><br />The contents of this additional file are completely up to the site administrator;  this file will <b>never</b> be distributed with any version of <b>webtrees</b>.  The administrator should never make changes to the standard language files;  all local changes should be concentrated in this optional file.');
	break;

case 'lang_langcode':
	$title=i18n::translate('Language detection codes');
	$text=i18n::translate('These codes allow <b>webtrees</b> to detect the Preferred Language setting of the browser being used. <b>webtrees</b> determines the language actually being requested by the browser by matching the browser\'s language code against this list.  Individual list entries must be separated by a semicolon.');
	break;

case 'lang_shortcut':
	$title=i18n::translate('Abbreviation for language files');
	$text=i18n::translate('This code defines an abbreviation for the language name.  This abbreviation forms part of the name of each of the language files used by <b>webtrees</b>.  For example, the abbreviation used for French is <b>fr</b>, and consequently the file names for French are <i>configure_help.<b>fr</b>.php</i>, <i>countries.<b>fr</b>.php</i>, <i>facts.<b>fr</b>.php</i>, <i>help_text.<b>fr</b>.php</i>, and <i>lang.<b>fr</b>.php</i>');
	break;

case 'language_to_edit':
	$title=i18n::translate('Language to edit');
	$text=i18n::translate('In this list box you select the language whose messages you want to edit.');
	break;

case 'language_to_export':
	$title=i18n::translate('Language to export');
	$text=i18n::translate('From this list box you can select the language whose messages you want to export.<br /><br />The routine currently only exports the contents of the <i>configure_help.xx.php</i>, <i>help_text.xx.php</i>, and <i>lang.xx.php</i> files.  The output is an HTML file that you can print from your browser.');
	break;

case 'lifespan_add_person':
	$title=i18n::translate('Add person');
	$text=i18n::translate('You can have several persons on the timeline.<br /><br />Use this box to supply each person\'s ID.  If you don\'t know the ID of the person, you can click the <b>Find ID</b> link next to the box.<br /><br />~Include Immediate Family CheckBox~<br/>Include Immediate Family is checked by default.  Leave checked to view the father, mother, spouse, siblings, and children of the individual being added to the timeline.  Uncheck if you wish to omit the immediate family.');
	break;

case 'line_up_generations':
	$title=i18n::translate('Line up the same generations');
	$text=i18n::translate('When this check box is checked, the chart will be printed with the same generations lining up horizontally on the page.  When it is unchecked, each generation will appear going down the page regardless of the type of relationship.');
	break;

case 'link_child':
	$title=i18n::translate('Link to an existing family as a child');
	$text=i18n::translate('You can link this person as a child to an existing family when you click this link.<br /><br />Suppose that at one time the parents of the person were unknown, and you discovered later that the parents have a record in this database.<br /><br />Just click the link, enter the ID of the family, and you have competed the task.  If you don\'t know the family\'s ID, you can search for it.');
	break;

case 'link_gedcom_id':
	$title=i18n::translate('GEDCOM ID');
	$text=i18n::translate('Use this section to select the alternate database identifier that contains the person you are linking to.');
	break;

case 'link_husband':
	$title=i18n::translate('Link to an existing family as a husband');
	$text=i18n::translate('This item will allow you to link the current individual as a husband to a family that is already in the database. By clicking this link you can add this person to an existing family, of which the husband was unknown until now. This person will take the place of the previously unknown husband. All events, marriage information, and children will keep their existing links to the family.<br /><br />Just click the link, enter the ID of the family, and you have competed the task. This is an advanced editing option that should only be used if the family you want to link to already exists.  If you want to add a <u>new</u> family to this individual, use the <b>Add a new wife</b> link.');
	break;

case 'link_new_husb':
	$title=i18n::translate('Add a husband using an existing person');
	$text=i18n::translate('This will allow you to link another individual, who already exists, as a new husband to this person.  This will create a new family with the husband you select.  You will also have the option of specifying a marriage for this new family.');
	break;

case 'link_new_wife':
	$title=i18n::translate('Add a wife using an existing person');
	$text=i18n::translate('This will allow you to link another individual, who already exists, as a new wife to this person.  This will create a new family with the wife you select.  You will also have the option of specifying a marriage for this new family.');
	break;

case 'link_person_id':
	$title=i18n::translate('Person ID');
	$text=i18n::translate('In this field you enter the ID of the person you are linking to (e.g. I100).');
	break;

case 'link_remote':
	$title=i18n::translate('Link remote person');
	$text=i18n::translate('Use this form to link people to other people either from another site or another genealogical database accessible to your copy of <b>webtrees</b>.<br /><br />To add such a link, you must first select the relationship type, then choose a site already known to <b>webtrees</b> or define a new site, and then enter that site\'s ID of the person you want to link to.  <b>webtrees</b> will then automatically download information from the remote site as necessary.  The downloaded information does <u>not</u> become part of your genealogical database; it remains on the original site but is incorporated into the various pages where this remotely linked person is displayed.<br /><br />Refer to the Help link next to each element on the page for more information about that element.  You can also check the online English tutorial for more information: <a href="#WT_WEBTREES_WIKI#/en/index.php?title=How_To:Remote_Link_Individuals_Across_Websites_And_Databases" target="_blank">#WT_WEBTREES_WIKI#</a>.');
	break;

case 'link_remote_location':
	$title=i18n::translate('Site location');
	$text=i18n::translate('This option allows you to choose whether data for the person you are linking to is on the same site but in a different genealogical database set, or whether the data is on a different site accessible through the Internet.<br /><br />If the person is on the same site, you will be asked to select the dataset identifier and enter the person\'s ID.<br /><br />For a remote site, you will be asked to enter its URL, a database identifier, and the person\'s remote ID.');
	break;

case 'link_remote_rel':
	$title=i18n::translate('Relationship to current person');
	$text=i18n::translate('Use this option to select the relationship the remote person has to the person you are linking them with on your site.  For example, selecting <i>Father</i> would mean that the person on the remote site is the father of the person you are linking them to locally.');
	break;

case 'link_remote_site':
	$title=i18n::translate('Site');
	$text=i18n::translate('In this section you specify the parameters that are required to connect to the remote site hosting the data you are linking to. You have the option of choosing from a list of known sites that you have used before, or entering the Site URL and Database ID for a new one.<br /><br />In the <b>Site URL</b> field, you enter the URL to access the web services description file (WDSL) which tells <b>webtrees</b> how to access the data on the remote site.  For a remote <b>webtrees</b> website, the URL to the WSDL file will look like this: <u>http://www.remotesite.com/webtrees/genservice.php?wsdl</u><br /><br />The <b>Database ID</b> field is used to enter an optional database identifier for remote sites that require one.  For <b>webtrees</b> sites, this is the name of the GEDCOM file. <br /><br />The <b>Username</b> and the <b>Password</b> fields are necessary if the database requires it.<br /><br /><i>Note: Remote <b>webtrees</b> sites must be running version 4.0 or later; earlier versions do not have this capability.</i>');
	break;

case 'link_wife':
	$title=i18n::translate('Link to an existing family as a wife');
	$text=i18n::translate('This item will allow you to link the current individual as a wife to a family that is already in the database.<br /><br />This is an advanced editing option that should only be used if the family you want to link to already exists.  If you want to add a <u>new</u> family to this individual, use the <b>Add a new husband</b> link.');
	break;

case 'login_buttons_aut':
	$title=i18n::translate('Login buttons');
	$text=i18n::translate('Here you see two buttons to login to the system.<br /><br />The page you will be taken to depends on which button you click after typing your user name and password.<br /><ul><li>The <b>Login</b> button<br />If you click this button, you will be logged in and go directly to your My Page, where you can edit your settings, add or edit favorites, send and read messages, etc.</li><li>The <b>Admin</b> button<br />If you have Admin rights, you can click this button to go directly to the main Administration page.</li></ul>');
	break;

case 'login_buttons':
	$title=i18n::translate('Login buttons');
	$text=i18n::translate('Here you see two buttons to login to the system.<br /><br />The page you will be taken to or sent back to depends on which button you click after typing your user name and password.<br /><ul><li>The <b>Login</b> button<br />If you click this button, you will return to the page you were just on, but with logged-in access rights.<br /><br />For example, if you click <b>Login</b> when you were at the Pedigree page, you will return to that same page.  If you click this button when you were on the main Home Page, you will be taken to My Page.</li><li>The <b>Admin</b> button<br />If you have Admin rights, you can click this button to go directly to the main Administration page.</li></ul>');
	break;

case 'login_page':
	$title=i18n::translate('Login page');
	$text=i18n::translate('On this page you can login, request a new password, or request a new user account.');
	break;

case 'macfile_detected':
	$title=i18n::translate('Macintosh file detected.  On cleanup your file will be converted to a DOS file.');
	$text=i18n::translate('<b>webtrees</b> detected that your GEDCOM file was created on a Macintosh computer.<br /><br />Macintosh files end each line with a CR control code.  CR is Ctrl+M.<br />Unix files end each line with an LF control code.  LF is Ctrl+J.<br />Windows and DOS use a two-code sequence, CR followed by LF.<br /><br /><b>webtrees</b> requires that all files use Unix or DOS line endings. When you click the Cleanup button, your line endings will be converted accordingly.');
	break;

case 'mail_option1':
	$title=i18n::translate('Internal messaging');
	$text=i18n::translate('With this option, the <b>webtrees</b> internal messaging system will be used and no emails will be sent.<br /><br />You will receive only <u>internal</u> messages from the other users.  When another site user sends you a message, that message will appear in the Message block on your personal My Page.  If you have removed this block from your My Page, you will not see any messages.  They will, however, show up as soon as you configure My Page to again have the Message block.');
	break;

case 'mail_option2':
	$title=i18n::translate('Internal messaging with emails');
	$text=i18n::translate('This option is like <b>webtrees</b> internal messaging, with one addition.  As an extra, a copy of the message will also be sent to the email address you configured on your Account page.<br /><br />This is the default contact method.');
	break;

case 'mail_option3':
	$title=i18n::translate('Mailto link');
	$text=i18n::translate('With this option, you will only receive email messages at the address you configured on your Account page.  The messaging system internal to <b>webtrees</b> will not be used at all, and there will never be any messages in the Message block on your personal My Page.');
	break;

case 'mail_option4':
	$title=i18n::translate('No contact method');
	$text=i18n::translate('With this option, you will not receive any messages.  Even the administrator will not be able to reach you.');
	break;

case 'manage_media':
	$title=i18n::translate('Manage multimedia');
	$text=i18n::translate('On this page you can easily manage your Media files and directories.<br /><br />When you create new Media subdirectories, <b>webtrees</b> will ensure that the identical directory structure is maintained within the <b>%sthumbs</b> directory.  When you upload new Media files, <b>webtrees</b> can automatically create the thumbnails for you.<br /><br />Beside each image in the Media list you\'ll find the following options.  The options actually shown depend on the current status of the Media file.<ul><li><b>Edit</b>&nbsp;&nbsp;When you click on this option, you\'ll see a page where you can change the title of the Media object.  If the Media object is not yet linked to a person, family, or source in the currently active database, you can establish this link here.  You can rename the file or even change its location within the <b>%s</b> directory structure.  When necessary, <b>webtrees</b> will automatically create the required subdirectories or any missing thumbnails.</li><li><b>Edit raw GEDCOM record</b>&nbsp;&nbsp;This option is only available when the administrator has enabled it.  You can view or edit the raw GEDCOM data associated with this Media object.  You should be very careful when you use this option.</li><li><b>Delete file</b>&nbsp;&nbsp;This option lets you erase all knowledge of the Media file from the current database.  Other databases will not be affected.  If this Media file is not mentioned in any other database, it, and its associated thumbnail, will be deleted.</li><li><b>Remove object</b>&nbsp;&nbsp;This option lets you erase all knowledge of the Media file from the current database.  Other databases will not be affected.  The Media file, and its associated thumbnail, will not be deleted.</li><li><b>Remove links</b>&nbsp;&nbsp;This option lets you remove all links to the media object from the current database.  The file will not be deleted, and the Media object by which this file is known to the current database will be retained.  Other databases will not be affected.</li><li><b>Set link</b>&nbsp;&nbsp;This option lets you establish links between the media file and persons, families, or sources of the current database.  When necessary, <b>webtrees</b> will also create the Media object by which the Media file is known to the database.</li><li><b>Create thumbnail</b>&nbsp;&nbsp;When you select this option, <b>webtrees</b> will create the missing thumbnail.</li></ul>', $MEDIA_DIRECTORY, $MEDIA_DIRECTORY);
	break;

case 'medialist_recursive':
	$title=i18n::translate('List files in subdirectories');
	$text=i18n::translate('When this option is selected, the MultiMedia Objects will search not only the directory selected from the Filter list but all its subdirectories as well. When this option is not selected, only the selected directory is searched.<br /><br />The titles of all media objects found are then examined to determine whether they contain the text entered in the Filter.  The result of these two actions determines the multimedia objects to be listed.');
	break;

case 'menu':
	$title=i18n::translate('Menus');
	$text=i18n::translate('The page headers have drop-down menus associated with each menu icon.<br /><br />When you move your mouse pointer over an icon a sub-menu will appear, if one exists.  When you click on an icon you will be taken to the first item in the sub-menu.<br /><br />The following menu icons are usually available:<ul><li><a href="?help=menu_famtree">Home page</a><br /></li><li><a href="?help=menu_myged">My Page</a><br /></li><li><a href="?help=menu_charts">Charts</a><br /></li><li><a href="?help=menu_lists">Lists</a><br /></li><li><a href="?help=menu_annical">Anniversary calendar</a><br /></li><li><a href="?help=menu_clip">Family tree clippings cart</a><br /></li><li><a href="?help=menu_search">Search</a><br /></li><li><a href="?help=help">Help</a></li></ul>');
	break;

case 'menu_annical':
	$title=i18n::translate('Anniversary calendar menu');
	$text=i18n::translate('The Anniversary Calendar displays the events in a GEDCOM for a given date, month, or year.<ol><li><a href="?help=day_month_help"><b>View Day</b></a></li><li><a href="?help=day_month_help"><b>View Month</b></a></li><li><a href="?help=day_month_help"><b>View Year</b></a><br />These menu items will take you to the Anniversary Calendar to display a list of all the events for the current day, month, or year.</li></ol>');
	break;

case 'menu_charts':
	$title=i18n::translate('Charts menu');
	$text=i18n::translate('The available charts are:<ol><li><a href="?help=help_pedigree.php"><b>Pedigree Tree</b></a><br />This will link you to the Pedigree chart of this GEDCOM file. The pedigree will start with the person configured by the administrator. When you are logged in the starting person can be whoever you have configured in your Account preferences.</li><li><a href="?help=help_descendancy.php"><b>Descendancy Chart</b></a><br />The Descendancy chart is essentially a <a href="?help=help_pedigree.php"><b>Pedigree Tree</b></a> in reverse order.  This comparison is not quite correct, but while the Pedigree chart shows you all the ancestors of a starting person, the Descendancy chart shows you all the descendants of a starting person.</li><li><a href="?help=help_timeline.php"><b>Timeline Chart</b></a><br />Here you view the events of a person along a time line.  It\'s interesting to compare the events of two or more persons along the same time line.</li><li><a href="?help=help_relationship.php"><b>Relationship Chart</b></a><br />Here you can check the relation of a person to yourself or to another person.</li><li><a href="?help=help_ancestry.php"><b>Ancestry Chart</b></a><br />This chart is very similar to the <a href="?help=help_pedigree.php"><b>Pedigree Tree</b></a>, but with more details and alternate <a href="?help=chart_style"><b>Chart style</b></a> displays.</li><li><a href="?help=help_fanchart.php"><b>Circle Diagram</b></a><br />This chart is very similar to the <a href="?help=help_pedigree.php"><b>Pedigree Tree</b></a>, but in a more graphical way.</li></ol>');
	break;

case 'menu_clip':
	$title=i18n::translate('Clippings menu');
	$text=i18n::translate('You will see this item in the menu bar only when the administrator has enabled this feature.<br /><br />The Clippings Cart allows you to store information about individuals, families, and sources in a temporary file that you can later download in GEDCOM 5.5.1 format.');
	break;

case 'menu_famtree':
	$title=i18n::translate('Home page menu');
	$text=i18n::translate('All of this site\'s available genealogical databases are listed in this menu. Each database has its own customized Home page, like this one.  If there is only one database at this site, there is no sub-menu under the Home page icon.');
	break;

case 'menu_lists':
	$title=i18n::translate('Lists menu');
	$text=i18n::translate('The following lists are available:<ol><li><a href="?help=help_indilist.php"><b>Individuals</b></a></li><li><a href="?help=help_famlist.php"><b>Families</b></a><br />In these two lists you can browse alphabetical lists of individuals or families in this GEDCOM.</li><li><a href="?help=help_sourcelist.php"><b>Sources</b></a><br />This item returns a list of all the sources used in the GEDCOM.</li><li><a href="?help=help_placelist.php"><b>Place Hierarchy</b></a><br />Here you can look for people by Place. A two-column list will be returned. Individuals are listed on the left, families on the right.</li><li><a href="?help=help_medialist.php"><b>MultiMedia</b></a><br />You see this menu item only if enabled by the site admin.  This will display links to all multimedia files in this GEDCOM.</li></ol>');
	break;

case 'menu_myged':
	$title=i18n::translate('My page menu');
	$text=i18n::translate('If you are logged in, this menu can include the following items:<ol><li>My Page<br />This takes you to your own customizable Starting page.</li><li>My Account<br />You can edit your personal data here.</li><li>My Pedigree<br />If you have selected a Root person for this GEDCOM, this will take you to the Pedigree chart for that person.</li><li>My Individual Record<br />This link will take you to your Individual Information page, where all genealogical data about yourself and your family is displayed.</li></ol>');
	break;

case 'menu_search':
	$title=i18n::translate('Search menu');
	$text=i18n::translate('The Search page is a more powerful version of the Search box you may find in each page header.');
	break;

case 'messaging2':
	$title=i18n::translate('Internal messaging with emails');
	$text=i18n::translate('When you send this message you will receive a copy sent via email to the address you provided.');
	break;

case 'more_config':
	$title=i18n::translate('More help');
	$text=i18n::translate('More help is available by clicking the <b>?</b> next to items on the page.');
	break;

case 'move_mediadirs':
	$title=i18n::translate('Move media directories');
	$text=i18n::translate('When the Media Firewall is enabled, Multi-Media files can be stored in a server directory that is not accessible from the Internet.<br /><br />These buttons allow you to easily move an entire Media directory structure between the protected (not web-addressable) <b>%s%s</b> and the normal <b>%s</b> directories.', $MEDIA_FIREWALL_ROOTDIR, $MEDIA_DIRECTORY, $MEDIA_DIRECTORY);
	break;

case 'movedown_faq_item':
	$title=i18n::translate('Move FAQ item down');
	$text=i18n::translate('This option will let you move an item downwards on the FAQ page.<br /><br />Each time you use this option, the FAQ Position number of this item is increased by one.  You can achieve the same effect by editing the item in question and changing the FAQ Position field.  When more than one FAQ item has the same position number, only one of these items will be visible.');
	break;

case 'moveup_faq_item':
	$title=i18n::translate('Move FAQ item up');
	$text=i18n::translate('This option will let you move an item upwards on the FAQ page.<br /><br />Each time you use this option, the FAQ Position number of this item is reduced by one.  You can achieve the same effect by editing the item in question and changing the FAQ Position field.  When more than one FAQ item has the same position number, only one of these items will be visible.');
	break;

case 'multi_letter_alphabet':
	$title=i18n::translate('Multi-letter alphabet');
	$text=i18n::translate('Multi-letter combinations that are to be treated as a single distinct letter when sorting lists of names and titles in this language.<br /><br />Some languages, Hungarian and Slovak for example, consider certain combinations of letters to be distinct letters in their own right.  The order in which you specify these letter combinations determines the order in which they are inserted into the normal alphabet during sorting.  This is important when several multi-letter combinations have the same first letter.  Except for <b>ch</b>, these letter combinations are inserted into the normal alphabet according to their first letter.  <b>ch</b> is always inserted after <b>h</b>.');
	break;

case 'multi_letter_equiv':
	$title=i18n::translate('Multi-letter equivalents');
	$text=i18n::translate('In some languages, multiple letters are often treated as equivalent to a single letter when generating lists of names.<br /><br />For example, in Dutch, names beginning with IJ are listed together with names beginning with Y. In Norwegian, names beginning with AA are listed with &Aring;. In some languages, there are letters that can be written as one character or two. For example in Slovakian, the two characters D and \xC5\xBE can be written as the single character \xC7\x85. By specifying equivalents here, you can allow names beginning with these letters to be grouped together on the individual list pages.<br /><br />You should specify a comma-separated list of equivalents. To support databases that don\'t recognize UTF-8 encoding, you should specify both upper and lower case equivalents. This example demonstrates the format to use.<br /><br />Aa=&Aring;,aa=&aring;');
	break;

case 'mygedview_charts':
// duplicate text. see index_charts
	$title=i18n::translate('Charts block');
	$text=i18n::translate('This block allows a pedigree, descendancy, or hourglass chart to appear on My Page or the Home Page.  Because of space limitations, the charts should be placed only on the left side of the page.<br /><br />When this block appears on the Home Page, the root person and the type of chart to be displayed are determined by the administrator.  When this block appears on the user\'s personalized My Page, these options are determined by the user.<br /><br />The behavior of these charts is identical to their behavior when they are called up from the menus.  Click on the box of a person to see more details about them.');
	break;

case 'mygedview_customize':
	$title=i18n::translate('Customize My Page');
	$text=i18n::translate('When you entered here for the first time, you already had some blocks on this page.  If you like, you can customize this My Page.<br /><br />When you click this link you will be taken to a form where you can add, move, or delete blocks.  More explanation is available on that form.');
	break;

case 'mygedview_favorites':
	$title=i18n::translate('Favorites block');
	$text=i18n::translate('Favorites are similar to bookmarks.<br /><br />Suppose you have somebody in the family tree whose record you want to check regularly.  Just go to the person\'s Individual Information page and select the <b>Add to My Favorites</b> option from the Favorites drop-down list. This person is now book marked and added to your list of favorites.<br /><br />Wherever you are on this site, you can click on a name in the "My Favorites" drop-down list in the header.  This will take you to the Individual Information page of that person.');
	break;

case 'mygedview_message':
	$title=i18n::translate('Messages block');
	$text=i18n::translate('In this block you will find the messages sent to you by other users or the admin.  You too can send messages to other users or to the admin.<br /><br />The <b>webtrees</b> mail system is designed to help protect your privacy.  You don\'t have to leave your email address here and others will not be able to see your email address.<br /><br />To expand a message, click on the message subject or the "<b>+</b>" symbol beside it.  You can delete multiple messages by checking the boxes next to the messages you want to delete and clicking on the <b>Delete Selected Messages</b> button.');
	break;

case 'mygedview_myjournal':
	$title=i18n::translate('Journal block');
	$text=i18n::translate('You can use this journal to write notes or reminders for your own use.  When you make such a note, it will still be there the next time you visit the site.<br /><br />These notes are private and will not be visible to others.');
	break;

case 'mygedview_portal':
// duplicate text. see index_myged_help
	$title=i18n::translate('My Page');
	$text=i18n::translate('This is your personal page.<br /><br />Here you will find easy links to access your personal data such as <b>My Account</b>, <b>My Indi</b> (this is your Individual Information page), and <b>My Pedigree</b>.  You can have blocks with <b>Messages</b>, a <b>Journal</b> (like a Notepad) and many more.<br /><br />The layout of this page is similar to the Home Page that you see when you first access this site.  While the parts of the Home Page are selected by the site administrator, you can select what parts to include on this personalized page.  You will find the link to customize this page in the Welcome block or separately when the Welcome block is not present.<br /><br />You can choose from the following blocks:<ul><li><a href="?help=index_charts"><b>Charts</b></a></li><li><a href="?help=mygedview_customize"><b>Customize my page</b></a></li><li><a href="?help=index_stats"><b>GEDCOM statistics</b></a></li><li><a href="?help=index_loggedin"><b>Logged in users</b></a></li><li><a href="?help=mygedview_message"><b>Messages</b></a></li><li><a href="?help=mygedview_favorites"><b>My favorites</b></a></li><li><a href="?help=mygedview_myjournal"><b>My journal</b></a></li><li><a href="?help=index_onthisday"><b>On this day in your history</b></a></li><li><a href="?help=index_media"><b>Random media</b></a></li><li><a href="?help=recent_changes"><b>Recent changes</b></a></li><li><a href="?help=index_events"><b>Upcoming events</b></a></li><li><a href="?help=mygedview_welcome"><b>Welcome</b></a></li></ul>');
	break;

case 'mygedview_stats':
// duplicate text. see index_stats
	$title=i18n::translate('GEDCOM statistics block');
	$text=i18n::translate('In this block you will see some statistics about the current GEDCOM file.  If you need more information than is listed, send a message to the contact at the bottom of the page.');
	break;

case 'mygedview_welcome':
	$title=i18n::translate('Welcome block');
	$text=i18n::translate('The Welcome block shows you:<ul><li>The current GEDCOM file</li><li>The date and time</li><li>Links to:<ul><li>My Account</li><li>My Pedigree</li><li>My Individual Record</li><li>Customize My Page</li></ul></li></ul><br /><b>Note:</b><br />You will see the links to <b>My Indi</b> and <b>My Pedigree</b> only if you are known to the current GEDCOM file.  You might have a record in one GEDCOM file and therefore see the <b>My Indi</b> and <b>My Pedigree</b> links, while in another GEDCOM file you do not have a record and consequently these links are not displayed.');
	break;

case 'name_list':
	$title=i18n::translate('Name list');
	$text=i18n::translate('This box will display either a surname list or a complete name list.  In both cases all surnames will start with the initial letter that you clicked in the Alphabetical index, unless you clicked <b>ALL</b>.<br /><br />Whether you will see a surname list or the complete name list depends on the status of the <b>Skip/Show Surname Lists</b> link.');
	break;

case 'new_dir':
	$title=i18n::translate('Media directory structure');
	$text=i18n::translate('As an admin user you can create the directory structure you require to keep your media files organized. Creating directories from this page ensures that the thumbnail directories are created as well as creating a suitable index.php in each directory.<br /><br />Click on this link to enter the name of the directory you wish to create.');
	break;

case 'new_password':
	$title=i18n::translate('Request new password');
	$text=i18n::translate('If you have forgotten your password, you can click this link to request a new password.<br /><br />You will be taken to the "Lost Password Request" page.');
	break;

case 'new_user':
	$title=i18n::translate('Request user account');
	$text=i18n::translate('If you are a visitor to this site and wish to request a user account, you can click this link.<br /><br />You will be taken to the "Register" page.');
	break;

case 'new_user_realname':
	$title=i18n::translate('Real name');
	$text=i18n::translate('In this box you have to type your real name.<br /><br />We need your first and last names to determine whether you qualify for an account at this site, and what your rights should be.  This name will be visible to other logged-in family members and users.');
	break;

case 'next_path':
	$title=i18n::translate('Find next relationship path');
	$text=i18n::translate('You can click this button to see whether there is another relationship path between the two people.  Previously found paths can be displayed again by clicking the link with the path number.');
	break;

case 'no_update_CHAN':
	$title=i18n::translate('Do not update the CHAN (Last Change) record');
	$text=i18n::translate('Administrators sometimes need to clean up and correct the data submitted by users.  For example, they might need to correct the PLAC location to include the country.  When Administrators make such corrections, information about the original change is normally replaced.  This may not be desirable.<br /><br />When this option is selected, <b>webtrees</b> will retain the original Change information instead of replacing it with that of the current session.  With this option selected, Administrators also have the ability to modify or delete the information associated with the original CHAN tag.');
	break;

case 'oldest_top':
	$title=i18n::translate('Show oldest top');
	$text=i18n::translate('When this check box is checked, the chart will be printed with oldest people at the top.  When it is unchecked, youngest people will appear at the top.<br /><br />Note: This option works only if <b>Line up the same generations</b> is also checked.');
	break;

case 'page':
	$title=i18n::translate('Page');
	$text=i18n::translate('Help');
	break;

case 'password':
	$title=i18n::translate('Password');
	$text=i18n::translate('In this box you type your password.<br /><br /><b>The password is case sensitive.</b>  This means that <b>MyPassword</b> is <u>not</u> the same as <b>mypassword</b> or <b>MYPASSWORD</b>.');
	break;

case 'person_facts':
	$title=i18n::translate('Facts privacy settings by ID');
	$text=i18n::translate('These settings define facts that are hidden for a specific person, family, or source and the level at which they are hidden.  This only applies to level 1 fact records such as BIRT or DEAT that will appear with their own headings on the relevant details page  of the person, family, or source.<br /><br />The first element is the ID of the person, family, or source. The second element is the fact.  The <b>Choice</b> element specifies the fact itself or related details.  The <b>Show to?</b> element determines at what access level the fact is shown.  Not all facts shown in the list are applicable to all types of IDs.  For example, Birth and Death facts are not relevant to Source records.<br /><br />The $person_facts array works the same as the $global_facts array except that you also specify the GEDCOM ID of the person you want to hide facts for. You could, for example, hide the marriage record for a specific person.');
	break;

case 'person_privacy':
	$title=i18n::translate('Privacy settings by ID');
	$text=i18n::translate('These settings allow administrators to override default privacy settings for a particular person, family, source, or media object.<br /><br />Suppose for example you have a child who died in infancy. Normally because the child is dead, its details would be shown to public users. However, you and everyone else in your family are still private. You don\'t want to remove the death record for the child but you want to hide the details and make them private. If this child had the ID of I100 you should enter the following privacy settings:<br />ID: I100<br />Show to: Show only to authenticated users<br /><br />This works the other way as well. If you wanted to make public the details of someone (ID I101) who you know to be dead but don\'t have a death date for, you could add the following:<br />ID: I101<br />Show to: Show to public');
	break;

case 'phpinfo':
	$title=i18n::translate('PHP information');
	$text=i18n::translate('This page provides extensive information about the server on which <b>webtrees</b> is being hosted.  Many configuration details about the server\'s software, as it relates to PHP and <b>webtrees</b>, can be viewed.');
	break;
	
case 'pls_note11':
	$title=i18n::translate('Lost password request');
	$text=i18n::translate('To have your password reset, enter your user name.<br /><br />We will respond by sending you an email to the address registered with your account.  The email will contain a URL and confirmation code for your account. When you visit this URL, you can change your password and login to this site. For security reasons, you should not give this confirmation code to anyone.<br /><br />If you require assistance from the site administrator, please use the contact link below.');
	break;

case 'ppp_default_form':
	$title=i18n::translate('Default order');
	$text=i18n::translate('This means that there is no place encoding format declared in this GEDCOM file and the default format is assumed.<br /><br />If another format had been found, it would have been shown between the <b>(</b> and <b>)</b> at the end of the line.');
	break;

case 'ppp_levels':
	$title=i18n::translate('Location levels');
	$text=i18n::translate('This shows the levels that are displayed now.  The list box showing places is actually a sublist of the leftmost level.<br /><br />EXAMPLE:<br />The default order is City, County, State/Province, Country.<br />If the current level is "Top Level", the box will list all the countries in the database.<br />If the current level is "U.S.A., Top Level", the box will list all the states in the U.S.A.<br />etc.<br /><br />You can click a level to go back one or more steps.');
	break;

case 'ppp_match_one':
	$title=i18n::translate('Place order format');
	$text=i18n::translate('GEDCOM ORDER<br />The locations are assumed to be encoded in the place format explicitly declared in the GEDCOM file.  This overrules the default order.');
	break;

case 'ppp_placelist':
	$title=i18n::translate('Place hierarchy');
	$text=i18n::translate('In this list you can see the locations that are found subordinate to the current location you have chosen.  If you have not yet selected a place, you will see a list of all of the top level locations (e.g. countries or states).<br /><br />The names of the locations in the list are clickable; clicking on a location works like a filter, you will be taken to the next level down.');
	break;

case 'ppp_view_records':
	$title=i18n::translate('View all records');
	$text=i18n::translate('Clicking on this link will show you a list of all of the individuals and families that have events occurring in this place.  When you get to the end of a place hierarchy, which is normally a town or city, the name list will be shown automatically.');
	break;

case 'preview_faq_item':
	$title=i18n::translate('Preview all FAQ items');
	$text=i18n::translate('This option lets an admin user view the FAQ page without all the editing options and links.<br /><br />Except for a single <b>Edit</b> link above the first FAQ item, the appearance of the FAQ page will be identical to what an ordinary user would see. This special <b>Edit</b> link will restore full Edit functionality to the FAQ page.');
	break;

case 'preview':
	$title=i18n::translate('Printer-friendly version');
	$text=i18n::translate('Clicking the Printer-friendly Version link will remove the items that don\'t look good on a printed page (menus, input boxes, extra links, the question marks for the contextual help, etc.)<br /><br />On the Printer-friendly version of the page, you will get a <b>Print</b> link at the bottom of the page. Just click it and your printer dialog will pop up. After printing, just click the <b>Back</b> link and the screen will be rebuilt normally.<br /><br />Note: Although the "Printer-friendly version" removes many links from the displayed page, the remaining links are still clickable.');
	break;

case 'privacy_error':
	$title=i18n::translate('This information is private and cannot be shown.');
	$text=i18n::translate('There are several possible reasons for this message:<br /><br /><ul><li><b>Information on living people is set to "Private"</b><br />Visitors and registered users who are not logged in can see full information only for deceased individuals. If allowed by the system administrator, visitors may register for an account by clicking the Login button, then the Request new password link.<br /></li><li><b>You are a user with user name and password...</b><br />But you have not logged in successfully or you have been inactive for a while and your session timed out.<br /></li><li><b>Due to privacy</b><br />The person does not want to be shown at all (Hidden) and may have asked the admin to set him or her to "Private".  Privacy can be set to:<br /><ol><li>Show only to authenticated users</li><li>Show only to admin users</li><li>Hide even from admin users</li></ol></li><li><b>Out of "Relation Path"</b><br />Even if you are a regular user <u>and</u> logged in, it can still happen that you see this message if the person you are trying to view is not related to you within the number of relationship steps (Relation Path length) set by the site administrator for this GEDCOM.<br /><br />Examples:<br />When the Relation Path length is set to <b>1</b>, you can only see the details of your own family, father, mother, brother, sister (but not the spouses and children of your brother or sister)<br /><br />When the Relation Path is set to <b>2</b>, you can also see the details of your brother\'s wife and their children (but not the spouses of their children).<br /><br />The higher the Relation Path length setting, the more remote relatives you can see.<br /></li></ul><br />If you think that you qualify to see certain hidden details, please contact the site administrator.  Use the contact link on any page.');
	break;

case 'random_media_ajax_controls':
	$title=i18n::translate('Show slideshow controls?');
	$text=i18n::translate('You can use this setting to show or hide the slideshow controls of the Random Media block.<br /><br />These controls allow the user to jump to another random object or to play through randomly selected media like a slideshow. The slideshow changes the contents of the block without preloading information from the server and without reloading the entire page.');
	break;

case 'random_media_filter':
	$title=i18n::translate('Media filter');
	$text=i18n::translate('You can restrict what the Random Media block is permitted to show according to the format and type of media item.  When a given checkbox is checked, the Random Media block is allowed to display media items of that format or type.<br /><br />Format or Type codes that exist in your database but are not in these checkbox lists are assumed to have the corresponding checkbox checked.  For example, if your database contains Media objects of format <b><i>pdf</i></b>, the Random Media block is always permitted to display them.  Similarly, if your database contains Media objects of type <b><i>special</i></b>, the Random Media block is always permitted to display them.');
	break;

case 'random_media_persons_or_all':
	$title=i18n::translate('Show only persons, events, or all?');
	$text=i18n::translate('This option lets you determine the type of media to show.<br /><br />When you select <b>Persons</b>, only media associated with persons will be shown.  Usually, this would be a person\'s photograph.  When you select <b>Events</b>, only media associated with facts or events will be shown.  This might be an image of a certificate.  When you select <b>ALL</b>, this block will show all types of media.');
	break;

case 'random_media_start_slide':
	$title=i18n::translate('Start slideshow on page load?');
	$text=i18n::translate('Should the slideshow start automatically when the page is loaded.<br /><br />The slideshow changes the contents of the block without preloading information from the server and without reloading the entire page.');
	break;

case 'readmefile':
	$title=i18n::translate('Readme file');
	$text=i18n::translate('See <a href="readme.txt" target="_blank"><b>Readme.txt</b></a> for more information.');
	break;

case 'recent_changes':
	$title=i18n::translate('Recent changes block');
	$text=i18n::translate('This block shows you the most recent changes to the GEDCOM as recorded by the CHAN GEDCOM tag.');
	break;

case 'register_comments':
	$title=i18n::translate('Comments');
	$text=i18n::translate('Use this field to tell the site administrator why you are requesting an account and how you are related to the genealogy displayed on this site.  You can also use this to enter any other comments you may have for the site administrator.');
	break;

case 'register_gedcomid':
	$title=i18n::translate('GEDCOM INDI record ID');
	$text=i18n::translate('Every person in the database has a unique ID number on this site.  If you know the ID number for your own record, please enter it here.  If you don\'t know your ID number or could not find it because of privacy settings, please provide enough information in the Comments field to help the site administrator identify who you are on this site so that he can set the ID for you.');
	break;

case 'register_info_01':
	$title=i18n::translate('Request new user account');
	$text=i18n::translate('The amount of data that can be publicly viewed on this website may be limited due to applicable law concerning privacy protection. Many people do not want their personal data publicly available on the Internet. Personal data could be misused for spam or identity theft.<br /><br />Access to this site is permitted to every visitor who has a user account. After the administrator has verified and approved your account application, you will be able to login.<br /><br />If Relationship Privacy has been activated you will only be able to access your own close relatives\' private information after logging in. The administrator can also allow database editing for certain users, so that they can change or add information.<br /><br />If you need any further support, please use the link below to contact the administrator.');
	break;

case 'relationship_id':
	$title=i18n::translate('ID\'s of person 1 and person 2');
	$text=i18n::translate('If you have jumped from another page to this one by having clicked the <b>Relation to me</b> link, you will see here the relationship between yourself and that other individual.<br /><br />If you arrived at this page through the <b>Relationship Chart</b> menu entry on any page header, you have to type the identifier numbers of the two people whose relationship you wish to see.  If you don\'t know the identifier of the desired person, you can click the <b>Find ID</b> link.');
	break;

case 'remove_person':
	$title=i18n::translate('Remove person');
	$text=i18n::translate('Click this link to remove the person from the timeline.');
	break;

case 'remove_tags':
	$title=i18n::translate('Remove custom tags');
	$text=i18n::translate('Checking this option will remove any custom tags that may have been added to the records by <b>webtrees</b>.<br /><br />Custom tags used by <b>webtrees</b> include the <b>_PGVU</b> tag which identifies the user who changed a record online and the <b>_THUM</b> tag which tells <b>webtrees</b> that the image should be used as a thumbnail.<br /><br />Custom tags may cause errors when importing the downloaded GEDCOM to another genealogy application.');
	break;

case 'reorder_children':
	$title=i18n::translate('Reorder children');
	$text=i18n::translate('Children are displayed in the order in which they appear in the family record.  Children are not automatically sorted by birth date because often the birth dates of some of the children are uncertain but the order of their birth <u>is</u> known.<br /><br />This option will allow you to change the order of the children within the family\'s record.  Since you might want to sort the children by their birth dates, there is a button you can press that will do this for you.<br /><br />You can also drag-and-drop any information box to change the order of the children.  As you move the mouse cursor over an information box, its shape will change to a pair of double-headed crossed arrows. If you push and hold the left mouse button before moving the mouse cursor, the information box will follow the mouse cursor up or down in the list.  As the information box is moved, the other boxes will make room.  When you release the left mouse button, the information box will take its new place in the list.');
	break;

case 'reorder_families':
	$title=i18n::translate('Reorder families');
	$text=i18n::translate('Families on the Close Relatives tab are displayed in the order in which they appear in the individual\'s GEDCOM record.  Families are not sorted by the marriage date because often the marriage dates are unknown but the order of the marriages <u>is</u> known.<br /><br />This option will allow you to change the order of the families in which they are listed on the Close Relatives tab.  If you want to sort the families by their marriage dates, there is a button you can press that will automatically do this for you.');
	break;

case 'restore_faq_edits':
	$title=i18n::translate('Restore FAQ edit functionality');
	$text=i18n::translate('This option restores the FAQ page to what an admin user normally sees, so that individual FAQ items may be edited.');
	break;

case 'review_changes':
	$title=i18n::translate('Review GEDCOM changes');
	$text=i18n::translate('This block will list all of the records that have been changed online and that still need to be reviewed and accepted into the database.');
	break;

case 'rootid':
	$title=i18n::translate('Pedigree chart root person');
	$text=i18n::translate('If you want to display a chart with a new starting (root) person, the ID of that new starting person is typed here.<br /><br />If you don\'t know the ID of that person, use the <b>Find ID</b> link.<br /><br /><b>ID NUMBER</b><br />The ID numbers used inside <b>webtrees</b> are <u>not</u> the identification numbers issued by various governments (driving permit or passport numbers, for instance).  The ID number referred to here is simply a number used within the database to uniquely identify each individual; it was assigned by the ancestry program that created the GEDCOM file which was imported into <b>webtrees</b>.');
	break;

case 'rss_feed':
	$title=i18n::translate('RSS feed settings');
	$text=i18n::translate('The ATOM/RSS feed available in <b>webtrees</b> allows anyone to view, using a suitable feed aggregator, the contents of your site\'s Home Page without visiting the site. Most aggregators will pop up a notice letting the user know when something has changed on a page being monitored. This essentially allows anyone to monitor your <b>webtrees</b> site without needing to visit it regularly.<br /><br />The Feed block is used to customize the link to the feed, allowing specific feed types (most readers can deal with most types so this can usually be left at the default), and the specific module you would like in your feed. The language of the feed and the GEDCOM used will be based on the language and GEDCOM active in <b>webtrees</b> when you select the feed.<br /><br />The types of feed that can be generated include ATOM, RSS 2.0, RSS 1.0, RSS 0.92, HTML and JavaScript. The first four types are for feed aggregators, while JavaScript and HTML are meant to enable inclusion of the feeds in other web pages.  Note that the numbers of the RSS feed indicate different styles, not a different version.<br /><br />There is an option to select authentication that will log the user in, and allow the user to view, using a suitable RSS aggregator, any information that he could normally view if logged in. Basic Authentication uses <i>Basic HTTP Authentication</i> to log the user in. Future enhancements might allow <i>Digest Authentication</i>.<br /><br />This <a href=\'http://en.wikipedia.org/wiki/RSS_(file_format)\' target=\'_blank\' alt=\'Wikipedia article\' title=\'Wikipedia article\'><b>Wikipedia article</b></a> contains comprehensive information and links about RSS and the various RSS formats. <i>Basic HTTP Authentication</i> is discussed in this <a href=\'http://en.wikipedia.org/wiki/Basic_authentication_scheme\' target=\'_blank\' alt=\'Wikipedia article\' title=\'Wikipedia article\'><b>Wikipedia article</b></a>, while <i>Digest Authentication</i> is discussed in this <a http://en.wikipedia.org/wiki/Digest_access_authentication\' target=\'_blank\' alt=\'Wikipedia article\' title=\'Wikipedia article\'><b>Wikipedia article</b></a>.');
	break;

case 'search':
	$title=i18n::translate('Search information');
	$text=i18n::translate('This search box is small but powerful.  You can have <b>webtrees</b> search almost anything for you. When you click the <b>></b> or <b>Search</b> button, you will be linked to the Search page to see the results of your search.  You will find extensive help about searching options on the Search page.<ul><li><a href="?help=search.php">Search page</a></li><li><a href="?help=search_enter_terms">Enter search terms</a></li><li><a href="?help=soundex_search">Soundex search</a></li><li><a href="?help=search_replace">Search and replace</a></li></ul>');
	break;

case 'search_enter_terms':
	$title=i18n::translate('Enter search terms');
	$text=i18n::translate('In this Search box you can enter criteria such as dates, given names, surnames, places, multimedia, etc.<br /><br /><b>Wildcards</b><br />Wildcards, as you probably know them (like * or ?), are not allowed, but the program will automatically assume wildcards.<br /><br />Suppose you type in the Search box the following: <b>Pete</b>.  The result could be, assuming the names are in the database:<div style="padding-left:30px;"><b>Pete</b> Smith<br /><b>Pete</b>r Johnes<br />Will <b>Pete</b>rson<br />somebody --Born 01 January 1901 <b>Pete</b>rsburg<br />etc.</div><br /><b>Dates</b><br />Typing a year in the Search box will result in a list of individuals who are somehow connected to that year.<br /><br />If you type <b>1950</b>, the result will be all individuals with an event that occurred in 1950.  These events could be births, deaths, marriages, Bar Mitzvahs, LDS Sealings, etc.<br /><br />If you type <b>4 Dec</b>, all persons connected to an event that occurred on 4 December of whatever year will be listed.  Persons connected to an event on 14 or 24 December will be listed as well.  As you see, wildcards are always assumed, so you do not have to type them.  Sometimes, the results can be surprising.<br /><br /><b>Proper dates</b><br /><b>webtrees</b> searches for data, as they are stored in the GEDCOM file.  If, for example, you want to search for an event on December 14, you should type <b>14&nbsp;dec</b> because this is how the date is stored in the database.<br /><br />If you were to type <b>dec&nbsp;14</b>, the result could be a person connected to an event on 08&nbsp;<b>dec</b>ember&nbsp;18<b>14</b>.  Again, the results can be surprising.<br /><br />You can use regular expressions in your search if you are familiar with them.  For example, if you wanted to find all of the people who have dates in the 20th century, you could enter the search <b>19[0-9][0-9]</b> and you would get all of the people with dates from 1900-1999.<br /><br />If you need more help with this searching system, please let us know, so that we can extend this Help file as well.<br /><br />~Search the way you think the name is written (Soundex)~<br /><br />Soundex is a method of coding words according to their pronunciation.  This allows you to search the database for names and places when you don\'t know precisely how they are written.  <b>webtrees</b> supports two different Soundex algorithms that produce vastly different results.<ul><li><b>Basic</b><br />This method, patented in 1918 by Russell, is very simple and can be done by hand.<br /><br />Because the Basic method retains the first letter of the name as part of the resultant code, it is not very helpful when you are unsure of that first letter.  The Basic algorithm is not well suited to names that were originally in languages other than English, and even with English names the results are very surprising.  For example, a Basic Soundex search for <b>Smith</b> will return not only <b>Smith, Smid, Smit, Schmidt, Smyth, Smithe, Smithee, Schmitt</b>, all of which are clearly variations of <b>Smith</b>, but also <b>Smead, Sneed, Smoote, Sammett, Shand,</b> and <b>Snoddy</b>.  <br /><br /></li><li><b>Daitch-Mokotoff</b><br />This method, developed in 1985, is much more complex than the Basic method and is not easily done by hand.<br /><br />A Soundex search using this method produces much more accurate results.</li></ul>For details on both Soundex algorithms, visit this <a href="http://www.jewishgen.org/infofiles/soundex.html" target=_blank><b>Jewish Genealogical Society</b></a> web page.<br /><br /> ~Search and Replace~<br /><br />Here, you can search for a misspelling or other inaccurate information and replace it with correct information.<br /><br /><b>Searching</b><br />This feature performs searching just like a <a href="help_text.php?help=search_enter_terms_help">normal search</a>.<br /><br /><b>Replacing</b><br />All instances of the search term that are found are replaced by the replacement term in the database.<br /><br /><b>For Example...</b><br />Suppose you accidentally misspell your great-grandpa Michael\'s name.  You accidentally entered \'Micheal.\' <br /><br />You would type <b>Micheal</b> in the Search box, and <b>Michael</b> in the Replace box.<br />Every instance of "Micheal" would then be replaced by "Michael"<br /><br /><b>Search for...</b><br />Select the scope of the search.  You can limit the search to names or places, or apply no limit (search everything).  The <i>Whole words only</i> option will only search for your term in the place field as a whole word.  This means that searching for <i>UT</i> would only match <b>UT</b> and not <i>UT</i> in the other words such as Connectic<b>ut</b>.<br /><br />Don\'t worry if you accidentally replace something where you don\'t want to.  Just click the "Accept/Reject Changes" link at the bottom of the page to accept the changes you want, and reject the changes you don\'t want.<br /><br />If you need more help with this searching system, please let us know, so that we can improve this Help file as well.');
	break;

case 'search_exclude_tags':
	$title=i18n::translate('Exclude filter');
	$text=i18n::translate('The <b>Exclude some non-genealogical data</b> choice will cause the Search function to ignore the following GEDCOM tags:<div style="padding-left:30px;"><b>_PGVU</b> - Last change by<br /><b>CHAN</b> - Last change date<br /><b>FILE</b> - External File<br /><b>FORM</b> - Format<br /><b>TYPE</b> - Type<br /><b>SUBM</b> - Submitter<br /><b>REFN</b> - Reference Number</div><br />In addition to these optionally excluded tags, the Search function always excludes these tags:<div style="padding-left:30px;"><b>_UID</b> - Globally unique Identifier<br /><b>RESN</b> - Restriction</div>');
	break;

case 'search_include_ASSO':
	$title=i18n::translate('Associates');
	$text=i18n::translate('This option causes <b>webtrees</b> to show all individuals who are recorded as having an association relationship to the person or family that was found as a direct result of the search.  The inverse, where all persons or families are shown when a person found as a direct result of the search has an association relationship to these other persons or families, is not possible.<br /><br />Example:  Suppose person <b>A</b> is godparent to person <b>B</b>.  This relationship is recorded in the GEDCOM record of person <b>B</b> by means of an ASSO tag.  No corresponding tag exists in the GEDCOM record of person <b>A</b>.<br /><br />When this option is set to <b>Yes</b> and the Search results list includes <b>B</b>, <b>A</b> will be included automatically because of the ASSO tag in the GEDCOM record of <b>B</b>.  However, if the Search results list includes <b>A</b>, <b>B</b> will not be included automatically since there is no matching ASSO tag in the GEDCOM record of person <b>A</b>.');
	break;

case 'search_replace':
	$title=i18n::translate('Search and replace');
	$text=i18n::translate('Here, you can search for a misspelling or other inaccurate information and replace it with correct information.<br /><br /><b>Searching</b><br />This feature performs searching just like a <a href="help_text.php?help=search_enter_terms_help">normal search</a>.<br /><br /><b>Replacing</b><br />All instances of the search term that are found are replaced by the replacement term in the database.<br /><br /><b>For Example...</b><br />Suppose you accidentally misspell your great-grandpa Michael\'s name.  You accidentally entered \'Micheal.\' <br /><br />You would type <b>Micheal</b> in the Search box, and <b>Michael</b> in the Replace box.<br />Every instance of "Micheal" would then be replaced by "Michael"<br /><br /><b>Search for...</b><br />Select the scope of the search.  You can limit the search to names or places, or apply no limit (search everything).  The <i>Whole words only</i> option will only search for your term in the place field as a whole word.  This means that searching for <i>UT</i> would only match <b>UT</b> and not <i>UT</i> in the other words such as Connectic<b>ut</b>.<br /><br />Don\'t worry if you accidentally replace something where you don\'t want to.  Just click the "Accept/Reject Changes" link at the bottom of the page to accept the changes you want, and reject the changes you don\'t want.<br /><br />If you need more help with this searching system, please let us know, so that we can improve this Help file as well.');
	break;

case 'setperms':
	$title=i18n::translate('Set media permissions');
	$text=i18n::translate('Recursively set the permissions on the protected (not web-addressable) <b>%s%s</b> and the normal <b>%s</b> directories to either world-writable or read-only.', $MEDIA_FIREWALL_ROOTDIR, $MEDIA_DIRECTORY, $MEDIA_DIRECTORY);
	break;

case 'showUnknown':
	$title=i18n::translate('Show unknown gender');
	$text=i18n::translate('Hide or show the list of given names of persons of unknown gender.<br /><br />The Top 10 Given Names block always hides the list of given names when no persons of that gender exist in your database.  This option lets you hide the list of persons of unknown gender even when there are such persons in your database.');
	break;

case 'show_age_marker':
	$title=i18n::translate('Show age marker');
	$text=i18n::translate('If you check this box, you will see an Age marker.<br /><br />You can slide this Age marker up or down along the time line.  The sliding Age marker is a nice tool to check the age of a person at a certain event.  You can enable or disable the Age marker individually for each person in the chart.');
	break;

case 'show_changes':
	$title=i18n::translate('This record has been updated.  Click here to show changes.');
	$text=i18n::translate('When you see this message, it means two things:<ol><li>Somebody has made changes to the GEDCOM<br />Records may have been added, deleted, or changed.</li><li>The changes have not yet been accepted by the administrator.<br />Once the changes have been accepted or rejected, you will no longer see this message.</li></ol>You can see what changes have been made when you click the link.  If you notice that a change is not correct, please notify the admin.');
	break;

case 'show_context':
	$title=i18n::translate('Show contextual help');
	$text=i18n::translate('Show contextual help');
	break;

case 'show_fact_sources':
	$title=i18n::translate('Show all sources');
	$text=i18n::translate('When this option is checked, you can see all Source or Note records for this person.  When this option is unchecked, Source or Note records that are associated with other facts for this person will not be shown.');
	break;

case 'show_fam_gedcom':
	$title=i18n::translate('Show GEDCOM record');
	$text=i18n::translate('The information about the family, as it is stored in the database, will be displayed when you click this link.  The display will show raw GEDCOM data.');
	break;

case 'show_fam_timeline':
	$title=i18n::translate('Show couple on timeline chart');
	$text=i18n::translate('When you click this link you will jump to the Timeline page, where all facts of the couple will be displayed on a timeline scale.');
	break;

case 'show_full':
	$title=i18n::translate('Hide or show details');
	$text=i18n::translate('With this option you can either show or hide all details in the Name boxes.  You can display more boxes on one screen when the details are hidden.<br /><br />When all details are hidden, the Zoom icon described below is not shown.  However, if the administrator has enabled the Zoom function, the entire box will act like a Zoom icon to reveal full details about the person.<br /><br />When the details are not hidden and the Zoom function, identified by a magnifying glass icon, has been enabled by the administrator, you can reveal even more details about that person.  If you normally have to click on the Zoom icon to zoom in, you can reveal additional hidden details by clicking that icon here.  Similarly, if you can zoom in by hovering over the Zoom icon, hidden details will be revealed by hovering over that icon here.<br /><br />If you have clicked on the Zoom icon to reveal more details, you can restore the box to its normal level of detail by clicking on the Zoom icon again.  If you have revealed more details by simply moving the mouse pointer over the Zoom icon, the box will be restored to its normal level of detail when you move the mouse pointer away from the Zoom icon.');
	break;

case 'show_marnms':
	$title=i18n::translate('Include married names');
	$text=i18n::translate('The individual and family list pages can either include or exclude married names.  This option can be helpful when searching for individuals or families where you only know the married name.  Married names can only be included if they already exist in the database.<br /><br />On the family list, this value defaults to exclude.  On the individual list, the default value is set in the GEDCOM Configuration page.<br /><br />When you change this option, your selection will be remembered until you log off or your session ends.');
	break;

case 'show_spouse':
	$title=i18n::translate('Show spouses');
	$text=i18n::translate('By default this chart does not show spouses for the descendants because it makes the chart harder to read and understand.  Turning this option on will show spouses on the chart.');
	break;

case 'show_thumb':
	$title=i18n::translate('Show thumbnails');
	$text=i18n::translate('Thumbnails will be shown if you check this box.');
	break;

case 'simple_filter':
	$title=i18n::translate('Simple search filter');
	$text=i18n::translate('Simple search filter based on the characters entered, no wildcards are accepted.');
	break;

case 'skip_sublist':
	$title=i18n::translate('Skip surname lists');
	$text=i18n::translate('The standard setting is that, after you have clicked a letter of the Alphabetical index, you will get a sub-list with surnames.  If you click this link, all individuals with surnames that have the currently selected initial letter will be displayed immediately. Thereafter, the list of individuals will be displayed directly whenever you click on a new initial letter in the Alphabetical list.<br /><br />To reverse this action, click on the Show Surname lists link.');
	break;

case 'sort_style':
	$title=i18n::translate('Sort style');
	$text=i18n::translate('This option controls how the information is sorted.<br /><br />When you select <b>Alphabetically</b>, the information is shown in alphabetical order. When you select <b>By Anniversary</b>, the information is ordered by anniversary, with the most recent anniversaries first.');
	break;

case 'sortby':
	$title=i18n::translate('Sequence');
	$text=i18n::translate('Select the order in which you wish to see the list.');
	break;

case 'soundex_search':
	$title=i18n::translate('Search the way you think the name is written (Soundex)');
	$text=i18n::translate('Soundex is a method of coding words according to their pronunciation.  This allows you to search the database for names and places when you don\'t know precisely how they are written.  <b>webtrees</b> supports two different Soundex algorithms that produce vastly different results.<ul><li><b>Basic</b><br />This method, patented in 1918 by Russell, is very simple and can be done by hand.<br /><br />Because the Basic method retains the first letter of the name as part of the resultant code, it is not very helpful when you are unsure of that first letter.  The Basic algorithm is not well suited to names that were originally in languages other than English, and even with English names the results are very surprising.  For example, a Basic Soundex search for <b>Smith</b> will return not only <b>Smith, Smid, Smit, Schmidt, Smyth, Smithe, Smithee, Schmitt</b>, all of which are clearly variations of <b>Smith</b>, but also <b>Smead, Sneed, Smoote, Sammett, Shand,</b> and <b>Snoddy</b>.  <br /><br /></li><li><b>Daitch-Mokotoff</b><br />This method, developed in 1985, is much more complex than the Basic method and is not easily done by hand.<br /><br />A Soundex search using this method produces much more accurate results.</li></ul>For details on both Soundex algorithms, visit this <a href="http://www.jewishgen.org/infofiles/soundex.html" target=_blank><b>Jewish Genealogical Society</b></a> web page.');
	break;

case 'stat':
	$title=i18n::translate('Options for statistics plots');
	$text=i18n::translate('A number of different plots of statistics from your database can be produced.<br /><br />Select the chart, then adjust the options from the drop-down boxes.<br /><br />The numbers included in each plot depend on the data available. For example, individuals without a month of birth (e.g. just \'1856\') cannot be included in a plot of births by month.');
	break;

case 'stat_gax':
	$title=i18n::translate('Select the desired age interval');
	$text=i18n::translate('For example, <b>interval 10 years</b> describes the following set of age ranges:<div style=\"padding-left:30px;\">younger than one year<br />one year to 5 years<br />6 to 10<br />11 to 20<br />21 to 30<br />31 to 40<br />41 to 50<br />51 to 60<br />61 to 70<br />71 to 80<br />81 to 90<br />91 to 100<br />older than 100 years</div>');
	break;

case 'stat_gbx':
	$title=i18n::translate('Select the desired age interval');
	$text=i18n::translate('For example, <b>interval 2 years</b> describes the following set of age ranges:<div style=\"padding-left:30px;\">younger than 16 years<br />16 to 18<br />19 to 20<br />21 to 22<br />23 to 24<br />25 to 26<br />27 to 28<br />29 to 30<br />31 to 32<br />33 to 35<br />36 to 40<br />41 to 50<br />older than 50 years</div>');
	break;

case 'stat_gcx':
	$title=i18n::translate('Select the desired count interval');
	$text=i18n::translate('For example, <b>interval one child</b> describes the following set of child count ranges:<div style=\"padding-left:30px;\">without children<br />one child<br />two children<br />3, 4, 5, 6, 7, 8, 9, 10 children<br />more than 10 children</div>');
	break;

case 'stat_gwx':
	$title=i18n::translate('Select the desired age interval');
	$text=i18n::translate('For example, <b>months after marriage</b> describes the following set of month ranges:<div style=\"padding-left:30px;\">before the marriage<br />from the marriage to 8 months after<br />from 8 to 12<br />from 12 to 15<br />from 15 to 18<br />from 18 to 24<br />from 24 to 48<br />over 48 months after the marriage</div><br /><br />When you want to show quarters you have to choose: <b>quarters</b>');
	break;

case 'stat_gwz':
	$title=i18n::translate('Boundaries for Z axis');
	$text=i18n::translate('Select the desired starting year and interval<br /><br />For example, <b>from 1700 interval 50 years</b> describes the following set of date ranges:<div style=\"padding-left:30px;\">before 1700<br />1700 to 1749<br />1750 to 1799<br />1800 to 1849<br />1850 to 1899<br />1900 to 1949<br />1950 to 1999<br />2000 or later</div>');
	break;

case 'stat_x':
	$title=i18n::translate('X axis');
	$text=i18n::translate('The following options are available for the X axis (horizontal). Each will then be presented according to options set for the Y and Z axes.<p style=\"padding-left: 25px\"><b>Month of birth</b>&nbsp;&nbsp;individuals born in each month.<br /><b>Month of death</b>&nbsp;&nbsp;individuals who died in each month.<br /><b>Month of marriage</b>&nbsp;&nbsp;marriages that occurred in each month.<br /><b>Month of birth of first child in a relation</b>&nbsp;&nbsp;the number of first-borns for each family by month.<br /><b>Month of first marriage</b>&nbsp;&nbsp;the number of first marriages per month.<br /><b>Months between marriage and first child</b>&nbsp;&nbsp;the number of months between marriage and birth of first child to that couple.<br /><b>Age related to birth year</b>&nbsp;&nbsp;age at death, related to the time period that includes each person\'s birth year.<br /><b>Age related to death year</b>&nbsp;&nbsp;age at death, related to the time period that includes each person\'s year of death.<br /><b>Age in year of marriage</b>&nbsp;&nbsp;the average age of individuals at the time of their marriages.<br /><b>Age in year of first marriage</b>&nbsp;&nbsp;the average age of individuals at the time of their first marriage.<br /><b>Number of children</b>&nbsp;&nbsp;average family sizes.<br /><b>Individual distribution</b>&nbsp;&nbsp;placement of all persons or persons with the specified name, by country.<br /><b>Birth by country</b>&nbsp;&nbsp;country of birth.<br /><b>Marriage by country</b>&nbsp;&nbsp;country of marriage.<br /><b>Death by country</b>&nbsp;&nbsp;country of death.<br /><b>Individuals with sources</b>&nbsp;&nbsp;pie chart of individuals with sources.<br /><b>Families with sources</b>&nbsp;&nbsp;pie chart of families with sources.</p>');
	break;

case 'stat_y':
	$title=i18n::translate('Y axis');
	$text=i18n::translate('The following options are available for the Y axis (vertical). These options alter the way the items presented on the X axis are displayed.<p style=\"padding-left: 25px\"><b>numbers</b>&nbsp;&nbsp;displays the number of individuals in each category defined by the X axis.<br /><b>percentage</b>&nbsp;&nbsp;calculates and diplays the proportion of each item in the X axis categories.</p>');
	break;

case 'stat_z':
	$title=i18n::translate('Z axis');
	$text=i18n::translate('The following options are available for the Z axis. These options provide a sub-division of the categories selected for the X axis.<p style=\"padding-left: 25px\"><b>none</b>&nbsp;&nbsp;displays the items as a single column for each X axis category.<br /><b>gender</b>&nbsp;&nbsp;displays the items in 2 columns (male and female) for each X axis category.<br /><b>date periods</b>&nbsp;&nbsp;displays the items in a number of columns related to the time periods set in the next section, for each X axis category.</p>');
	break;

case 'style':
	$title=i18n::translate('Presentation style');
	$text=i18n::translate('This option controls how the information is presented.<br /><br />When you select <b>List</b>, the information is shown in text form, similar to what you see in the various Chart boxes.  This format is well suited to blocks that print on the right side of the page.<br /><br />When you select <b>Table</b>, the information is shown in tabular format, and is more suited to the larger blocks that print on the left side of the page.');
	break;

case 'talloffset':
	$title=i18n::translate('Page layout');
	$text=i18n::translate('With this option you determine the page layout orientation.<br /><br />Changing this setting might be useful if you want to make a screen print or if you have a different type of screen.<ul><li><b>Portrait</b> mode will make the tree taller, such that a 4 generation chart should fit on a single page printed vertically.</li><li><b>Landscape</b> mode will make a wider tree that should print on a single page printed horizontally.</li><li><b>Oldest at top</b> mode rotates the chart, but not its boxes, by 90 degrees counter-clockwise, so that the oldest generation is at the top of the chart.</li><li><b>Oldest at bottom</b> mode rotates the chart, but not its boxes, by 90 degrees clockwise, so that the oldest generation is at the bottom of the chart.</li></ul');
	break;

case 'time_limit':
	$title=i18n::translate('Time limit:');
	$text=i18n::translate('The maximum time the import is allowed to run for processing the GEDCOM file.');
	break;

case 'timeline_control':
	$title=i18n::translate('Timeline control');
	$text=i18n::translate('Click the drop down menu to change the speed at which the timeline scrolls.<br/><br/>~Begin Year~<br/>Enter the starting year of the range.<br/><br/>~End Year~<br/>Enter the ending year of the range.<br/><br/>~Search~<br/>Click the Search button to begin searching for events that occurred within the range identified by the Begin Year and End Year fields.');
	break;

case 'todo':
	$title=i18n::translate('"To Do" block');
	$text=i18n::translate('This block helps you keep track of <b>_TODO</b> tasks in the database.<br /><br />To add &quot;To Do&quot; tasks to your records, you may first need amend the GEDCOM configuration so that the <b>_TODO</b> fact is in the list of facts that can be added to the records of individuals, families, sources, and repositories.  Each of these lists, which you will find in the Edit Options section of the GEDCOM configuration, is independent.  The order of the list entries is not important; you can add the new entries at the beginning of each list.');
	break;

case 'todo_show_future':
	$title=i18n::translate('Show future tasks');
	$text=i18n::translate('Show &quot;To Do&quot; tasks that have a date in the future.  Otherwise only items with a date in the past are shown.');
	break;

case 'todo_show_other':
	$title=i18n::translate('Show other users\' tasks');
	$text=i18n::translate('Show &quot;To Do&quot; tasks assigned to other users');
	break;

case 'todo_show_unassigned':
	$title=i18n::translate('Show unassigned tasks');
	$text=i18n::translate('Show &quot;To Do&quot; tasks that are not assigned to any user');
	break;

case 'upload_media_file':
	$title=i18n::translate('Media file to upload');
	$text=i18n::translate('In this field you specify the location and name, on your local computer, of the media file you wish to upload to the server.  You can use the <b>Browse</b> button to search your local computer for the desired file.<br /><br />The uploaded file will have the same name on the server, and it will be uploaded to the directory specified in the <b>Folder on server</b> field.<br /><br />If you do not see the <b>Folder on server</b> field or cannot change it, you do not have sufficient permissions or your GEDCOM configuration has been set to allow no directory levels beyond the default <b>%s</b>.  In this case, the media file will be uploaded to the directory <b>%s</b>.', $MEDIA_DIRECTORY, $MEDIA_DIRECTORY);
	break;

case 'upload_media_folder':
	$title=i18n::translate('Upload media folder');
	$text=i18n::translate('Your GEDCOM configuration allows up to %s directory levels beyond the default <b>%s</b> where uploaded media files are normally stored. This lets you organize your media files, and you don\'t need to be as concerned about maintaining unique names for each media file.<br /><br />In this field you specify the destination directory on your server where the uploaded media file is to be stored.  Be sure to pay attention to the case (upper or lower case) of what you enter or select here, since file and directory names are case sensitive.<br /><br />If the directory name you enter here does not exist, it will be created automatically. If you enter more than the additional %s directory levels permitted by your GEDCOM configuration, your input will be truncated accordingly.<br /><br />Thumbnails will be uploaded or created in an identical directory structure, starting with <b>%sthumbs/</b>.', $MEDIA_DIRECTORY_LEVELS, $MEDIA_DIRECTORY, $MEDIA_DIRECTORY_LEVELS, $MEDIA_DIRECTORY);
	break;

case 'upload_media':
	$title=i18n::translate('Upload media files');
	$text=i18n::translate('Select files from your local computer to upload to your server.  All files will be uploaded to the directory <b>%s</b> or to one of its sub-directories.<br /><br />Folder names you specify will be appended to <b>%s</b>. For example, <b>%smyfamily</b>. If the thumbnail directory does not exist, it is created automatically.', $MEDIA_DIRECTORY, $MEDIA_DIRECTORY, $MEDIA_DIRECTORY);
	break;

case 'upload_path':
	$title=i18n::translate('Upload path');
	$text=i18n::translate('This is the path where the GEDCOM file you wish to upload can be found. To select the path, click on <b>Browse</b> and navigate to your GEDCOM file and then click <b>Open</b>.');
	break;

case 'upload_server_file':
	$title=i18n::translate('File name on server');
	$text=i18n::translate('The media file you are uploading can be, and probably should be, named differently on the server than it is on your local computer.  This is so because often the local file name has meaning to you but is much less meaningful to others visiting this site.  Consider also the possibility that you and someone else both try to upload different files called "granny.jpg".<br /><br />In this field, you specify the new name of the file you are uploading.  The name you enter here will also be used to name the thumbnail, which can be uploaded separately or generated automatically.  You do not need to enter the file name extension (jpg, gif, pdf, doc, etc.)<br /><br />Leave this field blank to keep the original name of the file you have uploaded from your local computer.');
	break;

case 'upload_server_folder':
	$title=i18n::translate('Folder name on server');
	$text=i18n::translate('The administrator has enabled up to %s folder levels below the default <b>%s</b>.  This helps to organize the media files and reduces the possibility of name collisions.<br /><br />In this field, you specify the destination folder where the uploaded media file should be stored.  The matching thumbnail file, either uploaded separately or generated automatically, will be stored in a similar folder structure starting at <b>%sthumbs/</b> instead of <b>%s</b>.  You do not need to enter the <b>%s</b> part of the destination folder name.<br /><br />If you are not sure about what to enter here, you should contact your site administrator for advice.', $MEDIA_DIRECTORY_LEVELS, $MEDIA_DIRECTORY, $MEDIA_DIRECTORY, $MEDIA_DIRECTORY);
	break;

case 'upload_thumbnail_file':
	$title=i18n::translate('Thumbnail to upload');
	$text=i18n::translate('In this field you specify the location and name, on your local computer, of the thumbnail file you wish to upload to the server.  You can use the <b>Browse</b> button to search your local computer for the desired file.  When this field is filled in, the <b>Automatic thumbnail</b> checkbox is ignored.<br /><br />If the <b>Media file to upload</b> field has been filled in, your uploaded thumbnail file will be named according to the contents of that field, regardless of what it is called on your local computer.  If that field is empty, the uploaded thumbnail file will be copied to two places on the server, once into the server directory mentioned in the <b>Folder on server</b> field, and then again into an identical directory structure starting with <b>%sthumbs/</b>.<br /><br />If you do not see the <b>Folder on server</b> field or cannot change it, you do not have sufficient permissions or your GEDCOM configuration has been set to allow no directory levels beyond the default <b>%s</b> where uploaded media files are normally stored.', $MEDIA_DIRECTORY, $MEDIA_DIRECTORY);
	break;

case 'user_privacy':
	$title=i18n::translate('User privacy settings');
	$text=i18n::translate('These settings give administrators the ability to override default privacy settings for individuals in the GEDCOM based on Username.  Suppose you don\'t want the Username <b>John</b> to be able to see any details of ID I100 in the GEDCOM, you could configure it like this:<br />Username: John<br />ID: I100<br />Show?: "Hide"<br /><br />and details for the specified individual would be hidden for the Username "John" only.<br /><br />To show the details of I101 (which usually would be hidden because I101 is still alive) to Username "John" set:<br /><br />Username: John<br />ID: I101<br />Show?: "Show"');
	break;

case 'useradmin':
	$title=i18n::translate('User administration');
	$text=i18n::translate('On this page you can administer the current users and add new users.<br /><br /><b>User List</b><br />In this table the current users, their status, and their rights are displayed.  You can <b>delete</b> or <b>edit</b> users.<br /><br /><b>Add a new user</b><br />This form is almost the same as the one users see on the  <b>My Account</b> page.<br /><br />For several subjects we did not make special Help text for the administrator. In those cases you will see the following message:');
	break;

case 'useradmin_auto_accept':
	$title=i18n::translate('Automatically accept changes made by this user');
	$text=i18n::translate('By checking this box you are allowing the system to automatically accept any edit changes made by this user.  The user must also have accept privileges on the GEDCOM in order for this setting to take effect.');
	break;

case 'useradmin_can_admin':
	$title=i18n::translate('User can administer check box');
	$text=i18n::translate('If this box is checked, the user will have the same rights that you have.<dl><dt>These rights include:</dt><dd>Add / Remove / Edit Users</dd><dd>Broadcast messages to all users</dd><dd>Edit Welcome messages</dd><dd>Edit and configure language files</dd><dt></dt><dd>Upgrade <b>webtrees</b></dd><dd>Change program and GEDCOM configurations</dd><dd>Administer the GEDCOMs</dd><dd>Change Privacy settings</dd><dd>And anything else that is not mentioned here.</dd></dl><br />The user <u>cannot</u> change anything on your server outside <b>webtrees</b>.');
	break;

case 'useradmin_can_edit':
	$title=i18n::translate('Access level');
	$text=i18n::translate('The user can have different access and editing privileges for each genealogical database in the system.<ul><li><b>None:</b> The user cannot access the private data in this GEDCOM.</li><li><b>Access:</b> The user cannot edit or accept data into the database but can see the private data.</li><li><b>Edit:</b> The user can edit values but another user with <b>Accept</b> privileges must approve the changes before they are added to the database and made public.</li><li><b>Accept:</b> The user can edit.  He can also edit and approve changes made by other users.</li><li><b>Admin GEDCOM:</b> The user edit and approve changes made by other users.  The user can also edit configuration and privacy settings for <u>this</u> GEDCOM.</li></ul>System administrators, identified through the <b>User can administer</b> check box, are automatically given <b>Admin GEDCOM</b> privileges.');
	break;

case 'useradmin_edit_user':
	$title=i18n::translate('Update user account');
	$text=i18n::translate('This form is used by the administrator to change a user\'s account<br /><br />The form is very similar to the <b>Add a new user</b> and <b>Update MyAccount</b> forms.');
	break;

case 'useradmin_editaccount':
	$title=i18n::translate('Edit account information');
	$text=i18n::translate('If this box is checked, this user will be able to edit his account information.  Although this is not generally recommended, you can create a single user name and password for multiple users.  When this box is unchecked for all users with the shared account, they are prevented from editing the account information and only an administrator can alter that account.');
	break;

case 'useradmin_gedcomid':
	$title=i18n::translate('GEDCOM INDI record ID');
	$text=i18n::translate('The GEDCOM INDI record ID identifies the user.  It has to be set by the administrator.<br /><br />This ID is used as the ID on several pages such as <b>My Individual Record</b> and <b>My Pedigree</b>.<br /><br />You can set the user\'s GEDCOM ID separately for each GEDCOM.  If a user does not have a record in a GEDCOM, you leave that box empty.');
	break;

case 'useradmin_path_length':
	$title=i18n::translate('Maximum relationship privacy path length');
	$text=i18n::translate('If <i>Limit access to related people</i> is enabled, this user will only be able to see or edit living individuals within this number of relationship steps.');
	break;

case 'useradmin_relation_priv':
	$title=i18n::translate('Limit access to related people');
	$text=i18n::translate('If this box is checked, the user will only be allowed access to living people that they are related to.  They will be able to see anyone who is within the relationship path length set by their <i>Max relationship privacy path length</i> setting.  You can require relationship privacy for all of your users by turning on the global option in the GEDCOM privacy settings.<br /><br />This setting requires that the user be associated with a GEDCOM ID before they will be able to see any living people.');
	break;

case 'useradmin_rootid':
	$title=i18n::translate('Pedigree chart root person');
	$text=i18n::translate('For each genealogical database, you can designate a <b>Root Person</b> for the user.<br /><br />This Root Person does not need to be the user himself; it can be anybody.  The user will probably want to start the Pedigree chart with himself.  You control that, as well as the default Root person on other charts, here.<br /><br />If the user has Edit rights to his own account information, he can change this setting himself.');
	break;

case 'useradmin_user_default_tab':
	$title=i18n::translate('User default tab setting');
	$text=i18n::translate('This setting allows you to specify which tab is opened automatically when this user accesses the Individual Information page.  If allowed to edit their account, the user can change this setting later.');
	break;

case 'useradmin_verbyadmin':
	$title=i18n::translate('User approved by admin');
	$text=i18n::translate('If a user has used the Self Registration module and has verified himself, the last step, before his account will become active, is your approval.<br /><br />After you have approved the user\'s application for a new account, the user will receive an email message.  The message will tell the user that his account is now active.  He can login with the user name and password that he supplied when he applied for the account.');
	break;

case 'useradmin_verified':
	$title=i18n::translate('User verified himself');
	$text=i18n::translate('<b>Self Registration</b><br />A user can apply for a new account by means of the <b>self registration</b> module.<br /><br />When he does so, he will receive an email message with a link to verify his application.  After the applicant has acted on the instructions in that email, you will see this box checked, and you can proceed with the next step, <b>User approved by Admin</b>.  You should wait with your approval as long as this box is not checked.<br /><br /><b>Add user manually</b><br />If you use this form to add a user manually, you will find this box checked already.');
	break;

case 'useradmin_visibleonline':
	$title=i18n::translate('Visible online');
	$text=i18n::translate('This checkbox controls your visibility to other users while you\'re online.  It also controls your ability to see other online users who are configured to be visible.<br /><br />When this box is unchecked, you will be completely invisible to others, and you will also not be able to see other online users.  When this box is checked, exactly the opposite is true.  You will be visible to others, and you will also be able to see others who are configured to be visible.');
	break;

case 'username':
	$title=i18n::translate('Username');
	$text=i18n::translate('<br />In this box you type your user name.<br /><br /><b>The user name is case sensitive.</b>  This means that <b>MyName</b> is <u>not</u> the same as <b>myname</b> or <b>MYNAME</b>.');
	break;

case 'utf8_ansi':
	$title=i18n::translate('Convert from UTF-8 to ANSI');
	$text=i18n::translate('For optimal display on the Internet, <b>webtrees</b> uses the UTF-8 character set.  Some programs, Family Tree Maker for example, do not support importing GEDCOM files encoded in UTF-8.  Checking this box will convert the file from <b>UTF-8</b> to <b>ANSI (ISO-8859-1)</b>.<br /><br />The format you need depends on the program you use to work with your downloaded GEDCOM file.  If you aren\'t sure, consult the documentation of that program.<br /><br />Note that for special characters to remain unchanged, you will need to keep the file in UTF-8 and convert it to your program\'s method for handling these special characters by some other means.  Consult your program\'s manufacturer or author.<br /><br />This <a href=\'http://en.wikipedia.org/wiki/UTF-8\' target=\'_blank\' title=\'Wikipedia article\'><b>Wikipedia article</b></a> contains comprehensive information and links about UTF-8.');
	break;

case 'validate_gedcom':
	$title=i18n::translate('Validate GEDCOM');
	$text=i18n::translate('This is the third step in the procedure to add externally created GEDCOM data to your genealogical database.<br /><br /><b>webtrees</b> will check the input file for the correct use of Date format, Place format, Character Set, etc.  Some deviations from the GEDCOM 5.5.1 Standard, to which <b>webtrees</b> adheres, can be corrected automatically. Examples are Macintosh line endings and incorrect use of Place format.  When this happens, you will see a message that the data has been changed.  For other abnormalities you will get a warning message with a recommended solution.<br /><br /><b>Optional Tools</b><br />At this moment there is only one additional tool:<br /><b>Change Individual ID to...</b>.<br /><br /><b>More help</b><br />More help is available by clicking the <b>?</b> next to items on the page.');
	break;

case 'verify_gedcom':
	$title=i18n::translate('Verify GEDCOM');
	$text=i18n::translate('Here you can choose to either continue with the upload and import of this GEDCOM file or to abort the upload and import.');
	break;

case 'view_server_folder':
	$title=i18n::translate('View server folder');
	$text=i18n::translate('The administrator has enabled up to %s folder levels below the default <b>%s</b>.  This helps to organize the media files and reduces the possibility of name collisions.<br /><br />In this field, you select the media folder whose contents you wish to view.  When you select <b>ALL</b>, all media files will be shown without regard to the folder in which they are stored.  This can produce a very long list of media items.', $MEDIA_DRECTORY_LEVELS, $MEDIA_DIRECTORY);
	break;

case 'welcome_new':
	$title=i18n::translate('Welcome to your new <b>webtrees</b> website.');
	$text=i18n::translate('Since you are seeing this page, you have successfully installed webtrees on your server and are ready to begin configuring it to your requirements.<br />This Help page will guide you through the configuration process.  As you enter different fields, this window will provide you with help information about the field you are in.  You may close this window; to open it again click on one of the "?" question marks next to the field label.');
	break;

case 'yahrzeit':
	$title=i18n::translate('Yahrzeiten block');
	$text=i18n::translate('This block shows you Yahrzeiten that are coming up in the near future.<br /><br />Yahrzeiten (singular: Yahrzeit) are anniversaries of a person\'s death.  These anniversaries are observed in the Jewish tradition; they are no longer in common use in other traditions.  «Yahrzeit» can also be spelled «Jahrzeit» or «Yartzeit».<br /><br />The Administrator determines how far ahead the block will look.  You can further refine the block\'s display of upcoming Yahrzeiten through configuration options.');
	break;

case 'zip':
	$title=i18n::translate('Zip clippings');
	$text=i18n::translate('Select this option as to save your clippings in a ZIP file.  For more information about ZIP files, please visit <a href="http://www.winzip.com" target="_blank">http://www.winzip.com</a>.');
	break;
	
default:
	$title=i18n::translate('Help');
	$text=i18n::translate('The help text has not been written for this item.');
	// If we've been called from a module, allow the module to provide the help text
	$mod=safe_GET('mod', WT_REGEX_ALPHANUM);
	if (file_exists(WT_ROOT.'modules/'.$mod.'/help_text.php')) {
		require WT_ROOT.'modules/'.$mod.'/help_text.php';
	}
	break;
	
///////////////////////////////////////////////////////////////////////
//                                                                   //
//     REDUNTANT HELP LINKS (more to follow)                         //
//                                                                   //
///////////////////////////////////////////////////////////////////////
/*
case 'active':
	$title=i18n::translate('Active');
	$text=i18n::translate('Allow users to select this language if the option <b>Allow user to change language</b> is enabled.');
	break;

case 'dir_editor.php':
	$title=i18n::translate('Cleanup index directory');
	$text=i18n::translate('This tool can help site administrators clean up files in the Index directory.<br /><br />Over time, files such as log files, old GEDCOM files, and old backup files can build up in the Index directory.  Since many of these files are created by the program, they may be owned by the web server user.  If they are owned by the web server user, you might not be able to delete them. This tool lets you delete these files even when they are owned by the web server user account.<br /><br />To delete a file or subdirectory from the Index directory drag it to the wastebasket or select its checkbox.  Click the Delete button to permanently remove the indicated files.<br /><br />Files marked with <img src="./images/RESN_confidential.gif" alt="\" > are required for proper operation and cannot be removed.<br />Files marked with <img src="./images/RESN_locked.gif" alt="\" > have important settings or pending change data and should only be deleted if you are sure you know what you are doing.');
	break;

case 'help_dir_editor.php':
	$title=i18n::translate('Cleanup index directory');
	$text=i18n::translate('This tool can help site administrators clean up files in the Index directory.<br /><br />Over time, files such as log files, old GEDCOM files, and old backup files can build up in the Index directory.  Since many of these files are created by the program, they may be owned by the web server user.  If they are owned by the web server user, you might not be able to delete them. This tool lets you delete these files even when they are owned by the web server user account.<br /><br />To delete a file or subdirectory from the Index directory drag it to the wastebasket or select its checkbox.  Click the Delete button to permanently remove the indicated files.<br /><br />Files marked with <img src="./images/RESN_confidential.gif" alt="\" > are required for proper operation and cannot be removed.<br />Files marked with <img src="./images/RESN_locked.gif" alt="\" > have important settings or pending change data and should only be deleted if you are sure you know what you are doing.');
	break;

case 'index':
	$title=i18n::translate('Home page');
	$text=i18n::translate('This page is the Home Page. It welcomes you to the selected <a href="#def_gedcom">GEDCOM</a> file. You can return to this page by selecting Home Page from the top menu. If there are multiple GEDCOMs on this site, you can select a GEDCOM from the drop-down menu.<br /><br />This Help page contains information about:<ul><li><a href="#index_portal"><b>Home Page</b></a></li><li><a href="#header"><b>Header Area</b></a></li><li><a href="#menu"><b>Menus</b></a></li><li><a href="#header_general"><b>General Information</b></a></li><li><a href="#def"><b>Definitions</b></a></li></ul>');
	break;

case 'index_portal_head':
	$title=i18n::translate('Index page portal');
	$text=i18n::translate('<div class="name_head"><center><b>The Home Page</b></center></div>');
	break;

case 'menu_help':
// not used? see 'menu'
	$title=i18n::translate('Menus');
	$text=i18n::translate('The page headers have drop-down menus associated with each menu icon.<br /><br />When you move your mouse pointer over an icon a sub-menu will appear, if one exists.  When you click on an icon you will be taken to the first item in the sub-menu.<br /><br />The following menu icons are usually available:<ul><li><a href="#menu_fam">Home page</a><br /></li><li><a href="#menu_myged">My Page</a><br /></li><li><a href="#menu_charts">Charts</a><br /></li><li><a href="#menu_lists">Lists</a><br /></li><li><a href="#menu_annical">Anniversary Calendar</a><br /></li><li><a href="#menu_clip">Family Tree Clippings Cart</a><br /></li><li><a href="#menu_search">Search</a><br /></li><li><a href="?help=help">Help</a></li></ul>');
	break;

case 'multiple':
	$title=i18n::translate('multiple');
	$text=i18n::translate('<center>--- This is a general help text for multiple pages ---</center>');
	break;

case 'upload_gedcom':
 // not used? see 'help_uploadgedcom.php'
	$title=i18n::translate('Upload GEDCOM');
	$text=i18n::translate('Unlike the <b>Add GEDCOM</b> function, the GEDCOM file you wish to add to your database does not have to be on your server.<br /><br />In Step 1 you select a GEDCOM file from your local computer. Type the complete path and file name in the text box or use the <b>Browse</b> button on the page.<br /><br />You can also use this function to upload a ZIP file containing the GEDCOM file. <b>webtrees</b> will recognize the ZIP file and extract the file and the filename automatically.<br /><br />If a GEDCOM file with the same name already exists in <b>webtrees</b>, it will, after your confirmation, be overwritten. However, all GEDCOM settings made previously will be preserved.<br /><br />You will find more help on other pages of the procedure.');
	break;


// ===================================================	

// Is the next block still needed? see 'help_contents_help'

case 'ah10':
	$title=i18n::translate('ah10');
	$text=i18n::translate('_GEDCOM: Administration page');
	break;

case 'ah11':
	$title=i18n::translate('ah11');
	$text=i18n::translate('_GEDCOM: Configure');
	break;

case 'ah12':
	$title=i18n::translate('ah12');
	$text=i18n::translate('_GEDCOM: Import');
	break;

case 'ah13':
	$title=i18n::translate('ah13');
	$text=i18n::translate('_GEDCOM: Upload');
	break;

case 'ah14':
	$title=i18n::translate('ah14');
	$text=i18n::translate('_GEDCOM: Validate');
	break;

case 'ah15':
	$title=i18n::translate('ah15');
	$text=i18n::translate('_GEDCOM: Convert ANSI to UTF-8');
	break;

case 'ah16':
	$title=i18n::translate('ah16');
	$text=i18n::translate('_GEDCOM: Privacy settings');
	break;

case 'ah17':
	$title=i18n::translate('ah17');
	$text=i18n::translate('_User Administration');
	break;

case 'ah18':
	$title=i18n::translate('ah18');
	$text=i18n::translate('_Administration');
	break;

case 'ah19':
	$title=i18n::translate('ah19');
	$text=i18n::translate('_GEDCOM: Media tool');
	break;

case 'ah20':
	$title=i18n::translate('ah20');
	$text=i18n::translate('_GEDCOM: Change Individual ID to ...');
	break;

case 'ah21':
	$title=i18n::translate('ah21');
	$text=i18n::translate('_Translator tools');
	break;

case 'ah23':
	$title=i18n::translate('ah23');
	$text=i18n::translate('_Configure supported languages');
	break;

case 'ah24':
	$title=i18n::translate('ah24');
	$text=i18n::translate('_User Information migrate (Index --&gt;&gt; SQL)');
	break;

case 'ah25':
	$title=i18n::translate('ah25');
	$text=i18n::translate('_<b>webtrees</b> backup');
	break;

case 'ah26':
	$title=i18n::translate('ah26');
	$text=i18n::translate('_FAQ List: Edit');
	break;

case 'ah2':
	$title=i18n::translate('ah2');
	$text=i18n::translate('_Configure <b>webtrees</b>');
	break;

case 'ah3':
	$title=i18n::translate('ah3');
	$text=i18n::translate('_GEDCOM: Add vs Upload');
	break;

case 'ah4':
	$title=i18n::translate('ah4');
	$text=i18n::translate('_GEDCOM: Configuration file');
	break;

case 'ah5':
	$title=i18n::translate('ah5');
	$text=i18n::translate('_GEDCOM: Default');
	break;

case 'ah6':
	$title=i18n::translate('ah6');
	$text=i18n::translate('_GEDCOM: Delete');
	break;

case 'ah7':
	$title=i18n::translate('ah7');
	$text=i18n::translate('_GEDCOM: Add');
	break;

case 'ah8':
	$title=i18n::translate('ah8');
	$text=i18n::translate('_GEDCOM: Create new');
	break;

case 'ah9':
	$title=i18n::translate('ah9');
	$text=i18n::translate('_GEDCOM: Download');
	break;

// ======================================================
*/
	
}

print_simple_header(i18n::translate('Help for «%s»', htmlspecialchars($title)));
echo '<div class="helpheader">', htmlspecialchars($title),'</div>';
echo '<div class="helpcontent">', nl2br($text),'</div>';
echo '<div class="helpfooter"><br />';
echo '<a href="javascript:;" onclick="window.history.go(-1)">',"<img src=\"$WT_IMAGE_DIR/".$WT_IMAGES["larrow"]["other"]."\" alt=\"<\"><br />";
echo '<a href="help_text.php?help=help_contents_help"><b>', i18n::translate('Help Contents'), '</b></a><br />';
echo '<a href="javascript:;" onclick="window.close();"><b>', i18n::translate('Close Window'), '</b></a>';
echo '</div>';
print_simple_footer();
?>
