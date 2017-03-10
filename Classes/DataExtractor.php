<?php

namespace Classes;

use Exception;
use SimpleXMLElement;

/**
 * Class DataExtractor
 * @package Classes
 */
class DataExtractor
{

    /**
     * Path to file
     * @var
     */
    private $filePath;

    /**
     * Opened XML file
     * @var
     */
    private $handle;

    /**
     * Chunk size to read
     * @var
     */
    private $chunkSize;

    /**
     * DataExtractor constructor.
     * @param $filePath
     * @param int $chunkSize
     */
    public function __construct($filePath, $chunkSize = 1024)
    {
        $this->setFilePath($filePath)->setHandle()->setChunkSize($chunkSize);
    }

    /**
     * DataExtractor destructor.
     */
    public function __destruct()
    {
        if ($this->getHandle()) {
            fclose($this->getHandle());
        }
    }

    /**
     * Get node from file
     * @param string $nodeElement
     * @param callable $callback
     */
    public function getNode($nodeElement, callable $callback = null)
    {
        $startNode = '<' . $nodeElement . '>';
        $endNode = '</' . $nodeElement . '>';
        $cursorPosition = 0;
        while (true) {
            $startPosition = $this->getPosition($startNode, $cursorPosition);
            if ($startPosition === false) {
                break;
            }
            $endPosition = $this->getPosition($endNode, $startPosition) + mb_strlen($endNode);
            fseek($this->getHandle(), $startPosition);
            $data = fread($this->getHandle(), ($endPosition - $startPosition));
            if ($callback) {
                $callback($data);
            }
            $cursorPosition = ftell($this->getHandle());
        }
    }

    /**
     * Get position of string
     * @param string $string
     * @param int $startFrom
     * @return bool|int
     */
    private function getPosition($string, $startFrom = 0)
    {
        fseek($this->getHandle(), $startFrom, SEEK_SET);
        $data = fread($this->getHandle(), $this->getChunkSize());
        $stringPos = mb_strpos($data, $string);
        if ($stringPos !== false) {
            return $stringPos + $startFrom;
        }
        if (feof($this->getHandle())) {
            return false;
        }
        return $this->getPosition($string, $this->getChunkSize() + $startFrom);
    }

    /**
     * Get array from XML string
     * @param string $nodeAsString
     * @return array
     */
    public function getArrayFromXMLString($nodeAsString)
    {
        $simpleXML = simplexml_load_string($nodeAsString);
        if (libxml_get_errors()) {
            user_error('Libxml throws some errors.', implode(',', libxml_get_errors()));
        }
        return $this->xmlToArray($simpleXML);
    }

    /**
     * Turns a SimpleXMLElement into an array
     * @param SimpleXMLElement $xml
     * @return array
     */
    private function xmlToArray($xml)
    {
        if (is_object($xml) && get_class($xml) == 'SimpleXMLElement') {
            $attributes = $xml->attributes();
            foreach ($attributes as $k => $v) {
                $a[$k] = (string)$v;
            }
            $x = $xml;
            $xml = get_object_vars($xml);
        }

        if (is_array($xml)) {
            if (count($xml) == 0) {
                return (string) $x;
            }
            $r = array();
            foreach ($xml as $key => $value) {
                $r[$key] = $this->xmlToArray($value);
            }
            // Ignore attributes
            if (isset($a)) {
                $r['@attributes'] = $a;
            }
            return $r;
        }
        return (string)$xml;
    }

    /**
     * @return resource
     */
    private function getHandle()
    {
        return $this->handle;
    }

    /**
     * Open file for reading
     * @return $this
     * @throws Exception
     */
    private function setHandle()
    {
        $handle = fopen($this->getFilePath(), 'r');
        if (!$handle) {
            throw new Exception('Error opening file for reading');
        }
        $this->handle = $handle;
        return $this;
    }

    /**
     * @return string
     */
    private function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @throws Exception
     * @param string $filePath
     * @return DataExtractor
     */
    private function setFilePath($filePath)
    {
        $filePath = realpath($filePath);
        if (!file_exists($filePath)) {
            throw new Exception('Cannot load file: ' . $filePath);
        }
        $this->filePath = (string) $filePath;
        return $this;
    }

    /**
     * @return int
     */
    private function getChunkSize()
    {
        return $this->chunkSize;
    }

    /**
     * @param int $chunkSize
     * @return DataExtractor
     */
    private function setChunkSize($chunkSize)
    {
        $this->chunkSize = (int) $chunkSize;
        return $this;
    }

}