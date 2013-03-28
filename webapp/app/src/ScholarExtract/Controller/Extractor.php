<?php

namespace ScholarExtract\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Upload\File as UploadFile;
use Upload\Validation as UploadVal;
use Upload\Storage\FileSystem as UploadFileSystem;
use RuntimeException, Exception;

/**
 * Converter Class
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
    private $extractors

    /**
     * @var string Filepath of uploads
     */
    private $filepath

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Upload\Storage\FileSystem           $uploader
     * @param array                               $extractors
     * @param string                              $filepath    Filepath of uploads
     */
    public function __construct(UploadFileSystem $uploader, array $extractors, $filepath)
    {
        $this->uploader   = $uploader;
        $this->extractors = $extractors;
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
            $filepath = $this->filepath. '/' . $filename;

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