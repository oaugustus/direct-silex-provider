<?php
namespace Direct\Api;

use Symfony\Component\HttpFoundation\Response;


/**
 * Api is the ExtDirect Api class.
 *
 * It provide the ExtDirect Api descriptor of exposed Controllers and methods.
 *
 * @author Otavio Fernandes <otavio@neton.com.br>
 */
class Api
{
    /**
     * Bundles to extract the api.
     * 
     * @var array 
     */
    protected $bundles = array();
    
    /**
     * The ExtDirect JSON API description.
     * 
     * @var JSON
     */
    protected $api = null;
    
    /**
     * The Silex application framework.
     * 
     * @var Silex\Application 
     */
    protected $app = null;
    

    /**
     * Initialize the API.
     * 
     * @param array  $bundles The bundles array
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->bundles = $app['direct.bundles'];
    }

    /**
     * Return the API in JSON format.
     *
     * @return string JSON API description
     */
    public function  __toString()
    {        
        if ($this->app['debug']){
            $this->api = $this->createApi();
        } else {
            $this->api = $this->getCachedApi();
        }
                
        return json_encode($this->api);        
    }  
    
    /**
     * Get the ExtDirect API response.
     * 
     * @return String
     */
    public function getApi()
    {
        if ($this->app['debug']){
            $exceptionLogStr = 
                'Ext.direct.Manager.on("exception", function(error){console.error(Ext.util.Format.format("Remote Call: {0}.{1}\n{2}", error.action, error.method, error.message, error.where)); return false;});';            
        }else {
            $exceptionLogStr = 
                sprintf('Ext.direct.Manager.on("exception", function(error){alert("%s");});', $this->app['direct.exception.message']);
        }
        
        $apiStr = sprintf("Ext.Direct.addProvider(%s);\n%s", $this, $exceptionLogStr);
        
        // force convertion of api to string
        $response = new Response($apiStr);
        
        // set the response header
        $response->headers->set('Content-Type', 'text/javascript');
        
        return $response;
    }
    
    
    /**
     * Returns the cached api.
     * 
     * @todo Implements the Api cache feature
     * 
     * @return type 
     */
    private function getCachedApi()
    {        
        return $this->createApi();
    }
    
    /**
     * Create the ExtDirect API based on controllers files.
     *
     * @return string JSON description of Direct API
     */
    private function createApi()
    {
        $bundles = $this->getControllers();

        $actions = array();        

        foreach ($bundles as $bundle => $controllers ) {
            $bundleShortName = str_replace('Bundle', '', $bundle);
                        
            foreach ($controllers as $controller) {
                $api = new ControllerApi($controller);

                if ($api->isExposed()) {
                    $actions[$bundleShortName."_".$api->getActionName()] = $api->getApi();
                }
                
            }
        }

        return array(
            'url' => $this->app['request']->getBaseUrl().
                     $this->app['direct.route.pattern'],
            'type' => $this->app['direct.api.type'],
            'namespace' => $this->app['direct.api.namespace'],
            'id' => $this->app['direct.api.id'],
            'actions' => $actions
        );
    }

    /**
     * Return the cached ExtDirect API.
     *
     * @return string JSON description of Direct API
     */
    protected function getApiFromCache()
    {
        //@todo: implement the cache mechanism
        return json_encode($this->createApi());
    }

    /**
     * Get all controllers from all bundles.
     *
     * @return array Controllers list
     */
    protected function getControllers()
    {
        $controllers = array();
        $finder = new ControllerFinder();
        
        // get each controller from a bundle
        foreach ($this->bundles as $bundle => $path) {
            
            $found = $finder->getControllers($bundle, $path);
            
            // if any controller exist in the bundle
            if (!empty ($found)) {
                // store this controller
                $controllers[$bundle] = $found;
            }
        }

        return $controllers;
    }    
}
