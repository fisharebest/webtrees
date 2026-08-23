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

final readonly class Japanese extends AbstractLanguage
{
    protected const string    ENDONYM            = '日本語';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'ja';
    protected const string    LOCALE_CODE        = 'ja_JP@collation=phonebook';
    protected const Script    SCRIPT             = Script::Jpan;
    protected const Weekday   FIRST_DAY          = Weekday::Sunday;
    protected const string    LIST_SEPARATOR     = '、';
    protected const string    LIST_SEPARATOR_AND = '、';
    protected const string    LIST_SEPARATOR_OR  = 'または';

    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        '1月',
        '2月',
        '3月',
        '4月',
        '5月',
        '6月',
        '7月',
        '8月',
        '9月',
        '10月',
        '11月',
        '12月',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'ティシュレー',
        'ヘシュヴァン',
        'キスレーヴ',
        'テベット',
        'シュバット',
        'アダル I',
        'アダル II',
        'アダル',
        'ニサン',
        'イヤル',
        'シバン',
        'タムズ',
        'アブ',
        'エルール',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'ヴァンデミエール',
        'ブリュメール',
        'フリメール',
        'ニヴォーズ',
        'プリュヴィオーズ',
        'ヴァントーズ',
        'ジェルミナール',
        'フロレアール',
        'プレリアール',
        'メシドール',
        'テルミドール',
        'フリュクティドール',
        '補充日',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'ムハッラム',
        'サファル',
        'ラビーウル・アウワル',
        'ラビーウッサーニー',
        'ジュマーダル・ウーラー',
        'ジュマーダッサーニー',
        'ラジャブ',
        'シャアバーン',
        'ラマダーン',
        'シャウワール',
        'ズー・アルカアダ',
        'ズー・アルヒッジャ',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'ファルヴァルディーン',
        'オルディーベヘシュト',
        'ホルダード',
        'ティール',
        'モルダード',
        'シャフリーヴァル',
        'メフル',
        'アーバーン',
        'アーザル',
        'デイ',
        'バフマン',
        'エスファンド',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

        /** @return array{string, string} */
    private function ja(string $s): array
    {
        return [$s, $s . 'の%s'];
    }

    /**
     * @return array<Relationship>
     */
    public function relationships(): array
    {
        return [
            // Adopted
            Relationship::fixed(...$this->ja('養母'))->adoptive()->mother(),
            Relationship::fixed(...$this->ja('養父'))->adoptive()->father(),
            Relationship::fixed(...$this->ja('養親'))->adoptive()->parent(),
            Relationship::fixed(...$this->ja('養女'))->adopted()->daughter(),
            Relationship::fixed(...$this->ja('養子'))->adopted()->son(),
            Relationship::fixed(...$this->ja('養子女'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$this->ja('里母'))->fostering()->mother(),
            Relationship::fixed(...$this->ja('里父'))->fostering()->father(),
            Relationship::fixed(...$this->ja('里親'))->fostering()->parent(),
            Relationship::fixed(...$this->ja('里娘'))->fostered()->daughter(),
            Relationship::fixed(...$this->ja('里子'))->fostered()->son(),
            Relationship::fixed(...$this->ja('里子女'))->fostered()->child(),
            // Step
            Relationship::fixed(...$this->ja('継母'))->parent()->wife(),
            Relationship::fixed(...$this->ja('継父'))->parent()->husband(),
            Relationship::fixed(...$this->ja('継娘'))->married()->spouse()->daughter(),
            Relationship::fixed(...$this->ja('継息子'))->married()->spouse()->son(),
            Relationship::fixed(...$this->ja('継子'))->married()->spouse()->child(),
            // Parents
            Relationship::fixed(...$this->ja('母'))->mother(),
            Relationship::fixed(...$this->ja('父'))->father(),
            Relationship::fixed(...$this->ja('親'))->parent(),
            // Children
            Relationship::fixed(...$this->ja('娘'))->daughter(),
            Relationship::fixed(...$this->ja('息子'))->son(),
            Relationship::fixed(...$this->ja('子'))->child(),
            // Siblings — elder/younger distinction
            Relationship::fixed(...$this->ja('双子の姉'))->multiple()->older()->sister(),
            Relationship::fixed(...$this->ja('双子の妹'))->multiple()->younger()->sister(),
            Relationship::fixed(...$this->ja('双子の兄'))->multiple()->older()->brother(),
            Relationship::fixed(...$this->ja('双子の弟'))->multiple()->younger()->brother(),
            Relationship::fixed(...$this->ja('双子'))->multiple()->sibling(),
            Relationship::fixed(...$this->ja('姉'))->older()->sister(),
            Relationship::fixed(...$this->ja('兄'))->older()->brother(),
            Relationship::fixed(...$this->ja('妹'))->younger()->sister(),
            Relationship::fixed(...$this->ja('弟'))->younger()->brother(),
            Relationship::fixed(...$this->ja('姉妹'))->sister(),
            Relationship::fixed(...$this->ja('兄弟'))->brother(),
            Relationship::fixed(...$this->ja('きょうだい'))->sibling(),
            // Half-siblings
            Relationship::fixed(...$this->ja('異母姉妹'))->father()->daughter(),
            Relationship::fixed(...$this->ja('異母兄弟'))->father()->son(),
            Relationship::fixed(...$this->ja('異父姉妹'))->mother()->daughter(),
            Relationship::fixed(...$this->ja('異父兄弟'))->mother()->son(),
            Relationship::fixed(...$this->ja('異父母きょうだい'))->parent()->child(),
            // Partners
            Relationship::fixed(...$this->ja('元妻'))->divorced()->partner()->female(),
            Relationship::fixed(...$this->ja('元夫'))->divorced()->partner()->male(),
            Relationship::fixed(...$this->ja('元配偶者'))->divorced()->partner(),
            Relationship::fixed(...$this->ja('婚約者'))->engaged()->partner()->female(),
            Relationship::fixed(...$this->ja('婚約者'))->engaged()->partner()->male(),
            Relationship::fixed(...$this->ja('妻'))->wife(),
            Relationship::fixed(...$this->ja('夫'))->husband(),
            Relationship::fixed(...$this->ja('配偶者'))->spouse(),
            Relationship::fixed(...$this->ja('パートナー'))->partner(),
            // In-laws — spouse's parents
            Relationship::fixed(...$this->ja('姑'))->husband()->mother(),
            Relationship::fixed(...$this->ja('舅'))->husband()->father(),
            Relationship::fixed(...$this->ja('義母'))->wife()->mother(),
            Relationship::fixed(...$this->ja('義父'))->wife()->father(),
            Relationship::fixed(...$this->ja('義親'))->married()->spouse()->parent(),
            // In-laws — child's spouse
            Relationship::fixed(...$this->ja('嫁'))->son()->wife(),
            Relationship::fixed(...$this->ja('婿'))->daughter()->husband(),
            Relationship::fixed(...$this->ja('嫁'))->child()->wife(),
            Relationship::fixed(...$this->ja('婿'))->child()->husband(),
            // In-laws — spouse's siblings
            Relationship::fixed(...$this->ja('小姑'))->husband()->sister(),
            Relationship::fixed(...$this->ja('義兄弟'))->husband()->brother(),
            Relationship::fixed(...$this->ja('義姉妹'))->wife()->sister(),
            Relationship::fixed(...$this->ja('義兄弟'))->wife()->brother(),
            // In-laws — sibling's spouse
            Relationship::fixed(...$this->ja('兄嫁'))->brother()->wife(),
            Relationship::fixed(...$this->ja('姉婿'))->sister()->husband(),
            // Grandparents — paternal
            Relationship::fixed(...$this->ja('父方の祖母'))->father()->mother(),
            Relationship::fixed(...$this->ja('父方の祖父'))->father()->father(),
            // Grandparents — maternal
            Relationship::fixed(...$this->ja('母方の祖母'))->mother()->mother(),
            Relationship::fixed(...$this->ja('母方の祖父'))->mother()->father(),
            // Grandparents — generic
            Relationship::fixed(...$this->ja('祖父母'))->parent()->parent(),
            // Grandchildren — son's children
            Relationship::fixed(...$this->ja('孫娘'))->son()->daughter(),
            Relationship::fixed(...$this->ja('孫息子'))->son()->son(),
            // Grandchildren — daughter's children
            Relationship::fixed(...$this->ja('孫娘'))->daughter()->daughter(),
            Relationship::fixed(...$this->ja('孫息子'))->daughter()->son(),
            // Grandchildren — generic
            Relationship::fixed(...$this->ja('孫'))->child()->child(),
            // Aunts/Uncles — paternal
            Relationship::fixed(...$this->ja('父方の伯母/叔母'))->father()->sister(),
            Relationship::fixed(...$this->ja('父方の伯父/叔父'))->father()->brother(),
            // Aunts/Uncles — maternal
            Relationship::fixed(...$this->ja('母方の伯母/叔母'))->mother()->sister(),
            Relationship::fixed(...$this->ja('母方の伯父/叔父'))->mother()->brother(),
            // Aunts/Uncles — generic
            Relationship::fixed(...$this->ja('伯母/叔母'))->parent()->sister(),
            Relationship::fixed(...$this->ja('伯父/叔父'))->parent()->brother(),
            // Uncle/aunt's spouses
            Relationship::fixed(...$this->ja('伯母/叔母の夫'))->parent()->sister()->husband(),
            Relationship::fixed(...$this->ja('伯父/叔父の妻'))->parent()->brother()->wife(),
            // Nieces/Nephews
            Relationship::fixed(...$this->ja('姪'))->brother()->daughter(),
            Relationship::fixed(...$this->ja('甥'))->brother()->son(),
            Relationship::fixed(...$this->ja('姪'))->sister()->daughter(),
            Relationship::fixed(...$this->ja('甥'))->sister()->son(),
            Relationship::fixed(...$this->ja('甥姪'))->sibling()->child(),
            // Cousins — generic (Japanese doesn't distinguish paternal/maternal cousins as strictly as Chinese)
            Relationship::fixed(...$this->ja('従姉'))->older()->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->ja('従妹'))->younger()->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->ja('従兄'))->older()->parent()->sibling()->son(),
            Relationship::fixed(...$this->ja('従弟'))->younger()->parent()->sibling()->son(),
            Relationship::fixed(...$this->ja('従姉妹'))->parent()->sibling()->daughter(),
            Relationship::fixed(...$this->ja('従兄弟'))->parent()->sibling()->son(),
            Relationship::fixed(...$this->ja('いとこ'))->parent()->sibling()->child(),
            // Dynamic: great-grandparents
            Relationship::dynamic(fn (int $n) => match ($n) {
                3       => $this->ja('曾祖母'),
                4       => $this->ja('高祖母'),
                default => $this->ja(($n - 2) . '世の祖母'),
            })->ancestor()->female(),
            Relationship::dynamic(fn (int $n) => match ($n) {
                3       => $this->ja('曾祖父'),
                4       => $this->ja('高祖父'),
                default => $this->ja(($n - 2) . '世の祖父'),
            })->ancestor()->male(),
            Relationship::dynamic(fn (int $n) => match ($n) {
                3       => $this->ja('曾祖'),
                4       => $this->ja('高祖'),
                default => $this->ja(($n - 2) . '世の祖'),
            })->ancestor(),
            // Dynamic: great-grandchildren
            Relationship::dynamic(fn (int $n) => match ($n) {
                3       => $this->ja('曾孫娘'),
                4       => $this->ja('玄孫娘'),
                default => $this->ja(($n - 2) . '世の孫娘'),
            })->descendant()->female(),
            Relationship::dynamic(fn (int $n) => match ($n) {
                3       => $this->ja('曾孫'),
                4       => $this->ja('玄孫'),
                default => $this->ja(($n - 2) . '世の孫'),
            })->descendant()->male(),
            Relationship::dynamic(fn (int $n) => match ($n) {
                3       => $this->ja('曾孫'),
                4       => $this->ja('玄孫'),
                default => $this->ja(($n - 2) . '世の孫'),
            })->descendant(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(fn (int $n) => $this->ja($n . '世の伯母/叔母'))->ancestor()->sister(),
            Relationship::dynamic(fn (int $n) => $this->ja($n . '世の伯父/叔父'))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(fn (int $n) => $this->ja($n . '世の姪'))->sibling()->descendant()->female(),
            Relationship::dynamic(fn (int $n) => $this->ja($n . '世の甥'))->sibling()->descendant()->male(),
            Relationship::dynamic(fn (int $n) => $this->ja($n . '世の甥姪'))->sibling()->descendant(),
        ];
    }
}
