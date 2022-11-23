<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryNl;

/**
 * Class LanguageFy - Representation of the Western Frisian language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageFy extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'fy';
    }

    public function defaultTerritory()
    {
        return new TerritoryNl();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
