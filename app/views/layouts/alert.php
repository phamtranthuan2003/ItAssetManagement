<?php
$flash = getFlash();
if ($flash):
?>
<div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
    <?php if ($flash['type'] === 'success'): ?>
        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
    <?php elseif ($flash['type'] === 'danger'): ?>
        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
    <?php elseif ($flash['type'] === 'warning'): ?>
        <i class="bi bi-exclamation-circle-fill fs-5 me-2"></i>
    <?php else: ?>
        <i class="bi bi-info-circle-fill fs-5 me-2"></i>
    <?php endif; ?>
    <div><?= e($flash['message']) ?></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
