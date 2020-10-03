<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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

declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Ramsey\Uuid\Uuid;

use function str_contains;

/**
 * Static GEDCOM data for tags
 */
class GedcomTag
{
    /** All tags that webtrees knows how to translate - including special/internal tags */
    private const ALL_TAGS = [
        'ABBR',
        'ADDR',
        'ADR1',
        'ADR2',
        'ADOP',
        'ADOP:DATE',
        'ADOP:PLAC',
        'AFN',
        'AGE',
        'AGNC',
        'ALIA',
        'ANCE',
        'ANCI',
        'ANUL',
        'ASSO',
        'AUTH',
        'BAPL',
        'BAPL:DATE',
        'BAPL:PLAC',
        'BAPM',
        'BAPM:DATE',
        'BAPM:PLAC',
        'BARM',
        'BARM:DATE',
        'BARM:PLAC',
        'BASM',
        'BASM:DATE',
        'BASM:PLAC',
        'BIRT',
        'BIRT:DATE',
        'BIRT:PLAC',
        'BLES',
        'BLES:DATE',
        'BLES:PLAC',
        'BLOB',
        'BURI',
        'BURI:DATE',
        'BURI:PLAC',
        'CALN',
        'CAST',
        'CAUS',
        'CEME',
        'CENS',
        'CENS:DATE',
        'CENS:PLAC',
        'CHAN',
        'CHAN:DATE',
        'CHAN:_WT_USER',
        'CHAR',
        'CHIL',
        'CHR',
        'CHR:DATE',
        'CHR:PLAC',
        'CHRA',
        'CITN',
        'CITY',
        'COMM',
        'CONC',
        'CONT',
        'CONF',
        'CONF:DATE',
        'CONF:PLAC',
        'CONL',
        'COPR',
        'CORP',
        'CREM',
        'CREM:DATE',
        'CREM:PLAC',
        'CTRY',
        'DATA',
        'DATA:DATE',
        'DATE',
        'DEAT',
        'DEAT:CAUS',
        'DEAT:DATE',
        'DEAT:PLAC',
        'DESC',
        'DESI',
        'DEST',
        'DIV',
        'DIVF',
        'DSCR',
        'EDUC',
        'EDUC:AGNC',
        'EMAI',
        'EMAIL',
        'EMAL',
        'EMIG',
        'EMIG:DATE',
        'EMIG:PLAC',
        'ENDL',
        'ENDL:DATE',
        'ENDL:PLAC',
        'ENGA',
        'ENGA:DATE',
        'ENGA:PLAC',
        'EVEN',
        'EVEN:DATE',
        'EVEN:PLAC',
        'EVEN:TYPE',
        'FACT',
        'FACT:TYPE',
        'FAM',
        'FAMC',
        'FAMF',
        'FAMS',
        'FAX',
        'FCOM',
        'FCOM:DATE',
        'FCOM:PLAC',
        'FILE',
        'FONE',
        'FORM',
        'GEDC',
        'GIVN',
        'GRAD',
        'HEAD',
        'HUSB',
        'IDNO',
        'IMMI',
        'IMMI:DATE',
        'IMMI:PLAC',
        'INDI',
        'INFL',
        'LANG',
        'LATI',
        'LEGA',
        'LONG',
        'MAP',
        'MARB',
        'MARB:DATE',
        'MARB:PLAC',
        'MARR_CIVIL',
        'MARR_PARTNERS',
        'MARR_RELIGIOUS',
        'MARR_UNKNOWN',
        'MARC',
        'MARL',
        'MARR',
        'MARR:DATE',
        'MARR:PLAC',
        'MARS',
        'MEDI',
        'NAME',
        'NAME:FONE',
        'NAME:_HEB',
        'NATI',
        'NATU',
        'NATU:DATE',
        'NATU:PLAC',
        'NCHI',
        'NICK',
        'NMR',
        'NOTE',
        'NPFX',
        'NSFX',
        'OBJE',
        'OCCU',
        'OCCU:AGNC',
        'ORDI',
        'ORDN',
        'ORDN:AGNC',
        'ORDN:DATE',
        'ORDN:PLAC',
        'PAGE',
        'PEDI',
        'PHON',
        'PLAC',
        'PLAC:FONE',
        'PLAC:ROMN',
        'PLAC:_HEB',
        'POST',
        'PROB',
        'PROP',
        'PUBL',
        'QUAY',
        'REFN',
        'RELA',
        'RELI',
        'REPO',
        'RESI',
        'RESI:DATE',
        'RESI:PLAC',
        'RESN',
        'RETI',
        'RETI:AGNC',
        'RFN',
        'RIN',
        'ROLE',
        'ROMN',
        'SERV',
        'SEX',
        'SHARED_NOTE',
        'SLGC',
        'SLGC:DATE',
        'SLGC:PLAC',
        'SLGS',
        'SLGS:DATE',
        'SLGS:PLAC',
        'SOUR',
        'SPFX',
        'SSN',
        'STAE',
        'STAT',
        'STAT:DATE',
        'SUBM',
        'SUBN',
        'SURN',
        'TEMP',
        'TEXT',
        'TIME',
        'TITL',
        'TITL:FONE',
        'TITL:ROMN',
        'TITL:_HEB',
        'TRLR',
        'TYPE',
        'URL',
        'VERS',
        'WIFE',
        'WILL',
        'WWW',
        '_ADOP_CHIL',
        '_ADOP_GCHI',
        '_ADOP_GCH1',
        '_ADOP_GCH2',
        '_ADOP_HSIB',
        '_ADOP_SIBL',
        '_ADPF',
        '_ADPM',
        '_AKA',
        '_AKAN',
        '_ASSO',
        '_BAPM_CHIL',
        '_BAPM_GCHI',
        '_BAPM_GCH1',
        '_BAPM_GCH2',
        '_BAPM_HSIB',
        '_BAPM_SIBL',
        '_BIBL',
        '_BIRT_CHIL',
        '_BIRT_GCHI',
        '_BIRT_GCH1',
        '_BIRT_GCH2',
        '_BIRT_HSIB',
        '_BIRT_SIBL',
        '_BRTM',
        '_BRTM:DATE',
        '_BRTM:PLAC',
        '_BURI_CHIL',
        '_BURI_GCHI',
        '_BURI_GCH1',
        '_BURI_GCH2',
        '_BURI_GPAR',
        '_BURI_HSIB',
        '_BURI_SIBL',
        '_BURI_SPOU',
        '_CHR_CHIL',
        '_CHR_GCHI',
        '_CHR_GCH1',
        '_CHR_GCH2',
        '_CHR_HSIB',
        '_CHR_SIBL',
        '_COML',
        '_CREM_CHIL',
        '_CREM_GCHI',
        '_CREM_GCH1',
        '_CREM_GCH2',
        '_CREM_GPAR',
        '_CREM_HSIB',
        '_CREM_SIBL',
        '_CREM_SPOU',
        '_DATE',
        '_DBID',
        '_DEAT_CHIL',
        '_DEAT_GCHI',
        '_DEAT_GCH1',
        '_DEAT_GCH2',
        '_DEAT_GPAR',
        '_DEAT_GPA1',
        '_DEAT_GPA2',
        '_DEAT_HSIB',
        '_DEAT_PARE',
        '_DEAT_SIBL',
        '_DEAT_SPOU',
        '_DEG',
        '_DETS',
        '_DNA',
        '_EMAIL',
        '_EYEC',
        '_FA1',
        '_FA2',
        '_FA3',
        '_FA4',
        '_FA5',
        '_FA6',
        '_FA7',
        '_FA8',
        '_FA9',
        '_FA10',
        '_FA11',
        '_FA12',
        '_FA13',
        '_FNRL',
        '_FREL',
        '_GEDF',
        '_GODP',
        '_GOV',
        '_HAIR',
        '_HEB',
        '_HEIG',
        '_HNM',
        '_HOL',
        '_INTE',
        '_LOC',
        '_MARI',
        '_MARNM',
        '_PRIM',
        '_MARNM_SURN',
        '_MARR_CHIL',
        '_MARR_FAMC',
        '_MARR_GCHI',
        '_MARR_GCH1',
        '_MARR_GCH2',
        '_MARR_HSIB',
        '_MARR_PARE',
        '_MARR_SIBL',
        '_MBON',
        '_MDCL',
        '_MEDC',
        '_MEND',
        '_MILI',
        '_MILT',
        '_MREL',
        '_MSTAT',
        '_NAME',
        '_NAMS',
        '_NLIV',
        '_NMAR',
        '_NMR',
        '_PLACE',
        '_WT_USER',
        '_PRMN',
        '_SCBK',
        '_SEPR',
        '_SSHOW',
        '_STAT',
        '_SUBQ',
        '_TODO',
        '_TYPE',
        '_UID',
        '_URL',
        '_WEIG',
        '_WITN',
        '_YART',
        '__BRTM_CHIL',
        '__BRTM_GCHI',
        '__BRTM_GCH1',
        '__BRTM_GCH2',
        '__BRTM_HSIB',
        '__BRTM_SIBL',
        // These pseudo-tags are generated dynamically to display media object attributes
        '__FILE_SIZE__',
        '__IMAGE_SIZE__',
    ];

    /** @var string[] Possible values for the Object-File-Format types */
    private const OBJE_FILE_FORM_TYPE = [
        'audio',
        'book',
        'card',
        'certificate',
        'coat',
        'document',
        'electronic',
        'fiche',
        'film',
        'magazine',
        'manuscript',
        'map',
        'newspaper',
        'photo',
        'tombstone',
        'video',
        'painting',
        'other',
    ];

    /**
     * Is $tag one of our known tags?
     *
     * @param string $tag
     *
     * @return bool
     */
    public static function isTag($tag): bool
    {
        return in_array($tag, self::ALL_TAGS, true);
    }

    /**
     * Translate a tag, for an (optional) record
     *
     * @param string $tag
     *
     * @return string
     */
    public static function getLabel($tag): string
    {
        switch ($tag) {
            case 'ABBR':
                /* I18N: gedcom tag ABBR */
                return I18N::translate('Abbreviation');
            case 'ADDR':
                /* I18N: gedcom tag ADDR */
                return I18N::translate('Address');
            case 'ADR1':
                /* I18N: gedcom tag ADD1 */
                return I18N::translate('Address line 1');
            case 'ADR2':
                /* I18N: gedcom tag ADD2 */
                return I18N::translate('Address line 2');
            case 'ADR3':
                /* I18N: gedcom tag ADD2 */
                return I18N::translate('Address line 3');
            case 'ADOP':
                /* I18N: gedcom tag ADOP */
                return I18N::translate('Adoption');
            case 'ADOP:DATE':
                return I18N::translate('Date of adoption');
            case 'ADOP:PLAC':
                return I18N::translate('Place of adoption');
            case 'AFN':
                /* I18N: gedcom tag AFN */
                return I18N::translate('Ancestral file number');
            case 'AGE':
                /* I18N: gedcom tag AGE */
                return I18N::translate('Age');
            case 'AGNC':
                /* I18N: gedcom tag AGNC */
                return I18N::translate('Agency');
            case 'ALIA':
                /* I18N: gedcom tag ALIA */
                return I18N::translate('Alias');
            case 'ANCE':
                /* I18N: gedcom tag ANCE */
                return I18N::translate('Generations of ancestors');
            case 'ANCI':
                /* I18N: gedcom tag ANCI */
                return I18N::translate('Ancestors interest');
            case 'ANUL':
                /* I18N: gedcom tag ANUL */
                return I18N::translate('Annulment');
            case 'ASSO':
                /* I18N: gedcom tag ASSO */
                return I18N::translate('Associate');
            case 'AUTH':
                /* I18N: gedcom tag AUTH */
                return I18N::translate('Author');
            case 'BAPL':
                /* I18N: gedcom tag BAPL. LDS = Church of Latter Day Saints. */
                return I18N::translate('LDS baptism');
            case 'BAPL:DATE':
                /* I18N: LDS = Church of Latter Day Saints. */
                return I18N::translate('Date of LDS baptism');
            case 'BAPL:PLAC':
                /* I18N: LDS = Church of Latter Day Saints. */
                return I18N::translate('Place of LDS baptism');
            case 'BAPM':
                /* I18N: gedcom tag BAPM */
                return I18N::translate('Baptism');
            case 'BAPM:DATE':
                return I18N::translate('Date of baptism');
            case 'BAPM:PLAC':
                return I18N::translate('Place of baptism');
            case 'BARM':
                /* I18N: gedcom tag BARM */
                return I18N::translate('Bar mitzvah');
            case 'BARM:DATE':
                return I18N::translate('Date of bar mitzvah');
            case 'BARM:PLAC':
                return I18N::translate('Place of bar mitzvah');
            case 'BASM':
                /* I18N: gedcom tag BASM */
                return I18N::translate('Bat mitzvah');
            case 'BASM:DATE':
                return I18N::translate('Date of bat mitzvah');
            case 'BASM:PLAC':
                return I18N::translate('Place of bat mitzvah');
            case 'BIRT':
                /* I18N: gedcom tag BIRT */
                return I18N::translate('Birth');
            case 'BIRT:DATE':
                return I18N::translate('Date of birth');
            case 'BIRT:PLAC':
                return I18N::translate('Place of birth');
            case 'BLES':
                /* I18N: gedcom tag BLES */
                return I18N::translate('Blessing');
            case 'BLES:DATE':
                return I18N::translate('Date of blessing');
            case 'BLES:PLAC':
                return I18N::translate('Place of blessing');
            case 'BLOB':
                /* I18N: gedcom tag BLOB */
                return I18N::translate('Binary data object');
            case 'BURI':
                /* I18N: gedcom tag BURI */
                return I18N::translate('Burial');
            case 'BURI:DATE':
                return I18N::translate('Date of burial');
            case 'BURI:PLAC':
                return I18N::translate('Place of burial');
            case 'CALN':
                /* I18N: gedcom tag CALN */
                return I18N::translate('Call number');
            case 'CAST':
                /* I18N: gedcom tag CAST */
                return I18N::translate('Caste');
            case 'CAUS':
                /* I18N: gedcom tag CAUS */
                return I18N::translate('Cause');
            case 'CEME':
                /* I18N: gedcom tag CEME */
                return I18N::translate('Cemetery');
            case 'CENS':
                /* I18N: gedcom tag CENS */
                return I18N::translate('Census');
            case 'CENS:DATE':
                return I18N::translate('Census date');
            case 'CENS:PLAC':
                return I18N::translate('Census place');
            case '_UPD': // Family Tree Builder uses "1 _UPD 14 APR 2012 00:14:10 GMT-5" instead of 1 CHAN/2 DATE/3 TIME
            case 'CHAN':
            /* I18N: gedcom tag CHAN */
                return I18N::translate('Last change');
            case 'CHAN:DATE':
                /* I18N: gedcom tag CHAN:DATE */
                return I18N::translate('Date of last change');
            case 'CHAN:_WT_USER':
                /* I18N: gedcom tag CHAN:_WT_USER */
                return I18N::translate('Author of last change');
            case 'CHAR':
                /* I18N: gedcom tag CHAR */
                return I18N::translate('Character set');
            case 'CHIL':
                /* I18N: gedcom tag CHIL */
                return I18N::translate('Child');
            case 'CHR':
                /* I18N: gedcom tag CHR */
                return I18N::translate('Christening');
            case 'CHR:DATE':
                return I18N::translate('Date of christening');
            case 'CHR:PLAC':
                return I18N::translate('Place of christening');
            case 'CHRA':
                /* I18N: gedcom tag CHRA */
                return I18N::translate('Adult christening');
            case 'CITN':
                /* I18N: gedcom tag CITN */
                return I18N::translate('Citizenship');
            case 'CITY':
                /* I18N: gedcom tag CITY */
                return I18N::translate('City');
            case 'COMM':
                /* I18N: gedcom tag COMM */
                return I18N::translate('Comment');
            case 'CONC':
                /* I18N: gedcom tag CONC */
                return I18N::translate('Concatenation');
            case 'CONT':
                /* I18N: gedcom tag CONT */
                return I18N::translate('Continued');
            case 'CONF':
                /* I18N: gedcom tag CONF */
                return I18N::translate('Confirmation');
            case 'CONF:DATE':
                return I18N::translate('Date of confirmation');
            case 'CONF:PLAC':
                return I18N::translate('Place of confirmation');
            case 'CONL':
                /* I18N: gedcom tag CONL. LDS = Church of Latter Day Saints. */
                return I18N::translate('LDS confirmation');
            case 'COPR':
                /* I18N: gedcom tag COPR */
                return I18N::translate('Copyright');
            case 'CORP':
                /* I18N: gedcom tag CORP */
                return I18N::translate('Corporation');
            case 'CREM':
                /* I18N: gedcom tag CREM */
                return I18N::translate('Cremation');
            case 'CREM:DATE':
                return I18N::translate('Date of cremation');
            case 'CREM:PLAC':
                return I18N::translate('Place of cremation');
            case 'CTRY':
                /* I18N: gedcom tag CTRY */
                return I18N::translate('Country');
            case 'DATA':
                /* I18N: gedcom tag DATA */
                return I18N::translate('Data');
            case 'DATA:DATE':
                return I18N::translate('Date of entry in original source');
            case '_DATE': // Family Tree Builder uses OBJE:_DATE
            case 'DATE':
                /* I18N: gedcom tag DATE */
                return I18N::translate('Date');
            case 'DEAT':
                /* I18N: gedcom tag DEAT */
                return I18N::translate('Death');
            case 'DEAT:CAUS':
                return I18N::translate('Cause of death');
            case 'DEAT:DATE':
                return I18N::translate('Date of death');
            case 'DEAT:PLAC':
                return I18N::translate('Place of death');
            case 'DESC':
                /* I18N: gedcom tag DESC */
                return I18N::translate('Descendants');
            case 'DESI':
                /* I18N: gedcom tag DESI */
                return I18N::translate('Descendants interest');
            case 'DEST':
                /* I18N: gedcom tag DEST */
                return I18N::translate('Destination');
            case 'DIV':
                /* I18N: gedcom tag DIV */
                return I18N::translate('Divorce');
            case 'DIVF':
                /* I18N: gedcom tag DIVF */
                return I18N::translate('Divorce filed');
            case 'DSCR':
                /* I18N: gedcom tag DSCR */
                return I18N::translate('Description');
            case 'EDUC':
                /* I18N: gedcom tag EDUC */
                return I18N::translate('Education');
            case 'EDUC:AGNC':
                return I18N::translate('School or college');
            case 'EMAI':
            case 'EMAL':
            case 'EMAIL':
                /* I18N: gedcom tag EMAIL */
                return I18N::translate('Email address');
            case 'EMIG':
                /* I18N: gedcom tag EMIG */
                return I18N::translate('Emigration');
            case 'EMIG:DATE':
                return I18N::translate('Date of emigration');
            case 'EMIG:PLAC':
                return I18N::translate('Place of emigration');
            case 'ENDL':
                /* I18N: gedcom tag ENDL. LDS = Church of Latter Day Saints. */
                return I18N::translate('LDS endowment');
            case 'ENDL:DATE':
                /* I18N: LDS = Church of Latter Day Saints. */
                return I18N::translate('Date of LDS endowment');
            case 'ENDL:PLAC':
                /* I18N: LDS = Church of Latter Day Saints. */
                return I18N::translate('Place of LDS endowment');
            case 'ENGA':
                /* I18N: gedcom tag ENGA */
                return I18N::translate('Engagement');
            case 'ENGA:DATE':
                return I18N::translate('Date of engagement');
            case 'ENGA:PLAC':
                return I18N::translate('Place of engagement');
            case 'EVEN':
                /* I18N: gedcom tag EVEN */
                return I18N::translate('Event');
            case 'EVEN:DATE':
                return I18N::translate('Date of event');
            case 'EVEN:PLAC':
                return I18N::translate('Place of event');
            case 'EVEN:TYPE':
                return I18N::translate('Type of event');
            case 'FACT':
                /* I18N: gedcom tag FACT */
                return I18N::translate('Fact');
            case 'FACT:TYPE':
                return I18N::translate('Type of fact');
            case 'FAM':
                /* I18N: gedcom tag FAM */
                return I18N::translate('Family');
            case 'FAMC':
                /* I18N: gedcom tag FAMC */
                return I18N::translate('Family as a child');
            case 'FAMF':
                /* I18N: gedcom tag FAMF */
                return I18N::translate('Family file');
            case 'FAMS':
                /* I18N: gedcom tag FAMS */
                return I18N::translate('Family as a spouse');
            case 'FAX':
                /* I18N: gedcom tag FAX */
                return I18N::translate('Fax');
            case 'FCOM':
                /* I18N: gedcom tag FCOM */
                return I18N::translate('First communion');
            case 'FCOM:DATE':
                return I18N::translate('Date of first communion');
            case 'FCOM:PLAC':
                return I18N::translate('Place of first communion');
            case 'FILE':
                /* I18N: gedcom tag FILE */
                return I18N::translate('Filename');
            case 'FONE':
                /* I18N: gedcom tag FONE */
                return I18N::translate('Phonetic');
            case 'FORM':
                /* I18N: gedcom tag FORM */
                return I18N::translate('Format');
            case 'GEDC':
                /* I18N: gedcom tag GEDC */
                return I18N::translate('GEDCOM file');
            case 'GIVN':
                /* I18N: gedcom tag GIVN */
                return I18N::translate('Given names');
            case 'GRAD':
                /* I18N: gedcom tag GRAD */
                return I18N::translate('Graduation');
            case 'HEAD':
                /* I18N: gedcom tag HEAD */
                return I18N::translate('Header');
            case 'HUSB':
                /* I18N: gedcom tag HUSB */
                return I18N::translate('Husband');
            case 'IDNO':
                /* I18N: gedcom tag IDNO */
                return I18N::translate('Identification number');
            case 'IMMI':
                /* I18N: gedcom tag IMMI */
                return I18N::translate('Immigration');
            case 'IMMI:DATE':
                return I18N::translate('Date of immigration');
            case 'IMMI:PLAC':
                return I18N::translate('Place of immigration');
            case 'INDI':
                /* I18N: gedcom tag INDI */
                return I18N::translate('Individual');
            case 'INFL':
                /* I18N: gedcom tag INFL */
                return I18N::translate('Infant');
            case 'LANG':
                /* I18N: gedcom tag LANG */
                return I18N::translate('Language');
            case 'LATI':
                /* I18N: gedcom tag LATI */
                return I18N::translate('Latitude');
            case 'LEGA':
                /* I18N: gedcom tag LEGA */
                return I18N::translate('Legatee');
            case 'LONG':
                /* I18N: gedcom tag LONG */
                return I18N::translate('Longitude');
            case 'MAP':
                /* I18N: gedcom tag MAP */
                return I18N::translate('Coordinates');
            case 'MARB':
                /* I18N: gedcom tag MARB */
                return I18N::translate('Marriage banns');
            case 'MARB:DATE':
                return I18N::translate('Date of marriage banns');
            case 'MARB:PLAC':
                return I18N::translate('Place of marriage banns');
            case 'MARC':
                /* I18N: gedcom tag MARC */
                return I18N::translate('Marriage contract');
            case 'MARL':
                /* I18N: gedcom tag MARL */
                return I18N::translate('Marriage license');
            case 'MARR':
                /* I18N: gedcom tag MARR */
                return I18N::translate('Marriage');
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
                /* I18N: gedcom tag MARS */
                return I18N::translate('Marriage settlement');
            case 'MEDI':
                /* I18N: gedcom tag MEDI */
                return I18N::translate('Media type');
            case 'NAME':
                /* I18N: gedcom tag NAME */
                return I18N::translate('Name');
            case 'NAME:FONE':
                return I18N::translate('Phonetic name');
            case 'NAME:_HEB':
                return I18N::translate('Name in Hebrew');
            case 'NATI':
                /* I18N: gedcom tag NATI */
                return I18N::translate('Nationality');
            case 'NATU':
                /* I18N: gedcom tag NATU */
                return I18N::translate('Naturalization');
            case 'NATU:DATE':
                return I18N::translate('Date of naturalization');
            case 'NATU:PLAC':
                return I18N::translate('Place of naturalization');
            case 'NCHI':
                /* I18N: gedcom tag NCHI */
                return I18N::translate('Number of children');
            case 'NICK':
                /* I18N: gedcom tag NICK */
                return I18N::translate('Nickname');
            case 'NMR':
                /* I18N: gedcom tag NMR */
                return I18N::translate('Number of marriages');
            case 'NOTE':
                /* I18N: gedcom tag NOTE */
                return I18N::translate('Note');
            case 'NPFX':
                /* I18N: gedcom tag NPFX */
                return I18N::translate('Name prefix');
            case 'NSFX':
                /* I18N: gedcom tag NSFX */
                return I18N::translate('Name suffix');
            case 'OBJE':
                /* I18N: gedcom tag OBJE */
                return I18N::translate('Media object');
            case 'OCCU':
                /* I18N: gedcom tag OCCU */
                return I18N::translate('Occupation');
            case 'OCCU:AGNC':
                return I18N::translate('Employer');
            case 'ORDI':
                /* I18N: gedcom tag ORDI */
                return I18N::translate('Ordinance');
            case 'ORDN':
                /* I18N: gedcom tag ORDN */
                return I18N::translate('Ordination');
            case 'ORDN:AGNC':
                return I18N::translate('Religious institution');
            case 'ORDN:DATE':
                return I18N::translate('Date of ordination');
            case 'ORDN:PLAC':
                return I18N::translate('Place of ordination');
            case 'PAGE':
                /* I18N: gedcom tag PAGE */
                return I18N::translate('Citation details');
            case 'PEDI':
                /* I18N: gedcom tag PEDI */
                return I18N::translate('Relationship to parents');
            case 'PHON':
                /* I18N: gedcom tag PHON */
                return I18N::translate('Phone');
            case '_PLACE': // Family Tree Builder uses OBJE:_PLACE
            case 'PLAC':
            /* I18N: gedcom tag PLAC */
                return I18N::translate('Place');
            case 'PLAC:FONE':
                return I18N::translate('Phonetic place');
            case 'PLAC:ROMN':
                return I18N::translate('Romanized place');
            case 'PLAC:_HEB':
                return I18N::translate('Place in Hebrew');
            case 'POST':
                /* I18N: gedcom tag POST */
                return I18N::translate('Postal code');
            case 'PROB':
                /* I18N: gedcom tag PROB */
                return I18N::translate('Probate');
            case 'PROP':
                /* I18N: gedcom tag PROP */
                return I18N::translate('Property');
            case 'PUBL':
                /* I18N: gedcom tag PUBL */
                return I18N::translate('Publication');
            case 'QUAY':
                /* I18N: gedcom tag QUAY */
                return I18N::translate('Quality of data');
            case 'REFN':
                /* I18N: gedcom tag REFN */
                return I18N::translate('Reference number');
            case 'RELA':
                /* I18N: gedcom tag RELA */
                return I18N::translate('Relationship');
            case 'RELI':
                /* I18N: gedcom tag RELI */
                return I18N::translate('Religion');
            case 'REPO':
                /* I18N: gedcom tag REPO */
                return I18N::translate('Repository');
            case 'REPO:NAME':
                return I18N::translateContext('Repository', 'Name');
            case 'RESI':
                /* I18N: gedcom tag RESI */
                return I18N::translate('Residence');
            case 'RESI:DATE':
                return I18N::translate('Date of residence');
            case 'RESI:PLAC':
                return I18N::translate('Place of residence');
            case 'RESN':
                /* I18N: gedcom tag RESN */
                return I18N::translate('Restriction');
            case 'RETI':
                /* I18N: gedcom tag RETI */
                return I18N::translate('Retirement');
            case 'RETI:AGNC':
                return I18N::translate('Employer');
            case 'RFN':
                /* I18N: gedcom tag RFN */
                return I18N::translate('Record file number');
            case '_PHOTO_RIN':
                // Family Tree Builder uses "0 OBJE/1 _PHOTO_RIN"
                // no  break
            case '_PRIN':// Family Tree Builder uses "0 _ALBUM/1 _PHOTO/2 _PRIN"
            case 'RIN':
                /* I18N: gedcom tag RIN */
                return I18N::translate('Record ID number');
            case 'ROLE':
                /* I18N: gedcom tag ROLE */
                return I18N::translate('Role');
            case 'ROMN':
                /* I18N: gedcom tag ROMN */
                return I18N::translate('Romanized');
            case 'SERV':
                /* I18N: gedcom tag SERV */
                return I18N::translate('Remote server');
            case 'SEX':
                /* I18N: gedcom tag SEX */
                return I18N::translate('Gender');
            case 'SHARED_NOTE':
                return I18N::translate('Shared note');
            case 'SLGC':
                /* I18N: gedcom tag SLGC. LDS = Church of Latter Day Saints. */
                return I18N::translate('LDS child sealing');
            case 'SLGC:DATE':
                /* I18N: LDS = Church of Latter Day Saints. */
                return I18N::translate('Date of LDS child sealing');
            case 'SLGC:PLAC':
                /* I18N: LDS = Church of Latter Day Saints. */
                return I18N::translate('Place of LDS child sealing');
            case 'SLGS':
                /* I18N: gedcom tag SLGS. LDS = Church of Latter Day Saints. */
                return I18N::translate('LDS spouse sealing');
            case 'SOUR':
                /* I18N: gedcom tag SOUR */
                return I18N::translate('Source');
            case 'SPFX':
                /* I18N: gedcom tag SPFX */
                return I18N::translate('Surname prefix');
            case 'SSN':
                /* I18N: gedcom tag SSN */
                return I18N::translate('Social security number');
            case 'STAE':
                /* I18N: gedcom tag STAE */
                return I18N::translate('State');
            case 'STAT':
                /* I18N: gedcom tag STAT */
                return I18N::translate('Status');
            case 'STAT:DATE':
                return I18N::translate('Status change date');
            case 'SUBM':
                /* I18N: gedcom tag SUBM */
                return I18N::translate('Submitter');
            case 'SUBN':
                /* I18N: gedcom tag SUBN */
                return I18N::translate('Submission');
            case 'SURN':
                /* I18N: gedcom tag SURN */
                return I18N::translate('Surname');
            case 'TEMP':
                /* I18N: gedcom tag TEMP */
                return I18N::translate('Temple');
            case 'TEXT':
                /* I18N: gedcom tag TEXT */
                return I18N::translate('Text');
            case 'TIME':
                /* I18N: gedcom tag TIME */
                return I18N::translate('Time');
            case 'TITL':
                /* I18N: gedcom tag TITL */
                return I18N::translate('Title');
            case 'TITL:FONE':
                return I18N::translate('Phonetic title');
            case 'TITL:ROMN':
                return I18N::translate('Romanized title');
            case 'TITL:_HEB':
                return I18N::translate('Title in Hebrew');
            case 'TRLR':
                /* I18N: gedcom tag TRLR */
                return I18N::translate('Trailer');
            case 'TYPE':
                /* I18N: gedcom tag TYPE */
                return I18N::translate('Type');
            case 'URL':
                /* I18N: gedcom tag URL (A web address / URL) */
                return I18N::translate('URL');
            case 'VERS':
                /* I18N: gedcom tag VERS */
                return I18N::translate('Version');
            case 'WIFE':
                /* I18N: gedcom tag WIFE */
                return I18N::translate('Wife');
            case 'WILL':
                /* I18N: gedcom tag WILL */
                return I18N::translate('Will');
            case 'WWW':
                /* I18N: gedcom tag WWW (A web address / URL) */
                return I18N::translate('URL');

            case '_ADPF':
                /* I18N: gedcom tag _ADPF */
                return I18N::translate('Adopted by father');

            case '_ADPM':
                /* I18N: gedcom tag _ADPM */
                return I18N::translate('Adopted by mother');

            case '_AKA':
            case '_AKAN':
                /* I18N: gedcom tag _AKA */
                return I18N::translate('Also known as');

            case '_ALBUM':
                // Family Tree Builder uses OBJE:_ALBUM
                /* I18N: gedcom tag _ALBUM */
                return I18N::translate('Album');
            case '_ASSO':
                /* I18N: gedcom tag _ASSO */
                return I18N::translate('Associate');

            case '_BIBL':
                /* I18N: gedcom tag _BIBL */
                return I18N::translate('Bibliography');

            case '_BRTM':
                /* I18N: gedcom tag _BRTM */
                return I18N::translate('Brit milah');
            case '_BRTM:DATE':
                return I18N::translate('Date of brit milah');
            case '_BRTM:PLAC':
                return I18N::translate('Place of brit milah');

            case '_COML':
                /* I18N: gedcom tag _COML */
                return I18N::translate('Common law marriage');

            case '_DBID':
                /* I18N: gedcom tag _DBID */
                return I18N::translate('Linked database ID');

            case '_DEG':
                /* I18N: gedcom tag _DEG */
                return I18N::translate('Degree');
            case '_DETS':
                /* I18N: gedcom tag _DETS */
                return I18N::translate('Death of one spouse');
            case '_DNA':
                /* I18N: gedcom tag _DNA (from FTM 2010) */
                return I18N::translate('DNA markers');
            case '_EMAIL':
                /* I18N: gedcom tag _EMAIL */
                return I18N::translate('Email address');
            case '_EYEC':
                /* I18N: gedcom tag _EYEC */
                return I18N::translate('Eye color');
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
                /* I18N: gedcom tag _FNRL */
                return I18N::translate('Funeral');
            case '_FREL':
                /* I18N: gedcom tag _FREL */
                return I18N::translate('Relationship to father');
            case '_GEDF':
                /* I18N: gedcom tag _GEDF */
                return I18N::translate('GEDCOM file');
            case '_GODP':
                /* I18N: gedcom tag _GODP */
                return I18N::translate('Godparent');
            case '_GOV':
                /* I18N: gedcom tag _GOV - see https://gov.genealogy.net */
                return I18N::translate('GOV identifier');
            case '_HAIR':
                /* I18N: gedcom tag _HAIR */
                return I18N::translate('Hair color');
            case '_HEB':
                /* I18N: gedcom tag _HEB */
                return I18N::translate('Hebrew');
            case '_HEIG':
                /* I18N: gedcom tag _HEIG */
                return I18N::translate('Height');
            case '_HNM':
                /* I18N: gedcom tag _HNM */
                return I18N::translate('Hebrew name');
            case '_HOL':
                /* I18N: gedcom tag _HOL */
                return I18N::translate('Holocaust');

            case '_INTE':
                /* I18N: gedcom tag _INTE */
                return I18N::translate('Interment');

            case '_LOC':
                /* I18N: gedcom tag _LOC */
                return I18N::translate('Location');
            case '_MARI':
                /* I18N: gedcom tag _MARI */
                return I18N::translate('Marriage intention');
            case '_MARNM':
                /* I18N: gedcom tag _MARNM */
                return I18N::translate('Married name');
            case '_PRIM':
                /* I18N: gedcom tag _PRIM */
                return I18N::translate('Highlighted image');
            case '_MARNM_SURN':
                return I18N::translate('Married surname');

            case '_MBON':
                /* I18N: gedcom tag _MBON */
                return I18N::translate('Marriage bond');
            case '_MDCL':
                /* I18N: gedcom tag _MDCL */
                return I18N::translate('Medical');
            case '_MEDC':
                /* I18N: gedcom tag _MEDC */
                return I18N::translate('Medical condition');
            case '_MEND':
                /* I18N: gedcom tag _MEND */
                return I18N::translate('Marriage ending status');
            case '_MILI':
                /* I18N: gedcom tag _MILI */
                return I18N::translate('Military');
            case '_MILT':
                /* I18N: gedcom tag _MILT */
                return I18N::translate('Military service');
            case '_MREL':
                /* I18N: gedcom tag _MREL */
                return I18N::translate('Relationship to mother');
            case '_MSTAT':
                /* I18N: gedcom tag _MSTAT */
                return I18N::translate('Marriage beginning status');
            case '_NAME':
                /* I18N: gedcom tag _NAME */
                return I18N::translate('Mailing name');
            case '_NAMS':
                /* I18N: gedcom tag _NAMS */
                return I18N::translate('Namesake');
            case '_NLIV':
                /* I18N: gedcom tag _NLIV */
                return I18N::translate('Not living');
            case '_NMAR':
                /* I18N: gedcom tag _NMAR */
                return I18N::translate('Never married');

            case '_NMR':
                /* I18N: gedcom tag _NMR */
                return I18N::translate('Not married');

            case '_PHOTO':
                // Family Tree Builder uses "0 _ALBUM/1_PHOTO"
                return I18N::translate('Photo');
            case '_WT_USER':
                return I18N::translate('by');
            case '_PRMN':
                /* I18N: gedcom tag _PRMN */
                return I18N::translate('Permanent number');
            case '_RNAME':
                return I18N::translate('Religious name');

            case '_SCBK':
                /* I18N: gedcom tag _SCBK */
                return I18N::translate('Scrapbook');
            case '_SEPR':
                /* I18N: gedcom tag _SEPR */
                return I18N::translate('Separation');
            case '_SSHOW':
                /* I18N: gedcom tag _SSHOW */
                return I18N::translate('Slide show');
            case '_STAT':
                /* I18N: gedcom tag _STAT */
                return I18N::translate('Marriage status');
            case '_SUBQ':
                /* I18N: gedcom tag _SUBQ */
                return I18N::translate('Short version');
            case '_TODO':
                /* I18N: gedcom tag _TODO */
                return I18N::translate('Research task');
            case '_TYPE':
                /* I18N: gedcom tag _TYPE */
                return I18N::translate('Media type');
            case '_UID':
                /* I18N: gedcom tag _UID */
                return I18N::translate('Unique identifier');
            case '_URL':
                /* I18N: gedcom tag _URL */
                return I18N::translate('URL');
            case '_WEIG':
                /* I18N: gedcom tag _WEIG */
                return I18N::translate('Weight');
            case '_WITN':
                /* I18N: gedcom tag _WITN */
                return I18N::translate('Witness');
            case '_WT_OBJE_SORT':
                /* I18N: gedcom tag _WT_OBJE_SORT */
                return I18N::translate('Re-order media');
            case '_YART':
                /* I18N: gedcom tag _YART - A yahrzeit is a special anniversary of death in the Hebrew faith/calendar. */
                return I18N::translate('Yahrzeit');
            case '_FILESIZE': // Family Tree Builder uses OBJE:_FILESIZE
            case '__FILE_SIZE__':
                // This pseudo-tag is generated internally to present information about a media object
                return I18N::translate('File size');
            case '__IMAGE_SIZE__':
                // This pseudo-tag is generated internally to present information about a media object
                return I18N::translate('Image dimensions');
            default:
                // If no specialisation exists (e.g. DEAT:CAUS), then look for the general (CAUS)
                if (str_contains($tag, ':')) {
                    [, $tag] = explode(':', $tag, 2);

                    return self::getLabel($tag);
                }

                // Still no translation? Highlight this as an error
                return '<span class="error" title="' . I18N::translate('Unrecognized GEDCOM code') . '">' . e($tag) . '</span>';
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
    public static function getLabelValue($tag, $value, GedcomRecord $record = null, $element = 'div'): string
    {
        return
            '<' . $element . ' class="fact_' . $tag . '">' .
            /* I18N: a label/value pair, such as “Occupation: Farmer”. Some languages may need to change the punctuation. */
            I18N::translate('<span class="label">%1$s:</span> <span class="field" dir="auto">%2$s</span>', self::getLabel($tag), $value) .
            '</' . $element . '>';
    }

    /**
     * Get a list of facts, for use in the "fact picker" edit control
     *
     * @param string $fact_type
     *
     * @return string[]
     */
    public static function getPicklistFacts($fact_type): array
    {
        switch ($fact_type) {
            case Individual::RECORD_TYPE:
                $tags = [
                    // Facts, attributes for individuals (no links to FAMs)
                    'RESN',
                    'NAME',
                    'SEX',
                    'BIRT',
                    'CHR',
                    'DEAT',
                    'BURI',
                    'CREM',
                    'ADOP',
                    'BAPM',
                    'BARM',
                    'BASM',
                    'BLES',
                    'CHRA',
                    'CONF',
                    'FCOM',
                    'ORDN',
                    'NATU',
                    'EMIG',
                    'IMMI',
                    'CENS',
                    'PROB',
                    'WILL',
                    'GRAD',
                    'RETI',
                    'EVEN',
                    'CAST',
                    'DSCR',
                    'EDUC',
                    'IDNO',
                    'NATI',
                    'NCHI',
                    'NMR',
                    'OCCU',
                    'PROP',
                    'RELI',
                    'RESI',
                    'SSN',
                    'TITL',
                    'FACT',
                    'BAPL',
                    'CONL',
                    'ENDL',
                    'SLGC',
                    'SUBM',
                    'ASSO',
                    'ALIA',
                    'ANCI',
                    'DESI',
                    'RFN',
                    'AFN',
                    'REFN',
                    'RIN',
                    'CHAN',
                    'NOTE',
                    'SHARED_NOTE',
                    'SOUR',
                    'OBJE',
                    // non standard tags
                    '_BRTM',
                    '_DEG',
                    '_DNA',
                    '_EYEC',
                    '_FNRL',
                    '_HAIR',
                    '_HEIG',
                    '_HNM',
                    '_HOL',
                    '_INTE',
                    '_MDCL',
                    '_MEDC',
                    '_MILI',
                    '_MILT',
                    '_NAME',
                    '_NAMS',
                    '_NLIV',
                    '_NMAR',
                    '_PRMN',
                    '_TODO',
                    '_UID',
                    '_WEIG',
                    '_YART',
                ];
                break;

            case Family::RECORD_TYPE:
                $tags = [
                    // Facts for families, left out HUSB, WIFE & CHIL links
                    'RESN',
                    'ANUL',
                    'CENS',
                    'DIV',
                    'DIVF',
                    'ENGA',
                    'MARB',
                    'MARC',
                    'MARR',
                    'MARL',
                    'MARS',
                    'RESI',
                    'EVEN',
                    'NCHI',
                    'SUBM',
                    'SLGS',
                    'REFN',
                    'RIN',
                    'CHAN',
                    'NOTE',
                    'SHARED_NOTE',
                    'SOUR',
                    'OBJE',
                    // non standard tags
                    '_NMR',
                    'MARR_CIVIL',
                    'MARR_RELIGIOUS',
                    'MARR_PARTNERS',
                    'MARR_UNKNOWN',
                    '_COML',
                    '_MBON',
                    '_MARI',
                    '_SEPR',
                    '_TODO',
                ];
                break;

            case Source::RECORD_TYPE:
                $tags = [
                    // Facts for sources
                    'DATA',
                    'AUTH',
                    'TITL',
                    'ABBR',
                    'PUBL',
                    'TEXT',
                    'REPO',
                    'REFN',
                    'RIN',
                    'CHAN',
                    'NOTE',
                    'SHARED_NOTE',
                    'OBJE',
                    'RESN',
                ];
                break;

            case Repository::RECORD_TYPE:
                $tags = [
                    // Facts for repositories
                    'NAME',
                    'ADDR',
                    'PHON',
                    'EMAIL',
                    'FAX',
                    'WWW',
                    'NOTE',
                    'SHARED_NOTE',
                    'REFN',
                    'RIN',
                    'CHAN',
                    'RESN',
                ];
                break;

            case 'PLAC':
                $tags = [
                    // Facts for places
                    'FONE',
                    'ROMN',
                    // non standard tags
                    '_GOV',
                    '_HEB',
                ];
                break;

            case 'NAME':
                $tags = [
                    // Facts subordinate to NAME
                    'FONE',
                    'ROMN',
                    // non standard tags
                    '_HEB',
                    '_AKA',
                    '_MARNM',
                ];
                break;

            default:
                $tags = [];
                break;
        }

        $facts = [];
        foreach ($tags as $tag) {
            $facts[$tag] = self::getLabel($tag);
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
    public static function getFileFormTypeValue(string $type): string
    {
        switch (strtolower($type)) {
            case 'audio':
                /* I18N: Type of media object */
                return I18N::translate('Audio');
            case 'book':
                /* I18N: Type of media object */
                return I18N::translate('Book');
            case 'card':
                /* I18N: Type of media object */
                return I18N::translate('Card');
            case 'certificate':
                /* I18N: Type of media object */
                return I18N::translate('Certificate');
            case 'coat':
                /* I18N: Type of media object */
                return I18N::translate('Coat of arms');
            case 'document':
                /* I18N: Type of media object */
                return I18N::translate('Document');
            case 'electronic':
                /* I18N: Type of media object */
                return I18N::translate('Electronic');
            case 'fiche':
                /* I18N: Type of media object */
                return I18N::translate('Microfiche');
            case 'film':
                /* I18N: Type of media object */
                return I18N::translate('Microfilm');
            case 'magazine':
                /* I18N: Type of media object */
                return I18N::translate('Magazine');
            case 'manuscript':
                /* I18N: Type of media object */
                return I18N::translate('Manuscript');
            case 'map':
                /* I18N: Type of media object */
                return I18N::translate('Map');
            case 'newspaper':
                /* I18N: Type of media object */
                return I18N::translate('Newspaper');
            case 'photo':
                /* I18N: Type of media object */
                return I18N::translate('Photo');
            case 'tombstone':
                /* I18N: Type of media object */
                return I18N::translate('Tombstone');
            case 'video':
                /* I18N: Type of media object */
                return I18N::translate('Video');
            case 'painting':
                /* I18N: Type of media object */
                return I18N::translate('Painting');
            default:
                /* I18N: Type of media object */
                return I18N::translate('Other');
        }
    }

    /**
     * A list of all possible values for 1 FILE/2 FORM/3 TYPE
     *
     * @return string[]
     */
    public static function getFileFormTypes(): array
    {
        $values = array_map(static function (string $keyword): string {
            return self::getFileFormTypeValue($keyword);
        }, array_combine(self::OBJE_FILE_FORM_TYPE, self::OBJE_FILE_FORM_TYPE));

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
    public static function createUid(): string
    {
        $uid = str_replace('-', '', Uuid::uuid4()->toString());

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
