<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritoryPg;

/**
 * Class LanguageTpi - Representation of the Tok Pisin language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageTpi extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'tpi';
    }

    public function defaultTerritory()
    {
        return new TerritoryPg();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
