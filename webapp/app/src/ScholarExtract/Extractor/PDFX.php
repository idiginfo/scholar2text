<?php

namespace ScholarExtract\Extractor;

use Guzzle\Http\Client as GuzzleClient;
use RuntimeException;

/**
 * PDFX Extractor
 */
class PDFX implements ExtractorInterface
{
    /**
     * @var string  The python command to perform the conversion
     */
    private $guzzle;
    
    /**
     * @var string  The endpoint URL
     */ 
    private $url;

    // --------------------------------------------------------------


    /**
     * Constructor
     *
     * @param Guzzle\Http\Client
     * @param string  The URL to the endpoint
     */
    public function __construct(GuzzleClient $client = null, $url = 'http://example.com/xxx')
    {
        $this->guzzle = $client ?: new GuzzleClient();
        $this->url    = $url;
    }
    
    // --------------------------------------------------------------

    static public function getName()
    {
        return "PDFX";
    }

    // --------------------------------------------------------------

    static public function getDescription()
    {
        return "A library.. fix me";        
    }

    // --------------------------------------------------------------

    static public function getLink()
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

/* EOF: PDFX.php */