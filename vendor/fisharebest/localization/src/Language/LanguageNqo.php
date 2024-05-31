<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Script\ScriptNkoo;
use Fisharebest\Localization\Territory\TerritoryGn;

/**
 * Class LanguageNqo - Representation of the N’Ko language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageNqo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'nqo';
    }

    public function defaultScript()
    {
        return new ScriptNkoo();
    }

    public function defaultTerritory()
    {
        return new TerritoryGn();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
