<?php


ScholarExtract\Extractor;

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

    public function getName()
    {
        return "A web service for extracting PDF to XML for scientific articles";
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

/* EOF: PDFX.php */