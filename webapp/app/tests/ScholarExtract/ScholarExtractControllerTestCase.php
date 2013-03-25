<?php

namespace ScholarExtract;

use Silex\WebTestCase;

abstract class ScholarExtractControllerTestCase extends WebTestCase
{
    /**
     * Implement the createApplication method to be used in all controller tests
     */
    public function createApplication()
    {
        $app = new App(App::DEVELOP);
        $app['exception_handler']->disable();

        return $app;
    }
}

/* EOF: ScholarExtractControllerTestCase.php */