<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Report;

use Fisharebest\Webtrees\I18N;

use function ord;
use function preg_replace;
use function str_contains;
use function str_pad;
use function str_replace;
use function strlen;
use function strpos;
use function strrpos;
use function strtolower;
use function strtoupper;
use function substr;

use const STR_PAD_LEFT;
use const STR_PAD_RIGHT;

/**
 * RTL Functions for use in the PDF reports
 */
class RightToLeftSupport
{
    private const string UTF8_LRM = "\xE2\x80\x8E"; // U+200E (Left to Right mark:  zero-width character with LTR directionality)
    private const string UTF8_RLM = "\xE2\x80\x8F"; // U+200F (Right to Left mark:  zero-width character with RTL directionality)
    private const string UTF8_LRO = "\xE2\x80\xAD"; // U+202D (Left to Right override: force everything following to LTR mode)
    private const string UTF8_RLO = "\xE2\x80\xAE"; // U+202E (Right to Left override: force everything following to RTL mode)
    private const string UTF8_LRE = "\xE2\x80\xAA"; // U+202A (Left to Right embedding: treat everything following as LTR text)
    private const string UTF8_RLE = "\xE2\x80\xAB"; // U+202B (Right to Left embedding: treat everything following as RTL text)
    private const string UTF8_PDF = "\xE2\x80\xAC"; // U+202C (Pop directional formatting: restore state prior to last LRO, RLO, LRE, RLE)

    private const string OPEN_PARENTHESES = '([{';

    private const string CLOSE_PARENTHESES = ')]}';

    private const string NUMBERS = '0123456789';

    private const string NUMBER_PREFIX = '+-'; // Treat these like numbers when at beginning or end of numeric strings

    private const string NUMBER_PUNCTUATION = '- ,.:/'; // Treat these like numbers when inside numeric strings

    private const string PUNCTUATION = ',.:;?!';

    // Markup
    private const string START_LTR    = '<LTR>';
    private const string END_LTR      = '</LTR>';
    private const string START_RTL    = '<RTL>';
    private const string END_RTL      = '</RTL>';
    private const int LENGTH_START = 5;
    private const int LENGTH_END   = 6;

    /* Were we previously processing LTR or RTL. */
    private static string $previousState;

    /* Are we currently processing LTR or RTL. */
    private static string $currentState;

    /* Text waiting to be processed. */
    private static string $waitingText;

    /* Offset into the text. */
    private static int $posSpanStart;

    /**
     * This function strips &lrm; and &rlm; from the input string. It should be used for all
     * text that has been passed through the PrintReady() function before that text is stored
     * in the database. The database should NEVER contain these characters.
     *
     * @param string $inputText The string from which the &lrm; and &rlm; characters should be stripped
     *
     * @return string The input string, with &lrm; and &rlm; stripped
     */
    private static function stripLrmRlm(string $inputText): string
    {
        return str_replace([
            self::UTF8_LRM,
            self::UTF8_RLM,
            self::UTF8_LRO,
            self::UTF8_RLO,
            self::UTF8_LRE,
            self::UTF8_RLE,
            self::UTF8_PDF,
            '&lrm;',
            '&rlm;',
            '&LRM;',
            '&RLM;',
        ], '', $inputText);
    }

    /**
     * This function encapsulates all texts in the input with <span dir='xxx'> and </span>
     * according to the directionality specified.
     *
     * @param string $inputText Raw input
     *
     * @return string The string with all texts encapsulated as required
     */
    public static function spanLtrRtl(string $inputText): string
    {
        if ($inputText === '') {
            // Nothing to do
            return '';
        }

        $workingText = str_replace("\n", '<br>', $inputText);
        $workingText = str_replace([
            '<span class="starredname"><br>',
            '<span<br>class="starredname">',
        ], '<br><span class="starredname">', $workingText); // Reposition some incorrectly placed line breaks
        $workingText = self::stripLrmRlm($workingText); // Get rid of any existing UTF8 control codes

        self::$previousState = '';
        self::$currentState  = strtoupper(I18N::direction());
        $numberState         = false; // Set when we're inside a numeric string
        $result              = '';
        self::$waitingText   = '';
        $openParDirection    = [];

        self::beginCurrentSpan($result);

        while ($workingText !== '') {
            $charArray     = self::getChar($workingText, 0); // Get the next ASCII or UTF-8 character
            $currentLetter = $charArray['letter'];
            $currentLen    = $charArray['length'];

            $openParIndex  = strpos(self::OPEN_PARENTHESES, $currentLetter); // Which opening parenthesis is this?
            $closeParIndex = strpos(self::CLOSE_PARENTHESES, $currentLetter); // Which closing parenthesis is this?

            switch ($currentLetter) {
                case '<':
                    // Assume this '<' starts an HTML element
                    $endPos = strpos($workingText, '>'); // look for the terminating '>'
                    if ($endPos === false) {
                        $endPos = 0;
                    }
                    $currentLen += $endPos;
                    $element    = substr($workingText, 0, $currentLen);
                    $temp       = strtolower(substr($element, 0, 3));
                    if (strlen($element) < 7 && $temp === '<br') {
                        if ($numberState) {
                            $numberState = false;
                            if (self::$currentState === 'RTL') {
                                self::$waitingText .= self::UTF8_PDF;
                            }
                        }
                        self::breakCurrentSpan($result);
                    } elseif (self::$waitingText === '') {
                        $result .= $element;
                    } else {
                        self::$waitingText .= $element;
                    }
                    $workingText = substr($workingText, $currentLen);
                    break;
                case '&':
                    // Assume this '&' starts an HTML entity
                    $endPos = strpos($workingText, ';'); // look for the terminating ';'
                    if ($endPos === false) {
                        $endPos = 0;
                    }
                    $currentLen += $endPos;
                    $entity     = substr($workingText, 0, $currentLen);
                    if (strtolower($entity) === '&nbsp;') {
                        $entity = '&nbsp;'; // Ensure consistent case for this entity
                    }
                    if (self::$waitingText === '') {
                        $result .= $entity;
                    } else {
                        self::$waitingText .= $entity;
                    }
                    $workingText = substr($workingText, $currentLen);
                    break;
                case '{':
                    if (substr($workingText, 1, 1) === '{') {
                        // Assume this '{{' starts a TCPDF directive
                        $endPos = strpos($workingText, '}}'); // look for the terminating '}}'
                        if ($endPos === false) {
                            $endPos = 0;
                        }
                        $currentLen        = $endPos + 2;
                        $directive         = substr($workingText, 0, $currentLen);
                        $workingText       = substr($workingText, $currentLen);
                        $result            .= self::$waitingText . $directive;
                        self::$waitingText = '';
                        break;
                    }
                    // no break
                default:
                    // Look for strings of numbers with optional leading or trailing + or -
                    // and with optional embedded numeric punctuation
                    if ($numberState) {
                        // If we're inside a numeric string, look for reasons to end it
                        $offset    = 0; // Be sure to look at the current character first
                        $charArray = self::getChar($workingText . "\n", $offset);
                        if (!str_contains(self::NUMBERS, $charArray['letter'])) {
                            // This is not a digit. Is it numeric punctuation?
                            if (substr($workingText . "\n", $offset, 6) === '&nbsp;') {
                                $offset += 6; // This could be numeric punctuation
                            } elseif (str_contains(self::NUMBER_PUNCTUATION, $charArray['letter'])) {
                                $offset += $charArray['length']; // This could be numeric punctuation
                            }
                            // If the next character is a digit, the current character is numeric punctuation
                            $charArray = self::getChar($workingText . "\n", $offset);
                            if (!str_contains(self::NUMBERS, $charArray['letter'])) {
                                // This is not a digit. End the run of digits and punctuation.
                                $numberState = false;
                                if (self::$currentState === 'RTL') {
                                    if (!str_contains(self::NUMBER_PREFIX, $currentLetter)) {
                                        $currentLetter = self::UTF8_PDF . $currentLetter;
                                    } else {
                                        $currentLetter .= self::UTF8_PDF; // Include a trailing + or - in the run
                                    }
                                }
                            }
                        }
                    } elseif (str_contains(self::NUMBER_PREFIX, $currentLetter)) {
                        // If we're outside a numeric string, look for reasons to start it
                        // This might be a number lead-in
                        $offset   = $currentLen;
                        $nextChar = substr($workingText . "\n", $offset, 1);
                        if (str_contains(self::NUMBERS, $nextChar)) {
                            $numberState = true; // We found a digit: the lead-in is therefore numeric
                            if (self::$currentState === 'RTL') {
                                $currentLetter = self::UTF8_LRE . $currentLetter;
                            }
                        }
                    } elseif (str_contains(self::NUMBERS, $currentLetter)) {
                        $numberState = true; // The current letter is a digit
                        if (self::$currentState === 'RTL') {
                            $currentLetter = self::UTF8_LRE . $currentLetter;
                        }
                    }

                    // Determine the directionality of the current UTF-8 character
                    $newState = self::$currentState;

                    while (true) {
                        if (I18N::scriptDirection(I18N::textScript($currentLetter)) === 'rtl') {
                            if (self::$currentState === '') {
                                $newState = 'RTL';
                                break;
                            }

                            if (self::$currentState === 'RTL') {
                                break;
                            }
                            // Switch to RTL only if this isn't a solitary RTL letter
                            $tempText = substr($workingText, $currentLen);
                            while ($tempText !== '') {
                                $nextCharArray = self::getChar($tempText, 0);
                                $nextLetter    = $nextCharArray['letter'];
                                $nextLen       = $nextCharArray['length'];
                                $tempText      = substr($tempText, $nextLen);

                                if (I18N::scriptDirection(I18N::textScript($nextLetter)) === 'rtl') {
                                    $newState = 'RTL';
                                    break 2;
                                }

                                if (str_contains(self::PUNCTUATION, $nextLetter) || str_contains(self::OPEN_PARENTHESES, $nextLetter)) {
                                    $newState = 'RTL';
                                    break 2;
                                }

                                if ($nextLetter === ' ') {
                                    break;
                                }
                                $nextLetter .= substr($tempText . "\n", 0, 5);
                                if ($nextLetter === '&nbsp;') {
                                    break;
                                }
                            }
                            // This is a solitary RTL letter : wrap it in UTF8 control codes to force LTR directionality
                            $currentLetter = self::UTF8_LRO . $currentLetter . self::UTF8_PDF;
                            $newState      = 'LTR';
                            break;
                        }
                        if ($currentLen !== 1 || $currentLetter >= 'A' && $currentLetter <= 'Z' || $currentLetter >= 'a' && $currentLetter <= 'z') {
                            // Since it’s neither Hebrew nor Arabic, this UTF-8 character or ASCII letter must be LTR
                            $newState = 'LTR';
                            break;
                        }
                        if ($closeParIndex !== false) {
                            // This closing parenthesis has to inherit the matching opening parenthesis' directionality
                            if (!empty($openParDirection[$closeParIndex]) && $openParDirection[$closeParIndex] !== '?') {
                                $newState = $openParDirection[$closeParIndex];
                            }
                            $openParDirection[$closeParIndex] = '';
                            break;
                        }
                        self::$waitingText .= $currentLetter;
                        $workingText       = substr($workingText, $currentLen);
                        if ($openParIndex !== false) {
                            // Opening parentheses always inherit the following directionality
                            while (true) {
                                if ($workingText === '') {
                                    break;
                                }
                                if (str_starts_with($workingText, ' ')) {
                                    // Spaces following this left parenthesis inherit the following directionality too
                                    self::$waitingText .= ' ';
                                    $workingText       = substr($workingText, 1);
                                    continue;
                                }
                                if (str_starts_with($workingText, '&nbsp;')) {
                                    // Spaces following this left parenthesis inherit the following directionality too
                                    self::$waitingText .= '&nbsp;';
                                    $workingText       = substr($workingText, 6);
                                    continue;
                                }
                                break;
                            }
                            $openParDirection[$openParIndex] = '?';
                            break 2; // double break because we're waiting for more information
                        }

                        // We have a digit or a "normal" special character.
                        //
                        // When this character is not at the start of the input string, it inherits the preceding directionality;
                        // at the start of the input string, it assumes the following directionality.
                        //
                        // Exceptions to this rule will be handled later during final clean-up.
                        //
                        if (self::$currentState !== '') {
                            $result            .= self::$waitingText;
                            self::$waitingText = '';
                        }
                        break 2; // double break because we're waiting for more information
                    }
                    if ($newState !== self::$currentState) {
                        // A direction change has occurred
                        self::finishCurrentSpan($result);
                        self::$previousState = self::$currentState;
                        self::$currentState  = $newState;
                        self::beginCurrentSpan($result);
                    }
                    self::$waitingText .= $currentLetter;
                    $workingText       = substr($workingText, $currentLen);
                    $result            .= self::$waitingText;
                    self::$waitingText = '';

                    foreach ($openParDirection as $index => $value) {
                        // Since we now know the proper direction, remember it for all waiting opening parentheses
                        if ($value === '?') {
                            $openParDirection[$index] = self::$currentState;
                        }
                    }

                    break;
            }
        }

        // We're done. Finish last <span> if necessary
        if ($numberState) {
            if (self::$waitingText === '') {
                if (self::$currentState === 'RTL') {
                    $result .= self::UTF8_PDF;
                }
            } elseif (self::$currentState === 'RTL') {
                self::$waitingText .= self::UTF8_PDF;
            }
        }
        self::finishCurrentSpan($result, true);

        // Get rid of any waiting text
        if (self::$waitingText !== '') {
            if (I18N::direction() === 'rtl' && self::$currentState === 'LTR') {
                $result .= self::START_RTL;
                $result .= self::$waitingText;
                $result .= self::END_RTL;
            } else {
                $result .= self::START_LTR;
                $result .= self::$waitingText;
                $result .= self::END_LTR;
            }
            self::$waitingText = '';
        }

        // Lastly, do some more cleanups

        // Move leading RTL numeric strings to following LTR text
        // (this happens when the page direction is RTL and the original text begins with a number and is followed by LTR text)
        while (substr($result, 0, self::LENGTH_START + 3) === self::START_RTL . self::UTF8_LRE) {
            $spanEnd = strpos($result, self::END_RTL . self::START_LTR);
            if ($spanEnd === false) {
                break;
            }
            $textSpan = self::stripLrmRlm(substr($result, self::LENGTH_START + 3, $spanEnd - self::LENGTH_START - 3));
            if (I18N::scriptDirection(I18N::textScript($textSpan)) === 'rtl') {
                break;
            }
            $result = self::START_LTR . substr($result, self::LENGTH_START, $spanEnd - self::LENGTH_START) . substr($result, $spanEnd + self::LENGTH_START + self::LENGTH_END);
            break;
        }

        // On RTL pages, put trailing "." in RTL numeric strings into its own RTL span
        if (I18N::direction() === 'rtl') {
            $result = str_replace(self::UTF8_PDF . '.' . self::END_RTL, self::UTF8_PDF . self::END_RTL . self::START_RTL . '.' . self::END_RTL, $result);
        }

        // Trim trailing blanks preceding <br> in LTR text
        while (self::$previousState !== 'RTL') {
            if (str_contains($result, ' <LTRbr>')) {
                $result = str_replace(' <LTRbr>', '<LTRbr>', $result);
                continue;
            }
            if (str_contains($result, '&nbsp;<LTRbr>')) {
                $result = str_replace('&nbsp;<LTRbr>', '<LTRbr>', $result);
                continue;
            }
            if (str_contains($result, ' <br>')) {
                $result = str_replace(' <br>', '<br>', $result);
                continue;
            }
            if (str_contains($result, '&nbsp;<br>')) {
                $result = str_replace('&nbsp;<br>', '<br>', $result);
                continue;
            }
            break; // Neither space nor &nbsp; : we're done
        }

        // Trim trailing blanks preceding <br> in RTL text
        while (true) {
            if (str_contains($result, ' <RTLbr>')) {
                $result = str_replace(' <RTLbr>', '<RTLbr>', $result);
                continue;
            }
            if (str_contains($result, '&nbsp;<RTLbr>')) {
                $result = str_replace('&nbsp;<RTLbr>', '<RTLbr>', $result);
                continue;
            }
            break; // Neither space nor &nbsp; : we're done
        }

        // Convert '<LTRbr>' and '<RTLbr'
        $result = str_replace([
            '<LTRbr>',
            '<RTLbr>',
        ], [
            self::END_LTR . '<br>' . self::START_LTR,
            self::END_RTL . '<br>' . self::START_RTL,
        ], $result);

        // Include leading indeterminate directional text in whatever follows
        if (substr($result . "\n", 0, self::LENGTH_START) !== self::START_LTR && substr($result . "\n", 0, self::LENGTH_START) !== self::START_RTL && !str_starts_with($result . "\n", '<br>')) {
            $leadingText = '';
            while (true) {
                if ($result === '') {
                    $result = $leadingText;
                    break;
                }
                if (substr($result . "\n", 0, self::LENGTH_START) !== self::START_LTR && substr($result . "\n", 0, self::LENGTH_START) !== self::START_RTL) {
                    $leadingText .= substr($result, 0, 1);
                    $result      = substr($result, 1);
                    continue;
                }
                $result = substr($result, 0, self::LENGTH_START) . $leadingText . substr($result, self::LENGTH_START);
                break;
            }
        }

        // Include solitary "-" and "+" in surrounding RTL text
        $result = str_replace([
            self::END_RTL . self::START_LTR . '-' . self::END_LTR . self::START_RTL,
            self::END_RTL . self::START_LTR . '+' . self::END_LTR . self::START_RTL,
        ], [
            '-',
            '+',
        ], $result);

        //$result = strtr($result, [
        //    self::END_RTL . self::START_LTR . '-' . self::END_LTR . self::START_RTL => '-',
        //    self::END_RTL . self::START_LTR . '+' . self::END_LTR . self::START_RTL => '+',
        //]);

        // Remove empty spans
        $result = str_replace([
            self::START_LTR . self::END_LTR,
            self::START_RTL . self::END_RTL,
        ], '', $result);

        // Finally, correct '<LTR>', '</LTR>', '<RTL>', and '</RTL>'
        // LTR text: <span dir="ltr"> text </span>
        // RTL text: <span dir="rtl"> text </span>

        $result = str_replace([
            self::START_LTR,
            self::END_LTR,
            self::START_RTL,
            self::END_RTL,
        ], [
            '<span dir="ltr">',
            '</span>',
            '<span dir="rtl">',
            '</span>',
        ], $result);

        return $result;
    }

    /**
     * Wrap words that have an asterisk suffix in <u> and </u> tags.
     * This should underline starred names to show the preferred name.
     *
     * @param string $textSpan
     * @param string $direction
     *
     * @return string
     */
    private static function starredName(string $textSpan, string $direction): string
    {
        // To avoid a TCPDF bug that mixes up the word order, insert those <u> and </u> tags
        // only when page and span directions are identical.
        if ($direction === strtoupper(I18N::direction())) {
            while (true) {
                $starPos = strpos($textSpan, '*');
                if ($starPos === false) {
                    break;
                }
                $trailingText = substr($textSpan, $starPos + 1);
                $textSpan     = substr($textSpan, 0, $starPos);
                $wordStart    = strrpos($textSpan, ' '); // Find the start of the word
                if ($wordStart !== false) {
                    $leadingText = substr($textSpan, 0, $wordStart + 1);
                    $wordText    = substr($textSpan, $wordStart + 1);
                } else {
                    $leadingText = '';
                    $wordText    = $textSpan;
                }
                $textSpan = $leadingText . '<u>' . $wordText . '</u>' . $trailingText;
            }
            $textSpan = preg_replace('~<span class="starredname">(.*)</span>~', '<u>\1</u>', $textSpan);
            // The &nbsp; is a work-around for a TCPDF bug eating blanks.
            $textSpan = str_replace([
                ' <u>',
                '</u> ',
            ], [
                '&nbsp;<u>',
                '</u>&nbsp;',
            ], $textSpan);
        } else {
            // Text and page directions differ:  remove the <span> and </span>
            $textSpan = preg_replace('~(.*)\*~', '\1', $textSpan);
            $textSpan = preg_replace('~<span class="starredname">(.*)</span>~', '\1', $textSpan);
        }

        return $textSpan;
    }

    /**
     * Get the next character from an input string
     *
     * @param string $text
     * @param int    $offset
     *
     * @return array{letter:string,length:int}
     */
    private static function getChar(string $text, int $offset): array
    {
        if ($text === '') {
            return [
                'letter' => '',
                'length' => 0,
            ];
        }

        $char   = substr($text, $offset, 1);
        $length = 1;
        if ((ord($char) & 0xE0) === 0xC0) {
            $length = 2;
        }
        if ((ord($char) & 0xF0) === 0xE0) {
            $length = 3;
        }
        if ((ord($char) & 0xF8) === 0xF0) {
            $length = 4;
        }
        $letter = substr($text, $offset, $length);

        return [
            'letter' => $letter,
            'length' => $length,
        ];
    }

    /**
     * Insert <br> into current span
     *
     * @param string $result
     *
     * @return void
     */
    private static function breakCurrentSpan(string &$result): void
    {
        // Interrupt the current span, insert that <br>, and then continue the current span
        $result            .= self::$waitingText;
        self::$waitingText = '';

        $breakString = '<' . self::$currentState . 'br>';
        $result      .= $breakString;
    }

    /**
     * Begin current span
     *
     * @param string $result
     *
     * @return void
     */
    private static function beginCurrentSpan(string &$result): void
    {
        if (self::$currentState === 'LTR') {
            $result .= self::START_LTR;
        }
        if (self::$currentState === 'RTL') {
            $result .= self::START_RTL;
        }

        self::$posSpanStart = strlen($result);
    }

    /**
     * Finish current span
     *
     * @param string $result
     * @param bool   $theEnd
     *
     * @return void
     */
    private static function finishCurrentSpan(string &$result, bool $theEnd = false): void
    {
        $textSpan = substr($result, self::$posSpanStart);
        $result   = substr($result, 0, self::$posSpanStart);

        // Get rid of empty spans, so that our check for presence of RTL will work
        $result = str_replace([
            self::START_LTR . self::END_LTR,
            self::START_RTL . self::END_RTL,
        ], '', $result);

        // Look for numeric strings that are times (hh:mm:ss). These have to be separated from surrounding numbers.
        $tempResult = '';
        while ($textSpan !== '') {
            $posColon = strpos($textSpan, ':');
            if ($posColon === false) {
                break;
            } // No more possible time strings
            $posLRE = strpos($textSpan, self::UTF8_LRE);
            if ($posLRE === false) {
                break;
            } // No more numeric strings
            $posPDF = strpos($textSpan, self::UTF8_PDF, $posLRE);
            if ($posPDF === false) {
                break;
            } // No more numeric strings

            $tempResult    .= substr($textSpan, 0, $posLRE + 3); // Copy everything preceding the numeric string
            $numericString = substr($textSpan, $posLRE + 3, $posPDF - $posLRE); // Separate the entire numeric string
            $textSpan      = substr($textSpan, $posPDF + 3);
            $posColon      = strpos($numericString, ':');
            if ($posColon === false) {
                // Nothing that looks like a time here
                $tempResult .= $numericString;
                continue;
            }
            $posBlank = strpos($numericString . ' ', ' ');
            $posNbsp  = strpos($numericString . '&nbsp;', '&nbsp;');
            if ($posBlank < $posNbsp) {
                $posSeparator    = $posBlank;
                $lengthSeparator = 1;
            } else {
                $posSeparator    = $posNbsp;
                $lengthSeparator = 6;
            }
            if ($posColon > $posSeparator) {
                // We have a time string preceded by a blank: Exclude that blank from the numeric string
                $tempResult    .= substr($numericString, 0, $posSeparator);
                $tempResult    .= self::UTF8_PDF;
                $tempResult    .= substr($numericString, $posSeparator, $lengthSeparator);
                $tempResult    .= self::UTF8_LRE;
                $numericString = substr($numericString, $posSeparator + $lengthSeparator);
            }

            $posBlank = strpos($numericString, ' ');
            $posNbsp  = strpos($numericString, '&nbsp;');
            if ($posBlank === false && $posNbsp === false) {
                // The time string isn't followed by a blank
                $textSpan = $numericString . $textSpan;
                continue;
            }

            // We have a time string followed by a blank: Exclude that blank from the numeric string
            if ($posBlank === false) {
                $posSeparator    = $posNbsp;
                $lengthSeparator = 6;
            } elseif ($posNbsp === false) {
                $posSeparator    = $posBlank;
                $lengthSeparator = 1;
            } elseif ($posBlank < $posNbsp) {
                $posSeparator    = $posBlank;
                $lengthSeparator = 1;
            } else {
                $posSeparator    = $posNbsp;
                $lengthSeparator = 6;
            }
            $tempResult    .= substr($numericString, 0, $posSeparator);
            $tempResult    .= self::UTF8_PDF;
            $tempResult    .= substr($numericString, $posSeparator, $lengthSeparator);
            $posSeparator  += $lengthSeparator;
            $numericString = substr($numericString, $posSeparator);
            $textSpan      = self::UTF8_LRE . $numericString . $textSpan;
        }
        $textSpan       = $tempResult . $textSpan;
        $trailingBlanks = '';
        $trailingBreaks = '';

        /* ****************************** LTR text handling ******************************** */

        if (self::$currentState === 'LTR') {
            // Move trailing numeric strings to the following RTL text. Include any blanks preceding or following the numeric text too.
            if (I18N::direction() === 'rtl' && self::$previousState === 'RTL' && !$theEnd) {
                $trailingString = '';
                $savedSpan      = $textSpan;
                while ($textSpan !== '') {
                    // Look for trailing spaces and tentatively move them
                    if (str_ends_with($textSpan, ' ')) {
                        $trailingString = ' ' . $trailingString;
                        $textSpan       = substr($textSpan, 0, -1);
                        continue;
                    }
                    if (str_ends_with($textSpan, '&nbsp;')) {
                        $trailingString = '&nbsp;' . $trailingString;
                        $textSpan       = substr($textSpan, 0, -1);
                        continue;
                    }
                    if (substr($textSpan, -3) !== self::UTF8_PDF) {
                        // There is no trailing numeric string
                        $textSpan = $savedSpan;
                        break;
                    }

                    // We have a numeric string
                    $posStartNumber = strrpos($textSpan, self::UTF8_LRE);
                    if ($posStartNumber === false) {
                        $posStartNumber = 0;
                    }
                    $trailingString = substr($textSpan, $posStartNumber) . $trailingString;
                    $textSpan       = substr($textSpan, 0, $posStartNumber);

                    // Look for more spaces and move them too
                    while ($textSpan !== '') {
                        if (str_ends_with($textSpan, ' ')) {
                            $trailingString = ' ' . $trailingString;
                            $textSpan       = substr($textSpan, 0, -1);
                            continue;
                        }
                        if (str_ends_with($textSpan, '&nbsp;')) {
                            $trailingString = '&nbsp;' . $trailingString;
                            $textSpan       = substr($textSpan, 0, -1);
                            continue;
                        }
                        break;
                    }

                    self::$waitingText = $trailingString . self::$waitingText;
                    break;
                }
            }

            $savedSpan = $textSpan;
            // Move any trailing <br>, optionally preceded or followed by blanks, outside this LTR span
            while ($textSpan !== '') {
                if (str_ends_with($textSpan, ' ')) {
                    $trailingBlanks = ' ' . $trailingBlanks;
                    $textSpan       = substr($textSpan, 0, -1);
                    continue;
                }
                if (str_ends_with('......' . $textSpan, '&nbsp;')) {
                    $trailingBlanks = '&nbsp;' . $trailingBlanks;
                    $textSpan       = substr($textSpan, 0, -6);
                    continue;
                }
                break;
            }
            while (str_ends_with($textSpan, '<LTRbr>')) {
                $trailingBreaks = '<br>' . $trailingBreaks; // Plain <br> because it’s outside a span
                $textSpan       = substr($textSpan, 0, -7);
            }
            if ($trailingBreaks !== '') {
                while ($textSpan !== '') {
                    if (str_ends_with($textSpan, ' ')) {
                        $trailingBreaks = ' ' . $trailingBreaks;
                        $textSpan       = substr($textSpan, 0, -1);
                        continue;
                    }
                    if (str_ends_with($textSpan, '&nbsp;')) {
                        $trailingBreaks = '&nbsp;' . $trailingBreaks;
                        $textSpan       = substr($textSpan, 0, -6);
                        continue;
                    }
                    break;
                }
                self::$waitingText = $trailingBlanks . self::$waitingText; // Put those trailing blanks inside the following span
            } else {
                $textSpan = $savedSpan;
            }

            $trailingBlanks      = '';
            $trailingPunctuation = '';
            $trailingID          = '';
            $trailingSeparator   = '';
            $leadingSeparator    = '';

            while (I18N::direction() === 'rtl') {
                if (str_contains($result, self::START_RTL)) {
                    // Remove trailing blanks for inclusion in a separate LTR span
                    while ($textSpan !== '') {
                        if (str_ends_with($textSpan, ' ')) {
                            $trailingBlanks = ' ' . $trailingBlanks;
                            $textSpan       = substr($textSpan, 0, -1);
                            continue;
                        }
                        if (str_ends_with($textSpan, '&nbsp;')) {
                            $trailingBlanks = '&nbsp;' . $trailingBlanks;
                            $textSpan       = substr($textSpan, 0, -1);
                            continue;
                        }
                        break;
                    }

                    // Remove trailing punctuation for inclusion in a separate LTR span
                    if ($textSpan === '') {
                        $trailingChar = "\n";
                    } else {
                        $trailingChar = substr($textSpan, -1);
                    }
                    if (str_contains(self::PUNCTUATION, $trailingChar)) {
                        $trailingPunctuation = $trailingChar;
                        $textSpan            = substr($textSpan, 0, -1);
                    }
                }

                // Remove trailing ID numbers that look like "(xnnn)" for inclusion in a separate LTR span
                while (true) {
                    if (!str_ends_with($textSpan, ')')) {
                        break;
                    } // There is no trailing ')'
                    $posLeftParen = strrpos($textSpan, '(');
                    if ($posLeftParen === false) {
                        break;
                    } // There is no leading '('
                    $temp = self::stripLrmRlm(substr($textSpan, $posLeftParen)); // Get rid of UTF8 control codes

                    // If the parenthesized text doesn't look like an ID number,
                    // we don't want to touch it.
                    // This check won’t work if somebody uses ID numbers with an unusual format.
                    $offset    = 1;
                    $charArray = self::getChar($temp, $offset); // Get 1st character of parenthesized text
                    if (str_contains(self::NUMBERS, $charArray['letter'])) {
                        break;
                    }
                    $offset += $charArray['length']; // Point at 2nd character of parenthesized text
                    if (!str_contains(self::NUMBERS, substr($temp, $offset, 1))) {
                        break;
                    }
                    // 1st character of parenthesized text is alpha, 2nd character is a digit; last has to be a digit too
                    if (!str_contains(self::NUMBERS, substr($temp, -2, 1))) {
                        break;
                    }

                    $trailingID = substr($textSpan, $posLeftParen);
                    $textSpan   = substr($textSpan, 0, $posLeftParen);
                    break;
                }

                // Look for " - " or blank preceding the ID number and remove it for inclusion in a separate LTR span
                if ($trailingID !== '') {
                    while ($textSpan !== '') {
                        if (str_ends_with($textSpan, ' ')) {
                            $trailingSeparator = ' ' . $trailingSeparator;
                            $textSpan          = substr($textSpan, 0, -1);
                            continue;
                        }
                        if (str_ends_with($textSpan, '&nbsp;')) {
                            $trailingSeparator = '&nbsp;' . $trailingSeparator;
                            $textSpan          = substr($textSpan, 0, -6);
                            continue;
                        }
                        if (str_ends_with($textSpan, '-')) {
                            $trailingSeparator = '-' . $trailingSeparator;
                            $textSpan          = substr($textSpan, 0, -1);
                            continue;
                        }
                        break;
                    }
                }

                // Look for " - " preceding the text and remove it for inclusion in a separate LTR span
                $foundSeparator = false;
                $savedSpan      = $textSpan;
                while ($textSpan !== '') {
                    if (str_starts_with($textSpan, ' ')) {
                        $leadingSeparator = ' ' . $leadingSeparator;
                        $textSpan         = substr($textSpan, 1);
                        continue;
                    }
                    if (str_starts_with($textSpan, '&nbsp;')) {
                        $leadingSeparator = '&nbsp;' . $leadingSeparator;
                        $textSpan         = substr($textSpan, 6);
                        continue;
                    }
                    if (str_starts_with($textSpan, '-')) {
                        $leadingSeparator = '-' . $leadingSeparator;
                        $textSpan         = substr($textSpan, 1);
                        $foundSeparator   = true;
                        continue;
                    }
                    break;
                }
                if (!$foundSeparator) {
                    $textSpan         = $savedSpan;
                    $leadingSeparator = '';
                }
                break;
            }

            // We're done: finish the span
            $textSpan = self::starredName($textSpan, 'LTR'); // Wrap starred name in <u> and </u> tags
            while (true) {
                // Remove blanks that precede <LTRbr>
                if (str_contains($textSpan, ' <LTRbr>')) {
                    $textSpan = str_replace(' <LTRbr>', '<LTRbr>', $textSpan);
                    continue;
                }
                if (str_contains($textSpan, '&nbsp;<LTRbr>')) {
                    $textSpan = str_replace('&nbsp;<LTRbr>', '<LTRbr>', $textSpan);
                    continue;
                }
                break;
            }
            if ($leadingSeparator !== '') {
                $result .= self::START_LTR . $leadingSeparator . self::END_LTR;
            }
            $result .= $textSpan . self::END_LTR;
            if ($trailingSeparator !== '') {
                $result .= self::START_LTR . $trailingSeparator . self::END_LTR;
            }
            if ($trailingID !== '') {
                $result .= self::START_LTR . $trailingID . self::END_LTR;
            }
            if ($trailingPunctuation !== '') {
                $result .= self::START_LTR . $trailingPunctuation . self::END_LTR;
            }
            if ($trailingBlanks !== '') {
                $result .= self::START_LTR . $trailingBlanks . self::END_LTR;
            }
        }

        /* ****************************** RTL text handling ******************************** */

        if (self::$currentState === 'RTL') {
            $savedSpan = $textSpan;

            // Move any trailing <br>, optionally followed by blanks, outside this RTL span
            while ($textSpan !== '') {
                if (str_ends_with($textSpan, ' ')) {
                    $trailingBlanks = ' ' . $trailingBlanks;
                    $textSpan       = substr($textSpan, 0, -1);
                    continue;
                }
                if (str_ends_with('......' . $textSpan, '&nbsp;')) {
                    $trailingBlanks = '&nbsp;' . $trailingBlanks;
                    $textSpan       = substr($textSpan, 0, -6);
                    continue;
                }
                break;
            }
            while (str_ends_with($textSpan, '<RTLbr>')) {
                $trailingBreaks = '<br>' . $trailingBreaks; // Plain <br> because it’s outside a span
                $textSpan       = substr($textSpan, 0, -7);
            }
            if ($trailingBreaks !== '') {
                self::$waitingText = $trailingBlanks . self::$waitingText; // Put those trailing blanks inside the following span
            } else {
                $textSpan = $savedSpan;
            }

            // Move trailing numeric strings to the following LTR text. Include any blanks preceding or following the numeric text too.
            if (!$theEnd && I18N::direction() !== 'rtl') {
                $trailingString = '';
                $savedSpan      = $textSpan;
                while ($textSpan !== '') {
                    // Look for trailing spaces and tentatively move them
                    if (str_ends_with($textSpan, ' ')) {
                        $trailingString = ' ' . $trailingString;
                        $textSpan       = substr($textSpan, 0, -1);
                        continue;
                    }
                    if (str_ends_with($textSpan, '&nbsp;')) {
                        $trailingString = '&nbsp;' . $trailingString;
                        $textSpan       = substr($textSpan, 0, -1);
                        continue;
                    }
                    if (substr($textSpan, -3) !== self::UTF8_PDF) {
                        // There is no trailing numeric string
                        $textSpan = $savedSpan;
                        break;
                    }

                    // We have a numeric string
                    $posStartNumber = strrpos($textSpan, self::UTF8_LRE);
                    if ($posStartNumber === false) {
                        $posStartNumber = 0;
                    }
                    $trailingString = substr($textSpan, $posStartNumber) . $trailingString;
                    $textSpan       = substr($textSpan, 0, $posStartNumber);

                    // Look for more spaces and move them too
                    while ($textSpan !== '') {
                        if (str_ends_with($textSpan, ' ')) {
                            $trailingString = ' ' . $trailingString;
                            $textSpan       = substr($textSpan, 0, -1);
                            continue;
                        }
                        if (str_ends_with($textSpan, '&nbsp;')) {
                            $trailingString = '&nbsp;' . $trailingString;
                            $textSpan       = substr($textSpan, 0, -1);
                            continue;
                        }
                        break;
                    }

                    self::$waitingText = $trailingString . self::$waitingText;
                    break;
                }
            }

            // Trailing " - " needs to be prefixed to the following span
            if (!$theEnd && str_ends_with('...' . $textSpan, ' - ')) {
                $textSpan          = substr($textSpan, 0, -3);
                self::$waitingText = ' - ' . self::$waitingText;
            }

            while (I18N::direction() === 'rtl') {
                // Look for " - " preceding <RTLbr> and relocate it to the front of the string
                $posDashString = strpos($textSpan, ' - <RTLbr>');
                if ($posDashString === false) {
                    break;
                }
                $posStringStart = strrpos(substr($textSpan, 0, $posDashString), '<RTLbr>');
                if ($posStringStart === false) {
                    $posStringStart = 0;
                } else {
                    $posStringStart += 9;
                } // Point to the first char following the last <RTLbr>

                $textSpan = substr($textSpan, 0, $posStringStart) . ' - ' . substr($textSpan, $posStringStart, $posDashString - $posStringStart) . substr($textSpan, $posDashString + 3);
            }

            // Strip leading spaces from the RTL text
            $countLeadingSpaces = 0;
            while ($textSpan !== '') {
                if (str_starts_with($textSpan, ' ')) {
                    $countLeadingSpaces++;
                    $textSpan = substr($textSpan, 1);
                    continue;
                }
                if (str_starts_with($textSpan, '&nbsp;')) {
                    $countLeadingSpaces++;
                    $textSpan = substr($textSpan, 6);
                    continue;
                }
                break;
            }

            // Strip trailing spaces from the RTL text
            $countTrailingSpaces = 0;
            while ($textSpan !== '') {
                if (str_ends_with($textSpan, ' ')) {
                    $countTrailingSpaces++;
                    $textSpan = substr($textSpan, 0, -1);
                    continue;
                }
                if (str_ends_with($textSpan, '&nbsp;')) {
                    $countTrailingSpaces++;
                    $textSpan = substr($textSpan, 0, -6);
                    continue;
                }
                break;
            }

            // Look for trailing " -", reverse it, and relocate it to the front of the string
            if (str_ends_with($textSpan, ' -')) {
                $posDashString  = strlen($textSpan) - 2;
                $posStringStart = strrpos(substr($textSpan, 0, $posDashString), '<RTLbr>');
                if ($posStringStart === false) {
                    $posStringStart = 0;
                } else {
                    $posStringStart += 9;
                } // Point to the first char following the last <RTLbr>

                $textSpan = substr($textSpan, 0, $posStringStart) . '- ' . substr($textSpan, $posStringStart, $posDashString - $posStringStart) . substr($textSpan, $posDashString + 2);
            }

            if ($countLeadingSpaces !== 0) {
                $newLength = strlen($textSpan) + $countLeadingSpaces;
                $textSpan  = str_pad($textSpan, $newLength, ' ', I18N::direction() === 'rtl' ? STR_PAD_LEFT : STR_PAD_RIGHT);
            }
            if ($countTrailingSpaces !== 0) {
                if (I18N::direction() === 'ltr') {
                    if ($trailingBreaks === '') {
                        // Move trailing RTL spaces to front of following LTR span
                        $newLength         = strlen(self::$waitingText) + $countTrailingSpaces;
                        self::$waitingText = str_pad(self::$waitingText, $newLength, ' ', STR_PAD_LEFT);
                    }
                } else {
                    $newLength = strlen($textSpan) + $countTrailingSpaces;
                    $textSpan  = str_pad($textSpan, $newLength);
                }
            }

            // We're done: finish the span
            $textSpan = self::starredName($textSpan, 'RTL'); // Wrap starred name in <u> and </u> tags
            $result   .= $textSpan . self::END_RTL;
        }

        if (self::$currentState !== 'LTR' && self::$currentState !== 'RTL') {
            $result .= $textSpan;
        }

        $result .= $trailingBreaks; // Get rid of any waiting <br>
    }
}
