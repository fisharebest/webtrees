<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryTm;

/**
 * Class LanguageTk - Representation of the Turkmen language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageTk extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'tk';
    }

    public function defaultTerritory()
    {
        return new TerritoryTm();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
