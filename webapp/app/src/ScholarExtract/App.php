<?php

namespace ScholarExtract;

use Silex\Application as SilexApp;
use Silex\Provider\ServiceControllerServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Upload\Storage\FileSystem as UploadFileSystem;

/**
 * Main App File
 */
class App extends SilexApp
{
    const PRODUCTION = 1;
    const DEVELOP    = 2;

    // --------------------------------------------------------------

    /**
     * @var int  DEVELOP or PRODUCTION
     */
    private $mode;

    // --------------------------------------------------------------

    public static function main($mode = self::PRODUCTION)
    {
        $cls = get_called_class();
        $obj = new $cls($mode);
        return $obj->execute();
    }

    // --------------------------------------------------------------

    /**
     * Constructor
     * 
     * @param int $mode  self::PRODUCTION or self::DEVELOP
     */
    public function __construct($mode = self::PRODUCTION)
    {
        $this->mode = (int) $mode;
    }

    // --------------------------------------------------------------

    /**
     * Main Execute Method
     */
    public function execute()
    {
        $this->loadCommonLibraries();
        $this->loadWebLibraries();

        return $this->run();
    }

    // --------------------------------------------------------------

    private function loadWebLibraries()
    {
        $app =& $this;

        //Mode
        if ($this->mode == self::DEVELOP) {
            $app['debug'] = true;
        }

        //
        // Web Libraries
        //

        //URL Generator
        $app->register(new UrlGeneratorServiceProvider());

        //Service Controller
        $app->register(new ServiceControllerServiceProvider());

        //Twig
        $app->register(new TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/templates',
        ));

        //Uploader (overwrite = true)
        $app['uploader'] = $app->share(function() use ($app) {
            return new UploadFileSystem(realpath(__DIR__ . '/../uploads'), true);
        }

        //
        // Controllers
        //

        //ConvertInterface
        $app['convert.controller'] = $app->share(function() use ($app) {
            return new Controller\Converter($app['twig']);
        });
    }

    // --------------------------------------------------------------

    private function loadCommonLibraries()
    {
        $app =& $this;
    }

}

/* EOF: App.php */