<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryNg;

/**
 * Class LanguageKaj - Representation of the Kaje language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageKaj extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'kaj';
    }

    public function defaultTerritory()
    {
        return new TerritoryNg();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
