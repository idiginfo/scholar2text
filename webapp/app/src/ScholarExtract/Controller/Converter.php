<?php

namespace ScholarExtract\Controller;

use Silex\Application;
use Twig_Environment;
use ScholarExtract\Library\PDFConverter;
use Symfony\Component\HttpFoundation\Request;
use Upload\File as UploadFile;
use Upload\Validation as UploadVal;
use Upload\Storage\FileSystem as UploadFileSystem;
use RuntimeException, Exception;

/**
 * Converter Class
 */
class Converter
{
    /**
     * @var \Twig_Library
     */
    private $twig;

    /**
     * @var ScholarExtract\Library\PDFConverter
     */
    private $converter;

    /**
     * @var  Upload\Storage\FileSystem
     */
    private $uploader;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Twig_Environment                    $twig
     * @param Upload\Storage\FileSystem           $uploader
     * @param ScholarExtract\Library\PDFConverter $converter
     */
    public function __construct(Twig_Environment $twig, UploadFileSystem $uploader, PDFConverter $converter)
    {
        $this->twig      = $twig;
        $this->uploader  = $uploader;
        $this->converter = $converter;
    }

    // --------------------------------------------------------------

    /**
     * Index HTML Page
     *
     * GET /
     */
    public function indexAction()
    {
        return $this->twig->render('index.html.twig');
    }

    // --------------------------------------------------------------

    /**
     * Upload Action
     *
     * POST /upload
     */
    public function uploadAction(Request $req, Application $app)
    {        
        //Setup a key
        $key = md5(time() . rand(100000, 999999));

        //Setup the file
        $f = new UploadFile('pdffile', $this->uploader);

        //Rename it on upload to our key
        $f->setName($key);

        //Set validations
        $f->addValidations($this->getValidators());

        //Do the upload
        try {
            $f->upload();

            $filename = $f->getNameWithExtension();
            $filepath = $app['pdf_filepath'] . '/' . $filename;

            try {
                $txtOutput = $this->converter->convert($filepath);    
            }
            catch (RuntimeException $e) {
                $txtOutput = false;
            }
            
            if ( ! $txtOutput) {
                $txtOutput = '';
            }

            $output = array(
                'pdfurl' => $app['url_generator']->generate('pdf', array('file' => $filename)),
                'pdf'    => $filename,
                'txt'    => $txtOutput
            );

            return $app->json($output, 200);
        }
        catch (Exception $e) {
            return $this->abort($app, $f->getErrors(), 400);
        }
    }

    // --------------------------------------------------------------

    /**
     * Render a PDF and then destroy it
     *
     * @param string $file  The filename
     */
    public function renderPdfAction(Request $req, Application $app, $file)
    {
        //Get the filepath
        $filepath = $app['pdf_filepath'] . '/' . $file;

        //Will remove the file after it is done streaming
        $app->finish(function() use ($filepath) {
            unlink($filepath);
        });

        //If the file is readable, then send it; else 404
        if (is_readable($filepath)) {
            return $app->sendFile($filepath); //, 200, array('Content-Type' => 'application/pdf'));
        }
        else {
            return $this->abort($app, "PDF file gone.  Uploaded PDFs are deleted immediately", 410);
        }
    }

    // --------------------------------------------------------------

    protected function abort(Application $app, array $messages, $code = 500)
    {
        if ( ! is_array($messages)) {
            $messages = array($messages);
        }

        return $app->json(array('messages' => $messages), (int) $code);
    }

    // --------------------------------------------------------------


    /**
     * Get file upload validators
     *
     * @return array  Array of Upload Validators
     */
    private function getValidators()
    {
        $mimeVal = new UploadVal\Mimetype('application/pdf');
        $sizeVal = new UploadVal\Size('10M');
        $mimeVal->setMessage("The file does not appear to be a PDF");
        return array($mimeVal, $sizeVal);
    }
}

/* EOF: Converter.php */