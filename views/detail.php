<?php
/**
* @file detail.php
* @brief Template per la vista dettaglio atto
*
* Variabili disponibili:
* - **items**: \Gino\App\NoticeBoard\NoticeBoardItem, atto @ref Gino.App.NoticeBoard.NoticeBoardItem
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
    <h1><?= \Gino\htmlChars($item->object) ?></h1>
    <? $ctg = new NoticeBoardCategory($item->category, $item->getController()); ?>
    <? $deliberaive = new NoticeBoardDeliberative($item->deliberaive, $item->getController()); ?>

    <p><?= _('Categoria') ?>: <?= \Gino\htmlChars($ctg->name) ?></p>
    <? if($deliberaive->id): ?>
        <p><?= _('Organo deliberante') ?>: <?= \Gino\htmlChars($deliberative->name) ?></p>
    <? endif ?>
    <p><?= _('Numero di protocollo') ?>: <?= \Gino\htmlChars($item->protocol_number) ?></p>
    <p><?= _('Numero atto') ?>: <?= \Gino\htmlChars($item->act_number) ?></p>
    <p><?= _('Data atto') ?>: <?= \Gino\dbDateToDate($item->act_date) ?></p>
    <p><?= _('Inizio pubblicazione') ?>: <?= \Gino\dbDateToDate($item->publication_date_begin) ?></p>
    <p><?= _('Fine pubblicazione') ?>: <?= \Gino\dbDateToDate($item->publication_date_end) ?></p>

    <? if(count($item->attachments)): ?>
        <h2><?= _('Allegati') ?></h2>
        <ul class="attachments">
        <? foreach($item->attachments as $attachment_id): ?>
            <? $attachment = new NoticeBoardItemAttachment($attachment_id, $item->getController()); ?>
            <li class="<?= $attachment->extension() ?>"><a href="<?= $attachment->downloadUrl() ?>"><?= \Gino\htmlChars($attachment->description) ?></a> (<?= round($attachment->filesize / 1000, 1) ?> kb)</li>
        <? endforeach ?>
        </ul>
    <? endif ?>

</section>
<? // @endcond ?>
