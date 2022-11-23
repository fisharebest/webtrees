<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule2;
use Fisharebest\Localization\Script\ScriptKnda;
use Fisharebest\Localization\Territory\TerritoryIn;

/**
 * Class LanguageKn - Representation of the Kannada language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageKn extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'kn';
    }

    public function defaultScript()
    {
        return new ScriptKnda();
    }

    public function defaultTerritory()
    {
        return new TerritoryIn();
    }

    public function pluralRule()
    {
        return new PluralRule2();
    }
}
