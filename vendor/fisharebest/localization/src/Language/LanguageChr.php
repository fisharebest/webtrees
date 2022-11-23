<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryUs;

/**
 * Class LanguageChr - Representation of the Cherokee language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageChr extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'chr';
    }

    public function defaultTerritory()
    {
        return new TerritoryUs();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
