<?php

namespace XtractPDF;

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\ProcessBuilder;
use Upload\Storage\FileSystem as UploadFileSystem;
use Upload\File as UploadFile;
use Upload\Validation as UploadVal;
use Exception;

// ------------------------------------------------------------------

require(__DIR__ . '/app/vendor/autoload.php');

App::main(App::DEVELOP);

die();

// ------------------------------------------------------------------

// //Setup the App
// $app = new Application();



// $app->get('/', function(Application $app, Request $req) {
    
//     return $app['twig']->render('index.html.twig');

// })->bind('front');

// // ------------------------------------------------------------------

// $app->post('/upload', function(Application $app, Request $req) {
    
//     //Setup the upload
//     $f = new UploadFile('pdffile', $app['uploader']);
//     $f->addValidations(getValidators());

//     //Do the upload
//     try {
//         $f->upload();

//         $filepath  = 'uploads/' . $f->getNameWithExtension();
//         $txtOutput = convertToTxt($filepath);

//         if ($txtOutput == 'False') {
//             $txtOutput = '';
//         }

//         $output = array(
//             'pdf' => $filepath,
//             'txt' => $txtOutput
//         );

//         return $app->json($output, 200);
//     }
//     catch (Exception $e) {
//         return $app->json(array('messages' => $f->getErrors()), 400);
//     }

// })->bind('upload');

// // ------------------------------------------------------------------

// $app->get('/test', function(Application $app, Request $req) {

//     $file = __DIR__ . '/python/samples/sample4.pdf';

//     ob_start();
//     var_dump(convertToTxt($file));
//     return "<pre>" . ob_get_clean() . "</pre>";

// })->bind('test');


// // ------------------------------------------------------------------

// $app->run();

// // ------------------------------------------------------------------

// /**
//  * Get the upload validators
//  */
// function getValidators()
// {
//         $mimeVal = new UploadVal\Mimetype('application/pdf');
//         $sizeVal = new UploadVal\Size('10M');
//         $mimeVal->setMessage("The file does not appear to be a PDF");
//         return array($mimeVal, $sizeVal);
// }

// // ------------------------------------------------------------------

// /**
//  * Run external Python app to get the conversions
//  *
//  * @param string $file   Full path to file
//  * @param boolean $narr  Try to extract the narrative?
//  */
// function convertToTxt($file, $narr = true)
// {
//     $cmd = array(__DIR__ . '/python/scholar2txt.py');

//     if ( ! $narr) {
//         $cmd[] = '--skipnarr';
//     }

//     //Get the process and run it
//     $cmd[] = $file;
//     $builder = new ProcessBuilder($cmd);
//     $proc = $builder->getProcess();
//     $proc->run();

//     //Return the output
//     return $proc->getOutput();
// }