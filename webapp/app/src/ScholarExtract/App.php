<?php

namespace ScholarExtract;

use Silex\Application as SilexApp;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Upload\Storage\FileSystem as UploadFileSystem;
use Symfony\Component\Process\ProcessBuilder;

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
        parent::__construct();

        //Mode
        $this->mode = (int) $mode;

        //Load libraries
        $this->loadCommonLibraries();
        $this->loadWebLibraries();        
    }

    // --------------------------------------------------------------

    /**
     * Main Execute Method
     */
    public function execute()
    {
        return $this->run();
    }

    // --------------------------------------------------------------

    protected function basePath($subPath = '')
    {
        $subPath = trim($subPath, '/');
        return realpath(__DIR__ . '/../../../' . $subPath);
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

        //Service Controller
        $app->register(new ServiceControllerServiceProvider());

        //URL Generator
        $app->register(new UrlGeneratorServiceProvider());

        //Twig
        $app->register(new TwigServiceProvider(), array(
            'twig.path' => $this->basePath('/templates')
        ));

        //Uploader (overwrite = true)
        $app['uploader'] = $app->share(function() use ($app) {
            return new UploadFileSystem($this->basePath('/uploads'), true);
        });

        //
        // Controllers
        //

        //ConvertInterface
        $app['convert.controller'] = $app->share(function() use ($app) {
            return new Controller\Converter($app['twig'], $app['uploader'], $app['converter']);
        });

        //
        // Routes
        //
        $app->get('/', "convert.controller:indexAction")->bind('front');
        $app->post('/upload', "convert.controller:uploadAction")->bind('upload');
    }

    // --------------------------------------------------------------

    private function loadCommonLibraries()
    {
        $app =& $this;

        $app['converter'] = $this->share(function() use ($app ) {
            return new Library\PDFConverter(new ProcessBuilder());
        });
    }

}

/* EOF: App.php */