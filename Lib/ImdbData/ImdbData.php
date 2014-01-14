<?php
/**
 * ImdbData retrieval tools
 *
 * @package     ImdbData
 * @copyright   Copyright (c) 2013 Coen Zimmerman
 * @license     http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version     Release: @package_version@
 * @link        http://www.github.com/CJZimmerman/ImdbData
 */

// cakephp doesn't support namespaces
//namespace ImdbData;
//use ImdbData\RawWebData\RawWebDataInterface;
//use ImdbData\ImdbDataInterpreter\ImdbDataInterpreterInterface;

// cakephp including
App::uses('RawWebDataInterface', 'ImdbData/RawWebData');
App::uses('ImdbDataInterpreterInterface', 'ImdbData/ImdbDataInterpreter');

/** 
 * Retrieves and Provides data for a movie with a given IMDb Code
 * Created by ImdbDataFactory
 * 
 * @package     ImdbData
 */
class ImdbData
{
    /**
     * @var string
     */
    private $_imdbCode;

    /**
     * @var RawWebDataInterface
     */
    private $_rawData;
    /**
     * @var ImdbDataInterpreterInterface
     */
    private $_interpreter;

    /**
     * @var string
     */
    public $error;

    /**
     * @var array
     */
    private $_data = array(
        'imdb_code' =>      null,
        'title' =>          null,
        'year' =>           null,
        'rating' =>         null,
        'duration' =>       null,
        
        'directors' =>      array(),
        'writers' =>        array(),
        'cast' =>           array(),
        'trivia' =>         array(),

        'genres' =>         array(),
        'countries' =>      array(),
        'languages' =>      array(),
        'colors' =>         array(),
    );                                         // array with organized imdb data

    /**
     * @param string                       $imdbCode
     * @param RawWebDataInterface          $rawData 
     * @param ImdbDataInterpreterInterface $interpreter
     */
    public function __construct($imdbCode, RawWebDataInterface $rawData, ImdbDataInterpreterInterface $interpreter)
    {
        $this->_imdbCode = $imdbCode;
        $this->_rawData = $rawData;
        $this->_interpreter = $interpreter;

        $this->_data['imdb_code'] = $imdbCode;
    }

    /**
     * @return boolean      whether any data was correctly retrieved
     */
    public function getAndInterpretData()
    {
        $this->error = false;

        $data = $this->_rawData->getData($this->_imdbCode);

        if (empty($data)) {
            $this->error = "No data retrieved.";
            return false;
        }

        $this->_data = $this->_interpreter->interpretData($data);

        if (empty($this->_data) || !is_array($this->_data)) {
            $this->error = "Data could not be interpreted.";
            return false;
        }
        
        return true;
    }

    /**
     * @param  string $type     data type to retrieve, should be an index in the _data array
     * @return mixed            data property (string or array), or null if property not found
     */
    public function getProperty($type)
    {
        if (is_array($this->_data) && !empty($this->_data[$type])) {
            return $this->_data[$type];
        } else {
            return null;
        }
    }

    /**
     * @return array
     */
    public function getAllData()
    {
        return $this->_data;
    }
}