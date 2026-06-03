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

use function count;
use function ksort;
use function str_replace;
use function trim;

/**
 * @template TRenderer of AbstractRenderer
 *
 * @extends AbstractElement<TRenderer>
 */
abstract class AbstractTextBox extends AbstractElement implements ElementContainerInterface
{
    /** @var array<AbstractElement<TRenderer>> */
    protected array $elements = [];

    public function __construct(
        protected float $width,
        protected float $height,
        protected bool $border,
        protected string $bgcolor,
        protected bool $newline, // Does following text start on new line
        protected float $left,
        protected float $top,
        protected bool $pagecheck,
        protected string $style, // D or empty string: Draw (default), F: Fill, DF/FD: Draw and fill, CEO: Clip odd/even, CNZ: Clip non-zero winding
        protected bool $fill,
        protected bool $padding,
        protected bool $reseth, // Resets this box last height after it’s done
    ) {
    }

    /**
     * @param AbstractElement<TRenderer> $element
     */
    public function addElement(AbstractElement $element): void
    {
        $this->elements[] = $element;
    }

    /**
     * Merge consecutive text elements that share the same style, sort
     * footnotes by number, and discard empty non-text elements.
     *
     * @param TRenderer $renderer
     */
    protected function collapseElements(AbstractRenderer $renderer): void
    {
        $newelements      = [];
        $lastelement      = null;
        $footnote_element = [];
        $cE               = count($this->elements);

        for ($i = 0; $i < $cE; $i++) {
            $element = $this->elements[$i];
            if ($element instanceof AbstractElement) {
                if ($element instanceof AbstractText) {
                    if ($footnote_element !== []) {
                        ksort($footnote_element);
                        foreach ($footnote_element as $links) {
                            $newelements[] = $links;
                        }
                        $footnote_element = [];
                    }
                    if ($lastelement === null) {
                        $lastelement = $element;
                    } elseif ($element->getStyle() === $lastelement->getStyle()) {
                        // Merge text with the same style
                        $lastelement->addText(str_replace("\n", '<br>', $element->getValue()));
                    } else {
                        $newelements[] = $lastelement;
                        $lastelement   = $element;
                    }
                } elseif ($element instanceof AbstractFootnote) {
                    // Check if the Footnote has been set with its link number
                    $renderer->checkFootnote($element);
                    // Save the last element if any
                    if ($lastelement !== null) {
                        $newelements[] = $lastelement;
                        $lastelement   = null;
                    }
                    // Save the Footnote with its link number as key for sorting later
                    $footnote_element[$element->num] = $element;
                } elseif (trim($element->getValue()) !== '') {
                    // Do not keep empty elements
                    if ($footnote_element !== []) {
                        ksort($footnote_element);
                        foreach ($footnote_element as $links) {
                            $newelements[] = $links;
                        }
                        $footnote_element = [];
                    }
                    if ($lastelement !== null) {
                        $newelements[] = $lastelement;
                        $lastelement   = null;
                    }
                    $newelements[] = $element;
                }
            } else {
                if ($lastelement !== null) {
                    $newelements[] = $lastelement;
                    $lastelement   = null;
                }
                if ($footnote_element !== []) {
                    ksort($footnote_element);
                    foreach ($footnote_element as $links) {
                        $newelements[] = $links;
                    }
                    $footnote_element = [];
                }
                $newelements[] = $element;
            }
        }
        if ($lastelement !== null) {
            $newelements[] = $lastelement;
        }
        if ($footnote_element !== []) {
            ksort($footnote_element);
            foreach ($footnote_element as $links) {
                $newelements[] = $links;
            }
        }
        $this->elements = $newelements;
    }

    /**
     * Iterate elements to calculate line count, element height, and footnote height.
     *
     * @param TRenderer $renderer
     * @param float     $wrap_width Available width for text wrapping
     *
     * @return array{line_count: int, element_height: float, footnote_height: float}
     */
    protected function calculateElementDimensions(AbstractRenderer $renderer, float $wrap_width): array
    {
        $line_count      = 0;
        $element_height  = 0.0;
        $footnote_height = 0.0;
        $w               = 0;

        foreach ($this->elements as $element) {
            $ew = $element->setWrapWidth($wrap_width - $w, $wrap_width);
            if ($ew === $wrap_width) {
                $w = 0;
            }
            $lw = $element->getWidth($renderer);
            // Accumulate line feed count
            $line_count += $lw[2];
            if ($lw[1] === 1) {
                $w = $lw[0];
            } elseif ($lw[1] === 2) {
                $w = 0;
            } else {
                $w += $lw[0];
            }
            if ($w > $wrap_width) {
                $w = $lw[0];
            }
            // For non-text elements (images), accumulate height
            $element_height += $element->getHeight($renderer);
        }

        return [
            'line_count'      => $line_count,
            'element_height'  => $element_height,
            'footnote_height' => $footnote_height,
        ];
    }
}
