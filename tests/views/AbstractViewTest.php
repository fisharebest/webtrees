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

namespace Fisharebest\Webtrees;

use DOMDocument;

use function str_starts_with;

use const LIBXML_PEDANTIC;

/**
 * Common functions for testing views
 */
abstract class AbstractViewTest extends TestCase
{
    protected const EVIL_VALUE = '<script>evil()</script>';

    /**
     * Check the view runs without error and generates valid HTML
     *
     * @param string $view
     * @param array<array<string,array<string,mixed>>>  $data
     */
    protected function doTestView(string $view, array $data): void
    {
        foreach ($this->cartesian($data) as $datum) {
            $html = view($view, $datum);

            $this->validateHTML($html);
        }
    }

    /**
     * @param array<string,array<string,mixed>> $input
     *
     * @return array<array<string,array<string,mixed>>>
     */
    private function cartesian(array $input): array
    {
        $result = [[]];

        foreach ($input as $key => $values) {
            $append = [];

            foreach ($result as $product) {
                foreach ($values as $item) {
                    $product[$key] = $item;
                    $append[]      = $product;
                }
            }

            $result = $append;
        }

        return $result;
    }

    /**
     * @param string $html
     */
    protected function validateHTML(string $html): void
    {
        if (str_starts_with($html, '<!DOCTYPE html>')) {
            $xml = $html;
        } else {
            $xml = '<!DOCTYPE html><html lang="en"><body>' . $html . '</body></html>';
        }

        $doc = new DOMDocument();
        $doc->validateOnParse = true;

        self::assertTrue($doc->loadXML($xml, LIBXML_PEDANTIC), $html);
    }
}
