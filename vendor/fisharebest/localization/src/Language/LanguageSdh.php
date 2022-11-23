<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule1;
use Fisharebest\Localization\Territory\TerritoryTr;

/**
 * Class LanguageKu - Representation of the Southern Kurdish language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageSdh extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'sdh';
    }

    public function defaultTerritory()
    {
        return new TerritoryTr();
    }

    public function pluralRule()
    {
        return new PluralRule1();
    }
}
