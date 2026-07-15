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

final readonly class ChineseSimplified extends AbstractLanguage
{
    protected const string    ENDONYM            = '简体中文';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'zh-Hans';
    protected const string    LOCALE_CODE        = 'zh_CN@collation=phonebook';
    protected const Script    SCRIPT             = Script::Hans;
    protected const string    DATE_ABOUT         = '关于 %s';
    protected const string    DATE_AFTER         = '在%s 之后';
    protected const string    DATE_BEFORE        = '在%s 之前';
    protected const string    DATE_BETWEEN_AND   = '在%s和%s 间';
    protected const string    DATE_CALCULATED    = '计算出 %s';
    protected const string    DATE_ESTIMATED     = '估计于%s年';
    protected const string    DATE_FROM          = '从 %s';
    protected const string    DATE_FROM_TO       = '从 %s 到 %s';
    protected const string    DATE_INTERPRETED   = '解释 %s';
    protected const string    DATE_TO            = '到 %s';
    protected const string    ERA_BCE            = '公元前%s年';
    protected const string    ERA_CE             = 'AD' . UTF8::NO_BREAK_SPACE . '%s';
    protected const string    LIST_SEPARATOR     = '、';
    protected const string    LIST_SEPARATOR_AND = '和';
    protected const string    LIST_SEPARATOR_OR  = '或';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '10',
        '11',
        '12',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '7',
        '8',
        '9',
        '10',
        '11',
        '12',
        '13',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '10',
        '11',
        '12',
        '13',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '10',
        '11',
        '12',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '10',
        '11',
        '12',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

    public function assembleDate(string $day, string $month, string $year): string
    {
        $parts = [];

        if ($year !== '') {
            $parts[] = $year . '年';
        }

        if ($month !== '') {
            $parts[] = $month . '月';
        }

        if ($day !== '') {
            $parts[] = $day . '日';
        }

        return implode('', $parts);
    }

    public function dateOrder(): string
    {
        return 'YMD';
    }

    public function relationships(): array
    {
        // Chinese uses 的 (de) for possessive — "母亲的%s" = "mother's X"
        $zh = static fn (string $s): array => [$s, $s . '的%s'];

        return [
            // Adopted
            Relationship::fixed(...$zh('养母'))->adoptive()->mother(),
            Relationship::fixed(...$zh('养父'))->adoptive()->father(),
            Relationship::fixed(...$zh('养父母'))->adoptive()->parent(),
            Relationship::fixed(...$zh('养女'))->adopted()->daughter(),
            Relationship::fixed(...$zh('养子'))->adopted()->son(),
            Relationship::fixed(...$zh('养子女'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$zh('寄养母亲'))->fostering()->mother(),
            Relationship::fixed(...$zh('寄养父亲'))->fostering()->father(),
            Relationship::fixed(...$zh('寄养父母'))->fostering()->parent(),
            Relationship::fixed(...$zh('寄养女'))->fostered()->daughter(),
            Relationship::fixed(...$zh('寄养子'))->fostered()->son(),
            Relationship::fixed(...$zh('寄养子女'))->fostered()->child(),
            // Step
            Relationship::fixed(...$zh('继母'))->parent()->wife(),
            Relationship::fixed(...$zh('继父'))->parent()->husband(),
            Relationship::fixed(...$zh('继女'))->married()->spouse()->daughter(),
            Relationship::fixed(...$zh('继子'))->married()->spouse()->son(),
            Relationship::fixed(...$zh('继子女'))->married()->spouse()->child(),
            // Parents
            Relationship::fixed(...$zh('母亲'))->mother(),
            Relationship::fixed(...$zh('父亲'))->father(),
            Relationship::fixed(...$zh('父母'))->parent(),
            // Children
            Relationship::fixed(...$zh('女儿'))->daughter(),
            Relationship::fixed(...$zh('儿子'))->son(),
            Relationship::fixed(...$zh('孩子'))->child(),
            // Siblings — elder/younger distinction
            Relationship::fixed(...$zh('双胞胎姐姐'))->twin()->older()->sister(),
            Relationship::fixed(...$zh('双胞胎妹妹'))->twin()->younger()->sister(),
            Relationship::fixed(...$zh('双胞胎哥哥'))->twin()->older()->brother(),
            Relationship::fixed(...$zh('双胞胎弟弟'))->twin()->younger()->brother(),
            Relationship::fixed(...$zh('双胞胎'))->twin()->sibling(),
            Relationship::fixed(...$zh('姐姐'))->older()->sister(),
            Relationship::fixed(...$zh('哥哥'))->older()->brother(),
            Relationship::fixed(...$zh('妹妹'))->younger()->sister(),
            Relationship::fixed(...$zh('弟弟'))->younger()->brother(),
            Relationship::fixed(...$zh('姐妹'))->sister(),
            Relationship::fixed(...$zh('兄弟'))->brother(),
            Relationship::fixed(...$zh('兄弟姐妹'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$zh('同父异母姐妹'))->father()->daughter(),
            Relationship::fixed(...$zh('同父异母兄弟'))->father()->son(),
            Relationship::fixed(...$zh('同母异父姐妹'))->mother()->daughter(),
            Relationship::fixed(...$zh('同母异父兄弟'))->mother()->son(),
            Relationship::fixed(...$zh('同父异母/同母异父兄弟姐妹'))->parent()->child(),
            // Partners
            Relationship::fixed(...$zh('前妻'))->divorced()->partner()->female(),
            Relationship::fixed(...$zh('前夫'))->divorced()->partner()->male(),
            Relationship::fixed(...$zh('前配偶'))->divorced()->partner(),
            Relationship::fixed(...$zh('未婚妻'))->engaged()->partner()->female(),
            Relationship::fixed(...$zh('未婚夫'))->engaged()->partner()->male(),
            Relationship::fixed(...$zh('妻子'))->wife(),
            Relationship::fixed(...$zh('丈夫'))->husband(),
            Relationship::fixed(...$zh('配偶'))->spouse(),
            Relationship::fixed(...$zh('伴侣'))->partner(),
            // In-laws — spouse's parents
            Relationship::fixed(...$zh('婆婆'))->husband()->mother(),
            Relationship::fixed(...$zh('公公'))->husband()->father(),
            Relationship::fixed(...$zh('岳母'))->wife()->mother(),
            Relationship::fixed(...$zh('岳父'))->wife()->father(),
            Relationship::fixed(...$zh('公婆/岳父母'))->married()->spouse()->parent(),
            // In-laws — child's spouse
            Relationship::fixed(...$zh('儿媳'))->son()->wife(),
            Relationship::fixed(...$zh('女婿'))->daughter()->husband(),
            Relationship::fixed(...$zh('儿媳'))->child()->wife(),
            Relationship::fixed(...$zh('女婿'))->child()->husband(),
            // In-laws — spouse's siblings
            Relationship::fixed(...$zh('姑子'))->husband()->sister(),
            Relationship::fixed(...$zh('夫之兄弟'))->husband()->brother(),
            Relationship::fixed(...$zh('姨子'))->wife()->sister(),
            Relationship::fixed(...$zh('舅子'))->wife()->brother(),
            // In-laws — sibling's spouse
            Relationship::fixed(...$zh('嫂子'))->brother()->wife(),
            Relationship::fixed(...$zh('姐夫'))->sister()->husband(),
            // Grandparents — paternal
            Relationship::fixed(...$zh('奶奶'))->father()->mother(),
            Relationship::fixed(...$zh('爷爷'))->father()->father(),
            // Grandparents — maternal
            Relationship::fixed(...$zh('外婆'))->mother()->mother(),
            Relationship::fixed(...$zh('外公'))->mother()->father(),
            // Grandparents — generic
            Relationship::fixed(...$zh('祖父母'))->parent()->parent(),
            // Grandchildren — son's children
            Relationship::fixed(...$zh('孙女'))->son()->daughter(),
            Relationship::fixed(...$zh('孙子'))->son()->son(),
            // Grandchildren — daughter's children
            Relationship::fixed(...$zh('外孙女'))->daughter()->daughter(),
            Relationship::fixed(...$zh('外孙'))->daughter()->son(),
            // Grandchildren — generic
            Relationship::fixed(...$zh('孙辈'))->child()->child(),
            // Aunts/Uncles — paternal
            Relationship::fixed(...$zh('姑姑'))->father()->sister(),
            Relationship::fixed(...$zh('叔伯'))->father()->brother(),
            // Aunts/Uncles — maternal
            Relationship::fixed(...$zh('姨妈'))->mother()->sister(),
            Relationship::fixed(...$zh('舅舅'))->mother()->brother(),
            // Aunts/Uncles — generic
            Relationship::fixed(...$zh('姑姨'))->parent()->sister(),
            Relationship::fixed(...$zh('叔舅'))->parent()->brother(),
            // Uncle/aunt's spouses
            Relationship::fixed(...$zh('姑父'))->father()->sister()->husband(),
            Relationship::fixed(...$zh('婶母'))->father()->brother()->wife(),
            Relationship::fixed(...$zh('姨父'))->mother()->sister()->husband(),
            Relationship::fixed(...$zh('舅妈'))->mother()->brother()->wife(),
            // Nieces/Nephews — brother's children
            Relationship::fixed(...$zh('侄女'))->brother()->daughter(),
            Relationship::fixed(...$zh('侄子'))->brother()->son(),
            // Nieces/Nephews — sister's children
            Relationship::fixed(...$zh('外甥女'))->sister()->daughter(),
            Relationship::fixed(...$zh('外甥'))->sister()->son(),
            // Nieces/Nephews — generic
            Relationship::fixed(...$zh('侄子女'))->sibling()->child(),
            // Cousins — paternal uncle's children (堂) with older/younger
            Relationship::fixed(...$zh('堂姐'))->older()->father()->brother()->daughter(),
            Relationship::fixed(...$zh('堂妹'))->younger()->father()->brother()->daughter(),
            Relationship::fixed(...$zh('堂哥'))->older()->father()->brother()->son(),
            Relationship::fixed(...$zh('堂弟'))->younger()->father()->brother()->son(),
            Relationship::fixed(...$zh('堂姐妹'))->father()->brother()->daughter(),
            Relationship::fixed(...$zh('堂兄弟'))->father()->brother()->son(),
            // Cousins — 表 (father's sister's or mother's siblings' children) with older/younger
            Relationship::fixed(...$zh('表姐'))->older()->father()->sister()->daughter(),
            Relationship::fixed(...$zh('表妹'))->younger()->father()->sister()->daughter(),
            Relationship::fixed(...$zh('表哥'))->older()->father()->sister()->son(),
            Relationship::fixed(...$zh('表弟'))->younger()->father()->sister()->son(),
            Relationship::fixed(...$zh('表姐'))->older()->mother()->sibling()->daughter(),
            Relationship::fixed(...$zh('表妹'))->younger()->mother()->sibling()->daughter(),
            Relationship::fixed(...$zh('表哥'))->older()->mother()->sibling()->son(),
            Relationship::fixed(...$zh('表弟'))->younger()->mother()->sibling()->son(),
            Relationship::fixed(...$zh('表姐妹'))->father()->sister()->daughter(),
            Relationship::fixed(...$zh('表兄弟'))->father()->sister()->son(),
            Relationship::fixed(...$zh('表姐妹'))->mother()->sibling()->daughter(),
            Relationship::fixed(...$zh('表兄弟'))->mother()->sibling()->son(),
            // Cousins — generic fallback
            Relationship::fixed(...$zh('堂/表亲'))->parent()->sibling()->child(),
            // Dynamic: great-grandparents
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $zh('曾祖母'),
                4       => $zh('高祖母'),
                default => $zh(($n - 2) . '世祖母'),
            })->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $zh('曾祖父'),
                4       => $zh('高祖父'),
                default => $zh(($n - 2) . '世祖父'),
            })->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $zh('曾祖'),
                4       => $zh('高祖'),
                default => $zh(($n - 2) . '世祖'),
            })->ancestor(),
            // Dynamic: great-grandchildren
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $zh('曾孙女'),
                4       => $zh('玄孙女'),
                default => $zh(($n - 2) . '世孙女'),
            })->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $zh('曾孙'),
                4       => $zh('玄孙'),
                default => $zh(($n - 2) . '世孙'),
            })->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => match ($n) {
                3       => $zh('曾孙辈'),
                4       => $zh('玄孙辈'),
                default => $zh(($n - 2) . '世孙辈'),
            })->descendant(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $zh($n . '世姑姨'))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $zh($n . '世叔舅'))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $zh($n . '世侄女'))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $zh($n . '世侄子'))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $zh($n . '世侄'))->sibling()->descendant(),
        ];
    }
}
