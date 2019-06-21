<?php
/** @var cmsTemplate $this */
$this->renderAsset('icms2ext/backend/form', [
    'title' => $title,
    'form' => $form,
    'item' => $item,
    'errors' => $errors,
    'breadcrumbs' => $breadcrumbs ?? null,
]);