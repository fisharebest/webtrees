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

namespace Fisharebest\Webtrees\CommonMark;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;

/**
 * Convert webtrees 1.x census-assistant markup into tables.
 * Note that webtrees 2.0 generates markdown tables directly.
 *
 * .start_formatted_area.
 * .b.HEADING1|.b.HEADING2|.b.HEADING3
 * COL1|COL2|COL3
 * COL1|COL2|COL3
 * .end_formatted_area.
 */
class CensusTableExtension implements ConfigurableExtensionInterface
{
    // Keywords used to create the webtrees 1.x census-assistant notes.
    public const CA_PREFIX = '.start_formatted_area.';
    public const CA_SUFFIX = '.end_formatted_area.';
    public const TH_PREFIX = '.b.';

    /**
     * The core TableExtension will already have configured tables.
     *
     * @param ConfigurationBuilderInterface $builder
     */
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
    }

    /**
     * Assumes we have also registered the core TableExtension.
     *
     * @param EnvironmentBuilderInterface $environment
     */
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addBlockStartParser(new CensusTableStartParser());
    }
}
