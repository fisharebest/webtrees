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
    public function relationships(): array
    {
        // Yiddish genitive: "פֿון דער" (fun der, feminine), "פֿון דעם" (fun dem, masculine/neuter)
        $der = static fn (string $s): array => [$s, '%s פֿון דער ' . $s];
        $dem = static fn (string $s): array => [$s, '%s פֿון דעם ' . $s];

        // Dynamic "עלטער-" (elter-) prefix for great- (like German "Ur-")
        $elter = static fn (int $n, string $nom, string $prep): array => [
            str_repeat('עלטער-', $n) . $nom,
            '%s ' . $prep . str_repeat('עלטער-', $n) . $nom,
        ];

        return [
            // Adopted
            Relationship::fixed(...$der('אַדאָפּטיוו-מאַמע'))->adoptive()->mother(),
            Relationship::fixed(...$dem('אַדאָפּטיוו-טאַטע'))->adoptive()->father(),
            Relationship::fixed(...$dem('אַדאָפּטיוו-עלטער'))->adoptive()->parent(),
            Relationship::fixed(...$der('אַדאָפּטירטע טאָכטער'))->adopted()->daughter(),
            Relationship::fixed(...$dem('אַדאָפּטירטער זון'))->adopted()->son(),
            Relationship::fixed(...$dem('אַדאָפּטירט קינד'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$der('פֿלעגע-מאַמע'))->fostering()->mother(),
            Relationship::fixed(...$dem('פֿלעגע-טאַטע'))->fostering()->father(),
            Relationship::fixed(...$dem('פֿלעגע-עלטער'))->fostering()->parent(),
            Relationship::fixed(...$der('פֿלעגע-טאָכטער'))->fostered()->daughter(),
            Relationship::fixed(...$dem('פֿלעגע-זון'))->fostered()->son(),
            Relationship::fixed(...$dem('פֿלעגע-קינד'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$der('מאַמע'))->mother(),
            Relationship::fixed(...$dem('טאַטע'))->father(),
            Relationship::fixed(...$dem('עלטער'))->parent(),
            // Children
            Relationship::fixed(...$der('טאָכטער'))->daughter(),
            Relationship::fixed(...$dem('זון'))->son(),
            Relationship::fixed(...$dem('קינד'))->child(),
            // Siblings
            Relationship::fixed(...$der('צווילינג-שוועסטער'))->twin()->sister(),
            Relationship::fixed(...$dem('צווילינג-ברודער'))->twin()->brother(),
            Relationship::fixed(...$dem('צווילינג'))->twin()->sibling(),
            Relationship::fixed(...$der('עלטערע שוועסטער'))->older()->sister(),
            Relationship::fixed(...$dem('עלטערער ברודער'))->older()->brother(),
            Relationship::fixed(...$der('ייִנגערע שוועסטער'))->younger()->sister(),
            Relationship::fixed(...$dem('ייִנגערער ברודער'))->younger()->brother(),
            Relationship::fixed(...$der('שוועסטער'))->sister(),
            Relationship::fixed(...$dem('ברודער'))->brother(),
            Relationship::fixed(...$dem('געשוויסטער'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$der('האַלב-שוועסטער'))->parent()->daughter(),
            Relationship::fixed(...$dem('האַלב-ברודער'))->parent()->son(),
            Relationship::fixed(...$dem('האַלב-געשוויסטער'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$der('שטיפֿמאַמע'))->parent()->wife(),
            Relationship::fixed(...$dem('שטיפֿטאַטע'))->parent()->husband(),
            Relationship::fixed(...$dem('שטיפֿעלטער'))->parent()->married()->spouse(),
            Relationship::fixed(...$der('שטיפֿטאָכטער'))->married()->spouse()->daughter(),
            Relationship::fixed(...$dem('שטיפֿזון'))->married()->spouse()->son(),
            Relationship::fixed(...$dem('שטיפֿקינד'))->married()->spouse()->child(),
            Relationship::fixed(...$der('שטיפֿשוועסטער'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$dem('שטיפֿברודער'))->parent()->spouse()->son(),
            Relationship::fixed(...$dem('שטיפֿגעשוויסטער'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$der('געוועזענע ווײַב'))->divorced()->partner()->female(),
            Relationship::fixed(...$dem('געוועזענער מאַן'))->divorced()->partner()->male(),
            Relationship::fixed(...$dem('געוועזענער פּאַרטנער'))->divorced()->partner(),
            Relationship::fixed(...$der('כּלה'))->engaged()->partner()->female(),
            Relationship::fixed(...$dem('חתן'))->engaged()->partner()->male(),
            Relationship::fixed(...$der('ווײַב'))->wife(),
            Relationship::fixed(...$dem('מאַן'))->husband(),
            Relationship::fixed(...$dem('פּאַרטנער'))->spouse(),
            Relationship::fixed(...$dem('פּאַרטנער'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$der('שוויגער'))->married()->spouse()->mother(),
            Relationship::fixed(...$dem('שווער'))->married()->spouse()->father(),
            Relationship::fixed(...$dem('שווער/שוויגער'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$der('שנור'))->child()->wife(),
            Relationship::fixed(...$dem('איידעם'))->child()->husband(),
            Relationship::fixed(...$dem('איידעם/שנור'))->child()->married()->spouse(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$der('שוועגערין'))->spouse()->sister(),
            Relationship::fixed(...$dem('שוואָגער'))->spouse()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$der('שוועגערין'))->sibling()->wife(),
            Relationship::fixed(...$dem('שוואָגער'))->sibling()->husband(),
            // Grandparents
            Relationship::fixed(...$der('באָבע'))->parent()->mother(),
            Relationship::fixed(...$dem('זיידע'))->parent()->father(),
            Relationship::fixed(...$dem('באָבע/זיידע'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$der('אייניקלע'))->child()->daughter(),
            Relationship::fixed(...$dem('אייניקל'))->child()->son(),
            Relationship::fixed(...$dem('אייניקל'))->child()->child(),
            // Aunts/uncles
            Relationship::fixed(...$der('מומע'))->parent()->sister(),
            Relationship::fixed(...$dem('פֿעטער'))->parent()->brother(),
            // Nieces/nephews
            Relationship::fixed(...$der('פּלימעניצע'))->sibling()->daughter(),
            Relationship::fixed(...$dem('פּלימעניק'))->sibling()->son(),
            Relationship::fixed(...$dem('פּלימעניק'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$der('קוזינע'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$dem('קוזין'))->parent()->sibling()->son(),
            Relationship::fixed(...$dem('קוזין'))->parent()->sibling()->child(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $elter($n - 1, 'מומע', 'פֿון דער '))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $elter($n - 1, 'פֿעטער', 'פֿון דעם '))->ancestor()->brother(),
            // Dynamic: grand-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $elter($n - 1, 'פּלימעניצע', 'פֿון דער '))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $elter($n - 1, 'פּלימעניק', 'פֿון דעם '))->sibling()->descendant()->male(),
            // Dynamic: ancestors
            Relationship::dynamic(static fn (int $n) => $elter($n - 1, 'באָבע', 'פֿון דער '))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $elter($n - 1, 'זיידע', 'פֿון דעם '))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $elter($n - 1, 'באָבע/זיידע', 'פֿון דעם '))->ancestor(),
            // Dynamic: descendants
            Relationship::dynamic(static fn (int $n) => $elter($n - 2, 'אייניקלע', 'פֿון דער '))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $elter($n - 2, 'אייניקל', 'פֿון דעם '))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $elter($n - 2, 'אייניקל', 'פֿון דעם '))->descendant(),
        ];
    }
}
