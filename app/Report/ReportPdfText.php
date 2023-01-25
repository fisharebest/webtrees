<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use function count;
use function explode;
use function hexdec;
use function preg_match;
use function str_replace;
use function substr_count;

/**
 * Class ReportPdfText
 */
class ReportPdfText extends ReportBaseText
{
    /**
     * PDF Text renderer
     *
     * @param PdfRenderer $renderer
     *
     * @return void
     */
    public function render($renderer): void
    {
        // Set up the style
        if ($renderer->getCurrentStyle() !== $this->styleName) {
            $renderer->setCurrentStyle($this->styleName);
        }
        $temptext = str_replace('#PAGENUM#', (string) $renderer->tcpdf->PageNo(), $this->text);
        // underline «title» part of Source item
        $temptext = str_replace([
            '«',
            '»',
        ], [
            '<u>',
            '</u>',
        ], $temptext);

        // Paint the text color or they might use inherited colors by the previous function
        $match = [];
        if (preg_match('/#?(..)(..)(..)/', $this->color, $match)) {
            $r = hexdec($match[1]);
            $g = hexdec($match[2]);
            $b = hexdec($match[3]);
            $renderer->tcpdf->setTextColor($r, $g, $b);
        } else {
            $renderer->tcpdf->setTextColor(0, 0, 0);
        }
        $temptext = RightToLeftSupport::spanLtrRtl($temptext);
        $temptext = str_replace(
            [
                '<br><span dir="rtl">',
                '<br><span dir="ltr">',
                '> ',
                ' <',
            ],
            [
                '<span dir="rtl" ><br>',
                '<span dir="ltr" ><br>',
                '>&nbsp;',
                '&nbsp;<',
            ],
            $temptext
        );
        $renderer->tcpdf->writeHTML(
            $temptext,
            false,
            false,
            true,
            false,
            ''
        ); //change height - line break etc. - the form is mirror on rtl pages
        // Reset the text color to black or it will be inherited
        $renderer->tcpdf->setTextColor(0, 0, 0);
    }

    /**
     * Returns the height in points of the text element
     * The height is already calculated in getWidth()
     *
     * @param PdfRenderer $renderer
     *
     * @return float
     */
    public function getHeight($renderer): float
    {
        return 0;
    }

    /**
     * Splits the text into lines if necessary to fit into a giving cell
     *
     * @param PdfRenderer $renderer
     *
     * @return array{0:float,1:int,2:float}
     */
    public function getWidth($renderer): array
    {
        // Setup the style name, a font must be selected to calculate the width
        if ($renderer->getCurrentStyle() !== $this->styleName) {
            $renderer->setCurrentStyle($this->styleName);
        }

        // Check for the largest font size in the box
        $fsize = $renderer->getCurrentStyleHeight();
        if ($fsize > $renderer->largestFontHeight) {
            $renderer->largestFontHeight = $fsize;
        }

        // Get the line width for the text in points
        $lw = $renderer->tcpdf->GetStringWidth($this->text);
        // Line Feed counter - Number of lines in the text
        $lfct = substr_count($this->text, "\n") + 1;
        // If there is still remaining wrap width...
        $wrapWidthRemaining = $this->wrapWidthRemaining;
        if ($wrapWidthRemaining > 0) {
            // Check with line counter too!
            if ($lw >= $wrapWidthRemaining || $lfct > 1) {
                $newtext = '';
                $lines   = explode("\n", $this->text);
                // Go throught the text line by line
                foreach ($lines as $line) {
                    // Line width in points + a little margin
                    $lw = $renderer->tcpdf->GetStringWidth($line);
                    // If the line has to be wraped
                    if ($lw > $wrapWidthRemaining) {
                        $words    = explode(' ', $line);
                        $addspace = count($words);
                        $lw       = 0;
                        foreach ($words as $word) {
                            $addspace--;
                            $lw += $renderer->tcpdf->GetStringWidth($word . ' ');
                            if ($lw <= $wrapWidthRemaining) {
                                $newtext .= $word;
                                if ($addspace !== 0) {
                                    $newtext .= ' ';
                                }
                            } else {
                                $lw = $renderer->tcpdf->GetStringWidth($word . ' ');
                                $newtext .= "\n$word";
                                if ($addspace !== 0) {
                                    $newtext .= ' ';
                                }
                                // Reset the wrap width to the cell width
                                $wrapWidthRemaining = $this->wrapWidthCell;
                            }
                        }
                    } else {
                        $newtext .= $line;
                    }
                    // Check the Line Feed counter
                    if ($lfct > 1) {
                        // Add a new line as long as it’s not the last line
                        $newtext .= "\n";
                        // Reset the line width
                        $lw = 0;
                        // Reset the wrap width to the cell width
                        $wrapWidthRemaining = $this->wrapWidthCell;
                    }
                    $lfct--;
                }
                $this->text = $newtext;
                $lfct       = substr_count($this->text, "\n");

                return [
                    $lw,
                    1,
                    $lfct,
                ];
            }
        }
        $l    = 0;
        $lfct = substr_count($this->text, "\n");
        if ($lfct > 0) {
            $l = 2;
        }

        return [
            $lw,
            $l,
            $lfct,
        ];
    }
}
