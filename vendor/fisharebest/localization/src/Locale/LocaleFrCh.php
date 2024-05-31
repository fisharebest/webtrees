<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCh;

/**
 * Class LocaleFrCh - Swiss French
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocaleFrCh extends LocaleFr
{
    public function endonym()
    {
        return 'français suisse';
    }

    public function endonymSortable()
    {
        return 'FRANCAIS SUISSE';
    }

    protected function percentFormat()
    {
        return self::PLACEHOLDER . self::PERCENT;
    }

    public function territory()
    {
        return new TerritoryCh();
    }
}
