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

final readonly class Kurdish extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'kurd';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'ku';
    protected const string    LOCALE_CODE        = 'ku_TR@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = '.';
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const string    DATE_ABOUT         = 'Der dorê %s';
    protected const string    DATE_AFTER         = 'paşê %s';
    protected const string    DATE_BEFORE        = 'Berya/berê %s';
    protected const string    DATE_BETWEEN_AND   = 'Navbera %s û %s';
    protected const string    DATE_CALCULATED    = 'Çortik %s';
    protected const string    DATE_ESTIMATED     = 'Texmînî %s';
    protected const string    DATE_FROM          = 'ji %s';
    protected const string    DATE_FROM_TO       = 'Ji %s heya %s';
    protected const string    DATE_INTERPRETED   = 'Şirovekirinî %s';
    protected const string    DATE_TO            = 'heya/ ta %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'BZ';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'PZ';
    protected const string    LIST_SEPARATOR_AND = ' û ';
    protected const string    LIST_SEPARATOR_OR  = ' an ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'Rêbendan',
        'Reşemî',
        'Adar',
        'Avrêl',
        'Gulan',
        'Pûşper',
        'Tîrmeh',
        'Gelawêj',
        'Rezber',
        'Kewçêr',
        'Sermawez',
        'Berfanbar',
    ];
    protected const string    PERCENT_FORMAT     = '%%%s';

    protected const array GREGORIAN_MONTHS_LOCATIVE = [
        '',
        'Çile',
        'Sibat',
        'Adar',
        'Nîsan',
        'Gulan',
        'Hezîran',
        'Tîrmeh',
        'Gelawêj',
        'Îlon',
        'Cotmeh',
        'Sermawez',
        'Berfanbar',
    ];

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = [
        '',
        'Rêbendan',
        'Reşemî',
        'Adar',
        'Nîsan',
        'Gulan',
        'Pûşper',
        'Tîrmeh',
        'Gelawêj',
        'Îlon',
        'Cotmeh',
        'Sermawez',
        'Berfanbar',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Tishrei',
        'Heshvan',
        'Kislev',
        'Tevet',
        'Shevat',
        'Adar I',
        'Adar II',
        'Adar',
        'Nissan',
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
        'sertayê rojan',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Muherrem',
        'Sefer',
        'Rebîûl-ewwel',
        'Rebîûl-axir',
        'Cemazîyel-ewwel',
        'Cemazîyel-axir',
        'Receb',
        'Şeban',
        'Ramazan',
        'Şewwal',
        'Zîlqade',
        'Zîlhicce',
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

    /** @return array{string, string} */
    private function ku(string $s): array
    {
        return [$s, $s . ' yê %s'];
    }

    /** @return array{string, string} */
    private function great(int $n, string $nominative): array
    {
        $prefix = str_repeat('kal', $n);

        return [$prefix . $nominative, $prefix . $nominative . ' yê %s'];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {

        return [
            // Parents
            Relationship::fixed(...$this->ku('dayik'))->mother(),
            Relationship::fixed(...$this->ku('bav'))->father(),
            Relationship::fixed(...$this->ku('dêûbav'))->parent(),
            // Children
            Relationship::fixed(...$this->ku('keç'))->daughter(),
            Relationship::fixed(...$this->ku('kur'))->son(),
            Relationship::fixed(...$this->ku('zarok'))->child(),
            // Siblings
            Relationship::fixed(...$this->ku('xwişk'))->sister(),
            Relationship::fixed(...$this->ku('bira'))->brother(),
            Relationship::fixed(...$this->ku('xwişkûbira'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->ku('nîvxwişk'))->parent()->daughter(),
            Relationship::fixed(...$this->ku('nîvbira'))->parent()->son(),
            Relationship::fixed(...$this->ku('nîvxwişkûbira'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->ku('diya'))->parent()->wife(),
            Relationship::fixed(...$this->ku('bavê'))->parent()->husband(),
            Relationship::fixed(...$this->ku('dêûbavê zincîrî'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->ku('keça zincîrî'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->ku('kurê zincîrî'))->married()->spouse()->son(),
            Relationship::fixed(...$this->ku('zarokê zincîrî'))->married()->spouse()->child(),
            Relationship::fixed(...$this->ku('xwişka zincîrî'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$this->ku('birayê zincîrî'))->parent()->spouse()->son(),
            Relationship::fixed(...$this->ku('xwişkûbirayê zincîrî'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->ku('hevjîna berê'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->ku('hevjînê berê'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->ku('hevjînê berê'))->divorced()->partner(),
            Relationship::fixed(...$this->ku('destgirtî'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->ku('destgirtî'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->ku('jin'))->wife(),
            Relationship::fixed(...$this->ku('mêr'))->husband(),
            Relationship::fixed(...$this->ku('hevjîn'))->spouse(),
            Relationship::fixed(...$this->ku('hevkar'))->partner(),
            // In-laws
            Relationship::fixed(...$this->ku('xesû'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->ku('xezûr'))->married()->spouse()->father(),
            Relationship::fixed(...$this->ku('xesûxezûr'))->married()->spouse()->parent(),
            Relationship::fixed(...$this->ku('bûk'))->child()->wife(),
            Relationship::fixed(...$this->ku('zava'))->child()->husband(),
            Relationship::fixed(...$this->ku('jinbira'))->spouse()->sister(),
            Relationship::fixed(...$this->ku('hêvir'))->spouse()->brother(),
            Relationship::fixed(...$this->ku('bûk'))->sibling()->wife(),
            Relationship::fixed(...$this->ku('hêvir'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->ku('dapîr'))->parent()->mother(),
            Relationship::fixed(...$this->ku('bapîr'))->parent()->father(),
            Relationship::fixed(...$this->ku('dapîr û bapîr'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->ku('nevîça'))->child()->daughter(),
            Relationship::fixed(...$this->ku('nevî'))->child()->son(),
            Relationship::fixed(...$this->ku('nevî'))->child()->child(),
            // Aunts and uncles — maternal/paternal
            Relationship::fixed(...$this->ku('xaltî'))->mother()->sister(),
            Relationship::fixed(...$this->ku('xal'))->mother()->brother(),
            Relationship::fixed(...$this->ku('met'))->father()->sister(),
            Relationship::fixed(...$this->ku('ap'))->father()->brother(),
            Relationship::fixed(...$this->ku('xaltî'))->parent()->sister(),
            Relationship::fixed(...$this->ku('ap'))->parent()->brother(),
            // Nieces and nephews
            Relationship::fixed(...$this->ku('keça xwişk/birayî'))->sibling()->daughter(),
            Relationship::fixed(...$this->ku('kurê xwişk/birayî'))->sibling()->son(),
            Relationship::fixed(...$this->ku('zarokê xwişk/birayî'))->sibling()->child(),
            // Cousins — flat
            Relationship::fixed(...$this->ku('pismam'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->ku('pismam'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->ku('pismam'))->parent()->sibling()->child(),
            // Dynamic — great-grandparents and beyond
            Relationship::dynamic(fn (int $n) => $this->great($n - 2, 'dapîr'))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 2, 'bapîr'))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 2, 'bapîr'))->ancestor(),
            Relationship::dynamic(fn (int $n) => $this->great($n - 2, 'nevî'))->descendant(),
        ];
    }
}
