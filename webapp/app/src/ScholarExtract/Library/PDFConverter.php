<?php

namespace ScholarExtract\Library;

use Symfony\Component\Process\ProcessBuilder;

/**
 * PDF Converter
 */
class PDFConverter
{
    /**
     * @var string
     */
    private static $cmd = __DIR__ . '/python/scholar2txt.py';
    
    // --------------------------------------------------------------

    /**
     * @var Symfony\Component\Process\ProcessBuilder
     */ 
    private $proc;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param Symfony\Component\Process\ProcessBuilder
     */
    public function __construct(ProcessBuilder $proc)
    {
        $this->proc = $proc;
    }

    // --------------------------------------------------------------
    
    /**
     * Extract text from PDF file
     *
     * @param string  $file         Realpath to file
     * @param boolean $extractNarr  If false, will skip narrative extraction
     * @return string|boolean       False if could not be converted
     */
    public function convert($file, $extractNarr = true)
    {
        //File readable?
        if ( ! is_readable($file)) {
            throw new \RuntimeException("The file is not readable: " . $file);
        }

        $proc = clone $this->proc;

        //Get the command
        $proc->add(self::$cmd);
        if ( ! $narr) {
            $proc->add('--skipnarr');
        }
        $proc->add($file);

        //Get the process and run it
        $process = $proc->getProcess();
        $process->run();

        //Return the output
        $output = $process->getOutput();
        return ($output == 'False') ? false : $output;  
    }    
}

/* EOF: PDFConverter.php */