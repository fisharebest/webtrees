<?php

namespace League\Glide\Urls;

class UrlBuilderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $urlBuilder = UrlBuilderFactory::create('/img');

        $this->assertInstanceOf('League\Glide\Urls\UrlBuilder', $urlBuilder);
        $this->assertEquals('/img/image.jpg', $urlBuilder->getUrl('image.jpg'));
    }

    public function testCreateWithSignKey()
    {
        $urlBuilder = UrlBuilderFactory::create('img', 'example-sign-key');

        $this->assertEquals(
            '/img/image.jpg?s=56020c3dc5f68487c14510343c3e2c43',
            $urlBuilder->getUrl('image.jpg')
        );
    }

    public function testCreateWithSignKeyWithLeadingSlash()
    {
        $urlBuilder = UrlBuilderFactory::create('/img', 'example-sign-key');

        $this->assertEquals(
            '/img/image.jpg?s=56020c3dc5f68487c14510343c3e2c43',
            $urlBuilder->getUrl('image.jpg')
        );
    }
}
