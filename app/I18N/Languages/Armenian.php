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
use Fisharebest\Webtrees\Enums\Script;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;
use Fisharebest\Webtrees\Enums\PluralRule;

final readonly class Armenian extends AbstractLanguage
{
    protected const PluralRule PLURAL_RULE = PluralRule::TwoFormsPluralForMoreThanOne;

    protected const string    ENDONYM            = 'հայերեն';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'hy';
    protected const string    LOCALE_CODE        = 'hy_AM@collation=phonebook';
    protected const string    DIGITS_SEPARATOR   = UTF8::NO_BREAK_SPACE;
    protected const string    DECIMAL_SYMBOL     = ',';
    protected const Script    SCRIPT             = Script::Armn;
    protected const string    DATE_ABOUT         = 'մոտ %s';
    protected const string    DATE_AFTER         = '%s - ից հետո';
    protected const string    DATE_BEFORE        = '%s - ից առաջ';
    protected const string    DATE_BETWEEN_AND   = '%s - ի և %s -ի միջև';
    protected const string    DATE_CALCULATED    = 'հաշվարկված %s';
    protected const string    DATE_ESTIMATED     = 'գնահատված %s';
    protected const string    DATE_FROM          = '%s - ից';
    protected const string    DATE_FROM_TO       = '%s - ից մինչև %s';
    protected const string    DATE_INTERPRETED   = 'մեկնաբանված %s';
    protected const string    DATE_TO            = 'դեպի %s';
    protected const string    ERA_BCE            = '%s' . UTF8::NO_BREAK_SPACE . 'մ.թ.ա';
    protected const string    LIST_SEPARATOR_AND = ' և ';
    protected const string    LIST_SEPARATOR_OR  = ' կամ ';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'հունվար',
        'փետրվար',
        'մարտ',
        'ապրիլ',
        'մայիս',
        'հունիս',
        'հուլիս',
        'օգոստոս',
        'սեպտեմբեր',
        'հոկտեմբեր',
        'նոյեմբեր',
        'դեկտեմբեր',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'Տիշրեյ',
        'Հեշվան',
        'Կիսլև',
        'Տեւետ',
        'Շևաթ',
        'Ադար Ա',
        'Ադար Բ',
        'Ադար',
        'Նիսան',
        'Իյար',
        'Սիվան',
        'Թամուզ',
        'Ավ',
        'Էլուլ',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'Վանդեմիաիր',
        'Բրումեյր',
        'Ֆրիմեր',
        'Նիվոզե',
        'Պլյուվիոզ',
        'Վենտոզե',
        'Ջերմինալ',
        'Ֆլորեալ',
        'Պրայրիալ',
        'Մեսիդոր',
        'Թերմիդոր',
        'Ֆրուկտիդոր',
        'լրացնող օրեր',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'Մուհարամ',
        'Սաֆար',
        'Ռաբի ալ-Ավվալ',
        'Ռաբի աս-Սանի',
        'Ջումադա ալ-Ավվալ',
        'Ջումադա աս-Սանի',
        'Ռաջաբ',
        'Շաաբան',
        'Ռամադան',
        'Շավվալ',
        'Զուլ-Քաադա',
        'Զուլ-Հիջա',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'Ֆարվարդին',
        'Օրդիբեհեշթ',
        'Խորդադ',
        'Տիր',
        'Մորդադ',
        'Շահրիվար',
        'Մեհր',
        'Աբան',
        'Ազար',
        'Դեյ',
        'Բահման',
        'Էսֆանդ',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        // Armenian genitive uses suffix -ի (after consonants) or -յի (after vowels)
        // We use explicit nominative and genitive forms
        $hy = static fn (string $nom, string $gen): array => [$nom, $gen . ' %s'];

        return [
            // Adopted
            Relationship::fixed(...$hy('խորթ մայր', 'խորթ մոր'))->adoptive()->mother(),
            Relationship::fixed(...$hy('խորթ հայր', 'խորթ հոր'))->adoptive()->father(),
            Relationship::fixed(...$hy('խորթ ծնող', 'խորթ ծնողի'))->adoptive()->parent(),
            Relationship::fixed(...$hy('որդեգրուհի դուստր', 'որդեգրուհի դստրի'))->adopted()->daughter(),
            Relationship::fixed(...$hy('որդեգրուհի որդի', 'որդեգրուհի որդու'))->adopted()->son(),
            Relationship::fixed(...$hy('որդեգրուհի զավակ', 'որդեգրուհի զավակի'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$hy('խնամայր', 'խնամոր'))->fostering()->mother(),
            Relationship::fixed(...$hy('խնահայր', 'խնահոր'))->fostering()->father(),
            Relationship::fixed(...$hy('խնածնող', 'խնածնողի'))->fostering()->parent(),
            Relationship::fixed(...$hy('խնամի դուստր', 'խնամի դստրի'))->fostered()->daughter(),
            Relationship::fixed(...$hy('խնամի որդի', 'խնամի որդու'))->fostered()->son(),
            Relationship::fixed(...$hy('խնամի զավակ', 'խնամի զավակի'))->fostered()->child(),
            // Parents
            Relationship::fixed(...$hy('մայր', 'մոր'))->mother(),
            Relationship::fixed(...$hy('հայր', 'հոր'))->father(),
            Relationship::fixed(...$hy('ծնող', 'ծնողի'))->parent(),
            // Children
            Relationship::fixed(...$hy('դուստր', 'դստրի'))->daughter(),
            Relationship::fixed(...$hy('որդի', 'որդու'))->son(),
            Relationship::fixed(...$hy('զավակ', 'զավակի'))->child(),
            // Siblings
            Relationship::fixed(...$hy('երկվորյակ քույր', 'երկվորյակ քուրոջ'))->twin()->sister(),
            Relationship::fixed(...$hy('երկվորյակ եղբայր', 'երկվորյակ եղբոր'))->twin()->brother(),
            Relationship::fixed(...$hy('երկվորյակ', 'երկվորյակի'))->twin()->sibling(),
            Relationship::fixed(...$hy('քույր', 'քուրոջ'))->sister(),
            Relationship::fixed(...$hy('եղբայր', 'եղբոր'))->brother(),
            Relationship::fixed(...$hy('քույր/եղբայր', 'քուրոջ/եղբոր'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$hy('հորեղբայր քույր', 'հորեղբոր քուրոջ'))->father()->daughter(),
            Relationship::fixed(...$hy('հորեղբայր եղբայր', 'հորեղբոր եղբոր'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$hy('մորեղբայր քույր', 'մորեղբոր քուրոջ'))->mother()->daughter(),
            Relationship::fixed(...$hy('մորեղբայր եղբայր', 'մորեղբոր եղբոր'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$hy('խորթ քույր', 'խորթ քուրոջ'))->parent()->daughter(),
            Relationship::fixed(...$hy('խորթ եղբայր', 'խորթ եղբոր'))->parent()->son(),
            Relationship::fixed(...$hy('քույր/եղբայր', 'քուրոջ/եղբոր'))->parent()->child(),
            // Stepfamily
            Relationship::fixed(...$hy('խորթ մայր', 'խորթ մոր'))->parent()->wife(),
            Relationship::fixed(...$hy('խորթ հայր', 'խորթ հոր'))->parent()->husband(),
            Relationship::fixed(...$hy('խորթ դուստր', 'խորթ դստրի'))->married()->spouse()->daughter(),
            Relationship::fixed(...$hy('խորթ որդի', 'խորթ որդու'))->married()->spouse()->son(),
            Relationship::fixed(...$hy('խորթ զավակ', 'խորթ զավակի'))->married()->spouse()->child(),
            Relationship::fixed(...$hy('խորթ դուստր', 'խորթ դստրի'))->parent()->spouse()->daughter(),
            Relationship::fixed(...$hy('խորթ որդի', 'խորթ որդու'))->parent()->spouse()->son(),
            Relationship::fixed(...$hy('խորթ զավակ', 'խորթ զավակի'))->parent()->spouse()->child(),
            // Partners
            Relationship::fixed(...$hy('նախկին կին', 'նախկին կնոջ'))->divorced()->partner()->female(),
            Relationship::fixed(...$hy('նախկին ամուսին', 'նախկին ամուսնու'))->divorced()->partner()->male(),
            Relationship::fixed(...$hy('նախկին կողակից', 'նախկին կողակցի'))->divorced()->partner(),
            Relationship::fixed(...$hy('նշանած', 'նշանածի'))->engaged()->partner()->female(),
            Relationship::fixed(...$hy('նշանած', 'նշանածի'))->engaged()->partner()->male(),
            Relationship::fixed(...$hy('կին', 'կնոջ'))->wife(),
            Relationship::fixed(...$hy('ամուսին', 'ամուսնու'))->husband(),
            Relationship::fixed(...$hy('կողակից', 'կողակցի'))->spouse(),
            Relationship::fixed(...$hy('զուգակից', 'զուգակցի'))->partner(),
            // In-laws (spouse's parents)
            Relationship::fixed(...$hy('սկեսուր', 'սկեսուրի'))->husband()->mother(),
            Relationship::fixed(...$hy('սկեսրայր', 'սկեսրոջ'))->husband()->father(),
            Relationship::fixed(...$hy('զոքանչ', 'զոքանչի'))->wife()->mother(),
            Relationship::fixed(...$hy('աներ', 'աների'))->wife()->father(),
            Relationship::fixed(...$hy('ծնող կողակցի', 'ծնող կողակցի'))->married()->spouse()->parent(),
            // In-laws (child's spouse)
            Relationship::fixed(...$hy('հարս', 'հարսի'))->child()->wife(),
            Relationship::fixed(...$hy('փեսա', 'փեսայի'))->child()->husband(),
            Relationship::fixed(...$hy('հարս/փեսա', 'հարսի/փեսայի'))->child()->married()->spouse(),
            // In-laws (spouse's siblings)
            Relationship::fixed(...$hy('տակերոջ', 'տակերոջի'))->husband()->sister(),
            Relationship::fixed(...$hy('տագեր', 'տագրի'))->husband()->brother(),
            Relationship::fixed(...$hy('քենի', 'քենու'))->wife()->sister(),
            Relationship::fixed(...$hy('աներոջ', 'աներոջի'))->wife()->brother(),
            // In-laws (sibling's spouse)
            Relationship::fixed(...$hy('հարս', 'հարսի'))->brother()->wife(),
            Relationship::fixed(...$hy('փեսա', 'փեսայի'))->sister()->husband(),
            Relationship::fixed(...$hy('կողակից կողակցի', 'կողակից կողակցի'))->sibling()->spouse(),
            // Grandparents
            Relationship::fixed(...$hy('տատիկ', 'տատիկի'))->parent()->mother(),
            Relationship::fixed(...$hy('պապիկ', 'պապիկի'))->parent()->father(),
            Relationship::fixed(...$hy('տատիկ/պապիկ', 'տատիկի/պապիկի'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$hy('թոռ', 'թոռի'))->child()->daughter(),
            Relationship::fixed(...$hy('թոռ', 'թոռի'))->child()->son(),
            Relationship::fixed(...$hy('թոռ', 'թոռի'))->child()->child(),
            // Aunts
            Relationship::fixed(...$hy('հորաքույր', 'հորաքուրոջ'))->father()->sister(),
            Relationship::fixed(...$hy('մորաքույր', 'մորաքուրոջ'))->mother()->sister(),
            Relationship::fixed(...$hy('հորաքույր/մորաքույր', 'հորաքուրոջ/մորաքուրոջ'))->parent()->sister(),
            // Uncles
            Relationship::fixed(...$hy('հորեղբայր', 'հորեղբոր'))->father()->brother(),
            Relationship::fixed(...$hy('քեռի', 'քեռու'))->mother()->brother(),
            Relationship::fixed(...$hy('հորեղբայր/քեռի', 'հորեղբոր/քեռու'))->parent()->brother(),
            // Nieces/nephews through brother
            Relationship::fixed(...$hy('եղբոր դուստր', 'եղբոր դստրի'))->brother()->daughter(),
            Relationship::fixed(...$hy('եղբոր որդի', 'եղբոր որդու'))->brother()->son(),
            // Nieces/nephews through sister
            Relationship::fixed(...$hy('քուրոջ դուստր', 'քուրոջ դստրի'))->sister()->daughter(),
            Relationship::fixed(...$hy('քուրոջ որդի', 'քուրոջ որդու'))->sister()->son(),
            // Generic niece/nephew
            Relationship::fixed(...$hy('քուրոջ/եղբոր դուստր', 'քուրոջ/եղբոր դստրի'))->sibling()->daughter(),
            Relationship::fixed(...$hy('քուրոջ/եղբոր որդի', 'քուրոջ/եղբոր որդու'))->sibling()->son(),
            // Cousins
            Relationship::fixed(...$hy('զարմիկ քույր', 'զարմիկի քուրոջ'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$hy('զարմիկ եղբայր', 'զարմիկի եղբոր'))->parent()->sibling()->son(),
            Relationship::fixed(...$hy('զարմիկ', 'զարմիկի'))->parent()->sibling()->child(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $hy('մեծ հորաքույր/մորաքույր' . ($n > 2 ? ' ×' . ($n - 1) : ''), 'մեծ հորաքուրոջ/մորաքուրոջ' . ($n > 2 ? ' ×' . ($n - 1) : '')))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $hy('մեծ հորեղբայր/քեռի' . ($n > 2 ? ' ×' . ($n - 1) : ''), 'մեծ հորեղբոր/քեռու' . ($n > 2 ? ' ×' . ($n - 1) : '')))->ancestor()->brother(),
            // Dynamic: grand-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $hy('մեծ քուրոջ/եղբոր դուստր' . ($n > 2 ? ' ×' . ($n - 1) : ''), 'մեծ քուրոջ/եղբոր դստրի' . ($n > 2 ? ' ×' . ($n - 1) : '')))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $hy('մեծ քուրոջ/եղբոր որդի' . ($n > 2 ? ' ×' . ($n - 1) : ''), 'մեծ քուրոջ/եղբոր որդու' . ($n > 2 ? ' ×' . ($n - 1) : '')))->sibling()->descendant()->male(),
            // Dynamic: ancestors
            Relationship::dynamic(static fn (int $n) => $hy('մեծ տատիկ' . ($n > 3 ? ' ×' . ($n - 2) : ''), 'մեծ տատիկի' . ($n > 3 ? ' ×' . ($n - 2) : '')))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $hy('մեծ պապիկ' . ($n > 3 ? ' ×' . ($n - 2) : ''), 'մեծ պապիկի' . ($n > 3 ? ' ×' . ($n - 2) : '')))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $hy('նախնի' . ($n > 3 ? ' ×' . ($n - 2) : ''), 'նախնու' . ($n > 3 ? ' ×' . ($n - 2) : '')))->ancestor(),
            // Dynamic: descendants
            Relationship::dynamic(static fn (int $n) => $hy('մեծ թոռ' . ($n > 3 ? ' ×' . ($n - 2) : ''), 'մեծ թոռի' . ($n > 3 ? ' ×' . ($n - 2) : '')))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $hy('մեծ թոռ' . ($n > 3 ? ' ×' . ($n - 2) : ''), 'մեծ թոռի' . ($n > 3 ? ' ×' . ($n - 2) : '')))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $hy('մեծ թոռ' . ($n > 3 ? ' ×' . ($n - 2) : ''), 'մեծ թոռի' . ($n > 3 ? ' ×' . ($n - 2) : '')))->descendant(),
        ];
    }
}
