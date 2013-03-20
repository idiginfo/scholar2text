<?php

namespace ScholarExtract\Controller;

use Twig_Environment;
use ScholarExtract\Library\PDFConverter;
use Symfony\Component\HttpFoundation\Request;
use Upload\File as UploadFile;
use Upload\Validation as UploadVal;
use Upload\Storage\FileSystem as UploadFileSystem;


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
     */
    public function indexAction()
    {
        return $this->twig->render('index.html.twig');
    }

    // --------------------------------------------------------------

    /**
     * Upload Action
     */
    public function uploadAction(Request $req)
    {
        //Setup the upload
        $f = new UploadFile('pdffile', $app['uploader']);
        $f->addValidations($this->getValidators());

        //Do the upload
        try {
            $f->upload();

            $filepath  = 'uploads/' . $f->getNameWithExtension();
            $txtOutput = convertToTxt($filepath);

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

    private getValidators()
    {
        $mimeVal = new UploadVal\Mimetype('application/pdf');
        $sizeVal = new UploadVal\Size('10M');
        $mimeVal->setMessage("The file does not appear to be a PDF");
        return array($mimeVal, $sizeVal);
    }
}

/* EOF: Converter.php */