<?php if (isset($progress)): ?>

    <?php if ($progress >= 70): ?>
    <?php $barClassStyle = 'progress-bar-success'; ?>
    <?php elseif ($progress < 30): ?>
    <?php $barClassStyle = 'progress-bar-danger'; ?>
    <?php else: ?>
    <?php $barClassStyle = 'progress-bar-warning'; ?>
    <?php endif; ?>

<div class="progress">
  <div class="progress-bar <?= $barClassStyle ?> progress-bar-strdasiped" role="progressbar"
  aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?= $progress ?>%">
      <span class="text-black"><?= $progress ?>%<span>
  </div>
</div>

<?php endif; ?>