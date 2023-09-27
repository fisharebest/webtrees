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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\Contracts\ElementInterface;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;

use function array_key_exists;
use function array_map;
use function e;
use function is_numeric;
use function nl2br;
use function str_contains;
use function str_starts_with;
use function strip_tags;
use function trim;
use function view;

/**
 * A GEDCOM element is a tag/primitive in a GEDCOM file.
 */
abstract class AbstractElement implements ElementInterface
{
    // HTML attributes for an <input>
    protected const MAXIMUM_LENGTH = false;
    protected const PATTERN        = false;

    private const WHITESPACE_LINE = [
        "\t"       => ' ',
        "\n"       => ' ',
        "\r"       => ' ',
        "\v"       => ' ', // Vertical tab
        "\u{85}"   => ' ', // NEL - newline
        "\u{2028}" => ' ', // LS - line separator
        "\u{2029}" => ' ', // PS - paragraph separator
    ];

    private const WHITESPACE_TEXT = [
        "\t"       => ' ',
        "\r\n"     => "\n",
        "\r"       => "\n",
        "\v"       => "\n",
        "\u{85}"   => "\n",
        "\u{2028}" => "\n",
        "\u{2029}" => "\n\n",
    ];

    // Which child elements can appear under this element.
    protected const SUBTAGS = [];

    // A label to describe this element
    private string $label;

    /** @var array<string,string> Subtags of this element */
    private array $subtags;

    /**
     * @param string             $label
     * @param array<string>|null $subtags
     */
    public function __construct(string $label, array $subtags = null)
    {
        $this->label   = $label;
        $this->subtags = $subtags ?? static::SUBTAGS;
    }

    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        $value = strtr($value, self::WHITESPACE_LINE);

        while (str_contains($value, '  ')) {
            $value = strtr($value, ['  ' => ' ']);
        }

        return trim($value);
    }

    /**
     * Convert a multi-line value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    protected function canonicalText(string $value): string
    {
        $value = strtr($value, self::WHITESPACE_TEXT);

        return trim($value, "\n");
    }

    /**
     * Should we collapse the children of this element when editing?
     *
     * @return bool
     */
    public function collapseChildren(): bool
    {
        return false;
    }

    /**
     * Create a default value for this element.
     *
     * @param Tree $tree
     *
     * @return string
     */
    public function default(Tree $tree): string
    {
        return '';
    }

    /**
     * An edit control for this data.
     *
     * @param string $id
     * @param string $name
     * @param string $value
     * @param Tree   $tree
     *
     * @return string
     */
    public function edit(string $id, string $name, string $value, Tree $tree): string
    {
        $values = $this->values();

        if ($values !== []) {
            $value = $this->canonical($value);

            // Ensure the current data is in the list.
            if (!array_key_exists($value, $values)) {
                $values = [$value => $value] + $values;
            }

            // We may use markup to display values, but not when editing them.
            $values = array_map(static fn (string $x): string => strip_tags($x), $values);

            return view('components/select', [
                'id'       => $id,
                'name'     => $name,
                'options'  => $values,
                'selected' => $value,
            ]);
        }

        $attributes = [
            'class'     => 'form-control',
            'dir'       => 'auto',
            'type'      => 'text',
            'id'        => $id,
            'name'      => $name,
            'value'     => $value,
            'maxlength' => static::MAXIMUM_LENGTH,
            'pattern'   => static::PATTERN,
        ];

        return '<input ' . Html::attributes($attributes) . ' />';
    }

    /**
     * An edit control for this data.
     *
     * @param string $id
     * @param string $name
     * @param string $value
     *
     * @return string
     */
    public function editHidden(string $id, string $name, string $value): string
    {
        return '<input class="form-control" type="hidden" id="' . e($id) . '" name="' . e($name) . '" value="' . e($value) . '" />';
    }

    /**
     * An edit control for this data.
     *
     * @param string $id
     * @param string $name
     * @param string $value
     *
     * @return string
     */
    public function editTextArea(string $id, string $name, string $value): string
    {
        return '<textarea class="form-control" id="' . e($id) . '" name="' . e($name) . '" rows="3" dir="auto">' . e($value) . '</textarea>';
    }

    /**
     * Escape @ signs in a GEDCOM export.
     *
     * @param string $value
     *
     * @return string
     */
    public function escape(string $value): string
    {
        return strtr($value, ['@' => '@@']);
    }

    /**
     * Create a label for this element.
     *
     * @return string
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * Create a label/value pair for this element.
     *
     * @param string $value
     * @param Tree   $tree
     *
     * @return string
     */
    public function labelValue(string $value, Tree $tree): string
    {
        $label = '<span class="label">' . $this->label() . '</span>';
        $value = '<span class="value align-top">' . $this->value($value, $tree) . '</span>';
        $html  = I18N::translate(/* I18N: e.g. "Occupation: farmer" */ '%1$s: %2$s', $label, $value);

        return '<div>' . $html . '</div>';
    }

    /**
     * Set, remove or replace a subtag.
     *
     * @param string $subtag
     * @param string $repeat
     * @param string $before
     *
     * @return void
     */
    public function subtag(string $subtag, string $repeat, string $before = ''): void
    {
        if ($before === '' || ($this->subtags[$before] ?? null) === null) {
            $this->subtags[$subtag] = $repeat;
        } else {
            $tmp = [];

            foreach ($this->subtags as $key => $value) {
                if ($key === $before) {
                    $tmp[$subtag] = $repeat;
                }
                $tmp[$key] = $value;
            }

            $this->subtags = $tmp;
        }
    }

    /**
     * @return array<string,string>
     */
    public function subtags(): array
    {
        return $this->subtags;
    }

    /**
     * Display the value of this type of element.
     *
     * @param string $value
     * @param Tree   $tree
     *
     * @return string
     */
    public function value(string $value, Tree $tree): string
    {
        $values = $this->values();

        if ($values === []) {
            if (str_contains($value, "\n")) {
                return '<span class="ut d-inline-block">' . nl2br(e($value, false)) . '</span>';
            }

            return '<span class="ut">' . e($value) . '</span>';
        }

        $canonical = $this->canonical($value);

        return $values[$canonical] ?? '<bdi>' . e($value) . '</bdi>';
    }

    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        return [];
    }

    /**
     * Display the value of this type of element - convert URLs to links.
     *
     * @param string $value
     *
     * @return string
     */
    protected function valueAutoLink(string $value): string
    {
        $canonical = $this->canonical($value);

        if (str_contains($canonical, 'http://') || str_contains($canonical, 'https://')) {
            $html = Registry::markdownFactory()->autolink($canonical);
            $html = strip_tags($html, ['a', 'br']);
        } else {
            $html = nl2br(e($canonical), false);
        }

        if (str_contains($html, '<br>')) {
            return '<span class="ut d-inline-block">' . $html . '</span>';
        }

        return '<span class="ut">' . $html . '</span>';
    }

    /**
     * Display the value of this type of element - multi-line text with/without markdown.
     *
     * @param string $value
     * @param Tree   $tree
     *
     * @return string
     */
    protected function valueFormatted(string $value, Tree $tree): string
    {
        $canonical = $this->canonical($value);

        $format = $tree->getPreference('FORMAT_TEXT');

        switch ($format) {
            case 'markdown':
                return Registry::markdownFactory()->markdown($canonical, $tree);

            default:
                return Registry::markdownFactory()->autolink($canonical, $tree);
        }
    }

    /**
     * Display the value of this type of element - convert to URL.
     *
     * @param string $value
     *
     * @return string
     */
    protected function valueLink(string $value): string
    {
        $canonical = $this->canonical($value);

        if (str_starts_with($canonical, 'https://') || str_starts_with($canonical, 'http://')) {
            return '<a dir="auto" href="' . e($canonical) . '">' . e($value) . '</a>';
        }

        return e($value);
    }

    /**
     * Display the value of this type of element.
     *
     * @param string $value
     *
     * @return string
     */
    public function valueNumeric(string $value): string
    {
        $canonical = $this->canonical($value);

        if (is_numeric($canonical)) {
            return I18N::number((int) $canonical);
        }

        return e($value);
    }
}
