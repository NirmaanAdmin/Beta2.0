<?php defined('BASEPATH') or exit('No direct script access allowed');
// Theese lines should aways at the end of the document left side. Dont indent these lines
$html = <<<EOF
<div class="div_pdf">
$per_clients
</div>
EOF;
$html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
$pdf->writeHTML($html, true, false, true, false, '');
 