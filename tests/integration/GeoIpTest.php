<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GeoIpTest extends PHPUnit_Framework_Testcase
{

    public function setUp()
    {
        $app = new \Stack\CallableHttpKernel(function(Request $request) {
            return new Response($request->headers->get('X-Country', 'NONE'));
        });

        $stack = new \Stack\Builder();
        $stack->push('\Ducks\Stack\GeoIp');

        $this->app = $stack->resolve($app);
    }

    public function testWithoutGeolocation()
    {
        $request = Request::create('/');

        $response = $this->app->handle($request);

        $this->assertEquals('NONE', $response->getContent());
    }

    /** @dataProvider provideIpAddresses */
    public function testWithGeolocation($ip, $expectedCountry)
    {
        $request = Request::create('/');
        $request->server->set('REMOTE_ADDR', $ip);

        $response = $this->app->handle($request);

        $this->assertEquals($expectedCountry, $response->getContent());
    }

    public function provideIpAddresses()
    {
        return array(
            array('202.158.214.106', 'AU'),
            array('70.38.0.135', 'CA'),
            array('83.142.228.128', 'GB'),
            array('128.30.2.36', 'US'),
        );
    }

}
