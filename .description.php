<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Bizproc\FieldType;

$arActivityDescription = [
    "NAME" => 'Получение подразделения для поля',
    "DESCRIPTION" => '',
    "TYPE" => "activity",
    "CLASS" => "AddingDivision",
    "JSCLASS" => "BizProcActivity",
    "CATEGORY" => [
        "ID" => "other",
    ],
    'ADDITIONAL_RESULT' => ['FieldsMap'],
    'RETURN' => [
        'DEPARTMENT_ID' => [
            'NAME' => 'ID подразделения',
            'TYPE' => FieldType::INT
        ],
    ],
];

