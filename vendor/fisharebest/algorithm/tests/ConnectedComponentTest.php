<?php

namespace Fisharebest\Tests\Algorithm;

use Fisharebest\Algorithm\ConnectedComponent;

/**
 * @author    Greg Roach <greg@subaqua.co.uk>
 * @copyright (c) 2021 Greg Roach <greg@subaqua.co.uk>
 * @license   GPL-3.0+
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses>.
 */
class ConnectedComponentTest extends BaseTestCase
{
    /**
     * A graph with no components.
     *
     * @covers \Fisharebest\Algorithm\ConnectedComponent
     */
    public function testNoComponents()
    {
        $graph = array();

        $components = array();

        $algorithm = new ConnectedComponent($graph);

        $this->assertSame($components, $algorithm->findConnectedComponents());
    }

    /**
     * A graph with one component.
     *
     *    D----E
     *   / \    \
     *  /   \    \
     * A-----B---C
     *  \   /    /
     *   \ /    /
     *    F----/
     *
     * @covers \Fisharebest\Algorithm\ConnectedComponent
     */
    public function testOneComponent()
    {
        $graph = array(
            'A' => array('B' => 1, 'D' => 1, 'F' => 1),
            'B' => array('A' => 1, 'C' => 1, 'D' => 1, 'F' => 1),
            'C' => array('B' => 1, 'E' => 1, 'F' => 1),
            'D' => array('A' => 1, 'B' => 1, 'E' => 1),
            'E' => array('C' => 1, 'D' => 1),
            'F' => array('A' => 1, 'B' => 1, 'C' => 1),
        );

        $components = array(
            1 => array('A', 'B', 'C', 'D', 'E', 'F'),
        );

        $algorithm = new ConnectedComponent($graph);

        $this->assertSame($components, $algorithm->findConnectedComponents());
    }

    /**
     * A graph with two component.
     *
     *    D    E
     *   / \    \
     *  /   \    \
     * A-----B   C
     *  \   /
     *   \ /
     *    F
     *
     * @covers \Fisharebest\Algorithm\ConnectedComponent
     */
    public function testTwoComponent()
    {
        $graph = array(
            'A' => array('B' => 1, 'D' => 1, 'F' => 1),
            'B' => array('A' => 1, 'D' => 1, 'F' => 1),
            'C' => array('E' => 1),
            'D' => array('A' => 1, 'B' => 1),
            'E' => array('C' => 1),
            'F' => array('A' => 1, 'B' => 1),
        );

        $components = array(
            1 => array('A', 'B', 'D', 'F'),
            2 => array('C', 'E'),
        );

        $algorithm = new ConnectedComponent($graph);

        $this->assertSame($components, $algorithm->findConnectedComponents());
    }

    /**
     * A graph with two component.
     *
     * A   B
     *
     * @covers \Fisharebest\Algorithm\ConnectedComponent
     */
    public function testUnconnected()
    {
        $graph = array(
            'A' => array(),
            'B' => array(),
        );

        $components = array(
            1 => array('A'),
            2 => array('B'),
        );

        $algorithm = new ConnectedComponent($graph);

        $this->assertSame($components, $algorithm->findConnectedComponents());
    }
}
