<?php
/**
 * @file class.NoticeBoardCategory.php
 * @brief Contiene la definizione e l'implementazione della classe Gino.App.NoticeBoard.NoticeBoardCategory
 * @copyright 2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */

namespace Gino\App\NoticeBoard;

/**
 * @brief Classe di tipo Gino.Model che rappresenta una categoria di documenti
 * @version 0.1.0
 * @copyright 2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */
class NoticeBoardCategory extends \Gino\Model
{
    public static $table = 'notice_board_category';

    /**
     * @brief Costruttore
     *
     * @param int $id id record
     * @param \Gino\App\NoticeBoard\noticeBoard $instance
     * @return istanza di Gino.App.NoticeBoard.NoticeBoardCategory
     */
    public function __construct($id, $instance)
    {
        $this->_controller = $instance;
        $this->_tbl_data = self::$table;

        $this->_fields_label = array(
            'name' => _('Nome'),
            'color' => array(_('Colore'), _('Inserire il codice esadecimale, es. ff0000')),
        );

        parent::__construct($id);

        $this->_model_label = _('Categoria');
    }

    /**
     * @brief Rappresentazione a stringa dell'oggetto
     *
     * @return nome categoria
     */
    function __toString()
    {
        return (string) $this->name;
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

        return $structure;
    }

    /**
     * @brief Array associativo id => nome delle categorie
     *
     * @param \Gino\App\NoticeBoard\noticeBoard $controller
     * @return array associativo id => nome delle categorie
     */
    public static function getForSelect(\Gino\App\NoticeBoard\noticeBoard $controller)
    {
        $objs = self::objects($controller);
        $res = array();
        foreach($objs as $obj) {
            $res[$obj->id] = \Gino\htmlChars($obj->name);
        }

        return $res;
    }
}

