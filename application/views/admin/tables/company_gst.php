<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
    'description',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'company_gst_details';
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['isdefault']);
$output       = $result['output'];
$rResult      = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name' || $aColumns[$i] == 'id') {
            $_data = '<span><a href="#" onclick="edit_company_gst(this,' . $aRow['id'] . '); return false;" data-name="' . e($aRow['name']) . '" data-description="' . e(clear_textarea_breaks($aRow['description'])) . '">' . e($_data) . '</a></span>';
            if ($aColumns[$i] == 'id' && $aRow['isdefault'] == 1) {
                $_data .= '<span class="display-block text-info">' . _l('Default GST') . '</span>';
            }
        } else if($aColumns[$i] == 'description') {
            $_data = process_text_content_for_display($_data);
        }
        $row[] = $_data;
    }

    $options = '<div class="tw-flex tw-items-center tw-space-x-3">';
    $options .= '<a href="#" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700" onclick="edit_company_gst(this,' . $aRow['id'] . '); return false;" data-name="' . e($aRow['name']) . '" data-description="' . e(clear_textarea_breaks($aRow['description'])) . '">
        <i class="fa-regular fa-pen-to-square fa-lg"></i>
    </a>';

    if($aRow['isdefault'] == 0) {
        $options .= '<a href="' . admin_url('companygst/make_default_gst/' . $aRow['id']) . '" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700" ' . _attributes_to_string([
            'data-toggle' => 'tooltip',
            'title'       => _l('Make Default GST'),
            ]) . '>
            <i class="fa-regular fa-star fa-lg"></i>
        </a>';
        $options .= '<a href="' . admin_url('companygst/delete_company_gst/' . $aRow['id']) . '"
        class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
            <i class="fa-regular fa-trash-can fa-lg"></i>
        </a>';
    }
    $options .= '</div>';

    $row[]              = $options;
    $output['aaData'][] = $row;
}