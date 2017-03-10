<?php

namespace Classes;

use XMLWriter;

/**
 * Class DataSaver
 * @package Classes
 */
class DataSaver
{

    /**
     * Max sum of iteration before flush XML in memory
     */
    const ITERATION_MAX = 1000;

    /**
     * @var XMLWriter
     */
    private $xmlWriter;

    /**
     * @var string
     */
    private $version;
    /**
     * @var string
     */
    private $encoding;

    /**
     * @var
     */
    private $filePath;

    /**
     * @var bool
     */
    private $indent;
    /**
     * @var int
     */
    private $iteration;

    /**
     * DataSaver constructor.
     * @param $filePath
     * @param string $version
     * @param string $encoding
     * @param bool $indent
     */
    public function __construct($filePath, $version = '1.0', $encoding = 'UTF-8', $indent = false)
    {
        $this->setXmlWriter(new XMLWriter())
            ->setFilePath($filePath)
            ->setVersion($version)
            ->setEncoding($encoding)
            ->setIndent($indent)
            ->resetIteration();
    }

    /**
     * @param $rootNode
     */
    public function startDocument($rootNode)
    {
        $this->getXmlWriter()->openMemory();
        $this->getXmlWriter()->setIndent($this->getIndent());
        $this->getXmlWriter()->startDocument($this->getVersion(), $this->getEncoding());
        $this->getXmlWriter()->startElement($rootNode);
    }

    /**
     * @param $nodeName
     * @param $nodeData
     */
    public function addNode($nodeName, $nodeData)
    {
        if (is_array($nodeData)) {
            $this->createNode([$nodeName => $nodeData]);
        }
        // Flush XML in memory to file every ITERATION_MAX iterations
        if ($this->getIteration() % self::ITERATION_MAX == 0) {
            $this->writeDocument();
        }
    }

    /**
     *
     */
    public function finishDocument()
    {
        $this->getXmlWriter()->endElement();
        $this->writeDocument();
    }

    /**
     * @param array $nodeData
     */
    private function createNode(array $nodeData)
    {
        foreach ($nodeData as $key => $val) {
            if (is_numeric($key)) {
                $key = 'key' . $key;
            }
            if (is_array($val)) {
                $this->getXmlWriter()->startElement($key);
                $this->createNode($val);
                $this->getXmlWriter()->endElement();
            } else {
                $this->getXmlWriter()->writeElement($key, $val);
                $this->incrementIteration();
            }
        }
    }

    /**
     *
     */
    private function writeDocument()
    {
        file_put_contents($this->getFilePath(), $this->getXmlWriter()->flush(true), FILE_APPEND);
    }

    /**
     * @return XMLWriter
     */
    private function getXmlWriter()
    {
        return $this->xmlWriter;
    }

    /**
     * @param XMLWriter $xmlWriter
     * @return DataSaver
     */
    private function setXmlWriter(XMLWriter $xmlWriter)
    {
        $this->xmlWriter = $xmlWriter;
        return $this;
    }

    /**
     * @return string
     */
    private function getFilePath()
    {
        return (string) $this->filePath;
    }

    /**
     * @param string $filePath
     * @return DataSaver
     */
    private function setFilePath($filePath)
    {
        if(!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }
        $this->filePath = $filePath;
        return $this;
    }

    /**
     * @return boolean
     */
    private function getIndent()
    {
        return (bool)$this->indent;
    }

    /**
     * @param boolean $indent
     * @return DataSaver
     */
    private function setIndent($indent)
    {
        $this->indent = $indent;
        return $this;
    }

    /**
     * @return string
     */
    private function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return DataSaver
     */
    private function setVersion($version)
    {
        $this->version = (string)$version;
        return $this;
    }

    /**
     * @return string
     */
    private function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     * @return DataSaver
     */
    private function setEncoding($encoding)
    {
        $this->encoding = (string)$encoding;
        return $this;
    }

    /**
     * @return int
     */
    private function getIteration()
    {
        return (int)$this->iteration;
    }

    /**
     * @param int $iteration
     * @return DataSaver
     */
    private function setIteration($iteration)
    {
        $this->iteration = $iteration;
        return $this;
    }

    /**
     * @return DataSaver
     */
    private function resetIteration()
    {
        $this->iteration = 0;
        return $this;
    }

    /**
     * @return DataSaver
     */
    private function incrementIteration()
    {
        $this->setIteration($this->getIteration() + 1);
        return $this;
    }

}