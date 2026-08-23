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

use Fisharebest\ExtCalendar\CalendarInterface;
use Fisharebest\ExtCalendar\JewishCalendar;
use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\Enums\Script;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

final readonly class Yiddish extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsSingularForOne;

    protected const string    ENDONYM            = 'ייִדיש';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'yi';
    protected const string    LOCALE_CODE        = 'yi_001@collation=phonebook';
    protected const Script    SCRIPT             = Script::Hebr;
    protected const string    DATE_ABOUT         = 'וועגן %s';
    protected const string    DATE_AFTER         = 'נאָך %s';
    protected const string    DATE_BEFORE        = 'פאַר %s';
    protected const string    DATE_BETWEEN_AND   = 'צווישן %s און %s';
    protected const string    DATE_CALCULATED    = 'אויסגערעכנט %s';
    protected const string    DATE_ESTIMATED     = 'ווערט שאַצט %s';
    protected const string    DATE_FROM          = 'פון %s';
    protected const string    DATE_FROM_TO       = 'פון %s צו %s';
    protected const string    DATE_INTERPRETED   = 'אינטערפּרעטאַציע %s';
    protected const string    DATE_TO            = 'צו %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'פֿ"ק';
    protected const string    ERA_CE             = '%s' . UTF8::NO_BREAK_SPACE . 'נ"ק';
    protected const string    LIST_SEPARATOR_AND = ' און ';
    protected const string    LIST_SEPARATOR_OR  = ' אָדער ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'יאַנואַר',
        'פעברואַר',
        'מאַרץ',
        'אַפּריל',
        'מייַ',
        'יוני',
        'יולי',
        'אויגוסט',
        'סעפּטעמבער',
        'אָקטאָבער',
        'נאוועמבער',
        'דעצעמבער',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'תשרי',
        'מרחשון',
        'כסלו',
        'טבת',
        'שבט',
        'אדר א׳',
        'אדר ב׳',
        'אדר',
        'ניסן',
        'אייר',
        'סיון',
        'תמוז',
        'אב',
        'אלול',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'וענדעמיאר',
        'ברימער',
        'פרימער',
        'ניבוז',
        'פליביוז',
        'ונטוז',
        'ז׳רמינאל',
        'פלוראל',
        'פריריאל',
        'מעסידור',
        'תערמידור',
        'פרוקטידור',
        'אַדדיטיאָנאַל טעג',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'מוחראם',
        'צאפר',
        'ראביע אל-אוואל',
        'ראביע אל-ת׳אני',
        'ג׳ומאדא אל-אוואל',
        'ג׳ומאדא אל-ת׳אניה',
        'ראג׳אב',
        'שאבאן',
        'ראמדאן',
        'שאוואל',
        'ז׳ו אל-קעדה',
        'זו אל-חיג׳ה',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'פארבארדין',
        'אורדיבהשת',
        'חורדאד',
        'טיר',
        'מורדאד',
        'שאהריבאר',
        'מעהר',
        'אבאן',
        'אזר',
        'דיי',
        'באהמן',
        'עספנד',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;
    /**
     * @return array<int,string>
     */
    /** @var array<int,string> */
    protected const array ALPHABET = [
        'א',
        'ב',
        'ג',
        'ד',
        'ה',
        'ו',
        'ז',
        'ח',
        'ט',
        'י',
        'כ',
        'ל',
        'מ',
        'נ',
        'ס',
        'ע',
        'פ',
        'צ',
        'ק',
        'ר',
        'ש',
        'ת',
    ];

    public function calendar(): CalendarInterface
    {
        return new JewishCalendar();
    }

    /**
     * @return array<Relationship>
     */
        /** @return array{string, string} */
    private function der(string $s): array
    {
        return [$s, '%s פֿון דער ' . $s];
    }

    /** @return array{string, string} */
    private function dem(string $s): array
    {
        return [$s, '%s פֿון דעם ' . $s];
    }

    /** @return array{string, string} */
    private function elter(int $n, string $nom, string $prep): array
    {
        return [
            str_repeat('עלטער-', $n) . $nom,
            '%s ' . $prep . str_repeat('עלטער-', $n) . $nom,
        ];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->der('אַדאָפּטיוו-מאַמע'))->adoptive()->mother(),
            Relationship::fixed(...$this->dem('אַדאָפּטיוו-טאַטע'))->adoptive()->father(),
            Relationship::fixed(...$this->dem('אַדאָפּטיוו-עלטער'))->adoptive()->parent(),
            Relationship::fixed(...$this->der('אַדאָפּטירטע טאָכטער'))->adopted()->daughter(),
            Relationship::fixed(...$this->dem('אַדאָפּטירטער זון'))->adopted()->son(),
            Relationship::fixed(...$this->dem('אַדאָפּטירט קינד'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$this->der('פֿלעגע-מאַמע'))->fostering()->mother(),
            Relationship::fixed(...$this->dem('פֿלעגע-טאַטע'))->fostering()->father(),
            Relationship::fixed(...$this->dem('פֿלעגע-עלטער'))->fostering()->parent(),
            Relationship::fixed(...$this->der('פֿלעגע-טאָכטער'))->fostered()->daughter(),
            Relationship::fixed(...$this->dem('פֿלעגע-זון'))->fostered()->son(),
            Relationship::fixed(...$this->dem('פֿלעגע-קינד'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$this->der('מאַמע'))->mother(),
            Relationship::fixed(...$this->dem('טאַטע'))->father(),
            Relationship::fixed(...$this->dem('עלטער'))->parent(),
            // Children
            Relationship::fixed(...$this->der('טאָכטער'))->daughter(),
            Relationship::fixed(...$this->dem('זון'))->son(),
            Relationship::fixed(...$this->dem('קינד'))->child(),
            // Siblings
            Relationship::fixed(...$this->der('צווילינג-שוועסטער'))->multiple()->sister(),
            Relationship::fixed(...$this->dem('צווילינג-ברודער'))->multiple()->brother(),
            Relationship::fixed(...$this->dem('צווילינג'))->multiple()->sibling(),
            Relationship::fixed(...$this->der('עלטערע שוועסטער'))->older()->sister(),
            Relationship::fixed(...$this->dem('עלטערער ברודער'))->older()->brother(),
            Relationship::fixed(...$this->der('ייִנגערע שוועסטער'))->younger()->sister(),
            Relationship::fixed(...$this->dem('ייִנגערער ברודער'))->younger()->brother(),
            Relationship::fixed(...$this->der('שוועסטער'))->sister(),
            Relationship::fixed(...$this->dem('ברודער'))->brother(),
            Relationship::fixed(...$this->dem('געשוויסטער'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->der('האַלב-שוועסטער'))->parent()->daughter(),
            Relationship::fixed(...$this->dem('האַלב-ברודער'))->parent()->son(),
            Relationship::fixed(...$this->dem('האַלב-געשוויסטער'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$this->der('שטיפֿמאַמע'))->parent()->wife(),
            Relationship::fixed(...$this->dem('שטיפֿטאַטע'))->parent()->husband(),
            Relationship::fixed(...$this->dem('שטיפֿעלטער'))->parent()->married()->spouse(),
            Relationship::fixed(...$this->der('שטיפֿטאָכטער'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->dem('שטיפֿזון'))->married()->spouse()->son(),
            Relationship::fixed(...$this->dem('שטיפֿקינד'))->married()->spouse()->child(),
            Relationship::fixed(...$this->der('שטיפֿשוועסטער'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$this->dem('שטיפֿברודער'))->parent()->spouse()->son(),
            Relationship::fixed(...$this->dem('שטיפֿגעשוויסטער'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$this->der('געוועזענע ווײַב'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->dem('געוועזענער מאַן'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->dem('געוועזענער פּאַרטנער'))->divorced()->partner(),
            Relationship::fixed(...$this->der('כּלה'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->dem('חתן'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->der('ווײַב'))->wife(),
            Relationship::fixed(...$this->dem('מאַן'))->husband(),
            Relationship::fixed(...$this->dem('פּאַרטנער'))->spouse(),
            Relationship::fixed(...$this->dem('פּאַרטנער'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$this->der('שוויגער'))->married()->spouse()->mother(),
            Relationship::fixed(...$this->dem('שווער'))->married()->spouse()->father(),
            Relationship::fixed(...$this->dem('שווער/שוויגער'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$this->der('שנור'))->child()->wife(),
            Relationship::fixed(...$this->dem('איידעם'))->child()->husband(),
            Relationship::fixed(...$this->dem('איידעם/שנור'))->child()->married()->spouse(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$this->der('שוועגערין'))->spouse()->sister(),
            Relationship::fixed(...$this->dem('שוואָגער'))->spouse()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$this->der('שוועגערין'))->sibling()->wife(),
            Relationship::fixed(...$this->dem('שוואָגער'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$this->der('באָבע'))->parent()->mother(),
            Relationship::fixed(...$this->dem('זיידע'))->parent()->father(),
            Relationship::fixed(...$this->dem('באָבע/זיידע'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$this->der('אייניקלע'))->child()->daughter(),
            Relationship::fixed(...$this->dem('אייניקל'))->child()->son(),
            Relationship::fixed(...$this->dem('אייניקל'))->child()->child(),
            // Aunts/uncles
            Relationship::fixed(...$this->der('מומע'))->parent()->sister(),
            Relationship::fixed(...$this->dem('פֿעטער'))->parent()->brother(),
            // Nieces/nephews
            Relationship::fixed(...$this->der('פּלימעניצע'))->sibling()->daughter(),
            Relationship::fixed(...$this->dem('פּלימעניק'))->sibling()->son(),
            Relationship::fixed(...$this->dem('פּלימעניק'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$this->der('קוזינע'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->dem('קוזין'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->dem('קוזין'))->parent()->sibling()->child(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->elter($n - 1, 'מומע', 'פֿון דער '))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->elter($n - 1, 'פֿעטער', 'פֿון דעם '))->ancestor()->brother(),
            // Dynamic: grand-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->elter($n - 1, 'פּלימעניצע', 'פֿון דער '))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->elter($n - 1, 'פּלימעניק', 'פֿון דעם '))->sibling()->descendant()->male(),
            // Dynamic: ancestors
            Relationship::dynamic(fn (int $n) => $this->elter($n - 1, 'באָבע', 'פֿון דער '))->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => $this->elter($n - 1, 'זיידע', 'פֿון דעם '))->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => $this->elter($n - 1, 'באָבע/זיידע', 'פֿון דעם '))->ancestor(),
            // Dynamic: descendants
            Relationship::dynamic(fn (int $n) => $this->elter($n - 2, 'אייניקלע', 'פֿון דער '))->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->elter($n - 2, 'אייניקל', 'פֿון דעם '))->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->elter($n - 2, 'אייניקל', 'פֿון דעם '))->descendant(),
        ];
    }
}
