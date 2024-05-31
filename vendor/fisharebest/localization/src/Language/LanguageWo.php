<?php

namespace Fisharebest\Localization\Language;

use Fisharebest\Localization\PluralRule\PluralRule0;
use Fisharebest\Localization\Territory\TerritorySn;

/**
 * Class LanguageWo - Representation of the Wolof language.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class LanguageWo extends AbstractLanguage implements LanguageInterface
{
    public function code()
    {
        return 'wo';
    }

    public function defaultTerritory()
    {
        return new TerritorySn();
    }

    public function pluralRule()
    {
        return new PluralRule0();
    }
}
