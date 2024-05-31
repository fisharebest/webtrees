<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule5;
use Fisharebest\Localization\Territory\TerritoryMd;

/**
 * Class LanguageMo - Representation of the Moldovan language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageMo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'mo';
    }

    public function defaultTerritory()
    {
        return new TerritoryMd();
    }

    public function pluralRule()
    {
        return new PluralRule5();
    }
}
