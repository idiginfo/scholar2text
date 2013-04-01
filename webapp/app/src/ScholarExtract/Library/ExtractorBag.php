<?php

namespace ScholarExtract\Library;

use ScholarExtract\Extractor\ExtractorInterface;
use IteratorAggregate, Countable, ArrayIterator;
use LogicException;

/**
 * Extractor Bag
 */
class ExtractorBag implements IteratorAggregate, Countable
{
    private $objs;

    /**
     * @param ScholarExtract\Extractor\ExtractorInterface
     */
    private $default;

    // --------------------------------------------------------------
    
    public function __construct(array $extractors = array()) {
        $this->objs = array();
        $this->setAll($extractors);
    }

    // --------------------------------------------------------------

    public function add(ExtractorInterface $extractor)
    {
        $this->objs[$extractor::getName()] = $extractor;
    }

    // --------------------------------------------------------------

    public function setAll(array $extractors = array())
    {
        foreach($extractors as $extractor) {
            $this->add($extractor);
        }
    }

    // --------------------------------------------------------------

    public function get($extractorName)
    {
        return ($this->has($extractorName)) ? $this->objs[$extractorName] : null;
    }

    // --------------------------------------------------------------

    public function has($extractorName)
    {
        return isset($this->objs[$extractorName]);
    }

    // --------------------------------------------------------------

    public function remove($extractorName)
    {
        if ($this->has($extractorName)) {
            unset($this->objs[$extractorName]);
        }
    }

    // --------------------------------------------------------------

    /**
     * Returns an iterator for attributes.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new ArrayIterator($this->objs);
    }

    // --------------------------------------------------------------

    /**
     * Returns the number of extractors.
     *
     * @return int The number of extractors
     */
    public function count()
    {
        return count($this->objs);
    }    
}

/* EOF: ExtractorBag.php */