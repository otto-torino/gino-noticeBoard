<?php
/**
 * @file class.NoticeBoardDeliberative.php
 * @brief Contiene la definizione e l'implementazione della classe Gino.App.NoticeBoard.NoticeBoardDeliberative
 * @copyright 2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */

namespace Gino\App\NoticeBoard;

/**
 * @brief Classe di tipo Gino.Model che rappresenta un organo deliberante
 * @version 0.1.0
 * @copyright 2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @author marco guidotti <marco.guidotti@otto.to.it>
 * @author abidibo <abidibo@gmail.com>
 */
class NoticeBoardDeliberative extends \Gino\Model
{
    public static $table = 'notice_board_deliberative';

    /**
     * @brief Costruttore
     *
     * @param int $id id record
     * @param \Gino\App\NoticeBoard\noticeBoard $instance
     * @return istanza di Gino.App.NoticeBoard.NoticeBoardDeliberative
     */
    public function __construct($id, $instance)
    {
        $this->_controller = $instance;
        $this->_tbl_data = self::$table;

        $this->_fields_label = array(
            'name' => _('Nome'),
        );

        parent::__construct($id);

        $this->_model_label = _('Organo deliberante');
    }

    /**
     * @brief Rappresentazione a stringa dell'oggetto
     *
     * @return nome organo deliberante
     */
    function __toString() {
        return (string) $this->name;
    }
}
