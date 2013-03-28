<?php

namespace ScholarExtract\Controller;

use Silex\Application;
use Twig_Environment;
use Symfony\Component\HttpFoundation\Request;

/**
 * Main Interface Controller
 */
class MainInterface
{
    /**
     * @var Twig_Environment $twig
     */
    private $twig;

    /**
     * @var array
     */
    private $extractors;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig, array $extractors)
    {
        $this->twig       = $twig;
        $this->extractors = $extractors;
    }

    // --------------------------------------------------------------

    /**
     * Index HTML Page
     *
     * GET /
     */
    public function indexAction(Application $app, Request $req)
    {
        //Dynamic data for the main interface
        $data = array('extractors' => $this->extractors);

        //Display it
        return $this->twig->render('index.html.twig', $data);
    }
}

/* EOF: MainInterface.php */