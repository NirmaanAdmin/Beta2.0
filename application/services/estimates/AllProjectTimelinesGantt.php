<?php

namespace app\services\estimates;

use app\services\projects\AbstractGantt;

class AllProjectTimelinesGantt extends AbstractGantt
{
    protected $ci;

    protected $filters = [];

    public function __construct($filters)
    {
        $this->filters = $filters;
        $this->ci      = &get_instance();
    }

    public function get()
    {
        $gantt = [];

        $project_timelines = $this->queryProjectTimelines($this->filters['estimate_id']);

        foreach ($project_timelines as $project_timeline) {
            $row     = $this->prepareGanttRow($project_timeline);
            $gantt[] = $row;
            $tasks   = $this->ci->estimates_model->get_project_timelines_tasks($project_timeline['estimate_id'], ['milestone' => $project_timeline['id']]);

            foreach ($tasks as $task) {
                $gantt[] = array_merge(static::tasks_array_data($task, null, isset($row['end']) ? $row['end'] : null), [
                    'progress'     => 0,
                    'dependencies' => $row['id'],
                ]);
            }
        }

        return $gantt;
    }

    protected function queryProjectTimelines($estimate_id)
    {
        $this->ci->db->where('estimate_id', $estimate_id);
        $this->ci->db->order_by('milestone_order', 'asc');
        return $this->ci->db->get('project_timelines')->result_array();
    }

    protected function prepareGanttRow($project_timeline)
    {
        $row               = [];
        $row['id']         = 'proj_' . $project_timeline['id'];
        $row['project_timeline_id'] = $project_timeline['id'];
        $row['name']       = $project_timeline['name'];
        $row['progress']   = 0;
        $row['start']      = date('Y-m-d', strtotime($project_timeline['start_date']));

        if (!empty($project_timeline['deadline'])) {
            $row['end'] = date('Y-m-d', strtotime($project_timeline['deadline']));
        }

        $row['custom_class'] = 'noDrag';

        return $row;
    }
}
