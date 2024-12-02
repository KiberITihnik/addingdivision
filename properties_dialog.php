<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var \Bitrix\Bizproc\Activity\PropertiesDialog $dialog */
$map = $dialog->getMap();
$values = $dialog->getCurrentValues();
?>
<?php foreach ($map as $item): ?>
    <tr>
        <td align="right" width="40%" valign="top" style="padding-top: 12px;">
            <span class="adm-required-field"><?= $item['Name'] ?>:</span>

        </td>
        <td width="60%">
            <?php
            echo $dialog->renderFieldControl($item, null, true, \Bitrix\Bizproc\FieldType::RENDER_MODE_DESIGNER);
            ?>
        </td>
    </tr>
<?php endforeach ?>