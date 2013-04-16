<?php

namespace XtractPDF;

use Silex\Application as SilexApp;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use XtractPDF\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Upload\Storage\FileSystem as UploadFileSystem;
use Symfony\Component\Process\ProcessBuilder;
use Whoops\Provider\Silex\WhoopsServiceProvider;
use Whoops\Handler\JsonResponseHandler;
use Pimple;

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
        if($this['debug']) {
            $this->register(new WhoopsServiceProvider());
            $this['whoops']->pushHandler(new JsonResponseHandler());
        }

        //Run it!
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
            return new UploadFileSystem($this['pdf_filepath'], true);
        });

        //
        // Controllers
        //
        $app->mount('', new Controller\MainInterface());
        $app->mount('', new Controller\Extractor());
        $app->mount('', new Controller\StaticPages());
    }

    // --------------------------------------------------------------

    private function loadCommonLibraries()
    {
        $app =& $this;

        //Filepath
        $app['pdf_filepath'] = $this->basePath('/uploads');

        //PDF Extractors
        $app['extractors'] = $this->share(function() use ($app) {
            return new Library\ExtractorBag(array(
                new Extractor\PopplerPDFtoTxt(),                
                new Extractor\PDFMiner()

                // IMPELMENT THESE LATER
                // new Extractor\CrossRefExtractor(),
                // new Extractor\LaPDFText(),
            ));
        });
    }
}

/* EOF: App.php */