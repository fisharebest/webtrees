<?php

namespace Iodev\Whois\Modules\Asn;

use InvalidArgumentException;

/**
 * Immutable data object
 */
class AsnInfo
{
    /**
     * @param AsnResponse $response
     * @param string $asn
     * @param AsnRouteInfo[] $routes
     */
    public function __construct(AsnResponse $response, $asn, $routes)
    {
        if (!is_array($routes)) {
            throw new InvalidArgumentException("Routes must be an array");
        }
        $this->response = $response;
        $this->asn = strval($asn);
        $this->routes = $routes;
    }

    /** @var AsnResponse */
    private $response;

    /** @var string */
    private $asn;

    /** @var AsnRouteInfo[] */
    private $routes;

    /**
     * @return AsnResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getAsn()
    {
        return $this->asn;
    }

    /**
     * @return AsnRouteInfo[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
