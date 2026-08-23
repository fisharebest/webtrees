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

use function str_repeat;

final readonly class Finnish extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'suomi';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'fi';
    protected const string    LOCALE_CODE        = 'fi_FI@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = UTF8::NARROW_NO_BREAK_SPACE;
    protected const string    NEGATIVE_SYMBOL    = UTF8::MINUS_SIGN;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'noin %s';
    protected const string    DATE_AFTER         = '%s jälkeen';
    protected const string    DATE_BEFORE        = 'ennen %s';
    protected const string    DATE_BETWEEN_AND   = '%s - %s välillä';
    protected const string    DATE_CALCULATED    = 'todennäköisesti %s';
    protected const string    DATE_ESTIMATED     = 'arviolta %s';
    protected const string    DATE_FROM          = '%s alkaen';
    protected const string    DATE_FROM_TO       = '%s - %s asti';
    protected const string    DATE_INTERPRETED   = 'tulkittu %s';
    protected const string    DATE_TO            = '%s asti';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'EAA';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'JAA';
    protected const string    LIST_SEPARATOR_AND = ' ja ';
    protected const string    LIST_SEPARATOR_OR  = ' tai ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'tammikuu',
        'helmikuu',
        'maaliskuu',
        'huhtikuu',
        'toukokuu',
        'kesäkuu',
        'heinäkuu',
        'elokuu',
        'syyskuu',
        'lokakuu',
        'marraskuu',
        'joulukuu',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

    protected const array GREGORIAN_MONTHS_GENITIVE = [
        '',
        'tammikuuta',
        'helmikuuta',
        'maaliskuuta',
        'huhtikuuta',
        'toukokuuta',
        'kesäkuuta',
        'heinäkuuta',
        'elokuuta',
        'syyskuuta',
        'lokakuuta',
        'marraskuuta',
        'joulukuuta',
    ];

    protected const array GREGORIAN_MONTHS_LOCATIVE = [
        '',
        'tammikuussa',
        'helmikuussa',
        'maaliskuussa',
        'huhtikuussa',
        'toukokuussa',
        'kesäkuussa',
        'heinäkuussa',
        'elokuussa',
        'syyskuussa',
        'lokakuussa',
        'marraskuussa',
        'joulukuussa',
    ];

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = [
        '',
        'tammikuun',
        'helmikuun',
        'maaliskuun',
        'huhtikuun',
        'toukokuun',
        'kesäkuun',
        'heinäkuun',
        'elokuun',
        'syyskuun',
        'lokakuun',
        'marraskuun',
        'joulukuun',
    ];

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'tishrei-kuu',
        'heshvan-kuu',
        'kislev-kuu',
        'tevet-kuu',
        'shvat-kuu',
        'adar I-kuu',
        'adar II-kuu',
        'adar-kuu',
        'nisan-kuu',
        'ijar-kuu',
        'sivan-kuu',
        'tamuz-kuu',
        'av-kuu',
        'elul-kuu',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = [
        '',
        'tishrei-kuuta',
        'heshvan-kuuta',
        'kislev-kuuta',
        'tevet-kuuta',
        'shvat-kuuta',
        'adar I-kuuta',
        'adar II-kuuta',
        'adar-kuuta',
        'nisan-kuuta',
        'ijar-kuuta',
        'sivan-kuuta',
        'tamuz-kuuta',
        'av-kuuta',
        'elul-kuuta',
    ];

    protected const array JEWISH_MONTHS_LOCATIVE = [
        '',
        'tishrei-kuussa',
        'heshvan-kuussa',
        'kislev-kuussa',
        'tevet-kuussa',
        'shvat-kuussa',
        'adar I-kuussa',
        'adar II-kuussa',
        'adar-kuussa',
        'nisan-kuussa',
        'ijar-kuussa',
        'sivan-kuussa',
        'tamuz-kuussa',
        'av-kuussa',
        'elul-kuussa',
    ];

    protected const array JEWISH_MONTHS_INSTRUMENTAL = [
        '',
        'tishrei-kuun',
        'heshvan-kuun',
        'kislev-kuun',
        'tevet-kuun',
        'shvat-kuun',
        'adar I-kuun',
        'adar II-kuun',
        'adar-kuun',
        'nisan-kuun',
        'ijar-kuun',
        'sivan-kuun',
        'tamuz-kuun',
        'av-kuun',
        'elul-kuun',
    ];

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
        'jours complémentaires',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'muharram',
        'safar',
        'rabi al-awwal',
        'rabi al-thani',
        'jumada-al-awwal',
        'jumada-al-sani',
        'rajab',
        'sha`ban',
        'ramadan',
        'shawwal',
        'dhul-qa`da',
        'dhul-hijja',
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
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_ABOVE,
        UTF8::LATIN_CAPITAL_LETTER_A_WITH_DIAERESIS,
        UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
    ];

    protected function assembleDate(string $day, string $month, string $year): string
    {
        return $this->assembleDateDdotMY($day, $month, $year);
    }

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'A' . UTF8::COMBINING_RING_ABOVE => UTF8::LATIN_CAPITAL_LETTER_A_WITH_RING_ABOVE,
            'A' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_CAPITAL_LETTER_A_WITH_DIAERESIS,
            'O' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_CAPITAL_LETTER_O_WITH_DIAERESIS,
            'a' . UTF8::COMBINING_RING_ABOVE => UTF8::LATIN_SMALL_LETTER_A_WITH_RING_ABOVE,
            'a' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_SMALL_LETTER_A_WITH_DIAERESIS,
            'o' . UTF8::COMBINING_DIAERESIS  => UTF8::LATIN_SMALL_LETTER_O_WITH_DIAERESIS,
        ];
    }

    /**
     * Gregorian/Julian month names — case-inflected.
     *
     * @return array<int,string>
     */

    /**
     * @return array<Relationship>
     */
        /** @return array{string, string} */
    private function rel(string $nom, string $gen): array
    {
        return [$nom, '%s ' . $gen];
    }

    /** @return array{string, string} */
    private function iso(int $n, string $nom, string $gen): array
    {
        return [
            str_repeat('iso', $n) . $nom,
            '%s ' . str_repeat('iso', $n) . $gen,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Parents
            Relationship::fixed(...$this->rel('äiti', 'äidin'))->mother(),
            Relationship::fixed(...$this->rel('isä', 'isän'))->father(),
            Relationship::fixed(...$this->rel('vanhempi', 'vanhemman'))->parent(),
            // Children
            Relationship::fixed(...$this->rel('tytär', 'tyttären'))->daughter(),
            Relationship::fixed(...$this->rel('poika', 'pojan'))->son(),
            Relationship::fixed(...$this->rel('lapsi', 'lapsen'))->child(),
            // Siblings
            Relationship::fixed(...$this->rel('isosisko', 'siskon'))->older()->sister(),
            Relationship::fixed(...$this->rel('isoveli', 'veljen'))->older()->brother(),
            Relationship::fixed(...$this->rel('pikkusisko', 'siskon'))->younger()->sister(),
            Relationship::fixed(...$this->rel('pikkuveli', 'veljen'))->younger()->brother(),
            Relationship::fixed(...$this->rel('sisko', 'siskon'))->sister(),
            Relationship::fixed(...$this->rel('veli', 'veljen'))->brother(),
            Relationship::fixed(...$this->rel('sisarus', 'sisaruksen'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->rel('sisarpuoli', 'sisarpuolen'))->parent()->daughter(),
            Relationship::fixed(...$this->rel('velipuoli', 'velipuolen'))->parent()->son(),
            Relationship::fixed(...$this->rel('sisaruspuoli', 'sisaruspuolen'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->rel('äitipuoli', 'äitipuolen'))->parent()->wife(),
            Relationship::fixed(...$this->rel('isäpuoli', 'isäpuolen'))->parent()->husband(),
            Relationship::fixed(...$this->rel('vanhempipuoli', 'vanhempipuolen'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->rel('tytärpuoli', 'tytärpuolen'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->rel('poikapuoli', 'poikapuolen'))->married()->spouse()->son(),
            Relationship::fixed(...$this->rel('lapsipuoli', 'lapsipuolen'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->rel('ex-vaimo', 'ex-vaimon'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->rel('ex-aviomies', 'ex-aviomiehen'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->rel('ex-puoliso', 'ex-puolison'))->divorced()->partner(),
            Relationship::fixed(...$this->rel('morsian', 'morsiamen'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->rel('sulhanen', 'sulhasen'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->rel('vaimo', 'vaimon'))->wife(),
            Relationship::fixed(...$this->rel('aviomies', 'aviomiehen'))->husband(),
            Relationship::fixed(...$this->rel('puoliso', 'puolison'))->spouse(),
            Relationship::fixed(...$this->rel('kumppani', 'kumppanin'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$this->rel('anoppi', 'anopin'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->rel('appi', 'apin'))->married()->spouse()->father(),
            Relationship::fixed(...$this->rel('anoppi', 'anopin'))->spouse()->mother(),
            Relationship::fixed(...$this->rel('appi', 'apin'))->spouse()->father(),
            // Children-in-law
            Relationship::fixed(...$this->rel('miniä', 'miniän'))->child()->wife(),
            Relationship::fixed(...$this->rel('vävy', 'vävyn'))->child()->husband(),
            // Siblings-in-law
            Relationship::fixed(...$this->rel('käly', 'kälyn'))->spouse()->sister(),
            Relationship::fixed(...$this->rel('lanko', 'langon'))->spouse()->brother(),
            Relationship::fixed(...$this->rel('käly', 'kälyn'))->sibling()->wife(),
            Relationship::fixed(...$this->rel('lanko', 'langon'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->rel('isoäiti', 'isoäidin'))->parent()->mother(),
            Relationship::fixed(...$this->rel('isoisä', 'isoisän'))->parent()->father(),
            Relationship::fixed(...$this->rel('isovanhempi', 'isovanhemman'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->rel('tyttärentytär', 'tyttärentyttären'))->daughter()->daughter(),
            Relationship::fixed(...$this->rel('tyttärenpoika', 'tyttärenpojan'))->daughter()->son(),
            Relationship::fixed(...$this->rel('pojanpoika', 'pojanpojan'))->son()->son(),
            Relationship::fixed(...$this->rel('pojantytär', 'pojantyttären'))->son()->daughter(),
            Relationship::fixed(...$this->rel('lapsenlapsi', 'lapsenlapsen'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->rel('täti', 'tädin'))->parent()->sister(),
            Relationship::fixed(...$this->rel('eno', 'enon'))->mother()->brother(),
            Relationship::fixed(...$this->rel('setä', 'sedän'))->father()->brother(),
            Relationship::fixed(...$this->rel('setä', 'sedän'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->rel('veljentytär', 'veljentyttären'))->brother()->daughter(),
            Relationship::fixed(...$this->rel('veljenpoika', 'veljenpojan'))->brother()->son(),
            Relationship::fixed(...$this->rel('siskontytär', 'siskontyttären'))->sister()->daughter(),
            Relationship::fixed(...$this->rel('siskonpoika', 'siskonpojan'))->sister()->son(),
            Relationship::fixed(...$this->rel('sisarenlapsi', 'sisarenlapsen'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$this->rel('serkku', 'serkun'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->rel('serkku', 'serkun'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->rel('serkku', 'serkun'))->parent()->sibling()->child(),
            // Dynamic relationships — great-grandparents and beyond
            Relationship::dynamic(fn (int $n) => $this->iso($n - 1, 'äiti', 'äidin'))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->iso($n - 1, 'isä', 'isän'))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->iso($n - 1, 'vanhempi', 'vanhemman'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(fn (int $n) => $this->iso($n - 2, 'lapsenlapsi', 'lapsenlapsen'))->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->iso($n - 1, 'täti', 'tädin'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->iso($n - 1, 'setä', 'sedän'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->iso($n - 1, 'veljentytär', 'veljentyttären'))->brother()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->iso($n - 1, 'veljenpoika', 'veljenpojan'))->brother()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->iso($n - 1, 'siskontytär', 'siskontyttären'))->sister()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->iso($n - 1, 'siskonpoika', 'siskonpojan'))->sister()->descendant()->male(),
        ];
    }
}
