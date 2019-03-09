<?php

namespace Fisharebest\Localization\Territory;

/**
 * Class AbstractTerritory - Representation of a geographic area.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
abstract class AbstractTerritory
{
    /**
     * @return int
     */
    public function firstDay()
    {
        return 1;
    }

    /**
     * @return string
     */
    public function measurementSystem()
    {
        return 'metric';
    }

    /**
     * @return string
     */
    public function paperSize()
    {
        return 'A4';
    }

    /**
     * @return int
     */
    public function weekendStart()
    {
        return 6;
    }

    /**
     * @return int
     */
    public function weekendEnd()
    {
        return 0;
    }
}
