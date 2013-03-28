<?php

namespace ScholarExtract\Extractor;

/**
 * Extractor interface for different PDF extractors
 */
interface ExtractorInterface
{
    /**
     * @return string  A link to more information
     */
    abstract public function getLink();

    // --------------------------------------------------------------
    
    /**
     * @return string  A human-friendly name
     */
    abstract public function getName();

    // --------------------------------------------------------------
    
    /**
     * @return string  A description
     */
    abstract public function getDescription();

    // --------------------------------------------------------------
    
    /**
     * Extract information from PDF
     *
     * @param  string  $filepath  Full Filepath to PDF
     * @return string|boolean Serialized version of extracted data (false upon fail)
     */
    abstract public function extract($filepath);

}