<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleFilipino;
use Fisharebest\Localization\Territory\TerritoryPh;

/**
 * Class LanguageFil - Representation of the Filipino language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageFil extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'fil';
    }

    public function defaultTerritory()
    {
        return new TerritoryPh();
    }

    public function pluralRule()
    {
        return new PluralRuleFilipino();
    }
}
