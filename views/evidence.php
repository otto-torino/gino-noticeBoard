<?php
/**
* @file evidence.php
* @brief Template per il box atti in evidenza
*
* Variabili disponibili:
* - **categories**: array, array di categorie \Gino\App\NoticeBoard\NoticeBoardCategory
* - **form_search**: html, form di ricerca
* - **show_form**: bool, TRUE se il form deve essere mostrato espanso perché compilato
*
* @version 1.0.0
* @copyright 2012-2014 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @author Marco Guidotti guidottim@gmail.com
* @author abidibo abidibo@gmail.com
*/
?>
<? namespace Gino\App\NoticeBoard; ?>
<? //@cond no-doxygen ?>
<section class="noticeboard-evidence">
    <h1><?= _('Albo Pretorio') ?> <span class="fa fa-search link" onclick="$('form_search').toggleClass('hidden')"></span></h1>
    <div id="form_search" class="<?= $show_form ? '' : 'hidden' ?>">
        <?= $form_search ?>
    </div>
    <? if(count($categories)): ?>
        <? foreach($categories as $category): ?>
            <h2 class="accordion-toggler"><?= \Gino\htmlChars($category['ctg']->name) ?> (<?= count($category['items']) ?>)</h2>
            <div class="accordion-element">
                <ul>
                <? foreach($category['items'] as $item): ?>
                    <li><a href="<?= $item->getUrl() ?>"><?= \Gino\htmlChars($item->object) ?></a></li>
                <? endforeach ?>
                </ul>
            </div>
        <? endforeach ?>
        <script>
            var myAccordion = new Fx.Accordion($$('.accordion-toggler'), $$('.accordion-element'), {display: -1});
        </script>
    <? else: ?>
        <p><?= _('Non risultano atti in pubblicazione') ?></p>
    <? endif ?>
</section>
<? // @endcond ?>
