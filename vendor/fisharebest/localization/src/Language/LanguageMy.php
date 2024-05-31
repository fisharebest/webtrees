<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptMymr;
use Fisharebest\Localization\Territory\TerritoryMm;

/**
 * Class LanguageMy - Representation of the Burmese language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageMy extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'my';
    }

    public function defaultScript()
    {
        return new ScriptMymr();
    }

    public function defaultTerritory()
    {
        return new TerritoryMm();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
