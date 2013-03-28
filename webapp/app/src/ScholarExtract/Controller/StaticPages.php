<?php

/**
 * Static Pages Controller
 */
class StaticPages
{
    /**
     * @var Twig_Environment $twig
     */
    private $twig;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    // --------------------------------------------------------------

    public function about()
    {
        //About Page
    }

    // --------------------------------------------------------------

    public function api()
    {
        //API Page
    }

    // --------------------------------------------------------------

    public function terms()
    {
        //Terms of Use page
    }

    // --------------------------------------------------------------

    public function code()
    {
        //Link to Github
    }
}

/* EOF: StaticPages.php */