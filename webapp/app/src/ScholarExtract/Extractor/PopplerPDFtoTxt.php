<?php

namespace ScholarExtract\Extractor;

use Symfony\Component\Process\ProcessBuilder;
use RuntimeException;

/**
 * PopplerPDFtoTxt Extractor
 */
class PopplerPDFtoTxt implements ExtractorInterface
{
    /**
     * @var string  The python command to perform the conversion
     */
    private $cmd;
    
    /**
     * @var Symfony\Component\Process\ProcessBuilder
     */ 
    private $proc;

    // --------------------------------------------------------------

    
    /**
     * Constructor
     *
     * @param Symfony\Component\Process\ProcessBuilder
     * @param string  The command (null=default)
     */
    public function __construct(ProcessBuilder $proc = null, $cmd = null)
    {
        $this->cmd  = $cmd ?: realpath('somecmd');
        $this->proc = $proc ?: new ProcessBuilder();
    }

    // --------------------------------------------------------------

    static public function getName()
    {
        return "LaPDFText";
    }

    // --------------------------------------------------------------

    static public function getDescription()
    {
        return "A JAVA application for extracting PDF to XML for scientific articles.";        
    }

    // --------------------------------------------------------------

    static public function getLink()
    {
        return "https://code.google.com/p/lapdftext/";
    }

    // --------------------------------------------------------------

    /**
     * Extract text from PDF file
     *
     * @param string  $file    Realpath to file
     * @return string|boolean  False if could not be converted
     */
    public function extract($filepath)
    {
        throw new \BadMethodCallException("Not yet implemented");
    }
}

/* EOF: PopplerPDFtoTxt.php */