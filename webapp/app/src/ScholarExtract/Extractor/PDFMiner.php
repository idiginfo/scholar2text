<?php

namespace ScholarExtract\Extractor;

use Symfony\Component\Process\ProcessBuilder;
use RuntimeException;

/**
 * PDFMiner Extractor
 */
class PDFMiner implements ExtractorInterface
{
    /**
     * @var string  The python command to perform the conversion
     */
    private $pyCmd;
    
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
        $this->pyCmd = $pdfCmd ?: realpath('/usr/bin/pdf2txt');
        $this->proc  = $proc ?: new ProcessBuilder();

        if ( ! is_executable($this->pyCmd)) {
            throw new RuntimeException("The command does not exist or is not executable: ". $this->pyCmd);
        }
    }

    // --------------------------------------------------------------

    static public function getName()
    {
        return "PDFMiner";
    }

    // --------------------------------------------------------------

    static public function getDescription()
    {
        return "A Python library for extracting text from a PDF.";        
    }

    // --------------------------------------------------------------

    static public function getLink()
    {
        return "http://www.unixuser.org/~euske/python/pdfminer/";
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
        //Clone the process builder
        $proc = clone $this->proc;

        //Build the command
        $proc->add($this->pyCmd);
        $proc->add($filepath);
        $proc->add('-t text');

        //Get the process and run it
        $process = $proc->getProcess();
        $process->run();

        if ($process->isSuccessful()) {
            return $process->getOutput();
        }
        else {
            throw new RuntimeException($process->getExitCodeText());
        }

        //Return the output
        $output = $process->getOutput();
        return ($output == 'False') ? false : $output;  
    }
}

/* EOF: PDFMiner.php */
