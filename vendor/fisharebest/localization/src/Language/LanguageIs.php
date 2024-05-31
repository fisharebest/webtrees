<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule15;
use Fisharebest\Localization\Territory\TerritoryIs;

/**
 * Class LanguageIs - Representation of the Icelandic language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageIs extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'is';
    }

    public function defaultTerritory()
    {
        return new TerritoryIs();
    }

    public function pluralRule()
    {
        return new PluralRule15();
    }
}
