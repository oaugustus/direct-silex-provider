<?php

namespace Direct\Tests;

use Silex\Application;
use Direct\DirectExtension;
use Symfony\Component\HttpFoundation\Request;


class DirectExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('Direct\\DirectExtension')) {
            $this->markTestSkipped('Silex-Direct was not installed.');
        }
    }    
    
    /**
     * Test the /api.js route.
     */
    public function testApi()
    {
        // get the application instance
        $app = $this->getApplication();
        
        // create a request to /api.js route
        $request = Request::create('/api.js');                
        
        // get the response
        $response = $app->handle($request);
        
        $this->assertStringStartsWith('Ext.Direct.addProvider', $response->getContent());        
    }
    
    /**
     * Test the /route route.
     */
    public function testRoute()
    {
        $GLOBALS['HTTP_RAW_POST_DATA'] = '{"action":"Neton_Test","method":"test","data":[{"name":"Otavio"}],"type":"rpc","tid":1}';        
        $jsonRequest = json_decode($GLOBALS['HTTP_RAW_POST_DATA'],true);
        
        // get the application instance
        $app = $this->getApplication();
        $app['debug'] = true;
        
        
        // create a request to /route route
        $request = Request::create('/route','POST', $jsonRequest);        
        $request->overrideGlobals();
                       
        // get the response
        $response = $app->handle($request);
        
        $this->assertContains('Success', $response->getContent());        
    }
    
    
    /**
     * Setup the application.
     * 
     * @return Application 
     */
    private function getApplication()
    {
        // create the application
        $app = new Application();
        
        // register the Direct extension
        $app->register(new DirectExtension(), array(
            'direct.bundles' => array(
                'Neton' => __DIR__
            )
        ));
        
        return $app;
    }
    
}