<?php
namespace Yonkoma;

use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_Template;

class Singleton
{
    /**
     * Get a twig template
     *
     * @return Twig_Template
     */
    public static function getTwig(string $templateName)
    {
        global $config;
        static $twigInstance = null;
        static $twigTemplates = [];
        if ($twigInstance === null) {
            $loader = new Twig_Loader_Filesystem($config['path']['root']. 'template');
            $twigInstance = new Twig_Environment($loader, array(
                'cache' => $config['path']['root']. 'cache/twig',
            ));
        }
        if (!isset($twigTemplates[$templateName])) {
            $twigTemplates[$templateName] = $twigInstance->loadTemplate($templateName);
        }
        return $twigTemplates[$templateName];
    }
}
