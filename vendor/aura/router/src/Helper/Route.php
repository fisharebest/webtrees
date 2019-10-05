<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/mit-license.php MIT
 *
 */
namespace Aura\Router\Helper;

use Aura\Router\Exception\RouteNotFound;
use Aura\Router\Generator;

/**
 *
 * Generic Route Helper class
 *
 * @package Aura.Router
 *
 */
class Route
{
    /**
     *
     * The Generator object used by the RouteContainer
     *
     * @var Generator
     *
     */
    protected $generator;

    /**
     *
     * Constructor.
     *
     * @param Generator $generator The generator object to use
     *
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     *
     * Returns the generated raw route
     *
     * @param string $name The name of the route to lookup.
     *
     * @param array $data The data to pass into the route.
     *
     * @return string The results of calling _Generator::generate_.
     *
     * @throws RouteNotFound When the route cannot be found.
     *
     */
    public function __invoke($name, array $data = [])
    {
        return $this->generator->generate($name, $data);
    }
}