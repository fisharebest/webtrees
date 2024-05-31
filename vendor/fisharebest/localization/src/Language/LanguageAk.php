<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Territory\TerritoryGh;

/**
 * Class LanguageAk - Representation of the Akan language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageAk extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'ak';
    }

    public function defaultTerritory()
    {
        return new TerritoryGh();
    }

    public function pluralRule()
    {
        return new PluralRule2();
    }
}
