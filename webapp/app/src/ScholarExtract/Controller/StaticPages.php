<?php

namespace ScholarExtract\Controller;

use Twig_Environment;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class StaticPages
{
    private $twig;

    // --------------------------------------------------------------

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    // --------------------------------------------------------------

    public function aboutAction(Application $app, Request $req)
    {
        return $this->render('about.html.twig', "About XtractPDF", $app, $req);
    }

    // --------------------------------------------------------------

    public function apiDocsAction(Application $app, Request $req)
    {
        return $this->render('apidocs.html.twig', "XtractPDF API", $app, $req);
    }

    // --------------------------------------------------------------

    private function render($toRender, $title, Application $app, Request $req)
    {
        //Setup data
        $data = array('page_title' => $title);

        //Load the pagecontent
        $pageContent = $this->twig->render($toRender, $data);

        //If AJAX, only get the content
        if ($req->isXmlHttpRequest()) {
            return $pageContent;
        }
        else {
            $data['page_content'] = $pageContent;
            return $this->twig->render('static.html.twig', $data);
        }
    }
    
}

/* EOF: StaticPages.php */