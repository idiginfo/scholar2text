<?php

ScholarExtract\Extractor;

use Symfony\Component\Process\ProcessBuilder;
use RuntimeException;

/**
 * CrossRef Extractor
 */
class CrossRefExtractor implements ExtractorInterface
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
     * @param string  The python command to run for conversion(null = default)
     */
    public function __construct(ProcessBuilder $proc = null, $pdfCmd = null)
    {
        $this->cmd  = $pdfCmd ?: realpath(__DIR__ . '/../../../../../python/scholar2txt.py');
        $this->proc = $proc ?: new ProcessBuilder();
    }
    
    // --------------------------------------------------------------

    public function getName()
    {
        return "CrossRef PDFExtractor for Citations";
    }

    // --------------------------------------------------------------

    public function getDescription()
    {
        return "A Ruby library to extract bibliographic citations from a PDF";        
    }

    // --------------------------------------------------------------

    public function getLink()
    {
        return "https://github.com/CrossRef/pdfextract";
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


/* EOF: CrossRefExtractor.php */