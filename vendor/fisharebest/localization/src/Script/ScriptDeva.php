<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptDeva - Representation of the Devanagari script.
 *
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2022 Greg Roach
 * @license   GPL-3.0-or-later
 */
class ScriptDeva extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Deva';
    }

    public function numerals()
    {
        return array('०', '१', '२', '३', '४', '५', '६', '७', '८', '९');
    }

    public function number()
    {
        return '315';
    }

    public function unicodeName()
    {
        return 'Devanagari';
    }
}
