<?php
/**
 * @file class_noticeBoard.php
 * @brief Contiene la definizione ed implementazione della classe Gino.App.NoticeBoard.noticeBoard
 * @copyright 2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti
 * @author abidibo
 */

/**
 * @namespace Gino.App.NoticeBoard
 * @description Namespace dell'applicazione NoticeBoard
 */
namespace Gino\App\NoticeBoard;

use \Gino\Loader;
use \Gino\View;

require_once('class.NoticeBoardCategory.php');
require_once('class.NoticeBoardDeliberative.php');
require_once('class.NoticeBoardItem.php');
require_once('class.NoticeBoardItemAttachment.php');

/**
 * @brief Classe di tipo Gino.Controller per la gestione di un albo pretorio
 * @version 0.1.0
 * @copyright 2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */
class noticeBoard extends \Gino\Controller
{

    // elementi per pagina
    private $_ifp;

    /**
     * @brief Costruttore
     *
     * @param $instance_id id istanza
     *
     * @return istanza di Gino.App.NoticeBoard.noticeBoard
     */
    public function __construct($instance_id)
    {
        parent::__construct($instance_id);

        $this->_ifp = 20;
    }

    /**
     * @brief Restituisce alcune proprietà della classe utili per la generazione di nuove istanze
     * @return lista delle proprietà utilizzate per la creazione di istanze di tipo events (tabelle, css, viste, folders)
     */
    public static function getClassElements() 
    {
        return array(
            "tables"=>array(
                'notice_board_category',
                'notice_board_item',
                'notice_board_item_category',
            ),
            "css"=>array(
                'noticeBoard.css',
            ),
            "views" => array(
                'archive.php' => _('Archivio'),
                'evidence.php' => _('In evidenza'),
                'detail.php' => _('Dettaglio'),
            ),
            "folderStructure"=>array (
                CONTENT_DIR.OS.'noticeBoard'=> null
            ),
        );
    }

    /**
     * @brief Metodo invocato quando viene eliminata un'istanza di tipo Gino.App.NoticeBoard.noticeBoard
     * Si esegue la cancellazione dei dati da db e l'eliminazione di file e directory 
     * @return TRUE
     */
    public function deleteInstance() 
    {
        $this->requirePerm('can_admin');

        /** eliminazione atti */
        NoticeBoardItem::deleteInstance($this);

        /** eliminazione categorie */
        NoticeBoardCategory::deleteInstance($this);

       /*
         * delete css files
         */
        $classElements = $this->getClassElements();
        foreach($classElements['css'] as $css) {
            unlink(APP_DIR.OS.$this->_class_name.OS.\Gino\baseFileName($css)."_".$this->_instance_name.".css");
        }

        /* eliminazione views */
        foreach($classElements['views'] as $k => $v) {
            unlink($this->_view_dir.OS.\Gino\baseFileName($k)."_".$this->_instance_name.".php");
        }

        /*
         * delete folder structure
         */
        foreach($classElements['folderStructure'] as $fld=>$fldStructure) {
            \Gino\deleteFileDir($fld.OS.$this->_instance_name, TRUE);
        }

        return TRUE;
    }

    /**
     * @brief Metodi pubblici disponibili per inserimento in layout (non presenti nel file events.ini) e menu (presenti nel file events.ini)
     * @return lista metodi NOME_METODO => array('label' => LABEL, 'permissions' = PERMISSIONS)
     */
    public static function outputFunctions() 
    {
        $list = array(
            "evidence" => array("label"=>_("In evidenza"), "permissions"=>array()),
            "archive" => array("label"=>_("Archivio"), "permissions"=>array()),
        );

        return $list;
    }

    /**
     * @brief Box 'in evidenza'
     *
     * @return html, atti in evidenza
     */
    public function evidence()
    {
        $this->_registry->addCss($this->_class_www.'/noticeBoard_'.$this->_instance_name.'.css');
        $request = \Gino\Http\Request::instance();

        $protocol_number = $act_number = $act_date = $object = null;
        $where_arr = array();
        $show_form = false;
        if(isset($request->POST['submit_search_items'])) {
            $protocol_number = \Gino\cleanVar($request->POST, 'protocol_number', 'string');
            $act_number = \Gino\cleanVar($request->POST, 'act_number', 'string');
            $act_date = \Gino\cleanVar($request->POST, 'act_date', 'string');
            $object = \Gino\cleanVar($request->POST, 'object', 'string');

            if($protocol_number) {
                $where_arr[] = "protocol_number='".$protocol_number."'";
                $show_form = TRUE;
            }
            if($act_number) {
                $where_arr[] = "act_number='".$act_number."'";
                $show_form = TRUE;
            }
            if($act_date) {
                $where_arr[] = "act_date='".$act_date."'";
                $show_form = TRUE;
            }
            if($object) {
                $where_arr[] = "object LIKE '%".$object."%'";
                $show_form = TRUE;
            }
        }

        $form = $this->evidenceForm($protocol_number, $act_date, $act_number, $object);

        $ctgs = array();
        $categories = NoticeBoardCategory::objects($this, array('where' => "instance='".$this->_instance."'", 'order' => 'name'));

        $today = date('Y-m-d');

        foreach($categories as $category) {
            $items = NoticeBoardItem::objects($this, array('where' => "instance='".$this->_instance."' AND category='".$category->id."' AND publication_date_begin <= '".$today."' AND publication_date_end >= '".$today."'".(count($where_arr) ? " AND ".implode(' AND ', $where_arr) : '')));
            if(count($items)) {
                $ctgs[] = array(
                    'ctg' => $category,
                    'items' => $items
                );
            }
        }

        $view = new View($this->_view_dir, 'evidence_'.$this->_instance_name);
        $dict = array(
            'categories' => $ctgs,
            'form_search' => $form,
            'show_form' => $show_form
        );

        return $view->render($dict);
    }

    /**
     * @brief Form box in evidenza
     *
     * @param string $protocol_number
     * @param string $act_date
     * @param string $act_number
     * @param string $object
     * @return html, form
     */
    public function evidenceForm($protocol_number, $act_date, $act_number, $object)
    {
        $gform = Loader::load('Form', array('search_item', 'post', ''));

        $form = $gform->open('', FALSE, '');
        $form .= $gform->cinput('protocol_number', 'text', $protocol_number , _('Numero protocollo'), array('size' => 8));
        $form .= $gform->cinput_date('act_date', $act_date , _('Data atto'), array());
        $form .= $gform->cinput('act_number', 'text', $act_number , _('Numero atto'), array('size' => 8));
        $form .= $gform->cinput('object', 'text', $object , _('Oggetto'), array('size' => 8));
        $form .= $gform->cinput('submit_search_items', 'submit', _('filtra'), '', array());
        $form .= $gform->close();

        return $form;

    }

    /**
     * @brief Dettaglio atto albo pretorio
     *
     * @param \Gino\Http\Request $request
     * @throws Gino.Exception.Exception404 se l'atto non viene trovato
     * @return Gino.Http.Response
     */
    public function detail(\Gino\Http\Request $request)
    {
        $this->_registry->addCss($this->_class_www.'/noticeBoard_'.$this->_instance_name.'.css');

        $id = \Gino\cleanVar($request->GET, 'id', 'int');
        $item = new NoticeBoardItem($id, $this);
        if(!$item->id) {
            throw new \Gino\Exception\Exception404();
        }
        $this->_registry->addCss($this->_class_www."/noticeBoard_".$this->_instance_name.".css");

        $view = new View($this->_view_dir, 'detail_'.$this->_instance_name);
        $dict = array(
            'item' => $item
        );

        $document = new \Gino\Document($view->render($dict));
        return $document();
    }

    /**
     * @brief Archivio albo pretorio
     *
     * @param \Gino\Http\Request $request
     * @return Gino.Http.Response
     */
    public function archive(\Gino\Http\Request $request)
    {
        $this->_registry->addCss($this->_class_www.'/noticeBoard_'.$this->_instance_name.'.css');

        $where = array(
            "instance='".$this->_instance."'"
        );
        $session = \Gino\Session::instance();
        $this->sessionSearch();

        $open_form = FALSE;
        if($session->{'noticeBoardSearch'.$this->_instance}['category']) {
            $where[] = "category='".$session->{'noticeBoardSearch'.$this->_instance}['category']."'";
            $open_form = true;
        }
        if($session->{'noticeBoardSearch'.$this->_instance}['protocol_number']) {
            $where[] = "protocol_number='".$session->{'noticeBoardSearch'.$this->_instance}['protocol_number']."'";
            $open_form = true;
        }
        if($session->{'noticeBoardSearch'.$this->_instance}['act_number']) {
            $where[] = "act_number='".$session->{'noticeBoardSearch'.$this->_instance}['act_number']."'";
            $open_form = true;
        }
        if($session->{'noticeBoardSearch'.$this->_instance}['act_date']) {
            $where[] = "act_date='".$session->{'noticeBoardSearch'.$this->_instance}['act_date']."'";
            $open_form = true;
        }
        if($session->{'noticeBoardSearch'.$this->_instance}['publication_year']) {
            $where[] = "publication_date_begin LIKE '".$session->{'noticeBoardSearch'.$this->_instance}['publication_date_begin']."-%'";
            $open_form = true;
        }
        if($session->{'noticeBoardSearch'.$this->_instance}['object']) {
            $where[] = "object LIKE '%".$session->{'noticeBoardSearch'.$this->_instance}['object']."%'";
            $open_form = true;
        }

        $items_number = NoticeBoardItem::getCount($this, array('where'=>implode(' AND ', $where)));

        $paginator = Loader::load('Paginator', array($items_number, $this->_ifp));
        $limit = $paginator->limitQuery();
        $items = NoticeBoardItem::objects($this, array('where' => implode(' AND ', $where), 'limit' => $limit, 'order' => 'publication_date_begin DESC'));

        $view = new View($this->_view_dir, 'archive_'.$this->_instance_name);
        $dict = array(
            'items' => $items,
            'form_search' => $this->archiveForm(),
            'show_form' => $open_form,
            'pagination' => $paginator->pagination()
        );

        $document = new \Gino\Document($view->render($dict));
        return $document();

    }

    /**
     * @brief Form di ricerca archivio
     *
     * @return html, form
     */
    public function archiveForm()
    {

        $session = \Gino\Session::instance();
        $gform = Loader::load('Form', array('search_item', 'post', ''));

        $form = $gform->open('', false, '');
        $form .= $gform->cselect('search_category', $session->{'noticeBoardSearch'.$this->_instance}['category'], NoticeBoardCategory::getForSelect($this), _('Categoria'), array());
        $form .= $gform->cinput('search_protocol_number', 'text', $session->{'noticeBoardSearch'.$this->_instance}['protocol_number'] , _('Numero protocollo'), array('size' => 8));
        $form .= $gform->cinput_date('search_act_date', $session->{'noticeBoardSearch'.$this->_instance}['act_date'] , _('Data atto'), array());
        $form .= $gform->cinput('search_act_number', 'text', $session->{'noticeBoardSearch'.$this->_instance}['act_number'] , _('Numero atto'), array('size' => 8));
        $form .= $gform->cinput('search_publication_year', 'text', $session->{'noticeBoardSearch'.$this->_instance}['publication_year'] , _('Anno di pubblicazione'), array('size' => 8));
        $form .= $gform->cinput('search_object', 'text', $session->{'noticeBoardSearch'.$this->_instance}['object'] , _('Oggetto'), array('size' => 8));
        $submit_all = $gform->input('submit_search_all', 'submit', _('tutti'), array('classField'=>'submit'));
        $form .= $gform->cinput('submit_search', 'submit', _('cerca'), '', array('classField'=>'submit', 'text_add'=>' '.$submit_all));
        $form .= $gform->close();

        return $form;

    }

    /**
     * @brief Setta in sessione i parametri di ricerca
     *
     * @param int $ctg_id
     * @param string $date_from
     * @param string $date_to
     * @return void
     */
    private function sessionSearch($ctg_id = null, $date_from = null, $date_to = null) {

        $request = $this->_registry->request;
        $session = $request->session;

        if(isset($request->POST['submit_search_all'])) {
            $search = null;
            $session->{'noticeBoardSearch'.$this->_instance} = $search;
        }

        if(!$session->{'noticeBoardSearch'.$this->_instance}) {
            $search = array(
                'category' => null,
                'protocol_number' => null,
                'act_number' => null,
                'act_date' => null,
                'publication_year' => null,
                'object' => null,
            );
        }

        if(isset($request->POST['submit_search'])) {
            if(isset($request->POST['search_category'])) { 
                $search['category'] = \Gino\cleanVar($request->POST, 'search_category', 'int');
            }
            if(isset($request->POST['search_protocol_number'])) { 
                $search['protocol_number'] = \Gino\cleanVar($request->POST, 'search_protocol_number', 'string');
            }
            if(isset($request->POST['search_act_number'])) { 
                $search['act_number'] = \Gino\cleanVar($request->POST, 'search_act_number', 'string');
            }
            if(isset($request->POST['search_act_date'])) { 
                $search['act_date'] = \Gino\cleanVar($request->POST, 'search_act_date', 'string');
            }
            if(isset($request->POST['search_publication_year'])) { 
                $search['publication_year'] = \Gino\cleanVar($request->POST, 'search_publication_year', 'string');
            }
            if(isset($request->POST['search_object'])) { 
                $search['object'] = \Gino\cleanVar($request->POST, 'search_object', 'string');
            }
            $session->{'noticeBoardSearch'.$this->_instance} = $search;
        }

    }

    /**
     * @brief Download documento
     *
     * @param \Gino\Http\Request $request
     * @throws Gino.Exception.Exception404 se il documento non viene trovato
     * @return Gino.Http.ResponseFile
     */
    public function download(\Gino\Http\Request $request)
    {
        $id = \Gino\cleanVar($request->GET, 'id', 'int');
        $attachment = new NoticeBoardItemAttachment($id, $this);
        if(!$attachment->id) {
            throw new \Gino\Exception\Exception404();
        }

        return \Gino\download($this->getBaseAbsPath().OS.$attachment->attachment);
    }

    /**
     * @brief Interfaccia di amministrazione modulo
     *
     * @param \Gino\Http\Request $request
     * @return Gino.Http.Response
     */
    public function manageDoc(\Gino\Http\Request $request)
    {
        $this->requirePerm('can_admin');

        $block = \Gino\cleanVar($request->GET, 'block', 'string');


        $link_frontend = sprintf('<a href="%s">%s</a>', $this->linkAdmin(array(), 'block=frontend'), _('Frontend'));
        $link_ctg = sprintf('<a href="%s">%s</a>', $this->linkAdmin(array(), 'block=category'), _('Categorie'));
        $link_deliberative = sprintf('<a href="%s">%s</a>', $this->linkAdmin(array(), 'block=deliberative'), _('Organi deliberanti'));
        $link_dft = sprintf('<a href="%s">%s</a>', $this->linkAdmin(), _('Atti'));
        $sel_link = $link_dft;

        if($block == 'frontend' && $this->userHasPerm('can_admin')) {
            $backend = $this->manageFrontend();
            $sel_link = $link_frontend;
        }
        elseif($block=='category') {
            $backend = $this->manageNoticeBoardCategory();
            $sel_link = $link_ctg;
        }
        elseif($block=='deliberative') {
            $backend = $this->manageNoticeBoardDeliberative();
            $sel_link = $link_deliberative;
        }
        else {
            $backend = $this->manageNoticeBoardItem();
        }

        if(is_a($backend, '\Gino\Http\Response')) {
            return $backend;
        }

        // groups privileges
        $links_array = array($link_frontend, $link_deliberative, $link_ctg, $link_dft);

        $view = new view(null, 'tab');
        $dict = array(
          'title' => _('Gestione documenti'),
          'links' => $links_array,
          'selected_link' => $sel_link,
          'content' => $backend
        );

        $document = new \Gino\Document($view->render($dict));
        return $document();
    }

    /**
     * @brief Interfaccia di amministrazione NoticeBoardDeliberative
     *
     * @return \Gino\Http\Response oppure html, interfaccia di amministrazione
     */
    public function manageNoticeBoardDeliberative()
    {
        $admin_table = Loader::load('AdminTable', array($this, array()));

        $backend = $admin_table->backoffice(
            'NoticeBoardDeliberative',
            array(), // display options
            array(), // form options
            array()  // fields options
        );

        return $backend;
    }

    /**
     * @brief Interfaccia di amministrazione NoticeBoardCategory
     *
     * @return \Gino\Http\Response oppure html, interfaccia di amministrazione
     */
    public function manageNoticeBoardCategory()
    {
        $admin_table = Loader::load('AdminTable', array($this, array()));

        $backend = $admin_table->backoffice(
            'NoticeBoardCategory',
            array(), // display options
            array(), // form options
            array()  // fields options
        );

        return $backend;
    }

    /**
     * @brief Interfaccia di amministrazione NoticeBoardItem
     *
     * @return \Gino\Http\Response oppure html, interfaccia di amministrazione
     */
    public function manageNoticeBoardItem()
    {
        $admin_table = Loader::load('AdminTable', array($this, array()));

        $backend = $admin_table->backoffice(
            'NoticeBoardItem',
            array(
                'list_display' => array('category', 'object', 'insertion_date', 'act_number', 'publication_date_begin', 'publication_date_end'),
                'filter_fields' => array('object', 'act_number', 'category')
            ), // display options
            array(
                'f_upload' => true,
                'removeFields' => array('filesize') // remove here and from item m2mt declaration
            ), // form options
            array()  // fields options
        );

        return $backend;
    }

}
