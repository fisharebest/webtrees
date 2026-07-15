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

use Fisharebest\Webtrees\Enums\Script;
use Fisharebest\Webtrees\Enums\Weekday;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;

final readonly class Korean extends AbstractLanguage
{
    protected const string    ENDONYM            = '한국어';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'ko';
    protected const string    LOCALE_CODE        = 'ko_KR@collation=phonebook';
    protected const Script    SCRIPT             = Script::Kore;
    protected const Weekday   FIRST_DAY          = Weekday::Sunday;
    protected const string    DATE_ABOUT         = '약 %s';
    protected const string    DATE_AFTER         = '%s 이후';
    protected const string    DATE_BEFORE        = '%s 이전';
    protected const string    DATE_BETWEEN_AND   = '%s와 %s 사이';
    protected const string    DATE_CALCULATED    = '계산 된 %s';
    protected const string    DATE_ESTIMATED     = '예상 %s';
    protected const string    DATE_FROM          = '%s 에서';
    protected const string    DATE_FROM_TO       = '%s에서 %s까지';
    protected const string    DATE_INTERPRETED   = '설명 %s';
    protected const string    DATE_TO            = '%s까지';
    protected const string    LIST_SEPARATOR_AND = ' 그리고 ';
    protected const string    LIST_SEPARATOR_OR  = ' 또는 ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        '1월',
        '2월',
        '3월',
        '4월',
        '5월',
        '6월',
        '7월',
        '8월',
        '9월',
        '10월',
        '11월',
        '12월',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        '티슈레이',
        '헤시반',
        '키슬레브',
        '테벳',
        '슈밧',
        '아다르 I',
        '아다르 II',
        '아다르',
        '니산',
        '이야르',
        '시반',
        '타무즈',
        '아브',
        '엘룰',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        '방데미에르',
        '브뤼메르',
        '프리메르',
        '니보스',
        '플뤼비오스',
        '방토스',
        '제르미날',
        '플로레알',
        '프레리알',
        '메시도르',
        '테르미도르',
        '프뤽티도르',
        '보충일',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        '무하람',
        '사파르',
        '라비 알아왈',
        '라비 앗사니',
        '주마다 알울라',
        '주마다 앗사니야',
        '라자브',
        '샤반',
        '라마단',
        '샤왈',
        '둘 카다',
        '둘 히자',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        '파르바르딘',
        '오르디베헤시트',
        '호르다드',
        '티르',
        '모르다드',
        '샤흐리바르',
        '메흐르',
        '아반',
        '아자르',
        '데이',
        '바흐만',
        '에스판드',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

    public function relationships(): array
    {
        // Korean uses 의 (ui) for possessive — "어머니의 %s" = "mother's X"
        $ko = static fn (string $s): array => [$s, $s . '의 %s'];

        return [
            // Adopted
            Relationship::fixed(...$ko('양어머니'))->adoptive()->mother(),
            Relationship::fixed(...$ko('양아버지'))->adoptive()->father(),
            Relationship::fixed(...$ko('양부모'))->adoptive()->parent(),
            Relationship::fixed(...$ko('양딸'))->adopted()->daughter(),
            Relationship::fixed(...$ko('양아들'))->adopted()->son(),
            Relationship::fixed(...$ko('양자녀'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$ko('수양어머니'))->fostering()->mother(),
            Relationship::fixed(...$ko('수양아버지'))->fostering()->father(),
            Relationship::fixed(...$ko('수양부모'))->fostering()->parent(),
            Relationship::fixed(...$ko('수양딸'))->fostered()->daughter(),
            Relationship::fixed(...$ko('수양아들'))->fostered()->son(),
            Relationship::fixed(...$ko('수양자녀'))->fostered()->child(),
            // Step
            Relationship::fixed(...$ko('새어머니'))->parent()->wife(),
            Relationship::fixed(...$ko('새아버지'))->parent()->husband(),
            Relationship::fixed(...$ko('의붓딸'))->married()->spouse()->daughter(),
            Relationship::fixed(...$ko('의붓아들'))->married()->spouse()->son(),
            Relationship::fixed(...$ko('의붓자녀'))->married()->spouse()->child(),
            // Parents
            Relationship::fixed(...$ko('어머니'))->mother(),
            Relationship::fixed(...$ko('아버지'))->father(),
            Relationship::fixed(...$ko('부모'))->parent(),
            // Children
            Relationship::fixed(...$ko('딸'))->daughter(),
            Relationship::fixed(...$ko('아들'))->son(),
            Relationship::fixed(...$ko('자녀'))->child(),
            // Siblings — ego-relative elder terms using selfFemale()
            Relationship::fixed(...$ko('쌍둥이 언니'))->twin()->selfFemale()->older()->sister(),
            Relationship::fixed(...$ko('쌍둥이 누나'))->twin()->older()->sister(),
            Relationship::fixed(...$ko('쌍둥이 여동생'))->twin()->younger()->sister(),
            Relationship::fixed(...$ko('쌍둥이 오빠'))->twin()->selfFemale()->older()->brother(),
            Relationship::fixed(...$ko('쌍둥이 형'))->twin()->older()->brother(),
            Relationship::fixed(...$ko('쌍둥이 남동생'))->twin()->younger()->brother(),
            Relationship::fixed(...$ko('쌍둥이'))->twin()->sibling(),
            Relationship::fixed(...$ko('언니'))->selfFemale()->older()->sister(),
            Relationship::fixed(...$ko('누나'))->older()->sister(),
            Relationship::fixed(...$ko('여동생'))->younger()->sister(),
            Relationship::fixed(...$ko('오빠'))->selfFemale()->older()->brother(),
            Relationship::fixed(...$ko('형'))->older()->brother(),
            Relationship::fixed(...$ko('남동생'))->younger()->brother(),
            Relationship::fixed(...$ko('자매'))->sister(),
            Relationship::fixed(...$ko('형제'))->brother(),
            Relationship::fixed(...$ko('형제자매'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$ko('이복자매'))->father()->daughter(),
            Relationship::fixed(...$ko('이복형제'))->father()->son(),
            Relationship::fixed(...$ko('이부자매'))->mother()->daughter(),
            Relationship::fixed(...$ko('이부형제'))->mother()->son(),
            Relationship::fixed(...$ko('이복형제자매'))->parent()->child(),
            // Partners
            Relationship::fixed(...$ko('전처'))->divorced()->partner()->female(),
            Relationship::fixed(...$ko('전남편'))->divorced()->partner()->male(),
            Relationship::fixed(...$ko('전배우자'))->divorced()->partner(),
            Relationship::fixed(...$ko('약혼녀'))->engaged()->partner()->female(),
            Relationship::fixed(...$ko('약혼자'))->engaged()->partner()->male(),
            Relationship::fixed(...$ko('아내'))->wife(),
            Relationship::fixed(...$ko('남편'))->husband(),
            Relationship::fixed(...$ko('배우자'))->spouse(),
            Relationship::fixed(...$ko('파트너'))->partner(),
            // In-laws — spouse's parents
            Relationship::fixed(...$ko('시어머니'))->husband()->mother(),
            Relationship::fixed(...$ko('시아버지'))->husband()->father(),
            Relationship::fixed(...$ko('장모'))->wife()->mother(),
            Relationship::fixed(...$ko('장인'))->wife()->father(),
            Relationship::fixed(...$ko('시부모'))->married()->spouse()->parent(),
            // In-laws — child's spouse
            Relationship::fixed(...$ko('며느리'))->son()->wife(),
            Relationship::fixed(...$ko('사위'))->daughter()->husband(),
            Relationship::fixed(...$ko('며느리'))->child()->wife(),
            Relationship::fixed(...$ko('사위'))->child()->husband(),
            // In-laws — spouse's siblings
            Relationship::fixed(...$ko('시누이'))->husband()->sister(),
            Relationship::fixed(...$ko('시동생'))->husband()->younger()->brother(),
            Relationship::fixed(...$ko('아주버니'))->husband()->older()->brother(),
            Relationship::fixed(...$ko('시숙'))->husband()->brother(),
            Relationship::fixed(...$ko('처형'))->wife()->older()->sister(),
            Relationship::fixed(...$ko('처제'))->wife()->younger()->sister(),
            Relationship::fixed(...$ko('처남'))->wife()->brother(),
            // In-laws — sibling's spouse
            Relationship::fixed(...$ko('형수'))->brother()->wife(),
            Relationship::fixed(...$ko('매형'))->sister()->husband(),
            // Grandparents — paternal
            Relationship::fixed(...$ko('할머니'))->father()->mother(),
            Relationship::fixed(...$ko('할아버지'))->father()->father(),
            // Grandparents — maternal
            Relationship::fixed(...$ko('외할머니'))->mother()->mother(),
            Relationship::fixed(...$ko('외할아버지'))->mother()->father(),
            // Grandparents — generic
            Relationship::fixed(...$ko('조부모'))->parent()->parent(),
            // Grandchildren — son's children
            Relationship::fixed(...$ko('손녀'))->son()->daughter(),
            Relationship::fixed(...$ko('손자'))->son()->son(),
            // Grandchildren — daughter's children
            Relationship::fixed(...$ko('외손녀'))->daughter()->daughter(),
            Relationship::fixed(...$ko('외손자'))->daughter()->son(),
            // Grandchildren — generic
            Relationship::fixed(...$ko('손자녀'))->child()->child(),
            // Aunts/Uncles — paternal
            Relationship::fixed(...$ko('고모'))->father()->sister(),
            Relationship::fixed(...$ko('고모부'))->father()->sister()->husband(),
            Relationship::fixed(...$ko('큰아버지'))->father()->older()->brother(),
            Relationship::fixed(...$ko('작은아버지'))->father()->younger()->brother(),
            Relationship::fixed(...$ko('삼촌'))->father()->brother(),
            Relationship::fixed(...$ko('숙모'))->father()->brother()->wife(),
            // Aunts/Uncles — maternal
            Relationship::fixed(...$ko('이모'))->mother()->sister(),
            Relationship::fixed(...$ko('이모부'))->mother()->sister()->husband(),
            Relationship::fixed(...$ko('외삼촌'))->mother()->brother(),
            Relationship::fixed(...$ko('외숙모'))->mother()->brother()->wife(),
            // Aunts/Uncles — generic
            Relationship::fixed(...$ko('이모/고모'))->parent()->sister(),
            Relationship::fixed(...$ko('삼촌/외삼촌'))->parent()->brother(),
            // Nieces/Nephews
            Relationship::fixed(...$ko('조카딸'))->brother()->daughter(),
            Relationship::fixed(...$ko('조카'))->brother()->son(),
            Relationship::fixed(...$ko('조카딸'))->sister()->daughter(),
            Relationship::fixed(...$ko('조카'))->sister()->son(),
            Relationship::fixed(...$ko('조카'))->sibling()->child(),
            // Cousins — paternal (father's brother's children = 사촌)
            Relationship::fixed(...$ko('사촌언니'))->selfFemale()->older()->father()->brother()->daughter(),
            Relationship::fixed(...$ko('사촌누나'))->older()->father()->brother()->daughter(),
            Relationship::fixed(...$ko('사촌여동생'))->younger()->father()->brother()->daughter(),
            Relationship::fixed(...$ko('사촌자매'))->father()->brother()->daughter(),
            Relationship::fixed(...$ko('사촌형'))->selfFemale()->older()->father()->brother()->son(),
            Relationship::fixed(...$ko('사촌오빠'))->older()->father()->brother()->son(),
            Relationship::fixed(...$ko('사촌남동생'))->younger()->father()->brother()->son(),
            Relationship::fixed(...$ko('사촌형제'))->father()->brother()->son(),
            // Cousins — generic (all other lines)
            Relationship::fixed(...$ko('사촌'))->parent()->sibling()->child(),
            // Dynamic: great-grandparents
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $ko('증조할머니'),
                4       => $ko('고조할머니'),
                default => $ko(($n - 2) . '대조 할머니'),
            })->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $ko('증조할아버지'),
                4       => $ko('고조할아버지'),
                default => $ko(($n - 2) . '대조 할아버지'),
            })->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $ko('증조부모'),
                4       => $ko('고조부모'),
                default => $ko(($n - 2) . '대조 조상'),
            })->ancestor(),
            // Dynamic: great-grandchildren
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $ko('증손녀'),
                4       => $ko('고손녀'),
                default => $ko(($n - 2) . '대손 손녀'),
            })->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $ko('증손자'),
                4       => $ko('고손자'),
                default => $ko(($n - 2) . '대손 손자'),
            })->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $ko('증손자녀'),
                4       => $ko('고손자녀'),
                default => $ko(($n - 2) . '대손 손자녀'),
            })->descendant(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $ko($n . '대 이모/고모'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $ko($n . '대 삼촌/외삼촌'))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $ko($n . '대 조카딸'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $ko($n . '대 조카'))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $ko($n . '대 조카'))->sibling()->descendant(),
        ];
    }
}
