<?php
/**
 * Pages controller for CakePHP.
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    0.9.0
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */

App::uses('AppController', 'Controller');

/**
 * Pages controller (MVC component)
 *
 * Called upon to render static page content for this app.
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    Release: @package_version@
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
class PagesController extends AppController
{

	/**
	 * CakePHP: uses -- this controller does not use a model
	 *
	 * @var array
	 */
	public $uses = array();

	/**
	 * Displays a view
	 *
	 * The page to be displayed is read fro the $path
	 * 
	 * @return void
	 * @throws NotFoundException When the view file could not be found
	 *	       or MissingViewException in debug mode.
	 */
	public function display()
	{
		$path = func_get_args();
		$count = count($path);

		// no path arguments: root
		if (!$count) {
			return $this->redirect('/');
		}

		$page = $subpage = $title_for_layout = null;

		// use first and second path arguments, if available
		if (!empty($path[0])) {
			$page = $path[0];
		}

		if (!empty($path[1])) {
			$subpage = $path[1];
		}

		// humanize last path argument to use as a title
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		// catch errors for paths not found
		try {
			$this->render(implode('/', $path));
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}
}
