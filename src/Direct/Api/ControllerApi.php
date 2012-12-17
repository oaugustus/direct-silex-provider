<?php
namespace Direct\Api;

/**
 * ControllerApi encapsulate methods to get the Controller exposed Api.
 *
 * @author Otavio Fernandes <otabio@neton.com.br>
 */
class ControllerApi
{
    /**
     * Store the controller reflection object.
     * 
     * @var \Reflection
     */
    protected $reflection;

    /**
     * The controller ExtDirect api.
     * 
     * @var array
     */
    protected $api;

    /**
     * The remote annotation attribute.
     * 
     * @var string 
     */
    protected $remoteAttribute = '@remote';
    
    /**
     * The form annotation attribute.
     * 
     * @var string 
     */
    protected $formAttribute = '@form';    

    /**
     * Initialize the object.
     * 
     * @param \Symfony\Component\Container $container
     * @param string $controller
     */
    public function __construct($controller)
    {
        $this->reflection = new \ReflectionClass($controller);                
        
        $this->api = $this->createApi();        
    }

    /**
     * Check if the controller has any method exposed.
     *
     * @return Boolean true if has exposed, otherwise return false
     */
    public function isExposed()
    {        
        return (null != $this->api) ? true : false;
    }

    /**
     * Return the api.
     * 
     * @return array
     */
    public function getApi()
    {        
        return $this->api;
    }

    /**
     * Return the name of exposed direct Action.
     * 
     * @return string
     */
    public function getActionName()
    {        
        return str_replace('Controller','',$this->reflection->getShortName());
    }
    
    /**
     * Try create the controller api.
     *
     * @return array
     */
    protected function createApi()
    {
        $api = null;
        
        // get public methods from controller
        $methods = $this->reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $mApi = $this->getMethodApi($method);

            if ($mApi) {
                $api[] = $mApi;
            }
        }

        return $api;        
    }

    /**
     * Return the api of method.
     *
     * @param \ReflectionMethod $method
     *
     * @return mixed (array/boolean)
     */
    private function getMethodApi($method)
    {
        $api = false;

        if (strlen($method->getDocComment()) > 0) {
            $doc = $method->getDocComment();

            $isRemote = !!preg_match('/' . $this->remoteAttribute . '/', $doc);

            if ($isRemote) {
                $api['name'] = str_replace('Action','',$method->getName());
                $api['len'] = 1;//$method->getNumberOfParameters();

                if(!!preg_match('/' . $this->formAttribute . '/', $doc)) {
                    $api['formHandler'] = true;
                }
            }
        }

        return $api;
    }

}
