<?php
namespace Direct;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Direct\Api;
use Direct\Router;

class DirectServiceProvider implements ServiceProviderInterface
{
    /**
     * Register the service provider.
     * 
     * @param Application $app
     */
    public function register(Application $app)
    {        
        // direct api
        $app['direct.api'] = function() use ($app){
            return new Api($app);
        };
        
        // direct router
        $app['direct.router'] = function() use ($app){
            return new Router($app);
        };

        // redefine the Silex detault route class
        $app['route_class'] = 'Direct\\Silex\\Route';

        // default configs
        $app['direct.bundles'] = isset($app['direct.bundles']) ? $app['direct.bundles'] : array();
        $app['direct.responseEncode'] = isset($app['direct.responseEncode']) ? $app['direct.responseEncode'] : true;
        $app['direct.requestDecode'] = isset($app['direct.requestDecode']) ? $app['direct.requestDecode'] : true;
        $app['direct.route.pattern'] = isset($app['direct.route.pattern']) ? $app['direct.route.pattern'] : '/route';
        $app['direct.api.type'] = isset($app['direct.api.type']) ? $app['direct.api.type'] : 'remoting';
        $app['direct.api.namespace'] = isset($app['direct.api.namespace']) ? $app['direct.api.namespace'] : 'Actions';
        $app['direct.api.id'] = isset($app['direct.api.id']) ? $app['direct.api.id'] : 'API';
        $app['direct.exception.message'] = isset($app['direct.exception.message']) ? $app['direct.exception.message'] : 'Whoops, looks like something went wrong.';

    }

    /**
     * Setup the application.
     * 
     * @param Application $app 
     */
    public function boot(Application $app)
    {
        // the direct api route
        $app->get('/api.js', function(Application $app){
            return $app['direct.api']->getApi();
        })->bind('directapi');

        // the direct api route remoting description
        $app->get('/remoting.js', function(Application $app){
            return $app['direct.api']->getRemoting();
        });

        // the direct router route
        $app->post('/route', function(Application $app){
            // handle the route
            return $app['direct.router']->route();
        });
    }
}