<?php
/**
 * Application configuration data.  Data here has no GUI to edit it,
 * although most of it can be altered to customise local installations.
 *
 * NOTE: The one-item-per-line and extra-comma-after-last-item approach
 * is used to allow SVN to reliably merge changes for users that have
 * customised their local copy of this file.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2010 PGV Development Team.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CONFIG_DATA_PHP', '');

// Unknown surname in various scripts
// TODO: This is extremely poor I18N - there is not a 1:1 correlation between script and language
$UNKNOWN_NN=array(
	'hebrew'    =>'(לא-ידוע)',
	'arabic'    =>'(غير معروف)',
	'greek'     =>'(άγνωστος/η)',
	'cyrillic'  =>'(неопределено)', // Russian
	'han'       =>'(未知)',
	'latin'     =>WT_I18N::translate_c('surname', '(unknown)'),
	'common'    =>WT_I18N::translate_c('surname', '(unknown)'),
);

// Unknown givne name in various scripts
// TODO: This is extremely poor I18N - there is not a 1:1 correlation between script and language
$UNKNOWN_PN=array(
	'hebrew'    =>'(לא-ידוע)',
	'arabic'    =>'(غير معروف)',
	'greek'     =>'(άγνωστος/η)', // Russian
	'cyrillic'   =>'(неопределено)',
	'han'       =>'(未知)',
	'latin'     =>WT_I18N::translate_c('given name', '(unknown)'),
	'common'    =>WT_I18N::translate_c('given name', '(unknown)'),
);

// GEDCOM ADOP codes
$ADOP_CODES=array(
	'BOTH'=>WT_I18N::translate('Adopted by both parents'),
	'HUSB'=>WT_I18N::translate('Adopted by father'),
	'WIFE'=>WT_I18N::translate('Adopted by mother'),
);

// GEDCOM ADOP Female codes
$ADOP_CODES_F=array(
	'BOTH'=>WT_I18N::translate_c('FEMALE', 'Adopted by both parents'),
	'HUSB'=>WT_I18N::translate_c('FEMALE', 'Adopted by father'),
	'WIFE'=>WT_I18N::translate_c('FEMALE', 'Adopted by mother'),
);

// GEDCOM ADOP Male codes
$ADOP_CODES_M=array(
	'BOTH'=>WT_I18N::translate_c('MALE', 'Adopted by both parents'),
	'HUSB'=>WT_I18N::translate_c('MALE', 'Adopted by father'),
	'WIFE'=>WT_I18N::translate_c('MALE', 'Adopted by mother'),
);

// GEDCOM PEDI codes
$PEDI_CODES=array(
	'birth'  =>WT_I18N::translate_c('Pedigree', 'Birth'),
	'adopted'=>WT_I18N::translate_c('Pedigree', 'Adopted'),
	'foster' =>WT_I18N::translate_c('Pedigree', 'Foster'),
	'sealing'=>WT_I18N::translate_c('Pedigree', 'Sealing'),
);

// GEDCOM PEDI Female codes
$PEDI_CODES_F=array(
	'birth'  =>WT_I18N::translate_c('Female pedigree', 'Birth'),
	'adopted'=>WT_I18N::translate_c('Female pedigree', 'Adopted'),
	'foster' =>WT_I18N::translate_c('Female pedigree', 'Foster'),
	'sealing'=>WT_I18N::translate_c('Female pedigree', 'Sealing'),
);

// GEDCOM PEDI Male codes
$PEDI_CODES_M=array(
	'birth'  =>WT_I18N::translate_c('Male pedigree', 'Birth'),
	'adopted'=>WT_I18N::translate_c('Male pedigree', 'Adopted'),
	'foster' =>WT_I18N::translate_c('Male pedigree', 'Foster'),
	'sealing'=>WT_I18N::translate_c('Male pedigree', 'Sealing'),
);

// GEDCOM RELA codes for non-genealogical relationships.
// These aren't part of the standard, but we list the common ones here so we can translate them.
$RELA_CODES=array(
	'attendant'       =>WT_I18N::translate('Attendant'),
	'attending'       =>WT_I18N::translate('Attending'),
	'best_man'        =>WT_I18N::translate('Best Man'),
	'bridesmaid'      =>WT_I18N::translate('Bridesmaid'),
	'buyer'           =>WT_I18N::translate('Buyer'),
	'circumciser'     =>WT_I18N::translate('Circumciser'),
	'civil_registrar' =>WT_I18N::translate('Civil Registrar'),
	'employee'        =>WT_I18N::translate('Employee'),
	'employer'        =>WT_I18N::translate('Employer'),
	'foster_child'    =>WT_I18N::translate('Foster Child'),
	'foster_father'   =>WT_I18N::translate('Foster Father'),
	'foster_mother'   =>WT_I18N::translate('Foster Mother'),
	'friend'          =>WT_I18N::translate('Friend'),
	'godfather'       =>WT_I18N::translate('Godfather'),
	'godmother'       =>WT_I18N::translate('Godmother'),
	'godparent'       =>WT_I18N::translate('Godparent'),
	'godson'          =>WT_I18N::translate('Godson'),
	'goddaughter'     =>WT_I18N::translate('Goddaughter'),
	'godchild'        =>WT_I18N::translate('Godchild'),
	'guardian'        =>WT_I18N::translate('Guardian'),
	'informant'       =>WT_I18N::translate('Informant'),
	'lodger'          =>WT_I18N::translate('Lodger'),
	'nanny'           =>WT_I18N::translate('Nanny'),
	'nurse'           =>WT_I18N::translate('Nurse'),
	'owner'           =>WT_I18N::translate('Owner'),
	'priest'          =>WT_I18N::translate('Priest'),
	'rabbi'           =>WT_I18N::translate('Rabbi'),
	'registry_officer'=>WT_I18N::translate('Registry Officer'),
	'seller'          =>WT_I18N::translate('Seller'),
	'servant'         =>WT_I18N::translate('Servant'),
	'slave'           =>WT_I18N::translate('Slave'),
	'ward'            =>WT_I18N::translate('Ward'),
	'witness'         =>WT_I18N::translate('Witness'),
);

// GEDCOM RELA codes for non-genealogical relationships.
// These aren't part of the standard, but we list the common ones here so we can translate them.
$RELA_CODES_F=array(
	'attendant'       =>WT_I18N::translate_c('FEMALE', 'Attendant'),
	'attending'       =>WT_I18N::translate_c('FEMALE', 'Attending'),
	'buyer'           =>WT_I18N::translate_c('FEMALE', 'Buyer'),
	'civil_registrar' =>WT_I18N::translate_c('FEMALE', 'Civil Registrar'),
	'employee'        =>WT_I18N::translate_c('FEMALE', 'Employee'),
	'employer'        =>WT_I18N::translate_c('FEMALE', 'Employer'),
	'friend'          =>WT_I18N::translate_c('FEMALE', 'Friend'),
	'guardian'        =>WT_I18N::translate_c('FEMALE', 'Guardian'),
	'informant'       =>WT_I18N::translate_c('FEMALE', 'Informant'),
	'lodger'          =>WT_I18N::translate_c('FEMALE', 'Lodger'),
	'nurse'           =>WT_I18N::translate_c('FEMALE', 'Nurse'),
	'owner'           =>WT_I18N::translate_c('FEMALE', 'Owner'),
	'registry_officer'=>WT_I18N::translate_c('FEMALE', 'Registry Officer'),
	'seller'          =>WT_I18N::translate_c('FEMALE', 'Seller'),
	'servant'         =>WT_I18N::translate_c('FEMALE', 'Servant'),
	'slave'           =>WT_I18N::translate_c('FEMALE', 'Slave'),
	'ward'            =>WT_I18N::translate_c('FEMALE', 'Ward'),
);

// GEDCOM RELA codes for non-genealogical relationships.
// These aren't part of the standard, but we list the common ones here so we can translate them.
$RELA_CODES_M=array(
	'attendant'       =>WT_I18N::translate_c('MALE', 'Attendant'),
	'attending'       =>WT_I18N::translate_c('MALE', 'Attending'),
	'buyer'           =>WT_I18N::translate_c('MALE', 'Buyer'),
	'civil_registrar' =>WT_I18N::translate_c('MALE', 'Civil Registrar'),
	'employee'        =>WT_I18N::translate_c('MALE', 'Employee'),
	'employer'        =>WT_I18N::translate_c('MALE', 'Employer'),
	'friend'          =>WT_I18N::translate_c('MALE', 'Friend'),
	'guardian'        =>WT_I18N::translate_c('MALE', 'Guardian'),
	'informant'       =>WT_I18N::translate_c('MALE', 'Informant'),
	'lodger'          =>WT_I18N::translate_c('MALE', 'Lodger'),
	'nurse'           =>WT_I18N::translate_c('MALE', 'Nurse'),
	'owner'           =>WT_I18N::translate_c('MALE', 'Owner'),
	'registry_officer'=>WT_I18N::translate_c('MALE', 'Registry Officer'),
	'seller'          =>WT_I18N::translate_c('MALE', 'Seller'),
	'servant'         =>WT_I18N::translate_c('MALE', 'Servant'),
	'slave'           =>WT_I18N::translate_c('MALE', 'Slave'),
	'ward'            =>WT_I18N::translate_c('MALE', 'Ward'),
);

// NPFX tags - name prefixes
$NPFX_accept=array(
	'Adm',
	'Amb',
	'Brig',
	'Can',
	'Capt',
	'Chan',
	'Chapln',
	'Cmdr',
	'Col',
	'Cpl',
	'Cpt',
	'Dr',
	'Gen',
	'Gov',
	'Hon',
	'Lady',
	'Lt',
	'Mr',
	'Mrs',
	'Ms',
	'Msgr',
	'Pfc',
	'Pres',
	'Prof',
	'Pvt',
	'Rabbi',
	'Rep',
	'Rev',
	'Sen',
	'Sgt',
	'Sir',
	'Sr',
	'Sra',
	'Srta',
	'Ven',
);

// SPFX tags - surname prefixes
$SPFX_accept=array(
	'al',
	'da',
	'de',
	'dem',
	'den',
	'der',
	'di',
	'du',
	'el',
	'la',
	'van',
	'von',
);

// NSFX tags - name suffixes
$NSFX_accept=array(
	'I',
	'II',
	'III',
	'IV',
	'Jr',
	'Junior',
	'MD',
	'PhD',
	'Senior',
	'Sr',
	'V',
	'VI',
);

// FILE:FORM tags - file formats
$FILE_FORM_accept=array(
	'avi',
	'bmp',
	'gif',
	'jpeg',
	'mp3',
	'ole',
	'pcx',
	'png',
	'tiff',
	'wav',
);

// Fact tags (as opposed to event tags), that don't normally have a value
$emptyfacts=array(
	'ADOP',
	'ANUL',
	'BAPL',
	'BAPM',
	'BARM',
	'BASM',
	'BIRT',
	'BLES',
	'BURI',
	'CENS',
	'CHAN',
	'CHR',
	'CHRA',
	'CONF',
	'CONL',
	'CREM',
	'DATA',
	'DEAT',
	'DIV',
	'DIVF',
	'EMIG',
	'ENDL',
	'ENGA',
	'FCOM',
	'GRAD',
	'HUSB',
	'IMMI',
	'MAP',
	'MARB',
	'MARC',
	'MARL',
	'MARR',
	'MARS',
	'NATU',
	'ORDN',
	'PROB',
	'RESI',
	'RETI',
	'SLGC',
	'SLGS',
	'WIFE',
	'WILL',
	'_HOL',
	'_NMR',
	'_SEPR',
);

// Tags that don't require a PLAC subtag
$nonplacfacts=array(
	'ENDL',
	'NCHI',
	'SLGC',
	'SLGS',
);

// Tags that don't require a DATE subtag
$nondatefacts=array(
	'ABBR',
	'ADDR',
	'AFN',
	'AUTH',
	'CHIL',
	'EMAIL',
	'FAX',
	'HUSB',
	'NAME',
	'NCHI',
	'NOTE',
	'OBJE',
	'PHON',
	'PUBL',
	'REFN',
	'REPO',
	'RESN',
	'SEX',
	'SOUR',
	'SSN',
	'TEXT',
	'TITL',
	'WIFE',
	'WWW',
	'_EMAIL',
);

// Tags that require a TYPE subtag
$typefacts=array(
);

// Tags that require a DATE:TIME as well as a DATE
$date_and_time=array(
	'BIRT',
	'DEAT',
);

// Level 2 tags that apply to specific Level 1 tags
// Tags are applied in the order they appear here.
$level2_tags=array(
	'_HEB'=>array(
		'NAME',
		'TITL',
	),
	'ROMN'=>array(
		'NAME',
		'TITL',
	),
	'TYPE'=>array(
		'EVEN',
		'FACT',
		'GRAD',
		'IDNO',
		'MARR',
		'ORDN',
		'SSN',
	),
	'AGNC'=>array(
		'EDUC',
		'GRAD',
		'OCCU',
		'ORDN',
		'RETI',
	),
	'CAUS'=>array(
		'DEAT',
	),
	'CALN'=>array(
		'REPO',
	),
	'CEME'=>array( // CEME is NOT a valid 5.5.1 tag
		'BURI',
	),
	'RELA'=>array(
		'ASSO',
	),
	'DATE'=>array(
		'ADOP',
		'ANUL',
		'BAPL',
		'BAPM',
		'BARM',
		'BASM',
		'BIRT',
		'BLES',
		'BURI',
		'CENS',
		'CENS',
		'CHR',
		'CHRA',
		'CONF',
		'CONL',
		'CREM',
		'DEAT',
		'DIV',
		'DIVF',
		'EDUC',
		'EMIG',
		'ENDL',
		'ENGA',
		'EVEN',
		'EVEN',
		'FCOM',
		'GRAD',
		'IMMI',
		'MARB',
		'MARC',
		'MARL',
		'MARR',
		'MARS',
		'NATU',
		'OCCU',
		'ORDN',
		'PROB',
		'PROP',
		'RELI',
		'RESI',
		'RESI',
		'RETI',
		'SLGC',
		'SLGS',
		'WILL',
		'_TODO',
	),
	'TEMP'=>array(
		'BAPL',
		'CONL',
		'ENDL',
		'SLGC',
		'SLGS',
	),
	'PLAC'=>array(
		'ADOP',
		'ANUL',
		'BAPL',
		'BAPM',
		'BARM',
		'BASM',
		'BIRT',
		'BLES',
		'BURI',
		'CENS',
		'CENS',
		'CHR',
		'CHRA',
		'CONF',
		'CONL',
		'CREM',
		'DEAT',
		'DIV',
		'DIVF',
		'EDUC',
		'EMIG',
		'ENDL',
		'ENGA',
		'EVEN',
		'EVEN',
		'FCOM',
		'GRAD',
		'IMMI',
		'MARB',
		'MARC',
		'MARL',
		'MARR',
		'MARS',
		'NATU',
		'OCCU',
		'ORDN',
		'PROB',
		'PROP',
		'RELI',
		'RESI',
		'RESI',
		'RETI',
		'SLGC',
		'SLGS',
		'SSN',
		'WILL',
	),
	'STAT'=>array(
		'BAPL',
		'CONL',
		'ENDL',
		'SLGC',
		'SLGS',
	),
	'ADDR'=>array(
		'BIRT',
		'BURI',
		'CENS',
		'CHR',
		'CHRA',
		'CREM',
		'DEAT',
		'EDUC',
		'EVEN',
		'GRAD',
		'MARR',
		'OCCU',
		'ORDN',
		'PROP',
		'RESI',
	),
	'PHON'=>array(
		'OCCU',
		'RESI',
	),
	'FAX'=>array(
		'OCCU',
		'RESI',
	),
	'URL'=>array(
		'OCCU',
		'RESI',
	),
	'EMAIL'=>array(
		'OCCU',
		'RESI',
	),
	'AGE'=>array(
		'CENS',
		'DEAT',
	),
	'HUSB'=>array(
		'MARR',
	),
	'WIFE'=>array(
		'MARR',
	),
	'FAMC'=>array(
		'ADOP',
		'SLGC',
	),
	'FILE'=>array(
		'OBJE',
	),
	'_PRIM'=>array(
		'OBJE',
	),
	'EVEN'=>array(
		'DATA',
	),
	'_WT_USER'=>array(
		'_TODO',
	),
);

// The order of name parts, when generating names
$STANDARD_NAME_FACTS=array('NAME', 'NPFX', 'GIVN', 'SPFX', 'SURN', 'NSFX');
$REVERSED_NAME_FACTS=array('NAME', 'NPFX', 'SPFX', 'SURN', 'GIVN', 'NSFX');

// Create a label for a fact type.
// TODO: update rest of code to call WT_Gedcom_Tag::getLabel() directly
function translate_fact($fact, $person=null) {
	return WT_Gedcom_Tag::getLabel($fact, $person);
}

// Create a label for a relationship type.
function translate_rela($rela, $sex='') {
	global $RELA_CODES, $RELA_CODES_M, $RELA_CODES_F;

	if ($sex=='M' && array_key_exists($rela, $RELA_CODES_M)) {
		return $RELA_CODES_M[$rela];
	}
	if ($sex=='F' && array_key_exists($rela, $RELA_CODES_F)) {
		return $RELA_CODES_F[$rela];
	}
	if (array_key_exists($rela, $RELA_CODES)) {
		return $RELA_CODES[$rela];
	}
	// Still no translation? Return original relationship name
	return $rela;
}
