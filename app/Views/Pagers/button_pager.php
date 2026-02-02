<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>

<nav aria-label="<?= lang('Pager.pageNavigation') ?>">
   <ul class="pagination pagination-sm mb-0">
      <?php if ($pager->hasPrevious()) : ?>
         <li class="page-item">
            <a class="page-link btn btn-sm btn-outline-primary" href="<?= $pager->getFirst() ?>" aria-label="<?= lang('Pager.first') ?>">
               <?= lang('Pager.first') ?>
            </a>
         </li>
         <li class="page-item">
            <a class="page-link btn btn-sm btn-outline-primary" href="<?= $pager->getPrevious() ?>" aria-label="<?= lang('Pager.previous') ?>">
               <?= lang('Pager.previous') ?>
            </a>
         </li>
      <?php else : ?>
         <li class="page-item disabled">
            <span class="page-link btn btn-sm btn-outline-secondary"><?= lang('Pager.first') ?></span>
         </li>
         <li class="page-item disabled">
            <span class="page-link btn btn-sm btn-outline-secondary"><?= lang('Pager.previous') ?></span>
         </li>
      <?php endif ?>

      <?php foreach ($pager->links() as $link) : ?>
         <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
            <?php if ($link['active']) : ?>
               <span class="page-link btn btn-sm btn-primary text-white"><?= $link['title'] ?></span>
            <?php else : ?>
               <a class="page-link btn btn-sm btn-outline-primary" href="<?= $link['uri'] ?>">
                  <?= $link['title'] ?>
               </a>
            <?php endif; ?>
         </li>
      <?php endforeach ?>

      <?php if ($pager->hasNext()) : ?>
         <li class="page-item">
            <a class="page-link btn btn-sm btn-outline-primary" href="<?= $pager->getNext() ?>" aria-label="<?= lang('Pager.next') ?>">
               <?= lang('Pager.next') ?>
            </a>
         </li>
         <li class="page-item">
            <a class="page-link btn btn-sm btn-outline-primary" href="<?= $pager->getLast() ?>" aria-label="<?= lang('Pager.last') ?>">
               <?= lang('Pager.last') ?>
            </a>
         </li>
      <?php else : ?>
         <li class="page-item disabled">
            <span class="page-link btn btn-sm btn-outline-secondary"><?= lang('Pager.next') ?></span>
         </li>
         <li class="page-item disabled">
            <span class="page-link btn btn-sm btn-outline-secondary"><?= lang('Pager.last') ?></span>
         </li>
      <?php endif ?>
   </ul>
</nav>
