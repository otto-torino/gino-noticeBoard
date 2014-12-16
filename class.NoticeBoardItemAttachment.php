<?php
/**
 * @file class.NoticeBoardItemAttachment.php
 * @brief Contiene la definizione e l'implementazione della classe Gino.App.NoticeBoard.NoticeBoardItemAttachment
 * @copyright 2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */

namespace Gino\App\NoticeBoard;

use \Gino\FileField;

/**
 * @brief Classe di tipo Gino.Model che rappresenta un allegato ad un atto
 * @version 0.1.0
 * @copyright 2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */
class NoticeBoardItemAttachment extends \Gino\Model {

    public static $table = 'notice_board_item_attachment';
    private static $_extension_file = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'odt', 'txt', 'zip', 'rar', 'png', 'jpg', 'bmp', 'wav', 'mp3', '3gp');

    /**
     * @brief Costruttore
     *
     * @param int $id id record
     * @param \Gino\App\NoticeBoard\noticeBoard $instance
     * @return istanza di Gino.App.NoticeBoard.NoticeBoardItemAttachment
     */
    function __construct($id, $controller) {

        $this->_controller = $controller;

        $this->_model_label = _('Allegato');

        $this->_tbl_data = self::$table;
        $this->_fields_label = array(
            'noticeboarditem_id' => _('Elemento Albo Pretorio'),
            'noticeboardcategory_id' => _('Categoria'),
            'attachment' => array(_('Allegato'), _('Estensioni permesse: ').implode(',', self::$_extension_file)),
            'filesize' => _('Dimensioni'),
            'description' => _('Descrizione')
        );
        parent::__construct($id);

    }

    /**
     * @brief Rappresentazione a stringa dell'oggetto
     *
     * @return descrizione allegato
     */
    function __toString() {
        return $this->description;
    }

    /**
     * @brief Definizione della struttura del modello
     *
     * @see Gino.Model::structure()
     * @param $id id dell'istanza
     * @return array, struttura del modello
     */
    public function structure($id) {

        $structure = parent::structure($id);

        $base_path = $this->_controller->getBaseAbsPath();
        $structure['attachment'] = new FileField(array(
            'name'=>'attachment',
            'model'=>$this,
            'extensions'=>self::$_extension_file,
            'path'=>$base_path,
            'required'=>TRUE,
            'check_type'=>FALSE,
            'filesize_field' => 'filesize'
        ));

        $structure['filesize']->setWidget('hidden');
        $structure['noticeboarditem_id']->setWidget('hidden');
        $structure['noticeboardcategory_id']->setWidget('hidden');

        return $structure;

    }

    /**
     * @brief Estensione allegato
     *
     * @return estensione
     */
    public function extension()
    {
        $path_parts = pathinfo($this->attachment);
        $extension = strtolower($path_parts["extension"]);

        return $extension;
    }

    /**
     * @brief Download url
     *
     * @return url
     */
    public function downloadUrl()
    {
        return $this->_controller->link($this->_controller->getInstanceName(), 'download', array('id' => $this->id));
    }

}
