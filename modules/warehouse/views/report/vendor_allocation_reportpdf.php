<?php defined('BASEPATH') or exit('No direct script access allowed');
// Theese lines should aways at the end of the document left side. Dont indent these lines
$html = <<<EOF
<div >
$vendor_allocation_report
</div>
EOF;
$pdf->writeHTML($html, true, false, true, false, '');
