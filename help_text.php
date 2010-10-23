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
	$title=translate_fact('ABBR');
	$text=i18n::translate('Use this field for storing an abbreviated version of a title.  This field is used in conjunction with the title field on sources.  By default <b>webtrees</b> will first use the title and then the abbreviated title.<br /><br />According to the GEDCOM 5.5 specification, "this entry is to provide a short title used for sorting, filing, and retrieving source records (pg 62)."<br /><br />In <b>webtrees</b> the abbreviated title is optional, but in other genealogical programs it is required.');
	break;

case 'ADDR':
	$title=translate_fact('ADDR');
	$text=i18n::translate('Enter the address into the field just as you would write it on an envelope.<br /><br />Leave this field blank if you do not want to include an address.');
	break;

case 'ADR1':
	$title=translate_fact('ADR1');
	$text='';
	break;

case 'ADR2':
	$title=translate_fact('ADR2');
	$text='';
	break;

case 'ADOP':
	$title=translate_fact('ADOP');
	$text=''; //('Pertaining to creation of a legally approved child-parent relationship that does not exist biologically.');
	break;

case 'AFN':
	$title=translate_fact('AFN');
	$text=''; //('A unique permanent record file number of an individual record stored in Ancestral File.');
	break;

case 'AGE':
	$title=translate_fact('AGE');
	$text=''; //('The age of the individual at the time an event occurred, or the age listed in the document.');
	break;

case 'AGNC':
	$title=translate_fact('AGNC');
	$text=i18n::translate('The organization, institution, corporation, person, or other entity that has authority.<br /><br />For example, an employer of a person, or a church that administered rites or events, or an organization responsible for creating and/or archiving records.');
	break;

case 'ALIA':
	$title=translate_fact('ALIA');
	$text=''; //('An indicator to link different record descriptions of a person who may be the same person.');
	break;

case 'ANCE':
	$title=translate_fact('ANCE');
	$text=''; //('Pertaining to forbearers of an individual.');
	break;

case 'ANCI':
	$title=translate_fact('ANCI');
	$text=''; //('Indicates an interest in additional research for ancestors of this individual.');
	break;

case 'ANUL':
	$title=translate_fact('ANUL');
	$text=''; //('Declaring a marriage void from the beginning (never existed).');
	break;

case 'ASSO':
	$title=translate_fact('ASSO');
	$text=i18n::translate('Enter associate GEDCOM ID.');
	break;

case 'AUTH':
	$title=translate_fact('AUTH');
	$text=''; //('The name of the individual who created or compiled information.');
	break;

case 'BAPL':
	$title=translate_fact('BAPL');
	$text=''; //('The event of baptism performed at age eight or later by priesthood authority of the LDS Church.');
	break;

case 'BAPM':
	$title=translate_fact('BAPM');
	$text=''; //('The event of baptism, performed in infancy or later.');
	// I omitted "(not LDS)" since many people choose to use this
	// for all baptisms, rather than treat LDS as a special case.
	// (Wes Groleau)
	break;

case 'BAPM:DATE':
	$title=translate_fact('BAPM:DATE');
	$text=''; //('Date of baptism.');
	break;

case 'BAPM:PLAC':
	$title=translate_fact('BAPM:PLAC');
	$text=''; //('Place of baptism');
	break;

case 'BAPM:SOUR':
	$title=translate_fact('BAPM:SOUR');
	$text=''; //('Source for baptism');
	break;

case 'BARM':
	$title=translate_fact('BARM');
	$text=''; //('The ceremonial event held when a Jewish boy reaches age 13.');
	break;

case 'BARM:DATE':
	$title=translate_fact('BARM:DATE');
	$text=''; //('Date of bar mitzvah');
	break;

case 'BARM:PLAC':
	$title=translate_fact('BARM:PLAC');
	$text=''; //('Place of bar mitzvah');
	break;

case 'BARM:SOUR':
	$title=translate_fact('BARM:SOUR');
	$text=''; //('Source for bar mitzvah');
	break;

case 'BASM':
	$title=translate_fact('BASM');
	$text=''; //('The ceremonial event held when a Jewish girl reaches age 13, also known as "Bat Mitzvah."');
	break;

case 'BASM:DATE':
	$title=translate_fact('BASM:DATE');
	$text=''; //('Date of bas mitzvah');
	break;

case 'BASM:PLAC':
	$title=translate_fact('BASM:PLAC');
	$text=''; //('Place of bas mitzvah');
	break;

case 'BASM:SOUR':
	$title=translate_fact('BASM:SOUR');
	$text=''; //('Source for bas mitzvah');
	break;

case 'BIRT':
	$title=translate_fact('BIRT');
	$text=''; //('The event of entering into life.');
	break;

case 'BIRT:DATE':
	$title=translate_fact('BIRT:DATE');
	$text=''; //('Date of birth');
	break;

case 'BIRT:PLAC':
	$title=translate_fact('BIRT:PLAC');
	$text=''; //('Place of birth');
	break;

case 'BIRT:SOUR':
	$title=translate_fact('BIRT:SOUR');
	$text=''; //('Source for birth');
	break;

case 'BLES':
	$title=translate_fact('BLES');
	$text=''; //('A religious event of bestowing divine care or intercession.  'Sometimes given in connection with a naming ceremony.');
	break;

case 'BLOB':
	$title=translate_fact('BLOB');
	$text=''; //('"Binary Large OBject"--No longer used in GEDCOM 5.5.1');
	break;

case 'BURI':
	$title=translate_fact('BURI');
	$text=''; //('The event of the proper disposing of the mortal remains of a deceased person.');
	break;

case 'BURI:DATE':
	$title=translate_fact('BURI:DATE');
	$text=''; //('Date of burial');
	break;

case 'BURI:PLAC':
	$title=translate_fact('BURI:PLAC');
	$text=''; //('Place of burial');
	break;

case 'BURI:SOUR':
	$title=translate_fact('BURI:SOUR');
	$text=''; //('Source for burial');
	break;

case 'CALN':
	$title=translate_fact('CALN');
	$text=''; //('The number used by a repository to identify the specific items in its collections.');
	break;

case 'CAST':
	$title=translate_fact('CAST');
	$text=''; //('The name of an individual\'s rank or status in society which is sometimes based on racial or religious differences, or differences in wealth, inherited rank, profession, occupation, etc.');
	break;

case 'CAUS':
	$title=translate_fact('CAUS');
	$text=i18n::translate('A description of the cause of the associated event or fact, such as the cause of death.');
	break;

case 'CEME':
	$title=translate_fact('Cemetery');
	$text=i18n::translate('Enter the name of the cemetery or other resting place where individual is buried.');
	break;

case 'CENS':
	$title=translate_fact('CENS');
	$text=''; //('The event of the periodic count of the population for a designated locality, such as a national or state Census.');
	break;

case 'CHAN':
	$title=translate_fact('CHAN');
	$text=''; //('Indicates a change, correction, or modification. Typically used in connection with a DATE to specify when a change in information occurred.');
	break;

case 'CHAR':
	$title=translate_fact('CHAR');
	$text=''; //('An indicator of the character set used in writing this automated information.');
	break;

case 'CHIL':
	$title=translate_fact('CHIL');
	$text=''; //('The natural, adopted, or sealed (LDS) child of a father and a mother.');
	break;

case 'CHR':
	$title=translate_fact('CHR');
	$text=''; //('The religious event of baptizing and/or naming a child.');
	break;

case 'CHR:DATE':
	$title=translate_fact('CHR:DATE');
	$text=''; //('Date of christening');
	break;

case 'CHR:PLAC':
	$title=translate_fact('CHR:PLAC');
	$text=''; //('Place of christening');
	break;

case 'CHR:SOUR':
	$title=translate_fact('CHR:SOUR');
	$text=''; //('Source for christening');
	break;

case 'CHRA':
	$title=translate_fact('CHRA');
	$text=''; //('The religious event of baptizing and/or naming an adult person.');
	break;

case 'CITN':
	// This tag is not in the 5.5.1 spec
	$title=translate_fact('CITN');
	$text='';
	break;

case 'CITY':
	$title=translate_fact('CITY');
	$text=''; //('A lower level jurisdictional unit. Normally an incorporated municipal unit.');
	break;

case 'COMM':
	// This tag is not in the 5.5.1 spec
	$title=translate_fact('COMM');
	$text='';
	break;

case 'CONC':
	$title=translate_fact('CONC');
	$text=''; //('An indicator that additional data belongs to the superior value.  The information from the CONC value is to be connected to the value of the superior preceding line without a space and without a carriage return and/or new line character.  Values that are split for a CONC tag must always be split at a non- space.  If the value is split on a space the space will be lost when concatenation takes place.  This is because of the treatment that spaces get as a GEDCOM delimiter, many GEDCOM values are trimmed of trailing spaces and some systems look for the first non-space starting after the tag to determine the beginning of the value.');
	break;

case 'CONT':
	$title=translate_fact('CONT');
	$text=''; //('An indicator that additional data belongs to the superior value.  The information from the CONT value is to be connected to the value of the superior preceding line with a carriage return and/or new line character.  Leading spaces could be important to the formatting of the resultant text.  When importing values from CONT lines the reader should assume only one delimiter character following the CONT tag.  Assume that the rest of the leading spaces are to be a part of the value.');
	break;

case 'CONF':
	$title=translate_fact('CONF');
	$text=''; //('The religious event of conferring the gift of the Holy Ghost and, among protestants, full church membership.');
	break;

case 'CONF:DATE':
case 'CONL:DATE':
	$title=translate_fact('CONF:DATE');
	$text=''; //('Date of confirmation');
	break;

case 'CONF:PLAC':
case 'CONL:PLAC':
	$title=translate_fact('CONF:PLAC');
	$text=''; //('Place of confirmation');
	break;

case 'CONF:SOUR':
case 'CONL:SOUR':
	$title=translate_fact('CONF:SOUR');
	$text=''; //('Source for confirmation');
	break;

case 'CONL':
	$title=translate_fact('CONL');
	$text=''; //('The religious event by which a person receives membership in the LDS Church.');
	break;

case 'COPR':
	$title=translate_fact('COPR');
	$text=''; //('A statement that accompanies data to protect it from unlawful duplication and distribution.');
	break;

case 'CORP':
	$title=translate_fact('CORP');
	$text=''; //('A name of an institution, agency, corporation, or company.');
	break;

case 'CREM':
	$title=translate_fact('CREM');
	$text=''; //('Disposal of the remains of a person\'s body by fire.');
	break;

case 'CTRY':
	$title=translate_fact('CTRY');
	$text=''; //('The name or code of the country.');
	break;

case 'DATA':
	$title=translate_fact('DATA');
	$text=''; //('Pertaining to stored automated information.');
	break;

case 'DATA:DATE':
	$title=translate_fact('DATA:DATE');
	$text=''; //('Date of these data.');
	break;

case 'DATE':
	$title=translate_fact('DATE');
	$text=''; //('The time of an event in a calendar format.');
	break;

case 'DEAT':
	$title=translate_fact('DEAT');
	$text=''; //('The event when mortal life terminates.');
	break;

case 'DEAT:CAUS':
	$title=translate_fact('DEAT:CAUS');
	$text=''; //('Cause of death');
	break;

case 'DEAT:DATE':
	$title=translate_fact('DEAT:DATE');
	$text=''; //('Date of death');
	break;

case 'DEAT:PLAC':
	$title=translate_fact('DEAT:PLAC');
	$text=''; //('Place of death');
	break;

case 'DEAT:SOUR':
	$title=translate_fact('DEAT:SOUR');
	$text=''; //('Source for death');
	break;

case 'DESC':
	$title=translate_fact('DESC');
	$text=''; //('Pertaining to offspring of an individual.');
	break;

case 'DESI':
	$title=translate_fact('DESI');
	$text=''; //('Indicates an interest in research to identify additional descendants of this individual.');
	break;

case 'DEST':
	$title=translate_fact('DEST');
	$text=''; //('A system receiving data.');
	break;

case 'DIV':
	$title=translate_fact('DIV');
	$text=''; //('An event of dissolving a marriage through civil action.');
	break;

case 'DIVF':
	$title=translate_fact('DIVF');
	$text=''; //('An event of filing for a divorce by a spouse.');
	break;

case 'DSCR':
	$title=translate_fact('DSCR');
	$text=''; //('The physical characteristics of a person, place, or thing.');
	break;

case 'EDUC':
	$title=translate_fact('EDUC');
	$text=''; //('Indicator of a level of education attained.');
	break;

case 'EMAI':
case 'EMAIL':
case 'EMAL':
case '_EMAIL':
	$title=translate_fact('EMAIL');
	$text=i18n::translate('Enter the email address.<br /><br />An example email address looks like this: <b>name@hotmail.com</b>  Leave this field blank if you do not want to include an email address.');
	break;

case 'EMIG':
	$title=translate_fact('EMIG');
	$text=''; //('An event of leaving one\'s homeland with the intent of residing elsewhere.');
	break;

case 'ENDL':
	$title=translate_fact('ENDL');
	$text=''; //('A religious event where an endowment ordinance for an individual was performed by priesthood authority in an LDS temple.');
	break;

case 'ENGA':
	$title=translate_fact('ENGA');
	$text=''; //('An event of recording or announcing an agreement between two people to become married.');
	break;

case 'ENGA:DATE':
	$title=translate_fact('ENGA:DATE');
	$text=''; //('Date of engagement.');
	break;

case 'ENGA:PLAC':
	$title=translate_fact('ENGA:PLAC');
	$text=''; //('Place of engagement.');
	break;

case 'ENGA:SOUR':
	$title=translate_fact('ENGA:SOUR');
	$text=''; //('Source for engagement.');
	break;

case 'EVEN':
	$title=translate_fact('EVEN');
	$text=''; //('Pertaining to a noteworthy happening related to an individual, a group, or an organization.  An EVENt structure is usually qualified or classified by a subordinate use of the TYPE tag.');
	break;

case 'FACT':
	$title=translate_fact('FACT');
	$text=''; //('Pertaining to a noteworthy attribute or fact concerning an individual, a group, or an organization.  A FACT structure is usually qualified or classified by a subordinate use of the TYPE tag.');
	break;

case 'FAM':
	$title=translate_fact('FAM');
	$text=''; //('Identifies a legal, common law, or other customary relationship of man and woman and their children, if any, or a family created by virtue of the birth of a child to its biological father and mother.');
	break;

case 'FAMC':
	$title=translate_fact('FAMC');
	$text=''; //('Identifies the family in which an individual appears as a child.');
	break;

case 'FAMC:HUSB:BIRT:PLAC':
	$title=translate_fact('FAMC:HUSB:BIRT:PLAC');
	$text='';
	break;

case 'FAMC:HUSB:FAMC:HUSB:GIVN':
	$title=translate_fact('FAMC:HUSB:FAMC:HUSB:GIVN');
	$text='';
	break;

case 'FAMC:HUSB:FAMC:WIFE:GIVN':
	$title=translate_fact('FAMC:HUSB:FAMC:WIFE:GIVN');
	$text='';
	break;

case 'FAMC:HUSB:GIVN':
	$title=translate_fact('FAMC:HUSB:GIVN');
	$text='';
	break;

case 'FAMC:HUSB:OCCU':
	$title=translate_fact('FAMC:HUSB:OCCU');
	$text='';
	break;

case 'FAMC:HUSB:OCCU':
	$title=translate_fact('FAMC:HUSB:OCCU');
	$text='';
	break;

case 'FAMC:MARR:PLAC':
	$title=translate_fact('FAMC:MARR:PLAC');
	$text='';
	break;

case 'FAMC:MARR:PLAC':
	$title=translate_fact('FAMC:MARR:PLAC');
	$text='';
	break;

case 'FAMC:WIFE:FAMC:HUSB:GIVN':
	$title=translate_fact('FAMC:WIFE:FAMC:HUSB:GIVN');
	$text='';
	break;

case 'FAMC:WIFE:FAMC:WIFE:GIVN':
	$title=translate_fact('FAMC:WIFE:FAMC:WIFE:GIVN');
	$text='';
	break;

case 'FAMC:WIFE:GIVN':
	$title=translate_fact('FAMC:WIFE:GIVN');
	$text='';
	break;

case 'FAMC:WIFE:SURN':
	$title=translate_fact('FAMC:WIFE:SURN');
	$text='';
	break;

case 'FAMF':
	$title=translate_fact('FAMF');
	$text=''; //('Pertaining to, or the name of, a family file. Names stored in a file that are assigned to a family for doing temple ordinance work.');
	break;

case 'FAMS':
	$title=translate_fact('FAMS');
	$text=''; //('Identifies the family in which an individual appears as a spouse.');
	break;

case 'FAMS:CENS:DATE':
	$title=translate_fact('FAMS:CENS:DATE');
	$text='';
	break;

case 'FAMS:CENS:PLAC':
	$title=translate_fact('FAMS:CENS:PLAC');
	$text='';
	break;

case 'FAMS:CHIL:BIRT:PLAC':
	$title=translate_fact('FAMS:CHIL:BIRT:PLAC');
	$text='';
	break;

case 'FAMS:DIV:DATE':
	$title=translate_fact('FAMS:DIV:DATE');
	$text='';
	break;

case 'FAMS:DIV:PLAC':
	$title=translate_fact('FAMS:DIV:PLAC');
	$text='';
	break;

case 'FAMS:MARR:DATE':
	$title=translate_fact('FAMS:MARR:DATE');
	$text='';
	break;

case 'FAMS:MARR:PLAC':
	$title=translate_fact('FAMS:MARR:PLAC');
	$text='';
	break;

case 'FAMS:NOTE':
	$title=translate_fact('FAMS:NOTE');
	$text='';
	break;

case 'FAMS:SLGS:DATE':
	$title=translate_fact('FAMS:SLGS:DATE');
	$text='';
	break;

case 'FAMS:SLGS:PLAC':
	$title=translate_fact('FAMS:SLGS:PLAC');
	$text='';
	break;

case 'FAMS:SLGS:TEMP':
	$title=translate_fact('FAMS:SLGS:TEMP');
	$text='';
	break;

case 'FAMS:SPOUSE:BIRT:PLAC':
	$title=translate_fact('FAMS:SPOUSE:BIRT:PLAC');
	$text='';
	break;

case 'FAMS:SPOUSE:DEAT:PLAC':
	$title=translate_fact('FAMS:SPOUSE:DEAT:PLAC');
	$text='';
	break;

case 'FAX':
	$title=translate_fact('FAX');
	$text=i18n::translate('Enter the FAX number including the country and area code.<br /><br />Leave this field blank if you do not want to include a FAX number.  For example, a number in Germany might be +49 25859 56 76 89 and a number in USA or Canada might be +1 888 555-1212.');
	break;

case 'FCOM':
	$title=translate_fact('FCOM');
	$text=''; //('A religious rite, the first act of sharing in the Lord\'s supper as part of church worship.');
	break;

case 'FCOM:DATE':
	$title=translate_fact('FCOM:DATE');
	$text=''; //('Date of First Communion');
	break;

case 'FCOM:PLAC':
	$title=translate_fact('FCOM:PLAC');
	$text=''; //('Place of First Communion');
	break;

case 'FCOM:SOUR':
	$title=translate_fact('FCOM:SOUR');
	$text=''; //('Source for First Communion');
	break;

case 'FILE':
	$title=translate_fact('FILE');
	$text=''; //('This is the most important field in the multimedia object record.  It indicates which file to use.  At the very minimum, you need to enter the file\'s name.  Depending on your settings, more information about the file\'s location may be helpful.<br /><br />You can use the <b>Find Media</b> link to help you locate media items that have already been uploaded to the site.');
	break;

case 'FONE':
	$title=translate_fact('FONE');
	$text=''; //('A phonetic variation of a superior text string');
	break;

case 'FORM':
	$title=translate_fact('FORM');
	$text=i18n::translate('This is an optional field that can be used to enter the file format of the multimedia object.  Some genealogy programs may look at this field to determine how to handle the item.  However, since media do not transfer across computer systems very well, this field is not very important.');
	break;

case 'GEDC':
	$title=translate_fact('GEDC');
	$text=''; //('Information about the use of GEDCOM in a transmission.');
	break;

case 'GIVN':
	$title=translate_fact('GIVN');
	$text=i18n::translate('In this field you should enter the given names for the person.  As an example, in the name "John Robert Finlay", the given names that should be entered here are "John Robert"');
	break;

case 'GRAD':
	$title=translate_fact('GRAD');
	$text=''; //('An event of awarding educational diplomas or degrees to individuals.');
	break;

case 'HEAD':
	$title=translate_fact('HEAD');
	$text=''; //('Identifies information pertaining to an entire GEDCOM transmission.');
	break;

case 'HUSB':
	$title=translate_fact('HUSB');
	$text=''; //('An individual in the family role of a married man or father.');
	break;

case 'IDNO':
	$title=translate_fact('IDNO');
	$text='';
	break;

case 'IMMI':
	$title=translate_fact('IMMI');
	$text='';
	break;

case 'INDI':
	$title=translate_fact('INDI');
	$text='';
	break;

case 'INFL':
	$title=translate_fact('INFL');
	$text='';
	break;

case 'LANG':
	$title=translate_fact('LANG');
	$text='';
	break;

case 'LATI':
	$title=translate_fact('LATI');
	$text='';
	break;

case 'LEGA':
	$title=translate_fact('LEGA');
	$text='';
	break;

case 'LONG':
	$title=translate_fact('LONG');
	$text='';
	break;

case 'MAP':
	$title=translate_fact('MAP');
	$text='';
	break;

case 'MARB':
	$title=translate_fact('MARB');
	$text='';
	break;

case 'MARB:DATE':
	$title=translate_fact('MARB:DATE');
	$text='';
	break;

case 'MARB:PLAC':
	$title=translate_fact('MARB:PLAC');
	$text='';
	break;

case 'MARB:SOUR':
	$title=translate_fact('MARB:SOUR');
	$text='';
	break;

case 'MARC':
	$title=translate_fact('MARC');
	$text='';
	break;

case 'MARL':
	$title=translate_fact('MARL');
	$text='';
	break;

case 'MARR':
	$title=translate_fact('MARR');
	$text='';
	break;

case 'MARR:':
	$title=translate_fact('MARR');
	$text='';
	break;

case 'MARR:PLAC':
	$title=translate_fact('MARR:PLAC');
	$text='';
	break;

case 'MARR:SOUR':
	$title=translate_fact('MARR:SOUR');
	$text='';
	break;

case 'MARR_CIVIL':
	$title=translate_fact('MARR_CIVIL');
	$text='';
	break;

case 'MARR_PARTNERS':
	$title=translate_fact('MARR_PARTNERS');
	$text='';
	break;

case 'MARR_RELIGIOUS':
	$title=translate_fact('MARR_RELIGIOUS');
	$text='';
	break;

case 'MARR_UNKNOWN':
	$title=translate_fact('MARR_UNKNOWN');
	$text='';
	break;

case 'MARS':
	$title=translate_fact('MARS');
	$text='';
	break;

case 'MEDI':
	$title=translate_fact('MEDI');
	$text='';
	break;

case 'NAME':
	$title=translate_fact('NAME');
	$text=i18n::translate('This is the most important field in a person\'s Name record.<br /><br />This field should be filled automatically as the other fields are filled in, but it is provided so that you can edit the information according to your personal preference.<br /><br />The name in this field should be entered according to the GEDCOM 5.5.1 standards with the surname surrounded by forward slashes "/".  As an example, the name "John Robert Finlay Jr." should be entered like this: "John Robert /Finlay/ Jr.".');
	break;

case 'NAME:FONE':
	$title=translate_fact('NAME:FONE');
	$text='';
	break;

case 'NAME:_HEB':
	$title=translate_fact('NAME:_HEB');
	$text='';
	break;

case 'NATI':
	$title=translate_fact('NATI');
	$text='';
	break;

case 'NATU':
	$title=translate_fact('NATU');
	$text='';
	break;

case 'NCHI':
	$title=translate_fact('NCHI');
	$text=i18n::translate('Enter the number of children for this individual or family. This is an optional field.');
	break;

case 'NICK':
	$title=translate_fact('NICK');
	$text=i18n::translate('In this field you should enter any nicknames for the person.<br />This is an optional field.<br /><br />Ways to add a nickname:<ul><li>Select <b>modify name</b> then enter nickname and save</li><li>Select <b>add new name</b> then enter nickname AND name and save</li><li>Select <b>edit GEDCOM record</b> to add multiple [2&nbsp;NICK] records subordinate to the main [1&nbsp;NAME] record.</li></ul>');
	break;

case 'NMR':
	$title=translate_fact('NMR');
	$text='';
	break;

case 'NOTE':
	$title=translate_fact('NOTE');
	$text=i18n::translate('Notes are free-form text and will appear in the Fact Details section of the page.');
	break;

case 'NPFX':
	$title=translate_fact('NPFX');
	$text=i18n::translate('This optional field allows you to enter a name prefix such as "Dr." or "Adm."');
	break;

case 'NSFX':
	$title=translate_fact('NSFX');
	$text=i18n::translate('In this optional field you should enter the name suffix for the person.  Examples of name suffixes are "Sr.", "Jr.", and "III".');
	break;

case 'OBJE':
	$title=translate_fact('OBJE');
	$text='';
	break;

case 'OCCU':
	$title=translate_fact('OCCU');
	$text='';
	break;

case 'ORDI':
	$title=translate_fact('ORDI');
	$text='';
	break;

case 'ORDN':
	$title=translate_fact('ORDN');
	$text='';
	break;

case 'PAGE':
	$title=translate_fact('PAGE');
	$text=i18n::translate('In the Citation Details field you would enter the page number or other information that might help someone find the information in the source.');
	break;

case 'PEDI':
	$title=translate_fact('PEDI');
	$text=i18n::translate('This field describes the relationship of the child to its family.  The possibilities are:<ul><li><b>unknown</b>&nbsp;&nbsp;&nbsp;The child\'s relationship to its family cannot be determined.  When this option is selected, the Pedigree field will not be copied into the database.<br /><br /></li><li><b>Birth</b>&nbsp;&nbsp;&nbsp;This option indicates that the child is related to its family by birth.<br /><br /></li><li><b>Adopted</b>&nbsp;&nbsp;&nbsp;This option indicates that the child was adopted by its family.  This does <i>not</i> indicate that there is no blood relationship between the child and its family; it shows that the child was adopted by the family in question sometime after the child\'s birth.<br /><br /></li><li><b>Foster</b>&nbsp;&nbsp;&nbsp;This option indicates that the child is a foster child of the family.  Usually, there is no blood relationship between the child and its family.<br /><br /></li><li><b>Sealing</b>&nbsp;&nbsp;&nbsp;The child was sealed to its family in an LDS <i>sealing</i> ceremony.  A child sealing is performed when the parents were sealed to each other after the birth of the child.  Children born after the parents\' sealing are automatically sealed to the family.<br /><br /></li></ul>');
	break;

case 'PHON':
	$title=translate_fact('PHON');
	$text=i18n::translate('Enter the phone number including the country and area code.<br /><br />Leave this field blank if you do not want to include a phone number.  For example, a number in Germany might be +49 25859 56 76 89 and a number in USA or Canada might be +1 888 555-1212.');
	break;

case 'PLAC':
	$title=translate_fact('PLAC');
	$text=i18n::translate('Places should be entered according to the standards for genealogy.  In genealogy, places are recorded with the most specific information about the place first and then working up to the least specific place last, using commas to separate the different place levels.  The level at which you record the place information should represent the levels of government or church where vital records for that place are kept.<br /><br />For example, a place like Salt Lake City would be entered as "Salt Lake City, Salt Lake, Utah, USA".<br /><br />Let\'s examine each part of this place.  The first part, "Salt Lake City," is the city or township where the event occurred.  In some countries, there may be municipalities or districts inside a city which are important to note.  In that case, they should come before the city.  The next part, "Salt Lake," is the county.  "Utah" is the state, and "USA" is the country.  It is important to note each place because genealogical records are kept by the governments of each level.<br /><br />If a level of the place is unknown, you should leave a space between the commas.  Suppose, in the example above, you didn\'t know the county for Salt Lake City.  You should then record it like this: "Salt Lake City, , Utah, USA".  Suppose you only know that a person was born in Utah.  You would enter the information like this: ", , Utah, USA".  <br /><br />You can use the <b>Find Place</b> link to help you find places that already exist in the database.');
	break;

case 'PLAC:FONE':
	$title=translate_fact('PLAC:FONE');
	$text='';
	break;

case 'PLAC:ROMN':
	$title=translate_fact('PLAC:ROMN');
	$text='';
	break;

case 'PLAC:_HEB':
	$title=translate_fact('PLAC:_HEB');
	$text='';
	break;

case 'POST':
	$title=translate_fact('POST');
	$text='';
	break;

case 'PROB':
	$title=translate_fact('PROB');
	$text='';
	break;

case 'PROP':
	$title=translate_fact('PROP');
	$text='';
	break;

case 'PUBL':
	$title=translate_fact('PUBL');
	$text='';
	break;

case 'QUAY':
	$title=translate_fact('QUAY');
	$text=i18n::translate('You would use this field to record the quality or reliability of the data found in this source.  Many genealogy applications use a number in the field. <b>3</b> might mean that the data is a primary source, <b>2</b> might mean that it was a secondary source, <b>1</b> might mean the information is questionable, and <b>0</b> might mean that the source is unreliable.');
	break;

case 'REFN':
	$title=translate_fact('REFN');
	$text='';
	break;

case 'RELA':
	$title=translate_fact('RELA');
	$text=i18n::translate('Select a relationship name from the list. Selecting <b>Godfather</b> means: <i>This associate is the Godfather of the current individual</i>.');
	break;

case 'RELI':
	$title=translate_fact('RELI');
	$text='';
	break;

case 'REPO':
	$title=translate_fact('REPO');
	$text='';
	break;

case 'RESI':
	$title=translate_fact('RESI');
	$text='';
	break;

case 'RESN':
	$title=translate_fact('RESN');
	$text=
		i18n::translate('Apart from general privacy settings, <b>webtrees</b> has the ability to set restrictions on viewing and editing fact information for individuals and families. The restrictions can be set by anyone who is allowed to edit the information, unless privacy or formerly set restrictions prohibit this.').
		'<br /><br />'.i18n::translate('The following values can be used:').
		'<br /><ul><li><b>'.i18n::translate_c('Restriction status', 'None').'</b><br />'.i18n::translate('Site administrators, GEDCOM administrators, and users who have rights to edit can change the information. Fact information can be viewed according to privacy settings as applied by the administrator.').
		'</li><li><b>'.i18n::translate_c('Restriction status', 'Do not change').'</b><br />'.i18n::translate('This setting has no influence on the visibility of the fact data. It restricts editing rights to site administrators and GEDCOM administrators. If the information applies to the user himself, he can also view and, assuming he has editing rights, edit it.').
		'</li><li><b>'.i18n::translate_c('Restriction status', 'Privacy').'</b><br />'.i18n::translate('Site administrators and GEDCOM administrators can view and edit the information. If the information applies to the user himself, he can also view and, assuming he has editing rights, edit it. It will be hidden from all other users regardless of their login status.').
		'</li><li><b>'.i18n::translate_c('Restriction status', 'Confidential').'</b><br />'.i18n::translate('Only site administrators and GEDCOM administrators can view and edit the information. It will be hidden from all other users regardless of their login status.').
		'</li></ul>';
	break;

case 'RETI':
	$title=translate_fact('RETI');
	$text='';
	break;

case 'RFN':
	$title=translate_fact('RFN');
	$text='';
	break;

case 'RIN':
	$title=translate_fact('RIN');
	$text='';
	break;

case 'ROLE':
	$title=translate_fact('ROLE');
	$text='';
	break;

case 'ROMN':
	$title=translate_fact('ROMN');
	$text=i18n::translate('In many cultures it is customary to have a traditional name spelled in the traditional characters and also a romanized version of the name as it would be spelled or pronounced in languages based on the Latin alphabet, such as English.<br /><br />If you prefer to use a non-Latin alphabet such as Hebrew, Greek, Russian, Chinese, or Arabic to enter the name in the standard name fields, then you can use this field to enter the same name using the Latin alphabet.  Both versions of the name will appear in lists and charts.<br /><br />Although this field is labeled "Romanized", it is not restricted to containing only characters based on the Latin alphabet.  This might be of use with Japanese names, where three different alphabets may occur.');
	break;

case 'SERV':
	$title=translate_fact('SERV');
	$text='';
	break;

case 'SEX':
	$title=translate_fact('SEX');
	$text=i18n::translate('Choose the appropriate gender from the drop-down list.  The <b>unknown</b> option indicates that the gender is unknown.');
	break;

case 'SHARED_NOTE':
	$title=translate_fact('SHARED_NOTE');
	$text=i18n::translate('Shared Notes are free-form text and will appear in the Fact Details section of the page.<br /><br />Each shared note can be linked to more than one person, family, source, or event.');
	break;

case 'SLGC':
	$title=translate_fact('SLGC');
	$text='';
	break;

case 'SLGS':
	$title=translate_fact('SLGS');
	$text='';
	break;

case 'SOUR':
	$title=translate_fact('SOUR');
	$text=i18n::translate('This field allows you to change the source record that this fact\'s source citation links to.  This field takes a Source ID.  Beside the field will be listed the title of the current source ID.  Use the <b>Find ID</b> link to look up the source\'s ID number.  To remove the entire citation, make this field blank.');
	break;

case 'SPFX':
	$title=translate_fact('SPFX');
	$text=i18n::translate('Enter or select from the list words that precede the main part of the Surname.  Examples of such words are <b>von</b> Braun, <b>van der</b> Kloot, <b>de</b> Graaf, etc.');
	break;

case 'SSN':
	$title=translate_fact('SSN');
	$text='';
	break;

case 'STAE':
	$title=translate_fact('STAE');
	$text='';
	break;

case 'STAT':
	$title=translate_fact('STAT');
	$text=i18n::translate('This is an optional status field and is used mostly for LDS ordinances as they are run through the TempleReady program.');
	break;

case 'STAT:DATE':
	$title=translate_fact('STAT:DATE');
	$text='';
	break;

case 'SUBM':
	$title=translate_fact('SUBM');
	$text='';
	break;

case 'SUBN':
	$title=translate_fact('SUBN');
	$text='';
	break;

case 'SURN':
	$john_doe=i18n::translate('John /DOE/'); // Same text used in editgedcoms.php
	$fullname=str_replace('/', '', $john_doe);
	list(,$surname)=explode('/', $john_doe);
	$title=translate_fact('SURN');
	$text=i18n::translate('In this field you should enter the surname for the person.  In the name %1$s, the surname is %2$s.', "<b>{$fullname}</b>", "<b>{$surname}</b>");
	$text.='<br/><br/>';
	$text.=i18n::translate('Individuals with multiple surnames, common in Spain and Portugal, should separate the surnames with a comma.  This indicates that the person is to be listed under each of the names.  For example, <b>Cortes,Vega</b> will be listed under both <b>C</b> and <b>V</b>, whereas <b>Cortes Vega</b> will only be listed under <b>C</b>.');
	break;

case 'TEMP':
	$title=translate_fact('TEMP');
	$text=i18n::translate('For LDS ordinances, this field records the Temple where it was performed.');
	break;

case 'TEXT':
	$title=translate_fact('TEXT');
	$text=i18n::translate('In this field you would enter the citation text for this source.  Examples of data may be a transcription of the text from the source, or a description of what was in the citation.');
	break;

case 'TIME':
	$title=translate_fact('TIME');
	$text=i18n::translate('Enter the time for this event in 24-hour format with leading zeroes. Midnight is 00:00. Examples: 04:50 13:00 20:30.');
	break;

case 'TITL':
	$title=translate_fact('TITL');
	$text=i18n::translate('Enter a title for the item you are editing.  If this is a title for a multimedia item, enter a descriptive title that will identify that item to the user.');
	break;

case 'TITL:FONE':
	$title=translate_fact('TITL:FONE');
	$text='';
	break;

case 'TITL:ROMN':
	$title=translate_fact('TITL:ROMN');
	$text='';
	break;

case 'TITL:_HEB':
	$title=translate_fact('TITL:_HEB');
	$text='';
	break;

case 'TRLR':
	$title=translate_fact('TRLR');
	$text='';
	break;

case 'TYPE':
	$title=translate_fact('TYPE');
	$text=i18n::translate('The Type field is used to enter additional information about the item.  In most cases, the field is completely free-form, and you can enter anything you want.');
	break;

case 'URL':
	$title=translate_fact('URL');
	$text=i18n::translate('Enter the URL address including the http://.<br /><br />An example URL looks like this: <b>http://www.webtrees.net/</b> Leave this field blank if you do not want to include a URL.');
	break;

case 'VERS':
	$title=translate_fact('VERS');
	$text='';
	break;

case 'WIFE':
	$title=translate_fact('WIFE');
	$text='';
	break;

case 'WILL':
	$title=translate_fact('WILL');
	$text='';
	break;

case 'WWW':
	$title=translate_fact('WWW');
	$text='';
	break;

case '_ADOP_CHIL':
	$title=translate_fact('_ADOP_CHIL');
	$text='';
	break;

case '_ADOP_COUS':
	$title=translate_fact('_ADOP_COUS');
	$text='';
	break;

case '_ADOP_FSIB':
	$title=translate_fact('_ADOP_FSIB');
	$text='';
	break;

case '_ADOP_GCHI':
	$title=translate_fact('_ADOP_GCHI');
	$text='';
	break;

case '_ADOP_GGCH':
	$title=translate_fact('_ADOP_GGCH');
	$text='';
	break;

case '_ADOP_HSIB':
	$title=translate_fact('_ADOP_HSIB');
	$text='';
	break;

case '_ADOP_MSIB':
	$title=translate_fact('_ADOP_MSIB');
	$text='';
	break;

case '_ADOP_NEPH':
	$title=translate_fact('_ADOP_NEPH');
	$text='';
	break;

case '_ADOP_SIBL':
	$title=translate_fact('_ADOP_SIBL');
	$text='';
	break;

case '_ADPF':
	$title=translate_fact('_ADPF');
	$text='';
	break;

case '_ADPM':
	$title=translate_fact('_ADPM');
	$text='';
	break;

case '_AKA':
case '_AKAN':
	$title=translate_fact('_AKA');
	$text='';
	break;

case '_BAPM_CHIL':
	$title=translate_fact('_BAPM_CHIL');
	$text='';
	break;

case '_BAPM_COUS':
	$title=translate_fact('_BAPM_COUS');
	$text='';
	break;

case '_BAPM_FSIB':
	$title=translate_fact('_BAPM_FSIB');
	$text='';
	break;

case '_BAPM_GCHI':
	$title=translate_fact('_BAPM_GCHI');
	$text='';
	break;

case '_BAPM_GGCH':
	$title=translate_fact('_BAPM_GGCH');
	$text='';
	break;

case '_BAPM_HSIB':
	$title=translate_fact('_BAPM_HSIB');
	$text='';
	break;

case '_BAPM_MSIB':
	$title=translate_fact('_BAPM_MSIB');
	$text='';
	break;

case '_BAPM_NEPH':
	$title=translate_fact('_BAPM_NEPH');
	$text='';
	break;

case '_BAPM_SIBL':
	$title=translate_fact('_BAPM_SIBL');
	$text='';
	break;

case '_BIBL':
	$title=translate_fact('_BIBL');
	$text='';
	break;

case '_BIRT_CHIL':
	$title=translate_fact('_BIRT_CHIL');
	$text='';
	break;

case '_BIRT_COUS':
	$title=translate_fact('_BIRT_COUS');
	$text='';
	break;

case '_BIRT_FSIB':
	$title=translate_fact('_BIRT_FSIB');
	$text='';
	break;

case '_BIRT_GCHI':
	$title=translate_fact('_BIRT_GCHI');
	$text='';
	break;

case '_BIRT_GGCH':
	$title=translate_fact('_BIRT_GGCH');
	$text='';
	break;

case '_BIRT_HSIB':
	$title=translate_fact('_BIRT_HSIB');
	$text='';
	break;

case '_BIRT_MSIB':
	$title=translate_fact('_BIRT_MSIB');
	$text='';
	break;

case '_BIRT_NEPH':
	$title=translate_fact('_BIRT_NEPH');
	$text='';
	break;

case '_BIRT_SIBL':
	$title=translate_fact('_BIRT_SIBL');
	$text='';
	break;

case '_BRTM':
	$title=translate_fact('_BRTM');
	$text='';
	break;

case '_BRTM:DATE':
	$title=translate_fact('_BRTM:DATE');
	$text='';
	break;

case '_BRTM:PLAC':
	$title=translate_fact('_BRTM:PLAC');
	$text='';
	break;

case '_BRTM:SOUR':
	$title=translate_fact('_BRTM:SOUR');
	$text='';
	break;

case '_BURI_CHIL':
	$title=translate_fact('_BURI_CHIL');
	$text='';
	break;

case '_BURI_COUS':
	$title=translate_fact('_BURI_COUS');
	$text='';
	break;

case '_BURI_FATH':
	$title=translate_fact('_BURI_FATH');
	$text='';
	break;

case '_BURI_FSIB':
	$title=translate_fact('_BURI_FSIB');
	$text='';
	break;

case '_BURI_GCHI':
	$title=translate_fact('_BURI_GCHI');
	$text='';
	break;

case '_BURI_GGCH':
	$title=translate_fact('_BURI_GGCH');
	$text='';
	break;

case '_BURI_GGPA':
	$title=translate_fact('_BURI_GGPA');
	$text='';
	break;

case '_BURI_GPAR':
	$title=translate_fact('_BURI_GPAR');
	$text='';
	break;

case '_BURI_HSIB':
	$title=translate_fact('_BURI_HSIB');
	$text='';
	break;

case '_BURI_MOTH':
	$title=translate_fact('_BURI_MOTH');
	$text='';
	break;

case '_BURI_MSIB':
	$title=translate_fact('_BURI_MSIB');
	$text='';
	break;

case '_BURI_NEPH':
	$title=translate_fact('_BURI_NEPH');
	$text='';
	break;

case '_BURI_SIBL':
	$title=translate_fact('_BURI_SIBL');
	$text='';
	break;

case '_BURI_SPOU':
	$title=translate_fact('_BURI_SPOU');
	$text='';
	break;

case '_CHR_CHIL':
	$title=translate_fact('_CHR_CHIL');
	$text='';
	break;

case '_CHR_COUS':
	$title=translate_fact('_CHR_COUS');
	$text='';
	break;

case '_CHR_FSIB':
	$title=translate_fact('_CHR_FSIB');
	$text='';
	break;

case '_CHR_GCHI':
	$title=translate_fact('_CHR_GCHI');
	$text='';
	break;

case '_CHR_GGCH':
	$title=translate_fact('_CHR_GGCH');
	$text='';
	break;

case '_CHR_HSIB':
	$title=translate_fact('_CHR_HSIB');
	$text='';
	break;

case '_CHR_MSIB':
	$title=translate_fact('_CHR_MSIB');
	$text='';
	break;

case '_CHR_NEPH':
	$title=translate_fact('_CHR_NEPH');
	$text='';
	break;

case '_CHR_SIBL':
	$title=translate_fact('_CHR_SIBL');
	$text='';
	break;

case '_COML':
	$title=translate_fact('_COML');
	$text='';
	break;

case '_CREM_CHIL':
	$title=translate_fact('_CREM_CHIL');
	$text='';
	break;

case '_CREM_COUS':
	$title=translate_fact('_CREM_COUS');
	$text='';
	break;

case '_CREM_FATH':
	$title=translate_fact('_CREM_FATH');
	$text='';
	break;

case '_CREM_FSIB':
	$title=translate_fact('_CREM_FSIB');
	$text='';
	break;

case '_CREM_GCHI':
	$title=translate_fact('_CREM_GCHI');
	$text='';
	break;

case '_CREM_GGCH':
	$title=translate_fact('_CREM_GGCH');
	$text='';
	break;

case '_CREM_GGPA':
	$title=translate_fact('_CREM_GGPA');
	$text='';
	break;

case '_CREM_GPAR':
	$title=translate_fact('_CREM_GPAR');
	$text='';
	break;

case '_CREM_HSIB':
	$title=translate_fact('_CREM_HSIB');
	$text='';
	break;

case '_CREM_MOTH':
	$title=translate_fact('_CREM_MOTH');
	$text='';
	break;

case '_CREM_MSIB':
	$title=translate_fact('_CREM_MSIB');
	$text='';
	break;

case '_CREM_NEPH':
	$title=translate_fact('_CREM_NEPH');
	$text='';
	break;

case '_CREM_SIBL':
	$title=translate_fact('_CREM_SIBL');
	$text='';
	break;

case '_CREM_SPOU':
	$title=translate_fact('_CREM_SPOU');
	$text='';
	break;

case '_DBID':
	$title=translate_fact('_DBID');
	$text='';
	break;

case '_DEAT_CHIL':
	$title=translate_fact('_DEAT_CHIL');
	$text='';
	break;

case '_DEAT_COUS':
	$title=translate_fact('_DEAT_COUS');
	$text='';
	break;

case '_DEAT_FATH':
	$title=translate_fact('_DEAT_FATH');
	$text='';
	break;

case '_DEAT_FSIB':
	$title=translate_fact('_DEAT_FSIB');
	$text='';
	break;

case '_DEAT_GCHI':
	$title=translate_fact('_DEAT_GCHI');
	$text='';
	break;

case '_DEAT_GGCH':
	$title=translate_fact('_DEAT_GGCH');
	$text='';
	break;

case '_DEAT_GGPA':
	$title=translate_fact('_DEAT_GGPA');
	$text='';
	break;

case '_DEAT_GPAR':
	$title=translate_fact('_DEAT_GPAR');
	$text='';
	break;

case '_DEAT_HSIB':
	$title=translate_fact('_DEAT_HSIB');
	$text='';
	break;

case '_DEAT_MOTH':
	$title=translate_fact('_DEAT_MOTH');
	$text='';
	break;

case '_DEAT_MSIB':
	$title=translate_fact('_DEAT_MSIB');
	$text='';
	break;

case '_DEAT_NEPH':
	$title=translate_fact('_DEAT_NEPH');
	$text='';
	break;

case '_DEAT_SIBL':
	$title=translate_fact('_DEAT_SIBL');
	$text='';
	break;

case '_DEAT_SPOU':
	$title=translate_fact('_DEAT_SPOU');
	$text='';
	break;

case '_DEG':
	$title=translate_fact('_DEG');
	$text='';
	break;

case '_DETS':
	$title=translate_fact('_DETS');
	$text='';
	break;

case '_EMAIL':
	$title=translate_fact('_EMAIL');
	$text='';
	break;

case '_EYEC':
	$title=translate_fact('_EYEC');
	$text='';
	break;

case '_FA1':
	$title=translate_fact('_FA1');
	$text='';
	break;

case '_FA2':
	$title=translate_fact('_FA2');
	$text='';
	break;

case '_FA3':
	$title=translate_fact('_FA3');
	$text='';
	break;

case '_FA4':
	$title=translate_fact('_FA4');
	$text='';
	break;

case '_FA5':
	$title=translate_fact('_FA5');
	$text='';
	break;

case '_FA6':
	$title=translate_fact('_FA6');
	$text='';
	break;

case '_FA7':
	$title=translate_fact('_FA7');
	$text='';
	break;

case '_FA8':
	$title=translate_fact('_FA8');
	$text='';
	break;

case '_FA9':
	$title=translate_fact('_FA9');
	$text='';
	break;

case '_FA10':
	$title=translate_fact('_FA10');
	$text='';
	break;

case '_FA11':
	$title=translate_fact('_FA11');
	$text='';
	break;

case '_FA12':
	$title=translate_fact('_FA12');
	$text='';
	break;

case '_FA13':
	$title=translate_fact('_FA13');
	$text='';
	break;

case '_FAMC_EMIG':
	$title=translate_fact('_FAMC_EMIG');
	$text='';
	break;

case '_FAMC_RESI':
	$title=translate_fact('_FAMC_RESI');
	$text='';
	break;

case '_FNRL':
	$title=translate_fact('_FNRL');
	$text='';
	break;

case '_FREL':
	$title=translate_fact('_FREL');
	$text='';
	break;

case '_GEDF':
	$title=translate_fact('_GEDF');
	$text='';
	break;

case '_HAIR':
	$title=translate_fact('_HAIR');
	$text='';
	break;

case '_HEB':
	$title=translate_fact('_HEB');
	$text=i18n::translate('In many cultures it is customary to have a traditional name spelled in the traditional characters and also a romanized version of the name as it would be spelled or pronounced in languages based on the Latin alphabet, such as English.<br /><br />If you prefer to use the Latin alphabet to enter the name in the standard name fields, then you can use this field to enter the same name in the non-Latin alphabet such as Greek, Hebrew, Russian, Arabic, or Chinese.  Both versions of the name will appear in lists and charts.<br /><br />Although this field is labeled "Hebrew", it is not restricted to containing only Hebrew characters.');
	break;

case '_HEIG':
	$title=translate_fact('_HEIG');
	$text='';
	break;

case '_HNM':
	$title=translate_fact('_HNM');
	$text='';
	break;

case '_HOL':
	$title=translate_fact('_HOL');
	$text='';
	break;

case '_INTE':
	$title=translate_fact('_INTE');
	$text='';
	break;

case '_MARB_CHIL':
	$title=translate_fact('_MARB_CHIL');
	$text='';
	break;

case '_MARB_COUS':
	$title=translate_fact('_MARB_COUS');
	$text='';
	break;

case '_MARB_FAMC':
	$title=translate_fact('_MARB_FAMC');
	$text='';
	break;

case '_MARB_FATH':
	$title=translate_fact('_MARB_FATH');
	$text='';
	break;

case '_MARB_FSIB':
	$title=translate_fact('_MARB_FSIB');
	$text='';
	break;

case '_MARB_GCHI':
	$title=translate_fact('_MARB_GCHI');
	$text='';
	break;

case '_MARB_GGCH':
	$title=translate_fact('_MARB_GGCH');
	$text='';
	break;

case '_MARB_HSIB':
	$title=translate_fact('_MARB_HSIB');
	$text='';
	break;

case '_MARB_MOTH':
	$title=translate_fact('_MARB_MOTH');
	$text='';
	break;

case '_MARB_MSIB':
	$title=translate_fact('_MARB_MSIB');
	$text='';
	break;

case '_MARB_NEPH':
	$title=translate_fact('_MARB_NEPH');
	$text='';
	break;

case '_MARB_SIBL':
	$title=translate_fact('_MARB_SIBL');
	$text='';
	break;

case '_MARI':
	$title=translate_fact('_MARI');
	$text='';
	break;

case '_MARNM':
	$title=translate_fact('_MARNM');
	$text=i18n::translate('Enter the married name for this person, using the same formatting rules that apply to the Name field.  This field is optional.<br /><br />For example, if Mary Jane Brown married John White, you might enter (without the quotation marks, of course)<ul><li>American usage:&nbsp;&nbsp;"Mary Jane Brown /White/"</li><li>European usage:&nbsp;&nbsp;"Mary Jane /White/"</li><li>Alternate European usage:&nbsp;&nbsp;"Mary Jane /White-Brown/" or "Mary Jane /Brown-White/"</li></ul>You should do this only if Mary Brown began calling herself by the new name after marrying John White.  In some places, Quebec (Canada) for example, it\'s illegal for names to be changed in this way.<br /><br />Men sometimes change their name after marriage, most often using the hyphenated form but occasionally taking the wife\'s surname.');
	break;

case '_MARNM_SURN':
	$title=translate_fact('_MARNM_SURN');
	$text='';
	break;

case '_MARR_CHIL':
	$title=translate_fact('_MARR_CHIL');
	$text='';
	break;

case '_MARR_COUS':
	$title=translate_fact('_MARR_COUS');
	$text='';
	break;

case '_MARR_FAMC':
	$title=translate_fact('_MARR_FAMC');
	$text='';
	break;

case '_MARR_FATH':
	$title=translate_fact('_MARR_FATH');
	$text='';
	break;

case '_MARR_FSIB':
	$title=translate_fact('_MARR_FSIB');
	$text='';
	break;

case '_MARR_GCHI':
	$title=translate_fact('_MARR_GCHI');
	$text='';
	break;

case '_MARR_GGCH':
	$title=translate_fact('_MARR_GGCH');
	$text='';
	break;

case '_MARR_HSIB':
	$title=translate_fact('_MARR_HSIB');
	$text='';
	break;

case '_MARR_MOTH':
	$title=translate_fact('_MARR_MOTH');
	$text='';
	break;

case '_MARR_MSIB':
	$title=translate_fact('_MARR_MSIB');
	$text='';
	break;

case '_MARR_NEPH':
	$title=translate_fact('_MARR_NEPH');
	$text='';
	break;

case '_MARR_SIBL':
	$title=translate_fact('_MARR_SIBL');
	$text='';
	break;

case '_MBON':
	$title=translate_fact('_MBON');
	$text='';
	break;

case '_MDCL':
	$title=translate_fact('_MDCL');
	$text='';
	break;

case '_MEDC':
	$title=translate_fact('_MEDC');
	$text='';
	break;

case '_MEND':
	$title=translate_fact('_MEND');
	$text='';
	break;

case '_MILI':
	$title=translate_fact('_MILI');
	$text='';
	break;

case '_MILT':
	$title=translate_fact('_MILT');
	$text='';
	break;

case '_MREL':
	$title=translate_fact('_MREL');
	$text='';
	break;

case '_MSTAT':
	$title=translate_fact('_MSTAT');
	$text='';
	break;

case '_NAME':
	$title=translate_fact('_NAME');
	$text='';
	break;

case '_NAMS':
	$title=translate_fact('_NAMS');
	$text='';
	break;

case '_NLIV':
	$title=translate_fact('_NLIV');
	$text='';
	break;

case '_NMAR':
	$title=translate_fact('_NMAR');
	$text='';
	break;

case '_NMR':
	$title=translate_fact('_NMR');
	$text='';
	break;

case '_PRIM':
	$title=translate_fact('_PRIM');
	$text=i18n::translate('Use this field to signal that this media item is the highlighted or primary item for the person it is attached to.  The highlighted image is the one that will be used on charts and on the Individual page.');
	break;

case '_WT_USER':
	$title=translate_fact('_WT_USER');
	$text='';
	break;

case '_PRMN':
	$title=translate_fact('_PRMN');
	$text='';
	break;

case '_SCBK':
	$title=translate_fact('_SCBK');
	$text='';
	break;

case '_SEPR':
	$title=translate_fact('_SEPR');
	$text='';
	break;

case '_SSHOW':
	$title=translate_fact('_SSHOW');
	$text='';
	break;

case '_STAT':
	$title=translate_fact('_STAT');
	$text='';
	break;

case '_SUBQ':
	$title=translate_fact('_SUBQ');
	$text='';
	break;

case '_THUM':
	$title=translate_fact('_THUM');
	$text=i18n::translate('This option lets you override the usual selection for a thumbnail image.<br /><br />The GEDCOM has a configuration option that specifies whether <b>webtrees</b> should send the large or the small image to the browser whenever the current page requires a thumbnail.  The &laquo;Always use main image?&raquo; option, when set to <b>Yes</b>, temporarily overrides the setting of the GEDCOM configuration option, so that <b>webtrees</b> will always send the large image.  You cannot force <b>webtrees</b> to send the small image when the GEDCOM configuration specifies that large images should always be used.<br /><br /><b>webtrees</b> does not re-size the image being sent; the browser does this according to the page specifications it has also received.  This can have undesirable consequences when the image being sent is not truly a thumbnail where <b>webtrees</b> is expecting to send a small image.  This is not an error:  There are occasions where it may be desirable to display a large image in places where one would normally expect to see a thumbnail-sized picture.<br /><br />You should avoid setting the &laquo;Always use main image?&raquo; option to <b>Yes</b>.  This choice will cause excessive amounts of image-related data to be sent to the browser, only to have the browser discard the excess.  Page loads, particularly of charts with many images, can be seriously slowed.');
	break;

case '_TODO':
	$title=translate_fact('_TODO');
	$text='';
	break;

case '_TYPE':
	$title=translate_fact('_TYPE');
	$text='';
	break;

case '_UID':
	$title=translate_fact('_UID');
	$text='';
	break;

case '_URL':
	$title=translate_fact('_URL');
	$text='';
	break;

case '_WEIG':
	$title=translate_fact('_WEIG');
	$text='';
	break;

case '_YART':
	$title=translate_fact('_YART');
	$text='';
	break;

case '__BRTM_CHIL':
	$title=translate_fact('__BRTM_CHIL');
	$text='';
	break;

case '__BRTM_COUS':
	$title=translate_fact('__BRTM_COUS');
	$text='';
	break;

case '__BRTM_FSIB':
	$title=translate_fact('__BRTM_FSIB');
	$text='';
	break;

case '__BRTM_GCHI':
	$title=translate_fact('__BRTM_GCHI');
	$text='';
	break;

case '__BRTM_GGCH':
	$title=translate_fact('__BRTM_GGCH');
	$text='';
	break;

case '__BRTM_HSIB':
	$title=translate_fact('__BRTM_HSIB');
	$text='';
	break;

case '__BRTM_MSIB':
	$title=translate_fact('__BRTM_MSIB');
	$text='';
	break;

case '__BRTM_NEPH':
	$title=translate_fact('__BRTM_NEPH');
	$text='';
	break;

case '__BRTM_SIBL':
	$title=translate_fact('__BRTM_SIBL');
	$text='';
	break;


	//////////////////////////////////////////////////////////////////////////////
	// This section contains an entry for every configuration item
	//////////////////////////////////////////////////////////////////////////////

case 'ABBREVIATE_CHART_LABELS':
	$title=i18n::translate('Abbreviate chart labels');
	$text=i18n::translate('This option controls whether or not to abbreviate labels like <b>Birth</b> on charts with just the first letter like <b>B</b>.');
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

case 'CALENDAR_FORMAT':
	$title=i18n::translate('Calendar format');
	$text=i18n::translate('Dates can be recorded in various calendars such as Gregorian, Julian, or the Jewish Calendar.  This option allows you to convert dates to a preferred calendar.  For example, you could select Gregorian to convert Julian and Hebrew dates to Gregorian.  The converted date is shown in parentheses after the regular date.<br /><br />Dates are only converted if they are valid for the calendar.  For example, only dates between 22&nbsp;SEP&nbsp;1792 and 31&nbsp;DEC&nbsp;1805 will be converted to the French Republican calendar and only dates after 15&nbsp;OCT&nbsp;1582 will be converted to the Gregorian calendar.<br /><br />Hebrew is the same as Jewish, but using Hebrew characters.  Arabic is the same as Hijri, but using Arabic characters.<br /><br />Note: Since the Jewish and Hijri calendar day starts at dusk, any event taking place from dusk till midnight will display as one day prior to the correct date.  The display of Hebrew and Arabic can be problematic in old browsers, which may display text backwards (left to right) or not at all.');
	break;

case 'CHART_BOX_TAGS':
	$title=i18n::translate('Other facts to show in charts');
	$text=i18n::translate('This should be a comma or space separated list of facts, in addition to Birth and Death, that you want to appear in chart boxes such as the Pedigree chart.  This list requires you to use fact tags as defined in the GEDCOM 5.5.1 Standard.  For example, if you wanted the occupation to show up in the box, you would add "OCCU" to this field.');
	break;

case 'CHECK_MARRIAGE_RELATIONS':
	$title=i18n::translate('Check relationships by marriage');
	$text=i18n::translate('When calculating relationships, this option controls whether <b>webtrees</b> will include spouses/partners as well as blood relatives.');
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

case 'CONTACT_USER_ID':
	$title=i18n::translate('Genealogy contact');
	$text=i18n::translate('The person to contact about the genealogical data on this site.');
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

case 'ENABLE_AUTOCOMPLETE':
	$title=i18n::translate('Enable autocomplete');
	$text=i18n::translate('This option determines whether Autocomplete should be active while information is being entered into certain fields on input forms.  When this option is set to <b>Yes</b>, text input fields for which Autocomplete is possible are indicated by a differently colored background.<br /><br />When Autocomplete is active, <b>webtrees</b> will search its database for possible matches according to what you have already entered.  As you enter more information, the list of possible matches is refined.  When you see the desired input in the list of matches, you can move the mouse cursor to that line of the list and then click the left mouse button to complete the input.<br /><br />The disadvantages of Autocomplete are that it slows the program, entails significant database activity, and also results in more data being sent to the browser.');
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
	$text=i18n::translate('Many genealogy programs create GEDCOM files with custom tags, and <b>webtrees</b> understands most of them.  When unrecognised tags are found, this option lets you choose whether to ignore them or display a warning message.');
	break;

case 'HIDE_LIVE_PEOPLE':
        $title=i18n::translate('Enable privacy');
        $text=i18n::translate('This option will enable all privacy settings and hide the details of living people, as defined or modified on the Privacy tab of each GEDCOM\'s configuration page.');
        $text .= '<p>';
		$text .= i18n::plural('Note: "living" is defined (if no death or burial is known) as ending %d year after birth or estimated birth.','Note: "living" is defined (if no death or burial is known) as ending %d years after birth or estimated birth.', get_gedcom_setting(WT_GED_ID, 'MAX_ALIVE_AGE'), get_gedcom_setting(WT_GED_ID, 'MAX_ALIVE_AGE'));
		$text .= ' ';
		$text .= i18n::translate('The length of time after birth can be set on the Privacy configuration tab option "Age at which to assume a person is dead".');
		$text .= '</p>';
        break;

case 'INDEX_DIRECTORY':
	$title=i18n::translate('Data file directory');
	$text=i18n::translate('The path to a readable and writable directory where <b>webtrees</b> should store data files (include the trailing "/").  <b>webtrees</b> does not require this directory\'s name to be "data".  You can choose any name you like.<br /><br />For security, this directory should be placed somewhere in the server\'s file space that is not accessible from the Internet. An example of such a structure follows:<br /><b>webtrees:</b> dir1/dir2/dir3/webtrees<br /><b>Index:</b> dir1/dir4/dir5/dir6/data<br /><br />For the example shown, you would enter <b>../../dir4/dir5/dir6/data/</b> in this field.');
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

case 'KEEP_ALIVE':
	$title=i18n::translate('Apply living rules to recently deceased');
	$text=i18n::translate('In some countries, privacy laws apply not only to living people, but also to those who have died recently.  This option will allow you to extend the privacy rules for living people to those who were born or died within a specified number of years.  Leave these values empty to disable this feature.');
	break;

case 'LANGUAGE':
	$title=i18n::translate('Language');
	$text=i18n::translate('Assign the default language for the site.<br /><br />When the <b>Allow user to change language</b> option is set, users can override this setting through their browser\'s preferred language configuration, configuration options on their Account page, or through links or buttons on most <b>webtrees</b> pages.');
	break;

case 'LINK_ICONS':
	$title=i18n::translate('PopUp links on charts');
	$text=i18n::translate('Allows the user to select links to other charts and close relatives of the person.<br /><br />Set to <b>Disabled</b> to disable this feature.  Set to <b>On Mouse Over</b> to popup the links when the user mouses over the icon in the box.  Set to <b>On Mouse Click</b> to popup the links when the user clicks on the icon in the box.');
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

case 'MAX_EXECUTION_TIME':
	// Find the default value for max_execution_time
	ini_restore('max_execution_time');
	$dflt_cpu=ini_get('max_execution_time');
	$title=i18n::translate('PHP time limit');
	$text=
		i18n::plural(
			'By default, your server allows scripts to run for %s second.',
			'By default, your server allows scripts to run for %s seconds.',
			$dflt_cpu, $dflt_cpu
		).
		' '.
		i18n::translate('You can request a higher or lower limit, although the server may ignore this request.').
		' '.
		i18n::translate('If you leave this setting empty, the default value will be used.');
	break;

case 'MAX_PEDIGREE_GENERATIONS':
	$title=i18n::translate('Maximum pedigree generations');
	$text=i18n::translate('Set the maximum number of generations to display on Pedigree charts.');
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
	$text=i18n::translate('Directory in which the protected Media directory can be created.  When this field is empty, the <b>%s</b> directory will be used.', get_site_setting('INDEX_DIRECTORY'));
	break;

case 'MEDIA_FIREWALL_THUMBS':
	$title=i18n::translate('Protect thumbnails of protected images');
	$text=i18n::translate('When an image is in the protected Media directory, should its thumbnail be protected as well?');
	break;

case 'MEDIA_ID_PREFIX':
	$title=i18n::translate('Media ID prefix');
	$text=i18n::translate('When a new media record is added online in <b>webtrees</b>, a new ID for that media will be generated automatically. The media ID will have this prefix.');
	break;

case 'MEMORY_LIMIT':
	// Find the default value for max_execution_time
	ini_restore('memory_limit');
	$dflt_mem=ini_get('memory_limit');
	$title=i18n::translate('Memory limit');
	$text=i18n::translate('By default, your server allows scripts to use %s of memory.', $dflt_mem).
		' '.
		i18n::translate('You can request a higher or lower limit, although the server may ignore this request.').
		' '.
		i18n::translate('If you leave this setting empty, the default value will be used.');
	break;

case 'META_DESCRIPTION':
	$title=i18n::translate('Description META tag');
	$text=i18n::translate('The value to place in the Description meta tag in the HTML page header.  Leave this field empty to use the title of the currently active database.');
	break;

case 'META_ROBOTS':
	$title=i18n::translate('Robots META tag');
	$text=i18n::translate('The value to place in the Robots meta tag in the HTML page header.  Some robots or web crawlers ignore this value.');
	break;

case 'META_TITLE':
	$title=i18n::translate('Add to TITLE header tag');
	$text=i18n::translate('This text will be appended to each page title.  It will be shown in the browser\'s title bar, bookmarks, etc.');
	break;

case 'MULTI_MEDIA':
	$title=i18n::translate('Enable multimedia features');
	$text=i18n::translate('<b>webtrees</b> allows you to link pictures, videos, and other multimedia objects to your GEDCOM.  If you do not use multimedia, you can disable the multimedia features.');
	break;

case 'NOTE_ID_PREFIX':
	$title=i18n::translate('Note ID prefix');
	$text=i18n::translate('When a new note record is added online in <b>webtrees</b>, a new ID for that note will be generated automatically. The note ID will have this prefix.');
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

case 'RELATIONSHIP_PATH_LENGTH':
	$title=i18n::translate('Maximum relationship path length');
	$text=
		i18n::translate('Where a user is associated to an individual in the database, you can prevent them from accessing the details of distant relations.  You specify the number of relationship steps that the user is allowed to see.  This option only affects access to living people, access to dead people is covered by the global privacy settings.').
		'<br/><br/>'.
		i18n::translate('For example, if you specify a path length of 2, the person will be able to see their grandson (child, child), their aunt (parent, sibling), their step-daughter (spouse, child), but not their first cousin (parent, sibling, child).').
		'<br/><br/>'.
		i18n::translate('Note: longer path lengths require a lot of calculation, which can make your site run slowly for these users.');
	break;

case 'SESSION_TIME':
	$title=i18n::translate('Session timeout');
	$text=i18n::translate('The time in seconds that a <b>webtrees</b> session remains active before requiring a login.  The default is 7200, which is 2 hours.');
	break;

case 'SMTP_ACTIVE':
	$title=i18n::translate('Use SMTP to send external mails');
	$text=i18n::translate('Use SMTP to send e-mails from <b>webtrees</b>.<br /><br />This option requires access to an SMTP mail server.  When set to <b>No</b> <b>webtrees</b> will use the e-mail system built into PHP on this server.');
	break;

case 'SMTP_AUTH_PASS':
	$title=i18n::translate('Password');
	$text=i18n::translate('The password required for authentication with the SMTP server.');
	break;

case 'SMTP_AUTH_USER':
	$title=i18n::translate('Username');
	$text=i18n::translate('The user name required for authentication with the SMTP server.');
	break;

case 'SMTP_AUTH':
	$title=i18n::translate('Username and password');
	$text=i18n::translate('Use name and password authentication to connect to the SMTP server.<br /><br />Some SMTP servers require all connections to be authenticated before they will accept outbound e-mails.');
	break;

case 'SMTP_FROM_NAME':
	$title=i18n::translate('Sender name');
	$text=i18n::translate('Enter the name to be used in the &laquo;From:&raquo; field of e-mails originating at this site.<br /><br />For example, if your name is <b>John Smith</b> and you are the site administrator for a site that is  known as <b>Jones Genealogy</b>, you could enter something like <b>John Smith</b> or <b>Jones Genealogy</b> or even <b>John Smith, Administrator: Jones Genealogy</b>.  You may enter whatever you wish, but HTML is not permitted.');
	break;

case 'SMTP_HELO':
	$title=i18n::translate('Sending domain name');
	$text=i18n::translate('This is the domain part of a valid e-mail address on the SMTP server.<br /><br />For example, if you have an e-mail account such as <b>yourname@abc.xyz.com</b>, you would enter <b>abc.xyz.com</b> here.');
	break;

case 'SMTP_HOST':
	$title=i18n::translate('Outgoing server (SMTP) name');
	$text=i18n::translate('This is the name of the SMTP mail server.  Example: <b>smtp.foo.bar.com</b>.<br /><br />Configuration values for some e-mail providers:<br /><br /><b>Gmail<br /></b><br /><b>Outgoing server (SMTP) name:</b> smtp.gmail.com<br /><b>SMTP Port:</b> 465 or 587<br /><b>Secure connection:</b> SSL<br /><br /><b>Hotmail<br /></b><br /><b>Outgoing server (SMTP) name:</b> smtp.live.com<br /><b>SMTP Port:</b> 25 or 587<br /><b>Secure connection:</b> TLS<br /><br /><b>Yahoo Mail Plus (currently a paid service)<br /></b><br /><b>Outgoing server (SMTP) name:</b> smtp.mail.yahoo.com<br /><b>SMTP Port:</b> 25');
	break;

case 'SMTP_PORT':
	$title=i18n::translate('SMTP port');
	$text=i18n::translate('The port number to be used for connections to the SMTP server.  Generally, this is port <b>25</b>.');
	break;

case 'SMTP_SIMPLE_MAIL':
	$title=i18n::translate('Use simple mail headers in external mails');
	$text=i18n::translate('In normal mail headers for external mails, the email address as well as the name are used. Some mail systems will not accept this. When set to <b>Yes</b>, only the email address will be used.');
	break;

case 'SMTP_SSL':
	$title=i18n::translate('Secure connection');
	$text=i18n::translate('Transport Layer Security (TLS) and Secure Sockets Layer (SSL) are Internet data encryption protocols.<br /><br />TLS 1.0, 1.1 and 1.2 are standardized developments of SSL 3.0. TLS 1.0 and SSL 3.1 are equivalent. Further work on SSL is now done under the new name, TLS.<br /><br />If your SMTP Server requires the SSL protocol during login, you should select the <b>SSL</b> option. If your SMTP Server requires the TLS protocol during login, you should select the <b>TLS</b> option.');
	break;

case 'STORE_MESSAGES':
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

case 'QUICK_REQUIRED_FACTS':
	$title=i18n::translate('Facts for new individuals');
	$text=i18n::translate('This is a comma separated list of GEDCOM fact tags that will be shown when adding a new person.  For example, if BIRT is in the list, fields for birth date and birth place will be shown on the form.');
	break;

case 'QUICK_REQUIRED_FAMFACTS':
	$title=i18n::translate('Facts for new families');
	$text=i18n::translate('This is a comma separated list of GEDCOM fact tags that will be shown when adding a new family.  For example, if MARR is in the list, then fields for marriage date and marriage place will be shown on the form.');
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

case 'SAVE_WATERMARK_IMAGE':
	$title=i18n::translate('Store watermarked full size images on server?');
	$text=i18n::translate('If the Media Firewall is enabled, should copies of watermarked full size images be stored on the server in addition to the same images without watermarks?<br /><br />When set to <b>Yes</b>, full-sized watermarked images will be produced more quickly at the expense of higher server disk space requirements.');
	break;

case 'SAVE_WATERMARK_THUMB':
	$title=i18n::translate('Store watermarked thumbnails on server?');
	$text=i18n::translate('If the Media Firewall is enabled, should copies of watermarked thumbnails be stored on the server in addition to the same thumbnails without watermarks?<br /><br />When set to <b>Yes</b>, media lists containing watermarked thumbnails will be produced more quickly at the expense of higher server disk space requirements.');
	break;

case 'SERVER_URL':
	$title=i18n::translate('Website URL');
	$text=i18n::translate('If your site can be reached using more than one URL, such as <b>http://www.example.com/webtrees/</b> and <b>http://webtrees.example.com/</b>, you can specify the preferred URL.  Requests for the other URLs will be redirected to the preferred one.');
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
	$text=i18n::translate('If you have enabled multi-media in your site, this option will display a person\'s thumbnail image next to their name in charts and boxes.');
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
	$text=i18n::translate('This option will retain family links in private records.  This means that you will see empty "private" boxes on the pedigree chart and on other charts with private people.');
	break;

case 'SHOW_REGISTER_CAUTION':
	$title=i18n::translate('Show acceptable use agreement on «Request new user account» page');
	$text=i18n::translate('When set to <b>Yes</b>, the following message will appear above the input fields on the «Request new user account» page:<div class="list_value_wrap"><div class="largeError">Notice:</div><div class="error">By completing and submitting this form, you agree:<ul><li>to protect the privacy of living people listed on our site;</li><li>and in the text box below, to explain to whom you are related, or to provide us with information on someone who should be listed on our site.</li></ul></div></div>');
	break;

case 'SHOW_RELATIVES_EVENTS':
	$title=i18n::translate('Show events of close relatives on individual page');
	$text=i18n::translate('Births, marriages, and deaths of relatives are important events in one\'s life. This option controls whether or not to show these events on the <i>Personal facts and details</i> tab on the Individual page.<br /><br />The events affected by this option are:<ul><li>Death of spouse</li><li>Birth and death of children</li><li>Death of parents</li><li>Birth and death of siblings</li><li>Death of grand-parents</li><li>Birth and death of parents\' siblings</li></ul>');
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

case 'SURNAME_LIST_STYLE':
	$title=i18n::translate('Surname list style');
	$text=i18n::translate('<p>Lists of surnames, as they appear in the Top 10 Surnames block, the Individual lists, and the Family lists, can be shown in different styles.</p><dl><dt>Table</dt><dd>In this style, the surnames are shown in a table that can be sorted either by surname or by count.</dd><dt>Tag cloud</dt><dd>In this style, the surnames are shown in a list, and the font size used for each name depends on the number of occurrences of that name in the database.  The list is not sortable.</dd><dt>List</dt><dd>This is a simple list of names, with a count of each name, in a tabulated format of up to four columns</dd></dl>');
	break;

case 'SURNAME_TRADITION':
	$title=i18n::translate('Surname tradition');
	$text=i18n::translate('When you add new members to a family, <b>webtrees</b> can supply default values for surnames according to regional custom.<br /><br /><ul><li>In the <b>Paternal</b> tradition, all family members share the father\'s surname.</li><li>In the <b>Spanish</b> and <b>Portuguese</b> tradition, children receive a surname from each parent.</li><li>In the <b>Icelandic</b> tradition, children receive their male parent\'s given name as a surname, with a suffix that denotes gender.</li><li>In the <b>Polish</b> tradition, all family members share the father\'s surname. For some surnames, the suffix indicates gender.  The suffixes <i>ski</i>, <i>cki</i>, and <i>dzki</i> indicate male, while the corresponding suffixes <i>ska</i>, <i>cka</i>, and <i>dzka</i> indicate female.</li></ul>');
	break;

case 'THEME':
	$title=i18n::translate('Theme');
	$text=
		i18n::translate('You can change the appearance of <b>webtrees</b> using "themes".  Each theme has a different style, layout, color scheme, etc.').
		'<br/><br/>'.
		i18n::translate('Themes can be selected at three levels: user, GEDCOM, and site.  User settings take priority over GEDCOM settings, which in turn take priority over the site setting.  Selecting "default theme" at user level will give the setting for the current GEDCOM.  Selecting "default theme" at GEDCOM level will give the site setting.');
	break;

case 'THUMBNAIL_WIDTH':
	$title=i18n::translate('Width of generated thumbnails');
	$text=i18n::translate('This is the width (in pixels) that the program will use when automatically generating thumbnails.  The default setting is 100.');
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
	$text=''; // TODO: the original help text refered to the PGV wiki page (http://wiki.phpgedview.net/en/index.php?title=Media_Firewall), which is out-of-date.  We should either write an equivalent page for webtrees, or put some help inline.
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
	$text=i18n::translate('Use silhouette images when no highlighted image for that person has been specified.  The images used are specific to the gender of the person in question.<br /><br /><table><tr><td wrap valign="middle">This image might be used when the gender of the person is unknown:')
	." </td><td><img src=\"".$WT_IMAGES["default_image_U"]."\" width=\"40\" alt=\"\" title=\"\" /></td></tr></table>";
	break;

case 'USE_THUMBS_MAIN':
	$title=i18n::translate('Use thumbnail');
	$text=i18n::translate('This option determines whether <b>webtrees</b> should send the large or the small image to the browser whenever a chart or the Personal Details page requires a thumbnail.<br /><br />The <b>No</b> choice will cause <b>webtrees</b> to send the large image, while the <b>Yes</b> choice will cause the small image to be sent.  Each individual image also has the &laquo;Always use main image?&raquo; option which, when set to <b>Yes</b>, will cause the large image to be sent regardless of the setting of the &laquo;Use thumbnail&raquo; option in the GEDCOM configuration.  You cannot force <b>webtrees</b> to send small images when the GEDCOM configuration specifies that large images should always be used.<br /><br /><b>webtrees</b> does not re-size the image being sent; the browser does this according to the page specifications it has also received.  This can have undesirable consequences when the image being sent is not truly a thumbnail where <b>webtrees</b> is expecting to send a small image.  This is not an error:  There are occasions where it may be desirable to display a large image in places where one would normally expect to see a thumbnail-sized picture.<br /><br />You should avoid setting the &laquo;Use thumbnail&raquo; option to <b>No</b>.  This choice will cause excessive amounts of image-related data to be sent to the browser, only to have the browser discard the excess.  Page loads, particularly of charts with many images, can be seriously slowed.');
	break;

case 'WATERMARK_THUMB':
	$title=i18n::translate('Add watermarks to thumbnails?');
	$text=i18n::translate('If the Media Firewall is enabled, should thumbnails be watermarked? Your media lists will load faster if you don\'t watermark the thumbnails.');
	break;

case 'WEBMASTER_USER_ID':
	$title=i18n::translate('Support contact');
	$text=i18n::translate('The person to be contacted about technical questions or errors encountered on your site.');
	break;

case 'WELCOME_TEXT_AUTH_MODE_CUST_HEAD':
	$title=i18n::translate('Standard header for custom welcome text');
	$text=i18n::translate('Choose to display a standard header for your custom Welcome text.  When your users change language, this header will appear in the new language.<br /><br />If set to <b>Yes</b>, the header will look like this:<div class="list_value_wrap"><center><b>Welcome to this Genealogy website</b></center><br />Access is permitted to users who have an account and a password for this website.<br /></div>');
	break;

case 'WELCOME_TEXT_AUTH_MODE_CUST':
	$title=i18n::translate('Custom welcome text');
	$text=i18n::translate('If you have opted for custom welcome text, you can type that text here.  To set this text for other languages, you must switch to that language, and visit this page again.');
	break;

case 'WELCOME_TEXT_AUTH_MODE':
	$title=i18n::translate('Welcome text on login page');
	$text=i18n::translate('Here you can choose text to appear on the login screen. You must determine which predefined text is most appropriate.<br /><br />You can also choose to enter your own custom Welcome text.  Please refer to the Help text associated with the <b>Custom Welcome text</b> field for more information.<br /><br />The predefined texts are:<ul><li><b>Predefined text that states all users can request a user account:</b><div class="list_value_wrap"><center><b>Welcome to this Genealogy website</b></center><br />Access to this site is permitted to every visitor who has a user account.<br /><br />If you have a user account, you can login on this page.  If you don\'t have a user account, you can apply for one by clicking on the appropriate link below.<br /><br />After verifying your application, the site administrator will activate your account.  You will receive an email when your application has been approved.</div><br/></li><li><b>Predefined text that states admin will decide on each request for a user account:</b><div class="list_value_wrap"><center><b>Welcome to this Genealogy website</b></center><br />Access to this site is permitted to <u>authorized</u> users only.<br /><br />If you have a user account you can login on this page.  If you don\'t have a user account, you can apply for one by clicking on the appropriate link below.<br /><br />After verifying your information, the administrator will either approve or decline your account application.  You will receive an email message when your application has been approved.</div><br/></li><li><b>Predefined text that states only family members can request a user account:</b><div class="list_value_wrap"><center><b>Welcome to this Genealogy website</b></center><br />Access to this site is permitted to <u>family members only</u>.<br /><br />If you have a user account you can login on this page.  If you don\'t have a user account, you can apply for one by clicking on the appropriate link below.<br /><br />After verifying the information you provide, the administrator will either approve or decline your request for an account.  You will receive an email when your request is approved.</div></li></ul>');
	break;

case 'WORD_WRAPPED_NOTES':
	$title=i18n::translate('Add spaces where notes were wrapped');
	$text=i18n::translate('Some genealogy programs wrap notes at word boundaries while others wrap notes anywhere.  This can cause <b>webtrees</b> to run words together.  Setting this to <b>Yes</b> will add a space between words where they are wrapped in the original GEDCOM during the import process. If you have already imported the file you will need to re-import it.');
	break;

case 'ZOOM_BOXES':
	$title=i18n::translate('Zoom boxes on charts');
	$text=i18n::translate('Allows a user to zoom boxes on charts to get more information.<br /><br />Set to <b>Disabled</b> to disable this feature.  Set to <b>On Mouse Over</b> to zoom boxes when the user mouses over the icon in the box.  Set to <b>On Mouse Click</b> to zoom boxes when the user clicks on the icon in the box.');
	break;

	//////////////////////////////////////////////////////////////////////////////
	// This section should contain an entry for every page.
	//////////////////////////////////////////////////////////////////////////////

case 'addmedia.php':
	$title=i18n::translate('Add a new media item');
	$text='';
	break;

case 'addsearchlink.php':
	$title=i18n::translate('Add a local link');
	$text='';
	break;

case 'admin.php':
	$title=i18n::translate('Administration');
	$text=/* I18N: do not translate this text - it will be updated very soon */i18n::translate('On this page you will find links to the configuration pages, administration pages, documentation, and log files.<br /><br /><b>Current Server Time:</b>, just below the page title, shows the time of the server on which your site is hosted. This means that if the server is located in New York while you\'re in France, the time shown will be six hours less than your local time, unless, of course, the server is running on Greenwich Mean Time (GMT).  The time shown is the server time when you opened or refreshed this page.');
	break;

case 'ancestry.php':
	$title=i18n::translate('Ancestry chart');
	$text=i18n::translate('The Ancestry page is very similar to the <a href="?help=pedigree.php">Pedigree Tree</a>, but with more details and alternate <a href="?help=chart_style">Chart style</a> displays.<br /><br />Each ancestry is shown with a unique number, calculated according to the <i>Sosa-Stradonitz</i> system:<div style="padding-left:30px;"><b>Even</b> numbers for men (child*2)<br /><b>Odd</b> numbers for women (husband+1) except for <b>1</b></div><br />Example:<br /><div style="padding-left:30px;">The root person is <b>1</b>, regardless of gender.<br /><b>1</b>\'s father is <b>2</b> (<b>1</b> * 2), mother is <b>3</b> (<b>2</b> + 1).<br /><b>2</b>\'s father is <b>4</b> (<b>2</b> * 2), mother is <b>5</b> (<b>4</b> + 1).<br /><b>3</b>\'s father is <b>6</b> (<b>3</b> * 2), mother is <b>7</b> (<b>6</b> + 1).<br /><b>7</b>\'s father is <b>14</b> (<b>7</b> * 2), mother is <b>15</b> (<b>14</b> +1).</div><br />etc.');
	break;

case 'branches.php':
	$title=i18n::translate('Branches');
	$text='';
	break;

case 'calendar.php':
	// menu
	$title=i18n::translate('Anniversary calendar');
	$text=i18n::translate('The anniversary calendar shows the persons and families who are linked to an event at a certain day or month or during a certain period of time. It has an advanced filtering system to select the right date, period, and events for you.<ul><li><a href="?help=annivers_date_select"><b>Day:</b></a></li><li><a href="?help=annivers_month_select"><b>Month:</b></a></li><li><a href="?help=annivers_year_select"><b>Year:</b></a></li><li><a href="?help=annivers_show"><b>Show / Show events of:</b></a></li><li><a href="?help=annivers_sex"><b>Gender</b></a></li><li><a href="?help=annivers_event"><b>Event</b></a></li><li><a href="?help=day_month"><b>View day / View month / View year</b></a></li><li><a href="?help=annivers_tip"><b>Tip</b></a></li></ul>');
	break;

case 'compact.php':
	$title=i18n::translate('Compact chart');
	$text='';
	break;

case 'descendancy.php':
	$title=i18n::translate('Descendancy chart');
	$text=i18n::translate('This page will show the descendants of a person.<br /><br />You can choose a starting (root) person for this Descendancy chart or you can be linked to this page by clicking the <b>Descendancy Chart</b> link on another page.  Click on Arrow icons to navigate this tree in the direction of the arrow.  Click on the Chart icon in any Person box to change the root of the tree to that person.');
	break;

case 'edit_merge.php':
	$title=i18n::translate('Merge records');
	$text=i18n::translate('This page will allow you to merge two GEDCOM records from the same GEDCOM file.<br /><br />This is useful for people who have merged GEDCOMs and now have many people, families, and sources that are the same.<br /><br />The page consists of three steps.<br /><ol><li>You enter two GEDCOM IDs.  The IDs <u>must</u> be of the same type.  You cannot merge an individual and a family or family and source, for example.<br />In the <b>Merge To ID:</b> field enter the ID of the record you want to be the new record after the merge is complete.<br />In the <b>Merge From ID:</b> field enter the ID of the record whose information will be merged into the Merge To ID: record.  This record will be deleted after the Merge.</li><li>You select what facts you want to keep from the two records when they are merged.  Just click the checkboxes next to the ones you want to keep.</li><li>You inspect the results of the merge, just like with all other changes made online.</li></ol>Someone with Accept rights will have to authorize your changes to make them permanent.');
	break;

case 'editconfig_gedcom.php':
	$title=i18n::translate('GEDCOM configuration');
	$text=i18n::translate('Each genealogical database is configured independently, and this form allows you to change the settings for the current GEDCOM.');
	break;

case 'edituser.php':
	$title=i18n::translate('My account');
	$text=i18n::translate('Here you can change your settings and preferences.<br /><br />You can change your user name, full name, password, language, email address, theme of the site, and preferred contact method.<br /><br />You cannot change the GEDCOM INDI record ID; that has to be done by an administrator.');
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
	$title=i18n::translate('Family list page');
	$text=i18n::translate('On this page you can display a list of families.  The names will be displayed with surnames first and sorted into alphabetical order.<br /><br />The output of the Name list depends on:<ol><li>The letter you clicked in the Alphabetical index.</li><li>Whether you clicked "Skip" or "Show" Surname List.</li></ol>You can search on the husband\'s or the wife\'s surname;  both are included in the list.<br /><br />More help is available by clicking the <b>?</b> next to items on the page.');
	break;

case 'fanchart.php':
	$title=i18n::translate('Circle diagram page');
	$text=i18n::translate('The Circle Diagram is very similar to the <a href="?help=pedigree.php">Pedigree Tree</a>, but in a more graphical way.<br /><br />The Root person is shown in the center, his parents on the first ring, grandparents on the second ring, and so on.<br /><br />Years of birth and death are printed under the name when known.<br /><br />Clicking on a name on the chart will open a links menu specific to that person.  From this menu you can choose to center the diagram on that person or on one of that person\'s close relatives, or you can jump to that person\'s Individual Information page or a different chart for that person.');
	break;

case 'gedcheck.php':
	$title=i18n::translate('GEDCOM checker');
	$text='';
	break;

case 'hourglass.php':
	$title=i18n::translate('Hourglass chart');
	$text=i18n::translate('The Hourglass chart will show the ancestors and descendants of the selected root person on the same chart.  This chart is a mix between the Descendancy chart and the Pedigree chart.<br /><br />The root person is centered in the middle of the page with his descendants listed to the left and his ancestors listed to the right.  In this view, each generation is lined up across the page starting with the earliest generation and ending with the latest.<br /><br />If there is a downwards arrow on the screen under the root person, clicking on it will display a list of the root person\'s close family members that you can use the navigate down the chart.  Selecting a name from this list will reload the chart with the selected person as the new root person.');
	break;

case 'index.php':
	// This page does not have its own "help with this page".  Instead, it uses either index_portal, or mypage_portal
	break;

case 'indilist.php':
	$title=i18n::translate('Individuals list page');
	$text=i18n::translate('On this page you can display a list of individuals.  The names will be displayed with surnames first and sorted into alphabetical order.<br /><br />The output of the Name list depends on:<ol><li>The letter you clicked in the Alphabetical index.</li><li>Whether you clicked "Skip" or "Show" Surname List.</li></ol>More help is available by clicking the <b>?</b> next to items on the page.');
	break;

case 'individual.php':
	$title=i18n::translate('Individual information');
	$text=i18n::translate('All details of a person are displayed on this page.<br /><br />If there is a picture available, you will see it at the top left side.  You will see the names of the person next to the picture.<br /><br />Names can have notes and sources attached to them. If any of the names have notes or sources, you will see them listed under the names they relate to.<br /><br />A person might have an AKA (maybe he\'s known under another name). If that is the case, it will be displayed.<br /><br />If you have Edit rights to this person, you will also see <b>Edit</b> and <b>Delete</b> links next to the items that you can edit.<br /><br />On this page you see tab sheets for <b>Personal Facts and Details</b>, <b>Notes</b>, <b>Sources</b>, <b>Media</b>, and <b>Close Relatives</b>.  These tab sheets show you all the information about this individual that is stored in the database.<br /><ul><li>The <b>Personal Facts and Details</b> tab will show you the facts and details about this person and any fact from their marriages. Clicking on any date on this tab will take you to the Anniversary Calendar for that date, so that you can see other events that happened on the same day. Clicking on a place will take you to the Place Hierarchy where you can view other people who had events in the same place. For marriage and other family related facts, the name of the person\'s spouse is available so that you can view the spouse and a link to the family record is also provided.</li><li>The <b>Notes</b> tab will show you any general notes relating to this person.</li><li>The <b>Sources</b> tab will show you all of the <u>general</u> sources for this person. These sources are <u>not</u> linked to individual facts, not even the person\'s name; they are associated with the individual himself.  Clicking on the title of a source will take you to a more detailed Source Information page that will display other people who are also linked to the same source.</li><li>The <b>Media</b> tab will list all of the pictures and other media items that are attached to this individual. Clicking on a thumbnail of the picture will open up a larger view of the image. Clicking on the picture caption will show you the picture on the MultiMedia page.</li><li>The <b>Close Relatives</b> tab lists this person\'s parents and siblings as well as all of the spouses and children that this person has had. These persons will be listed in boxes similar to the charts that you may have already seen.</li></ul>On the right of the screen you will find a box with links.  Many of the links in the box are the same as the links in the menus. For example, clicking on the <b>Pedigree Chart</b> link on the side links will take you to the Pedigree chart for this person. This is different from the menu links, because clicking on the <b>Pedigree Chart</b> link in the menu will take you back to the default Pedigree chart for this database.<br /><br />One of the links that might appear in this list if it has been enabled by the admin, is the <b>View GEDCOM Record</b> link. This link will show you the raw GEDCOM record of this individual.<br /><br />If the Clippings Cart has been enabled by the site admin, you will also have a link that will allow you to add this person to your Clippings Cart.<br /><br />The <b>Relationship to me</b> link will only appear if you are logged in and have been assigned an ID in the GEDCOM. This link will take you to the Pedigree chart and show you how you are related to this person.<br /><br />More help is available by clicking the <b>?</b> next to items on the page.');
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

case 'logs.php':
	$title=i18n::translate('Logs');
	$text='';
	break;

case 'manageservers.php':
	$title=i18n::translate('Manage sites');
	$text=i18n::translate('On this page you can add remote sites and ban IP addresses.<br /><br />Remote sites can be added by providing the site title, URL, database id(optional), username, and password for the remote web service.<br /><br />IP address banning is accomplished by supplying any valid IP address range. For example, 212.10.*.*  Remote sites within the IP address ranges in the Banned list will not be able to access your web service.  You can ban specific IP addresses too.');
	break;

case 'media.php':
	$title=i18n::translate('Manage multimedia');
	$text='';
	break;

case 'medialist.php':
	$title=i18n::translate('Multimedia object list');
	$text=i18n::translate('This page lists all of the multimedia objects available for this GEDCOM (family tree) file.<br /><br />To display the list, first make your selections from the range of filtering options displayed at the top of the page. On the basis of those settings, after clicking "Search" you will see thumbnails, descriptions, and links for each media object.<br /><br />For each item you can then choose to view a full sized, image, its details, or go to one of the people of families it is linked to');
	break;

case 'module_admin.php':
	$title=i18n::translate('Module administration');
	$text='';
	break;

case 'note.php':
	$title=i18n::translate('Shared note');
	$text='';
	break;

case 'notelist.php':
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
	$text=i18n::translate('This page allows you to generate reports, which can be saved on your computer.  You can view these reports at a later time, without being connected to <b>webtrees</b> or the internet.  Reports are available in two formats: PDF (for printing) and HTML (for viewing on screen).').
		'<br/><br/>'.
		i18n::translate('Since reports can contain private data, some may only be available when you are logged in.  The site administrator can configure access levels using the module administration page.');
	break;

case 'search.php':
	$title=i18n::translate('Search');
	$text=i18n::translate('Although this page looks very simple, there is a very powerful and complicated search engine behind the two forms.  Most genealogy web sites just let you search for a name.  <b>webtrees</b> lets you search for almost anything.<br /><br />The Search box on the left of the screen is the same as the Search box in each page header.<br /><br />If you are looking for people in connection to a certain year, just type the year. The program will find all connections for you.<br /><br />Looking for a name, or place?  Just type in the name or place, completely or just a part of it, and <b>webtrees</b> does the rest.<br /><br /><b>Soundex search method</b><br />With the search boxes on the right, you can search for names of persons and places, even if you don\'t know precisely how to write the name.<br /><br />When there are several genealogical databases on one site and the administrator has enabled switching between them, your search will return the results for all of them.<br /><br />You will find more help about these two boxes by clicking the <b>?</b> above the boxes.');
	break;

case 'search_advanced.php':
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
		i18n::translate('Clicking %s will redraw the tree with that person as the new root.', '<img src="'.$WT_IMAGES['tree'].'" width="15px" height="15px" alt="">').
		'<br />'.
		i18n::translate('Clicking %s will take you to that family\'s detail page.', '<img src="'.$WT_IMAGES['button_family'].'" width="15px" height="15px" alt="">').
		'<br /></li><li><b>'.i18n::translate('Toggle Spouses').'</b><br />'.
		i18n::translate('The %s icon directly under the Zoom buttons will toggle the display of all spouses on or off on the descendancy side.  When the display is set to show spouses, all of a person\'s spouses will appear in the box with them.  All of the person\'s children will be shown as well.  When the option to show spouses is off, only the person\'s last spouse and children with that spouse will be shown.', '<img src="'.$WT_IMAGES['sfamily'].'" width="15px" height="15px" alt="">').
		'<br /></li><li><b>'.i18n::translate('Large Tree').'</b>'.
		'<br />'.
		i18n::translate('The Interactive Tree is available from many different pages including the Tree tab on the Individual Information page and the Charts block on the Home Page.  When viewing the tree from one of these other pages, you will also have a Tree icon under the Zoom icons.').
		'<br />'.
		i18n::translate('Clicking %s will take you to the Interactive Tree page.', '<img src="'.$WT_IMAGES['tree'].'" width="15px" height="15px" alt="">').
		'</li></ul>';
	break;

case 'uploadmedia.php':
	$title=i18n::translate('Upload media files');
	$text=''; // TODO: This text is broken - you cannot embed variables like this.  Use %s instead.  But it probably wants completely rewriting and splitting into paragraphs.
	// 'Uploading media files is quite straightforward.  Here is a little additional information.<br /><br /><b>Thumbnails</b><br />Thumbnails should have a size somewhere around 100px width.  The thumbnail <u>must</u> be named identically to the full-size version.  If your system can generate thumbnails automatically, you will see a notice to that effect on the Upload Media page.<br /><br /><b>Uploading</b><br />Files will be uploaded automatically to the directory <b>#GLOBALS[MEDIA_DIRECTORY]#</b> for the full-sized version and to <b>#GLOBALS[MEDIA_DIRECTORY]#thumbs/</b> for the thumbnails.<br /><br />See <a href=\"readme.html\" target=\"_blank\"><b>ReadMe.html</b></a> for more information.';
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

case 'add_facts':
	$title=i18n::translate('Add a fact');
	$text=i18n::translate('Here you can add a fact to the record being edited.<br /><br />First choose a fact from the drop-down list, then click the <b>Add</b> button.  All possible facts that you can add to the database are in that drop-down list.');
	$text.=i18n::translate('Add from clipboard');
	$text.='<br/><br/>';
	$text.=i18n::translate('<b>webtrees</b> allows you to copy up to 10 facts, with all their details, to a clipboard.  This clipboard is different from the Clippings Cart that you can use to export portions of your database.<br /><br />You can select any of the facts from the clipboard and copy the selected fact to the Individual, Family, Media, Source, or Repository record currently being edited.  However, you cannot copy facts of dissimilar record types.  For example, you cannot copy a Marriage fact to a Source or an Individual record since the Marriage fact is associated only with Family records.<br /><br />This is very helpful when entering similar facts, such as census facts, for many individuals or families.');
	break;

case 'add_gedcom':
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

case 'add_new_gedcom':
	$title=i18n::translate('Create a new GEDCOM');
	$text=i18n::translate('You can start a new genealogical database from scratch.<br /><br />This procedure requires only a few simple steps. Step 1 is different from what you know already about uploading and adding. The other steps will be familiar.<ol><li><b>Naming the new GEDCOM</b><br />Type the name of the new GEDCOM <u>without</u> the extension <b>.ged</b>. The new file will be created in the directory named above the box where you enter the name.  Click <b>Add</b>.</li><li><b>Configuration page</b><br />You already know this page;  you configure the settings for your new GEDCOM file.</li><li><b>Validate</b><br />You already know this page;  the new GEDCOM is checked.  Since there is nothing in it, it will be ok.</li><li><b>Importing Records</b><br />Since there will be only one record to import, this will be finished very fast.</li></ol>That\'s it.  Now you can go to the Pedigree chart to see your first person in the new GEDCOM. Click the name of the person and start editing. After that, you can link new individuals to the first person.');
	break;

case 'add_note':
	// This is a general help text for multiple pages
	$title=i18n::translate('Add a new note');
	$text=i18n::translate('If you have a note to add to this record, this is the place to do so.<br /><br />Just click the link, a window will open, and you can type your note.  When you are finished typing, just click the button below the box, close the window, and that\'s all.')
	. '<br /><br />~' . i18n::translate('General info about adding') . '~<br />'
	. i18n::translate('When you have added a fact, note, source, or multimedia file to a record in the database, the addition still has to be approved by a user who has Accept rights.<br /><br />Until the changes have been Accepted, they are identified as "pending" by a differently colored border.  All users with Edit rights can see these changes as well as the original information.  Users who do not have Edit rights will only see the original information. When the addition has been Accepted, the borders will disappear and the new data will display normally, replacing the old.  At that time, users without Edit rights will see the new data too.');
	break;

case 'add_opf_child':
	$title=i18n::translate('Add a new child to create one-parent family');
	$text=i18n::translate('By clicking this link, you can add a <u>new</u> child to this person, creating a one-parent family.<br /><br />Just click the link, and you will get a pop up window to add the new person.  Fill out as many boxes as you can and click the <b>Save</b> button.<br /><br />That\'s all.');
	break;

case 'add_person':
	$title=i18n::translate('Add a new person to the chart');
	$text=i18n::translate('You can have several persons on the timeline.<br /><br />Use this box to supply each person\'s ID.  If you don\'t know the ID of the person, you can click the <b>Find ID</b> link next to the box.<br /><br />~Include Immediate Family CheckBox~<br/>Include Immediate Family is checked by default.  Leave checked to view the father, mother, spouse, siblings, and children of the individual being added to the timeline.  Uncheck if you wish to omit the immediate family.');
	break;

case 'add_shared_note':
	// This is a general help text for multiple pages
	$title=i18n::translate('Add a new shared note');
	$text=i18n::translate('When you click the <b>Add a new Shared Note</b> link, a new window will open.  You can choose to link to an existing shared note, or you can create a new shared note and at the same time create a link to it.')
	. '<br /><br />~' . i18n::translate('General info about adding') . '~<br />'
	. i18n::translate('When you have added a fact, note, source, or multimedia file to a record in the database, the addition still has to be approved by a user who has Accept rights.<br /><br />Until the changes have been Accepted, they are identified as "pending" by a differently colored border.  All users with Edit rights can see these changes as well as the original information.  Users who do not have Edit rights will only see the original information. When the addition has been Accepted, the borders will disappear and the new data will display normally, replacing the old.  At that time, users without Edit rights will see the new data too.');
	break;

case 'add_sibling':
	$title=i18n::translate('Add a new brother or sister');
	$text=i18n::translate('You can add a child to this family when you click this link.  "This Family", in this case, is the father and mother of the principal person of this screen.<br /><br />Keep in mind that you are going to add a sibling of that person.  Adding a brother or sister is simple: Just click the link, fill out the boxes in the pop up screen and that\'s all.<br /><br />If you have to add a son or daughter of the principal person, scroll down a little and click the link in "Family with Spouse".');
	break;

case 'add_son_daughter':
	$title=i18n::translate('Add a new son or daughter');
	$text=i18n::translate('You can add a child to this family when you click this link.  "This Family", in this case, is the principal person of this screen and his or her spouse.<br /><br />Keep in mind that you are going to add a son or daughter of that person.  Adding a son or daughter is simple: Just click the link, fill out the boxes in the popup screen and that\'s all.<br /><br />If you have to add a brother or sister of the principal person, scroll up a little and click the link in "Family with Parents".');
	break;

case 'add_source':
	// This is a general help text for multiple pages
	$title=i18n::translate('Add a new source citation');
	$text=i18n::translate('Here you can add a source <b>Citation</b> to this record.<br /><br />Just click the link, a window will open, and you can choose the source from the list (Find ID) or create a new source and then add the Citation.<br /><br />Adding sources is an important part of genealogy because it allows other researchers to verify where you obtained your information.')
	. '<br /><br />~' . i18n::translate('General info about adding') . '~<br />'
	. i18n::translate('When you have added a fact, note, source, or multimedia file to a record in the database, the addition still has to be approved by a user who has Accept rights.<br /><br />Until the changes have been Accepted, they are identified as "pending" by a differently colored border.  All users with Edit rights can see these changes as well as the original information.  Users who do not have Edit rights will only see the original information. When the addition has been Accepted, the borders will disappear and the new data will display normally, replacing the old.  At that time, users without Edit rights will see the new data too.');
	break;

case 'add_wife':
	$title=i18n::translate('Add a new wife');
	$text=i18n::translate('By clicking this link, you can add a <u>new</u> female person and link this person to the principal individual as a new wife.<br /><br />Just click the link, and you will get a pop up window to add the new person.  Fill out as many boxes as you can and click the <b>Save</b> button.<br /><br />That\'s all.');
	break;

case 'age_differences':
	$title=i18n::translate('Show date differences');
	$text=i18n::translate('When this option box is checked, the «Close Relatives» tab will show date differences as follows:<br /><ul><li>birth dates of partners.<br />A negative value indicates that the second partner is older than the first.<br /><br /></li><li>marriage date and birth date of the first child.<br />A negative value here indicates that the child was born before the marriage date or that either the birth date or the marriage date is wrong.<br /><br /></li><li>birth dates of siblings.<br />A negative value here indicates that either the order of the children is wrong or that one of the birth dates is wrong.</li></ul>');
	break;

case 'alpha':
	$title=i18n::translate('Alphabetical index');
	$text=i18n::translate('Clicking a letter in the Alphabetical index will display a list of the names that start with the letter you clicked.<br /><br />The second to last item in the Alphabetical index can be <b>(unknown)</b>.  This entry will be present when there are people in the database whose surname has not been recorded or does not contain any recognizable letters.  Unknown surnames are often recorded as <b>?</b>, and these will be recognized as <b>(unknown)</b>.  This will also happen if the person is unknown.<br /><br /><b>Note:</b><br />Surnames entered as, for example, <b>Nn</b>, <b>NN</b>, <b>Unknown</b>, or even <b>N.N.</b> will <u>not</u> be found in the <b>(unknown)</b> list. Instead, you will find these persons by clicking <b>N</b> or <b>U</b> because these are the initial letters of those names.  <b>webtrees</b> cannot possibly account for all possible ways of entering unknown surnames;  there is no recognized convention for this.<br /><br />At the end of the Alphabetical index you see <b>ALL</b>. When you click on this item, you will see a list of all surnames in the database.<br /><br /><b>Missing letters?</b><br />If your Alphabetical index appears to be incomplete, with missing letters, your database doesn\'t contain any surnames that start with that missing letter.');
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
	$text=i18n::translate('This option will remove private data from the downloaded GEDCOM file.  The file will be filtered according to the privacy settings that apply to each access level.  Privacy settings are specified on the GEDCOM configuration page.');
	break;

case 'block_move_right':
	$title=i18n::translate('Move list entries');
	$text=i18n::translate('Use these buttons to move an entry from one list to another.<br /><br />Highlight the entry to be moved, and then click a button to move or copy that entry in the direction of the arrow.  Use the <b>&raquo;</b> and <b>&laquo;</b> buttons to move the highlighted entry from the leftmost to the rightmost list or vice-versa.  Use the <b>&gt;</b> and <b>&lt;</b> buttons to move the highlighted entry between the Available Blocks list and the list to its right or left.<br /><br />The entries in the Available Blocks list do not change, regardless of what you do with the Move Right and Move Left buttons.  This is so because the same block can appear several times on the same page.  The HTML block is a good example of why you might want to do this.');
	break;

case 'block_move_up':
	$title=i18n::translate('Move list entries');
	$text=i18n::translate('Use these buttons to re-arrange the order of the entries within the list.  The blocks will be printed in the order in which they are listed.<br /><br />Highlight the entry to be moved, and then click a button to move that entry up or down.');
	break;

case 'box_width':
	$title=i18n::translate('Box width');
	$text=i18n::translate('Here you can change the box width from 50 percent to 300 percent.  At 100 percent each box is about 270 pixels wide.');
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

case 'convertPath':
	$title=i18n::translate('Convert media path to');
	$text=i18n::translate('This option defines a constant path to be prefixed to all media paths in the output file.<br /><br />For example, if the media directory has been configured to be "/media" and if the media file being exported has a path "/media/pictures/xyz.jpg" and you have entered "c:\my pictures\my family" into this field, the resultant media path will be "c:\my pictures\my family/pictures/xyz.jpg".<br /><br />You will notice in this example:<ul><li>the current media directory name is stripped from the path</li><li>and the resultant path will not have correct folder name separators.</li></ul><br />If you wish to retain the media directory in media file paths of the output file, you will need to include that name in the <b>Convert media path to</b> field.<br /><br />You should also use the <b>Convert media folder separators to</b> option to ensure that the folder name separators are consistent and agree with the requirements of the receiving operating system.<br /><br />Media paths that are actually URLs will not be changed.');
	break;

case 'convertSlashes':
	$title=i18n::translate('Convert media folder separators to');
	$text=i18n::translate('This option determines whether folder names in the FILE specification of media objects should be separated by forward slashes or by backslashes.  Your choice depends on the requirements of the receiving operating system.<br /><br />The choice <b>Forward slashes : /</b> is appropriate for most operating systems other than Microsoft Windows.  The choice <b>Backslashes : \</b> should be used when the destination program is running on a Microsoft Windows system.<br /><br />Media paths that are actually URLs will not be changed.');
	break;

case 'day_month':
	$title=i18n::translate('View day / View month / View year');
	$text=i18n::translate('<ul><li>The <b>View Day</b> button will display the events of the chosen date in a list. All years are scanned, so only the day and month can be set here. Changing the year will have no effect.  You can reduce the list by choosing the option <b>Recent years</b> or <b>Living people</b>.<br /><br />Ages in the list will be calculated from the current year.</li><li>The <b>View Month</b> button will display a calendar diagram of the chosen month and year. Here too you can reduce the lists by choosing the option <b>Recent years</b> or <b>Living people</b>.<br /><br />You will get a realistic impression of what a calendar on the wall of your ancestors looked like by choosing a year in the past in combination with <b>Recent years</b>. All ages on the calendar are shown relative to the year in the Year box.</li><li>The <b>View Year</b> button will show you a list of events of the chosen year.  Here too you can reduce the list by choosing the option <b>Recent years</b> or <b>Living people</b>.<br /><br />You can show events for a range of years.  Just type the beginning and ending years of the range, with a dash <b>-</b> between them.  Examples:<br /><b>1992-4</b> for all events from 1992 to 1994<br /><b>1976-1984</b> for all events from 1976 to 1984<br /><br />To see all the events in a given decade or century, you can use <b>?</b> in place of the final digits. For example, <b>197?</b> for all events from 1970 to 1979 or <b>16??</b> for all events from 1600 to 1699.</li></ul>When you want to <b>change the year</b> you <b>have</b> to press one of these three buttons.  All other settings remain as they were.');
	break;

case 'def_gedcom_date':
	$title=i18n::translate('Dates in a GEDCOM file');
	$text=i18n::translate('Although the date field allows for free-form entry (meaning you can type in whatever you want), there are some rules about how dates should be entered according to the GEDCOM 5.5.1 standard.<ol><li>A full date is entered in the form DD MMM YYYY.  For example, <b>01&nbsp;MAR&nbsp;1801</b> or <b>14&nbsp;DEC&nbsp;1950</b>.</li><li>If you are missing a part of the date, you can omit that part.  E.g. <b>MAR&nbsp;1801</b> or <b>14&nbsp;DEC</b>.</li><li>If you are not sure or the date is not confirmed, you could enter <b>ABT&nbsp;MAR&nbsp;1801</b> (abt = about), <b>BEF&nbsp;20&nbsp;DEC&nbsp;1950</b> (bef = before), <b>AFT&nbsp;1949</b> (aft = after)</li><li>Date ranges are entered as <b>FROM&nbsp;MAR&nbsp;1801&nbsp;TO&nbsp;20&nbsp;DEC&nbsp;1810</b> or as <b>BET&nbsp;MAR&nbsp;1801&nbsp;AND&nbsp;20&nbsp;DEC&nbsp;1810</b> (bet = between)<br /><br />The <b>FROM</b> form indicates that the event being described happened continuously between the stated dates and is used with events such as employment. The <b>BET</b> form indicates a single occurrence of the event, sometime between the stated dates and is used with events such as birth.<br /><br />Imprecise dates, where the day of the month or the month is missing, are always interpreted as the first or last possible date, depending on whether that imprecise date occurs before or after the separating keyword.  For example, <b>FEB&nbsp;1804</b> is interpreted as <b>01&nbsp;FEB&nbsp;1804</b> when it occurs before the TO or AND, and as <b>29&nbsp;FEB&nbsp;1804</b> when it occurs after the TO or AND.</li></ol><b>Be sure to enter dates and abbreviations in <u>English</u>,</b> because then the GEDCOM file is exchangeable and <b>webtrees</b> can translate all dates and abbreviations properly into the currently active language.  Furthermore, <b>webtrees</b> does calculations using these dates. If improper dates are entered into date fields, <b>webtrees</b> will not be able to calculate properly.<br /><br />You can click on the Calendar icon for help selecting a date.');
	break;

case 'default_gedcom':
	$title=i18n::translate('Default GEDCOM');
	$text=i18n::translate('If you have more than one genealogical database, you can set here which of them will be the default.<br /><br />This default will be shown to all visitors and users who have not yet logged in.<br /><br />Users who can edit their account settings can override this default.  In that case, the user\'s preferred database will be shown after login.');
	break;

case 'delete_gedcom':
	$title=i18n::translate('Delete GEDCOM');
	$text=i18n::translate('<b>webtrees</b> creates its database from a GEDCOM file that was previously uploaded. When you select <b>Delete</b>, that section of the database will be erased.  You have to confirm your Delete request.<br /><br />Unless you have deliberately removed it outside <b>webtrees</b>, the original GEDCOM file will remain in the directory into which it was uploaded.  If you later want to work with that GEDCOM file again, you don\'t have to upload it again. You can choose the <b>Add GEDCOM</b> function.');
	break;

case 'delete_name':
	$title=i18n::translate('Delete name');
	$text=i18n::translate('<b>Edit name</b><br />When you click this link, another window will open.  There you can edit the name of the person.  Just type the changes into the boxes and click the button, close the window, and that\'s it.<br /><br /><b>DELETE NAME</b><br />By clicking this option you will mark this Name to be deleted from the database.  Note that deleting the name is completely different from deleting the individual.  Deleting the name just removes the name from the person. The person will <u>not</u> be deleted.  If it is an AKA that you want to delete, the person still has his other names.  If it is the <u>only</u> name that you want to remove, the person will still not be deleted, but will now be recorded as <b>(unknown)</b>.  The person will also not be disconnected from any other to relatives, sources, notes, etc.<br /><br />How does it work?<br />You will be asked to confirm your deletion request.  If you decide to continue, it can take a little time before you see a message that the name is deleted.<br /><br />When you continue with your visit, you will notice that the name is still visible and can be used as if the deletion had not occurred.<br /><br /><b>This is <u>not</u> an error.</b>  The site admin will get a message that a change has been made to the database, and that you removed the name.<br />The administrator can accept or reject your change. Only after the administrator has accepted your change will the deletion actually occur <u>irreversibly</u>.  If there is any doubt about your change, the administrator will contact you.');
	break;

case 'desc_generations':
	$title=i18n::translate('Number of generations');
	$text=i18n::translate('Here you can set the number of generations to display on this page.<br /><br />The right number for you depends of the size of your screen and whether you show details or not.  Processing time will increase as you increase the number of generations.');
	break;

case 'desc_rootid':
	$title=i18n::translate('Root individual');
	$text=i18n::translate('If you want to display a chart with a new starting (root) person, the ID of that new starting person is typed here.<br /><br />If you don\'t know the ID of that person, use the <b>Find ID</b> link.<br /><br /><b>ID NUMBER</b><br />The ID numbers used inside <b>webtrees</b> are <u>not</u> the identification numbers issued by various governments (driving permit or passport numbers, for instance).  The ID number referred to here is simply a number used within the database to uniquely identify each individual; it was assigned by the ancestry program that created the GEDCOM file which was imported into <b>webtrees</b>.');
	break;

case 'download_gedcom':
	$title=i18n::translate('Download GEDCOM');
	$text=i18n::translate('From this page you can download your genealogical database in GEDCOM format.  You may want to import the data into another genealogical program, or you may want to share its information with others.<br /><br />~CONVERT FROM UTF-8 TO ANSI~<br /><br />For optimal display on the Internet, <b>webtrees</b> uses the UTF-8 character set.  Some programs, Family Tree Maker for example, do not support importing GEDCOM files encoded in UTF-8.  Checking this box will convert the file from <b>UTF-8</b> to <b>ANSI (ISO-8859-1)</b>.<br /><br />The format you need depends on the program you use to work with your downloaded GEDCOM file.  If you aren\'t sure, consult the documentation of that program.<br /><br />Note that for special characters to remain unchanged, you will need to keep the file in UTF-8 and convert it to your program\'s method for handling these special characters by some other means.  Consult your program\'s manufacturer or author.<br /><br />This <a href=\'http://en.wikipedia.org/wiki/UTF-8\' target=\'_blank\' title=\'Wikipedia article\'><b>Wikipedia article</b></a> contains comprehensive information and links about UTF-8.<br /><br /><br /><br />~REMOVE CUSTOM WEBTREES TAGS~<br /><br />Checking this option will remove any custom tags that may have been added to the records by <b>webtrees</b>.<br /><br />Custom tags used by <b>webtrees</b> include the <b>_WT_USER</b> tag which identifies the user who changed a record online and the <b>_THUM</b> tag which tells <b>webtrees</b> that the image should be used as a thumbnail.<br /><br />Custom tags may cause errors when importing the downloaded GEDCOM to another genealogy application.<br /><br /><br /><br />~DOWNLOAD GEDCOM AS ZIP FILE~<br /><br />When you check this option, a copy of the GEDCOM file will be compressed into ZIP format before the download begins. This will reduce its size considerably, but you will need to use a compatible Unzip program (WinZIP, for example) to decompress the transmitted GEDCOM file before you can use it.<br /><br />This is a useful option for downloading large GEDCOM files.  There is a risk that the download time for the uncompressed file may exceed the maximum allowed execution time, resulting in incompletely downloaded files.  The ZIP option should reduce the download time by 75 percent.');
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

case 'edit_edit_raw':
	$title=i18n::translate('Edit raw GEDCOM record');
	$text=i18n::translate('This page allows you to edit the raw GEDCOM record.  You should use this page with caution; it requires a good understanding of the GEDCOM 5.5.1 Standard.  For more information on the GEDCOM 5.5.1 Standard, refer to Help topic <b>GEDCOM file</b>.<br /><br /><b>webtrees</b> provides many ways to add and edit information, but there could be occasions when you may want to edit the raw GEDCOM structure.  When possible, you should use the provided forms for adding information, but when that is impossible, you can use this form.  Upon submitting the form, your information will be checked for basic conformance to the Standard and the CHAN record will be updated.');
	break;

case 'edit_SOUR_EVEN':
	$title=i18n::translate('Edit source event');
	$text=i18n::translate('Each source records specific events, generally for a given date range and for a place jurisdiction.  For example a Census records census events and church records record birth, marriage, and death events.<br /><br />Select the events that are recorded by this source from the list of events provided. The date should be specified in a range format such as <i>FROM 1900 TO 1910</i>. The place jurisdiction is the name of the lowest jurisdiction that encompasses all lower-level places named in this source. For example, "Oneida, Idaho, USA" would be used as a source jurisdiction place for events occurring in the various towns within Oneida County. "Idaho, USA" would be the source jurisdiction place if the events recorded took place not only in Oneida County but also in other counties in Idaho.');
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
	$text.='<br/><br/><dl><dt>';
	$text.=i18n::translate('Internal messaging');
	$text.='</dt><dd>';
	$text.=i18n::translate('With this option, the <b>webtrees</b> internal messaging system will be used and no emails will be sent.<br /><br />You will receive only <u>internal</u> messages from the other users.  When another site user sends you a message, that message will appear in the Message block on your personal My Page.  If you have removed this block from your My Page, you will not see any messages.  They will, however, show up as soon as you configure My Page to again have the Message block.');
	$text.='</dd><dt>';
	$text.=i18n::translate('Internal messaging with emails');
	$text.='</dt><dd>';
	$text.=i18n::translate('This option is like <b>webtrees</b> internal messaging, with one addition.  As an extra, a copy of the message will also be sent to the email address you configured on your Account page.<br /><br />This is the default contact method.');
	$text.='</dd><dt>';
	$text.=i18n::translate('Mailto link');
	$text.='</dt><dd>';
	$text.=i18n::translate('With this option, you will only receive email messages at the address you configured on your Account page.  The messaging system internal to <b>webtrees</b> will not be used at all, and there will never be any messages in the Message block on your personal My Page.');
	$text.='</dd><dt>';
	$text.=i18n::translate('No contact method');
	$text.='</dt><dd>';
	$text.=i18n::translate('With this option, you will not receive any messages.  Even the administrator will not be able to reach you.');
	$text.='</dd></dl>';
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

case 'edituser_password':
	$title=i18n::translate('Password');
	$text=i18n::translate('Passwords must be at least 6 characters long and are case-sensitive, so that "s3CR#t" is different to "S3CR#t".');
	break;

case 'edituser_rootid':
	$title=i18n::translate('Pedigree chart root ID');
	$text=i18n::translate('This is the starting (Root) person of all your charts.<br /><br />If, for example, you were to click the link to the Pedigree, you would see this root person in the leftmost box.  This root person does not have to be you; you can start with any person (your grandfather or your mother\'s aunt, for instance), as long you have the rights to see that person.<br /><br />This changes the default Root person for most charts.  You can change the Root person on many charts, but that is just for that page at that particular invocation.');
	break;

case 'edituser_user_default_tab':
	$title=i18n::translate('Default tab setting');
	$text=i18n::translate('This setting allows you to specify which tab is opened automatically when you access the Individual Information page.');
	break;

case 'edituser_username':
	$title=i18n::translate('Username');
	$text=i18n::translate('You can change your username by updating it here.  Usernames are case insensitive and ignore accented letters, so that "chloe", "chlo&euml;" and "CHLOE" are considered to be the same.  Usernames may not contain the following characters: &lt;&gt;"%%{};');
	break;

case 'export_gedcom':
	$title=i18n::translate('Export');
	$text=i18n::translate('On this page you can export your data to a GEDCOM file in UTF-8 encoding. The file will be saved automatically to the data directory.');
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

case 'gedcom_administration':
	$title=i18n::translate('GEDCOM administration');
	$text=i18n::translate('The GEDCOM Administration page is the control center for administering all of your genealogical databases.');
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

case 'gedcom_title':
	$title=i18n::translate('GEDCOM title');
	$text=i18n::translate('Enter a descriptive title to be displayed when users are choosing among GEDCOM datasets at your site.');
	break;

case 'gen_missing_thumbs':
	$title=i18n::translate('Create missing thumbnails');
	$text=i18n::translate('This option will generate thumbnails for all files in the current directory which don\'t already have a thumbnail.  This is much more convenient than clicking the <b>Create thumbnail</b> link for each such file.<br /><br />If you wish to retain control over which files should have corresponding thumbnails, you should not use this option.  Instead, click the appropriate <b>Create thumbnail</b> links.');
	break;

case 'generate_thumb':
	$title=i18n::translate('Automatic thumbnail');
	$text=i18n::translate('Your system can generate thumbnails for certain types of images automatically.  There may be support for BMP, GIF, JPG, and PNG files.  The types that your system supports are listed beside the checkbox.<br /><br />By clicking this checkbox, you signal the system that you are uploading images of this type and that you want it to try to generate thumbnails for them.  Leave the box unchecked if you want to provide your own thumbnails.');
	break;

case 'google_chart_surname':
	$title=i18n::translate('Surname');
	$text=i18n::translate('The number of occurrences of the specified name will be shown on the map. If you leave this field empty, the most common surname will be used.');
	break;

case 'header_favorites':
	$title=i18n::translate('Favorites');
	$text=i18n::translate('The Favorites drop-down list shows the favorites that you have selected on your personalized My Page.  It also shows the favorites that the site administrator has selected for the currently active GEDCOM.  Clicking on one of the favorites entries will take you directly to the Individual Information page of that person.<br /><br />More help about adding Favorites is available in your personalized My Page.');
	break;

case 'help_contents_help':
	$title=i18n::translate('Help contents');
	$text=
			'<table><tr><td><span class="helpstart">'.i18n::translate('Help items').'</span>
			<ul><li><a href="?help=add_media">'.i18n::translate('Add media').'</a></li><li><a href="?help=ancestry.php">'.i18n::translate('Ancestry chart').'</a></li><li><a href="?help=calendar.php">'.i18n::translate('Calendar').'</a></li><li><a href="?help=fanchart.php">'.i18n::translate('Circle diagram').'</a></li><li><a href="?help=module.php?mod=clippings&mod_action=index">'
			.i18n::translate('Clippings cart').'</a></li><li><a href="?help=descendancy.php">'.i18n::translate('Descendancy chart').'</a></li><li><a href="?help=famlist.php">'.i18n::translate('Families').'</a></li><li><a href="?help=familybook.php">'
			.i18n::translate('Family book chart').'</a></li><li><a href="?help=family.php">'.i18n::translate('Family information').'</a></li><li><a href="?help=faq.php">'.i18n::translate('FAQ list').'</a></li><li><a href="?help=gedcom_info">'.i18n::translate('GEDCOM information').'</a></li><li><a href="?help=index_portal">'.i18n::translate('Home page').'</a></li><li><a href="?help=hourglass.php">'.i18n::translate('Hourglass chart').'</a></li><li><a href="?help=individual.php">'
			.i18n::translate('Individual information').'</a></li><li><a href="?help=indilist.php">'.i18n::translate('Individuals').'</a></li><li><a href="?help=treenav.php">'.i18n::translate('Interactive tree').'</a></li><li><a href="?help=login.php">'
			.i18n::translate('Login').'</a></li><li><a href="?help=pls_note11">'.i18n::translate('Lost password request').'</a></li><li><a href="?help=medialist.php">'.i18n::translate('Multimedia').'</a></li><li><a href="?help=edituser.php">'
			.i18n::translate('My account').'</a></li><li><a href="?help=mypage_portal">'.i18n::translate('My Page').'</a></li><li><a href="?help=edituser_password">'.i18n::translate('Password').'</a></li><li><a href="?help=pedigree.php">'.i18n::translate('Pedigree Tree').'</a></li><li><a href="?help=placelist.php">'
			.i18n::translate('Place hierarchy').'</a></li><li><a href="?help=relationship.php">'.i18n::translate('Relationship chart').'</a></li><li><a href="?help=reportengine.php">'.i18n::translate('Reports').'</a></li><li><a href="?help=login_register.php">'.i18n::translate('Request new user account').'</a></li><li><a href="?help=search">'.i18n::translate('Search').'</a></li><li><a href="?help=source.php">'.i18n::translate('Source').'</a></li><li><a href="?help=sourcelist.php">'
			.i18n::translate('Sources').'</a></li><li><a href="?help=timeline.php">'.i18n::translate('Timeline chart').'</a></li><li><a href="?help=edituser_username">'.i18n::translate('Username').'</a></li></ul></td>';
		if (WT_USER_IS_ADMIN) {
			$text.='<td valign="top"><span class="helpstart">'.i18n::translate('Administrator help items').'</span><ul><li><a href="?help=admin.php">'.i18n::translate('Administration').'</a></li><li><a href="?help=help_editconfig.php">'.i18n::translate('Configure').'</a></li><li><a href="?help=help_faq.php">'
			.i18n::translate('FAQ List: Edit').'</a></li><li><a href="?help=add_gedcom">'.i18n::translate('GEDCOM: Add').'</li><li><a href="?help=edit_gedcoms">'.i18n::translate('GEDCOM: Administration page').'</a></li><li><a href="?help=gedcom_configfile">'.i18n::translate('GEDCOM: Configuration file').'</a></li><li><a href="?help=edit_config_gedcom">'.i18n::translate('GEDCOM: Configure').'</a></li><li><a href="?help=add_new_gedcom">'.i18n::translate('GEDCOM: Create new').'</a></li><li><a href="?help=default_gedcom">'.i18n::translate('GEDCOM: Default').'</a></li><li><a href="?help=delete_gedcom">'.i18n::translate('GEDCOM: Delete').'</a></li><li><a href="?help=download_gedcom">'
			.i18n::translate('GEDCOM: Download').'<a/></li><li><a href="?help=import_gedcom">'.i18n::translate('GEDCOM: Import').'</a></li><li><a href="?help=upload_gedcom">'.i18n::translate('GEDCOM: Upload').'</a></li><li><a href="readme.html">'.i18n::translate('View readme.html file').'</a></li><li><a href="?help=help_useradmin.php">'.i18n::translate('User administration').'</a></li></ul></td>';
		}
	$text.=('</tr></table>');
	break;

case 'help_editconfig.php':
	$title=i18n::translate('Configure webtrees');
	$text=i18n::translate('On this page you can configure the global settings for <b>webtrees</b>.  You can do this after you have installed <b>webtrees</b> and are running it for the first time.<br /><br />As these settings are <b>global</b>, they are for the whole program and for all genealogical databases you use with <b>webtrees</b>.<br /><br />Each genealogical database also has additional configuration options that you set after clicking the <b>Click here to administer GEDCOMs</b> link on this page.<br /><br />You can also access the GEDCOM Administration function from the main Admin page, whose link is found under the My Page icon or in the header of most pages.  On the Admin page, the relevant link is called <b>Manage GEDCOMs and edit Privacy.</b>');
	break;

case 'import_gedcom':
	$title=i18n::translate('Import GEDCOM');
	$text=i18n::translate('In most cases importing of an externally created GEDCOM file is one step in procedures that result in bulk changes to the genealogical database.<br /><br />These steps are in a logical sequence and need to be completed in the prescribed order so that the genealogical database is usable.<br /><br />If, for some reason, you did not complete these steps in the correct order, you will see a <u>warning</u> message that the GEDCOM is not yet imported. To correct the problem, click the <b>Import GEDCOM</b> link to import the file.<br /><br />Existing GEDCOM configuration settings will not change when you re-import a GEDCOM.  Existing data will, however, be overwritten.');
	break;

case 'include_media':
	$title=i18n::translate('Include media (automatically zips files)');
	$text=i18n::translate('Select this option to include the media files associated with the records in your clippings cart.  Choosing this option will automatically zip the files during download.');
	break;

case 'index_add_favorites':
	$title=i18n::translate('Add a new favorite');
	$text=i18n::translate('This form allows you to add a new favorite item to your list of favorites.<br /><br />You must enter either an ID for the person, family, or source you want to store as a favorite, or you must enter a URL and a title.  The Note field is optional and can be used to describe the favorite.  Anything entered in the Note field will be displayed in the Favorites block after the item.');
	break;

case 'index_common_given_names':
	$title=i18n::translate('Most common given names block');
	$text=i18n::translate('This block displays a list of frequently occurring given names from this database. You can configure how many given names should appear in the list.');
	break;

case 'index_common_names':
	$title=i18n::translate('Most common surnames block');
	$text=i18n::translate('This block displays a list of frequently occurring surnames from this database. A surname must occur at least %s times before it will appear in this list.  The administrator has control over this threshold.<br /><br />When you click on a surname in this list, you will be taken to the Individuals, where you will get more details about that name.', get_gedcom_setting(WT_GED_ID, 'COMMON_NAMES_THRESHOLD'));
	break;

case 'index_favorites':
	$title=i18n::translate('GEDCOM favorites block');
	$text=i18n::translate('The GEDCOM Favorites block is much the same as the "My Favorites" block of My Page. Unlike the My Page configuration, only the administrator or a user with Admin rights can change the list of favorites in this block.<br /><br />The purpose of the GEDCOM Favorites block is to draw the visitor\'s attention to persons of special interest.  This GEDCOM\'s favorites are available for selection from a drop-down list in the header on every page.<br /><br />When you click on one of the listed site favorites, you will be taken to the Individual Information page of that person.');
	break;

case 'index_gedcom_news_adm':
	$title=i18n::translate('GEDCOM news block HTML');
	$text=i18n::translate('The GEDCOM News text allows the use of <b>HTML tags</b> and <b>HTML entities</b>.  HTML should not be used in News titles.<br /><br />Be sure to always use both start and end tags.  It may help to have an understanding of HTML appropriate for a web site administrator. This program uses <b>Cascading Style Sheets (CSS)</b> as well. A different CSS is implemented for each theme.  You can use classes from these style sheets to control the appearance of your messages.');
	break;

case 'index_gedcom_news':
	$title=i18n::translate('GEDCOM news block');
	$text=i18n::translate('The News block is like a bulletin board for this GEDCOM.  The site administrator can place important announcements or interesting news messages here.<br /><br />If you have something interesting to display, please contact the site administrator;  he can put your message on this bulletin board.');
	break;

case 'index_htmlplus_content':
	$title=i18n::translate('HTML content');
	$text=i18n::translate('As well as using the toolbar to apply HTML formatting, you can insert database fields which are updated automatically.  These special fields are marked with <b>#</b> characters.  For example <b>#totalFamilies#</b> will be replaced with the actual number of families in the database.  Advanced users may wish to apply CSS classes to their text, so that the formatting matches the currently selected theme.');
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

case 'index_login':
	$title=i18n::translate('Login block');
	$text=i18n::translate('You can login on almost every page of this program. You will usually do so on the first page, since you can only access privileged information when you are logged in.<br /><br />You can login by typing your <b>username</b> and <b>password</b> and then clicking the Login button.');
	break;

case 'index_media':
	$title=i18n::translate('Random picture block');
	$text=i18n::translate('In this block <b>webtrees</b> randomly chooses a media file to show you on each visit to this page.<br /><br />When you click on the picture, you will see its full-size version.  Below the picture you have a link to the person associated with the picture.  When you click on the picture caption, you will see the picture on the MultiMedia page. When you click on the person\'s name, you will be taken to the Individual Information page of that person.');
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

case 'link_child':
	$title=i18n::translate('Link to an existing family as a child');
	$text=i18n::translate('You can link this person as a child to an existing family when you click this link.<br /><br />Suppose that at one time the parents of the person were unknown, and you discovered later that the parents have a record in this database.<br /><br />Just click the link, enter the ID of the family, and you have competed the task.  If you don\'t know the family\'s ID, you can search for it.');
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
	$text=''; // TODO: The original help text refered to the PGV wiki site (http://wiki.phpgedview.net/en/index.php?title=How_To:Remote_Link_Individuals_Across_Websites_And_Databases).  We should write an equivalent page for WT.
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
	$text=i18n::translate('In this section you specify the parameters that are required to connect to the remote site hosting the data you are linking to. You have the option of choosing from a list of known sites that you have used before, or entering the Site URL and Database ID for a new one.<br /><br />In the <b>Site URL</b> field, you enter the URL to access the web services description file (WDSL) which tells <b>webtrees</b> how to access the data on the remote site.  For a remote <b>webtrees</b> website, the URL to the WSDL file will look like this: <u>http://www.remotesite.com/webtrees/genservice.php?wsdl</u><br /><br />The <b>Database ID</b> field is used to enter an optional database identifier for remote sites that require one.  For <b>webtrees</b> sites, this is the name of the GEDCOM file. <br /><br />The <b>Username</b> and the <b>Password</b> fields are only necessary if the database requires it.');
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

case 'manage_media':
	$title=i18n::translate('Manage multimedia');
	$text=i18n::translate('On this page you can easily manage your Media files and directories.<br /><br />When you create new Media subdirectories, <b>webtrees</b> will ensure that the identical directory structure is maintained within the <b>%sthumbs</b> directory.  When you upload new Media files, <b>webtrees</b> can automatically create the thumbnails for you.<br /><br />Beside each image in the Media list you\'ll find the following options.  The options actually shown depend on the current status of the Media file.<ul><li><b>Edit</b>&nbsp;&nbsp;When you click on this option, you\'ll see a page where you can change the title of the Media object.  If the Media object is not yet linked to a person, family, or source in the currently active database, you can establish this link here.  You can rename the file or even change its location within the <b>%s</b> directory structure.  When necessary, <b>webtrees</b> will automatically create the required subdirectories or any missing thumbnails.</li><li><b>Edit raw GEDCOM record</b>&nbsp;&nbsp;This option is only available when the administrator has enabled it.  You can view or edit the raw GEDCOM data associated with this Media object.  You should be very careful when you use this option.</li><li><b>Delete file</b>&nbsp;&nbsp;This option lets you erase all knowledge of the Media file from the current database.  Other databases will not be affected.  If this Media file is not mentioned in any other database, it, and its associated thumbnail, will be deleted.</li><li><b>Remove object</b>&nbsp;&nbsp;This option lets you erase all knowledge of the Media file from the current database.  Other databases will not be affected.  The Media file, and its associated thumbnail, will not be deleted.</li><li><b>Remove links</b>&nbsp;&nbsp;This option lets you remove all links to the media object from the current database.  The file will not be deleted, and the Media object by which this file is known to the current database will be retained.  Other databases will not be affected.</li><li><b>Set link</b>&nbsp;&nbsp;This option lets you establish links between the media file and persons, families, or sources of the current database.  When necessary, <b>webtrees</b> will also create the Media object by which the Media file is known to the database.</li><li><b>Create thumbnail</b>&nbsp;&nbsp;When you select this option, <b>webtrees</b> will create the missing thumbnail.</li></ul>', $MEDIA_DIRECTORY, $MEDIA_DIRECTORY);
	break;

case 'medialist_recursive':
	$title=i18n::translate('List files in subdirectories');
	$text=i18n::translate('When this option is selected, the MultiMedia Objects will search not only the directory selected from the Filter list but all its subdirectories as well. When this option is not selected, only the selected directory is searched.<br /><br />The titles of all media objects found are then examined to determine whether they contain the text entered in the Filter.  The result of these two actions determines the multimedia objects to be listed.');
	break;

case 'move_mediadirs':
	$title=i18n::translate('Move media directories');
	$text=i18n::translate('When the Media Firewall is enabled, Multi-Media files can be stored in a server directory that is not accessible from the Internet.<br /><br />These buttons allow you to easily move an entire Media directory structure between the protected (not web-addressable) <b>%s%s</b> and the normal <b>%s</b> directories.', $MEDIA_FIREWALL_ROOTDIR, $MEDIA_DIRECTORY, $MEDIA_DIRECTORY);
	break;

case 'mypage_customize':
	$title=i18n::translate('Customize My Page');
	$text=i18n::translate('When you entered here for the first time, you already had some blocks on this page.  If you like, you can customize this My Page.<br /><br />When you click this link you will be taken to a form where you can add, move, or delete blocks.  More explanation is available on that form.');
	break;

case 'mypage_favorites':
	$title=i18n::translate('Favorites block');
	$text=i18n::translate('Favorites are similar to bookmarks.<br /><br />Suppose you have somebody in the family tree whose record you want to check regularly.  Just go to the person\'s Individual Information page and select the <b>Add to My Favorites</b> option from the Favorites drop-down list. This person is now book marked and added to your list of favorites.<br /><br />Wherever you are on this site, you can click on a name in the "My Favorites" drop-down list in the header.  This will take you to the Individual Information page of that person.');
	break;

case 'mypage_message':
	$title=i18n::translate('Messages block');
	$text=i18n::translate('In this block you will find the messages sent to you by other users or the admin.  You too can send messages to other users or to the admin.<br /><br />The <b>webtrees</b> mail system is designed to help protect your privacy.  You don\'t have to leave your email address here and others will not be able to see your email address.<br /><br />To expand a message, click on the message subject or the "<b>+</b>" symbol beside it.  You can delete multiple messages by checking the boxes next to the messages you want to delete and clicking on the <b>Delete Selected Messages</b> button.');
	break;

case 'mypage_myjournal':
	$title=i18n::translate('Journal block');
	$text=i18n::translate('You can use this journal to write notes or reminders for your own use.  When you make such a note, it will still be there the next time you visit the site.<br /><br />These notes are private and will not be visible to others.');
	break;

case 'mypage_portal':
	$title=i18n::translate('My Page');
	$text=i18n::translate('This is your personal page.<br /><br />Here you will find easy links to access your personal data such as <b>My Account</b>, <b>My Indi</b> (this is your Individual Information page), and <b>My Pedigree</b>.  You can have blocks with <b>Messages</b>, a <b>Journal</b> (like a Notepad) and many more.<br /><br />The layout of this page is similar to the Home Page that you see when you first access this site.  While the parts of the Home Page are selected by the site administrator, you can select what parts to include on this personalized page.  You will find the link to customize this page in the Welcome block or separately when the Welcome block is not present.<br /><br />You can choose from the following blocks:<ul><li><a href="?help=mypage_charts"><b>Charts</b></a></li><li><a href="?help=mypage_customize"><b>Customize my page</b></a></li><li><a href="?help=mypage_stats"><b>GEDCOM statistics</b></a></li><li><a href="?help=index_loggedin"><b>Logged in users</b></a></li><li><a href="?help=mypage_message"><b>Messages</b></a></li><li><a href="?help=mypage_favorites"><b>My favorites</b></a></li><li><a href="?help=mypage_myjournal"><b>My journal</b></a></li><li><a href="?help=index_onthisday"><b>On this day in your history</b></a></li><li><a href="?help=index_media"><b>Random media</b></a></li><li><a href="?help=recent_changes"><b>Recent changes</b></a></li><li><a href="?help=index_events"><b>Upcoming events</b></a></li><li><a href="?help=mypage_welcome"><b>Welcome</b></a></li></ul>');
	break;

case 'mypage_stats':
// duplicate text. see index_stats
	$title=i18n::translate('GEDCOM statistics block');
	$text=i18n::translate('In this block you will see some statistics about the current GEDCOM file.  If you need more information than is listed, send a message to the contact at the bottom of the page.');
	break;

case 'mypage_welcome':
	$title=i18n::translate('Welcome block');
	$text=i18n::translate('The Welcome block shows you:<ul><li>The current GEDCOM file</li><li>The date and time</li><li>Links to:<ul><li>My Account</li><li>My Pedigree</li><li>My Individual Record</li><li>Customize My Page</li></ul></li></ul><br /><b>Note:</b><br />You will see the links to <b>My Indi</b> and <b>My Pedigree</b> only if you are known to the current GEDCOM file.  You might have a record in one GEDCOM file and therefore see the <b>My Indi</b> and <b>My Pedigree</b> links, while in another GEDCOM file you do not have a record and consequently these links are not displayed.');
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
	$text=i18n::translate('When this check box is checked, the chart will be printed with oldest people at the top.  When it is unchecked, youngest people will appear at the top.');
	break;

case 'password':
	$title=i18n::translate('Password');
	$text=i18n::translate('In this box you type your password.<br /><br /><b>The password is case sensitive.</b>  This means that <b>MyPassword</b> is <u>not</u> the same as <b>mypassword</b> or <b>MYPASSWORD</b>.');
	break;

case 'PGV_WIZARD':
	$title=i18n::translate('PhpGedView to <b>webtrees</b> transfer wizard');
	$text =i18n::translate('The PGV to <b>webtrees</b> wizard is an automated process to assist administrators make the move from a PGV installation to a new <b>webtrees</b> one. It will transfer all PGV GEDCOM and other database information directly to your new <b>webtrees</b> database. The following requirements are necessary:');
	$text .= '<ul><li>';
	$text .= i18n::translate('webtrees database must be on the same server as PGV\'s');
	$text .= '</li><li>';
	$text .= i18n::translate('PGV must be version 4.2.3, or any SVN up to #6973');
	$text .= '</li><li>';
	$text .= i18n::translate('All changes in PGV must be accepted');
	$text .= '</li><li>';
	$text .= i18n::translate('You must export your latest GEDCOM data');
	$text .= '</li><li>';
	$text .= i18n::translate('The current <b>webtrees</b> admin username must be the same as an existing PGV admin username');
	$text .= '</li><li>';
	$text .= i18n::translate('All existing PGV users must have distinct email addresses');
	$text .= '</li></ul><p>';
	$text .= i18n::translate('<b>Important Note:</b> The transfer wizard is not able to assist with moving media items. You will need to set up and move or copy your media configuration and objects separately after the transfer wizard is finished.');
	$text .= '</p>';
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

case 'register_comments':
	$title=i18n::translate('Comments');
	$text=i18n::translate('Use this field to tell the site administrator why you are requesting an account and how you are related to the genealogy displayed on this site.  You can also use this to enter any other comments you may have for the site administrator.');
	break;

case 'register_gedcomid':
	$title=i18n::translate('GEDCOM INDI record ID');
	$text=i18n::translate('Every person in the database has a unique ID number on this site.  If you know the ID number for your own record, please enter it here.  If you don\'t know your ID number or could not find it because of privacy settings, please provide enough information in the Comments field to help the site administrator identify who you are on this site so that he can set the ID for you.');
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
	$text=i18n::translate('Checking this option will remove any custom tags that may have been added to the records by <b>webtrees</b>.<br /><br />Custom tags used by <b>webtrees</b> include the <b>_WT_USER</b> tag which identifies the user who changed a record online and the <b>_THUM</b> tag which tells <b>webtrees</b> that the image should be used as a thumbnail.<br /><br />Custom tags may cause errors when importing the downloaded GEDCOM to another genealogy application.');
	break;

case 'reorder_children':
	$title=i18n::translate('Reorder children');
	$text=i18n::translate('Children are displayed in the order in which they appear in the family record.  Children are not automatically sorted by birth date because often the birth dates of some of the children are uncertain but the order of their birth <u>is</u> known.<br /><br />This option will allow you to change the order of the children within the family\'s record.  Since you might want to sort the children by their birth dates, there is a button you can press that will do this for you.<br /><br />You can also drag-and-drop any information box to change the order of the children.  As you move the mouse cursor over an information box, its shape will change to a pair of double-headed crossed arrows. If you push and hold the left mouse button before moving the mouse cursor, the information box will follow the mouse cursor up or down in the list.  As the information box is moved, the other boxes will make room.  When you release the left mouse button, the information box will take its new place in the list.');
	break;

case 'reorder_families':
	$title=i18n::translate('Reorder families');
	$text=i18n::translate('Families on the Close Relatives tab are displayed in the order in which they appear in the individual\'s GEDCOM record.  Families are not sorted by the marriage date because often the marriage dates are unknown but the order of the marriages <u>is</u> known.<br /><br />This option will allow you to change the order of the families in which they are listed on the Close Relatives tab.  If you want to sort the families by their marriage dates, there is a button you can press that will automatically do this for you.');
	break;

case 'review_changes':
	$title=i18n::translate('Review GEDCOM changes');
	$text=i18n::translate('This block will list all of the records that have been changed online and that still need to be reviewed and accepted into the database.');
	break;

case 'rootid':
	$title=i18n::translate('Pedigree chart root person');
	$text=i18n::translate('If you want to display a chart with a new starting (root) person, the ID of that new starting person is typed here.<br /><br />If you don\'t know the ID of that person, use the <b>Find ID</b> link.<br /><br /><b>ID NUMBER</b><br />The ID numbers used inside <b>webtrees</b> are <u>not</u> the identification numbers issued by various governments (driving permit or passport numbers, for instance).  The ID number referred to here is simply a number used within the database to uniquely identify each individual; it was assigned by the ancestry program that created the GEDCOM file which was imported into <b>webtrees</b>.');
	break;

case 'search_enter_terms':
	$title=i18n::translate('Enter search terms');
	$text=i18n::translate('In this Search box you can enter criteria such as dates, given names, surnames, places, multimedia, etc.<br /><br /><b>Wildcards</b><br />Wildcards, as you probably know them (like * or ?), are not allowed, but the program will automatically assume wildcards.<br /><br />Suppose you type in the Search box the following: <b>Pete</b>.  The result could be, assuming the names are in the database:<div style="padding-left:30px;"><b>Pete</b> Smith<br /><b>Pete</b>r Johnes<br />Will <b>Pete</b>rson<br />somebody --Born 01 January 1901 <b>Pete</b>rsburg<br />etc.</div><br /><b>Dates</b><br />Typing a year in the Search box will result in a list of individuals who are somehow connected to that year.<br /><br />If you type <b>1950</b>, the result will be all individuals with an event that occurred in 1950.  These events could be births, deaths, marriages, Bar Mitzvahs, LDS Sealings, etc.<br /><br />If you type <b>4 Dec</b>, all persons connected to an event that occurred on 4 December of whatever year will be listed.  Persons connected to an event on 14 or 24 December will be listed as well.  As you see, wildcards are always assumed, so you do not have to type them.  Sometimes, the results can be surprising.<br /><br /><b>Proper dates</b><br /><b>webtrees</b> searches for data, as they are stored in the GEDCOM file.  If, for example, you want to search for an event on December 14, you should type <b>14&nbsp;dec</b> because this is how the date is stored in the database.<br /><br />If you were to type <b>dec&nbsp;14</b>, the result could be a person connected to an event on 08&nbsp;<b>dec</b>ember&nbsp;18<b>14</b>.  Again, the results can be surprising.<br /><br />You can use regular expressions in your search if you are familiar with them.  For example, if you wanted to find all of the people who have dates in the 20th century, you could enter the search <b>19[0-9][0-9]</b> and you would get all of the people with dates from 1900-1999.<br /><br />If you need more help with this searching system, please let us know, so that we can extend this Help file as well.<br /><br />~Search the way you think the name is written (Soundex)~<br /><br />Soundex is a method of coding words according to their pronunciation.  This allows you to search the database for names and places when you don\'t know precisely how they are written.  <b>webtrees</b> supports two different Soundex algorithms that produce vastly different results.<ul><li><b>Basic</b><br />This method, patented in 1918 by Russell, is very simple and can be done by hand.<br /><br />Because the Basic method retains the first letter of the name as part of the resultant code, it is not very helpful when you are unsure of that first letter.  The Basic algorithm is not well suited to names that were originally in languages other than English, and even with English names the results are very surprising.  For example, a Basic Soundex search for <b>Smith</b> will return not only <b>Smith, Smid, Smit, Schmidt, Smyth, Smithe, Smithee, Schmitt</b>, all of which are clearly variations of <b>Smith</b>, but also <b>Smead, Sneed, Smoote, Sammett, Shand,</b> and <b>Snoddy</b>.  <br /><br /></li><li><b>Daitch-Mokotoff</b><br />This method, developed in 1985, is much more complex than the Basic method and is not easily done by hand.<br /><br />A Soundex search using this method produces much more accurate results.</li></ul>For details on both Soundex algorithms, visit this <a href="http://www.jewishgen.org/infofiles/soundex.html" target=_blank><b>Jewish Genealogical Society</b></a> web page.<br /><br /> ~Search and Replace~<br /><br />Here, you can search for a misspelling or other inaccurate information and replace it with correct information.<br /><br /><b>Searching</b><br />This feature performs searching just like a <a href="help_text.php?help=search_enter_terms_help">normal search</a>.<br /><br /><b>Replacing</b><br />All instances of the search term that are found are replaced by the replacement term in the database.<br /><br /><b>For Example...</b><br />Suppose you accidentally misspell your great-grandpa Michael\'s name.  You accidentally entered \'Micheal.\' <br /><br />You would type <b>Micheal</b> in the Search box, and <b>Michael</b> in the Replace box.<br />Every instance of "Micheal" would then be replaced by "Michael"<br /><br /><b>Search for...</b><br />Select the scope of the search.  You can limit the search to names or places, or apply no limit (search everything).  The <i>Whole words only</i> option will only search for your term in the place field as a whole word.  This means that searching for <i>UT</i> would only match <b>UT</b> and not <i>UT</i> in the other words such as Connectic<b>ut</b>.<br /><br />Don\'t worry if you accidentally replace something where you don\'t want to.  Just click the "Accept/Reject Changes" link at the bottom of the page to accept the changes you want, and reject the changes you don\'t want.<br /><br />If you need more help with this searching system, please let us know, so that we can improve this Help file as well.');
	break;

case 'search_exclude_tags':
	$title=i18n::translate('Exclude filter');
	$text=i18n::translate('The <b>Exclude some non-genealogical data</b> choice will cause the Search function to ignore the following GEDCOM tags:<div style="padding-left:30px;"><b>_WT_USER</b> - Last change by<br /><b>CHAN</b> - Last change date<br /><b>FILE</b> - External File<br /><b>FORM</b> - Format<br /><b>TYPE</b> - Type<br /><b>SUBM</b> - Submitter<br /><b>REFN</b> - Reference Number</div><br />In addition to these optionally excluded tags, the Search function always excludes these tags:<div style="padding-left:30px;"><b>_UID</b> - Globally unique Identifier<br /><b>RESN</b> - Restriction</div>');
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

case 'show_fact_sources':
	$title=i18n::translate('Show all sources');
	$text=i18n::translate('When this option is checked, you can see all Source or Note records for this person.  When this option is unchecked, Source or Note records that are associated with other facts for this person will not be shown.');
	break;

case 'show_full':
	$title=i18n::translate('Hide or show details');
	$text=i18n::translate('With this option you can either show or hide all details in the Name boxes.  You can display more boxes on one screen when the details are hidden.<br /><br />When all details are hidden, the Zoom icon described below is not shown.  However, if the administrator has enabled the Zoom function, the entire box will act like a Zoom icon to reveal full details about the person.<br /><br />When the details are not hidden and the Zoom function, identified by a magnifying glass icon, has been enabled by the administrator, you can reveal even more details about that person.  If you normally have to click on the Zoom icon to zoom in, you can reveal additional hidden details by clicking that icon here.  Similarly, if you can zoom in by hovering over the Zoom icon, hidden details will be revealed by hovering over that icon here.<br /><br />If you have clicked on the Zoom icon to reveal more details, you can restore the box to its normal level of detail by clicking on the Zoom icon again.  If you have revealed more details by simply moving the mouse pointer over the Zoom icon, the box will be restored to its normal level of detail when you move the mouse pointer away from the Zoom icon.');
	break;

case 'show_marnm':
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
	$text=i18n::translate('For example, <b>interval 10 years</b> describes the following set of age ranges:<div style="padding-left:30px;">younger than one year<br />one year to 5 years<br />6 to 10<br />11 to 20<br />21 to 30<br />31 to 40<br />41 to 50<br />51 to 60<br />61 to 70<br />71 to 80<br />81 to 90<br />91 to 100<br />older than 100 years</div>');
	break;

case 'stat_gbx':
	$title=i18n::translate('Select the desired age interval');
	$text=i18n::translate('For example, <b>interval 2 years</b> describes the following set of age ranges:<div style="padding-left:30px;">younger than 16 years<br />16 to 18<br />19 to 20<br />21 to 22<br />23 to 24<br />25 to 26<br />27 to 28<br />29 to 30<br />31 to 32<br />33 to 35<br />36 to 40<br />41 to 50<br />older than 50 years</div>');
	break;

case 'stat_gcx':
	$title=i18n::translate('Select the desired count interval');
	$text=i18n::translate('For example, <b>interval one child</b> describes the following set of child count ranges:<div style="padding-left:30px;">without children<br />one child<br />two children<br />3, 4, 5, 6, 7, 8, 9, 10 children<br />more than 10 children</div>');
	break;

case 'stat_gwx':
	$title=i18n::translate('Select the desired age interval');
	$text=i18n::translate('For example, <b>months after marriage</b> describes the following set of month ranges:<div style="padding-left:30px;">before the marriage<br />from the marriage to 8 months after<br />from 8 to 12<br />from 12 to 15<br />from 15 to 18<br />from 18 to 24<br />from 24 to 48<br />over 48 months after the marriage</div><br /><br />When you want to show quarters you have to choose: <b>quarters</b>');
	break;

case 'stat_gwz':
	$title=i18n::translate('Boundaries for Z axis');
	$text=i18n::translate('Select the desired starting year and interval<br /><br />For example, <b>from 1700 interval 50 years</b> describes the following set of date ranges:<div style="padding-left:30px;">before 1700<br />1700 to 1749<br />1750 to 1799<br />1800 to 1849<br />1850 to 1899<br />1900 to 1949<br />1950 to 1999<br />2000 or later</div>');
	break;

case 'stat_x':
	$title=i18n::translate('X axis');
	$text=i18n::translate('The following options are available for the X axis (horizontal). Each will then be presented according to options set for the Y and Z axes.<p style="padding-left: 25px"><b>Month of birth</b>&nbsp;&nbsp;individuals born in each month.<br /><b>Month of death</b>&nbsp;&nbsp;individuals who died in each month.<br /><b>Month of marriage</b>&nbsp;&nbsp;marriages that occurred in each month.<br /><b>Month of birth of first child in a relation</b>&nbsp;&nbsp;the number of first-borns for each family by month.<br /><b>Month of first marriage</b>&nbsp;&nbsp;the number of first marriages per month.<br /><b>Months between marriage and first child</b>&nbsp;&nbsp;the number of months between marriage and birth of first child to that couple.<br /><b>Age related to birth year</b>&nbsp;&nbsp;age at death, related to the time period that includes each person\'s birth year.<br /><b>Age related to death year</b>&nbsp;&nbsp;age at death, related to the time period that includes each person\'s year of death.<br /><b>Age in year of marriage</b>&nbsp;&nbsp;the average age of individuals at the time of their marriages.<br /><b>Age in year of first marriage</b>&nbsp;&nbsp;the average age of individuals at the time of their first marriage.<br /><b>Number of children</b>&nbsp;&nbsp;average family sizes.<br /><b>Individual distribution</b>&nbsp;&nbsp;placement of all persons or persons with the specified name, by country.<br /><b>Birth by country</b>&nbsp;&nbsp;country of birth.<br /><b>Marriage by country</b>&nbsp;&nbsp;country of marriage.<br /><b>Death by country</b>&nbsp;&nbsp;country of death.<br /><b>Individuals with sources</b>&nbsp;&nbsp;pie chart of individuals with sources.<br /><b>Families with sources</b>&nbsp;&nbsp;pie chart of families with sources.</p>');
	break;

case 'stat_y':
	$title=i18n::translate('Y axis');
	$text=i18n::translate('The following options are available for the Y axis (vertical). These options alter the way the items presented on the X axis are displayed.<p style="padding-left: 25px"><b>numbers</b>&nbsp;&nbsp;displays the number of individuals in each category defined by the X axis.<br /><b>percentage</b>&nbsp;&nbsp;calculates and diplays the proportion of each item in the X axis categories.</p>');
	break;

case 'stat_z':
	$title=i18n::translate('Z axis');
	$text=i18n::translate('The following options are available for the Z axis. These options provide a sub-division of the categories selected for the X axis.<p style="padding-left: 25px"><b>none</b>&nbsp;&nbsp;displays the items as a single column for each X axis category.<br /><b>gender</b>&nbsp;&nbsp;displays the items in 2 columns (male and female) for each X axis category.<br /><b>date periods</b>&nbsp;&nbsp;displays the items in a number of columns related to the time periods set in the next section, for each X axis category.</p>');
	break;

case 'talloffset':
	$title=i18n::translate('Page layout');
	$text=i18n::translate('With this option you determine the page layout orientation.<br /><br />Changing this setting might be useful if you want to make a screen print or if you have a different type of screen.<ul><li><b>Portrait</b> mode will make the tree taller, such that a 4 generation chart should fit on a single page printed vertically.</li><li><b>Landscape</b> mode will make a wider tree that should print on a single page printed horizontally.</li><li><b>Oldest at top</b> mode rotates the chart, but not its boxes, by 90 degrees counter-clockwise, so that the oldest generation is at the top of the chart.</li><li><b>Oldest at bottom</b> mode rotates the chart, but not its boxes, by 90 degrees clockwise, so that the oldest generation is at the bottom of the chart.</li></ul>');
	break;

case 'timeline_control':
	$title=i18n::translate('Timeline control');
	$text=i18n::translate('Click the drop down menu to change the speed at which the timeline scrolls.<br/><br/>~Begin Year~<br/>Enter the starting year of the range.<br/><br/>~End Year~<br/>Enter the ending year of the range.<br/><br/>~Search~<br/>Click the Search button to begin searching for events that occurred within the range identified by the Begin Year and End Year fields.');
	break;

case 'upload_gedcom':
	$title=i18n::translate('Upload GEDCOM');
	$text=i18n::translate('Unlike the <b>Add GEDCOM</b> function, the GEDCOM file you wish to add to your database does not have to be on your server.<br /><br />In Step 1 you select a GEDCOM file from your local computer. Type the complete path and file name in the text box or use the <b>Browse</b> button on the page.<br /><br />You can also use this function to upload a ZIP file containing the GEDCOM file. <b>webtrees</b> will recognize the ZIP file and extract the file and the filename automatically.<br /><br />If a GEDCOM file with the same name already exists in <b>webtrees</b>, it will, after your confirmation, be overwritten. However, all GEDCOM settings made previously will be preserved.<br /><br />You will find more help on other pages of the procedure.');
	break;

case 'upload_media_file':
	$title=i18n::translate('Media file to upload');
	$text=
		i18n::translate('Select the media file that you want to upload.  If a file already exists with the same name, it will be overwritten.').
		'<br/><br/>'.
		i18n::translate('It is easier to manage your media files if you choose a consistent format for the filenames.  To organise media files into folders, you must first set the number of levels in the GEDCOM administration page.');
	break;

case 'upload_media':
	$title=i18n::translate('Upload media files');
	$text=i18n::translate('Upload one or more media files from your local computer.  Media files can be pictures, video, audio, or other formats.');
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
	$text=i18n::translate('Choose the thumbnail image that you want to upload.  Although thumbnails can be generated automatically for images, you may wish to generate your own thumbnail, especially for other media types.  For example, you can provide a still image from a video, or a photograph of the person who made an audio recording.');
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

case 'useradmin_editaccount':
	$title=i18n::translate('Edit account information');
	$text=i18n::translate('If this box is checked, this user will be able to edit his account information.  Although this is not generally recommended, you can create a single user name and password for multiple users.  When this box is unchecked for all users with the shared account, they are prevented from editing the account information and only an administrator can alter that account.');
	break;

case 'useradmin_gedcomid':
	$title=i18n::translate('GEDCOM INDI record ID');
	$text=i18n::translate('The GEDCOM INDI record ID identifies the user.  It has to be set by the administrator.<br /><br />This ID is used as the ID on several pages such as <b>My Individual Record</b> and <b>My Pedigree</b>.<br /><br />You can set the user\'s GEDCOM ID separately for each GEDCOM.  If a user does not have a record in a GEDCOM, you leave that box empty.');
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

case 'view_server_folder':
	$title=i18n::translate('View server folder');
	$text=i18n::translate('The administrator has enabled up to %s folder levels below the default <b>%s</b>.  This helps to organize the media files and reduces the possibility of name collisions.<br /><br />In this field, you select the media folder whose contents you wish to view.  When you select <b>ALL</b>, all media files will be shown without regard to the folder in which they are stored.  This can produce a very long list of media items.',
		get_gedcom_setting(WT_GED_ID, 'MEDIA_DIRECTORY_LEVELS'),
		get_gedcom_setting(WT_GED_ID, 'MEDIA_DIRECTORY')
	);
	break;

case 'zip':
	$title=i18n::translate('Zip clippings');
	$text=i18n::translate('Select this option as to save your clippings in a ZIP file.  For more information about ZIP files, please visit <a href="http://www.winzip.com" target="_blank">http://www.winzip.com</a>.');
	break;

default:
	$title=i18n::translate('Help');
	$text=i18n::translate('The help text has not been written for this item.');
	// If we've been called from a module, allow the module to provide the help text
	$mod=safe_GET('mod', '[A-Za-z0-9_]+');
	if (file_exists(WT_ROOT.'modules/'.$mod.'/help_text.php')) {
		require WT_ROOT.'modules/'.$mod.'/help_text.php';
	}
	break;
}

print_simple_header(i18n::translate('Help for «%s»', htmlspecialchars(strip_tags($title))));
echo '<div class="helpheader">', nl2br($title), '</div>';
echo '<div class="helpcontent">', nl2br($text),'</div>';
echo '<div class="helpfooter"><br />';
echo '<a href="javascript:;" onclick="window.history.go(-1)">','<img src="', $WT_IMAGES["larrow"], '" alt="<"><br />';
echo '<a href="help_text.php?help=help_contents_help"><b>', i18n::translate('Help Contents'), '</b></a><br />';
echo '<a href="javascript:;" onclick="window.close();"><b>', i18n::translate('Close Window'), '</b></a>';
echo '</div>';
print_simple_footer();
