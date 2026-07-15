<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\I18N\Languages;

use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

use function mb_substr;
use function str_repeat;
use function str_starts_with;

final readonly class Czech extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::ThreeFormsCzechSlovak;

    protected const string    ENDONYM            = 'čeština';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'cs';
    protected const string    LOCALE_CODE        = 'cs_CZ@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'kolem %s';
    protected const string    DATE_AFTER         = 'po %s';
    protected const string    DATE_BEFORE        = 'před %s';
    protected const string    DATE_BETWEEN_AND   = 'mezi %s a %s';
    protected const string    DATE_CALCULATED    = 'dopočítáno %s';
    protected const string    DATE_ESTIMATED     = 'odhadem %s';
    protected const string    DATE_FROM          = 'od %s';
    protected const string    DATE_FROM_TO       = 'od %s do %s';
    protected const string    DATE_INTERPRETED   = 'interpretován %s';
    protected const string    DATE_TO            = 'do %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'př. n. l.';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'n. l.';
    protected const string    LIST_SEPARATOR_AND = ' a ';
    protected const string    LIST_SEPARATOR_OR  = ' nebo ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'leden',
        'únor',
        'březen',
        'duben',
        'květen',
        'červen',
        'červenec',
        'srpen',
        'září',
        'říjen',
        'listopad',
        'prosinec',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

    protected const array GREGORIAN_MONTHS_GENITIVE = [
        '',
        'ledna',
        'února',
        'března',
        'dubna',
        'května',
        'června',
        'července',
        'srpna',
        'září',
        'října',
        'listopadu',
        'prosince',
    ];

    protected const array GREGORIAN_MONTHS_LOCATIVE = [
        '',
        'lednu',
        'únoru',
        'březnu',
        'dubnu',
        'květnu',
        'červnu',
        'červenci',
        'srpnu',
        'září',
        'říjnu',
        'listopadu',
        'prosinci',
    ];

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = [
        '',
        'lednem',
        'únorem',
        'březnem',
        'dubnem',
        'květnem',
        'červnem',
        'červencem',
        'srpnem',
        'zářím',
        'říjnem',
        'listopadem',
        'prosincem',
    ];

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Tišri',
        'Chešvan',
        'Kislev',
        'Tevet',
        'Ševat',
        'Adar I',
        'Adar II',
        'Adar',
        'Nisan',
        'Ijar',
        'Sivan',
        'Tamuz',
        'Av',
        'Elul',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'Vendémiaire',
        'Brumaire',
        'Frimaire',
        'Nivôse',
        'Pluviôse',
        'Ventôse',
        'Germinal',
        'Floréal',
        'Prairial',
        'Messidor',
        'Thermidor',
        'Fructidor',
        'jours complémentaires (svátky)',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'al-muharram',
        'safar',
        'Rabi’ al-awwal',
        'Rabi’ al-thani',
        'džumádá l-úlá',
        'džumádá l-áchira',
        'radžab',
        'ša’bán',
        'ramadán',
        'šauvál',
        'dhú l-ka’da',
        'dhú’l-hidždža',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'Farvardin',
        'Ordibehesht',
        'Khordad',
        'Tir',
        'Mordad',
        'Shahrivar',
        'Mehr',
        'Aban',
        'Azar',
        'Dey',
        'Bahman',
        'Esfand',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;
    /**
     * @return array<int,string>
     */
    /** @var array<int,string> */
    protected const array ALPHABET = [
        'A',
        UTF8::LATIN_CAPITAL_LETTER_A_WITH_ACUTE,
        'B',
        'C',
        UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
        'D',
        UTF8::LATIN_CAPITAL_LETTER_D_WITH_CARON,
        'E',
        UTF8::LATIN_CAPITAL_LETTER_E_WITH_ACUTE,
        UTF8::LATIN_CAPITAL_LETTER_E_WITH_CARON,
        'F',
        'G',
        'H',
        'CH',
        'I',
        UTF8::LATIN_CAPITAL_LETTER_I_WITH_ACUTE,
        'J',
        'K',
        'L',
        'M',
        'N',
        UTF8::LATIN_CAPITAL_LETTER_N_WITH_CARON,
        'O',
        UTF8::LATIN_CAPITAL_LETTER_O_WITH_ACUTE,
        'P',
        'Q',
        'R',
        UTF8::LATIN_CAPITAL_LETTER_R_WITH_CARON,
        'S',
        UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
        'T',
        UTF8::LATIN_CAPITAL_LETTER_T_WITH_CARON,
        'U',
        UTF8::LATIN_CAPITAL_LETTER_U_WITH_ACUTE,
        UTF8::LATIN_CAPITAL_LETTER_U_WITH_RING_ABOVE,
        'V',
        'W',
        'X',
        'Y',
        UTF8::LATIN_CAPITAL_LETTER_Y_WITH_ACUTE,
        'Z',
        UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
    ];

    protected function assembleDate(string $day, string $month, string $year): string
    {
        return $this->assembleDateDdotMY($day, $month, $year);
    }

    public function initialLetter(string $string): string
    {
        if (str_starts_with($string, 'CS')) {
            return 'CS';
        }

        return mb_substr($string, 0, 1);
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'A' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_A_WITH_ACUTE,
            'C' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_C_WITH_CARON,
            'D' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_D_WITH_CARON,
            'E' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_CAPITAL_LETTER_E_WITH_ACUTE,
            'E' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_E_WITH_CARON,
            'I' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_CAPITAL_LETTER_I_WITH_ACUTE,
            'N' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_N_WITH_CARON,
            'O' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_CAPITAL_LETTER_O_WITH_ACUTE,
            'R' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_R_WITH_CARON,
            'S' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CARON,
            'T' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_T_WITH_CARON,
            'U' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_CAPITAL_LETTER_U_WITH_ACUTE,
            'U' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_U_WITH_RING_ABOVE,
            'Y' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_CAPITAL_LETTER_Y_WITH_ACUTE,
            'Z' . UTF8::COMBINING_CARON        => UTF8::LATIN_CAPITAL_LETTER_Z_WITH_CARON,
            'a' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_A_WITH_ACUTE,
            'c' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_C_WITH_CARON,
            'd' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_D_WITH_CARON,
            'e' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_SMALL_LETTER_E_WITH_ACUTE,
            'e' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_E_WITH_CARON,
            'i' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_SMALL_LETTER_I_WITH_ACUTE,
            'n' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_N_WITH_CARON,
            'o' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_SMALL_LETTER_O_WITH_ACUTE,
            'r' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_R_WITH_CARON,
            's' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_S_WITH_CARON,
            't' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_T_WITH_CARON,
            'u' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_SMALL_LETTER_U_WITH_ACUTE,
            'u' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_U_WITH_RING_ABOVE,
            'y' . UTF8::COMBINING_ACUTE_ACCENT => UTF8::LATIN_SMALL_LETTER_Y_WITH_ACUTE,
            'z' . UTF8::COMBINING_CARON        => UTF8::LATIN_SMALL_LETTER_Z_WITH_CARON,
        ];
    }

    public function relationships(): array
    {
        $pra = static fn (int $n, string $nominative, string $genitive): array => [
            ($n > 3 ? 'pra ×' . $n . ' ' : str_repeat('pra', $n)) . $nominative,
            ($n > 3 ? 'pra ×' . $n . ' ' : str_repeat('pra', $n)) . $genitive,
        ];

        return [
            // Parents
            Relationship::fixed('otec', '%s otce')->father(),
            Relationship::fixed('matka', '%s matky')->mother(),
            Relationship::fixed('rodič', '%s rodiče')->parent(),
            // Children
            Relationship::fixed('syn', '%s syna')->son(),
            Relationship::fixed('dcera', '%s dcery')->daughter(),
            Relationship::fixed('dítě', '%s dítěte')->child(),
            // Siblings
            Relationship::fixed('starší sestra', '%s starší sestry')->older()->sister(),
            Relationship::fixed('starší bratr', '%s staršího bratra')->older()->brother(),
            Relationship::fixed('starší sourozenec', '%s staršího sourozence')->older()->sibling(),
            Relationship::fixed('mladší sestra', '%s mladší sestry')->younger()->sister(),
            Relationship::fixed('mladší bratr', '%s mladšího bratra')->younger()->brother(),
            Relationship::fixed('mladší sourozenec', '%s mladšího sourozence')->younger()->sibling(),
            Relationship::fixed('bratr', '%s bratra')->brother(),
            Relationship::fixed('sestra', '%s sestry')->sister(),
            Relationship::fixed('sourozenec', '%s sourozence')->sibling(),
            // Divorced partners
            Relationship::fixed('bývalá manželka', '%s bývalé manželky')->divorced()->partner()->female(),
            Relationship::fixed('bývalý manžel', '%s bývalého manžela')->divorced()->partner()->male(),
            Relationship::fixed('bývalý partner/partnerka', '%s bývalého partnera/partnerky')->divorced()->partner(),
            // Engaged partners
            Relationship::fixed('snoubenka', '%s snoubenky')->engaged()->partner()->female(),
            Relationship::fixed('snoubenec', '%s snoubence')->engaged()->partner()->male(),
            // Married partners
            Relationship::fixed('manželka', '%s manželky')->wife(),
            Relationship::fixed('manžel', '%s manžela')->husband(),
            Relationship::fixed('manžel/manželka', '%s manžela/manželky')->spouse(),
            // Unmarried partners
            Relationship::fixed('partnerka', '%s partnerky')->partner()->female(),
            Relationship::fixed('partner', '%s partnera')->partner(),
            // In-laws (via wife)
            Relationship::fixed('tchán', '%s tchána')->wife()->father(),
            Relationship::fixed('tchyně', '%s tchyně')->wife()->mother(),
            // In-laws (via spouse)
            Relationship::fixed('tchán', '%s tchána')->spouse()->father(),
            Relationship::fixed('tchyně', '%s tchyně')->spouse()->mother(),
            Relationship::fixed('zeť', '%s zetě')->child()->husband(),
            Relationship::fixed('snacha', '%s snachy')->child()->wife(),
            Relationship::fixed('švagr', '%s švagra')->spouse()->brother(),
            Relationship::fixed('švagr', '%s švagra')->sibling()->husband(),
            Relationship::fixed('švagrová', '%s švagrové')->spouse()->sister(),
            Relationship::fixed('švagrová', '%s švagrové')->sibling()->wife(),
            // Step-parents
            Relationship::fixed('macecha', '%s macechy')->parent()->spouse()->female(),
            Relationship::fixed('otčím', '%s otčíma')->parent()->spouse()->male(),
            // Step-children
            Relationship::fixed('nevlastní dcera', '%s nevlastní dcery')->spouse()->daughter(),
            Relationship::fixed('nevlastní syn', '%s nevlastního syna')->spouse()->son(),
            // Half-siblings
            Relationship::fixed('nevlastní bratr', '%s nevlastního bratra')->parent()->son(),
            Relationship::fixed('nevlastní sestra', '%s nevlastní sestry')->parent()->daughter(),
            Relationship::fixed('nevlastní sourozenec', '%s nevlastního sourozence')->parent()->child(),
            // Grandparents
            Relationship::fixed('dědeček', '%s dědečka')->parent()->father(),
            Relationship::fixed('babička', '%s babičky')->parent()->mother(),
            Relationship::fixed('prarodič', '%s prarodiče')->parent()->parent(),
            // Great-grandparents (fixed)
            Relationship::fixed('pradědeček', '%s pradědečka')->parent()->parent()->father(),
            Relationship::fixed('prababička', '%s prababičky')->parent()->parent()->mother(),
            Relationship::fixed('praprarodič', '%s praprarodiče')->parent()->parent()->parent(),
            // Ancestors (dynamic)
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'dědeček', '%s pradědečka'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'babička', '%s prababičky'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'prarodič', '%s praprarodiče'))->ancestor(),
            // Grandchildren
            Relationship::fixed('vnuk', '%s vnuka')->child()->son(),
            Relationship::fixed('vnučka', '%s vnučky')->child()->daughter(),
            Relationship::fixed('vnouče', '%s vnoučete')->child()->child(),
            // Great-grandchildren (fixed)
            Relationship::fixed('pravnuk', '%s pravnuka')->child()->child()->son(),
            Relationship::fixed('pravnučka', '%s pravnučky')->child()->child()->daughter(),
            Relationship::fixed('pravnouče', '%s pravnoučete')->child()->child()->child(),
            // Descendants (dynamic)
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'vnuk', '%s pravnuka'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'vnučka', '%s pravnučky'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 1, 'vnouče', '%s pravnoučete'))->descendant(),
            // Aunts and uncles
            Relationship::fixed('strýc', '%s strýce')->parent()->brother(),
            Relationship::fixed('teta', '%s tety')->parent()->sister(),
            // Great-aunts and great-uncles
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'prastrýc', '%s prastrýce'))->ancestor()->brother(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'prateta', '%s pratety'))->ancestor()->sister(),
            // Nieces and nephews
            Relationship::fixed('neteř', '%s neteře')->sibling()->daughter(),
            Relationship::fixed('synovec', '%s synovce')->sibling()->son(),
            Relationship::fixed('synovec/neteř', '%s synovce/neteře')->sibling()->child(),
            // Great-nieces and great-nephews
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'prasynovec', '%s prasynovce'))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'praneteř', '%s praneteře'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $pra($n - 2, 'prasynovec/praneteř', '%s prasynovce/praneteře'))->sibling()->descendant(),
            // Cousins
            Relationship::dynamic(static fn (int $n): array => ['sestřenice', '%s sestřenice'])->symmetricCousin()->female(),
            Relationship::dynamic(static fn (int $n): array => ['bratranec', '%s bratrance'])->symmetricCousin()->male(),
            Relationship::dynamic(static fn (int $n): array => ['bratranec/sestřenice', '%s bratrance/sestřenice'])->symmetricCousin(),
        ];
    }

    /**
     * Gregorian/Julian month names — case-inflected.
     *
     * @return array<int,string>
     */
}
