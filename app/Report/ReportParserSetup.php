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

namespace Fisharebest\Webtrees\Report;

use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\I18N;

use function preg_match;
use function strtoupper;

/**
 * Class ReportParserSetup - parse a report.xml file and extract the setup options.
 */
class ReportParserSetup extends ReportParserBase
{
    /** @var array An array of report options/parameters */
    private array $data = [];

    /** @var array<string> An array of input attributes */
    private array $input;

    /**
     * Return the parsed data.
     *
     * @return array
     */
    public function reportProperties(): array
    {
        return $this->data;
    }

    /**
     * Handle <var var="" />
     *
     * @param string[] $attrs
     *
     * @return void
     */
    protected function varStartHandler(array $attrs): void
    {
        if (preg_match('/^I18N::number\((.+)\)$/', $attrs['var'], $match)) {
            $this->text .= I18N::number((int) $match[1]);
        } elseif (preg_match('/^I18N::translate\(\'(.+)\'\)$/', $attrs['var'], $match)) {
            $this->text .= I18N::translate($match[1]);
        } elseif (preg_match('/^I18N::translateContext\(\'(.+)\', *\'(.+)\'\)$/', $attrs['var'], $match)) {
            $this->text .= I18N::translateContext($match[1], $match[2]);
        } else {
            $this->text .= $attrs['var'];
        }
    }

    /**
     * Handle <title>
     *
     * @return void
     */
    protected function titleStartHandler(): void
    {
        $this->text = '';
    }

    /**
     * Handle </title>
     *
     * @return void
     */
    protected function titleEndHandler(): void
    {
        $this->data['title'] = $this->text;

        $this->text = '';
    }

    /**
     * Handle </description>
     *
     * @return void
     */
    protected function descriptionEndHandler(): void
    {
        $this->data['description'] = $this->text;

        $this->text = '';
    }

    /**
     * Handle <input>
     *
     * @param string[] $attrs
     *
     * @return void
     */
    protected function inputStartHandler(array $attrs): void
    {
        $this->text  = '';
        $this->input = [
            'name'    => $attrs['name'] ?? '',
            'type'    => $attrs['type'] ?? '',
            'lookup'  => $attrs['lookup'] ?? '',
            'options' => $attrs['options'] ?? '',
            'default' => '',
            'value'   => '',
        ];

        if (isset($attrs['default'])) {
            if ($attrs['default'] === 'NOW') {
                $date = Carbon::now();
                $this->input['default'] = strtoupper($date->format('d M Y'));
            } else {
                $match = [];
                if (preg_match('/NOW([+\-]\d+)/', $attrs['default'], $match) > 0) {
                    $date = Carbon::now()->addDays((int) $match[1]);
                    $this->input['default'] = strtoupper($date->format('d M Y'));
                } else {
                    $this->input['default'] = $attrs['default'];
                }
            }
        } elseif ($attrs['name'] === 'pageSize') {
            $this->input['default'] = I18N::locale()->territory()->paperSize();
        }
    }

    /**
     * Handle </input>
     *
     * @return void
     */
    protected function inputEndHandler(): void
    {
        $this->input['value'] = $this->text;
        if (!isset($this->data['inputs'])) {
            $this->data['inputs'] = [];
        }
        $this->data['inputs'][] = $this->input;
        $this->text             = '';
    }
}
