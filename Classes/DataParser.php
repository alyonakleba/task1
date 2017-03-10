<?php
namespace Classes;

use Classes\DataExtractor;
use Classes\DataSaver;

/**
 * Class DataParser
 * @package Classes
 */
class DataParser
{

    /**
     * @var
     */
    private $dataExtractor;
    /**
     * @var
     */
    private $dataSaver;
    /**
     * @var
     */
    private $options;

    /**
     * @var
     */
    private $config;

    /**
     * DataParser constructor.
     * @param array $config
     * @param array $options
     */
    public function __construct($config = array(), $options = array())
    {
        $this->config = $config;
        $this->setOptions($options);
        $fileName = basename($this->getOptions()['path']);
        $dataExtractor = new DataExtractor($this->getOptions()['path']);
        $dataSaver = new DataSaver($config['outputFilePath'].$fileName);
        $this->setDataExtractor($dataExtractor)->setDataSaver($dataSaver);
    }

    /**
     * Xml files parser
     */
    public function parseXml()
    {
        $rootNodeName = $this->config['rootNodeName'];
        $nodeName = $this->config['nodeName'];
        $this->getDataSaver()->startDocument($rootNodeName);

        $this->getDataExtractor()->getNode($nodeName, function ($node) use ($nodeName) {

            $criteria = $this->getOptions()['criteria'];
            $to = $this->getOptions()['to'];
            $from = $this->getOptions()['from'];

            // Transform the XMLString into an array and
            $nodeArray = $this->getDataExtractor()->getArrayFromXMLString($node);
            if (isset($nodeArray[$criteria]) && ($nodeArray[$criteria] >= $from && $nodeArray[$criteria] <= $to)) {
                $this->getDataSaver()->addNode($nodeName, $nodeArray);
            }
        });

        $this->getDataSaver()->finishDocument();
    }

    /**
     * @return DataExtractor
     */
    public function getDataExtractor()
    {
        return $this->dataExtractor;
    }

    /**
     * @param DataExtractor $dataExtractor
     * @return DataParser
     */
    public function setDataExtractor(DataExtractor $dataExtractor)
    {
        $this->dataExtractor = $dataExtractor;
        return $this;
    }

    /**
     * @return DataSaver
     */
    public function getDataSaver()
    {
        return $this->dataSaver;
    }

    /**
     * @param DataSaver $dataSaver
     * @return DataParser
     */
    public function setDataSaver(DataSaver $dataSaver)
    {
        $this->dataSaver = $dataSaver;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return DataParser
     */
    public function setOptions($options)
    {
        $this->options = (array)$options;
        return $this;
    }

}
