<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptCyrl;
use Fisharebest\Localization\Territory\TerritoryRu;

/**
 * Class LanguageCu - Representation of the Old Church Slavonic language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageCu extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'cu';
    }

    public function defaultScript()
    {
        return new ScriptCyrl();
    }

    public function defaultTerritory()
    {
        return new TerritoryRu();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
