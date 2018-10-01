<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Report;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;

/**
 * Class ReportParserSetup - parse a report.xml file and extract the setup options.
 */
class ReportParserSetup extends ReportParserBase
{
    /** @var array An array of report options/parameters */
    private $data = [];

    /** @var string[] An array of input attributes */
    private $input;

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
     * Process <Report>
     *
     * @param string[] $attrs
     *
     * @return void
     */
    protected function reportStartHandler($attrs)
    {
        $access = Auth::PRIV_PRIVATE;
        if (isset($attrs['access'])) {
            if (isset($$attrs['access'])) {
                $access = $$attrs['access'];
            }
        }
        $this->data['access'] = $access;

        if (isset($attrs['icon'])) {
            $this->data['icon'] = $attrs['icon'];
        } else {
            $this->data['icon'] = '';
        }
    }

    /**
     * Process <var var="">
     *
     * @param string[] $attrs
     *
     * @return void
     */
    protected function varStartHandler($attrs)
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
     * Process <Title>
     *
     * @return void
     */
    protected function titleStartHandler()
    {
        $this->text = '';
    }

    /**
     * Process </Title>
     *
     * @return void
     */
    protected function titleEndHandler()
    {
        $this->data['title'] = $this->text;
        $this->text          = '';
    }

    /**
     * Process </Description>
     *
     * @return void
     */
    protected function descriptionEndHandler()
    {
        $this->data['description'] = $this->text;
        $this->text                = '';
    }

    /**
     * Process <Input>
     *
     * @param string[] $attrs
     *
     * @return void
     */
    protected function inputStartHandler($attrs)
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
                $this->input['default'] = date('d M Y');
            } else {
                $match = [];
                if (preg_match('/NOW\s*([+\-])\s*(\d+)/', $attrs['default'], $match) > 0) {
                    $plus = 1;
                    if ($match[1] === '-') {
                        $plus = -1;
                    }
                    $this->input['default'] = date('d M Y', WT_TIMESTAMP + $plus * 60 * 60 * 24 * $match[2]);
                } else {
                    $this->input['default'] = $attrs['default'];
                }
            }
        }
    }

    /**
     * Process </Input>
     *
     * @return void
     */
    protected function inputEndHandler()
    {
        $this->input['value'] = $this->text;
        if (!isset($this->data['inputs'])) {
            $this->data['inputs'] = [];
        }
        $this->data['inputs'][] = $this->input;
        $this->text             = '';
    }
}
