<?php

namespace Fisharebest\Localization\Script;

/**
 * Class ScriptCpmn - Representation of the Gunjala Gondi script.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2019 Greg Roach
 * @license   GPLv3+
 */
class ScriptGong extends AbstractScript implements ScriptInterface
{
    public function code()
    {
        return 'Gong';
    }

    public function number()
    {
        return '312';
    }

    public function unicodeName()
    {
        return 'Gunjala_Gondi';
    }
}
