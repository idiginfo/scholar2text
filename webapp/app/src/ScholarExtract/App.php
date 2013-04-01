<?php

namespace ScholarExtract;

use Silex\Application as SilexApp;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Upload\Storage\FileSystem as UploadFileSystem;
use Symfony\Component\Process\ProcessBuilder;
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

        //Front Controller
        $app['maininterface.controller'] = $app->share(function() use ($app) {
            return new Controller\MainInterface($app['twig'], $app['extractors']);
        });

        //Extractor Controller
        $app['extractor.controller'] = $app->share(function() use ($app) {
            return new Controller\Extractor($app['uploader'], $app['extractors'], $app['pdf_filepath']);
        });

        //
        // Routes
        //
        $app->get('/', "maininterface.controller:indexAction")->bind('front');
        $app->get('/pdf/{file}', "extractor.controller:renderPdfAction")->bind('pdf');
        $app->post('/upload', "extractor.controller:uploadAction")->bind('upload');
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
                new Extractor\CrossRefExtractor(),
                new Extractor\LaPDFText(),
                new Extractor\PDFMiner(),
                new Extractor\PDFX(),
                new Extractor\PopplerPDFtoTxt(),
            ));
        });
    }
}

/* EOF: App.php */