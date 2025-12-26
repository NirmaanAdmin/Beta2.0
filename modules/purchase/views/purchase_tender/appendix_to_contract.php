<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appendix to Contract - Interior Works for Upper Ground Floor</title>
    <style>
        body {

            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .project-title {
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }

        .page-info {
            text-align: right;
            font-size: 0.9em;
            margin-bottom: 20px;
        }

        .contract-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .contract-table th,
        .contract-table td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
        }

        .contract-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
        }

        .clause-number {
            font-weight: bold;
        }

        .highlight {
            font-weight: bold;
        }

        .underline {
            text-decoration: underline;
        }

        .indented {
            margin-left: 20px;
        }

        .nested-list {
            margin-left: 40px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9em;
            color: #666;
        }

        .section-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div id="wrapper">
        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <?php echo form_open(admin_url('purchase/appendix_to_contract_add_update'), array('id' => 'appendix-to-contract-form'));
                            ?>
                            <input type="hidden" name="tender_id" value="<?php echo $tender_data[0]['tender_id']; ?>">
                            <input type="hidden" name="vendor_id" value="<?php echo $tender_data[0]['vendor_id']; ?>">
                            <input type="hidden" name="project_id" value="<?php echo $tender_data[0]['project_id']; ?>">
                            <div class="header">
                                <div class="project-title">Interior Works for <?php echo tender_name_by_id($tender_data[0]['tender_id']) ?> for <?php echo get_project_name_by_id($tender_data[0]['project_id']); ?></div>
                            </div>
                            <?php $document_number = isset($atc_data->document_number_1) ? $atc_data->document_number_1 : ''; ?>
                            <p>
                            <div class="page-info col-md-12" style="width: 20%;"><input type="text" class="form-control" name="document_number_1" value="<?php echo $document_number; ?>"></div>
                            <p><br><br><br><br>

                            <p>The Conditions of Contract comprise the "<strong>General Conditions</strong>", which form part of the "<strong>Conditions of Contract for Construction for Building and Engineering Works designed by the Employer"; Second Edition 2017 published by the Federation Internationale des Ingénieurs-Conseils (FIDIC)</strong> shall be applicable for this Contract. The Contractor is deemed to be acquainted with and shall be in possession of the "General Conditions".</p>

                            <p><strong>Particular Conditions Part A - Contract Data</strong></p>


                            <table class="contract-table">
                                <thead>
                                    <tr>
                                        <th>Clause</th>
                                        <th>Particulars</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="clause-number">Clause 1.0</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">1.1.27</td>
                                        <td>Defect Notification period (DNP)</td>
                                        <?php $first_index = isset($atc_data->data_json) ? $atc_data->data_json[0] : '365 days (after issuing Taking-Over Certificate)`'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $first_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">1.1.31</td>
                                        <td>Employer's name and Address:</td>
                                        <td>
                                            <?php $vendor_name = get_vendor_name_by_id($tender_data[0]['vendor_id']);
                                            $vendor_address = get_vendor_all_details_by_id($tender_data[0]['vendor_id'])->address; ?>
                                            <?php $company_name = isset($loa_data[0]['company_name']) ? $loa_data[0]['company_name'] : 'M/s ' . $vendor_name . ' ' . $vendor_address; ?>
                                            <?php $second_index = isset($atc_data->data_json) ? $atc_data->data_json[1] : $company_name; ?>
                                            <textarea name="data[]" class="form-control" rows="4"><?php echo $second_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">1.1.35</td>
                                        <td>Engineers Name and Address:</td>
                                        <?php $third_index = isset($atc_data->data_json) ? $atc_data->data_json[2] : 'NA'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $third_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">1.1.85</td>
                                        <td>Time of completion:</td>
                                        <?php $fourth_index = isset($atc_data->data_json) ? $atc_data->data_json[3] : 'On before 30th November 2023.'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fourth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">1.3.(a)(ii)</td>
                                        <td>Agreed method of electronic transmission:</td>
                                        <?php $fifth_index = isset($atc_data->data_json) ? $atc_data->data_json[4] : 'e-mail'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fifth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">1.3(d)</td>
                                        <td>Address of Employer for communication:</td>
                                        <td>
                                            <?php $sixth_index = isset($atc_data->data_json) ? $atc_data->data_json[5] : 'shikhar@basilius.in,abh.project@basilus.in,abhishek.intodia@basilius.in'; ?>
                                            <textarea name="data[]" class="form-control" rows="3"><?php echo $sixth_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">1.3(d)</td>
                                        <td>Address of engineer for communication:</td>
                                        <?php $seventh_index = isset($atc_data->data_json) ? $atc_data->data_json[6] : 'qs@basilius.in'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $seventh_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">1.4</td>
                                        <td>Contract shall be governed by the law of:</td>
                                        <?php $eighth_index = isset($atc_data->data_json) ? $atc_data->data_json[7] : 'Republic of India'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $eighth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">1.4</td>
                                        <td>Ruling Language:</td>
                                        <?php $ninth_index = isset($atc_data->data_json) ? $atc_data->data_json[8] : 'English'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $ninth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">1.4</td>
                                        <td>Language for communication:</td>
                                        <?php $tenth_index = isset($atc_data->data_json) ? $atc_data->data_json[9] : 'English'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $tenth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">1.8</td>
                                        <td>Numbers of additional paper copies of contractor's document</td>
                                        <?php $eleventh_index = isset($atc_data->data_json) ? $atc_data->data_json[10] : 'Two'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $eleventh_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">2.1</td>
                                        <td>After receiving the LOA, the contractor shall be given right of access to all or part of the site with in</td>
                                        <?php $twelfth_index = isset($atc_data->data_json) ? $atc_data->data_json[11] : 'Immediate'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $twelfth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">2.4</td>
                                        <td>Employer financial arrangement</td>
                                        <?php $thirteenth_index = isset($atc_data->data_json) ? $atc_data->data_json[12] : 'Not applicable'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $thirteenth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">4.2</td>
                                        <td>Performance Security (as percentage of the accepted Contract Amount in currencies and validity)</td>
                                        <td>
                                            <?php $fourteenth_index = isset($atc_data->data_json) ? $atc_data->data_json[13] : 'Percentage: 5% INR Validity:Up to 90 days after successful completion of Defect Liability Period'; ?>
                                            <textarea name="data[]" class="form-control" rows="3"><?php echo $fourteenth_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">4.19</td>
                                        <td>Electricity, Water & Gas</td>
                                        <td>
                                            <?php $fifteenth_index = isset($atc_data->data_json) ? $atc_data->data_json[14] : 'Electricity:Electricity shall be arranged by Employer at one point only on chargeable basis. Further distribution will be done by contractor at his cost.Recovery of electricity charges will be done @ charges billed by the service provider / local authority.Contractor is required to install sub meter and maintain daily power consumption record. The record shall be made available as and when required.Employer does not Guarantee full time power supply from the grid, Hence In case of non-availability of electricity, contractor should make alternate arrangement for power backup such as DG etc. at his own cost to complete the work in stipulated time period.Water: Construction water shall be arranged by Employer at one point, distribution network, pumps and storage tanks etc. should be arranged by contractor at his own cost. In case of unavailability of sufficient water, the additional requirement has to be made by contractor at their own risk & cost with all required arrangements.'; ?>
                                            <textarea name="data[]" class="form-control" rows="10"><?php echo $fifteenth_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">4.7.2</td>
                                        <td>Period for notification of errors in the items of reference:</td>
                                        <?php $sixteenth_index = isset($atc_data->data_json) ? $atc_data->data_json[15] : '7 days'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $sixteenth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">4.19</td>
                                        <td>Period of payment for temporary utilities</td>
                                        <?php $seventeenth_index = isset($atc_data->data_json) ? $atc_data->data_json[16] : 'Each month'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $seventeenth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">4.20</td>
                                        <td>Employer's free-issue material</td>
                                        <?php $eighteenth_index = isset($atc_data->data_json) ? $atc_data->data_json[17] : 'As per Item BOQ'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $eighteenth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">4.20</td>
                                        <td>Number of additional copies of progress reports</td>
                                        <?php $nineteenth_index = isset($atc_data->data_json) ? $atc_data->data_json[18] : 'Two'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $nineteenth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">5.1(b)</td>
                                        <td>Subcontracting</td>
                                        <?php $twentieth_index = isset($atc_data->data_json) ? $atc_data->data_json[19] : 'No allowed.'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $twentieth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">6.5</td>
                                        <td>Normal working hours on the site</td>
                                        <?php $twentyfirst_index = isset($atc_data->data_json) ? $atc_data->data_json[20] : '8.00 am to 10.00 pm'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $twentyfirst_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">6.6</td>
                                        <td>Facility for Staff & Labour</td>
                                        <td>
                                            <?php $twentysecond_index = isset($atc_data->data_json) ? $atc_data->data_json[21] : 'Only space shall be provided to the contractor for temporary construction of store and office.Accommodation facility for labour and staff shall not be permitted at site.'; ?>
                                            <textarea name="data[]" class="form-control" rows="4"><?php echo $twentysecond_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">6.6</td>
                                        <td>Key Personnel</td>
                                        <td>
                                            <?php $twentythird_index = isset($atc_data->data_json) ? $atc_data->data_json[22] : "Project Manager
                                            Planning Engineer
                                            Billing Engineer
                                            QA & QC Engineer
                                            Plant & Machinery In-Charge
                                            EHS In-Charge
                                            Storekeeper
                                            All other staff as per approved organization structure by Engineer as required to complete the work as per schedule.
                                            Appointment of Contractor's Project Manager & Construction manager will require Engineer approval"; ?>
                                            <textarea name="data[]" class="form-control" rows="10">
                                                <?php echo $twentythird_index; ?>
                                            </textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">8.3</td>
                                        <td>Programme</td>
                                        <?php $twentyfourth_index = isset($atc_data->data_json) ? $atc_data->data_json[23] : "The proposed execution schedule shall be enclosed by the bidder in schedule (Key Milestone level) in a format of project management tools like (MS Project (preferred) or Primavera) along with tender submission. Within 30 days of receiving the Notice to Proceed / LOA (whichever is earlier), the Contractor shall submit a detailed construction schedule in the same software tools.Whilst the activities and the duration of activities may differ from those identified on the Employer's schedule, however, the Employer will not change the dates for achieving major milestones and the completion date of tendered work.Month wise planned vs. achieved, MSP tracking resource planning showing deployment of labour and machinery along with detail work breakdown structure (WBS) to be submitted before 28th of preceding month for next month. Daily, weekly, fortnightly monitoring reports to be submitted to Engineer / Employer in approved formats. Any other additional requirement shall be fulfilled without any extra cost."; ?>
                                        <td>
                                            <textarea name="data[]" class="form-control" rows="8"><?php echo $twentyfourth_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">8.3</td>
                                        <td>Number of additional paper copies of program</td>
                                        <?php $twentyfifth_index = isset($atc_data->data_json) ? $atc_data->data_json[24] : 'Two'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $twentyfifth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">8.8</td>
                                        <td>Milestones, Sectional completion</td>
                                        <td>
                                            <?php $twentysixth_index = isset($atc_data->data_json) ? $atc_data->data_json[25] : "Refer to Key Milestone dates mentioned in clause 1.1.85.Delay damages shall be 1% of the final Contract Price per week in the currencies and proportions subjected to a maximum of 5%."; ?>
                                            <textarea name="data[]" class="form-control" rows="4"><?php echo $twentysixth_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">8.8</td>
                                        <td>Delay damage payable for each week of delay</td>
                                        <?php $twentyseventh_index = isset($atc_data->data_json) ? $atc_data->data_json[26] : 'Delay damages shall be 1% of the final Contract Price per week in the currencies and proportions subjected to a maximum of 5%.'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $twentyseventh_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">8.8</td>
                                        <td>Maximum amount of delay damages</td>
                                        <td>
                                            <?php $twentyeighth_index = isset($atc_data->data_json) ? $atc_data->data_json[27] : '5% of total contract sum.Additional Paragraphs:If the Contractor fails to achieve any of the milestones indicated in Contractor\'s program under Sub-Clause 8.3 or as specified in the Appendix to Tender, the Employer Representative may withhold temporary amounts from the Contractor\'s payments, at the rate indicated in the Appendix to Tender applied to the Section of the Works which is delayed. This retention shall be released as soon as the Contractor achieves the subsequent milestones within the specified time, otherwise this retention shall be treated as part of the delay damages paid to the Employer under this Sub- Clause.If the Works are to be completed in Sections within the respective time specified in the Appendix to Tender, then the Contractor shall pay delay damages to the Employer for any delay in completing the respective Sections of the Works at the rate specified in the Appendix to Tender applied to the value of the works delayed.'; ?>
                                            <textarea name="data[]" class="form-control" rows="10"><?php echo $twentyeighth_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">12.2</td>
                                        <td>Method of measurement</td>
                                        <?php $twentyninth_index = isset($atc_data->data_json) ? $atc_data->data_json[28] : 'As per IS'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $twentyninth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">13.4(b)(ii)</td>
                                        <td>Percentage rate to be applied to Provisional Sums for overhead charges and profit</td>
                                        <?php $thirtieth_index = isset($atc_data->data_json) ? $atc_data->data_json[29] : '50%'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $thirtieth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">13.8</td>
                                        <td>Adjustment for change in Cost</td>
                                        <td>
                                            <?php $thirtyfirst_index = isset($atc_data->data_json) ? $atc_data->data_json[30] : 'No escalation shall be payable on account of price change for any material, labour or any other reason whatsoever, during the contract period or extended period of contract till completion of work.
                                            The Prime Cost for procurement has been specified for the following materials as per Bill of quantities
                                            WOOVEN VINYL flooring
                                            Kota Stone
                                            Granite Stone
                                            Ceramic Tile
                                            The increase/ decrease in cost due to variation of actual procurement rates shall be adjusted.
                                            Escalation on any other item except prime rate shall not be payable, whatsoever the reasons. Contractor should include the escalation amount for items other than prime rate in their quoted price.'; ?>
                                            <textarea name="data[]" class="form-control" rows="12"><?php echo $thirtyfirst_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.2</td>
                                        <td>Total amount of advance payment (as a percentage of Accepted Contract Amount)</td>
                                        <td>
                                            <?php $thirtysecond_index = isset($atc_data->data_json) ? $atc_data->data_json[31] : '10% of the contract value (Without GST and Labour Cess) shall be paid to the contractor as mobilization advance. The first instalment of 5% shall be released after submission of Bank Guarantee and the second instalment of balance 5% shall be released after full mobilization at site.'; ?>
                                            <textarea name="data[]" class="form-control" rows="3"><?php echo $thirtysecond_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.2.3</td>
                                        <td>Percentage deductions for the repayment of advance payment</td>
                                        <?php $thirtythird_index = isset($atc_data->data_json) ? $atc_data->data_json[32] : '100% Advance shall be recovered from 1<sup>st</sup> RA bills prior to 75% of Total work done value on pro rata basis.'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $thirtythird_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.3</td>
                                        <td>Period of payment (submission of IPC for the work done in last month)</td>
                                        <td>
                                            <?php $thirtyfourth_index = isset($atc_data->data_json) ? $atc_data->data_json[33] : 'On 5th Working day of every month Payment Terms:
                                            Fixed Finishes:
                                            75% of item rate on prorate basis against installation.
                                            15% of item rate on prorate basis against final finishing.
                                            10% of item rate on prorate basis against handing over.
                                            Mill works and doors:
                                            50% on prorate basis against installation of carcass / structure before finishing at factory.
                                            15% on prorate basis against installation of material of carcass / structure.
                                            25% on a prorate basis against fixing of hardware and final finishings.
                                            10% on prorate basis against handing over.'; ?>
                                            <textarea name="data[]" class="form-control" rows="12">
                                            <?php echo $thirtyfourth_index; ?>
                                            </textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.3(b)</td>
                                        <td>Numbers of additional paper copies of statements</td>
                                        <?php $thirtyfifth_index = isset($atc_data->data_json) ? $atc_data->data_json[34] : 'Two'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $thirtyfifth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.3(iii)</td>
                                        <td>Percentage of retention</td>
                                        <td>
                                            <?php $thirtysixth_index = isset($atc_data->data_json) ? $atc_data->data_json[35] : '5% of work done value shall be deducted from each RA bill towards the obligation of defect liability period.
                                            Retention shall be released after successful completion of Defect Notification Period.'; ?>
                                            <textarea name="data[]" class="form-control" rows="3"><?php echo $thirtysixth_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.3(iii)</td>
                                        <td>Limit of retention money (as per percentage of Accepted Contract Amount)</td>
                                        <?php $thirtyseventh_index = isset($atc_data->data_json) ? $atc_data->data_json[36] : '5%'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $thirtyseventh_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.5(b)9i)</td>
                                        <td>Plant and Materials for payment when shipped</td>
                                        <?php $thirtyeighth_index = isset($atc_data->data_json) ? $atc_data->data_json[37] : 'Not Applicable'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $thirtyeighth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.5(c)(i)</td>
                                        <td>Plant and Materials for payment when delivered at site</td>
                                        <?php $thirtyninth_index = isset($atc_data->data_json) ? $atc_data->data_json[38] : 'Not Applicable'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $thirtyninth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.6.2</td>
                                        <td>Minimum amount of Payment Certificate (IPC)</td>
                                        <?php $fourteenth_index = isset($atc_data->data_json) ? $atc_data->data_json[39] : 'Interim payments on monthly on achieving min valuation INR 1,00,00,000/- except first two RA bills.'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fourteenth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.7(a)</td>
                                        <td>Period of payment of Advance Payment to contractor</td>
                                        <?php $fourtyoneth_index = isset($atc_data->data_json) ? $atc_data->data_json[40] : 'As per LOA'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fourtyoneth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.7(b)(i)</td>
                                        <td>Period for the Employer to make interim payments to the contractor under sub-Clause 14.6 [Interim Payment]</td>
                                        <td>
                                            <?php $fourtysecond_index = isset($atc_data->data_json) ? $atc_data->data_json[41] : 'An ad-hoc amount of 65% of net value of running account bill shall be paid within 21 days from the date of submission of bill in all respect. The balance of 35% shall be paid within 30 days from the date of certification of bill.However, if there is a reduction of more than 5% in the billed amount Vs actual amounts, then the system of ad hoc payment would be discontinued from the next RA bill onwards.'; ?>
                                            <textarea name="data[]" class="form-control" rows="5"><?php echo $fourtysecond_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.7(b)(ii)</td>
                                        <td>Period for the Employer to make interim payments to the contractor under sub-Clause 14.13 [Final Payment]</td>
                                        <?php $fourtythird_index = isset($atc_data->data_json) ? $atc_data->data_json[42] : '90 days after certification from Engineer.'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fourtythird_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.7(c)</td>
                                        <td>Period for the employer to make final payments to the Contractor</td>
                                        <?php $fourtyfourth_index = isset($atc_data->data_json) ? $atc_data->data_json[43] : '90 days'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fourtyfourth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.8</td>
                                        <td>Financing charges for delayed payment (percentage points above the average bank short-term lending rate as referred to under sub-paragraph (a))</td>
                                        <?php $fourtyfifth_index = isset($atc_data->data_json) ? $atc_data->data_json[44] : 'Not Applicable'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fourtyfifth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.11(b)</td>
                                        <td>Number of additional paper copies of draft Final Statement</td>
                                        <?php $fourtysixth_index = isset($atc_data->data_json) ? $atc_data->data_json[45] : 'Three'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fourtysixth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.15</td>
                                        <td>Currencies for payment of contract Price</td>
                                        <?php $fourtyseventh_index = isset($atc_data->data_json) ? $atc_data->data_json[46] : 'INR'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fourtyseventh_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.15(a)(i)</td>
                                        <td>Proportion or amount of Local and Foreign Currencies are:
                                            <ul class="nested-list">
                                                <li>Local</li>
                                                <li>Foreign</li>
                                            </ul>
                                        </td>
                                        <td>
                                            <?php $fourtyeighth_index = isset($atc_data->data_json) ? $atc_data->data_json[47] : '100% Nil'; ?>
                                            <textarea name="data[]" class="form-control" rows="3"><?php echo $fourtyeighth_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.15(c)</td>
                                        <td>Currencies and proportion for payment of Delay Damages</td>
                                        <?php $fourtyninth_index = isset($atc_data->data_json) ? $atc_data->data_json[48] : 'INR, 100%'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fourtyninth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">14.15(f)</td>
                                        <td>Rate of exchange</td>
                                        <?php $fifthty_index = isset($atc_data->data_json) ? $atc_data->data_json[49] : 'Not Applicable'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fifthty_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">17.2(d)</td>
                                        <td>Forces of nature, the risks of which are allocated to the contractor</td>
                                        <?php $fiftyfirst_index = isset($atc_data->data_json) ? $atc_data->data_json[50] : 'Not Applicable'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fiftyfirst_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">19.2(1)(b)</td>
                                        <td>Additional amount to be insured (as a percentage of the replacement value, if less or more than 15%)</td>
                                        <?php $fiftysecond_index = isset($atc_data->data_json) ? $atc_data->data_json[51] : 'Not Applicable'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fiftysecond_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">19.2(1)(iv)</td>
                                        <td>List of Exceptional Risks which shall not be excluded from the insurance cover for the Work</td>
                                        <?php $fiftythird_index = isset($atc_data->data_json) ? $atc_data->data_json[52] : 'Not Applicable'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fiftythird_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">19.2.3</td>
                                        <td>Period of insurance required for liability for breach of professional duty</td>
                                        <?php $fiftyfourth_index = isset($atc_data->data_json) ? $atc_data->data_json[53] : 'Not Applicable'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fiftyfourth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">19.2.4</td>
                                        <td>Amount of insurance required for injury to person and damaged to property</td>
                                        <?php $fiftyfifth_index = isset($atc_data->data_json) ? $atc_data->data_json[54] : 'As per the law of land.'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fiftyfifth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">19.2.6</td>
                                        <td>Other insurance required by laws and by local practice</td>
                                        <?php $fiftysixth_index = isset($atc_data->data_json) ? $atc_data->data_json[55] : 'As per law of Republic of INDIA'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fiftysixth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">15.5</td>
                                        <td>Employer's Entitlement to Termination</td>
                                        <td>
                                            <?php $fiftyseventh_index = isset($atc_data->data_json) ? $atc_data->data_json[56] : 'Employer reserve the right to foreclose the works.If this clause is exercised at a stage where the work executed is up to 50% of the contract value, then the foreclosure cost payable to Contractor shall be mutually discussed and agreed cost. If the executed contract value is 50% or more, then 1% of the remaining contract value shall be paid as Foreclosure cost to Contractor.'; ?>
                                            <textarea name="data[]" class="form-control" rows="5"><?php echo $fiftyseventh_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">18.1</td>
                                        <td>Contractors All Risk Policy</td>
                                        <?php $fiftyeighth_index = isset($atc_data->data_json) ? $atc_data->data_json[57] : 'Contractor Scope'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fiftyeighth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">18.2</td>
                                        <td>Insurance for Contractor Equipment</td>
                                        <?php $fiftyninth_index = isset($atc_data->data_json) ? $atc_data->data_json[58] : 'Contractor Scope'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $fiftyninth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">18.3</td>
                                        <td>Insurance for Workmen's Compensation Act</td>
                                        <?php $sixtieth_index = isset($atc_data->data_json) ? $atc_data->data_json[59] : 'Contractor Scope'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $sixtieth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">18.3 & 18.4</td>
                                        <td>Maximum amount of third party Insurance.</td>
                                        <td>
                                            <?php $sixtyfirst_index = isset($atc_data->data_json) ? $atc_data->data_json[60] : 'Insurance to be taken in the joint names of the Employer and the Contractor (being the Principal Beneficiary) against such risks, before commencement of the Works. The minimum limit of the coverage under the Policy shall be Rs 50.00 Lakhs per accident or occurrence, there being no limit on the number of such accidents or occurrences.'; ?>
                                            <textarea name="data[]" class="form-control" rows="4"><?php echo $sixtyfirst_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">21.1</td>
                                        <td>Time of appointment of Dispute Avoidance /Adjudication Board</td>
                                        <?php $sixtysecond_index = isset($atc_data->data_json) ? $atc_data->data_json[61] : '28 days'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $sixtysecond_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">21.1</td>
                                        <td>The DAAB shall comprise</td>
                                        <?php $sixtythird_index = isset($atc_data->data_json) ? $atc_data->data_json[62] : 'Three members'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $sixtythird_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">21.1</td>
                                        <td>List of proposed members of DAAB
                                            <ul class="nested-list">
                                                <li>Proposed by employer</li>
                                                <li>Proposed by contractor</li>
                                            </ul>
                                        </td>
                                        <td>
                                            <?php $sixtyfourth_index = isset($atc_data->data_json) ? $atc_data->data_json[63] : 'TBD TBD'; ?>
                                            <textarea name="data[]" class="form-control" rows="3"><?php echo $sixtyfourth_index; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">21.2</td>
                                        <td>Appointing entity (official) for DAAB members</td>
                                        <?php $sixtyfifth_index = isset($atc_data->data_json) ? $atc_data->data_json[64] : 'Employer'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $sixtyfifth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td class="clause-number">15.3</td>
                                        <td>Arbitration: Rule Place of Arbitration</td>
                                        <?php $sixty_sixth_index = isset($atc_data->data_json) ? $atc_data->data_json[65] : 'Arbitration and conciliation act 1996 Mumbai'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $sixty_sixth_index; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Labour cess</td>
                                        <?php $sixty_seventh_index = isset($atc_data->data_json) ? $atc_data->data_json[66] : 'Labour cess shall be paid extra.'; ?>
                                        <td><input type="text" class="form-control" name="data[]" value="<?php echo $sixty_seventh_index; ?>"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                                <button class="btn btn-info only-save customer-form-submiter">
                                    <?php echo _l('submit'); ?>
                                </button>

                            </div>
                            <?php echo form_close(); ?>

                            <div class="footer">
                                <p>Document: Appendix to Contract - Interior Works for <?php echo tender_name_by_id($tender_data[0]['tender_id']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php init_tail(); ?>
</body>

</html>