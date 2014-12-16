<?php
/**
 * @file class.NoticeBoardItem.php
 * @brief Contiene la definizione e l'implementazione della classe Gino.App.NoticeBoard.NoticeBoardItem
 * @copyright 2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */

namespace Gino\App\NoticeBoard;

use \Gino\Db;
use \Gino\ForeignKeyField;
use \Gino\DatetimeField;
use \Gino\ManyToManyThroughField;

/**
 * @brief Classe di tipo Gino.Model che rappresenta un atto dell'albo pretorio
 * @version 0.1.0
 * @copyright 2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */
class NoticeBoardItem extends \Gino\Model
{
    public static $table = 'notice_board_item';
    private static $_extension_file = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'odt', 'txt', 'zip', 'rar', 'png', 'jpg', 'bmp');

    /**
     * @brief Costruttore
     *
     * @param int $id id record
     * @param \Gino\App\NoticeBoard\noticeBoard $instance
     * @return istanza di Gino.App.NoticeBoard.NoticeBoardItem
     */
    public function __construct($id, $instance)
    {
        $this->_controller = $instance;
        $this->_tbl_data = self::$table;

        $this->_fields_label = array(
            'category' => _('Categoria'),
            'deliberative' => _('Organo deliberante'),
            'protocol_number' => _('Numero protocollo'),
            'act_date' => _('Data atto'),
            'act_number' => _('Numero atto'),
            'publication_date_begin' => _('Inizio pubblicazione'),
            'publication_date_end' => _('Fine pubblicazione'),
            'object' => _('Oggetto'),
            'notes' => _('Note'),
            'insertion_date' => _('Data inserimento'),
            'attachments' => _('Allegati'),
        );

        parent::__construct($id);

        $this->_model_label = _('Atto');
    }

    /**
     * @brief Rappresentazione a stringa dell'oggetto
     *
     * @return oggetto atto
     */
    function __toString()
    {
        return (string) $this->object;
    }

    /**
     * @brief Definizione della struttura del modello
     *
     * @see Gino.Model::structure()
     * @param $id id dell'istanza
     * @return array, struttura del modello
     */
    public function structure($id)
    {
        $structure = parent::structure($id);

        $structure['category'] = new ForeignKeyField(array(
            'name'=>'category',
            'model'=>$this,
            'foreign'=>'\Gino\App\NoticeBoard\NoticeBoardCategory',
            'foreign_where'=>"instance='".$this->_controller->getInstance()."'",
            'foreign_order'=>'name',
            'foreign_controller'=>$this->_controller,
            'add_related' => TRUE,
            'add_related_url' => $this->_controller->linkAdmin(array(), 'block=category&insert=1')
        ));

        $structure['deliberative'] = new ForeignKeyField(array(
            'name'=>'deliberative',
            'model'=>$this,
            'foreign'=>'\Gino\App\NoticeBoard\NoticeBoardDeliberative',
            'foreign_where'=>"instance='".$this->_controller->getInstance()."'",
            'foreign_order'=>'name',
            'foreign_controller'=>$this->_controller,
            'add_related' => TRUE,
            'add_related_url' => $this->_controller->linkAdmin(array(), 'block=deliberative&insert=1')
        ));

        $structure['insertion_date'] = new DatetimeField(array(
            'name'=>'insertion_date',
            'model'=>$this,
            'auto_now'=>FALSE,
            'auto_now_add'=>TRUE,
        ));

        $structure['attachments'] = new ManyToManyThroughField(array(
            'name'=>'attachments',
            'model'=>$this,
            'm2m'=>'\Gino\App\NoticeBoard\NoticeBoardItemAttachment',
            'm2m_controller'=>$this->_controller,
            'controller'=>$this->_controller,
            'remove_fields' => array('filesize')
        ));

        return $structure;
    }

    /**
     * @brief Numero atti
     * @param \Gino\App\NoticeBoard\noticeBoard $controller
     * @param array $options array associativo di opzioni (where clause)
     * @return numero atti
     */
    public static function getCount($controller, $options = null) {

        $res =0;

        $where = \Gino\gOpt('where', $options, '');

        $db = Db::instance();
        return $db->getNumRecords(self::$table, $where);

    }

    /**
     * @brief Url dettaglio atto
     *
     * @return url
     */
    public function getUrl()
    {
        return $this->_controller->link($this->_controller->getInstanceName(), 'detail', array('id' => $this->id));
    }

}
