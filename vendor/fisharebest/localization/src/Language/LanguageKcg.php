<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryNg;

/**
 * Class LanguageKcg - Representation of the Katab language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageKcg extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'kcg';
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
