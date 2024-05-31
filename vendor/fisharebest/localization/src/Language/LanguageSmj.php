<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleOneTwoOther;
use Fisharebest\Localization\Territory\TerritorySe;

/**
 * Class LanguageSmj - Representation of the Lule Sami language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSmj extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'smj';
    }

    public function defaultTerritory()
    {
        return new TerritorySe();
    }

    public function pluralRule()
    {
        return new PluralRuleOneTwoOther();
    }
}
