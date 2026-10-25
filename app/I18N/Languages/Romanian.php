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

final readonly class Romanian extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::ThreeFormsRomanian;

    protected const string    ENDONYM            = 'romnă';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'ro';
    protected const string    LOCALE_CODE        = 'ro_RO@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    LIST_SEPARATOR_AND = ' și ';
    protected const string    LIST_SEPARATOR_OR  = ' sau ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Ianuarie',
        'Februarie',
        'Martie',
        'Aprilie',
        'Mai',
        'Iunie',
        'Iulie',
        'August',
        'Septembrie',
        'Octombrie',
        'Noiembrie',
        'Decembrie',
    ];
    protected const string    PERCENT_FORMAT     = '%s' . UTF8::NO_BREAK_SPACE . '%%';

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Tișrei',
        'Heșvan',
        'Kislev',
        'Tevet',
        'Șevat',
        'Adar I',
        'Adar II',
        'Adar',
        'Nisan',
        'Iyar',
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
        'zile complementare',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Muharram',
        'Safar',
        'Rabi’ al-awwal',
        'Rabi’ al-thani',
        'Jumada al-awwal',
        'Jumada al-thani',
        'Rajab',
        'Sha’aban',
        'Ramadan',
        'Shawwal',
        'Dhu al-Qi’dah',
        'Dhu al-Hijjah',
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
        UTF8::LATIN_CAPITAL_LETTER_A_WITH_BREVE,
        UTF8::LATIN_CAPITAL_LETTER_A_WITH_CIRCUMFLEX,
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        UTF8::LATIN_CAPITAL_LETTER_I_WITH_CIRCUMFLEX,
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
        UTF8::LATIN_CAPITAL_LETTER_S_WITH_CEDILLA,
        'T',
        'Ţ',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
    ];

    /**
     * Letters with diacritics that are considered distinct letters in this language.
     *
     * @return array<string,string>
     */
    protected function normalizeExceptions(): array
    {
        return [
            'A' . UTF8::COMBINING_BREVE             => UTF8::LATIN_CAPITAL_LETTER_A_WITH_BREVE,
            'A' . UTF8::COMBINING_CIRCUMFLEX_ACCENT => UTF8::LATIN_CAPITAL_LETTER_A_WITH_CIRCUMFLEX,
            'I' . UTF8::COMBINING_CIRCUMFLEX_ACCENT => UTF8::LATIN_CAPITAL_LETTER_I_WITH_CIRCUMFLEX,
            'S' . UTF8::COMBINING_CEDILLA           => UTF8::LATIN_CAPITAL_LETTER_S_WITH_CEDILLA,
            'a' . UTF8::COMBINING_BREVE             => UTF8::LATIN_SMALL_LETTER_A_WITH_BREVE,
            'a' . UTF8::COMBINING_CIRCUMFLEX_ACCENT => UTF8::LATIN_SMALL_LETTER_A_WITH_CIRCUMFLEX,
            'i' . UTF8::COMBINING_CIRCUMFLEX_ACCENT => UTF8::LATIN_SMALL_LETTER_I_WITH_CIRCUMFLEX,
            's' . UTF8::COMBINING_CEDILLA           => UTF8::LATIN_SMALL_LETTER_S_WITH_CEDILLA,
        ];
    }

    /**
     * @return array<Relationship>
     */
        /** @return array{string, string} */
    private function rel(string $nom, string $gen): array
    {
        return [$nom, '%s ' . $gen];
    }

    /** @return array{string, string} */
    private function stra(int $n, string $nom, string $gen): array
    {
        return [
            str_repeat('stră', $n) . $nom,
            '%s ' . str_repeat('stră', $n) . $gen,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->rel('mamă adoptivă', 'mamei adoptive'))->adoptive()->mother(),
            Relationship::fixed(...$this->rel('tată adoptiv', 'tatălui adoptiv'))->adoptive()->father(),
            Relationship::fixed(...$this->rel('părinte adoptiv', 'părintelui adoptiv'))->adoptive()->parent(),
            Relationship::fixed(...$this->rel('fiică adoptivă', 'fiicei adoptive'))->adopted()->daughter(),
            Relationship::fixed(...$this->rel('fiu adoptiv', 'fiului adoptiv'))->adopted()->son(),
            Relationship::fixed(...$this->rel('copil adoptiv', 'copilului adoptiv'))->adopted()->child(),
            // Parents
            Relationship::fixed(...$this->rel('mamă', 'mamei'))->mother(),
            Relationship::fixed(...$this->rel('tată', 'tatălui'))->father(),
            Relationship::fixed(...$this->rel('părinte', 'părintelui'))->parent(),
            // Children
            Relationship::fixed(...$this->rel('fiică', 'fiicei'))->daughter(),
            Relationship::fixed(...$this->rel('fiu', 'fiului'))->son(),
            Relationship::fixed(...$this->rel('copil', 'copilului'))->child(),
            // Siblings
            Relationship::fixed(...$this->rel('soră geamănă', 'surorii geamăne'))->multiple()->sister(),
            Relationship::fixed(...$this->rel('frate geamăn', 'fratelui geamăn'))->multiple()->brother(),
            Relationship::fixed(...$this->rel('geamăn/ă', 'geamănului/ei'))->multiple()->sibling(),
            Relationship::fixed(...$this->rel('soră mai mare', 'surorii mai mari'))->older()->sister(),
            Relationship::fixed(...$this->rel('frate mai mare', 'fratelui mai mare'))->older()->brother(),
            Relationship::fixed(...$this->rel('soră mai mică', 'surorii mai mici'))->younger()->sister(),
            Relationship::fixed(...$this->rel('frate mai mic', 'fratelui mai mic'))->younger()->brother(),
            Relationship::fixed(...$this->rel('soră', 'surorii'))->sister(),
            Relationship::fixed(...$this->rel('frate', 'fratelui'))->brother(),
            Relationship::fixed(...$this->rel('frate/soră', 'fratelui/surorii'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->rel('soră vitregă', 'surorii vitrege'))->parent()->daughter(),
            Relationship::fixed(...$this->rel('frate vitreg', 'fratelui vitreg'))->parent()->son(),
            Relationship::fixed(...$this->rel('frate/soră vitregă', 'fratelui/surorii vitrege'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->rel('mamă vitregă', 'mamei vitrege'))->parent()->wife(),
            Relationship::fixed(...$this->rel('tată vitreg', 'tatălui vitreg'))->parent()->husband(),
            Relationship::fixed(...$this->rel('părinte vitreg', 'părintelui vitreg'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->rel('fiică vitregă', 'fiicei vitrege'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->rel('fiu vitreg', 'fiului vitreg'))->married()->spouse()->son(),
            Relationship::fixed(...$this->rel('copil vitreg', 'copilului vitreg'))->married()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->rel('fostă soție', 'fostei soții'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->rel('fost soț', 'fostului soț'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->rel('fost/ă partener/ă', 'fostului/ei partener/e'))->divorced()->partner(),
            Relationship::fixed(...$this->rel('logodnică', 'logodnicei'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->rel('logodnic', 'logodnicului'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->rel('soție', 'soției'))->wife(),
            Relationship::fixed(...$this->rel('soț', 'soțului'))->husband(),
            Relationship::fixed(...$this->rel('soț/soție', 'soțului/soției'))->spouse(),
            Relationship::fixed(...$this->rel('partener/ă', 'partenerului/ei'))->partner(),
            // In-laws
            Relationship::fixed(...$this->rel('soacră', 'soacrei'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->rel('socru', 'socrului'))->married()->spouse()->father(),
            Relationship::fixed(...$this->rel('socru/soacră', 'socrului/soacrei'))->married()->spouse()->parent(),
            Relationship::fixed(...$this->rel('noră', 'nurorii'))->child()->wife(),
            Relationship::fixed(...$this->rel('ginere', 'ginerelui'))->child()->husband(),
            Relationship::fixed(...$this->rel('ginere/noră', 'ginerelui/nurorii'))->child()->married()->spouse(),
            Relationship::fixed(...$this->rel('cumnată', 'cumnatei'))->spouse()->sister(),
            Relationship::fixed(...$this->rel('cumnat', 'cumnatului'))->spouse()->brother(),
            Relationship::fixed(...$this->rel('cumnată', 'cumnatei'))->sibling()->wife(),
            Relationship::fixed(...$this->rel('cumnat', 'cumnatului'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->rel('bunică', 'bunicii'))->parent()->mother(),
            Relationship::fixed(...$this->rel('bunic', 'bunicului'))->parent()->father(),
            Relationship::fixed(...$this->rel('bunic/ă', 'bunicului/ii'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->rel('nepoată', 'nepoatei'))->child()->daughter(),
            Relationship::fixed(...$this->rel('nepot', 'nepotului'))->child()->son(),
            Relationship::fixed(...$this->rel('nepot/nepoată', 'nepotului/nepoatei'))->child()->child(),
            // Aunts and uncles
            Relationship::fixed(...$this->rel('mătușă', 'mătușii'))->parent()->sister(),
            Relationship::fixed(...$this->rel('unchi', 'unchiului'))->parent()->brother(),
            // Nieces and nephews (same word as grandchild in Romanian)
            Relationship::fixed(...$this->rel('nepoată', 'nepoatei'))->sibling()->daughter(),
            Relationship::fixed(...$this->rel('nepot', 'nepotului'))->sibling()->son(),
            Relationship::fixed(...$this->rel('nepot/nepoată', 'nepotului/nepoatei'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$this->rel('verișoară', 'verișoarei'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->rel('văr', 'vărului'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->rel('văr/verișoară', 'vărului/verișoarei'))->parent()->sibling()->child(),
            // Dynamic relationships — great-grandparents and beyond
            Relationship::dynamic(fn (int $n) => $this->stra($n - 2, 'bunică', 'bunicii'))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->stra($n - 2, 'bunic', 'bunicului'))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->stra($n - 2, 'bunic/ă', 'bunicului/ii'))->ancestor(),
            // Dynamic — great-grandchildren
            Relationship::dynamic(fn (int $n) => $this->stra($n - 2, 'nepoată', 'nepoatei'))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->stra($n - 2, 'nepot', 'nepotului'))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->stra($n - 2, 'nepot/nepoată', 'nepotului/nepoatei'))->descendant(),
            // Dynamic — great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->stra($n - 1, 'mătușă', 'mătușii'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->stra($n - 1, 'unchi', 'unchiului'))->ancestor()->brother(),
            // Dynamic — great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->stra($n - 1, 'nepoată', 'nepoatei'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->stra($n - 1, 'nepot', 'nepotului'))->sibling()->descendant()->male(),
        ];
    }
}
