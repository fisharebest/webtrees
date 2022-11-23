<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptHans;
use Fisharebest\Localization\Territory\TerritoryCn;

/**
 * Class LanguageYue - Representation of the Chinese language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageYue extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'yue';
    }

    public function defaultTerritory()
    {
        return new TerritoryCn();
    }

    public function defaultScript()
    {
        return new ScriptHans();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
