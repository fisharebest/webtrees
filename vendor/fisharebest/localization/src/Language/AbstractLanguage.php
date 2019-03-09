<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRuleUnknown;
use Fisharebest\Localization\Script\ScriptLatn;
use Fisharebest\Localization\Territory\Territory001;

/**
 * Class AbstractLanguage - Representation of a language.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
abstract class AbstractLanguage
{
    public function defaultTerritory()
    {
        return new Territory001();
    }

    public function defaultScript()
    {
        return new ScriptLatn();
    }

    public function pluralRule()
    {
        return new PluralRuleUnknown();
    }
}
