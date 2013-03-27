<?php

namespace ScholarExtract\Library;

use Symfony\Component\Process\ProcessBuilder;
use RuntimeException;

/**
 * PDF Converter
 */
class PDFConverter
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
        $this->pyCmd = $pdfCmd ?: realpath(__DIR__ . '/../../../../../python/scholar2txt.py');
        $this->proc = $proc ?: new ProcessBuilder();
    }

    // --------------------------------------------------------------
    
    /**
     * Extract text from PDF file
     *
     * @param string  $file         Realpath to file
     * @param boolean $extractNarr  If false, will skip narrative extraction
     * @return string|boolean       False if could not be converted
     */
    public function convert($file, $extractNarr = false)
    {
        //File readable?
        if ( ! is_readable($file)) {
            throw new \RuntimeException("The file is not readable: " . $file);
        }

        //Clone the process builder
        $proc = clone $this->proc;

        //Get the command
        $proc->add($this->pyCmd);
        if ( ! $extractNarr) {
            $proc->add('--skipnarr');
        }
        $proc->add($file);

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

/* EOF: PDFConverter.php */