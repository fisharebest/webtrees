<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\Territory\TerritoryCh;

/**
 * Class LocaleFrCh - Swiss French
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
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
