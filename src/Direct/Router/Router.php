<?php
namespace Direct\Router;

/**
 * Router is the ExtDirect Router class.
 *
 * It provide the ExtDirect Router mechanism.
 *
 * @author Otavio Fernandes <otavio@neton.com.br>
 */
class Router
{
    /**
     * The ExtDirect Request object.
     * 
     * @var Direct\Request
     */
    protected $request;
    
    /**
     * The ExtDirect Response object.
     * 
     * @var Direct\Response
     */
    protected $response;
    
    /**
     * The Silex application framework.
     * 
     * @var Silex\Application 
     */
    protected $app = null;
    
    /**
     * Initialize the router object.
     * 
     * @param Silex\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->request = new Request($app['request']);
        $this->response = new Response($this->request->getCallType());
    }
        
    /**
     * Do the ExtDirect routing processing.
     *
     * @return JSON
     */
    public function route()
    {
        $batch = array();
                
        foreach ($this->request->getCalls() as $call) {
            $batch[] = $this->dispatch($call);
        }
        
        return $this->response->encode($batch);
    }

    /**
     * Dispatch a remote method call.
     * 
     * @param  Neton\DirectBundle\Router\Call $call
     * @return Mixed
     */
    private function dispatch($call)
    {
        
        $controller = $this->resolveController($call->getAction());
        $method = $call->getMethod()."Action";

        if (!is_callable(array($controller, $method))) {
            //todo: throw an execption method not callable
        }

        if ('form' == $this->request->getCallType()) {
            $result = $controller->$method($call->getData(), $this->request->getFiles());
            $response = $call->getResponse($result);
        } else {
            try{
                $result = $controller->$method($call->getData());
                $response = $call->getResponse($result);
            }catch(\Exception $e){
                $response = $call->getException($e);
            }            
        }

        return $response;
    }

    /**
     * Retorna o tipo de chamada.
     * 
     * @return String
     */
    private function getCallType()
    {
        return $this->request->getCallType();
    }
    
    /**
     * Resolve the called controller from action.
     * 
     * @param  string $action
     * @return <type>
     */
    private function resolveController($action)
    {
        list($bundleName, $controllerName) = explode('_',$action);
        
        $namespace = $bundleName."\\Controller";

        $class = $namespace."\\".$controllerName."Controller";

        try {
            $controller = new $class();
            if (is_subclass_of($controller, '\\Direct\\Controller\\DirectController')) {
                
                $controller->setApplication($this->app);
            }

            return $controller;
        } catch(Exception $e) {
            die($class);
        }
    }
}
