<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {die();}

use Bitrix\Bizproc\FieldType;

class CBPAddingDivision extends CBPActivity
{
    /** @var int */
    private $DEAL_ID;

    /**
     * CBPAddingDivision constructor.
     * @param $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->arProperties = [
            'DEAL_ID' => null,
            'DEPARTMENT_ID' => null,
        ];
    }

    /**
     * @return int
     */
    public function Execute(): int
    {
        $dealID = $this->__get('DEAL_ID');
        $rsDeal = CCrmDeal::GetListEx(
            array('DATE_CREATE' => 'DESC'),
            array('ID' => $dealID),
            false,
            false,
            array('ID', 'UF_CRM_1614328010', 'CATEGORY_ID')
        );

        if ($arDeal = $rsDeal->Fetch()) {
            if (!empty($arDeal['UF_CRM_1614328010']) && $arDeal['UF_CRM_1614328010'] !== 0) {
                $initiator = $arDeal['UF_CRM_1614328010'];
            }

            $arUser = CUser::GetByID($initiator)->Fetch();
            $fullName = $arUser['NAME'] . ' ' . $arUser['LAST_NAME'];

            $departmentID = $arUser['UF_DEPARTMENT'][0];
            if ($departmentID === 814) {
                $departmentID = $arUser['UF_DEPARTMENT'][1];
            }

            $rsDepartment = CIBlockSection::GetByID($departmentID);
            if ($arDepartment = $rsDepartment->Fetch()) {
                $departmentID = $arDepartment['ID'];

                $iblockID = 81;
                $propertyCode = 'DEPARTMENT';

                $arFilter = array(
                    'IBLOCK_ID' => $iblockID,
                    'PROPERTY_' . $propertyCode => $departmentID
                );

                $rsElement = CIBlockElement::GetList(
                    array(),
                    $arFilter,
                    false,
                    false,
                    array('ID')
                );

                if ($arElement = $rsElement->Fetch()) {
                    $elementID = $arElement['ID'];
                }
            }

            if (isset($elementID)) {
                $arFilter = array(
                    'IBLOCK_ID' => $iblockID,
                    'ID' => $elementID
                );

                $rsElement = CIBlockElement::GetList(
                    array(),
                    $arFilter,
                    false,
                    false,
                    array('IBLOCK_SECTION_ID')
                );

                if ($arElement = $rsElement->Fetch()) {
                    $sectionID = $arElement['IBLOCK_SECTION_ID'];

                    $arSection = CIBlockSection::GetByID($sectionID)->Fetch();
                }
            }
        }
        $this->__set('DEPARTMENT_ID', $sectionID);
        return CBPActivityExecutionStatus::Closed;
    }

    /**
     * @param $documentType
     * @param $activityName
     * @param $arWorkflowTemplate
     * @param $arWorkflowParameters
     * @param $arWorkflowVariables
     * @param null $arCurrentValues
     * @param string $formName
     * @return \Bitrix\Bizproc\Activity\PropertiesDialog
     */
    public static function GetPropertiesDialog($documentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters, $arWorkflowVariables, $arCurrentValues = null, $formName = '')
    {
        $dialog = new \Bitrix\Bizproc\Activity\PropertiesDialog(__FILE__, [
            'documentType' => $documentType,
            'activityName' => $activityName,
            'workflowTemplate' => $arWorkflowTemplate,
            'workflowParameters' => $arWorkflowParameters,
            'workflowVariables' => $arWorkflowVariables,
            'currentValues' => $arCurrentValues
        ]);

        $dialog->setMap([
            'DEAL_ID' => [
                'Name' => 'Id обращения',
                'FieldName' => 'DEAL_ID',
                'Type' => FieldType::INT,
            ],
        ]);
        return $dialog;
    }

    /**
     * @param $documentType
     * @param $activityName
     * @param $arWorkflowTemplate
     * @param $arWorkflowParameters
     * @param $arWorkflowVariables
     * @param $arCurrentValues
     * @param $errors
     * @return bool
     */
    public static function GetPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate, &$arWorkflowParameters, &$arWorkflowVariables, $arCurrentValues, &$errors)
    {
        $errors = [];
        $properties = $arCurrentValues;

        $errors = self::ValidateProperties($properties);
        if (count($errors) > 0) {
            return false;
        }

        $currentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
        $currentActivity['Properties'] = $properties;
        return true;
    }

    /**
     * Validate properties.
     *
     * @param array $arDael
     * @param CBPWorkflowTemplateUser|null $user
     * @return array
     */
    public static function ValidateProperties($arDael = array(), CBPWorkflowTemplateUser $user = null)
    {
        $errors = [];

        if (empty($arDael['DEAL_ID'])) {
            $errors[] = ['code' => 'NotExist', 'parameter' => 'DEAL_ID', 'message' => 'Не указан ID обращения'];
        }
        return array_merge($errors, parent::ValidateProperties($arDael, $user));
    }
}