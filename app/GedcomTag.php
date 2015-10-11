<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

use Rhumsaa\Uuid\Uuid;

/**
 * Static GEDCOM data for tags
 */
class GedcomTag {
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
	 * @return bool
	 */
	public static function isTag($tag) {
		return in_array($tag, self::$ALL_TAGS);
	}

	/**
	 * Translate a tag, for an (optional) record
	 *
	 * @param string               $tag
	 * @param GedcomRecord|null $record
	 *
	 * @return string
	 */
	public static function getLabel($tag, GedcomRecord $record = null) {
		if ($record instanceof Individual) {
			$sex = $record->getSex();
		} else {
			$sex = 'U';
		}

		switch ($tag) {
		case 'ABBR':
			return
				/* I18N: gedcom tag ABBR */
				I18N::translate('Abbreviation');
		case 'ADDR':
			return
				/* I18N: gedcom tag ADDR */
				I18N::translate('Address');
		case 'ADR1':
			return I18N::translate('Address line 1');
		case 'ADR2':
			return I18N::translate('Address line 2');
		case 'ADOP':
			return
				/* I18N: gedcom tag ADOP */
				I18N::translate('Adoption');
		case 'ADOP:DATE':
			return I18N::translate('Date of adoption');
		case 'ADOP:PLAC':
			return I18N::translate('Place of adoption');
		case 'AFN':
			return
				/* I18N: gedcom tag AFN */
				I18N::translate('Ancestral file number');
		case 'AGE':
			return
				/* I18N: gedcom tag AGE */
				I18N::translate('Age');
		case 'AGNC':
			return
				/* I18N: gedcom tag AGNC */
				I18N::translate('Agency');
		case 'ALIA':
			return
				/* I18N: gedcom tag ALIA */
				I18N::translate('Alias');
		case 'ANCE':
			return
				/* I18N: gedcom tag ANCE */
				I18N::translate('Generations of ancestors');
		case 'ANCI':
			return
				/* I18N: gedcom tag ANCI */
				I18N::translate('Ancestors interest');
		case 'ANUL':
			return
				/* I18N: gedcom tag ANUL */
				I18N::translate('Annulment');
		case 'ASSO':
			return
				/* I18N: gedcom tag ASSO */
				I18N::translate('Associate'); /* see also _ASSO */
		case 'AUTH':
			return
				/* I18N: gedcom tag AUTH */
				I18N::translate('Author');
		case 'BAPL':
			return
				/* I18N: gedcom tag BAPL */
				I18N::translate('LDS baptism');
		case 'BAPL:DATE':
			return I18N::translate('Date of LDS baptism');
		case 'BAPL:PLAC':
			return I18N::translate('Place of LDS baptism');
		case 'BAPM':
			return
				/* I18N: gedcom tag BAPM */
				I18N::translate('Baptism');
		case 'BAPM:DATE':
			return I18N::translate('Date of baptism');
		case 'BAPM:PLAC':
			return I18N::translate('Place of baptism');
		case 'BARM':
			return
				/* I18N: gedcom tag BARM */
				I18N::translate('Bar mitzvah');
		case 'BARM:DATE':
			return I18N::translate('Date of bar mitzvah');
		case 'BARM:PLAC':
			return I18N::translate('Place of bar mitzvah');
		case 'BASM':
			return
				/* I18N: gedcom tag BASM */
				I18N::translate('Bat mitzvah');
		case 'BASM:DATE':
			return I18N::translate('Date of bat mitzvah');
		case 'BASM:PLAC':
			return I18N::translate('Place of bat mitzvah');
		case 'BIRT':
			return
				/* I18N: gedcom tag BIRT */
				I18N::translate('Birth');
		case 'BIRT:DATE':
			return I18N::translate('Date of birth');
		case 'BIRT:PLAC':
			return I18N::translate('Place of birth');
		case 'BLES':
			return
				/* I18N: gedcom tag BLES */
				I18N::translate('Blessing');
		case 'BLES:DATE':
			return I18N::translate('Date of blessing');
		case 'BLES:PLAC':
			return I18N::translate('Place of blessing');
		case 'BLOB':
			return
				/* I18N: gedcom tag BLOB */
				I18N::translate('Binary data object');
		case 'BURI':
			return
				/* I18N: gedcom tag BURI */
				I18N::translate('Burial');
		case 'BURI:DATE':
			return I18N::translate('Date of burial');
		case 'BURI:PLAC':
			return I18N::translate('Place of burial');
		case 'CALN':
			return
				/* I18N: gedcom tag CALN */
				I18N::translate('Call number');
		case 'CAST':
			return
				/* I18N: gedcom tag CAST */
				I18N::translate('Caste');
		case 'CAUS':
			return
				/* I18N: gedcom tag CAUS */
				I18N::translate('Cause');
		case 'CEME':
			return
				/* I18N: gedcom tag CEME */
				I18N::translate('Cemetery');
		case 'CENS':
			return
				/* I18N: gedcom tag CENS */
				I18N::translate('Census');
		case 'CENS:DATE':
			return I18N::translate('Census date');
		case 'CENS:PLAC':
			return I18N::translate('Census place');
		case 'CHAN':
			return
				/* I18N: gedcom tag CHAN */
				I18N::translate('Last change');
		case 'CHAN:DATE':
			return
				/* I18N: gedcom tag CHAN:DATE */
				I18N::translate('Date of last change');
		case 'CHAN:_WT_USER':
			return
				/* I18N: gedcom tag CHAN:_WT_USER */
				I18N::translate('Author of last change');
		case 'CHAR':
			return
				/* I18N: gedcom tag CHAR */
				I18N::translate('Character set');
		case 'CHIL':
			return
				/* I18N: gedcom tag CHIL */
				I18N::translate('Child');
		case 'CHR':
			return
				/* I18N: gedcom tag CHR */
				I18N::translate('Christening');
		case 'CHR:DATE':
			return I18N::translate('Date of christening');
		case 'CHR:PLAC':
			return I18N::translate('Place of christening');
		case 'CHRA':
			return
				/* I18N: gedcom tag CHRA */
				I18N::translate('Adult christening');
		case 'CITN':
			return
				/* I18N: gedcom tag CITN */
				I18N::translate('Citizenship');
		case 'CITY':
			return
				/* I18N: gedcom tag CITY */
				I18N::translate('City');
		case 'COMM':
			return
				/* I18N: gedcom tag COMM */
				I18N::translate('Comment');
		case 'CONC':
			return
				/* I18N: gedcom tag CONC */
				I18N::translate('Concatenation');
		case 'CONT':
			return
				/* I18N: gedcom tag CONT */
				I18N::translate('Continued');
		case 'CONF':
			return
				/* I18N: gedcom tag CONF */
				I18N::translate('Confirmation');
		case 'CONF:DATE':
			return I18N::translate('Date of confirmation');
		case 'CONF:PLAC':
			return I18N::translate('Place of confirmation');
		case 'CONL':
			return
				/* I18N: gedcom tag CONL */
				I18N::translate('LDS confirmation');
		case 'COPR':
			return
				/* I18N: gedcom tag COPR */
				I18N::translate('Copyright');
		case 'CORP':
			return
				/* I18N: gedcom tag CORP */
				I18N::translate('Corporation');
		case 'CREM':
			return
				/* I18N: gedcom tag CREM */
				I18N::translate('Cremation');
		case 'CREM:DATE':
			return I18N::translate('Date of cremation');
		case 'CREM:PLAC':
			return I18N::translate('Place of cremation');
		case 'CTRY':
			return
				/* I18N: gedcom tag CTRY */
				I18N::translate('Country');
		case 'DATA':
			return
				/* I18N: gedcom tag DATA */
				I18N::translate('Data');
		case 'DATA:DATE':
			return I18N::translate('Date of entry in original source');
		case 'DATE':
			return
				/* I18N: gedcom tag DATE */
				I18N::translate('Date');
		case 'DEAT':
			return
				/* I18N: gedcom tag DEAT */
				I18N::translate('Death');
		case 'DEAT:CAUS':
			return I18N::translate('Cause of death');
		case 'DEAT:DATE':
			return I18N::translate('Date of death');
		case 'DEAT:PLAC':
			return I18N::translate('Place of death');
		case 'DESC':
			return
				/* I18N: gedcom tag DESC */
				I18N::translate('Descendants');
		case 'DESI':
			return
				/* I18N: gedcom tag DESI */
				I18N::translate('Descendants interest');
		case 'DEST':
			return
				/* I18N: gedcom tag DEST */
				I18N::translate('Destination');
		case 'DIV':
			return
				/* I18N: gedcom tag DIV */
				I18N::translate('Divorce');
		case 'DIVF':
			return
				/* I18N: gedcom tag DIVF */
				I18N::translate('Divorce filed');
		case 'DSCR':
			return
				/* I18N: gedcom tag DSCR */
				I18N::translate('Description');
		case 'EDUC':
			return
				/* I18N: gedcom tag EDUC */
				I18N::translate('Education');
		case 'EDUC:AGNC':
			return I18N::translate('School or college');
		case 'EMAI':
			return
				/* I18N: gedcom tag EMAI */
				I18N::translate('Email address');
		case 'EMAIL':
			return
				/* I18N: gedcom tag EMAIL */
				I18N::translate('Email address');
		case 'EMAL':
			return
				/* I18N: gedcom tag EMAL */
				I18N::translate('Email address');
		case 'EMIG':
			return
				/* I18N: gedcom tag EMIG */
				I18N::translate('Emigration');
		case 'EMIG:DATE':
			return I18N::translate('Date of emigration');
		case 'EMIG:PLAC':
			return I18N::translate('Place of emigration');
		case 'ENDL':
			return
				/* I18N: gedcom tag ENDL */
				I18N::translate('LDS endowment');
		case 'ENDL:DATE':
			return I18N::translate('Date of LDS endowment');
		case 'ENDL:PLAC':
			return I18N::translate('Place of LDS endowment');
		case 'ENGA':
			return
				/* I18N: gedcom tag ENGA */
				I18N::translate('Engagement');
		case 'ENGA:DATE':
			return I18N::translate('Date of engagement');
		case 'ENGA:PLAC':
			return I18N::translate('Place of engagement');
		case 'EVEN':
			return
				/* I18N: gedcom tag EVEN */
				I18N::translate('Event');
		case 'EVEN:DATE':
			return I18N::translate('Date of event');
		case 'EVEN:PLAC':
			return I18N::translate('Place of event');
		case 'FACT':
			return
				/* I18N: gedcom tag FACT */
				I18N::translate('Fact');
		case 'FAM':
			return
				/* I18N: gedcom tag FAM */
				I18N::translate('Family');
		case 'FAMC':
			return
				/* I18N: gedcom tag FAMC */
				I18N::translate('Family as a child');
		case 'FAMF':
			return
				/* I18N: gedcom tag FAMF */
				I18N::translate('Family file');
		case 'FAMS':
			return
				/* I18N: gedcom tag FAMS */
				I18N::translate('Family as a spouse');
		case 'FAMS:CENS:DATE':
			return I18N::translate('Spouse census date');
		case 'FAMS:CENS:PLAC':
			return I18N::translate('Spouse census place');
		case 'FAMS:DIV:DATE':
			return I18N::translate('Date of divorce');
		case 'FAMS:MARR:DATE':
			return I18N::translate('Date of marriage');
		case 'FAMS:MARR:PLAC':
			return I18N::translate('Place of marriage');
		case 'FAMS:NOTE':
			return I18N::translate('Spouse note');
		case 'FAMS:SLGS:DATE':
			return I18N::translate('Date of LDS spouse sealing');
		case 'FAMS:SLGS:PLAC':
			return I18N::translate('Place of LDS spouse sealing');
		case 'FAX':
			return
				/* I18N: gedcom tag FAX */
				I18N::translate('Fax');
		case 'FCOM':
			return
				/* I18N: gedcom tag FCOM */
				I18N::translate('First communion');
		case 'FCOM:DATE':
			return I18N::translate('Date of first communion');
		case 'FCOM:PLAC':
			return I18N::translate('Place of first communion');
		case 'FILE':
			return
				/* I18N: gedcom tag FILE */
				I18N::translate('Filename');
		case 'FONE':
			return
				/* I18N: gedcom tag FONE */
				I18N::translate('Phonetic');
		case 'FORM':
			return
				/* I18N: gedcom tag FORM */
				I18N::translate('Format');
		case 'GEDC':
			return
				/* I18N: gedcom tag GEDC */
				I18N::translate('GEDCOM file');
		case 'GIVN':
			return
				/* I18N: gedcom tag GIVN */
				I18N::translate('Given names');
		case 'GRAD':
			return
				/* I18N: gedcom tag GRAD */
				I18N::translate('Graduation');
		case 'HEAD':
			return
				/* I18N: gedcom tag HEAD */
				I18N::translate('Header');
		case 'HUSB':
			return
				/* I18N: gedcom tag HUSB */
				I18N::translate('Husband');
		case 'IDNO':
			return
				/* I18N: gedcom tag IDNO */
				I18N::translate('Identification number');
		case 'IMMI':
			return
				/* I18N: gedcom tag IMMI */
				I18N::translate('Immigration');
		case 'IMMI:DATE':
			return I18N::translate('Date of immigration');
		case 'IMMI:PLAC':
			return I18N::translate('Place of immigration');
		case 'INDI':
			return
				/* I18N: gedcom tag INDI */
				I18N::translate('Individual');
		case 'INFL':
			return
				/* I18N: gedcom tag INFL */
				I18N::translate('Infant');
		case 'LANG':
			return
				/* I18N: gedcom tag LANG */
				I18N::translate('Language');
		case 'LATI':
			return
				/* I18N: gedcom tag LATI */
				I18N::translate('Latitude');
		case 'LEGA':
			return
				/* I18N: gedcom tag LEGA */
				I18N::translate('Legatee');
		case 'LONG':
			return
				/* I18N: gedcom tag LONG */
				I18N::translate('Longitude');
		case 'MAP':
			return
				/* I18N: gedcom tag MAP */
				I18N::translate('Map');
		case 'MARB':
			return
				/* I18N: gedcom tag MARB */
				I18N::translate('Marriage banns');
		case 'MARB:DATE':
			return I18N::translate('Date of marriage banns');
		case 'MARB:PLAC':
			return I18N::translate('Place of marriage banns');
		case 'MARC':
			return
				/* I18N: gedcom tag MARC */
				I18N::translate('Marriage contract');
		case 'MARL':
			return
				/* I18N: gedcom tag MARL */
				I18N::translate('Marriage license');
		case 'MARR':
			return
				/* I18N: gedcom tag MARR */
				I18N::translate('Marriage');
		case 'MARR:DATE':
			return I18N::translate('Date of marriage');
		case 'MARR:PLAC':
			return I18N::translate('Place of marriage');
		case 'MARR_CIVIL':
			return I18N::translate('Civil marriage');
		case 'MARR_PARTNERS':
			return I18N::translate('Registered partnership');
		case 'MARR_RELIGIOUS':
			return I18N::translate('Religious marriage');
		case 'MARR_UNKNOWN':
			return I18N::translate('Marriage type unknown');
		case 'MARS':
			return
				/* I18N: gedcom tag MARS */
				I18N::translate('Marriage settlement');
		case 'MEDI':
			return
				/* I18N: gedcom tag MEDI */
				I18N::translate('Media type');
		case 'NAME':
			if ($record instanceof Repository) {
				return
					/* I18N: gedcom tag REPO:NAME */
					I18N::translateContext('Repository', 'Name');
			} else {
				return
					/* I18N: gedcom tag NAME */
					I18N::translate('Name');
			}
		case 'NAME:FONE':
			return I18N::translate('Phonetic name');
		case 'NAME:_HEB':
			return I18N::translate('Name in Hebrew');
		case 'NATI':
			return
				/* I18N: gedcom tag NATI */
				I18N::translate('Nationality');
		case 'NATU':
			return
				/* I18N: gedcom tag NATU */
				I18N::translate('Naturalization');
		case 'NATU:DATE':
			return I18N::translate('Date of naturalization');
		case 'NATU:PLAC':
			return I18N::translate('Place of naturalization');
		case 'NCHI':
			return
				/* I18N: gedcom tag NCHI */
				I18N::translate('Number of children');
		case 'NICK':
			return
				/* I18N: gedcom tag NICK */
				I18N::translate('Nickname');
		case 'NMR':
			return
				/* I18N: gedcom tag NMR */
				I18N::translate('Number of marriages');
		case 'NOTE':
			return
				/* I18N: gedcom tag NOTE */
				I18N::translate('Note');
		case 'NPFX':
			return
				/* I18N: gedcom tag NPFX */
				I18N::translate('Name prefix');
		case 'NSFX':
			return
				/* I18N: gedcom tag NSFX */
				I18N::translate('Name suffix');
		case 'OBJE':
			return
				/* I18N: gedcom tag OBJE */
				I18N::translate('Media object');
		case 'OCCU':
			return
				/* I18N: gedcom tag OCCU */
				I18N::translate('Occupation');
		case 'OCCU:AGNC':
			return I18N::translate('Employer');
		case 'ORDI':
			return
				/* I18N: gedcom tag ORDI */
				I18N::translate('Ordinance');
		case 'ORDN':
			return
				/* I18N: gedcom tag ORDN */
				I18N::translate('Ordination');
		case 'ORDN:AGNC':
			return I18N::translate('Religious institution');
		case 'ORDN:DATE':
			return I18N::translate('Date of ordination');
		case 'ORDN:PLAC':
			return I18N::translate('Place of ordination');
		case 'PAGE':
			return
				/* I18N: gedcom tag PAGE */
				I18N::translate('Citation details');
		case 'PEDI':
			return
				/* I18N: gedcom tag PEDI */
				I18N::translate('Relationship to parents');
		case 'PHON':
			return
				/* I18N: gedcom tag PHON */
				I18N::translate('Phone');
		case 'PLAC':
			return
				/* I18N: gedcom tag PLAC */
				I18N::translate('Place');
		case 'PLAC:FONE':
			return I18N::translate('Phonetic place');
		case 'PLAC:ROMN':
			return I18N::translate('Romanized place');
		case 'PLAC:_HEB':
			return I18N::translate('Place in Hebrew');
		case 'POST':
			return
				/* I18N: gedcom tag POST */
				I18N::translate('Postal code');
		case 'PROB':
			return
				/* I18N: gedcom tag PROB */
				I18N::translate('Probate');
		case 'PROP':
			return
				/* I18N: gedcom tag PROP */
				I18N::translate('Property');
		case 'PUBL':
			return
				/* I18N: gedcom tag PUBL */
				I18N::translate('Publication');
		case 'QUAY':
			return
				/* I18N: gedcom tag QUAY */
				I18N::translate('Quality of data');
		case 'REFN':
			return
				/* I18N: gedcom tag REFN */
				I18N::translate('Reference number');
		case 'RELA':
			return
				/* I18N: gedcom tag RELA */
				I18N::translate('Relationship');
		case 'RELI':
			return
				/* I18N: gedcom tag RELI */
				I18N::translate('Religion');
		case 'REPO':
			return
				/* I18N: gedcom tag REPO */
				I18N::translate('Repository');
		case 'RESI':
			return
				/* I18N: gedcom tag RESI */
				I18N::translate('Residence');
		case 'RESI:DATE':
			return I18N::translate('Date of residence');
		case 'RESI:PLAC':
			return I18N::translate('Place of residence');
		case 'RESN':
			return
				/* I18N: gedcom tag RESN */
				I18N::translate('Restriction');
		case 'RETI':
			return
				/* I18N: gedcom tag RETI */
				I18N::translate('Retirement');
		case 'RETI:AGNC':
			return I18N::translate('Employer');
		case 'RFN':
			return
				/* I18N: gedcom tag RFN */
				I18N::translate('Record file number');
		case 'RIN':
			return
				/* I18N: gedcom tag RIN */
				I18N::translate('Record ID number');
		case 'ROLE':
			return
				/* I18N: gedcom tag ROLE */
				I18N::translate('Role');
		case 'ROMN':
			return
				/* I18N: gedcom tag ROMN */
				I18N::translate('Romanized');
		case 'SERV':
			return
				/* I18N: gedcom tag SERV */
				I18N::translate('Remote server');
		case 'SEX':
			return
				/* I18N: gedcom tag SEX */
				I18N::translate('Gender');
		case 'SHARED_NOTE':
			return I18N::translate('Shared note');
		case 'SLGC':
			return
				/* I18N: gedcom tag SLGC */
				I18N::translate('LDS child sealing');
		case 'SLGC:DATE':
			return I18N::translate('Date of LDS child sealing');
		case 'SLGC:PLAC':
			return I18N::translate('Place of LDS child sealing');
		case 'SLGS':
			return
				/* I18N: gedcom tag SLGS */
				I18N::translate('LDS spouse sealing');
		case 'SOUR':
			return
				/* I18N: gedcom tag SOUR */
				I18N::translate('Source');
		case 'SPFX':
			return
				/* I18N: gedcom tag SPFX */
				I18N::translate('Surname prefix');
		case 'SSN':
			return
				/* I18N: gedcom tag SSN */
				I18N::translate('Social security number');
		case 'STAE':
			return
				/* I18N: gedcom tag STAE */
				I18N::translate('State');
		case 'STAT':
			return
				/* I18N: gedcom tag STAT */
				I18N::translate('Status');
		case 'STAT:DATE':
			return I18N::translate('Status change date');
		case 'SUBM':
			return
				/* I18N: gedcom tag SUBM */
				I18N::translate('Submitter');
		case 'SUBN':
			return
				/* I18N: gedcom tag SUBN */
				I18N::translate('Submission');
		case 'SURN':
			return
				/* I18N: gedcom tag SURN */
				I18N::translate('Surname');
		case 'TEMP':
			return
				/* I18N: gedcom tag TEMP */
				I18N::translate('Temple');
		case 'TEXT':
			return
				/* I18N: gedcom tag TEXT */
				I18N::translate('Text');
		case 'TIME':
			return
				/* I18N: gedcom tag TIME */
				I18N::translate('Time');
		case 'TITL':
			return
				/* I18N: gedcom tag TITL */
				I18N::translate('Title');
		case 'TITL:FONE':
			return I18N::translate('Phonetic title');
		case 'TITL:ROMN':
			return I18N::translate('Romanized title');
		case 'TITL:_HEB':
			return I18N::translate('Title in Hebrew');
		case 'TRLR':
			return
				/* I18N: gedcom tag TRLR */
				I18N::translate('Trailer');
		case 'TYPE':
			return
				/* I18N: gedcom tag TYPE */
				I18N::translate('Type');
		case 'URL':
			return
				/* I18N: gedcom tag URL (A web address / URL) */
				I18N::translate('URL');
		case 'VERS':
			return
				/* I18N: gedcom tag VERS */
				I18N::translate('Version');
		case 'WIFE':
			return
				/* I18N: gedcom tag WIFE */
				I18N::translate('Wife');
		case 'WILL':
			return
				/* I18N: gedcom tag WILL */
				I18N::translate('Will');
		case 'WWW':
			return
				/* I18N: gedcom tag WWW (A web address / URL) */
				I18N::translate('URL');
		case '_ADOP_CHIL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Adoption of a son');
			case 'F':
				return I18N::translate('Adoption of a daughter');
			default:
				return I18N::translate('Adoption of a child');
			}
		case '_ADOP_GCHI':
			switch ($sex) {
			case 'M':
				return I18N::translate('Adoption of a grandson');
			case 'F':
				return I18N::translate('Adoption of a granddaughter');
			default:
				return I18N::translate('Adoption of a grandchild');
			}
		case '_ADOP_GCH1':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('daughter’s son', 'Adoption of a grandson');
			case 'F':
				return I18N::translateContext('daughter’s daughter', 'Adoption of a granddaughter');
			default:
				return I18N::translate('Adoption of a grandchild');
			}
		case '_ADOP_GCH2':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('son’s son', 'Adoption of a grandson');
			case 'F':
				return I18N::translateContext('son’s daughter', 'Adoption of a granddaughter');
			default:
				return I18N::translate('Adoption of a grandchild');
			}
		case '_ADOP_HSIB':
			switch ($sex) {
			case 'M':
				return I18N::translate('Adoption of a half-brother');
			case 'F':
				return I18N::translate('Adoption of a half-sister');
			default:
				return I18N::translate('Adoption of a half-sibling');
			}
		case '_ADOP_SIBL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Adoption of a brother');
			case 'F':
				return I18N::translate('Adoption of a sister');
			default:
				return I18N::translate('Adoption of a sibling');
			}
		case '_ADPF':
			switch ($sex) {
			case 'M':
				return
					/* I18N: gedcom tag _ADPF */
					I18N::translateContext('MALE', 'Adopted by father');
			case 'F':
				return
					/* I18N: gedcom tag _ADPF */
					I18N::translateContext('FEMALE', 'Adopted by father');
			default:
				return
					/* I18N: gedcom tag _ADPF */
					I18N::translate('Adopted by father');
			}
		case '_ADPM':
			switch ($sex) {
			case 'M':
				return
					/* I18N: gedcom tag _ADPM */
					I18N::translateContext('MALE', 'Adopted by mother');
			case 'F':
				return
					/* I18N: gedcom tag _ADPM */
					I18N::translateContext('FEMALE', 'Adopted by mother');
			default:
				return
					/* I18N: gedcom tag _ADPM */
					I18N::translate('Adopted by mother');
			}
		case '_AKA':
		case '_AKAN':
			switch ($sex) {
			case 'M':
				return
					/* I18N: gedcom tag _AKA */
					I18N::translateContext('MALE', 'Also known as');
			case 'F':
				return
					/* I18N: gedcom tag _AKA */
					I18N::translateContext('FEMALE', 'Also known as');
			default:
				return
					/* I18N: gedcom tag _AKA */
					I18N::translate('Also known as');
			}
		case '_ASSO':
			return
				/* I18N: gedcom tag _ASSO */
				I18N::translate('Associate'); /* see also ASSO */
		case '_BAPM_CHIL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Baptism of a son');
			case 'F':
				return I18N::translate('Baptism of a daughter');
			default:
				return I18N::translate('Baptism of a child');
			}
		case '_BAPM_GCHI':
			switch ($sex) {
			case 'M':
				return I18N::translate('Baptism of a grandson');
			case 'F':
				return I18N::translate('Baptism of a granddaughter');
			default:
				return I18N::translate('Baptism of a grandchild');
			}
		case '_BAPM_GCH1':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('daughter’s son', 'Baptism of a grandson');
			case 'F':
				return I18N::translateContext('daughter’s daughter', 'Baptism of a granddaughter');
			default:
				return I18N::translate('Baptism of a grandchild');
			}
		case '_BAPM_GCH2':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('son’s son', 'Baptism of a grandson');
			case 'F':
				return I18N::translateContext('son’s daughter', 'Baptism of a granddaughter');
			default:
				return I18N::translate('Baptism of a grandchild');
			}
		case '_BAPM_HSIB':
			switch ($sex) {
			case 'M':
				return I18N::translate('Baptism of a half-brother');
			case 'F':
				return I18N::translate('Baptism of a half-sister');
			default:
				return I18N::translate('Baptism of a half-sibling');
			}
		case '_BAPM_SIBL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Baptism of a brother');
			case 'F':
				return I18N::translate('Baptism of a sister');
			default:
				return I18N::translate('Baptism of a sibling');
			}
		case '_BIBL':
			return
				/* I18N: gedcom tag _BIBL */
				I18N::translate('Bibliography');
		case '_BIRT_CHIL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Birth of a son');
			case 'F':
				return I18N::translate('Birth of a daughter');
			default:
				return I18N::translate('Birth of a child');
			}
		case '_BIRT_GCHI':
			switch ($sex) {
			case 'M':
				return I18N::translate('Birth of a grandson');
			case 'F':
				return I18N::translate('Birth of a granddaughter');
			default:
				return I18N::translate('Birth of a grandchild');
			}
		case '_BIRT_GCH1':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('daughter’s son', 'Birth of a grandson');
			case 'F':
				return I18N::translateContext('daughter’s daughter', 'Birth of a granddaughter');
			default:
				return I18N::translate('Birth of a grandchild');
			}
		case '_BIRT_GCH2':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('son’s son', 'Birth of a grandson');
			case 'F':
				return I18N::translateContext('son’s daughter', 'Birth of a granddaughter');
			default:
				return I18N::translate('Birth of a grandchild');
			}
		case '_BIRT_HSIB':
			switch ($sex) {
			case 'M':
				return I18N::translate('Birth of a half-brother');
			case 'F':
				return I18N::translate('Birth of a half-sister');
			default:
				return I18N::translate('Birth of a half-sibling');
			}
		case '_BIRT_SIBL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Birth of a brother');
			case 'F':
				return I18N::translate('Birth of a sister');
			default:
				return I18N::translate('Birth of a sibling');
			}
		case '_BRTM':
			return
				/* I18N: gedcom tag _BRTM */
				I18N::translate('Brit milah');
		case '_BRTM:DATE':
			return I18N::translate('Date of brit milah');
		case '_BRTM:PLAC':
			return I18N::translate('Place of brit milah');
		case '_BURI_CHIL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Burial of a son');
			case 'F':
				return I18N::translate('Burial of a daughter');
			default:
				return I18N::translate('Burial of a child');
			}
		case '_BURI_GCHI':
			switch ($sex) {
			case 'M':
				return I18N::translate('Burial of a grandson');
			case 'F':
				return I18N::translate('Burial of a granddaughter');
			default:
				return I18N::translate('Burial of a grandchild');
			}
		case '_BURI_GCH1':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('daughter’s son', 'Burial of a grandson');
			case 'F':
				return I18N::translateContext('daughter’s daughter', 'Burial of a granddaughter');
			default:
				return I18N::translate('Burial of a grandchild');
			}
		case '_BURI_GCH2':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('son’s son', 'Burial of a grandson');
			case 'F':
				return I18N::translateContext('son’s daughter', 'Burial of a granddaughter');
			default:
				return I18N::translate('Burial of a grandchild');
			}
		case '_BURI_GPAR':
			switch ($sex) {
			case 'M':
				return I18N::translate('Burial of a grandfather');
			case 'F':
				return I18N::translate('Burial of a grandmother');
			default:
				return I18N::translate('Burial of a grandparent');
			}
		case '_BURI_GPA1':
			switch ($sex) {
			case 'M':
				return I18N::translate('Burial of a paternal grandfather');
			case 'F':
				return I18N::translate('Burial of a paternal grandmother');
			default:
				return I18N::translate('Burial of a paternal grandparent');
			}
		case '_BURI_GPA2':
			switch ($sex) {
			case 'M':
				return I18N::translate('Burial of a maternal grandfather');
			case 'F':
				return I18N::translate('Burial of a maternal grandmother');
			default:
				return I18N::translate('Burial of a maternal grandparent');
			}
		case '_BURI_HSIB':
			switch ($sex) {
			case 'M':
				return I18N::translate('Burial of a half-brother');
			case 'F':
				return I18N::translate('Burial of a half-sister');
			default:
				return I18N::translate('Burial of a half-sibling');
			}
		case '_BURI_PARE':
			switch ($sex) {
			case 'M':
				return I18N::translate('Burial of a father');
			case 'F':
				return I18N::translate('Burial of a mother');
			default:
				return I18N::translate('Burial of a parent');
			}
		case '_BURI_SIBL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Burial of a brother');
			case 'F':
				return I18N::translate('Burial of a sister');
			default:
				return I18N::translate('Burial of a sibling');
			}
		case '_BURI_SPOU':
			switch ($sex) {
			case 'M':
				return I18N::translate('Burial of a husband');
			case 'F':
				return I18N::translate('Burial of a wife');
			default:
				return I18N::translate('Burial of a spouse');
			}
		case '_CHR_CHIL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Christening of a son');
			case 'F':
				return I18N::translate('Christening of a daughter');
			default:
				return I18N::translate('Christening of a child');
			}
		case '_CHR_GCHI':
			switch ($sex) {
			case 'M':
				return I18N::translate('Christening of a grandson');
			case 'F':
				return I18N::translate('Christening of a granddaughter');
			default:
				return I18N::translate('Christening of a grandchild');
			}
		case '_CHR_GCH1':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('daughter’s son', 'Christening of a grandson');
			case 'F':
				return I18N::translateContext('daughter’s daughter', 'Christening of a granddaughter');
			default:
				return I18N::translate('Christening of a grandchild');
			}
		case '_CHR_GCH2':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('son’s son', 'Christening of a grandson');
			case 'F':
				return I18N::translateContext('son’s daughter', 'Christening of a granddaughter');
			default:
				return I18N::translate('Christening of a grandchild');
			}
		case '_CHR_HSIB':
			switch ($sex) {
			case 'M':
				return I18N::translate('Christening of a half-brother');
			case 'F':
				return I18N::translate('Christening of a half-sister');
			default:
				return I18N::translate('Christening of a half-sibling');
			}
		case '_CHR_SIBL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Christening of a brother');
			case 'F':
				return I18N::translate('Christening of a sister');
			default:
				return I18N::translate('Christening of a sibling');
			}
		case '_COML':
			return
				/* I18N: gedcom tag _COML */
				I18N::translate('Common law marriage');
		case '_CREM_CHIL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Cremation of a son');
			case 'F':
				return I18N::translate('Cremation of a daughter');
			default:
				return I18N::translate('Cremation of a child');
			}
		case '_CREM_GCHI':
			switch ($sex) {
			case 'M':
				return I18N::translate('Cremation of a grandson');
			case 'F':
				return I18N::translate('Cremation of a granddaughter');
			default:
				return I18N::translate('Cremation of a grandchild');
			}
		case '_CREM_GCH1':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('daughter’s son', 'Cremation of a grandson');
			case 'F':
				return I18N::translateContext('daughter’s daughter', 'Cremation of a granddaughter');
			default:
				return I18N::translate('Cremation of a grandchild');
			}
		case '_CREM_GCH2':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('son’s son', 'Cremation of a grandson');
			case 'F':
				return I18N::translateContext('son’s daughter', 'Cremation of a granddaughter');
			default:
				return I18N::translate('Cremation of a grandchild');
			}
		case '_CREM_GPAR':
			switch ($sex) {
			case 'M':
				return I18N::translate('Cremation of a grandfather');
			case 'F':
				return I18N::translate('Cremation of a grandmother');
			default:
				return I18N::translate('Cremation of a grand-parent');
			}
		case '_CREM_GPA1':
			switch ($sex) {
			case 'M':
				return I18N::translate('Cremation of a paternal grandfather');
			case 'F':
				return I18N::translate('Cremation of a paternal grandmother');
			default:
				return I18N::translate('Cremation of a grand-parent');
			}
		case '_CREM_GPA2':
			switch ($sex) {
			case 'M':
				return I18N::translate('Cremation of a maternal grandfather');
			case 'F':
				return I18N::translate('Cremation of a maternal grandmother');
			default:
				return I18N::translate('Cremation of a grand-parent');
			}
		case '_CREM_HSIB':
			switch ($sex) {
			case 'M':
				return I18N::translate('Cremation of a half-brother');
			case 'F':
				return I18N::translate('Cremation of a half-sister');
			default:
				return I18N::translate('Cremation of a half-sibling');
			}
		case '_CREM_PARE':
			switch ($sex) {
			case 'M':
				return I18N::translate('Cremation of a father');
			case 'F':
				return I18N::translate('Cremation of a mother');
			default:
				return I18N::translate('Cremation of a parent');
			}
		case '_CREM_SIBL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Cremation of a brother');
			case 'F':
				return I18N::translate('Cremation of a sister');
			default:
				return I18N::translate('Cremation of a sibling');
			}
		case '_CREM_SPOU':
			switch ($sex) {
			case 'M':
				return I18N::translate('Cremation of a husband');
			case 'F':
				return I18N::translate('Cremation of a wife');
			default:
				return I18N::translate('Cremation of a spouse');
			}
		case '_DBID':
			return
				/* I18N: gedcom tag _DBID */
				I18N::translate('Linked database ID');
		case '_DEAT_CHIL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Death of a son');
			case 'F':
				return I18N::translate('Death of a daughter');
			default:
				return I18N::translate('Death of a child');
			}
		case '_DEAT_GCHI':
			switch ($sex) {
			case 'M':
				return I18N::translate('Death of a grandson');
			case 'F':
				return I18N::translate('Death of a granddaughter');
			default:
				return I18N::translate('Death of a grandchild');
			}
		case '_DEAT_GCH1':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('daughter’s son', 'Death of a grandson');
			case 'F':
				return I18N::translateContext('daughter’s daughter', 'Death of a granddaughter');
			default:
				return I18N::translate('Death of a grandchild');
			}
		case '_DEAT_GCH2':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('son’s son', 'Death of a grandson');
			case 'F':
				return I18N::translateContext('son’s daughter', 'Death of a granddaughter');
			default:
				return I18N::translate('Death of a grandchild');
			}
		case '_DEAT_GPAR':
			switch ($sex) {
			case 'M':
				return I18N::translate('Death of a grandfather');
			case 'F':
				return I18N::translate('Death of a grandmother');
			default:
				return I18N::translate('Death of a grand-parent');
			}
		case '_DEAT_GPA1':
			switch ($sex) {
			case 'M':
				return I18N::translate('Death of a paternal grandfather');
			case 'F':
				return I18N::translate('Death of a paternal grandmother');
			default:
				return I18N::translate('Death of a grand-parent');
			}
		case '_DEAT_GPA2':
			switch ($sex) {
			case 'M':
				return I18N::translate('Death of a maternal grandfather');
			case 'F':
				return I18N::translate('Death of a maternal grandmother');
			default:
				return I18N::translate('Death of a grand-parent');
			}
		case '_DEAT_HSIB':
			switch ($sex) {
			case 'M':
				return I18N::translate('Death of a half-brother');
			case 'F':
				return I18N::translate('Death of a half-sister');
			default:
				return I18N::translate('Death of a half-sibling');
			}
		case '_DEAT_PARE':
			switch ($sex) {
			case 'M':
				return I18N::translate('Death of a father');
			case 'F':
				return I18N::translate('Death of a mother');
			default:
				return I18N::translate('Death of a parent');
			}
		case '_DEAT_SIBL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Death of a brother');
			case 'F':
				return I18N::translate('Death of a sister');
			default:
				return I18N::translate('Death of a sibling');
			}
		case '_DEAT_SPOU':
			switch ($sex) {
			case 'M':
				return I18N::translate('Death of a husband');
			case 'F':
				return I18N::translate('Death of a wife');
			default:
				return I18N::translate('Death of a spouse');
			}
		case '_DEG':
			return
				/* I18N: gedcom tag _DEG */
				I18N::translate('Degree');
		case '_DETS':
			return
				/* I18N: gedcom tag _DETS */
				I18N::translate('Death of one spouse');
		case '_DNA':
			return
				/* I18N: gedcom tag _DNA (from FTM 2010) */
				I18N::translate('DNA markers');
		case '_EMAIL':
			return
				/* I18N: gedcom tag _EMAIL */
				I18N::translate('Email address');
		case '_EYEC':
			return
				/* I18N: gedcom tag _EYEC */
				I18N::translate('Eye color');
		case '_FA1':
			return I18N::translate('Fact 1');
		case '_FA2':
			return I18N::translate('Fact 2');
		case '_FA3':
			return I18N::translate('Fact 3');
		case '_FA4':
			return I18N::translate('Fact 4');
		case '_FA5':
			return I18N::translate('Fact 5');
		case '_FA6':
			return I18N::translate('Fact 6');
		case '_FA7':
			return I18N::translate('Fact 7');
		case '_FA8':
			return I18N::translate('Fact 8');
		case '_FA9':
			return I18N::translate('Fact 9');
		case '_FA10':
			return I18N::translate('Fact 10');
		case '_FA11':
			return I18N::translate('Fact 11');
		case '_FA12':
			return I18N::translate('Fact 12');
		case '_FA13':
			return I18N::translate('Fact 13');
		case '_FNRL':
			return
				/* I18N: gedcom tag _FNRL */
				I18N::translate('Funeral');
		case '_FREL':
			return
				/* I18N: gedcom tag _FREL */
				I18N::translate('Relationship to father');
		case '_GEDF':
			return
				/* I18N: gedcom tag _GEDF */
				I18N::translate('GEDCOM file');
		case '_GODP':
			return
				/* I18N: gedcom tag _GODP */
				I18N::translate('Godparent');
		case '_HAIR':
			return
				/* I18N: gedcom tag _HAIR */
				I18N::translate('Hair color');
		case '_HEB':
			return
				/* I18N: gedcom tag _HEB */
				I18N::translate('Hebrew');
		case '_HEIG':
			return
				/* I18N: gedcom tag _HEIG */
				I18N::translate('Height');
		case '_HNM':
			return
				/* I18N: gedcom tag _HNM */
				I18N::translate('Hebrew name');
		case '_HOL':
			return
				/* I18N: gedcom tag _HOL */
				I18N::translate('Holocaust');
		case '_INTE':
			switch ($sex) {
			case 'M':
				return
					/* I18N: gedcom tag _INTE */
					I18N::translateContext('MALE', 'Interred');
			case 'F':
				return
					/* I18N: gedcom tag _INTE */
					I18N::translateContext('FEMALE', 'Interred');
			default:
				return
					/* I18N: gedcom tag _INTE */
					I18N::translate('Interred');
			}
		case '_LOC':
			return
				/* I18N: gedcom tag _LOC */
				I18N::translate('Location');
		case '_MARI':
			return
				/* I18N: gedcom tag _MARI */
				I18N::translate('Marriage intention');
		case '_MARNM':
			return
				/* I18N: gedcom tag _MARNM */
				I18N::translate('Married name');
		case '_PRIM':
			return
				/* I18N: gedcom tag _PRIM */
				I18N::translate('Highlighted image');
		case '_MARNM_SURN':
			return I18N::translate('Married surname');
		case '_MARR_CHIL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Marriage of a son');
			case 'F':
				return I18N::translate('Marriage of a daughter');
			default:
				return I18N::translate('Marriage of a child');
			}
		case '_MARR_FAMC':
			return
				/* I18N: ...to each other */
				I18N::translate('Marriage of parents');
		case '_MARR_GCHI':
			switch ($sex) {
			case 'M':
				return I18N::translate('Marriage of a grandson');
			case 'F':
				return I18N::translate('Marriage of a granddaughter');
			default:
				return I18N::translate('Marriage of a grandchild');
			}
		case '_MARR_GCH1':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('daughter’s son', 'Marriage of a grandson');
			case 'F':
				return I18N::translateContext('daughter’s daughter', 'Marriage of a granddaughter');
			default:
				return I18N::translate('Marriage of a grandchild');
			}
		case '_MARR_GCH2':
			switch ($sex) {
			case 'M':
				return I18N::translateContext('son’s son', 'Marriage of a grandson');
			case 'F':
				return I18N::translateContext('son’s daughter', 'Marriage of a granddaughter');
			default:
				return I18N::translate('Marriage of a grandchild');
			}
		case '_MARR_HSIB':
			switch ($sex) {
			case 'M':
				return I18N::translate('Marriage of a half-brother');
			case 'F':
				return I18N::translate('Marriage of a half-sister');
			default:
				return I18N::translate('Marriage of a half-sibling');
			}
		case '_MARR_PARE':
			switch ($sex) {
			case 'M':
				return
					/* I18N: ...to another spouse */
					I18N::translate('Marriage of a father');
			case 'F':
				return
					/* I18N: ...to another spouse */
					I18N::translate('Marriage of a mother');
			default:
				return
					/* I18N: ...to another spouse */
					I18N::translate('Marriage of a parent');
			}
		case '_MARR_SIBL':
			switch ($sex) {
			case 'M':
				return I18N::translate('Marriage of a brother');
			case 'F':
				return I18N::translate('Marriage of a sister');
			default:
				return I18N::translate('Marriage of a sibling');
			}
		case '_MBON':
			return
				/* I18N: gedcom tag _MBON  */
				I18N::translate('Marriage bond');
		case '_MDCL':
			return
				/* I18N: gedcom tag _MDCL  */
				I18N::translate('Medical');
		case '_MEDC':
			return
				/* I18N: gedcom tag _MEDC  */
				I18N::translate('Medical condition');
		case '_MEND':
			return
				/* I18N: gedcom tag _MEND  */
				I18N::translate('Marriage ending status');
		case '_MILI':
			return
				/* I18N: gedcom tag _MILI  */
				I18N::translate('Military');
		case '_MILT':
			return
				/* I18N: gedcom tag _MILT  */
				I18N::translate('Military service');
		case '_MREL':
			return
				/* I18N: gedcom tag _MREL  */
				I18N::translate('Relationship to mother');
		case '_MSTAT':
			return
				/* I18N: gedcom tag _MSTAT */
				I18N::translate('Marriage beginning status');
		case '_NAME':
			return
				/* I18N: gedcom tag _NAME  */
				I18N::translate('Mailing name');
		case '_NAMS':
			return
				/* I18N: gedcom tag _NAMS  */
				I18N::translate('Namesake');
		case '_NLIV':
			return
				/* I18N: gedcom tag _NLIV  */
				I18N::translate('Not living');
		case '_NMAR':
			switch ($sex) {
			case 'M':
				return
					/* I18N: gedcom tag _NMAR */
					I18N::translateContext('MALE', 'Never married');
			case 'F':
				return
					/* I18N: gedcom tag _NMAR */
					I18N::translateContext('FEMALE', 'Never married');
			default:
				return
					/* I18N: gedcom tag _NMAR */
					I18N::translate('Never married');
			}
		case '_NMR':
			switch ($sex) {
			case 'M':
				return
					/* I18N: gedcom tag _NMR */
					I18N::translateContext('MALE', 'Not married');
			case 'F':
				return
					/* I18N: gedcom tag _NMR */
					I18N::translateContext('FEMALE', 'Not married');
			default:
				return
					/* I18N: gedcom tag _NMR */
					I18N::translate('Not married');
			}
		case '_WT_USER':
			return I18N::translate('by');
		case '_PRMN':
			return
				/* I18N: gedcom tag _PRMN  */
				I18N::translate('Permanent number');
		case '_SCBK':
			return
				/* I18N: gedcom tag _SCBK  */
				I18N::translate('Scrapbook');
		case '_SEPR':
			return
				/* I18N: gedcom tag _SEPR  */
				I18N::translate('Separated');
		case '_SSHOW':
			return
				/* I18N: gedcom tag _SSHOW */
				I18N::translate('Slide show');
		case '_STAT':
			return
				/* I18N: gedcom tag _STAT  */
				I18N::translate('Marriage status');
		case '_SUBQ':
			return
				/* I18N: gedcom tag _SUBQ  */
				I18N::translate('Short version');
		case '_TODO':
			return
				/* I18N: gedcom tag _TODO  */
				I18N::translate('Research task');
		case '_TYPE':
			return
				/* I18N: gedcom tag _TYPE  */
				I18N::translate('Media type');
		case '_UID':
			return
				/* I18N: gedcom tag _UID   */
				I18N::translate('Globally unique identifier');
		case '_URL':
			return
				/* I18N: gedcom tag _URL   */
				I18N::translate('URL');
		case '_WEIG':
			return
				/* I18N: gedcom tag _WEIG  */
				I18N::translate('Weight');
		case '_WITN':
			return
				/* I18N: gedcom tag _WITN  */
				I18N::translate('Witness');
		case '_WT_OBJE_SORT':
			return
				/* I18N: gedcom tag _WT_OBJE_SORT  */
				I18N::translate('Re-order media');
		case '_YART':
			return
				/* I18N: gedcom tag _YART  */
				I18N::translate('Yahrzeit');
			// Brit milah applies only to males, no need for male/female translations
		case '__BRTM_CHIL':
			return I18N::translate('Brit milah of a son');
		case '__BRTM_GCHI':
			return I18N::translate('Brit milah of a grandson');
		case '__BRTM_GCH1':
			return I18N::translateContext('daughter’s son', 'Brit milah of a grandson');
		case '__BRTM_GCH2':
			return I18N::translateContext('son’s son', 'Brit milah of a grandson');
		case '__BRTM_HSIB':
			return I18N::translate('Brit milah of a half-brother');
		case '__BRTM_SIBL':
			return I18N::translate('Brit milah of a brother');
			// These "pseudo" tags are generated internally to present information about a media object
		case '__FILE_SIZE__':
			return I18N::translate('File size');
		case '__IMAGE_SIZE__':
			return I18N::translate('Image dimensions');
		default:
			// If no specialisation exists (e.g. DEAT:CAUS), then look for the general (CAUS)
			if (strpos($tag, ':')) {
				list(, $tag) = explode(':', $tag, 2);

				return self::getLabel($tag, $record);
			}

			// Still no translation? Highlight this as an error
			return '<span class="error" title="' . I18N::translate('Unrecognized GEDCOM code') . '">' . Filter::escapeHtml($tag) . '</span>';
		}
	}

	/**
	 * Translate a label/value pair, such as “Occupation: Farmer”
	 *
	 * @param string            $tag
	 * @param string            $value
	 * @param GedcomRecord|null $record
	 * @param string|null       $element
	 *
	 * @return string
	 */
	public static function getLabelValue($tag, $value, GedcomRecord $record = null, $element = 'div') {
		return
			'<' . $element . ' class="fact_' . $tag . '">' .
			/* I18N: a label/value pair, such as “Occupation: Farmer”.  Some languages may need to change the punctuation. */
			I18N::translate('<span class="label">%1$s:</span> <span class="field" dir="auto">%2$s</span>', self::getLabel($tag, $record), $value) .
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
		uasort($facts, '\Fisharebest\Webtrees\I18N::strcasecmp');

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
			return I18N::translate('Audio');
		case 'book':
			return I18N::translate('Book');
		case 'card':
			return I18N::translate('Card');
		case 'certificate':
			return I18N::translate('Certificate');
		case 'coat':
			return I18N::translate('Coat of arms');
		case 'document':
			return I18N::translate('Document');
		case 'electronic':
			return I18N::translate('Electronic');
		case 'fiche':
			return I18N::translate('Microfiche');
		case 'film':
			return I18N::translate('Microfilm');
		case 'magazine':
			return I18N::translate('Magazine');
		case 'manuscript':
			return I18N::translate('Manuscript');
		case 'map':
			return I18N::translate('Map');
		case 'newspaper':
			return I18N::translate('Newspaper');
		case 'photo':
			return I18N::translate('Photo');
		case 'tombstone':
			return I18N::translate('Tombstone');
		case 'video':
			return I18N::translate('Video');
		case 'painting':
			return I18N::translate('Painting');
		default:
			return I18N::translate('Other');
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
		uasort($values, '\Fisharebest\Webtrees\I18N::strcasecmp');

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
