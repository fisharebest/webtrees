<?php

namespace Fisharebest\Localization\Locale;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryBr;

/**
 * Class LocalePtBr - Brazilian Portuguese
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LocalePtBr extends LocalePt
{
    public function endonym()
    {
        return 'português do Brasil';
    }

    public function endonymSortable()
    {
        return 'PORTUGUES DO BRASIL';
    }

    public function pluralRule()
    {
        return new PluralRule2();
    }

    public function territory()
    {
        return new TerritoryBr();
    }
}
