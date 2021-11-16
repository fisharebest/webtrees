<?php

namespace Iodev\Whois\Modules\Asn;

trait AsnInfoDeprecated
{
    /**
     * @deprecated will be removed in v4.2
     * @return string
     */
    public function getAsn()
    {
        return $this->asn;
    }

    /**
     * @deprecated will be removed in v4.2
     * @return AsnRouteInfo[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
