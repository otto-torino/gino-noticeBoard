<?php
/**
* @file archive.php
* @brief Template per la vista archivio albo pretorio
*
* Variabili disponibili:
* - **items**: array, atti @ref Gino.App.NoticeBoard.NoticeBoardItem
* - **form_search**: html, form di ricerca
* - **show_form**: bool, TRUE se il form deve essere mostrato espanso perché compilato
* - **pagination**: html, paginazione
*
* @version 1.0.0
* @copyright 2012-2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @author Marco Guidotti guidottim@gmail.com
* @author abidibo abidibo@gmail.com
*/
?>
<? namespace Gino\App\NoticeBoard; ?>
<? //@cond no-doxygen ?>
<section>
    <h1><?= _('Albo Pretorio - Archivio') ?> <span class="fa fa-search link" onclick="$('form_search').toggleClass('hidden')"></span></h1>
    <div id="form_search" class="<?= $show_form ? '' : 'hidden' ?>">
        <?= $form_search ?>
    </div>
    <? if(count($items)): ?>
        <ul>
        <? foreach($items as $item): ?>
            <li><a href="<?= $item->getUrl() ?>"><?= \Gino\htmlChars($item->object) ?></a></li>
        <? endforeach ?>
        </ul>
        <?= $pagination ?>
    <? else: ?>
        <p><?= _('La ricerca non ha prodotto risultati') ?></p>
    <? endif ?>
</section>
<? // @endcond ?>
