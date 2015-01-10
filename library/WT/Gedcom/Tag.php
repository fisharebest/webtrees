<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 Greg Roach
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

use Rhumsaa\Uuid\Uuid;

/**
 * Class WT_Gedcom_Tag - Static GEDCOM data for Tags
 */
class WT_Gedcom_Tag {
	/** @var string[] All tags that webtrees knows how to translate - including special/internal tags */
	private static $ALL_TAGS = array(
		'ABBR', 'ADDR', 'ADR1', 'ADR2', 'ADOP', 'ADOP:DATE', 'ADOP:PLAC',
		'AFN', 'AGE', 'AGNC', 'ALIA', 'ANCE', 'ANCI', 'ANUL', 'ASSO', 'AUTH', 'BAPL',
		'BAPL:DATE', 'BAPL:PLAC', 'BAPM', 'BAPM:DATE', 'BAPM:PLAC', 'BARM',
		'BARM:DATE', 'BARM:PLAC', 'BASM', 'BASM:DATE', 'BASM:PLAC',
		'BIRT', 'BIRT:DATE', 'BIRT:PLAC', 'BLES', 'BLES:DATE',
		'BLES:PLAC', 'BLOB', 'BURI', 'BURI:DATE', 'BURI:PLAC',
		'CALN', 'CAST', 'CAUS', 'CEME', 'CENS', 'CENS:DATE', 'CENS:PLAC', 'CHAN', 'CHAN:DATE', 'CHAN:_WT_USER', 'CHAR',
		'CHIL', 'CHR', 'CHR:DATE', 'CHR:PLAC', 'CHRA', 'CITN', 'CITY',
		'COMM', 'CONC', 'CONT', 'CONF', 'CONF:DATE', 'CONF:PLAC', 'CONL',
		'COPR', 'CORP', 'CREM', 'CREM:DATE', 'CREM:PLAC', 'CTRY', 'DATA',
		'DATA:DATE', 'DATE', 'DEAT', 'DEAT:CAUS', 'DEAT:DATE', 'DEAT:PLAC',
		'DESC', 'DESI', 'DEST', 'DIV', 'DIVF', 'DSCR', 'EDUC', 'EDUC:AGNC', 'EMAI',
		'EMAIL', 'EMAL', 'EMIG', 'EMIG:DATE', 'EMIG:PLAC', 'ENDL', 'ENDL:DATE',
		'ENDL:PLAC', 'ENGA', 'ENGA:DATE', 'ENGA:PLAC', 'EVEN', 'EVEN:DATE',
		'EVEN:PLAC', 'FACT', 'FAM', 'FAMC', 'FAMF', 'FAMS', 'FAMS:CENS:DATE', 'FAMS:CENS:PLAC',
		'FAMS:DIV:DATE', 'FAMS:MARR:DATE', 'FAMS:MARR:PLAC', 'FAMS:NOTE',
		'FAX', 'FCOM', 'FCOM:DATE',
		'FCOM:PLAC', 'FILE', 'FONE', 'FORM', 'GEDC', 'GIVN', 'GRAD',
		'HEAD', 'HUSB', 'IDNO', 'IMMI', 'IMMI:DATE', 'IMMI:PLAC', 'INDI', 'INFL',
		'LANG', 'LATI', 'LEGA', 'LONG', 'MAP', 'MARB', 'MARB:DATE', 'MARB:PLAC',
		'MARC', 'MARL', 'MARR', 'MARR:DATE', 'MARR:PLAC',
		'MARR_CIVIL', 'MARR_PARTNERS', 'MARR_RELIGIOUS', 'MARR_UNKNOWN', 'MARS',
		'MEDI', 'NAME', 'NAME:FONE', 'NAME:_HEB', 'NATI', 'NATU', 'NATU:DATE', 'NATU:PLAC',
		'NCHI', 'NICK', 'NMR', 'NOTE', 'NPFX', 'NSFX', 'OBJE', 'OCCU', 'OCCU:AGNC',
		'ORDI', 'ORDN', 'ORDN:AGNC', 'ORDN:DATE', 'ORDN:PLAC', 'PAGE', 'PEDI', 'PHON',
		'PLAC', 'PLAC:FONE', 'PLAC:ROMN', 'PLAC:_HEB', 'POST', 'PROB', 'PROP', 'PUBL',
		'QUAY', 'REFN', 'RELA', 'RELI', 'REPO', 'RESI', 'RESI:DATE', 'RESI:PLAC', 'RESN',
		'RETI', 'RETI:AGNC', 'RFN', 'RIN', 'ROLE', 'ROMN', 'SERV', 'SEX', 'SHARED_NOTE',
		'SLGC', 'SLGC:DATE', 'SLGC:PLAC', 'SLGS', 'SLGS:DATE', 'SLGS:PLAC', 'SOUR',
		'SPFX', 'SSN', 'STAE', 'STAT', 'STAT:DATE', 'SUBM', 'SUBN', 'SURN', 'TEMP',
		'TEXT', 'TIME', 'TITL', 'TITL:FONE', 'TITL:ROMN', 'TITL:_HEB', 'TRLR', 'TYPE',
		'URL', 'VERS', 'WIFE', 'WILL', 'WWW', '_ADOP_CHIL', '_ADOP_GCHI', '_ADOP_GCH1',
		'_ADOP_GCH2', '_ADOP_HSIB', '_ADOP_SIBL', '_ADPF', '_ADPM', '_AKA', '_AKAN', '_ASSO',
		'_BAPM_CHIL', '_BAPM_GCHI', '_BAPM_GCH1', '_BAPM_GCH2', '_BAPM_HSIB', '_BAPM_SIBL',
		'_BIBL', '_BIRT_CHIL', '_BIRT_GCHI', '_BIRT_GCH1', '_BIRT_GCH2', '_BIRT_HSIB', '_BIRT_SIBL',
		'_BRTM', '_BRTM:DATE', '_BRTM:PLAC', '_BURI_CHIL',
		'_BURI_GCHI', '_BURI_GCH1', '_BURI_GCH2', '_BURI_GPAR', '_BURI_HSIB', '_BURI_SIBL', '_BURI_SPOU',
		'_CHR_CHIL', '_CHR_GCHI', '_CHR_GCH1', '_CHR_GCH2', '_CHR_HSIB', '_CHR_SIBL', '_COML',
		'_CREM_CHIL', '_CREM_GCHI', '_CREM_GCH1', '_CREM_GCH2', '_CREM_GPAR', '_CREM_HSIB', '_CREM_SIBL', '_CREM_SPOU',
		'_DBID', '_DEAT_CHIL', '_DEAT_GCHI', '_DEAT_GCH1', '_DEAT_GCH2', '_DEAT_GPAR', '_DEAT_GPA1', '_DEAT_GPA2',
		'_DEAT_HSIB', '_DEAT_PARE', '_DEAT_SIBL', '_DEAT_SPOU', '_DEG', '_DETS',
		'_EMAIL', '_EYEC', '_FA1', '_FA2', '_FA3', '_FA4', '_FA5', '_FA6', '_FA7', '_FA8',
		'_FA9', '_FA10', '_FA11', '_FA12', '_FA13', '_FNRL', '_FREL', '_GEDF', '_GODP', '_HAIR',
		'_HEB', '_HEIG', '_HNM', '_HOL', '_INTE', '_LOC', '_MARB_CHIL', '_MARB_FAMC', '_MARB_GCHI',
		'_MARB_GCH1', '_MARB_GCH2', '_MARB_HSIB', '_MARB_PARE', '_MARB_SIBL', '_MARI',
		'_MARNM', '_PRIM', '_MARNM_SURN', '_MARR_CHIL', '_MARR_FAMC', '_MARR_GCHI',
		'_MARR_GCH1', '_MARR_GCH2', '_MARR_HSIB', '_MARR_PARE', '_MARR_SIBL', '_MBON',
		'_MDCL', '_MEDC', '_MEND', '_MILI', '_MILT', '_MREL', '_MSTAT', '_NAME', '_NAMS',
		'_NLIV', '_NMAR', '_NMR', '_WT_USER', '_PRMN', '_SCBK', '_SEPR', '_SSHOW', '_STAT',
		'_SUBQ', '_TODO', '_TYPE', '_UID', '_URL', '_WEIG', '_WITN', '_YART', '__BRTM_CHIL',
		'__BRTM_GCHI', '__BRTM_GCH1', '__BRTM_GCH2', '__BRTM_HSIB', '__BRTM_SIBL',
		// These pseudo-tags are generated dynamically to display media object attributes
		'__FILE_SIZE__', '__IMAGE_SIZE__',
	);

	/** @var string[] Possible values for the Object-File-Format types */
	private static $OBJE_FILE_FORM_TYPE = array(
		'audio', 'book', 'card', 'certificate', 'coat', 'document', 'electronic',
		'fiche', 'film', 'magazine', 'manuscript', 'map', 'newspaper', 'photo',
		'tombstone', 'video', 'painting', 'other',
	);

	/**
	 * Is $tag one of our known tags?
	 *
	 * @param string $tag
	 *
	 * @return boolean
	 */
	public static function isTag($tag) {
		return in_array($tag, self::$ALL_TAGS);
	}

	/**
	 * Translate a tag, for an (optional) record
	 *
	 * @param string               $tag
	 * @param WT_GedcomRecord|null $record
	 *
	 * @return string
	 */
	public static function getLabel($tag, WT_GedcomRecord $record = null) {
		if ($record instanceof WT_Individual) {
			$sex = $record->getSex();
		} else {
			$sex = 'U';
		}

		switch ($tag) {
		case 'ABBR':
			return
				/* I18N: gedcom tag ABBR */
				WT_I18N::translate('Abbreviation');
		case 'ADDR':
			return
				/* I18N: gedcom tag ADDR */
				WT_I18N::translate('Address');
		case 'ADR1':
			return WT_I18N::translate('Address line 1');
		case 'ADR2':
			return WT_I18N::translate('Address line 2');
		case 'ADOP':
			return
				/* I18N: gedcom tag ADOP */
				WT_I18N::translate('Adoption');
		case 'ADOP:DATE':
			return WT_I18N::translate('Date of adoption');
		case 'ADOP:PLAC':
			return WT_I18N::translate('Place of adoption');
		case 'AFN':
			return
				/* I18N: gedcom tag AFN */
				WT_I18N::translate('Ancestral file number');
		case 'AGE':
			return
				/* I18N: gedcom tag AGE */
				WT_I18N::translate('Age');
		case 'AGNC':
			return
				/* I18N: gedcom tag AGNC */
				WT_I18N::translate('Agency');
		case 'ALIA':
			return
				/* I18N: gedcom tag ALIA */
				WT_I18N::translate('Alias');
		case 'ANCE':
			return
				/* I18N: gedcom tag ANCE */
				WT_I18N::translate('Generations of ancestors');
		case 'ANCI':
			return
				/* I18N: gedcom tag ANCI */
				WT_I18N::translate('Ancestors interest');
		case 'ANUL':
			return
				/* I18N: gedcom tag ANUL */
				WT_I18N::translate('Annulment');
		case 'ASSO':
			return
				/* I18N: gedcom tag ASSO */
				WT_I18N::translate('Associate'); /* see also _ASSO */
		case 'AUTH':
			return
				/* I18N: gedcom tag AUTH */
				WT_I18N::translate('Author');
		case 'BAPL':
			return
				/* I18N: gedcom tag BAPL */
				WT_I18N::translate('LDS baptism');
		case 'BAPL:DATE':
			return WT_I18N::translate('Date of LDS baptism');
		case 'BAPL:PLAC':
			return WT_I18N::translate('Place of LDS baptism');
		case 'BAPM':
			return
				/* I18N: gedcom tag BAPM */
				WT_I18N::translate('Baptism');
		case 'BAPM:DATE':
			return WT_I18N::translate('Date of baptism');
		case 'BAPM:PLAC':
			return WT_I18N::translate('Place of baptism');
		case 'BARM':
			return
				/* I18N: gedcom tag BARM */
				WT_I18N::translate('Bar mitzvah');
		case 'BARM:DATE':
			return WT_I18N::translate('Date of bar mitzvah');
		case 'BARM:PLAC':
			return WT_I18N::translate('Place of bar mitzvah');
		case 'BASM':
			return
				/* I18N: gedcom tag BASM */
				WT_I18N::translate('Bat mitzvah');
		case 'BASM:DATE':
			return WT_I18N::translate('Date of bat mitzvah');
		case 'BASM:PLAC':
			return WT_I18N::translate('Place of bat mitzvah');
		case 'BIRT':
			return
				/* I18N: gedcom tag BIRT */
				WT_I18N::translate('Birth');
		case 'BIRT:DATE':
			return WT_I18N::translate('Date of birth');
		case 'BIRT:PLAC':
			return WT_I18N::translate('Place of birth');
		case 'BLES':
			return
				/* I18N: gedcom tag BLES */
				WT_I18N::translate('Blessing');
		case 'BLES:DATE':
			return WT_I18N::translate('Date of blessing');
		case 'BLES:PLAC':
			return WT_I18N::translate('Place of blessing');
		case 'BLOB':
			return
				/* I18N: gedcom tag BLOB */
				WT_I18N::translate('Binary data object');
		case 'BURI':
			return
				/* I18N: gedcom tag BURI */
				WT_I18N::translate('Burial');
		case 'BURI:DATE':
			return WT_I18N::translate('Date of burial');
		case 'BURI:PLAC':
			return WT_I18N::translate('Place of burial');
		case 'CALN':
			return
				/* I18N: gedcom tag CALN */
				WT_I18N::translate('Call number');
		case 'CAST':
			return
				/* I18N: gedcom tag CAST */
				WT_I18N::translate('Caste');
		case 'CAUS':
			return
				/* I18N: gedcom tag CAUS */
				WT_I18N::translate('Cause');
		case 'CEME':
			return
				/* I18N: gedcom tag CEME */
				WT_I18N::translate('Cemetery');
		case 'CENS':
			return
				/* I18N: gedcom tag CENS */
				WT_I18N::translate('Census');
		case 'CENS:DATE':
			return WT_I18N::translate('Census date');
		case 'CENS:PLAC':
			return WT_I18N::translate('Census place');
		case 'CHAN':
			return
				/* I18N: gedcom tag CHAN */
				WT_I18N::translate('Last change');
		case 'CHAN:DATE':
			return
				/* I18N: gedcom tag CHAN:DATE */
				WT_I18N::translate('Date of last change');
		case 'CHAN:_WT_USER':
			return
				/* I18N: gedcom tag CHAN:_WT_USER */
				WT_I18N::translate('Author of last change');
		case 'CHAR':
			return
				/* I18N: gedcom tag CHAR */
				WT_I18N::translate('Character set');
		case 'CHIL':
			return
				/* I18N: gedcom tag CHIL */
				WT_I18N::translate('Child');
		case 'CHR':
			return
				/* I18N: gedcom tag CHR */
				WT_I18N::translate('Christening');
		case 'CHR:DATE':
			return WT_I18N::translate('Date of christening');
		case 'CHR:PLAC':
			return WT_I18N::translate('Place of christening');
		case 'CHRA':
			return
				/* I18N: gedcom tag CHRA */
				WT_I18N::translate('Adult christening');
		case 'CITN':
			return
				/* I18N: gedcom tag CITN */
				WT_I18N::translate('Citizenship');
		case 'CITY':
			return
				/* I18N: gedcom tag CITY */
				WT_I18N::translate('City');
		case 'COMM':
			return
				/* I18N: gedcom tag COMM */
				WT_I18N::translate('Comment');
		case 'CONC':
			return
				/* I18N: gedcom tag CONC */
				WT_I18N::translate('Concatenation');
		case 'CONT':
			return
				/* I18N: gedcom tag CONT */
				WT_I18N::translate('Continued');
		case 'CONF':
			return
				/* I18N: gedcom tag CONF */
				WT_I18N::translate('Confirmation');
		case 'CONF:DATE':
			return WT_I18N::translate('Date of confirmation');
		case 'CONF:PLAC':
			return WT_I18N::translate('Place of confirmation');
		case 'CONL':
			return
				/* I18N: gedcom tag CONL */
				WT_I18N::translate('LDS confirmation');
		case 'COPR':
			return
				/* I18N: gedcom tag COPR */
				WT_I18N::translate('Copyright');
		case 'CORP':
			return
				/* I18N: gedcom tag CORP */
				WT_I18N::translate('Corporation');
		case 'CREM':
			return
				/* I18N: gedcom tag CREM */
				WT_I18N::translate('Cremation');
		case 'CREM:DATE':
			return WT_I18N::translate('Date of cremation');
		case 'CREM:PLAC':
			return WT_I18N::translate('Place of cremation');
		case 'CTRY':
			return
				/* I18N: gedcom tag CTRY */
				WT_I18N::translate('Country');
		case 'DATA':
			return
				/* I18N: gedcom tag DATA */
				WT_I18N::translate('Data');
		case 'DATA:DATE':
			return WT_I18N::translate('Date of entry in original source');
		case 'DATE':
			return
				/* I18N: gedcom tag DATE */
				WT_I18N::translate('Date');
		case 'DEAT':
			return
				/* I18N: gedcom tag DEAT */
				WT_I18N::translate('Death');
		case 'DEAT:CAUS':
			return WT_I18N::translate('Cause of death');
		case 'DEAT:DATE':
			return WT_I18N::translate('Date of death');
		case 'DEAT:PLAC':
			return WT_I18N::translate('Place of death');
		case 'DESC':
			return
				/* I18N: gedcom tag DESC */
				WT_I18N::translate('Descendants');
		case 'DESI':
			return
				/* I18N: gedcom tag DESI */
				WT_I18N::translate('Descendants interest');
		case 'DEST':
			return
				/* I18N: gedcom tag DEST */
				WT_I18N::translate('Destination');
		case 'DIV':
			return
				/* I18N: gedcom tag DIV */
				WT_I18N::translate('Divorce');
		case 'DIVF':
			return
				/* I18N: gedcom tag DIVF */
				WT_I18N::translate('Divorce filed');
		case 'DSCR':
			return
				/* I18N: gedcom tag DSCR */
				WT_I18N::translate('Description');
		case 'EDUC':
			return
				/* I18N: gedcom tag EDUC */
				WT_I18N::translate('Education');
		case 'EDUC:AGNC':
			return WT_I18N::translate('School or college');
		case 'EMAI':
			return
				/* I18N: gedcom tag EMAI */
				WT_I18N::translate('Email address');
		case 'EMAIL':
			return
				/* I18N: gedcom tag EMAIL */
				WT_I18N::translate('Email address');
		case 'EMAL':
			return
				/* I18N: gedcom tag EMAL */
				WT_I18N::translate('Email address');
		case 'EMIG':
			return
				/* I18N: gedcom tag EMIG */
				WT_I18N::translate('Emigration');
		case 'EMIG:DATE':
			return WT_I18N::translate('Date of emigration');
		case 'EMIG:PLAC':
			return WT_I18N::translate('Place of emigration');
		case 'ENDL':
			return
				/* I18N: gedcom tag ENDL */
				WT_I18N::translate('LDS endowment');
		case 'ENDL:DATE':
			return WT_I18N::translate('Date of LDS endowment');
		case 'ENDL:PLAC':
			return WT_I18N::translate('Place of LDS endowment');
		case 'ENGA':
			return
				/* I18N: gedcom tag ENGA */
				WT_I18N::translate('Engagement');
		case 'ENGA:DATE':
			return WT_I18N::translate('Date of engagement');
		case 'ENGA:PLAC':
			return WT_I18N::translate('Place of engagement');
		case 'EVEN':
			return
				/* I18N: gedcom tag EVEN */
				WT_I18N::translate('Event');
		case 'EVEN:DATE':
			return WT_I18N::translate('Date of event');
		case 'EVEN:PLAC':
			return WT_I18N::translate('Place of event');
		case 'FACT':
			return
				/* I18N: gedcom tag FACT */
				WT_I18N::translate('Fact');
		case 'FAM':
			return
				/* I18N: gedcom tag FAM */
				WT_I18N::translate('Family');
		case 'FAMC':
			return
				/* I18N: gedcom tag FAMC */
				WT_I18N::translate('Family as a child');
		case 'FAMF':
			return
				/* I18N: gedcom tag FAMF */
				WT_I18N::translate('Family file');
		case 'FAMS':
			return
				/* I18N: gedcom tag FAMS */
				WT_I18N::translate('Family as a spouse');
		case 'FAMS:CENS:DATE':
			return WT_I18N::translate('Spouse census date');
		case 'FAMS:CENS:PLAC':
			return WT_I18N::translate('Spouse census place');
		case 'FAMS:DIV:DATE':
			return WT_I18N::translate('Date of divorce');
		case 'FAMS:MARR:DATE':
			return WT_I18N::translate('Date of marriage');
		case 'FAMS:MARR:PLAC':
			return WT_I18N::translate('Place of marriage');
		case 'FAMS:NOTE':
			return WT_I18N::translate('Spouse note');
		case 'FAMS:SLGS:DATE':
			return WT_I18N::translate('Date of LDS spouse sealing');
		case 'FAMS:SLGS:PLAC':
			return WT_I18N::translate('Place of LDS spouse sealing');
		case 'FAX':
			return
				/* I18N: gedcom tag FAX */
				WT_I18N::translate('Fax');
		case 'FCOM':
			return
				/* I18N: gedcom tag FCOM */
				WT_I18N::translate('First communion');
		case 'FCOM:DATE':
			return WT_I18N::translate('Date of first communion');
		case 'FCOM:PLAC':
			return WT_I18N::translate('Place of first communion');
		case 'FILE':
			return
				/* I18N: gedcom tag FILE */
				WT_I18N::translate('Filename');
		case 'FONE':
			return
				/* I18N: gedcom tag FONE */
				WT_I18N::translate('Phonetic');
		case 'FORM':
			return
				/* I18N: gedcom tag FORM */
				WT_I18N::translate('Format');
		case 'GEDC':
			return
				/* I18N: gedcom tag GEDC */
				WT_I18N::translate('GEDCOM file');
		case 'GIVN':
			return
				/* I18N: gedcom tag GIVN */
				WT_I18N::translate('Given names');
		case 'GRAD':
			return
				/* I18N: gedcom tag GRAD */
				WT_I18N::translate('Graduation');
		case 'HEAD':
			return
				/* I18N: gedcom tag HEAD */
				WT_I18N::translate('Header');
		case 'HUSB':
			return
				/* I18N: gedcom tag HUSB */
				WT_I18N::translate('Husband');
		case 'IDNO':
			return
				/* I18N: gedcom tag IDNO */
				WT_I18N::translate('Identification number');
		case 'IMMI':
			return
				/* I18N: gedcom tag IMMI */
				WT_I18N::translate('Immigration');
		case 'IMMI:DATE':
			return WT_I18N::translate('Date of immigration');
		case 'IMMI:PLAC':
			return WT_I18N::translate('Place of immigration');
		case 'INDI':
			return
				/* I18N: gedcom tag INDI */
				WT_I18N::translate('Individual');
		case 'INFL':
			return
				/* I18N: gedcom tag INFL */
				WT_I18N::translate('Infant');
		case 'LANG':
			return
				/* I18N: gedcom tag LANG */
				WT_I18N::translate('Language');
		case 'LATI':
			return
				/* I18N: gedcom tag LATI */
				WT_I18N::translate('Latitude');
		case 'LEGA':
			return
				/* I18N: gedcom tag LEGA */
				WT_I18N::translate('Legatee');
		case 'LONG':
			return
				/* I18N: gedcom tag LONG */
				WT_I18N::translate('Longitude');
		case 'MAP':
			return
				/* I18N: gedcom tag MAP */
				WT_I18N::translate('Map');
		case 'MARB':
			return
				/* I18N: gedcom tag MARB */
				WT_I18N::translate('Marriage banns');
		case 'MARB:DATE':
			return WT_I18N::translate('Date of marriage banns');
		case 'MARB:PLAC':
			return WT_I18N::translate('Place of marriage banns');
		case 'MARC':
			return
				/* I18N: gedcom tag MARC */
				WT_I18N::translate('Marriage contract');
		case 'MARL':
			return
				/* I18N: gedcom tag MARL */
				WT_I18N::translate('Marriage license');
		case 'MARR':
			return
				/* I18N: gedcom tag MARR */
				WT_I18N::translate('Marriage');
		case 'MARR:DATE':
			return WT_I18N::translate('Date of marriage');
		case 'MARR:PLAC':
			return WT_I18N::translate('Place of marriage');
		case 'MARR_CIVIL':
			return WT_I18N::translate('Civil marriage');
		case 'MARR_PARTNERS':
			return WT_I18N::translate('Registered partnership');
		case 'MARR_RELIGIOUS':
			return WT_I18N::translate('Religious marriage');
		case 'MARR_UNKNOWN':
			return WT_I18N::translate('Marriage type unknown');
		case 'MARS':
			return
				/* I18N: gedcom tag MARS */
				WT_I18N::translate('Marriage settlement');
		case 'MEDI':
			return
				/* I18N: gedcom tag MEDI */
				WT_I18N::translate('Media type');
		case 'NAME':
			if ($record instanceof WT_Repository) {
				return
					/* I18N: gedcom tag REPO:NAME */
					WT_I18N::translate_c('Repository', 'Name');
			} else {
				return
					/* I18N: gedcom tag NAME */
					WT_I18N::translate('Name');
			}
		case 'NAME:FONE':
			return WT_I18N::translate('Phonetic name');
		case 'NAME:_HEB':
			return WT_I18N::translate('Name in Hebrew');
		case 'NATI':
			return
				/* I18N: gedcom tag NATI */
				WT_I18N::translate('Nationality');
		case 'NATU':
			return
				/* I18N: gedcom tag NATU */
				WT_I18N::translate('Naturalization');
		case 'NATU:DATE':
			return WT_I18N::translate('Date of naturalization');
		case 'NATU:PLAC':
			return WT_I18N::translate('Place of naturalization');
		case 'NCHI':
			return
				/* I18N: gedcom tag NCHI */
				WT_I18N::translate('Number of children');
		case 'NICK':
			return
				/* I18N: gedcom tag NICK */
				WT_I18N::translate('Nickname');
		case 'NMR':
			return
				/* I18N: gedcom tag NMR */
				WT_I18N::translate('Number of marriages');
		case 'NOTE':
			return
				/* I18N: gedcom tag NOTE */
				WT_I18N::translate('Note');
		case 'NPFX':
			return
				/* I18N: gedcom tag NPFX */
				WT_I18N::translate('Name prefix');
		case 'NSFX':
			return
				/* I18N: gedcom tag NSFX */
				WT_I18N::translate('Name suffix');
		case 'OBJE':
			return
				/* I18N: gedcom tag OBJE */
				WT_I18N::translate('Media object');
		case 'OCCU':
			return
				/* I18N: gedcom tag OCCU */
				WT_I18N::translate('Occupation');
		case 'OCCU:AGNC':
			return WT_I18N::translate('Employer');
		case 'ORDI':
			return
				/* I18N: gedcom tag ORDI */
				WT_I18N::translate('Ordinance');
		case 'ORDN':
			return
				/* I18N: gedcom tag ORDN */
				WT_I18N::translate('Ordination');
		case 'ORDN:AGNC':
			return WT_I18N::translate('Religious institution');
		case 'ORDN:DATE':
			return WT_I18N::translate('Date of ordination');
		case 'ORDN:PLAC':
			return WT_I18N::translate('Place of ordination');
		case 'PAGE':
			return
				/* I18N: gedcom tag PAGE */
				WT_I18N::translate('Citation details');
		case 'PEDI':
			return
				/* I18N: gedcom tag PEDI */
				WT_I18N::translate('Relationship to parents');
		case 'PHON':
			return
				/* I18N: gedcom tag PHON */
				WT_I18N::translate('Phone');
		case 'PLAC':
			return
				/* I18N: gedcom tag PLAC */
				WT_I18N::translate('Place');
		case 'PLAC:FONE':
			return WT_I18N::translate('Phonetic place');
		case 'PLAC:ROMN':
			return WT_I18N::translate('Romanized place');
		case 'PLAC:_HEB':
			return WT_I18N::translate('Place in Hebrew');
		case 'POST':
			return
				/* I18N: gedcom tag POST */
				WT_I18N::translate('Postal code');
		case 'PROB':
			return
				/* I18N: gedcom tag PROB */
				WT_I18N::translate('Probate');
		case 'PROP':
			return
				/* I18N: gedcom tag PROP */
				WT_I18N::translate('Property');
		case 'PUBL':
			return
				/* I18N: gedcom tag PUBL */
				WT_I18N::translate('Publication');
		case 'QUAY':
			return
				/* I18N: gedcom tag QUAY */
				WT_I18N::translate('Quality of data');
		case 'REFN':
			return
				/* I18N: gedcom tag REFN */
				WT_I18N::translate('Reference number');
		case 'RELA':
			return
				/* I18N: gedcom tag RELA */
				WT_I18N::translate('Relationship');
		case 'RELI':
			return
				/* I18N: gedcom tag RELI */
				WT_I18N::translate('Religion');
		case 'REPO':
			return
				/* I18N: gedcom tag REPO */
				WT_I18N::translate('Repository');
		case 'RESI':
			return
				/* I18N: gedcom tag RESI */
				WT_I18N::translate('Residence');
		case 'RESI:DATE':
			return WT_I18N::translate('Date of residence');
		case 'RESI:PLAC':
			return WT_I18N::translate('Place of residence');
		case 'RESN':
			return
				/* I18N: gedcom tag RESN */
				WT_I18N::translate('Restriction');
		case 'RETI':
			return
				/* I18N: gedcom tag RETI */
				WT_I18N::translate('Retirement');
		case 'RETI:AGNC':
			return WT_I18N::translate('Employer');
		case 'RFN':
			return
				/* I18N: gedcom tag RFN */
				WT_I18N::translate('Record file number');
		case 'RIN':
			return
				/* I18N: gedcom tag RIN */
				WT_I18N::translate('Record ID number');
		case 'ROLE':
			return
				/* I18N: gedcom tag ROLE */
				WT_I18N::translate('Role');
		case 'ROMN':
			return
				/* I18N: gedcom tag ROMN */
				WT_I18N::translate('Romanized');
		case 'SERV':
			return
				/* I18N: gedcom tag SERV */
				WT_I18N::translate('Remote server');
		case 'SEX':
			return
				/* I18N: gedcom tag SEX */
				WT_I18N::translate('Gender');
		case 'SHARED_NOTE':
			return WT_I18N::translate('Shared note');
		case 'SLGC':
			return
				/* I18N: gedcom tag SLGC */
				WT_I18N::translate('LDS child sealing');
		case 'SLGC:DATE':
			return WT_I18N::translate('Date of LDS child sealing');
		case 'SLGC:PLAC':
			return WT_I18N::translate('Place of LDS child sealing');
		case 'SLGS':
			return
				/* I18N: gedcom tag SLGS */
				WT_I18N::translate('LDS spouse sealing');
		case 'SOUR':
			return
				/* I18N: gedcom tag SOUR */
				WT_I18N::translate('Source');
		case 'SPFX':
			return
				/* I18N: gedcom tag SPFX */
				WT_I18N::translate('Surname prefix');
		case 'SSN':
			return
				/* I18N: gedcom tag SSN */
				WT_I18N::translate('Social security number');
		case 'STAE':
			return
				/* I18N: gedcom tag STAE */
				WT_I18N::translate('State');
		case 'STAT':
			return
				/* I18N: gedcom tag STAT */
				WT_I18N::translate('Status');
		case 'STAT:DATE':
			return WT_I18N::translate('Status change date');
		case 'SUBM':
			return
				/* I18N: gedcom tag SUBM */
				WT_I18N::translate('Submitter');
		case 'SUBN':
			return
				/* I18N: gedcom tag SUBN */
				WT_I18N::translate('Submission');
		case 'SURN':
			return
				/* I18N: gedcom tag SURN */
				WT_I18N::translate('Surname');
		case 'TEMP':
			return
				/* I18N: gedcom tag TEMP */
				WT_I18N::translate('Temple');
		case 'TEXT':
			return
				/* I18N: gedcom tag TEXT */
				WT_I18N::translate('Text');
		case 'TIME':
			return
				/* I18N: gedcom tag TIME */
				WT_I18N::translate('Time');
		case 'TITL':
			return
				/* I18N: gedcom tag TITL */
				WT_I18N::translate('Title');
		case 'TITL:FONE':
			return WT_I18N::translate('Phonetic title');
		case 'TITL:ROMN':
			return WT_I18N::translate('Romanized title');
		case 'TITL:_HEB':
			return WT_I18N::translate('Title in Hebrew');
		case 'TRLR':
			return
				/* I18N: gedcom tag TRLR */
				WT_I18N::translate('Trailer');
		case 'TYPE':
			return
				/* I18N: gedcom tag TYPE */
				WT_I18N::translate('Type');
		case 'URL':
			return
				/* I18N: gedcom tag URL (A web address / URL) */
				WT_I18N::translate('URL');
		case 'VERS':
			return
				/* I18N: gedcom tag VERS */
				WT_I18N::translate('Version');
		case 'WIFE':
			return
				/* I18N: gedcom tag WIFE */
				WT_I18N::translate('Wife');
		case 'WILL':
			return
				/* I18N: gedcom tag WILL */
				WT_I18N::translate('Will');
		case 'WWW':
			return
				/* I18N: gedcom tag WWW (A web address / URL) */
				WT_I18N::translate('URL');
		case '_ADOP_CHIL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Adoption of a son');
			case 'F':
				return WT_I18N::translate('Adoption of a daughter');
			default:
				return WT_I18N::translate('Adoption of a child');
			}
		case '_ADOP_GCHI':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Adoption of a grandson');
			case 'F':
				return WT_I18N::translate('Adoption of a granddaughter');
			default:
				return WT_I18N::translate('Adoption of a grandchild');
			}
		case '_ADOP_GCH1':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('daughter’s son', 'Adoption of a grandson');
			case 'F':
				return WT_I18N::translate_c('daughter’s daughter', 'Adoption of a granddaughter');
			default:
				return WT_I18N::translate('Adoption of a grandchild');
			}
		case '_ADOP_GCH2':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('son’s son', 'Adoption of a grandson');
			case 'F':
				return WT_I18N::translate_c('son’s daughter', 'Adoption of a granddaughter');
			default:
				return WT_I18N::translate('Adoption of a grandchild');
			}
		case '_ADOP_HSIB':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Adoption of a half-brother');
			case 'F':
				return WT_I18N::translate('Adoption of a half-sister');
			default:
				return WT_I18N::translate('Adoption of a half-sibling');
			}
		case '_ADOP_SIBL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Adoption of a brother');
			case 'F':
				return WT_I18N::translate('Adoption of a sister');
			default:
				return WT_I18N::translate('Adoption of a sibling');
			}
		case '_ADPF':
			switch ($sex) {
			case 'M':
				return
					/* I18N: gedcom tag _ADPF */
					WT_I18N::translate_c('MALE', 'Adopted by father');
			case 'F':
				return
					/* I18N: gedcom tag _ADPF */
					WT_I18N::translate_c('FEMALE', 'Adopted by father');
			default:
				return
					/* I18N: gedcom tag _ADPF */
					WT_I18N::translate('Adopted by father');
			}
		case '_ADPM':
			switch ($sex) {
			case 'M':
				return
					/* I18N: gedcom tag _ADPM */
					WT_I18N::translate_c('MALE', 'Adopted by mother');
			case 'F':
				return
					/* I18N: gedcom tag _ADPM */
					WT_I18N::translate_c('FEMALE', 'Adopted by mother');
			default:
				return
					/* I18N: gedcom tag _ADPM */
					WT_I18N::translate('Adopted by mother');
			}
		case '_AKA':
		case '_AKAN':
			switch ($sex) {
			case 'M':
				return
					/* I18N: gedcom tag _AKA */
					WT_I18N::translate_c('MALE', 'Also known as');
			case 'F':
				return
					/* I18N: gedcom tag _AKA */
					WT_I18N::translate_c('FEMALE', 'Also known as');
			default:
				return
					/* I18N: gedcom tag _AKA */
					WT_I18N::translate('Also known as');
			}
		case '_ASSO':
			return
				/* I18N: gedcom tag _ASSO */
				WT_I18N::translate('Associate'); /* see also ASSO */
		case '_BAPM_CHIL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Baptism of a son');
			case 'F':
				return WT_I18N::translate('Baptism of a daughter');
			default:
				return WT_I18N::translate('Baptism of a child');
			}
		case '_BAPM_GCHI':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Baptism of a grandson');
			case 'F':
				return WT_I18N::translate('Baptism of a granddaughter');
			default:
				return WT_I18N::translate('Baptism of a grandchild');
			}
		case '_BAPM_GCH1':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('daughter’s son', 'Baptism of a grandson');
			case 'F':
				return WT_I18N::translate_c('daughter’s daughter', 'Baptism of a granddaughter');
			default:
				return WT_I18N::translate('Baptism of a grandchild');
			}
		case '_BAPM_GCH2':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('son’s son', 'Baptism of a grandson');
			case 'F':
				return WT_I18N::translate_c('son’s daughter', 'Baptism of a granddaughter');
			default:
				return WT_I18N::translate('Baptism of a grandchild');
			}
		case '_BAPM_HSIB':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Baptism of a half-brother');
			case 'F':
				return WT_I18N::translate('Baptism of a half-sister');
			default:
				return WT_I18N::translate('Baptism of a half-sibling');
			}
		case '_BAPM_SIBL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Baptism of a brother');
			case 'F':
				return WT_I18N::translate('Baptism of a sister');
			default:
				return WT_I18N::translate('Baptism of a sibling');
			}
		case '_BIBL':
			return
				/* I18N: gedcom tag _BIBL */
				WT_I18N::translate('Bibliography');
		case '_BIRT_CHIL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Birth of a son');
			case 'F':
				return WT_I18N::translate('Birth of a daughter');
			default:
				return WT_I18N::translate('Birth of a child');
			}
		case '_BIRT_GCHI':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Birth of a grandson');
			case 'F':
				return WT_I18N::translate('Birth of a granddaughter');
			default:
				return WT_I18N::translate('Birth of a grandchild');
			}
		case '_BIRT_GCH1':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('daughter’s son', 'Birth of a grandson');
			case 'F':
				return WT_I18N::translate_c('daughter’s daughter', 'Birth of a granddaughter');
			default:
				return WT_I18N::translate('Birth of a grandchild');
			}
		case '_BIRT_GCH2':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('son’s son', 'Birth of a grandson');
			case 'F':
				return WT_I18N::translate_c('son’s daughter', 'Birth of a granddaughter');
			default:
				return WT_I18N::translate('Birth of a grandchild');
			}
		case '_BIRT_HSIB':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Birth of a half-brother');
			case 'F':
				return WT_I18N::translate('Birth of a half-sister');
			default:
				return WT_I18N::translate('Birth of a half-sibling');
			}
		case '_BIRT_SIBL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Birth of a brother');
			case 'F':
				return WT_I18N::translate('Birth of a sister');
			default:
				return WT_I18N::translate('Birth of a sibling');
			}
		case '_BRTM':
			return
				/* I18N: gedcom tag _BRTM */
				WT_I18N::translate('Brit milah');
		case '_BRTM:DATE':
			return WT_I18N::translate('Date of brit milah');
		case '_BRTM:PLAC':
			return WT_I18N::translate('Place of brit milah');
		case '_BURI_CHIL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Burial of a son');
			case 'F':
				return WT_I18N::translate('Burial of a daughter');
			default:
				return WT_I18N::translate('Burial of a child');
			}
		case '_BURI_GCHI':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Burial of a grandson');
			case 'F':
				return WT_I18N::translate('Burial of a granddaughter');
			default:
				return WT_I18N::translate('Burial of a grandchild');
			}
		case '_BURI_GCH1':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('daughter’s son', 'Burial of a grandson');
			case 'F':
				return WT_I18N::translate_c('daughter’s daughter', 'Burial of a granddaughter');
			default:
				return WT_I18N::translate('Burial of a grandchild');
			}
		case '_BURI_GCH2':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('son’s son', 'Burial of a grandson');
			case 'F':
				return WT_I18N::translate_c('son’s daughter', 'Burial of a granddaughter');
			default:
				return WT_I18N::translate('Burial of a grandchild');
			}
		case '_BURI_GPAR':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Burial of a grandfather');
			case 'F':
				return WT_I18N::translate('Burial of a grandmother');
			default:
				return WT_I18N::translate('Burial of a grandparent');
			}
		case '_BURI_GPA1':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Burial of a paternal grandfather');
			case 'F':
				return WT_I18N::translate('Burial of a paternal grandmother');
			default:
				return WT_I18N::translate('Burial of a paternal grandparent');
			}
		case '_BURI_GPA2':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Burial of a maternal grandfather');
			case 'F':
				return WT_I18N::translate('Burial of a maternal grandmother');
			default:
				return WT_I18N::translate('Burial of a maternal grandparent');
			}
		case '_BURI_HSIB':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Burial of a half-brother');
			case 'F':
				return WT_I18N::translate('Burial of a half-sister');
			default:
				return WT_I18N::translate('Burial of a half-sibling');
			}
		case '_BURI_PARE':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Burial of a father');
			case 'F':
				return WT_I18N::translate('Burial of a mother');
			default:
				return WT_I18N::translate('Burial of a parent');
			}
		case '_BURI_SIBL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Burial of a brother');
			case 'F':
				return WT_I18N::translate('Burial of a sister');
			default:
				return WT_I18N::translate('Burial of a sibling');
			}
		case '_BURI_SPOU':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Burial of a husband');
			case 'F':
				return WT_I18N::translate('Burial of a wife');
			default:
				return WT_I18N::translate('Burial of a spouse');
			}
		case '_CHR_CHIL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Christening of a son');
			case 'F':
				return WT_I18N::translate('Christening of a daughter');
			default:
				return WT_I18N::translate('Christening of a child');
			}
		case '_CHR_GCHI':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Christening of a grandson');
			case 'F':
				return WT_I18N::translate('Christening of a granddaughter');
			default:
				return WT_I18N::translate('Christening of a grandchild');
			}
		case '_CHR_GCH1':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('daughter’s son', 'Christening of a grandson');
			case 'F':
				return WT_I18N::translate_c('daughter’s daughter', 'Christening of a granddaughter');
			default:
				return WT_I18N::translate('Christening of a grandchild');
			}
		case '_CHR_GCH2':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('son’s son', 'Christening of a grandson');
			case 'F':
				return WT_I18N::translate_c('son’s daughter', 'Christening of a granddaughter');
			default:
				return WT_I18N::translate('Christening of a grandchild');
			}
		case '_CHR_HSIB':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Christening of a half-brother');
			case 'F':
				return WT_I18N::translate('Christening of a half-sister');
			default:
				return WT_I18N::translate('Christening of a half-sibling');
			}
		case '_CHR_SIBL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Christening of a brother');
			case 'F':
				return WT_I18N::translate('Christening of a sister');
			default:
				return WT_I18N::translate('Christening of a sibling');
			}
		case '_COML':
			return
				/* I18N: gedcom tag _COML */
				WT_I18N::translate('Common law marriage');
		case '_CREM_CHIL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Cremation of a son');
			case 'F':
				return WT_I18N::translate('Cremation of a daughter');
			default:
				return WT_I18N::translate('Cremation of a child');
			}
		case '_CREM_GCHI':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Cremation of a grandson');
			case 'F':
				return WT_I18N::translate('Cremation of a granddaughter');
			default:
				return WT_I18N::translate('Cremation of a grandchild');
			}
		case '_CREM_GCH1':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('daughter’s son', 'Cremation of a grandson');
			case 'F':
				return WT_I18N::translate_c('daughter’s daughter', 'Cremation of a granddaughter');
			default:
				return WT_I18N::translate('Cremation of a grandchild');
			}
		case '_CREM_GCH2':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('son’s son', 'Cremation of a grandson');
			case 'F':
				return WT_I18N::translate_c('son’s daughter', 'Cremation of a granddaughter');
			default:
				return WT_I18N::translate('Cremation of a grandchild');
			}
		case '_CREM_GPAR':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Cremation of a grandfather');
			case 'F':
				return WT_I18N::translate('Cremation of a grandmother');
			default:
				return WT_I18N::translate('Cremation of a grand-parent');
			}
		case '_CREM_GPA1':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Cremation of a paternal grandfather');
			case 'F':
				return WT_I18N::translate('Cremation of a paternal grandmother');
			default:
				return WT_I18N::translate('Cremation of a grand-parent');
			}
		case '_CREM_GPA2':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Cremation of a maternal grandfather');
			case 'F':
				return WT_I18N::translate('Cremation of a maternal grandmother');
			default:
				return WT_I18N::translate('Cremation of a grand-parent');
			}
		case '_CREM_HSIB':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Cremation of a half-brother');
			case 'F':
				return WT_I18N::translate('Cremation of a half-sister');
			default:
				return WT_I18N::translate('Cremation of a half-sibling');
			}
		case '_CREM_PARE':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Cremation of a father');
			case 'F':
				return WT_I18N::translate('Cremation of a mother');
			default:
				return WT_I18N::translate('Cremation of a parent');
			}
		case '_CREM_SIBL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Cremation of a brother');
			case 'F':
				return WT_I18N::translate('Cremation of a sister');
			default:
				return WT_I18N::translate('Cremation of a sibling');
			}
		case '_CREM_SPOU':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Cremation of a husband');
			case 'F':
				return WT_I18N::translate('Cremation of a wife');
			default:
				return WT_I18N::translate('Cremation of a spouse');
			}
		case '_DBID':
			return
				/* I18N: gedcom tag _DBID */
				WT_I18N::translate('Linked database ID');
		case '_DEAT_CHIL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Death of a son');
			case 'F':
				return WT_I18N::translate('Death of a daughter');
			default:
				return WT_I18N::translate('Death of a child');
			}
		case '_DEAT_GCHI':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Death of a grandson');
			case 'F':
				return WT_I18N::translate('Death of a granddaughter');
			default:
				return WT_I18N::translate('Death of a grandchild');
			}
		case '_DEAT_GCH1':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('daughter’s son', 'Death of a grandson');
			case 'F':
				return WT_I18N::translate_c('daughter’s daughter', 'Death of a granddaughter');
			default:
				return WT_I18N::translate('Death of a grandchild');
			}
		case '_DEAT_GCH2':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('son’s son', 'Death of a grandson');
			case 'F':
				return WT_I18N::translate_c('son’s daughter', 'Death of a granddaughter');
			default:
				return WT_I18N::translate('Death of a grandchild');
			}
		case '_DEAT_GPAR':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Death of a grandfather');
			case 'F':
				return WT_I18N::translate('Death of a grandmother');
			default:
				return WT_I18N::translate('Death of a grand-parent');
			}
		case '_DEAT_GPA1':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Death of a paternal grandfather');
			case 'F':
				return WT_I18N::translate('Death of a paternal grandmother');
			default:
				return WT_I18N::translate('Death of a grand-parent');
			}
		case '_DEAT_GPA2':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Death of a maternal grandfather');
			case 'F':
				return WT_I18N::translate('Death of a maternal grandmother');
			default:
				return WT_I18N::translate('Death of a grand-parent');
			}
		case '_DEAT_HSIB':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Death of a half-brother');
			case 'F':
				return WT_I18N::translate('Death of a half-sister');
			default:
				return WT_I18N::translate('Death of a half-sibling');
			}
		case '_DEAT_PARE':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Death of a father');
			case 'F':
				return WT_I18N::translate('Death of a mother');
			default:
				return WT_I18N::translate('Death of a parent');
			}
		case '_DEAT_SIBL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Death of a brother');
			case 'F':
				return WT_I18N::translate('Death of a sister');
			default:
				return WT_I18N::translate('Death of a sibling');
			}
		case '_DEAT_SPOU':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Death of a husband');
			case 'F':
				return WT_I18N::translate('Death of a wife');
			default:
				return WT_I18N::translate('Death of a spouse');
			}
		case '_DEG':
			return
				/* I18N: gedcom tag _DEG */
				WT_I18N::translate('Degree');
		case '_DETS':
			return
				/* I18N: gedcom tag _DETS */
				WT_I18N::translate('Death of one spouse');
		case '_DNA':
			return
				/* I18N: gedcom tag _DNA (from FTM 2010) */
				WT_I18N::translate('DNA markers');
		case '_EMAIL':
			return
				/* I18N: gedcom tag _EMAIL */
				WT_I18N::translate('Email address');
		case '_EYEC':
			return
				/* I18N: gedcom tag _EYEC */
				WT_I18N::translate('Eye color');
		case '_FA1':
			return WT_I18N::translate('Fact 1');
		case '_FA2':
			return WT_I18N::translate('Fact 2');
		case '_FA3':
			return WT_I18N::translate('Fact 3');
		case '_FA4':
			return WT_I18N::translate('Fact 4');
		case '_FA5':
			return WT_I18N::translate('Fact 5');
		case '_FA6':
			return WT_I18N::translate('Fact 6');
		case '_FA7':
			return WT_I18N::translate('Fact 7');
		case '_FA8':
			return WT_I18N::translate('Fact 8');
		case '_FA9':
			return WT_I18N::translate('Fact 9');
		case '_FA10':
			return WT_I18N::translate('Fact 10');
		case '_FA11':
			return WT_I18N::translate('Fact 11');
		case '_FA12':
			return WT_I18N::translate('Fact 12');
		case '_FA13':
			return WT_I18N::translate('Fact 13');
		case '_FNRL':
			return
				/* I18N: gedcom tag _FNRL */
				WT_I18N::translate('Funeral');
		case '_FREL':
			return
				/* I18N: gedcom tag _FREL */
				WT_I18N::translate('Relationship to father');
		case '_GEDF':
			return
				/* I18N: gedcom tag _GEDF */
				WT_I18N::translate('GEDCOM file');
		case '_GODP':
			return
				/* I18N: gedcom tag _GODP */
				WT_I18N::translate('Godparent');
		case '_HAIR':
			return
				/* I18N: gedcom tag _HAIR */
				WT_I18N::translate('Hair color');
		case '_HEB':
			return
				/* I18N: gedcom tag _HEB */
				WT_I18N::translate('Hebrew');
		case '_HEIG':
			return
				/* I18N: gedcom tag _HEIG */
				WT_I18N::translate('Height');
		case '_HNM':
			return
				/* I18N: gedcom tag _HNM */
				WT_I18N::translate('Hebrew name');
		case '_HOL':
			return
				/* I18N: gedcom tag _HOL */
				WT_I18N::translate('Holocaust');
		case '_INTE':
			switch ($sex) {
			case 'M':
				return
					/* I18N: gedcom tag _INTE */
					WT_I18N::translate_c('MALE', 'Interred');
			case 'F':
				return
					/* I18N: gedcom tag _INTE */
					WT_I18N::translate_c('FEMALE', 'Interred');
			default:
				return
					/* I18N: gedcom tag _INTE */
					WT_I18N::translate('Interred');
			}
		case '_LOC':
			return
				/* I18N: gedcom tag _LOC */
				WT_I18N::translate('Location');
		case '_MARI':
			return
				/* I18N: gedcom tag _MARI */
				WT_I18N::translate('Marriage intention');
		case '_MARNM':
			return
				/* I18N: gedcom tag _MARNM */
				WT_I18N::translate('Married name');
		case '_PRIM':
			return
				/* I18N: gedcom tag _PRIM */
				WT_I18N::translate('Highlighted image');
		case '_MARNM_SURN':
			return WT_I18N::translate('Married surname');
		case '_MARR_CHIL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Marriage of a son');
			case 'F':
				return WT_I18N::translate('Marriage of a daughter');
			default:
				return WT_I18N::translate('Marriage of a child');
			}
		case '_MARR_FAMC':
			return
				/* I18N: ...to each other */
				WT_I18N::translate('Marriage of parents');
		case '_MARR_GCHI':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Marriage of a grandson');
			case 'F':
				return WT_I18N::translate('Marriage of a granddaughter');
			default:
				return WT_I18N::translate('Marriage of a grandchild');
			}
		case '_MARR_GCH1':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('daughter’s son', 'Marriage of a grandson');
			case 'F':
				return WT_I18N::translate_c('daughter’s daughter', 'Marriage of a granddaughter');
			default:
				return WT_I18N::translate('Marriage of a grandchild');
			}
		case '_MARR_GCH2':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('son’s son', 'Marriage of a grandson');
			case 'F':
				return WT_I18N::translate_c('son’s daughter', 'Marriage of a granddaughter');
			default:
				return WT_I18N::translate('Marriage of a grandchild');
			}
		case '_MARR_HSIB':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Marriage of a half-brother');
			case 'F':
				return WT_I18N::translate('Marriage of a half-sister');
			default:
				return WT_I18N::translate('Marriage of a half-sibling');
			}
		case '_MARR_PARE':
			switch ($sex) {
			case 'M':
				return
					/* I18N: ...to another spouse */
					WT_I18N::translate('Marriage of a father');
			case 'F':
				return
					/* I18N: ...to another spouse */
					WT_I18N::translate('Marriage of a mother');
			default:
				return
					/* I18N: ...to another spouse */
					WT_I18N::translate('Marriage of a parent');
			}
		case '_MARR_SIBL':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate('Marriage of a brother');
			case 'F':
				return WT_I18N::translate('Marriage of a sister');
			default:
				return WT_I18N::translate('Marriage of a sibling');
			}
		case '_MBON':
			return
				/* I18N: gedcom tag _MBON  */
				WT_I18N::translate('Marriage bond');
		case '_MDCL':
			return
				/* I18N: gedcom tag _MDCL  */
				WT_I18N::translate('Medical');
		case '_MEDC':
			return
				/* I18N: gedcom tag _MEDC  */
				WT_I18N::translate('Medical condition');
		case '_MEND':
			return
				/* I18N: gedcom tag _MEND  */
				WT_I18N::translate('Marriage ending status');
		case '_MILI':
			return
				/* I18N: gedcom tag _MILI  */
				WT_I18N::translate('Military');
		case '_MILT':
			return
				/* I18N: gedcom tag _MILT  */
				WT_I18N::translate('Military service');
		case '_MREL':
			return
				/* I18N: gedcom tag _MREL  */
				WT_I18N::translate('Relationship to mother');
		case '_MSTAT':
			return
				/* I18N: gedcom tag _MSTAT */
				WT_I18N::translate('Marriage beginning status');
		case '_NAME':
			return
				/* I18N: gedcom tag _NAME  */
				WT_I18N::translate('Mailing name');
		case '_NAMS':
			return
				/* I18N: gedcom tag _NAMS  */
				WT_I18N::translate('Namesake');
		case '_NLIV':
			return
				/* I18N: gedcom tag _NLIV  */
				WT_I18N::translate('Not living');
		case '_NMAR':
			switch ($sex) {
			case 'M':
				return
					/* I18N: gedcom tag _NMAR */
					WT_I18N::translate_c('MALE', 'Never married');
			case 'F':
				return
					/* I18N: gedcom tag _NMAR */
					WT_I18N::translate_c('FEMALE', 'Never married');
			default:
				return
					/* I18N: gedcom tag _NMAR */
					WT_I18N::translate('Never married');
			}
		case '_NMR':
			switch ($sex) {
			case 'M':
				return
					/* I18N: gedcom tag _NMR */
					WT_I18N::translate_c('MALE', 'Not married');
			case 'F':
				return
					/* I18N: gedcom tag _NMR */
					WT_I18N::translate_c('FEMALE', 'Not married');
			default:
				return
					/* I18N: gedcom tag _NMR */
					WT_I18N::translate('Not married');
			}
		case '_WT_USER':
			return WT_I18N::translate('by');
		case '_PRMN':
			return
				/* I18N: gedcom tag _PRMN  */
				WT_I18N::translate('Permanent number');
		case '_SCBK':
			return
				/* I18N: gedcom tag _SCBK  */
				WT_I18N::translate('Scrapbook');
		case '_SEPR':
			return
				/* I18N: gedcom tag _SEPR  */
				WT_I18N::translate('Separated');
		case '_SSHOW':
			return
				/* I18N: gedcom tag _SSHOW */
				WT_I18N::translate('Slide show');
		case '_STAT':
			return
				/* I18N: gedcom tag _STAT  */
				WT_I18N::translate('Marriage status');
		case '_SUBQ':
			return
				/* I18N: gedcom tag _SUBQ  */
				WT_I18N::translate('Short version');
		case '_TODO':
			return
				/* I18N: gedcom tag _TODO  */
				WT_I18N::translate('Research task');
		case '_TYPE':
			return
				/* I18N: gedcom tag _TYPE  */
				WT_I18N::translate('Media type');
		case '_UID':
			return
				/* I18N: gedcom tag _UID   */
				WT_I18N::translate('Globally unique identifier');
		case '_URL':
			return
				/* I18N: gedcom tag _URL   */
				WT_I18N::translate('URL');
		case '_WEIG':
			return
				/* I18N: gedcom tag _WEIG  */
				WT_I18N::translate('Weight');
		case '_WITN':
			return
				/* I18N: gedcom tag _WITN  */
				WT_I18N::translate('Witness');
		case '_WT_OBJE_SORT':
			return
				/* I18N: gedcom tag _WT_OBJE_SORT  */
				WT_I18N::translate('Re-order media');
		case '_YART':
			return
				/* I18N: gedcom tag _YART  */
				WT_I18N::translate('Yahrzeit');
			// Brit milah applies only to males, no need for male/female translations
		case '__BRTM_CHIL':
			return WT_I18N::translate('Brit milah of a son');
		case '__BRTM_GCHI':
			return WT_I18N::translate('Brit milah of a grandson');
		case '__BRTM_GCH1':
			return WT_I18N::translate_c('daughter’s son', 'Brit milah of a grandson');
		case '__BRTM_GCH2':
			return WT_I18N::translate_c('son’s son', 'Brit milah of a grandson');
		case '__BRTM_HSIB':
			return WT_I18N::translate('Brit milah of a half-brother');
		case '__BRTM_SIBL':
			return WT_I18N::translate('Brit milah of a brother');
			// These "pseudo" tags are generated internally to present information about a media object
		case '__FILE_SIZE__':
			return WT_I18N::translate('File size');
		case '__IMAGE_SIZE__':
			return WT_I18N::translate('Image dimensions');
		default:
			// If no specialisation exists (e.g. DEAT:CAUS), then look for the general (CAUS)
			if (strpos($tag, ':')) {
				list(, $tag) = explode(':', $tag, 2);

				return self::getLabel($tag, $record);
			}

			// Still no translation? Highlight this as an error
			return '<span class="error" title="' . WT_I18N::translate('Unrecognized GEDCOM code') . '">' . WT_Filter::escapeHtml($tag) . '</span>';
		}
	}

	/**
	 * Translate a label/value pair, such as “Occupation: Farmer”
	 *
	 * @param string               $tag
	 * @param string               $value
	 * @param WT_GedcomRecord|null $record
	 * @param string|null          $element
	 *
	 * @return string
	 */
	public static function getLabelValue($tag, $value, WT_GedcomRecord $record = null, $element = 'div') {
		return
			'<' . $element . ' class="fact_' . $tag . '">' .
			/* I18N: a label/value pair, such as “Occupation: Farmer”.  Some languages may need to change the punctuation. */
			WT_I18N::translate('<span class="label">%1$s:</span> <span class="field" dir="auto">%2$s</span>', self::getLabel($tag, $record), $value) .
			'</' . $element . '>';
	}

	/**
	 * Get a list of facts, for use in the "fact picker" edit control
	 *
	 * @return string[]
	 */
	public static function getPicklistFacts() {
		// Just include facts that can be used at level 1 in a record
		$tags = array(
			'ABBR', 'ADOP', 'AFN', 'ALIA', 'ANUL', 'ASSO', 'AUTH', 'BAPL', 'BAPM', 'BARM',
			'BASM', 'BIRT', 'BLES', 'BURI', 'CAST', 'CENS', 'CHAN', 'CHR', 'CHRA', 'CITN',
			'CONF', 'CONL', 'CREM', 'DEAT', 'DIV', 'DIVF', 'DSCR', 'EDUC', 'EMIG', 'ENDL',
			'ENGA', 'EVEN', 'FACT', 'FCOM', 'FORM', 'GRAD', 'IDNO', 'IMMI', 'LEGA', 'MARB',
			'MARC', 'MARL', 'MARR', 'MARS', 'NAME', 'NATI', 'NATU', 'NCHI', 'NICK', 'NMR',
			'OCCU', 'ORDI', 'ORDN', 'PROB', 'PROP', 'REFN', 'RELI', 'REPO', 'RESI', 'RESN',
			'RETI', 'RFN', 'RIN', 'SEX', 'SLGC', 'SLGS', 'SSN', 'SUBM', 'TITL', 'WILL', 'WWW',
			'_BRTM', '_COML', '_DEG', '_EYEC', '_FNRL', '_HAIR', '_HEIG', '_HNM', '_HOL',
			'_INTE', '_MARI', '_MBON', '_MDCL', '_MEDC', '_MILI', '_MILT', '_NAME', '_NAMS',
			'_NLIV', '_NMAR', '_NMR', '_PRMN', '_SEPR', '_TODO', '_UID', '_WEIG', '_YART',
		);
		$facts = array();
		foreach ($tags as $tag) {
			$facts[$tag] = self::getLabel($tag, null);
		}
		uasort($facts, array('WT_I18N', 'strcasecmp'));

		return $facts;
	}

	/**
	 * Translate the value for 1 FILE/2 FORM/3 TYPE
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	public static function getFileFormTypeValue($type) {
		switch (strtolower($type)) {
		case 'audio':
			return WT_I18N::translate('Audio');
		case 'book':
			return WT_I18N::translate('Book');
		case 'card':
			return WT_I18N::translate('Card');
		case 'certificate':
			return WT_I18N::translate('Certificate');
		case 'coat':
			return WT_I18N::translate('Coat of arms');
		case 'document':
			return WT_I18N::translate('Document');
		case 'electronic':
			return WT_I18N::translate('Electronic');
		case 'fiche':
			return WT_I18N::translate('Microfiche');
		case 'film':
			return WT_I18N::translate('Microfilm');
		case 'magazine':
			return WT_I18N::translate('Magazine');
		case 'manuscript':
			return WT_I18N::translate('Manuscript');
		case 'map':
			return WT_I18N::translate('Map');
		case 'newspaper':
			return WT_I18N::translate('Newspaper');
		case 'photo':
			return WT_I18N::translate('Photo');
		case 'tombstone':
			return WT_I18N::translate('Tombstone');
		case 'video':
			return WT_I18N::translate('Video');
		case 'painting':
			return WT_I18N::translate('Painting');
		default:
			return WT_I18N::translate('Other');
		}
	}

	/**
	 * A list of all possible values for 1 FILE/2 FORM/3 TYPE
	 *
	 * @return string[]
	 */
	public static function getFileFormTypes() {
		$values = array();
		foreach (self::$OBJE_FILE_FORM_TYPE as $type) {
			$values[$type] = self::getFileFormTypeValue($type);
		}
		uasort($values, array('WT_I18N', 'strcasecmp'));

		return $values;
	}

	/**
	 * Generate a value for a new _UID field.
	 * Instead of RFC4122-compatible UUIDs, generate ones that
	 * are compatible with PAF, Legacy, RootsMagic, etc.
	 * In these, the string is upper-cased, dashes are removed,
	 * and a two-byte checksum is added.
	 *
	 * @return string
	 */
	public static function createUid() {
		$uid = str_replace('-', '', Uuid::uuid4());

		$checksum_a = 0; // a sum of the bytes
		$checksum_b = 0; // a sum of the incremental values of $checksum_a

		// Compute checksums
		for ($i = 0; $i < 32; $i += 2) {
			$checksum_a += hexdec(substr($uid, $i, 2));
			$checksum_b += $checksum_a & 0xff;
		}

		return strtoupper($uid . substr(dechex($checksum_a), -2) . substr(dechex($checksum_b), -2));
	}
}
