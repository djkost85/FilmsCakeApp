<?php
/**
 * Generating images for film statistics overviews.
 *
 * @package    FilmsCakeApp
 * @subpackage Image
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    0.9.0
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */

App::import('Vendor', 'PHPGraphLib', array('file' => 'PHPGraphLib.php'));

/**
 * StatsImageGenerator
 *
 * Generator of statistics images (PNG) for the FilmsCakeApp.
 *
 * @package    FilmsCakeApp
 * @subpackage Image
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    Release: @package_version@
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
class StatsImageGenerator
{
    /**
     * Passes on call to generate an image based on provided data
     * 
     * @param  string   $type       image type to create
     * @param  array    $data       data to use for creating the image
     * @param  int      $width      width of the image to generate
     * @param  int      $height     height of the image to generate
     * @return void                 (the graphwrite output directly to the browser)
     */
    public function generateImage($type, $data, $width, $height)
    {
        // check if $data is correct
        if (empty($data) || !is_array($data)) {
            // data is incorrect
            StatsImageGenerator::_generateEmptyImage();
            return;
        }

        switch ($type) {

            case 'years':
                StatsImageGenerator::_generateImageYears($data, "Films per Year", $width, $height);
                return;
                break;

            case 'months':
                StatsImageGenerator::_generateImageMonths($data, "Films per Month", $width, $height);
                return;
                break;

            // no default, intentional (fallback option is at function end)
        }

        // return an 'empty' warning image as a fallback option
        StatsImageGenerator::_generateEmptyImage();
    }


    /**
     * Generate an image displaying films per year
     * 
     * @param  array    $data       data to use for creating the image
     * @param  string   $title      the title to display above the image, if any
     * @param  int      $width      width of the image to generate
     * @param  int      $height     height of the image to generate
     * @return void                 (the graphwrite output directly to the browser)
     */
    private function _generateImageYears($data, $title = null, $width = 400, $height = 400)
    {
        // data[year]['film_count'] = films
        // data[year]['viewing_count'] = viewings

        // build image-class specific data arrays
        //  first array( display text => value)
        //  further arrays
        $tmpdata_view = array();
        $tmpdata_total = array();

        foreach ($data as $key => $year) {
            $tmpdata_total[$key] = $year['film_count'];
            $tmpdata_view[$key] = $year['viewing_count'];
        }

        //debug($tmpdata_total);
        //debug($tmpdata_view);

        // create the image
        $graph = new PHPGraphLib(400, 400);

        // set title, if present
        if (!empty($title)) {
            $graph->setTitle($title);
            $graph->setTitleLocation("left");
        }
        
        $graph->setLegend(true);
        $graph->setLegendTitle("Viewings", "Total");

        $graph->setGradient("green", "olive");
        $graph->setBarColor("white", "gray", "green");
        
        $graph->addData($tmpdata_view, $tmpdata_total);
        
        // createGraph outputs directly to browser
        $graph->createGraph();
    }


    /**
     * Generate an image displaying films per month
     * 
     * @param  array    $data       data to use for creating the image
     * @param  string   $title      the title to display above the image, if any
     * @param  int      $width      width of the image to generate
     * @param  int      $height     height of the image to generate
     * @return void                 (the graphwrite output directly to the browser)
     */
    private function _generateImageMonths($data, $title = null, $width = 1500, $height = 400)
    {
        // data[year-month]['film_count'] = films
        // data[year-month]['viewing_count'] = viewings

        // build image-class specific data arrays
        //  first array( display text => value)
        //  further arrays
        $tmpdata_view = array();
        $tmpdata_total = array();

        foreach ($data as $key => $month) {
            $tmpdata_total[$key] = $month['film_count'];
            $tmpdata_view[$key] = $month['viewing_count'];
        }

        // create the image
        $graph = new PHPGraphLib(1500, 400);

        // set title, if present
        if (!empty($title)) {
            $graph->setTitle($title);
            $graph->setTitleLocation("left");
        }
        
        $graph->setLegend(true);
        $graph->setLegendTitle("Viewings", "Total");

        $graph->setGradient("green", "olive");
        $graph->setBarColor("white", "gray", "green");
        
        $graph->addData($tmpdata_view, $tmpdata_total);
        
        // createGraph outputs directly to browser
        $graph->createGraph();
    }


    /**
     * Generate an 'empty' image
     * 
     * @return string   image content
     */
    private function _generateEmptyImage() 
    {
        $graph = new PHPGraphLib(200, 100);
        $graph->setTitle("no image");
        $graph->createGraph();
    }
}