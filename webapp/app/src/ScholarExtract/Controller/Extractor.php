<?php

namespace ScholarExtract\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Upload\File as UploadFile;
use Upload\Validation as UploadVal;
use Upload\Storage\FileSystem as UploadFileSystem;
use Upload\Exception\UploadException;
use RuntimeException, Exception;
use ScholarExtract\Library\ExtractorBag;

/**
 * Extractor Controller
 */
class Extractor
{
    /**
     * @var  Upload\Storage\FileSystem
     */
    private $uploader;

    /**
     * @var array
     */
    private $extractors;

    /**
     * @var string Filepath of uploads
     */
    private $filepath;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Upload\Storage\FileSystem           $uploader
     * @param ScholarExtract\Library\ExtractorBag $extractors
     * @param string                              $filepath    Filepath of uploads
     */
    public function __construct(UploadFileSystem $uploader, ExtractorBag $extractors, $filepath)
    {
        $this->uploader   = $uploader;
        $this->extractors = $extractors;
        $this->filepath   = $filepath;
    }

    // --------------------------------------------------------------

    /**
     * Upload Action
     *
     * POST /upload {engine=string}
     */
    public function uploadAction(Request $req, Application $app)
    {        
        //Setup a unique key to name and identify the uploaded file
        $key = md5(time() . rand(100000, 999999));

        //Setup the file upload object
        $f = new UploadFile('pdffile', $this->uploader);
        $f->setName($key); //Rename it on upload to our key
        $f->addValidations($this->getValidators()); //Set validations

        //Determine which extractor engine to use
        //$_POST['engine']
        $extractor = $this->extractors->get($req->request->get('engine') ?: 'PDFMiner');
        if ( ! $extractor) {
            return $this->abort($app, "The specified extractor does not exist", 400);
        }

        //Do the uploads
        try {
            $f->upload();

            $filename = $f->getNameWithExtension();
            $filepath = $this->filepath. '/' . $filename;

            // try {
            $txtOutput = $extractor->extract($filepath);    
            // }
            // catch (RuntimeException $e) {
            //     $txtOutput = false;
            // }
            
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
        catch (UploadException $e) {
            return $this->abort($app, $f->getErrors(), 400);
        }
    }

    // --------------------------------------------------------------

    /**
     * Render a PDF and then destroy it
     *
     * GET /pdf
     * 
     * @param string $file  The filename
     */
    public function renderPdfAction(Request $req, Application $app, $file)
    {
        //Get the filepath
        $filepath = $this->filepath . '/' . $file;

        //Will remove the file after it is done streaming
        $app->finish(function() use ($filepath) {
            if (file_exists($filepath)) {
                unlink($filepath);
            }
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

    protected function abort(Application $app, $messages, $code = 500)
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