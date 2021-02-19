<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Fisharebest\Webtrees\Tree;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Block\Renderer\DocumentRenderer;
use League\CommonMark\Block\Renderer\ParagraphRenderer;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Parser\AutolinkParser;
use League\CommonMark\Inline\Renderer\LinkRenderer;
use League\CommonMark\Inline\Renderer\TextRenderer;

use function array_key_exists;
use function array_map;
use function e;
use function is_numeric;
use function preg_replace;
use function strpos;
use function trim;
use function view;

/**
 * A GEDCOM element is a tag/primitive in a GEDCOM file.
 */
abstract class AbstractElement implements ElementInterface
{
    protected const REGEX_URL = '~((https?|ftp]):)(//([^\s/?#<>]*))?([^\s?#<>]*)(\?([^\s#<>]*))?(#[^\s?#<>]+)?~';

    // HTML attributes for an <input>
    protected const MAXIMUM_LENGTH = false;
    protected const PATTERN        = false;

    // Which child elements can appear under this element.
    protected const SUBTAGS = [];

    /** @var string A label to describe this element */
    private $label;

    /** @var array<string,string> */
    private $subtags;

    /**
     * AbstractGedcomElement constructor.
     *
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
        $value = strtr($value, ["\t" => ' ', "\r" => ' ', "\n" => ' ']);

        while (strpos($value, '  ') !== false) {
            $value = strtr($value, ['  ' => ' ']);
        }

        return trim($value);
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
            $values = array_map('strip_tags', $values);

            return view('components/select', [
                'id'       => $id,
                'name'     => $name,
                'options'  => $values,
                'selected' => $value,
            ]);
        }

        $attributes = [
            'class'    => 'form-control',
            'type'     => 'text',
            'id'       => $id,
            'name'     => $name,
            'value'    => $value,
            'maxvalue' => static::MAXIMUM_LENGTH,
            'pattern'  => static::PATTERN,
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
        return '<textarea class="form-control" id="' . e($id) . '" name="' . e($name) . '" rows="5" dir="auto">' . e($value) . '</textarea>';
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
        $value = '<span class="value">' . $this->value($value, $tree) . '</span>';
        $html  = I18N::translate(/* I18N: e.g. "Occupation: farmer" */ '%1$s: %2$s', $label, $value);

        return '<div>' . $html . '</div>';
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
                return '<span dir="auto" class="d-inline-block" style="white-space: pre-wrap;">' . e($value) . '</span>';
            }

            return '<span dir="auto">' . e($value) . '</span>';
        }

        $canonical = $this->canonical($value);

        return $values[$canonical] ?? '<span dir="auto">' . e($value) . '</span>';
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
     * Display the value of this type of element - convert URLs to links
     *
     * @param string $value
     *
     * @return string
     */
    protected function valueAutoLink(string $value): string
    {
        // Convert URLs into markdown auto-links.
        $value = preg_replace(self::REGEX_URL, '<$0>', $value);

        // Create a minimal commonmark processor - just add support for autolinks.
        $environment = new Environment();
        $environment
            ->addBlockRenderer(Document::class, new DocumentRenderer())
            ->addBlockRenderer(Paragraph::class, new ParagraphRenderer())
            ->addInlineRenderer(Text::class, new TextRenderer())
            ->addInlineRenderer(Link::class, new LinkRenderer())
            ->addInlineParser(new AutolinkParser());

        $converter = new CommonMarkConverter(['html_input' => Environment::HTML_INPUT_ESCAPE], $environment);

        return $converter->convertToHtml($value);
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
