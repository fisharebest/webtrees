<?php

namespace Fisharebest\Tests\Algorithm;

use Fisharebest\Algorithm\Dijkstra;

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
class DijkstraTest extends BaseTestCase
{
    /**
     * An undirected graph, with non-negative edge values.
     * No two shortest paths exist with the same length.
     *
     *     D---9---E
     *    / \       \
     *  14   2       6
     *  /     \       \
     * A---9---B--11--C
     *  \     /      /
     *   7  10      /
     *    \ /      /
     *     F-----15
     *
     *
     * @var integer[][] Test graph
     *
     * @covers \Fisharebest\Algorithm\Dijkstra
     */
    private $graph1 = array(
        'A' => array('B' => 9, 'D' => 14, 'F' => 7),
        'B' => array('A' => 9, 'C' => 11, 'D' => 2, 'F' => 10),
        'C' => array('B' => 11, 'E' => 6, 'F' => 15),
        'D' => array('A' => 14, 'B' => 2, 'E' => 9),
        'E' => array('C' => 6, 'D' => 9),
        'F' => array('A' => 7, 'B' => 10, 'C' => 15),
    );

    /**
     * Test that there are no paths to/from 'G'.
     *
     * @return void
     *
     * @covers \Fisharebest\Algorithm\Dijkstra
     */
    public function testNoPath()
    {
        $dijkstra = new Dijkstra($this->graph1);

        $this->assertSame(array(), $dijkstra->shortestPaths('A', 'G'));
        $this->assertSame(array(), $dijkstra->shortestPaths('G', 'A'));
    }

    /**
     * Test that there is a null paths to/from the same node.
     *
     * @return void
     *
     * @covers \Fisharebest\Algorithm\Dijkstra
     */
    public function testNullPath()
    {
        $dijkstra = new Dijkstra($this->graph1);

        $this->assertSame(array(array('A')), $dijkstra->shortestPaths('A', 'A'));
    }

    /**
     * Test there is a unique shortest path from 'A' to every other node.
     *
     * @return void
     *
     * @covers \Fisharebest\Algorithm\Dijkstra
     */
    public function testUniqueShortestPath()
    {
        $dijkstra = new Dijkstra($this->graph1);

        $this->assertSame(array(array('A', 'B')), $dijkstra->shortestPaths('A', 'B'));
        $this->assertSame(array(array('B', 'A')), $dijkstra->shortestPaths('B', 'A'));

        $this->assertSame(array(array('A', 'B', 'C')), $dijkstra->shortestPaths('A', 'C'));
        $this->assertSame(array(array('C', 'B', 'A')), $dijkstra->shortestPaths('C', 'A'));

        $this->assertSame(array(array('A', 'B', 'D')), $dijkstra->shortestPaths('A', 'D'));
        $this->assertSame(array(array('D', 'B', 'A')), $dijkstra->shortestPaths('D', 'A'));

        $this->assertSame(array(array('A', 'B', 'D', 'E')), $dijkstra->shortestPaths('A', 'E'));
        $this->assertSame(array(array('E', 'D', 'B', 'A')), $dijkstra->shortestPaths('E', 'A'));

        $this->assertSame(array(array('A', 'F')), $dijkstra->shortestPaths('A', 'F'));
        $this->assertSame(array(array('F', 'A')), $dijkstra->shortestPaths('F', 'A'));
    }

    /**
     * Test the multiple shortest paths between 'E' and 'F'.
     *
     * @return void
     *
     * @covers \Fisharebest\Algorithm\Dijkstra
     */
    public function testMultipleShortestPaths()
    {
        $dijkstra = new Dijkstra($this->graph1);

        $this->assertSame(array(array('E', 'C', 'F'), array('E', 'D', 'B', 'F')), $dijkstra->shortestPaths('E', 'F'));
        $this->assertSame(array(array('F', 'C', 'E'), array('F', 'B', 'D', 'E')), $dijkstra->shortestPaths('F', 'E'));
    }

    /**
     * Test the exclusion list, for next-shortest paths.
     *
     * @return void
     *
     * @covers \Fisharebest\Algorithm\Dijkstra
     */
    public function testExclusionList()
    {
        $dijkstra = new Dijkstra($this->graph1);

        $this->assertSame(array(array('E', 'D', 'B', 'F')), $dijkstra->shortestPaths('E', 'F', array('C')));
        $this->assertSame(array(array('F', 'B', 'D', 'E')), $dijkstra->shortestPaths('F', 'E', array('C')));
    }
}
