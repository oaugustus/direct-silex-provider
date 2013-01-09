<?php
namespace Direct\Api;

use Symfony\Component\Finder\Finder;

/**
 * Controller Finder find all controllers from a Bundle.
 *
 * @author Otavio Fernandes <otavio@neton.com.br>
 */
class ControllerFinder
{
    /**
     * Find all controllers from a bundle.
     * 
     * @param string $bundle
     * @param string $dir
     * 
     * @return Mixed
     */
    public function getControllers($bundle, $dir)
    {
        $dir = $dir.'/'.$bundle."/Controller";

        $controllers = array();
        
        if (is_dir($dir)) {
            $finder = new Finder();            
            $finder->files()->in($dir)->name('*Controller.php');
            
            foreach ($finder as $file) {

                $name = explode('.',$file->getFileName());
                $class = $bundle."\\Controller\\".$name[0];
                $controllers[] = $class;
            }
        }

        return $controllers;
    }
}
