<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Critical_tracker_target_date_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'Critical Item Description',
                'key'       => '{description}',
                'available' => [],
                'templates' => [
                    'send-critical-tracker-mail',
                ],
            ],
            [
                'name'      => 'Assigned To',
                'key'       => '{assignedTo}',
                'available' => [],
                'templates' => [
                    'send-critical-tracker-mail',
                ],
            ],
        ];
    }

    /**
     * @param  object $data  Should contain ->id = tblcritical_mom.id
     * @return array
     */
    public function format($data)
    {
        $itemId = $data->id;
        $this->ci->db->where('id', $itemId);
        $item = $this->ci->db->get(db_prefix() . 'critical_mom')->row();

        $fields = [];
        if (! $item) {
            return $fields;
        }

        // 1) Description
        $fields['{description}'] = $item->description;

        // 2) Assigned To
        $assignedTo = 'Unassigned';
        $parts      = [];

        // staff
        if (! empty($item->staff)) {
            $ids = array_filter(array_map('trim', explode(',', $item->staff)));
            if ($ids) {
                $this->ci->db
                     ->select('firstname, lastname')
                     ->from(db_prefix() . 'staff')
                     ->where_in('staffid', $ids);
                $staffRows = $this->ci->db->get()->result();
                $names     = array_map(function ($r) {
                    return trim("{$r->firstname} {$r->lastname}");
                }, $staffRows);

                if ($names) {
                    $parts[] = implode(', ', $names);
                }
            }
        }

        // vendor
        if (! empty($item->vendor)) {
            $parts[] = $item->vendor;
        }

        if ($parts) {
            $assignedTo = implode(' and ', $parts);
        }

        $fields['{assignedTo}'] = $assignedTo;
        $fields['{link}'] = site_url('admin/meeting_management/minutesController/critical_agenda');

        return $fields;
    }
}
