<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\EventListener\AddRequestFormatsListener;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Test AddRequestFormatsListener class.
 *
 * @author Gildas Quemener <gildas.quemener@gmail.com>
 */
class AddRequestFormatsListenerTest extends TestCase
{
    /**
     * @var AddRequestFormatsListener
     */
    private $listener;

    protected function setUp()
    {
        $this->listener = new AddRequestFormatsListener(['csv' => ['text/csv', 'text/plain']]);
    }

    protected function tearDown()
    {
        $this->listener = null;
    }

    public function testIsAnEventSubscriber()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\EventSubscriberInterface', $this->listener);
    }

    public function testRegisteredEvent()
    {
        $this->assertEquals(
            [KernelEvents::REQUEST => ['onKernelRequest', 100]],
            AddRequestFormatsListener::getSubscribedEvents()
        );
    }

    public function testSetAdditionalFormats()
    {
        $request = $this->getRequestMock();
        $event = $this->getRequestEventMock($request);

        $request->expects($this->once())
            ->method('setFormat')
            ->with('csv', ['text/csv', 'text/plain']);

        $this->listener->onKernelRequest($event);
    }

    protected function getRequestMock()
    {
        return $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
    }

    protected function getRequestEventMock(Request $request)
    {
        $event = $this
            ->getMockBuilder(RequestEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->any())
            ->method('getRequest')
            ->willReturn($request);

        return $event;
    }
}
