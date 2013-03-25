<?php

namespace ScholarExtract\Controller;

use Silex\Application;
use Twig_Environment;
use ScholarExtract\Library\PDFConverter;
use Symfony\Component\HttpFoundation\Request;
use Upload\File as UploadFile;
use Upload\Validation as UploadVal;
use Upload\Storage\FileSystem as UploadFileSystem;
use RuntimeException;

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
        //Setup the upload
        $f = new UploadFile('pdffile', $this->uploader);
        $f->addValidations($this->getValidators());

        //Do the upload
        try {
            $f->upload();

            $filepath  = 'uploads/' . $f->getNameWithExtension();

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
                'pdf' => $filepath,
                'txt' => $txtOutput
            );

            return $app->json($output, 200);
        }
        catch (Exception $e) {
            return $app->json(array('messages' => $f->getErrors()), 400);
        }
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