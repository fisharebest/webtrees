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

use Closure;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Registry;

use function preg_match;
use function strtoupper;

/**
 * Parse a report.xml file and extract the setup options.
 */
final class ParserSetup extends AbstractParser
{
    private string $title;

    private string $description;

    /** @var array<InputDefinition> */
    private array $inputs = [];

    /** Definition for the <Input> currently being parsed.  Promoted to $inputs by inputEndHandler(). */
    private InputDefinition $input;

    public function reportTitle(): string
    {
        return $this->title;
    }

    public function reportDescription(): string
    {
        return $this->description;
    }

    /** @return array<InputDefinition> */
    public function reportInputs(): array
    {
        return $this->inputs;
    }

    /**
     * Dispatch table for opening XML tags.
     *
     * Every tag that may legally appear in a report must be present here,
     * even if it requires no action on open (use a no-op).
     *
     * @return array<string,Closure(array<string,string>):void>
     */
    protected function startHandlers(): array
    {
        return [
            'Body'             => $this->noop(...),
            'Cell'             => $this->noop(...),
            'Description'      => $this->noop(...),
            'Doc'              => $this->noop(...),
            'Facts'            => $this->noop(...),
            'Footer'           => $this->noop(...),
            'Footnote'         => $this->noop(...),
            'FootnoteTexts'    => $this->noop(...),
            'Gedcom'           => $this->noop(...),
            'GedcomValue'      => $this->noop(...),
            'GeneratedBy'      => $this->noop(...),
            'Generation'       => $this->noop(...),
            'GetPersonName'    => $this->noop(...),
            'Header'           => $this->noop(...),
            'HighlightedImage' => $this->noop(...),
            'Image'            => $this->noop(...),
            'Input'            => $this->inputStartHandler(...),
            'Line'             => $this->noop(...),
            'List'             => $this->noop(...),
            'ListTotal'        => $this->noop(...),
            'NewPage'          => $this->noop(...),
            'Now'              => $this->noop(...),
            'PageNum'          => $this->noop(...),
            'Relatives'        => $this->noop(...),
            'RepeatTag'        => $this->noop(...),
            'Report'           => $this->noop(...),
            'SetVar'           => $this->noop(...),
            'Style'            => $this->styleStartHandler(...),
            'Text'             => $this->noop(...),
            'TextBox'          => $this->noop(...),
            'Title'            => $this->titleStartHandler(...),
            'TotalPages'       => $this->noop(...),
            'WebtreesLogo'     => $this->noop(...),
            'br'               => $this->noop(...),
            'if'               => $this->noop(...),
            'tempdoc'          => $this->noop(...),
            'var'              => $this->varStartHandler(...),
        ];
    }

    /**
     * Dispatch table for closing XML tags.
     *
     * Every tag that may legally appear in a report must be present here,
     * even if it requires no action on open (use a no-op).
     *
     * @return array<string,Closure():void>
     */
    protected function endHandlers(): array
    {
        return [
            'Body'             => $this->noop(...),
            'Cell'             => $this->noop(...),
            'Description'      => $this->descriptionEndHandler(...),
            'Doc'              => $this->noop(...),
            'Facts'            => $this->noop(...),
            'Footer'           => $this->noop(...),
            'Footnote'         => $this->noop(...),
            'FootnoteTexts'    => $this->noop(...),
            'Gedcom'           => $this->noop(...),
            'GedcomValue'      => $this->noop(...),
            'GeneratedBy'      => $this->noop(...),
            'Generation'       => $this->noop(...),
            'GetPersonName'    => $this->noop(...),
            'Header'           => $this->noop(...),
            'HighlightedImage' => $this->noop(...),
            'Image'            => $this->noop(...),
            'Input'            => $this->inputEndHandler(...),
            'Line'             => $this->noop(...),
            'List'             => $this->noop(...),
            'ListTotal'        => $this->noop(...),
            'NewPage'          => $this->noop(...),
            'Now'              => $this->noop(...),
            'PageNum'          => $this->noop(...),
            'Relatives'        => $this->noop(...),
            'RepeatTag'        => $this->noop(...),
            'Report'           => $this->noop(...),
            'SetVar'           => $this->noop(...),
            'Style'            => $this->noop(...),
            'Text'             => $this->noop(...),
            'TextBox'          => $this->noop(...),
            'Title'            => $this->titleEndHandler(...),
            'TotalPages'       => $this->noop(...),
            'WebtreesLogo'     => $this->noop(...),
            'br'               => $this->noop(...),
            'if'               => $this->noop(...),
            'tempdoc'          => $this->noop(...),
            'var'              => $this->noop(...),
        ];
    }

    /**
     * @param array<string,string> $attrs
     */
    private function styleStartHandler(array $attrs): void
    {
        Style::fromXmlAttributes($attrs);
    }

    /**
     * @param array<string,string> $attrs
     */
    private function varStartHandler(array $attrs): void
    {
        $placeholder_expander = new PlaceholderExpander(new VariableTable([]));

        $this->text .= $placeholder_expander->applyI18nFunctions($attrs['var']);
    }

    private function titleStartHandler(): void
    {
        $this->text = '';
    }

    private function titleEndHandler(): void
    {
        $this->title = $this->text;
        $this->text  = '';
    }

    private function descriptionEndHandler(): void
    {
        $this->description = $this->text;
        $this->text        = '';
    }

    /**
     * @param array<string,string> $attrs
     */
    private function inputStartHandler(array $attrs): void
    {
        $this->text = '';

        $default = '';

        if (isset($attrs['default'])) {
            if ($attrs['default'] === 'NOW') {
                $date    = Registry::timestampFactory()->now();
                $default = strtoupper($date->format('d M Y'));
            } elseif (preg_match('/NOW([+\-]\d+)/', $attrs['default'], $match) > 0) {
                $date    = Registry::timestampFactory()->now()->addDays((int) $match[1]);
                $default = strtoupper($date->format('d M Y'));
            } else {
                $default = $attrs['default'];
            }
        } elseif (($attrs['name'] ?? '') === 'pageSize') {
            $default = I18N::locale()->territory()->paperSize();
        }

        $this->input = new InputDefinition(
            name:    $attrs['name'] ?? '',
            type:    $attrs['type'] ?? '',
            lookup:  $attrs['lookup'] ?? '',
            options: $attrs['options'] ?? '',
            default: $default,
        );
    }

    private function inputEndHandler(): void
    {
        $this->inputs[] = $this->input->withValue($this->text);
        $this->text     = '';
    }
}
