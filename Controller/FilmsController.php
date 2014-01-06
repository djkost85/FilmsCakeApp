<?php
/**
 * Film controller for CakePHP.
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    0.9.0
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */

App::uses('ImdbRetrieve', 'Imdb');
App::uses('StatsImageGenerator', 'ImgGen');

/**
 * Film controller (MVC component)
 *
 * @package    FilmsCakeApp
 * @copyright  Copyright (c) 2013 Coen Zimmerman
 * @license    http://www.tabun.nl/LICENSE.TXT   Simplified BSD License
 * @version    Release: @package_version@
 * @link       http://www.tabun.nl/php/FilmsCakeApp
 */
class FilmsController extends AppController
{
    /**
     * CakePHP: $helpers
     *  
     * @var array
     */
    public $helpers = array('Html', 'Form');
    
    /**
     * CakePHP: $components
     *  
     * @var array
     */
    public $components = array('Paginator');

    /**
     * CakePHP: $uses
     *
     * @var  array
     */
    var $uses = array(
        'Film',
        'Director',
        'Actor',
        'Viewing',
        'Staticselect',
    );

    /**
     * CakePHP: Check if user can access this part of the site
     *
     * @param   array $user  data for user currently logged in, if any
     * @return  boolean      whether the user is authorized to access the page
     */
    public function isAuthorized($user = null)
    {
        // check Acl: anyone with viewing rights
        if ($this->Acl->check($user['role'], 'Films')) {
            return true;
        }

        return parent::isAuthorized($user);
    }

    /**
     * Action: search -- Show a list of films, apply search filter
     *
     * @return void
     */
    public function search() {

        // set the page we will redirect to
        $url['action'] = 'index';
        
        // build a URL will all the search elements in it
        foreach ($this->data as $k => $v) {
            foreach ($v as $kk => $vv) {
                $url[$k . '.' . $kk] = $vv;
            }
        }

        $this->redirect($url, null, true);
    }

    /**
     * Action: index -- Show a list of films
     *
     * @return void
     */
    public function index()
    {
        $this->Film->recursive = 2; // get associated models

        // make containable, to avoid getting too much data through actor roles
        $this->Film->Behaviors->attach('Containable');

        $this->Film->ActorRole->order = 'ActorRole.bill_order ASC';

        // paginate per 50, default sort by seen date
        $this->Paginator->settings = array(
            'order' => array(
                'Film.seen_last' => 'desc',
            ),
            'limit' => 50,
            'contain' => array(
                'Director',
                'Viewing.id',
                'ActorRole.role',
                'ActorRole.bill_order > 0',
                'ActorRole.Actor',
            ),
        );

        // search conditions for filter form
        // title
        if (!empty($this->passedArgs['Search.title'])) {
            // if there are no wildcards, place entire string between wildcards
            // else, replace the * for %
            if (preg_match('/\*/', $this->passedArgs['Search.title'])) {
                $this->Paginator->settings['conditions'][]['Film.title LIKE'] = str_replace('*', '%',
                    $this->passedArgs['Search.title']
                );
            } else {
                $this->Paginator->settings['conditions'][]['Film.title LIKE'] = '%'
                                                                              . $this->passedArgs['Search.title']
                                                                              . '%';
            }
            $this->request->data['Search']['title'] = $this->passedArgs['Search.title'];
        }

        // year
        // for some reason the same basic code produces Film.Search => array('year' => *) for this
        // whereas it produces Search.title => *!
        if (!empty($this->passedArgs['Film.Search']['year'])) {
            // if there are no wildcards, place entire string between wildcards
            // else, replace the * for %
            if (preg_match('/\*/', $this->passedArgs['Film.Search']['year'])) {
                $this->Paginator->settings['conditions'][]['Film.year LIKE'] = str_replace('*', '%',
                    $this->passedArgs['Film.Search']['year']
                );
            } else {
                $this->Paginator->settings['conditions'][]['Film.year LIKE'] = $this->passedArgs['Film.Search']['year'];
            }
            $this->request->data['Film']['Search']['year'] = $this->passedArgs['Film.Search']['year'];
        }
        
        //$this->set('films', $this->paginate());
        $this->set('films', $this->Paginator->paginate());

        // include viewingtypes for film list display
        $this->set('viewingTypes', $this->Staticselect->viewingTypesShort());
    }


    /**
     * Action: view -- Show the details of a single films
     *
     * @param   mixed $id       object id (usually integer) for the item being viewed
     * @return  void
     * @throws  NotFoundException if $id does not match an existing object
     */
    public function view($id = null)
    {
        $this->Film->id = $id;
        if (!$this->Film->exists()) {
            throw new NotFoundException(__('Invalid film'));
        }
        $this->set('film', $this->Film->read(null, $id));
    }


    /**
     * Action: add -- Add a new film to the system
     *
     * @return void
     * @see    /lib/Cake/Controller/Controller.php
     */
    public function add()
    {
        if ($this->request->is('post')) {
            
            $this->Film->create();
            if ($this->Film->saveAll($this->request->data)) {
                $this->Session->setFlash(__('The film has been saved'),
                    'default', array('class' => 'message success')
                );

                // after insertion, redirect to the IMDb status/data view page
                return $this->redirect(array('action' => 'imdb', $this->Film->id, false));
            }
            $this->Session->setFlash(
                __('The film could not be saved. Please, try again.'),
                'default', array('class' => 'message error')
            );
        }

        // form list options
        $this->set('viewingTypes', $this->Staticselect->viewingTypes(true));
        $this->set('filmStatuses', $this->Staticselect->filmStatuses());
        $this->set('imdbStatuses', $this->Staticselect->imdbStatuses());
    }

    /**
     * Action: edit -- Edit a film in the system
     *
     * @param  mixed $id        object id (usually integer) for the item being viewed
     * @return void
     * @throws NotFoundException if $id does not match an existing object
     * @see    /lib/Cake/Controller/Controller.php
     */
    public function edit($id = null)
    {
        $this->Film->recursive = 1; // get associated models
        $this->Film->id = $id;

        // nonexistant record: error
        if (!$this->Film->exists()) {
            throw new NotFoundException(__('Invalid film'));
            return;
        }

        // posted form: save and go back to the index
        if (    $this->request->is('post')
            ||  $this->request->is('put')
        ) {
            // don't add/update incomplete viewings -- but delete them
            for ($x = count($this->request->data['Viewing']) - 1; $x >= 0; $x--) {

                $tmpDate = date_parse($this->request->data['Viewing'][$x]['date']);

                // consider a viewing to be deletable if it has
                // no type set, or a date set that is not correct
                if (    empty($this->request->data['Viewing'][$x]['type'])
                    ||  (   !empty($this->request->data['Viewing'][$x]['date'])
                        &&  !checkdate($tmpDate['month'], $tmpDate['day'], $tmpDate['year'])
                    )
                ) {
                    // delete the viewing with that code
                    if (!empty($this->request->data['Viewing'][$x]['id'])) {
                        $this->Film->Viewing->delete($this->request->data['Viewing'][$x]['id']);
                    }

                    // and remove it from the data to saveall
                    unset($this->request->data['Viewing'][$x]);
                }
            }

            // debugging print
            //debug($this->request->data);

            if ( $this->Film->saveAll($this->request->data)) {
                $this->Session->setFlash(__('The film has been saved'),
                    'default', array('class' => 'message success')
                );
                //return $this->redirect( array('action' => 'index') );
            } else {
                $this->Session->setFlash(
                    __('The film could not be saved. Please try again.'),
                    'default', array('class' => 'message error')
                );
            }
        }

        // get current data for film (with associated models)
        $this->request->data = $this->Film->read(null, $id);
        $this->set('film', $this->Film->read(null, $id));

        // form list options
        $this->set('viewingTypes', $this->Staticselect->viewingTypes(true));
        $this->set('filmStatuses', $this->Staticselect->filmStatuses());
        $this->set('imdbStatuses', $this->Staticselect->imdbStatuses());
    }

    /**
     * Action: imdb -- Get IMDb data for a film
     *  
     * @param  mixed $id            the id code of the film to get/edit imdb data for
     * @param  boolean $retrieve    whether IMDb data should be retrieved from the website    
     * @return mixed                for redirects
     * @throws NotFoundException if $id does not match an existing object
     * @see    /lib/Cake/Controller/Controller.php
     */
    public function imdb($id = null, $retrieve = false)
    {
        //$this->Film->recursive = -1;
        $this->Film->id = $id;

        // nonexistant record: error
        if (!$this->Film->exists()) {
            throw new NotFoundException(__('Invalid film'));
            return $this->redirect( array('action' => 'index' ) );
        }

        // retrieve data? if not, just show/edit
        if ($retrieve) {
            // get current data for film (with associated models)
            $imdb = $this->Film->field('imdb');

            if (empty($imdb)) {
                $this->Session->setFlash(
                    __('No IMDb code specified for this film.'),
                    'default', array('class' => 'message error')
                );
                return $this->redirect(array('action' => 'edit', $id));
            }

            // start imdb lib function, get data from IMDb
            $imdbRetrieve = new ImdbRetrieve();
            $data = $imdbRetrieve->getData( $imdb );

            // error? set status
            if (!empty($data["error"])) {
                $this->Session->setFlash(
                    __('Error retrieving IMDb data: %s.', $data["error"]), 
                    'default', array('class' => 'message error')
                );

                // remember that we received an error, and thus don't have proper IMDb data for this film
                $this->Film->saveField('imdb_status', Configure::read('imdbStatuses.error'));
                return $this->redirect(array('action' => 'edit', $id));
            }

            // store CakePHP-savable data in $imdb_data array
            $imdb_data = array(
                'imdb_status' =>  Configure::read('imdbStatuses.fulldata'),
                'imdb_trivia' =>  '',
                'imdb_langs' =>   '',
                'imdb_color' =>   '',
                'imdb_genres' =>  '',
                'imdb_country' => '',
            );

            // store CakePHP savable data for associated models
            $imdb_data_assoc = array(
                'id' =>         $id,
                'Director' =>   array(),
                'ActorRole' =>  array(),
            );

            // imdb rating/grade
            if (!empty($data['rating'])) {
                $imdb_data['imdb_grade'] = $data['rating'];
            }

            // title, year
            if (!empty($data['title'])) {
                $imdb_data['imdb_title'] = $data['title'];
            }

            if (!empty($data['year'])) {
                $imdb_data['imdb_year'] = $data['year'];
            }

            // genres
            if (!empty($data['genres'])) {
                $imdb_data['imdb_genres'] = implode( ';', $data['genres'] );
            }

            // languages
            if (!empty($data['languages'])) {
                $imdb_data['imdb_langs'] = implode( ';', $data['languages'] );
            }

            // countries
            if (!empty($data['countries'])) {
                $imdb_data['imdb_country'] = implode( ';', $data['countries'] );
            }

            // color
            if (!empty($data['colors'])) {
                $imdb_data['imdb_color'] = implode( ';', $data['colors'] );
            }

            // partial data or not?
            if (empty($data['cast'])) {
                $imdb_data['imdb_status'] = Configure::read('imdbStatuses.partialdata');
            } else {
                // fix directors
                if (!empty($data['cast']['directors'])) {
                    foreach ($data['cast']['directors'] as $dir) {
                        // find the id code of the person
                        $dir_id = null;

                        $dir_found = $this->Director->find('first', array(
                            'conditions' => array('imdb_id' => $dir['id'])
                        ));

                        if (!empty($dir_found['Director']['id'])) {
                            $dir_id = $dir_found['Director']['id'];
                            // use older data only if new data not present
                            if (empty($dir['last_name']) || empty($dir['first_name'])) {
                                $dir['last_name'] = $dir_found['Director']['last_name'];
                                $dir['first_name'] = $dir_found['Director']['first_name'];
                            }
                        }

                        $imdb_data_assoc["Director"][] = array(
                            'id' =>         $dir_id,
                            'imdb_id' =>    $dir['id'],
                            'name' =>       $dir['name'],
                            'last_name' =>  $dir['last_name'],
                            'first_name' => $dir['first_name'],
                        );
                    }
                }

                //echo "<pre>";
                //print_r($data['cast']['actors']);
                //echo "</pre>";

                // fix actors
                if (!empty($data['cast']['actors'])) {
                    $actcnt = 0;
                    foreach ($data['cast']['actors'] as $act) {
                        // find the id code of the person
                        $act_id = null;
                        $act_found = $this->Actor->find('first', array(
                            'conditions' => array('imdb_id' => $act['id'])
                        ));

                        if (!empty($act_found['Actor']['id'])) {
                            $act_id = $act_found['Actor']['id'];
                        }

                        $imdb_data_assoc["ActorRole"][$actcnt] = array(
                            'Actor' => array(
                                'imdb_id' =>  $act['id'],
                                'name' =>     $act['name'],
                            ),
                            'role' =>         $act['role'],
                            'bill_order' =>   (!empty($act['bill_order'])) ? $act['bill_order'] : 0,
                        );

                        if ($act_id !== null) {
                            $imdb_data_assoc["ActorRole"][$actcnt]['Actor']['id'] = $act_id;
                        }

                        $actcnt++;
                    }
                }
            }

            // trivia
            if (!empty($data['trivia'])) {
                $imdb_data['imdb_trivia'] = implode("\n\n", array_values($data['trivia']));
            }

            // debug print
            //debug($imdb_data);
            //return;

            // save all relevant fields
            if (!$this->Film->save($imdb_data, false, array(
                    'imdb_status',
                    'imdb_title',
                    'imdb_year',
                    'imdb_grade',
                    'imdb_trivia',
                    'imdb_genres',
                    'imdb_color',
                    'imdb_langs',
                    'imdb_country',
                ))
            ) {
                $this->Session->setFlash(
                    __('The IMDb data for the film could not be saved.'),
                    'default', array('class' => 'message error')
                );
            }

            // delete all ActorRules first (prevents duplicate ActorRoles / errors)
            $this->Film->ActorRole->deleteAll(array('ActorRole.film_id' => $id), false);

            // save associated data (actors and directors)
            if (!$this->Film->saveAssociated($imdb_data_assoc, array('deep' => true))) {
                $this->Session->setFlash(
                    __('The directors/actors for the film could not be saved.'), 
                    'default', array('class' => 'message error')
                );
            }
            
            // set flash message according to data found
            if (!empty( $data['error_cast'])) {
                $this->Session->setFlash(
                    __('Error retrieving IMDb data for the cast: %s.', $data["error_cast"]),
                    'default', array('class' => 'message error')
                );
            } else if (!empty( $data['error_trivia'])) {
                $this->Session->setFlash(
                    __('Error retrieving IMDb data for the trivia: %s.', $data["error_trivia"]),
                    'default', array('class' => 'message error')
                );
            } else {
                $this->Session->setFlash(
                    __('The IMDb data for the film has been saved.'),
                    'default', array('class' => 'message success')
                );
            }

            // redirect to imdb data edit on success
            return $this->redirect(array('action' => 'imdb', $id, false));

        } else if (     $this->request->is('post')
                    ||  $this->request->is('put')
        ) {
            // edited manually
            
            // don't add/update incomplete directors -- but delete them
            for ($x = count($this->request->data['Director']) - 1; $x >= 0; $x--) {

                if (empty($this->request->data['Director'][$x]['last_name'])) {
                    // delete the object with that code
                    if (!empty($this->request->data['Director'][$x]['id'])) {
                        $this->Film->Director->delete($this->request->data['Director'][$x]['id']);
                    }

                    // and remove it from the data to saveall
                    unset($this->request->data['Director'][$x]);
                }
            }

            // save associated data (actors and directors)
            if (!$this->Film->saveAll($this->request->data)) {
                $this->Session->setFlash(
                    __('The IMDb data alterations for the film could not be saved.'), 
                    'default', array('class' => 'message error')
                );
            } else {
                $this->Session->setFlash(
                    __('The IMDb data for the film has been saved.'),
                    'default', array('class' => 'message success')
                );
            }
        }

        // test
        //print_r($data);

        // get film data for view
        $this->request->data = $this->Film->read(null, $id);
        $this->set('film', $this->Film->read(null, $id));

        // form list options
        $this->set('imdbStatuses', $this->Staticselect->imdbStatuses());
    }

    /**
     * Action: delete -- Delete a film from the system
     *
     * @param  mixed $id            the id code of the film to get/edit imdb data for
     * @return mixed                for redirects
     * @throws NotFoundException if $id does not match an existing object
     * @see    /lib/Cake/Controller/Controller.php
     * @todo   check directors, delete no-film-dirs
     */
    public function delete($id = null)
    {
        $this->request->onlyAllow('post'); // security measure, no GET data

        $this->Film->id = $id;
        if (!$this->Film->exists()) {
            throw new NotFoundException(__('Invalid film'));
        }
        
        // check directors, delete any that have no films left
        // TO DO

        if ($this->Film->delete()) {
            $this->Session->setFlash(__('Film deleted'), 'default', array('class' => 'message success'));
            return $this->redirect(array('action' => 'index'));
        }

        $this->Session->setFlash(__('Film was not deleted'), 'default', array('class' => 'message error'));
        return $this->redirect(array('action' => 'index'));
    }
    

    /**
     * Action: stats -- Show statistics for all films
     *
     * @return  void
     */
    public function stats()
    {
        $this->Film->recursive = 0;

        // get general statistics about movies
        // how many in total, viewings, etc
        $film_count = Hash::format(
            $this->Film->find('all', array(
                'fields'=> array('COUNT(Film.id) AS film_count'),
                'conditions' => array('Film.status' => Configure::read('filmStatuses.seen')),
                'recursive' => 0,
            )),
            array('{n}.{n}.film_count', 'film_count'),
            '%1$d'
        );

        // get general viewing-based stats
        $viewing_tmp = $this->Film->Viewing->find('all', array(
            'fields'=> array(
                'COUNT(Viewing.id) AS viewing_count',
                'SUM(Film.duration) AS total_duration',
            ),
            'conditions' => array(
                'Film.status' => Configure::read('filmStatuses.seen'),
            ),
            'recursive' => 1,
        ));

        $viewing_count = $viewing_tmp[0][0]['viewing_count'];
        $total_duration = $viewing_tmp[0][0]['total_duration'];

        // significant other data
        //  using Hash::format to strip one layer of array data
        $heleen_count = Hash::format(
            $this->Film->Viewing->find('all', array(
                'fields'=> array('COUNT(Viewing.id) AS viewing_count'),
                'conditions' => array(
                    'Film.status' => Configure::read('filmStatuses.seen'),
                    'Viewing.notes LIKE' => 'met Heleen%',
                ),
                'recursive' => 1,
            )),
            array('{n}.{n}.viewing_count'),
            '%1$d'
        );

        // get stats for viewing years
        //  first, find the full years with any data for them
        $viewing_years = $this->_getStatsYears();

        // genres
        $genres = array();
        $tmp_unsorted = array();
        $tmp_sorthelp = array();
        //  first collect all genres
        $genres_tmp = $this->_getGenres();
        
        //  then, get data per genre
        //      note: this seems to be off, the or conditions don't seem to be correct?
        foreach ($genres_tmp as $genre) {
            $tmp_genre = $this->Film->Viewing->find('all', array(
                'fields'=> array(
                    'COUNT(*) AS viewing_count',
                    'COUNT(DISTINCT Film.id) AS film_count',
                    'SUM(Film.duration) AS total_duration',
                ),
                'conditions' => array(
                    'Film.status' => Configure::read('filmStatuses.seen'),
                    array(
                        'or' => array(
                            array('imdb_genres LIKE' => '%' . $genre . ';%'),
                            array('imdb_genres LIKE' => '%' . $genre),
                        ),
                    )
                    
                ),
                'recursive' => 1,
            ));

            $tmp_genre[0][0]['genre'] = $genre;
            // store sorthelp with same key as the pre-sorted list
            $tmp_unsorted[] = $tmp_genre[0][0];
            $tmp_sorthelp[] = $tmp_genre[0][0]['viewing_count'];
        }

        // sort and build the final genres array
        arsort($tmp_sorthelp);
        foreach($tmp_sorthelp as $key => $value) {
            $genres[] = $tmp_unsorted[$key];
        }
        unset($genres_tmp, $genre, $tmp_genre);


        // languages
        //debug($this->_getLanguages());


        // countries
        $countries = array();
        $tmp_unsorted = array();
        $tmp_sorthelp = array();
        //  first collect all genres
        $countries_tmp = $this->_getCountries();
        
        //  then, get data per country
        //      note: this is not strictly safe, if text contains regexp symbols, it might cause problems
        foreach ($countries_tmp as $country) {
            $tmp_country = $this->Film->Viewing->find('all', array(
                'fields'=> array(
                    'COUNT(*) AS viewing_count',
                    'COUNT(DISTINCT Film.id) AS film_count',
                    'SUM(Film.duration) AS total_duration',
                ),
                'conditions' => array(
                    'Film.status' => Configure::read('filmStatuses.seen'),
                    array(
                        'or' => array(
                            array('imdb_country LIKE' => '%' . $country . ';%'),
                            array('imdb_country LIKE' => '%' . $country),
                        )
                    )  
                ),
                'recursive' => 1,
            ));

            $tmp_country[0][0]['country'] = $country;
            // store sorthelp with same key as the pre-sorted list
            $tmp_unsorted[] = $tmp_country[0][0];
            $tmp_sorthelp[] = $tmp_country[0][0]['viewing_count'];
        }

        // sort and build the final genres array
        arsort($tmp_sorthelp);
        foreach($tmp_sorthelp as $key => $value) {
            $countries[] = $tmp_unsorted[$key];
        }
        unset($countries_tmp, $country, $tmp_country);


        // combine view data into one array
        $data = array(
            'main' => array(
                'film_count' =>     $film_count[0],
                'viewing_count' =>  $viewing_count,
                'total_duration' => $total_duration,
                'heleen_count' =>   $heleen_count[0],
            ),
            'years' =>      $viewing_years,
            'genres' =>     $genres,
            'countries' =>  $countries,
        );
       
        $this->set('stats', $data);
    }


    /**
     * Action: statsImage -- Display an image based on current database content
     * 
     * @param  string $type         the image type to get (passed on to getStatsImage())
     * @return mixed                for redirects
     * @throws NotFoundException if $type does not match an existing type of image
     * @todo this should be cached; check file date, write file if required, serve file
     */
    public function statsImage($type = null) {

        $height = Configure::read('statsImage.height');

        // get relevant data for the image
        switch ($type) {
            case 'years':
                $data = $this->_getStatsYears(Configure::read('statsImage.limit.years'));
                $width = Configure::read('statsImage.width.years');
                break;

            case 'months':
                $data = $this->_getStatsMonths(Configure::read('statsImage.limit.months'));
                $width = Configure::read('statsImage.width.months');
                break;

            default:
                throw new NotFoundException(__('Invalid image type'));
                return;
        }

        // sort the data array from past to present
        asort($data);

        // get image content from generator
        $this->response->body(StatsImageGenerator::generateImage($type, $data, $width, $height));
        $this->response->type('png');

        return $this->response;
    }


    /**
     * Get a list of all the genres Films have in the imdb_genres column
     * 
     * @return array    array with all the genres
     */
    private function _getGenres() {

        // get all the genre-content
        $genres_tmp = $this->Film->find('all', array(
            'fields'=> array('imdb_genres'),
            'group' => 'imdb_genres',
            'recursive' => 0,
        ));
        
        //  second get unique genre list, by splitting up the ;-separated values
        $genres_tmp_separate = array();

        foreach ($genres_tmp as $genre) {

            if (empty($genre['Film']['imdb_genres'])) { continue; }

            $explode = explode(';', $genre['Film']['imdb_genres']);

            foreach ($explode as $part) {
                $genres_tmp_separate[] = trim($part);
            }
        }

        $genres_tmp_separate = array_unique($genres_tmp_separate);
        sort($genres_tmp_separate);

        return $genres_tmp_separate;
    }

    /**
     * Get a list of all the languages Films have in the imdb_langs column
     * 
     * @return array    array with all the languages
     */
    private function _getLanguages() {

        // get all the content
        $langs_tmp = $this->Film->find('all', array(
            'fields'=> array('imdb_langs'),
            'group' => 'imdb_langs',
            'recursive' => 0,
        ));
        
        //  second get unique list, by splitting up the ;-separated values
        $langs_tmp_separate = array();

        foreach ($langs_tmp as $lang) {

            if (empty($lang['Film']['imdb_langs'])) { continue; }

            $explode = explode(';', $lang['Film']['imdb_langs']);

            foreach ($explode as $part) {
                $langs_tmp_separate[] = trim($part);
            }
        }

        $langs_tmp_separate = array_unique($langs_tmp_separate);
        sort($langs_tmp_separate);

        return $langs_tmp_separate;
    }
    
    /**
     * Get a list of all the countries Films have in the imdb_country column
     * 
     * @return array    array with all the countries
     */
    private function _getCountries() {

        // get all the content
        $c_tmp = $this->Film->find('all', array(
            'fields'=> array('imdb_country'),
            'group' => 'imdb_country',
            'recursive' => 0,
        ));
        
        //  second get unique list, by splitting up the ;-separated values
        $c_separate = array();

        foreach ($c_tmp as $c) {

            if (empty($c['Film']['imdb_country'])) {
                continue;
            }

            $explode = explode(';', $c['Film']['imdb_country']);

            foreach ($explode as $part) {
                $c_separate[] = trim($part);
            }
        }

        $c_separate = array_unique($c_separate);
        sort($c_separate);

        return $c_separate;
    }


    /**
     * Collects statistics per year
     *
     * @param  int $limit   how many months to give stats on maximally
     * @return array    array with statistics per year
     */
    private function _getStatsYears($limit = null) {
        
        $data = array();
        
        // get all years that movies have been seen in
        $viewing_years_tmp = $this->Film->Viewing->find('all', array(
                'fields'=> array('YEAR(Viewing.date) AS dateyear', 'COUNT(Viewing.id) AS viewing_count'),
                'conditions' => array(
                    'Film.status' => Configure::read('filmStatuses.seen'),
                    'YEAR(Viewing.date) IS NOT NULL',
                ),
                'order' => 'dateyear DESC',
                'group' => 'dateyear',
                'recursive' => 1,
                'limit' => ($limit) ? $limit : null,
        ));

        // per year, get the actual stats
        foreach ($viewing_years_tmp as $key => $viewyear) {

            $data_key = $viewyear[0]['dateyear'];
            $data[$data_key] = $viewyear[0];

            // hours watched
            $tmp_year = $this->Film->Viewing->find('all', array(
                'fields'=> array(
                    //'COUNT(*) AS full_count',
                    'COUNT(DISTINCT Film.id) AS film_count',
                    'SUM(Film.duration) AS total_duration',
                ),
                'conditions' => array(
                    'Film.status' => Configure::read('filmStatuses.seen'),
                    'YEAR(Viewing.date)' => $data[$data_key]['dateyear'],
                ),
                'recursive' => 1,
            ));
            
            $data[$data_key] = am($data[$data_key], $tmp_year[0][0]);
        }
        
        return $data;
    }

    /**
     * Collects statistics per month
     *
     * @param  int $limit   how many months to give stats on maximally
     * @return array        array with statistics per year
     * @todo this is probably not a very efficient way to get a year-month dataset, redo later (look up months per year?)
     */
    private function _getStatsMonths($limit = null) {

        $data = array();
        
        // get all years that movies have been seen in
        $viewing_months_tmp = $this->Film->Viewing->find('all', array(
                'fields'=> array('DATE_FORMAT(Viewing.date, \'%Y-%m\') AS datemonth', 'COUNT(Viewing.id) AS viewing_count'),
                'conditions' => array(
                    'Film.status' => Configure::read('filmStatuses.seen'),
                    'DATE_FORMAT(Viewing.date, \'%Y-%m\') IS NOT NULL',
                ),
                'order' => 'datemonth DESC',
                'group' => 'datemonth',
                'recursive' => 1,
                'limit' => ($limit) ? $limit : null,
        ));

        // per year, get the actual stats
        foreach ($viewing_months_tmp as $key => $viewmonth) {
            $data_key = $viewmonth[0]['datemonth'];
            $data[$data_key] = $viewmonth[0];

            // hours watched
            $tmp_month = $this->Film->Viewing->find('all', array(
                'fields'=> array(
                    //'COUNT(*) AS full_count',
                    'COUNT(DISTINCT Film.id) AS film_count',
                    'SUM(Film.duration) AS total_duration',
                ),
                'conditions' => array(
                    'Film.status' => Configure::read('filmStatuses.seen'),
                    'DATE_FORMAT(Viewing.date, \'%Y-%m\')' => $data[$data_key]['datemonth'],
                ),
                'recursive' => 1,
            ));
            
            $data[$data_key] = am($data[$data_key], $tmp_month[0][0]);
        }
        
        return $data;
    }
}