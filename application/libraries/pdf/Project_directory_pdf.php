<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/App_pdf.php');

class Project_directory_pdf extends App_pdf
{
    protected $project_directory;

    public function __construct($project_directory)
    {
        $project_directory                = hooks()->apply_filters('request_html_pdf_data', $project_directory);
        $GLOBALS['Project_directory_pdf'] = $project_directory;
        parent::__construct();
        $this->project_directory = $project_directory;

        $this->SetTitle(_l('project_directory'));
        # Don't remove these lines - important for the PDF layout
        $this->project_directory = $this->fix_editor_html($this->project_directory);
    }

    // Override the Footer method from TCPDF or FPDI
    public function Footer()
    {
        // Trigger the custom hook for the footer content
        hooks()->do_action('pdf_footer', ['pdf_instance' => $this, 'type' => $this->type]);
       
        $this->SetY(-20); // 15mm from the bottom
        $this->SetX(-15); // 15mm from the bottom
        $this->SetFont($this->get_font_name(), 'I', 8);
        $this->SetTextColor(142, 142, 142);
        $this->Cell(0, 15, $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');

    }


    public function prepare()
    {
        $this->set_view_vars('project_directory', $this->project_directory);

        return $this->build();
    }

    protected function type()
    {
        return 'project_directory';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/admin/projects/my_export_data_pdf.php';
        $actualPath = APPPATH . 'views/admin/projects/project_directory_pdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}