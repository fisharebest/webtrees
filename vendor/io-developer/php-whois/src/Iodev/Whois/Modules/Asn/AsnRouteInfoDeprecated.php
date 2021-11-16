<?php

namespace Iodev\Whois\Modules\Asn;

trait AsnRouteInfoDeprecated
{
    /**
     * @deprecated will be removed in v4.2
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @deprecated will be removed in v4.2
     * @return string
     */
    public function getRoute6()
    {
        return $this->route6;
    }

    /**
     * @deprecated will be removed in v4.2
     * @return string
     */
    public function getDescr()
    {
        return $this->descr;
    }

    /**
     * @deprecated will be removed in v4.2
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @deprecated will be removed in v4.2
     * @return string
     */
    public function getMntBy()
    {
        return $this->mntBy;
    }

    /**
     * @deprecated will be removed in v4.2
     * @return string
     */
    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * @deprecated will be removed in v4.2
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }
}
