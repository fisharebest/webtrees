<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryRw;

/**
 * Class LanguageRw - Representation of the Kinyarwanda language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageRw extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'rw';
    }

    public function defaultTerritory()
    {
        return new TerritoryRw();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
