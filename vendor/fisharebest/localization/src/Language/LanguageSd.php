<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Script\ScriptArab;
use Fisharebest\Localization\Territory\TerritoryPk;

/**
 * Class LanguageSd - Representation of the Sindhi language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class LanguageSd extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'sd';
    }

    public function defaultScript()
    {
        return new ScriptArab();
    }

    public function defaultTerritory()
    {
        return new TerritoryPk();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
