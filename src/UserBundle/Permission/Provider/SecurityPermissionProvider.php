<?php

namespace UserBundle\Permission\Provider;

class SecurityPermissionProvider implements ProviderInterface
{
    public function getPermissions()
    {
        return array(
            'OFFICE_MANAGEMENT' => array(
                'ROLE_MANAGE_OFFICES',
            ),
            'USER_MANAGEMENT' => array(
                'ROLE_MANAGE_USERS',
                'ROLE_CHANGE_USER_PASSWORD',
                'ROLE_DESIGNATION_MANAGEMENT',
            ),
            'BACKLOG_PROCESSING' => array(
                'ROLE_MOUZA_OPTION_MANAGEMENT',
                'ROLE_MOUZA_OPTION_APPROVE',
                'ROLE_MOUZA_OPTION_DELETE',
                'ROLE_VOLUME_BOOK_VIEW',
                'ROLE_VOLUME_BOOK_CREATE',
                'ROLE_VOLUME_BOOK_UPDATE',
                'ROLE_VOLUME_BOOK_DELETE',
                'ROLE_VOLUME_BOOK_APPROVED',
                'ROLE_KHATIAN_ENTRY',
                'ROLE_KHATIAN_VIEW',
                'ROLE_KHATIAN_VERIFICATION',
                'ROLE_KHATIAN_COMPARISON',
                'ROLE_REFERENCING_TO_MOUZA_MAP',
                'ROLE_KHATIAN_APPROVAL',
                'ROLE_MARK_KHATIAN_AS_NON_DELIVERABLE',
                'ROLE_DOCUMENT_METADATA_MANAGEMENT',
                'ROLE_DAILY_WORKLOG_REPORT',
                'ROLE_PAYMENT_REPORT',
                'ROLE_VIEW_ARCHIVE',
                'ROLE_ARCHIVE_VOLUME',
                'ROLE_ARCHIVE_KHATIAN',
                'ROLE_FILE_IMPORT',
                'ROLE_VIEW_RESTRICTED_KHATIAN',
                'ROLE_VIEW_WORKFLOW_TEAM',
                'ROLE_CREATE_WORKFLOW_TEAM',
                'ROLE_DELETE_WORKFLOW_TEAM',
            ),
            'PORCHA_REQUEST_PROCESSING' => array(
                'ROLE_PORCHA_REQUEST_ENTRY',
                'ROLE_PORCHA_REQUEST_MANAGE',
                'ROLE_GENERATE_APPLICATION_RECEIPT',
                'ROLE_EDIT_PORCHA_REQUEST',
                'ROLE_GENERATE_TOPSHEETS'
            ),
            'PORCHA_CORRECTION_REQUEST_PROCESSING' => array(
                'ROLE_PORCHA_CORRECTION_REQUEST_MANAGE',
                'ROLE_PORCHA_CORRECTION_REQUEST_AC_LAND_MANAGE',
                'ROLE_PORCHA_COPY_REQUEST_MANAGE',
                'ROLE_PORCHA_COPY_REQUEST_MANAGE_DC'
            ),
            'MOUZA_MAP_AND_OTHER_DOCUMENT_APPLICATION_PROCESSING' => array(
                'ROLE_RECEIVE_DOCUMENT_REQUEST_APPLICATION',
                'ROLE_EDIT_DOCUMENT_REQUEST_APPLICATION',
                'ROLE_GENERATING_APPLICATION_RECEIPT',
                'ROLE_GENERATING_DOCUMENT_SERVICE_TOPSHEET',
                'ROLE_DOCUMENT_DELIVERY_MANAGEMENT',
                'ROLE_MOUZA_COURT_FEE_REGISTER',
                'ROLE_RECEIVE_DELIVERY_REGISTER',
            ),
            'GEOGRAPHICAL_INFO_MANAGEMENT' => array(
                'ROLE_GEOGRAPHICAL_INFO_APPROVED'
            ),
            'PORCHA_EDITING_PROCESS' => array(
                'ROLE_EDITING_ARCHIVED_KHATIAN',
                'ROLE_PROCESS_CORRECTION_REQUEST_FROM_AC_LAND',
                'ROLE_KHATIAN_HISTORY',
            ),
            'UDC_MANAGEMENT' => array(

                'ROLE_PORCHA_REGISTER_FOR_UDC',
                'ROLE_PORCHA_REGISTER_LIST_FOR_UDC',
                'ROLE_PORCHA_COURT_FEE_FOR_UDC',

                'ROLE_APPLY_FOR_ELRS_SERVICES',
                'ROLE_ACTIVATE_DEACTIVATE_UDC',
                'ROLE_VIEW_UDC_RECEIVE_DELIVERY_REGISTER',
                'ROLE_MESSAGING_WITH_UDC_OPERATOR',
                'ROLE_COURT_FEE_REGISTER_FOR_UDC',
                'ROLE_VIEW_UDC_RELATED_REPORTS'
            ),
            'DC_MANAGEMENT' => array(
                'ROLE_VIEW_DC_RELATED_REPORTS',
            ),
            'AC_LAND_OFFICE' => array(
                'ROLE_REQUEST_RECORD_COPY_TO_AC_LAND',
                'ROLE_PROCESSING_RECORD_COPY_REQUEST_FROM_AC_LAND'
            ),
            'DC_OFFICE_SETTINGS' => array(
                'ROLE_SETTING_OVERRIDING_RULES',
                'ROLE_MANAGING_HOLIDAY_CALENDAR',
                'ROLE_NON_DELIVERABLE_RULE_SETTINGS',
                'ROLE_EDIT_ANY_KHATIAN_TEMPLATE',
                'ROLE_CREATE_NEW_KHATIAN_TEMPLATE',
                'ROLE_KHATIAN_TEMPLATE_LIBRARY',
                'ROLE_APPROVE_KHATIAN_TEMPLATE',
                'ROLE_SET_TEMPLATE_FOR_OFFICE',
                'ROLE_NON_DELIVERABLE_DOCUMENT_REPORT',
                'ROLE_NON_DELIVERABLE_NOTIFICATION',
                'ROLE_OWN_OFFICE_SETTING',
                'ROLE_DELIVERY_DAY_SETTING',
                'ROLE_COURT_FEE_SETTING',
                'ROLE_ADDITIONAL_FEE_SETTING',
                'ROLE_SMS_SETTING',
            ),
            'MINISTRY_OFFICE_SETTINGS' => array(
                'ROLE_MANAGE_APP_DIVISIONS',
                'ROLE_MANAGE_APP_DISTRICTS',
                'ROLE_MANAGE_APP_UPOZILAS',
                'ROLE_MANAGE_APP_UNIONS',
            ),
            'SERIVCES' => array(
                'ROLE_MOUZA_MAP_REQUEST_ENTRY',
                'ROLE_PORCHA_CORRECTION_REQUEST_ENTRY',
                'ROLE_PORCHA_CORRECTION_REQUEST_AC_LAND_ENTRY',
                'ROLE_PORCHA_COPY_REQUEST_ENTRY',
                'ROLE_PORCHA_COPY_REQUEST_ENTRY_DC',
                'ROLE_MOUZA_MAP_REQUEST_MANAGE',
                'ROLE_CASE_COPY_REQUEST_ENTRY',
                'ROLE_CASE_COPY_REQUEST_MANAGE',
                'ROLE_INFORMATION_SLIP_REQUEST_ENTRY',
                'ROLE_INFORMATION_SLIP_REQUEST_MANAGE',
                'ROLE_CHANGE_DELIVERY_DATE',
                'ROLE_START_SERVICE_REQUEST',
                'ROLE_COMPLETE_SERVICE_REQUEST',
                'ROLE_DELIVER_SERVICE_REQUEST',
                'ROLE_DOCUMENT_DELETE'
            ),
            'LEASE_MANAGEMENT' => array(
                'ROLE_WATER_BODY_LEASE_CREATE',
                'ROLE_PREVIOUS_WATER_LEASE_CREATE',
                'ROLE_OPEN_WATER_BODY_LEASE_LIST',
                'ROLE_APPROVED_LEASE_LIST',
                'ROLE_RENEW_APPROVAL_ACCEPT',
                'ROLE_WAITING_FOR_RENEW_APPROVAL_LEASE_LIST',
                'ROLE_LEASE_CREATE',
                'ROLE_OPEN_MARKET_BODY_LEASE_LIST',
                'ROLE_MARKET_BODY_LEASE_CREATE',
                'ROLE_MARKET_LEASE_EDIT',
                'ROLE_PREVIOUS_LEASE_CREATE',
                'ROLE_PREVIOUS_LEASE_EDIT',
                'ROLE_WAITING_LEASE_LIST',
                'ROLE_LEASE_EDIT',
                'ROLE_PREVIOUS_LEASE_APPROVED',
                'ROLE_LEASE_DETAILS_DELETE',
                'ROLE_LEASE_VIEW',
                'ROLE_OPEN_LEASE_LIST',
                'ROLE_LEASE_ADD_TO_PORTAL',
            ),
            'LEASE_ASSIGN_MANAGEMENT' => array(
                'ROLE_ASSIGN_LEASE_CREATE',
                'ROLE_WAITING_FOR_TERMINATE_LEASE_STATUS_CHANGE',
                'ROLE_WAITING_FOR_TERMINATE_LEASE_LIST',
                'ROLE_ASSIGN_LEASE_EDIT',
                'ROLE_ASSIGN_LEASE_LIST',
                'ROLE_LEASE_ASSIGN_APPROVED',
                'ROLE_LEASE_ASSIGN_TERMINATED',
                'ROLE_ASSIGN_LEASE_VIEW',
                'ROLE_LEASE_STATUS_CHANGE'
            ),
            'APPLICATION_MANAGEMENT' => array(
                'ROLE_MANUAL_APPLICATION_CREATE',
                'ROLE_APPLICATION_LIST',
                'ROLE_APPROVED_APPLICATION_LIST',
                'ROLE_APPLICATION_VIEW',
                'ROLE_APPLICATION_STATUS_CHANGE',
                'ROLE_LEASE_WISE_APPLICATION_LIST'
            ),
            'ORDER_SHEET_MANAGEMENT' => array(
                'ROLE_ORDER_SHEET_LIST',
                'ROLE_LEASE_WISE_ORDER_SHEET_LIST',
                'ROLE_ORDER_SHEET_VIEW',
            ),
            'MARKET_MANAGEMENT' => array(
                'ROLE_MENU_ITEM_MARKET_CREATE',
                'ROLE_MENU_ITEM_MARKET_LIST',
                'ROLE_MENU_ITEM_MARKET_EDIT'
            ),
            'GADGET_MANAGEMENT' => array(
                'ROLE_GADGET_LIST',
                'ROLE_GADGET_VIEW',
                'ROLE_GADGET_CREATE',
                'ROLE_GADGET_EDIT',
                'ROLE_GADGET_APPROVED',
                'ROLE_GADGET_TERMINATED',
                'ROLE_GADGET_RENEW',
                'ROLE_GADGET_ADD_TO_PORTAL',
                'ROLE_OPEN_GADGET_LIST',
            ),
            'MENU_ITEMS' => array(
                'ROLE_MENU_ITEM_SERVICE',
                'ROLE_MENU_ITEM_MARKET',
                'ROLE_MENU_ITEM_TEMPLATE',
                'ROLE_MENU_ITEM_VOLUME',
                'ROLE_MENU_ITEM_KHATIAN',
                'ROLE_MENU_ITEM_BATCH_KHATIAN',
                'ROLE_MENU_ITEM_APP_KHATIAN',
                'ROLE_MENU_ITEM_ARCHIVE',
                'ROLE_MENU_ITEM_MOUZA_OPTION',
                'ROLE_MENU_ITEM_SETTINGS',
                'ROLE_MENU_ITEM_OFFICE',
                'ROLE_MENU_ITEM_USER',
                'ROLE_MENU_ITEM_LEASE',
                'ROLE_MENU_ITEM_APPLICATION',
                'ROLE_MENU_ITEM_LEASE_ASSIGN',
                'ROLE_MENU_ITEM_ORDER_SHEET',
                'ROLE_MENU_ITEM_GADGET_MANAGEMENT'
            )
        );
    }
}