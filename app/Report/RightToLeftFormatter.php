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

namespace Fisharebest\Webtrees\Report;

use Fisharebest\Webtrees\Encodings\UTF8;
use Fisharebest\Webtrees\I18N;

use function ord;
use function preg_replace;
use function str_contains;
use function str_ends_with;
use function str_pad;
use function str_replace;
use function str_starts_with;
use function strlen;
use function strpos;
use function strrpos;
use function strtolower;
use function strtoupper;
use function substr;

use const STR_PAD_LEFT;
use const STR_PAD_RIGHT;

/**
 * Formats bidirectional text by wrapping LTR and RTL runs in directional
 * <span> elements for correct rendering in PDF reports.
 *
 * Each instance processes a single input string.  The mutable processing
 * state lives on the instance, making the formatter re-entrant and
 * thread-safe (unlike the legacy static implementation).
 */
final class RightToLeftFormatter
{
    private const string OPEN_PARENTHESES = '([{';
    private const string CLOSE_PARENTHESES = ')]}';

    private const string NUMBERS = '0123456789';

    /** Treat these like numbers when at the beginning or end of numeric strings */
    private const string NUMBER_PREFIX = '+-';

    /** Treat these like numbers when inside numeric strings */
    private const string NUMBER_PUNCTUATION = '- ,.:/';

    private const string PUNCTUATION = ',.:;?!';

    // Intermediate markup tokens (replaced with real HTML at the end)
    private const string START_LTR    = '<LTR>';
    private const string END_LTR      = '</LTR>';
    private const string START_RTL    = '<RTL>';
    private const string END_RTL      = '</RTL>';
    private const int LENGTH_START = 5;
    private const int LENGTH_END   = 6;

    private string $previousState = '';

    private string $currentState;

    private string $waitingText = '';

    private int $posSpanStart = 0;

    /**
     * Format bidirectional text by wrapping runs in directional spans.
     *
     * This is the main entry point.  It processes the input character by
     * character, identifies directional runs, and wraps them in appropriate
     * <span dir="ltr"> or <span dir="rtl"> elements.
     */
    public function format(string $inputText): string
    {
        if ($inputText === '') {
            return '';
        }

        $workingText = str_replace("\n", '<br>', $inputText);
        $workingText = str_replace([
            '<span class="starredname"><br>',
            '<span<br>class="starredname">',
        ], '<br><span class="starredname">', $workingText);
        $workingText = self::stripLrmRlm($workingText);

        $this->currentState = strtoupper(I18N::direction());
        $numberState        = false;
        $result             = '';
        $openParDirection   = [];

        $this->beginCurrentSpan($result);

        while ($workingText !== '') {
            $charArray     = self::getChar($workingText, 0);
            $currentLetter = $charArray['letter'];
            $currentLen    = $charArray['length'];

            $openParIndex  = strpos(self::OPEN_PARENTHESES, $currentLetter);
            $closeParIndex = strpos(self::CLOSE_PARENTHESES, $currentLetter);

            switch ($currentLetter) {
                case '<':
                    $workingText = $this->processHtmlElement($workingText, $currentLen, $result, $numberState);
                    break;
                case '&':
                    $workingText = $this->processHtmlEntity($workingText, $currentLen, $result);
                    break;
                case '{':
                    if (substr($workingText, 1, 1) === '{') {
                        $workingText = $this->processTcpdfDirective($workingText, $result);
                        break;
                    }
                    // fall through to default processing
                    // no break
                default:
                    $workingText = $this->processCharacter(
                        $workingText,
                        $currentLetter,
                        $currentLen,
                        $result,
                        $numberState,
                        $openParDirection,
                        $openParIndex,
                        $closeParIndex,
                    );
                    break;
            }
        }

        // Finish last span
        if ($numberState) {
            if ($this->waitingText === '') {
                if ($this->currentState === 'RTL') {
                    $result .= UTF8::POP_DIRECTIONAL_FORMATTING;
                }
            } elseif ($this->currentState === 'RTL') {
                $this->waitingText .= UTF8::POP_DIRECTIONAL_FORMATTING;
            }
        }
        $this->finishCurrentSpan($result, true);

        // Flush any remaining waiting text
        if ($this->waitingText !== '') {
            if (I18N::direction() === 'rtl' && $this->currentState === 'LTR') {
                $result .= self::START_RTL . $this->waitingText . self::END_RTL;
            } else {
                $result .= self::START_LTR . $this->waitingText . self::END_LTR;
            }
            $this->waitingText = '';
        }

        $result = $this->postProcess($result);

        return $this->convertMarkupToHtml($result);
    }

    /**
     * Process an HTML element (anything starting with '<').
     */
    private function processHtmlElement(string $workingText, int $currentLen, string &$result, bool &$numberState): string
    {
        $endPos = strpos($workingText, '>');
        if ($endPos === false) {
            $endPos = 0;
        }
        $currentLen += $endPos;
        $element    = substr($workingText, 0, $currentLen);
        $temp       = strtolower(substr($element, 0, 3));
        if (strlen($element) < 7 && $temp === '<br') {
            if ($numberState) {
                $numberState = false;
                if ($this->currentState === 'RTL') {
                    $this->waitingText .= UTF8::POP_DIRECTIONAL_FORMATTING;
                }
            }
            $this->breakCurrentSpan($result);
        } elseif ($this->waitingText === '') {
            $result .= $element;
        } else {
            $this->waitingText .= $element;
        }

        return substr($workingText, $currentLen);
    }

    /**
     * Process an HTML entity (anything starting with '&').
     */
    private function processHtmlEntity(string $workingText, int $currentLen, string &$result): string
    {
        $endPos = strpos($workingText, ';');
        if ($endPos === false) {
            $endPos = 0;
        }
        $currentLen += $endPos;
        $entity     = substr($workingText, 0, $currentLen);
        if (strtolower($entity) === '&nbsp;') {
            $entity = '&nbsp;';
        }
        if ($this->waitingText === '') {
            $result .= $entity;
        } else {
            $this->waitingText .= $entity;
        }

        return substr($workingText, $currentLen);
    }

    /**
     * Process a TCPDF directive (anything starting with '{{').
     */
    private function processTcpdfDirective(string $workingText, string &$result): string
    {
        $endPos = strpos($workingText, '}}');
        if ($endPos === false) {
            $endPos = 0;
        }
        $currentLen        = $endPos + 2;
        $directive         = substr($workingText, 0, $currentLen);
        $result            .= $this->waitingText . $directive;
        $this->waitingText = '';

        return substr($workingText, $currentLen);
    }

    /**
     * Process a regular character, including numeric string detection and
     * directionality determination.
     *
     * @param array<int,string> $openParDirection
     */
    private function processCharacter(
        string $workingText,
        string $currentLetter,
        int $currentLen,
        string &$result,
        bool &$numberState,
        array &$openParDirection,
        int|false $openParIndex,
        int|false $closeParIndex,
    ): string {
        // Handle numeric string state
        $currentLetter = $this->processNumericState($workingText, $currentLetter, $currentLen, $numberState);

        // Determine the directionality of the current character
        $newState = $this->currentState;

        while (true) {
            if (I18N::scriptDirection(I18N::textScript($currentLetter)) === 'rtl') {
                if ($this->currentState === '') {
                    $newState = 'RTL';
                    break;
                }

                if ($this->currentState === 'RTL') {
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
                // Solitary RTL letter — force LTR directionality
                $currentLetter = UTF8::LEFT_TO_RIGHT_OVERRIDE . $currentLetter . UTF8::POP_DIRECTIONAL_FORMATTING;
                $newState      = 'LTR';
                break;
            }
            if ($currentLen !== 1 || $currentLetter >= 'A' && $currentLetter <= 'Z' || $currentLetter >= 'a' && $currentLetter <= 'z') {
                // Multi-byte or ASCII letter: must be LTR
                $newState = 'LTR';
                break;
            }
            if ($closeParIndex !== false) {
                // Closing parenthesis inherits the matching opening parenthesis' directionality
                if (!empty($openParDirection[$closeParIndex]) && $openParDirection[$closeParIndex] !== '?') {
                    $newState = $openParDirection[$closeParIndex];
                }
                $openParDirection[$closeParIndex] = '';
                break;
            }
            $this->waitingText .= $currentLetter;
            $workingText       = substr($workingText, $currentLen);
            if ($openParIndex !== false) {
                // Opening parentheses inherit the following directionality
                while (true) {
                    if ($workingText === '') {
                        break;
                    }
                    if (str_starts_with($workingText, ' ')) {
                        $this->waitingText .= ' ';
                        $workingText       = substr($workingText, 1);
                        continue;
                    }
                    if (str_starts_with($workingText, '&nbsp;')) {
                        $this->waitingText .= '&nbsp;';
                        $workingText       = substr($workingText, 6);
                        continue;
                    }
                    break;
                }
                $openParDirection[$openParIndex] = '?';
                return $workingText; // Waiting for more information
            }

            // Digit or special character: inherits surrounding directionality
            if ($this->currentState !== '') {
                $result            .= $this->waitingText;
                $this->waitingText = '';
            }
            return $workingText; // Waiting for more information
        }

        if ($newState !== $this->currentState) {
            // Direction change occurred
            $this->finishCurrentSpan($result);
            $this->previousState = $this->currentState;
            $this->currentState  = $newState;
            $this->beginCurrentSpan($result);
        }
        $this->waitingText .= $currentLetter;
        $workingText       = substr($workingText, $currentLen);
        $result            .= $this->waitingText;
        $this->waitingText = '';

        foreach ($openParDirection as $index => $value) {
            if ($value === '?') {
                $openParDirection[$index] = $this->currentState;
            }
        }

        return $workingText;
    }

    /**
     * Handle numeric string detection and embedding.
     *
     * Updates $numberState and may prepend/append Unicode embedding codes
     * to the current letter.
     */
    private function processNumericState(string $workingText, string $currentLetter, int $currentLen, bool &$numberState): string
    {
        if ($numberState) {
            // Inside a numeric string: look for reasons to end it
            $offset    = 0;
            $charArray = self::getChar($workingText . "\n", $offset);
            if (!str_contains(self::NUMBERS, $charArray['letter'])) {
                // Not a digit — check for numeric punctuation
                if (str_starts_with($workingText . "\n", '&nbsp;')) {
                    $offset += 6;
                } elseif (str_contains(self::NUMBER_PUNCTUATION, $charArray['letter'])) {
                    $offset += $charArray['length'];
                }
                // If next character is a digit, this is numeric punctuation
                $charArray = self::getChar($workingText . "\n", $offset);
                if (!str_contains(self::NUMBERS, $charArray['letter'])) {
                    // End the numeric run
                    $numberState = false;
                    if ($this->currentState === 'RTL') {
                        if (!str_contains(self::NUMBER_PREFIX, $currentLetter)) {
                            $currentLetter = UTF8::POP_DIRECTIONAL_FORMATTING . $currentLetter;
                        } else {
                            $currentLetter .= UTF8::POP_DIRECTIONAL_FORMATTING;
                        }
                    }
                }
            }
        } elseif (str_contains(self::NUMBER_PREFIX, $currentLetter)) {
            // Outside a numeric string: check for number lead-in
            $offset   = $currentLen;
            $nextChar = substr($workingText . "\n", $offset, 1);
            if (str_contains(self::NUMBERS, $nextChar)) {
                $numberState = true;
                if ($this->currentState === 'RTL') {
                    $currentLetter = UTF8::LEFT_TO_RIGHT_EMBEDDING . $currentLetter;
                }
            }
        } elseif (str_contains(self::NUMBERS, $currentLetter)) {
            $numberState = true;
            if ($this->currentState === 'RTL') {
                $currentLetter = UTF8::LEFT_TO_RIGHT_EMBEDDING . $currentLetter;
            }
        }

        return $currentLetter;
    }

    /**
     * Apply post-processing cleanups to the result after the main loop.
     */
    private function postProcess(string $result): string
    {
        // Move leading RTL numeric strings to following LTR text
        while (substr($result, 0, self::LENGTH_START + 3) === self::START_RTL . UTF8::LEFT_TO_RIGHT_EMBEDDING) {
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
            $result = str_replace(UTF8::POP_DIRECTIONAL_FORMATTING . '.' . self::END_RTL, UTF8::POP_DIRECTIONAL_FORMATTING . self::END_RTL . self::START_RTL . '.' . self::END_RTL, $result);
        }

        // Trim trailing blanks preceding <br> in LTR text
        while ($this->previousState !== 'RTL') {
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
            break;
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
            break;
        }

        // Convert '<LTRbr>' and '<RTLbr>'
        $result = str_replace(
            ['<LTRbr>', '<RTLbr>'],
            [self::END_LTR . '<br>' . self::START_LTR, self::END_RTL . '<br>' . self::START_RTL],
            $result
        );

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
        ], ['-', '+'], $result);

        // Remove empty spans
        $result = str_replace([
            self::START_LTR . self::END_LTR,
            self::START_RTL . self::END_RTL,
        ], '', $result);

        return $result;
    }

    /**
     * Convert the intermediate <LTR>/<RTL> markup tokens to real HTML spans.
     */
    private function convertMarkupToHtml(string $result): string
    {
        return str_replace(
            [self::START_LTR, self::END_LTR, self::START_RTL, self::END_RTL],
            ['<span dir="ltr">', '</span>', '<span dir="rtl">', '</span>'],
            $result
        );
    }

    private static function stripLrmRlm(string $inputText): string
    {
        return str_replace([
            UTF8::LEFT_TO_RIGHT_MARK,
            UTF8::RIGHT_TO_LEFT_MARK,
            UTF8::LEFT_TO_RIGHT_OVERRIDE,
            UTF8::RIGHT_TO_LEFT_OVERRIDE,
            UTF8::LEFT_TO_RIGHT_EMBEDDING,
            UTF8::RIGHT_TO_LEFT_EMBEDDING,
            UTF8::POP_DIRECTIONAL_FORMATTING,
            '&lrm;',
            '&rlm;',
            '&LRM;',
            '&RLM;',
        ], '', $inputText);
    }

    /**
     * Get the next ASCII or UTF-8 character from a string at the given offset.
     *
     * @return array{letter:string,length:int}
     */
    private static function getChar(string $text, int $offset): array
    {
        if ($text === '') {
            return ['letter' => '', 'length' => 0];
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

        return ['letter' => $letter, 'length' => $length];
    }

    private function breakCurrentSpan(string &$result): void
    {
        $result            .= $this->waitingText;
        $this->waitingText = '';
        $result            .= '<' . $this->currentState . 'br>';
    }

    private function beginCurrentSpan(string &$result): void
    {
        if ($this->currentState === 'LTR') {
            $result .= self::START_LTR;
        }
        if ($this->currentState === 'RTL') {
            $result .= self::START_RTL;
        }
        $this->posSpanStart = strlen($result);
    }

    private function finishCurrentSpan(string &$result, bool $theEnd = false): void
    {
        $textSpan = substr($result, $this->posSpanStart);
        $result   = substr($result, 0, $this->posSpanStart);

        // Remove empty spans
        $result = str_replace([
            self::START_LTR . self::END_LTR,
            self::START_RTL . self::END_RTL,
        ], '', $result);

        // Separate time strings from surrounding numbers
        $textSpan = $this->separateTimeStrings($textSpan);

        $trailingBlanks = '';
        $trailingBreaks = '';

        if ($this->currentState === 'LTR') {
            $this->finishLtrSpan($result, $textSpan, $trailingBlanks, $trailingBreaks, $theEnd);
        } elseif ($this->currentState === 'RTL') {
            $this->finishRtlSpan($result, $textSpan, $trailingBlanks, $trailingBreaks, $theEnd);
        } else {
            $result .= $textSpan;
        }

        $result .= $trailingBreaks;
    }

    /**
     * Separate time strings (hh:mm:ss) from surrounding numeric runs.
     */
    private function separateTimeStrings(string $textSpan): string
    {
        $tempResult = '';
        while ($textSpan !== '') {
            $posColon = strpos($textSpan, ':');
            if ($posColon === false) {
                break;
            }
            $posLRE = strpos($textSpan, UTF8::LEFT_TO_RIGHT_EMBEDDING);
            if ($posLRE === false) {
                break;
            }
            $posPDF = strpos($textSpan, UTF8::POP_DIRECTIONAL_FORMATTING, $posLRE);
            if ($posPDF === false) {
                break;
            }

            $tempResult    .= substr($textSpan, 0, $posLRE + 3);
            $numericString = substr($textSpan, $posLRE + 3, $posPDF - $posLRE);
            $textSpan      = substr($textSpan, $posPDF + 3);
            $posColon      = strpos($numericString, ':');
            if ($posColon === false) {
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
                $tempResult    .= substr($numericString, 0, $posSeparator);
                $tempResult    .= UTF8::POP_DIRECTIONAL_FORMATTING;
                $tempResult    .= substr($numericString, $posSeparator, $lengthSeparator);
                $tempResult    .= UTF8::LEFT_TO_RIGHT_EMBEDDING;
                $numericString = substr($numericString, $posSeparator + $lengthSeparator);
            }

            $posBlank = strpos($numericString, ' ');
            $posNbsp  = strpos($numericString, '&nbsp;');
            if ($posBlank === false && $posNbsp === false) {
                $textSpan = $numericString . $textSpan;
                continue;
            }

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
            $tempResult    .= UTF8::POP_DIRECTIONAL_FORMATTING;
            $tempResult    .= substr($numericString, $posSeparator, $lengthSeparator);
            $posSeparator  += $lengthSeparator;
            $numericString = substr($numericString, $posSeparator);
            $textSpan      = UTF8::LEFT_TO_RIGHT_EMBEDDING . $numericString . $textSpan;
        }

        return $tempResult . $textSpan;
    }

    /**
     * Finish processing an LTR text span.
     */
    private function finishLtrSpan(string &$result, string $textSpan, string &$trailingBlanks, string &$trailingBreaks, bool $theEnd): void
    {
        // Move trailing numeric strings to following RTL text
        if (I18N::direction() === 'rtl' && $this->previousState === 'RTL' && !$theEnd) {
            $trailingString = '';
            $savedSpan      = $textSpan;
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
                if (substr($textSpan, -3) !== UTF8::POP_DIRECTIONAL_FORMATTING) {
                    $textSpan = $savedSpan;
                    break;
                }
                $posStartNumber = strrpos($textSpan, UTF8::LEFT_TO_RIGHT_EMBEDDING);
                if ($posStartNumber === false) {
                    $posStartNumber = 0;
                }
                $trailingString = substr($textSpan, $posStartNumber) . $trailingString;
                $textSpan       = substr($textSpan, 0, $posStartNumber);

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
                $this->waitingText = $trailingString . $this->waitingText;
                break;
            }
        }

        $savedSpan = $textSpan;
        // Move trailing <br>, optionally preceded or followed by blanks, outside this LTR span
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
            $trailingBreaks = '<br>' . $trailingBreaks;
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
            $this->waitingText = $trailingBlanks . $this->waitingText;
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
                // Remove trailing punctuation
                $trailingChar = $textSpan === '' ? "\n" : substr($textSpan, -1);
                if (str_contains(self::PUNCTUATION, $trailingChar)) {
                    $trailingPunctuation = $trailingChar;
                    $textSpan            = substr($textSpan, 0, -1);
                }
            }

            // Remove trailing ID numbers that look like "(xnnn)"
            while (true) {
                if (!str_ends_with($textSpan, ')')) {
                    break;
                }
                $posLeftParen = strrpos($textSpan, '(');
                if ($posLeftParen === false) {
                    break;
                }
                $temp = self::stripLrmRlm(substr($textSpan, $posLeftParen));
                $offset    = 1;
                $charArray = self::getChar($temp, $offset);
                if (str_contains(self::NUMBERS, $charArray['letter'])) {
                    break;
                }
                $offset += $charArray['length'];
                if (!str_contains(self::NUMBERS, substr($temp, $offset, 1))) {
                    break;
                }
                if (!str_contains(self::NUMBERS, substr($temp, -2, 1))) {
                    break;
                }
                $trailingID = substr($textSpan, $posLeftParen);
                $textSpan   = substr($textSpan, 0, $posLeftParen);
                break;
            }

            // Look for " - " or blank preceding the ID number
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

            // Look for " - " preceding the text
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

        // Finish the LTR span
        $textSpan = self::starredName($textSpan, 'LTR');
        while (true) {
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

    /**
     * Finish processing an RTL text span.
     */
    private function finishRtlSpan(string &$result, string $textSpan, string &$trailingBlanks, string &$trailingBreaks, bool $theEnd): void
    {
        $savedSpan = $textSpan;

        // Move trailing <br>, optionally followed by blanks, outside this RTL span
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
            $trailingBreaks = '<br>' . $trailingBreaks;
            $textSpan       = substr($textSpan, 0, -7);
        }
        if ($trailingBreaks !== '') {
            $this->waitingText = $trailingBlanks . $this->waitingText;
        } else {
            $textSpan = $savedSpan;
        }

        // Move trailing numeric strings to following LTR text
        if (!$theEnd && I18N::direction() !== 'rtl') {
            $trailingString = '';
            $savedSpan      = $textSpan;
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
                if (substr($textSpan, -3) !== UTF8::POP_DIRECTIONAL_FORMATTING) {
                    $textSpan = $savedSpan;
                    break;
                }
                $posStartNumber = strrpos($textSpan, UTF8::LEFT_TO_RIGHT_EMBEDDING);
                if ($posStartNumber === false) {
                    $posStartNumber = 0;
                }
                $trailingString = substr($textSpan, $posStartNumber) . $trailingString;
                $textSpan       = substr($textSpan, 0, $posStartNumber);

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
                $this->waitingText = $trailingString . $this->waitingText;
                break;
            }
        }

        // Trailing " - " needs to be prefixed to the following span
        if (!$theEnd && str_ends_with('...' . $textSpan, ' - ')) {
            $textSpan          = substr($textSpan, 0, -3);
            $this->waitingText = ' - ' . $this->waitingText;
        }

        while (I18N::direction() === 'rtl') {
            // Look for " - " preceding <RTLbr> and relocate it
            $posDashString = strpos($textSpan, ' - <RTLbr>');
            if ($posDashString === false) {
                break;
            }
            $posStringStart = strrpos(substr($textSpan, 0, $posDashString), '<RTLbr>');
            if ($posStringStart === false) {
                $posStringStart = 0;
            } else {
                $posStringStart += 9;
            }
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

        // Look for trailing " -", reverse it, and relocate it
        if (str_ends_with($textSpan, ' -')) {
            $posDashString  = strlen($textSpan) - 2;
            $posStringStart = strrpos(substr($textSpan, 0, $posDashString), '<RTLbr>');
            if ($posStringStart === false) {
                $posStringStart = 0;
            } else {
                $posStringStart += 9;
            }
            $textSpan = substr($textSpan, 0, $posStringStart) . '- ' . substr($textSpan, $posStringStart, $posDashString - $posStringStart) . substr($textSpan, $posDashString + 2);
        }

        if ($countLeadingSpaces !== 0) {
            $newLength = strlen($textSpan) + $countLeadingSpaces;
            $textSpan  = str_pad($textSpan, $newLength, ' ', I18N::direction() === 'rtl' ? STR_PAD_LEFT : STR_PAD_RIGHT);
        }
        if ($countTrailingSpaces !== 0) {
            if (I18N::direction() === 'ltr') {
                if ($trailingBreaks === '') {
                    $newLength         = strlen($this->waitingText) + $countTrailingSpaces;
                    $this->waitingText = str_pad($this->waitingText, $newLength, ' ', STR_PAD_LEFT);
                }
            } else {
                $newLength = strlen($textSpan) + $countTrailingSpaces;
                $textSpan  = str_pad($textSpan, $newLength);
            }
        }

        // Finish the RTL span
        $textSpan = self::starredName($textSpan, 'RTL');
        $result   .= $textSpan . self::END_RTL;
    }

    /**
     * Wrap words that have an asterisk suffix in <u> and </u> tags.
     * This underlines starred names to show the preferred name.
     */
    private static function starredName(string $textSpan, string $direction): string
    {
        if ($direction === strtoupper(I18N::direction())) {
            while (true) {
                $starPos = strpos($textSpan, '*');
                if ($starPos === false) {
                    break;
                }
                $trailingText = substr($textSpan, $starPos + 1);
                $textSpan     = substr($textSpan, 0, $starPos);
                $wordStart    = strrpos($textSpan, ' ');
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
            $textSpan = str_replace([' <u>', '</u> '], ['&nbsp;<u>', '</u>&nbsp;'], $textSpan);
        } else {
            $textSpan = preg_replace('~(.*)\*~', '\1', $textSpan);
            $textSpan = preg_replace('~<span class="starredname">(.*)</span>~', '\1', $textSpan);
        }

        return $textSpan;
    }
}
