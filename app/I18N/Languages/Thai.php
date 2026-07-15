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
use Fisharebest\Webtrees\Enums\Weekday;
use Fisharebest\Webtrees\Relationship;
use Fisharebest\Webtrees\Report\PaperSize;

final readonly class Thai extends AbstractLanguage
{
    protected const string    ENDONYM            = 'ไทย';
    protected const PaperSize PAPER_SIZE         = PaperSize::A4;
    protected const string    LANGUAGE_TAG       = 'th';
    protected const string    LOCALE_CODE = 'th_TH@collation=phonebook';
    protected const array     DIGITS      = [
        0 => UTF8::THAI_DIGIT_ZERO,
        1 => UTF8::THAI_DIGIT_ONE,
        2 => UTF8::THAI_DIGIT_TWO,
        3 => UTF8::THAI_DIGIT_THREE,
        4 => UTF8::THAI_DIGIT_FOUR,
        5 => UTF8::THAI_DIGIT_FIVE,
        6 => UTF8::THAI_DIGIT_SIX,
        7 => UTF8::THAI_DIGIT_SEVEN,
        8 => UTF8::THAI_DIGIT_EIGHT,
        9 => UTF8::THAI_DIGIT_NINE,
    ];
    protected const Script    SCRIPT             = Script::Thai;
    protected const Weekday   FIRST_DAY          = Weekday::Sunday;
    protected const string    DATE_ABOUT         = 'khoảng %s';
    protected const string    DATE_AFTER         = 'sau %s';
    protected const string    DATE_BEFORE        = 'trước %s';
    protected const string    DATE_BETWEEN_AND   = 'giữa %s và %s';
    protected const string    DATE_CALCULATED    = 'được tính %s';
    protected const string    DATE_ESTIMATED     = 'ước tính %s';
    protected const string    DATE_FROM          = 'từ %s';
    protected const string    DATE_FROM_TO       = 'từ %s đến %s';
    protected const string    DATE_INTERPRETED   = 'giải thích là %s';
    protected const string    DATE_TO            = 'đến %s';
    protected const string    ERA_BCE            = '%s BCE';
    protected const string    LIST_SEPARATOR_AND = ' และ';
    protected const string    LIST_SEPARATOR_OR  = ' หรือ';


    protected const array GREGORIAN_MONTHS_NOMINATIVE = [
        '',
        'มกราคม',
        'กุมภาพันธ์',
        'มีนาคม',
        'เมษายน',
        'พฤษภาคม',
        'มิถุนายน',
        'กรกฎาคม',
        'สิงหาคม',
        'กันยายน',
        'ตุลาคม',
        'พฤศจิกายน',
        'ธันวาคม',
    ];

    protected const array GREGORIAN_MONTHS_GENITIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_LOCATIVE = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array GREGORIAN_MONTHS_INSTRUMENTAL = self::GREGORIAN_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_NOMINATIVE = [
        '',
        'ทิชเร',
        'เฮชวาน',
        'คิสเลฟ',
        'เตเวต',
        'เชวัต',
        'อาดาร์ 1',
        'อาดาร์ 2',
        'อาดาร์',
        'นิสซาน',
        'อิยาร์',
        'สิวาน',
        'ทามุซ',
        'อัฟ',
        'เอลุล',
    ];

    protected const array JEWISH_MONTHS_GENITIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_LOCATIVE = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array JEWISH_MONTHS_INSTRUMENTAL = self::JEWISH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_NOMINATIVE = [
        '',
        'วองเดมีแยร์',
        'บรูแมร์',
        'ฟรีแมร์',
        'นีโวส',
        'พลูวีโอส',
        'วองโตส',
        'แฌร์มีนาล',
        'ฟลอเรอัล',
        'แปรรีอัล',
        'เมสซีดอร์',
        'แตร์มีดอร์',
        'ฟรุกตีดอร์',
        'วันเสริม',
    ];

    protected const array FRENCH_MONTHS_GENITIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_LOCATIVE = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array FRENCH_MONTHS_INSTRUMENTAL = self::FRENCH_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_NOMINATIVE = [
        '',
        'มุฮัรรอม',
        'ซอฟัร',
        'รอบีอุลเอาวัล',
        'รอบีอุษษานี',
        'ญุมาดัลอูลา',
        'ญุมาดัษษานียะฮ์',
        'รอญับ',
        'ชะอ์บาน',
        'รอมะฎอน',
        'เชาวาล',
        'ซุลกิอ์ดะฮ์',
        'ซุลฮิจญะฮ์',
    ];

    protected const array HIJRI_MONTHS_GENITIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_LOCATIVE = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array HIJRI_MONTHS_INSTRUMENTAL = self::HIJRI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_NOMINATIVE = [
        '',
        'ฟาร์วาร์ดิน',
        'ออร์ดิเบเฮชต์',
        'คอร์ดาด',
        'ตีร์',
        'มอร์ดาด',
        'ชาห์ริวาร์',
        'เมห์ร',
        'อาบาน',
        'อาซาร์',
        'เดย์',
        'บาห์มัน',
        'เอสฟานด์',
    ];

    protected const array JALALI_MONTHS_GENITIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_LOCATIVE = self::JALALI_MONTHS_NOMINATIVE;

    protected const array JALALI_MONTHS_INSTRUMENTAL = self::JALALI_MONTHS_NOMINATIVE;

    public function relationships(): array
    {
        // Thai uses ของ (khǒong = "of") for possessive — "%s ของแม่" = "mother's X"
        $th = static fn (string $s): array => [$s, '%s ของ' . $s];

        return [
            // Adopted
            Relationship::fixed(...$th('แม่บุญธรรม'))->adoptive()->mother(),
            Relationship::fixed(...$th('พ่อบุญธรรม'))->adoptive()->father(),
            Relationship::fixed(...$th('พ่อแม่บุญธรรม'))->adoptive()->parent(),
            Relationship::fixed(...$th('ลูกสาวบุญธรรม'))->adopted()->daughter(),
            Relationship::fixed(...$th('ลูกชายบุญธรรม'))->adopted()->son(),
            Relationship::fixed(...$th('ลูกบุญธรรม'))->adopted()->child(),
            // Fostered
            Relationship::fixed(...$th('แม่อุปถัมภ์'))->fostering()->mother(),
            Relationship::fixed(...$th('พ่ออุปถัมภ์'))->fostering()->father(),
            Relationship::fixed(...$th('พ่อแม่อุปถัมภ์'))->fostering()->parent(),
            Relationship::fixed(...$th('ลูกสาวอุปถัมภ์'))->fostered()->daughter(),
            Relationship::fixed(...$th('ลูกชายอุปถัมภ์'))->fostered()->son(),
            Relationship::fixed(...$th('ลูกอุปถัมภ์'))->fostered()->child(),
            // Step
            Relationship::fixed(...$th('แม่เลี้ยง'))->parent()->wife(),
            Relationship::fixed(...$th('พ่อเลี้ยง'))->parent()->husband(),
            Relationship::fixed(...$th('ลูกเลี้ยงหญิง'))->married()->spouse()->daughter(),
            Relationship::fixed(...$th('ลูกเลี้ยงชาย'))->married()->spouse()->son(),
            Relationship::fixed(...$th('ลูกเลี้ยง'))->married()->spouse()->child(),
            // Parents
            Relationship::fixed(...$th('แม่'))->mother(),
            Relationship::fixed(...$th('พ่อ'))->father(),
            Relationship::fixed(...$th('พ่อแม่'))->parent(),
            // Children
            Relationship::fixed(...$th('ลูกสาว'))->daughter(),
            Relationship::fixed(...$th('ลูกชาย'))->son(),
            Relationship::fixed(...$th('ลูก'))->child(),
            // Siblings — elder/younger distinction
            Relationship::fixed(...$th('พี่สาวแฝด'))->twin()->older()->sister(),
            Relationship::fixed(...$th('น้องสาวแฝด'))->twin()->younger()->sister(),
            Relationship::fixed(...$th('พี่ชายแฝด'))->twin()->older()->brother(),
            Relationship::fixed(...$th('น้องชายแฝด'))->twin()->younger()->brother(),
            Relationship::fixed(...$th('แฝด'))->twin()->sibling(),
            Relationship::fixed(...$th('พี่สาว'))->older()->sister(),
            Relationship::fixed(...$th('พี่ชาย'))->older()->brother(),
            Relationship::fixed(...$th('พี่'))->older()->sibling(),
            Relationship::fixed(...$th('น้องสาว'))->younger()->sister(),
            Relationship::fixed(...$th('น้องชาย'))->younger()->brother(),
            Relationship::fixed(...$th('น้อง'))->younger()->sibling(),
            Relationship::fixed(...$th('พี่น้องหญิง'))->sister(),
            Relationship::fixed(...$th('พี่น้องชาย'))->brother(),
            Relationship::fixed(...$th('พี่น้อง'))->sibling(),
            // Half-siblings (paternal)
            Relationship::fixed(...$th('พี่น้องหญิงต่างแม่'))->father()->daughter(),
            Relationship::fixed(...$th('พี่น้องชายต่างแม่'))->father()->son(),
            // Half-siblings (maternal)
            Relationship::fixed(...$th('พี่น้องหญิงต่างพ่อ'))->mother()->daughter(),
            Relationship::fixed(...$th('พี่น้องชายต่างพ่อ'))->mother()->son(),
            // Half-siblings (generic)
            Relationship::fixed(...$th('พี่น้องต่างพ่อแม่'))->parent()->child(),
            // Partners
            Relationship::fixed(...$th('อดีตภรรยา'))->divorced()->partner()->female(),
            Relationship::fixed(...$th('อดีตสามี'))->divorced()->partner()->male(),
            Relationship::fixed(...$th('อดีตคู่สมรส'))->divorced()->partner(),
            Relationship::fixed(...$th('คู่หมั้นหญิง'))->engaged()->partner()->female(),
            Relationship::fixed(...$th('คู่หมั้นชาย'))->engaged()->partner()->male(),
            Relationship::fixed(...$th('ภรรยา'))->wife(),
            Relationship::fixed(...$th('สามี'))->husband(),
            Relationship::fixed(...$th('คู่สมรส'))->spouse(),
            Relationship::fixed(...$th('คู่ครอง'))->partner(),
            // In-laws — spouse's parents
            Relationship::fixed(...$th('แม่สามี'))->husband()->mother(),
            Relationship::fixed(...$th('พ่อสามี'))->husband()->father(),
            Relationship::fixed(...$th('แม่ยาย'))->wife()->mother(),
            Relationship::fixed(...$th('พ่อตา'))->wife()->father(),
            Relationship::fixed(...$th('พ่อแม่คู่สมรส'))->married()->spouse()->parent(),
            // In-laws — child's spouse
            Relationship::fixed(...$th('ลูกสะใภ้'))->child()->wife(),
            Relationship::fixed(...$th('ลูกเขย'))->child()->husband(),
            // In-laws — spouse's siblings
            Relationship::fixed(...$th('พี่น้องสามี'))->husband()->sibling(),
            Relationship::fixed(...$th('พี่น้องภรรยา'))->wife()->sibling(),
            // In-laws — sibling's spouse
            Relationship::fixed(...$th('พี่น้องเขย'))->sister()->husband(),
            Relationship::fixed(...$th('พี่น้องสะใภ้'))->brother()->wife(),
            // Grandparents — paternal/maternal distinction
            Relationship::fixed(...$th('ย่า'))->father()->mother(),
            Relationship::fixed(...$th('ปู่'))->father()->father(),
            Relationship::fixed(...$th('ยาย'))->mother()->mother(),
            Relationship::fixed(...$th('ตา'))->mother()->father(),
            Relationship::fixed(...$th('ปู่ย่าตายาย'))->parent()->parent(),
            // Grandchildren
            Relationship::fixed(...$th('หลานสาว'))->child()->daughter(),
            Relationship::fixed(...$th('หลานชาย'))->child()->son(),
            Relationship::fixed(...$th('หลาน'))->child()->child(),
            // Aunts/Uncles — paternal (อา = father's sibling)
            Relationship::fixed(...$th('อา'))->father()->sister(),
            Relationship::fixed(...$th('อา'))->father()->brother(),
            // Aunts/Uncles — maternal (น้า = mother's sibling)
            Relationship::fixed(...$th('น้า'))->mother()->sister(),
            Relationship::fixed(...$th('น้า'))->mother()->brother(),
            // Aunts/Uncles — generic
            Relationship::fixed(...$th('ป้า'))->parent()->sister(),
            Relationship::fixed(...$th('ลุง'))->parent()->brother(),
            // Uncle/aunt's spouses
            Relationship::fixed(...$th('ลุง'))->parent()->sister()->husband(),
            Relationship::fixed(...$th('ป้า'))->parent()->brother()->wife(),
            // Nieces/Nephews (หลาน — same as grandchild)
            Relationship::fixed(...$th('หลานสาว'))->sibling()->daughter(),
            Relationship::fixed(...$th('หลานชาย'))->sibling()->son(),
            Relationship::fixed(...$th('หลาน'))->sibling()->child(),
            // Cousins
            Relationship::fixed(...$th('ลูกพี่ลูกน้อง'))->parent()->sibling()->child(),
            // Dynamic: great-grandparents (ทวด)
            Relationship::dynamic(static fn (int $n) => $th(str_repeat('ทวด', $n - 2) . 'หญิง'))->ancestor()->female(),
            Relationship::dynamic(static fn (int $n) => $th(str_repeat('ทวด', $n - 2) . 'ชาย'))->ancestor()->male(),
            Relationship::dynamic(static fn (int $n) => $th(str_repeat('ทวด', $n - 2)))->ancestor(),
            // Dynamic: great-grandchildren (เหลน)
            Relationship::dynamic(static fn (int $n) => $th(str_repeat('เหลน', $n - 2) . 'สาว'))->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $th(str_repeat('เหลน', $n - 2) . 'ชาย'))->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $th(str_repeat('เหลน', $n - 2)))->descendant(),
            // Dynamic: great-aunts/uncles
            Relationship::dynamic(static fn (int $n) => $th('ป้าชั้นที่ ' . $n))->ancestor()->sister(),
            Relationship::dynamic(static fn (int $n) => $th('ลุงชั้นที่ ' . $n))->ancestor()->brother(),
            // Dynamic: great-nieces/nephews
            Relationship::dynamic(static fn (int $n) => $th('หลานสาวชั้นที่ ' . $n))->sibling()->descendant()->female(),
            Relationship::dynamic(static fn (int $n) => $th('หลานชายชั้นที่ ' . $n))->sibling()->descendant()->male(),
            Relationship::dynamic(static fn (int $n) => $th('หลานชั้นที่ ' . $n))->sibling()->descendant(),
        ];
    }
}
