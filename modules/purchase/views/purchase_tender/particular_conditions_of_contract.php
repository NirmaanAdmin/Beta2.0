<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Particular Conditions of Contract - HTML</title>
    <style>
        /* .doc {
            max-width: 900px;
            margin: 24px auto 80px;
            padding: 0 20px;
        } */

        h1,
        h2,
        h3,
        h4,
        h5 {
            line-height: 1.3;
            margin: 1.2em 0 0.5em;
        }

        h1 {
            font-size: 1.9rem;
        }

        h2 {
            font-size: 1.6rem;
        }

        h3 {
            font-size: 1.3rem;
        }

        p {
            margin: 0.5em 0;
        }

        hr {
            border: 0;
            border-top: 1px solid #000;
            margin: 24px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0 22px;
        }

        th,
        td {
            border: 1px solid #000000;
            padding: 8px 10px;
            vertical-align: top;
        }

        th {
            background: #f7f7f7;
            font-weight: 600;
        }

        .meta {
            color: var(--muted);
            font-size: 0.95rem;
        }

        .banner {
            padding: 12px 14px;
            border: 1px solid var(--border);
            background: #fafafa;
            border-radius: 8px;
            margin: 16px 0 22px;
        }

        .footer-note {
            color: var(--muted);
            font-size: 0.9rem;
            margin-top: 40px;
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
                            <div class="doc">
                                <h1>Particular Conditions of Contract</h1>
                                <p>The Conditions of Contract comprise the “<strong>General</strong><strong> </strong><strong>Conditions</strong>”,
                                    which form part of the “<strong>Conditions</strong><strong> </strong><strong>of</strong><strong>
                                    </strong><strong>Contracts</strong><strong> </strong><strong>for Construction</strong><strong>
                                    </strong><strong>for</strong><strong> </strong><strong>Building</strong><strong>
                                    </strong><strong>and</strong><strong> </strong><strong>Engineering</strong><strong>
                                    </strong><strong>Works</strong><strong> </strong><strong>designed</strong><strong>
                                    </strong><strong>by</strong><strong> </strong><strong>the</strong><strong>
                                    </strong><strong>Employer”;</strong><strong> </strong><strong>First</strong><strong>
                                    </strong><strong>Edition</strong><strong> </strong><strong>1999</strong><strong>
                                    </strong><strong>published</strong><strong> </strong><strong>by</strong><strong> </strong><strong>the Federation
                                    </strong><strong>Internationale</strong><strong> des </strong><strong>Ingénieurs</strong><strong>- Conseils
                                        (FIDIC) </strong>shall be applicable for this Contract. The Contractor is deemed to be acquainted with and shall
                                    be in possession of the “General Conditions”.</p>
                                <p>PARTICULAR CONDITIONS OF CONTRACT</p>
                                <p></p>
                                <?php echo form_open(admin_url('purchase/appendix_to_contract_add_update'), array('id' => 'appendix-to-contract-form'));
                                ?>
                                <input type="hidden" name="tender_id" value="<?php echo $tender_data[0]['tender_id']; ?>">
                                <input type="hidden" name="vendor_id" value="<?php echo $tender_data[0]['vendor_id']; ?>">
                                <input type="hidden" name="project_id" value="<?php echo $tender_data[0]['project_id']; ?>">
                                <table>
                                    <tr>
                                        <th>Clause</th>
                                        <th>Title</th>
                                        <th>Particulars</th>
                                        <th>Remarks</th>
                                    </tr>
                                    <tr>
                                        <td>Clause 1.0</td>
                                        <td>GENERAL PROVISIONS</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 1.1.2.9</td>
                                        <td>DAB</td>
                                        <?php $first_index = isset($atc_data->data_json) ? $atc_data->data_json[0] : 'Delete Sub-Clause 1.1.2.9 "DAB" and substitute with: "Three persons as mutually decided by the Employer and the Contractor to be appointed for Dispute Adjudication Board."'; ?>
                                        <td><textarea rows="5" cols="90" name="data[]" class="form-control" style="width:100%; height:100px;"><?= $first_index ?></textarea></td>

                                        <td><textarea name="data_remarks[]" class="form-control" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 1.1.3.1</td>
                                        <td>Base Date</td>
                                        <?php $second_index = isset($atc_data->data_json) ? $atc_data->data_json[1] : 'Delete Sub-Clause 1.1.3.1 (the definition of Base Date) and substitute with: "Base Date; means the Particular Calendar date of issue of tender document."'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $second_index ?></textarea></td>
                                        <td><textarea name="data_remarks[]" class="form-control" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 1.1.4.8</td>
                                        <td>Local Currency</td>
                                        <?php $third_index = isset($atc_data->data_json) ? $atc_data->data_json[2] : 'Delete Sub-Clause 1.1.3.1 (the definition of Base Date) and substitute with: "Base Date; means the Particular Calendar date of issue of tender document."'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $third_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 1.1.6.2</td>
                                        <td>Country</td>
                                        <?php $fourth_index = isset($atc_data->data_json) ? $atc_data->data_json[3] : 'Delete Sub-Clause 1.1.6.2 and substitute with:"Country means Republic of India."'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $fourth_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 1.1.6.5</td>
                                        <td>Laws</td>
                                        <?php $fifth_index = isset($atc_data->data_json) ? $atc_data->data_json[4] : '"Laws" of Republic of India."'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $fifth_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub-Clause 1.5</td>
                                        <td>Priority of Documents</td>
                                        <?php $sixth_index = isset($atc_data->data_json) ? $atc_data->data_json[5] : 'Delete Sub-Clause 1.5 and substitute with:"The documents forming the Contract are to be taken as mutually explanatory of one another. For the purposes of interpretation, the priority of the documents shall be in accordance with the following sequence:the Contract Agreement (if any),the Letter of Acceptance,the Letter of Tender,the Particular Conditions,the Conditions of Contract for Construction First Edition 1999 published by the Federation Internationale des Ingenieurs-Conseils (FIDIC),Contractors General Obligations,the Specification,the Drawings,Environmental, Health  Safety (EHS)Manual, andthe Schedules and any other documents forming part of the Contract.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $sixth_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <?php $seventh_index = isset($atc_data->data_json) ? $atc_data->data_json[6] : 'If an ambiguity or discrepancy is found in the documents, the Engineer shall issue any necessary clarification or instruction'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $seventh_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 1.6</td>
                                        <td>Contract Agreement</td>
                                        <?php $eighth_index = isset($atc_data->data_json) ? $atc_data->data_json[7] : 'Replace "28 days" by "45 days" in first line.Replace 3rd sentence by:"The cost of stamp duties and similar charges (if any) imposed by law in connection with entry into the Contract Agreement shall be borne by the Contractor".Add the following paragraph to the end of Sub- Clause 1.6The Employer and the Contractor each binds himself, his partners, successors, assigns and legal representatives to the other party here to and to the partners, successors, assigns and legal representatives of such other party in respect of all covenants, agreements and obligations contained in the contract documentsCounterpartsThe Contract Documents may be executed in one or more counterparts, all of whom shall be considered one and the same agreement and shall become effective when one or more such counterparts have been signed by each of the Parties and delivered to the other Party.Entire Contract DocumentsThe Contract Documents contains the entire agreement and understanding between the Parties hereto with respect to the subject matter hereof and supersede all prior agreements, arrangements or understandings, if any, whether oral or in writing, between the Parties on the subject matter hereof or in respect of matters dealt with herein.SeverabilityIn case any provision of the Contract Documents (or any portion thereof) or the application of any such provision (or any portion thereof) to any Person or circumstance shall be held invalid, illegal or unenforceable (in whole or in part) in any respect under any Applicable Law, by a court of competent jurisdiction, such invalidity, illegality or unenforceability shall not affect any other provision hereof (or the remaining portion thereof) or the application of such provision toany other Persons or circumstances and the'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $eighth_index ?> ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <?php $ninth_index = isset($atc_data->data_json) ? $atc_data->data_json[8] : 'remainder of the Contract Documents shall remain legally enforceable.No Partnership or AgencyNone of the provisions herein shall be deemed to constitute a partnership between the Parties and no Party shall have any authority to bind or shall be deemed to be the agent of any other Party inany manner whatsoever.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $ninth_index;?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 1.7</td>
                                        <td>Assignment</td>
                                        <?php $tenth_index = isset($atc_data->data_json) ? $atc_data->data_json[9] : 'Entire Sub clause is replaced by:The Employer may assign the whole or any part of the Contract or any benefit or interest in or under the Contract. The Contractor shall not assign the whole or any part of the Contract without the Employer prior written agreement,which shall be at the Employers sole discretion.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $tenth_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 1.12</td>
                                        <td>Confidential Details</td>
                                        <?php $elevanth_index = isset($atc_data->data_json) ? $atc_data->data_json[10] : 'Add new paragraph:The Contractor shall not disclose the details of the Contract, except to the extent necessary to carry out obligations under it or to comply with applicable laws. The Contractor shall not publish, permit to be published, or disclose any particulars of the works in any trade or technical paper or elsewhere without the prior written consent of the Employer. The Contractor shall impose similar confidential condition on Supplier(s) andSub-Contractor(s).'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $elevanth_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 1.13</td>
                                        <td>Compliance with laws</td>
                                        <?php $twelveth_index = isset($atc_data->data_json) ? $atc_data->data_json[11] : 'Sub paragraph (b) to be replaced by the following:-The Contractor shall give all notices, pay all taxes, duties and fees, and obtain all permits, licences and approvals, as required by the Laws in relation to the execution and completion of the Works and the remedying of any defects; and the Contractor shall indemnify, keep indemnified and hold the Employer harmless against and from the consequences of any failure to do so including statutory laws, Labour laws, environment laws.Add the following paragraphs: Representations, Warranties and CovenantsThe Contractor represents and warrants to theEmployer as follows:i. The Contractor is duly organized, validly existing and in good standing order under the laws of the jurisdiction of itsincorporation;'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $twelveth_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <?php $tirteenth_index = isset($atc_data->data_json) ? $atc_data->data_json[12] : 'The Contractor is competent to enter in contract, has the full power and authority to execute, deliver and perform its obligations under the Contract Documents and carry out the transaction contemplated hereby;The Contractor has taken all corporate and other actions under the applicable Laws and it has the power under its Articles and Memorandum of Association to execute, deliver and perform its obligations under the Contract Documents;The Contractor has obtained all permits and consent which are valid and subsisting under the applicable Laws for the execution of Project under the Contract Documents and it further represents and warrants that he is not under any restriction whatsoever nor does it require any approval/consents for the execution, delivery, performance and implementation of the Project;The Contractor has the requisite knowledge, skill, expertise and experience to perform and implement the Work in relation to the Project;The Contractor has the financial standing and the capacity to undertake the construction and completion of the Project in accordance with the terms of the Contract Documents;The Contract Documents constitutes legal, valid and binding obligation any encumbrances;The execution, delivery and performance of the Contract will not conflict with, result in the breach of, constitute a default under or accelerate performance required by any of the terms of any applicable Laws or any covenant, agreement, understanding,decree or order to which it is a party or'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $tirteenth_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <?php $fourthenth_index = isset($atc_data->data_json) ? $atc_data->data_json[13] : 'by which it or any of his properties or assets is bound or affected;There are no actions, suits, proceedings, or investigations pending or, to the best of the Contractors knowledge, threatened against it at law or in equity before any court or before any other judicial, quasi-judicial or other authority, the outcome of which may result in the breach of or constitute a default of the Contractor under the Contract Documents or which individually or in the aggregate may result in any material adverse effect on its business, properties or assets or its condition, financial or otherwise, or in any impairment of its ability to perform its obligations and duties under the Contract Documents;The Contractor has no knowledge of any violation or default with respect to any order, writ, injunction or any decree of any court or any legally binding order of any Governmental Authority which may result in any material adverse effect or impairment of the Contractors ability to perform its obligations enforceable against him in accordance with the terms of the Contract Documents;The Contractor shall have no right and interest in the Project and the same always shall vest in the Employer, free and clear of all and duties under the Contract Documents or to undertake the Project;The Contractor has complied with all applicable Laws and has not been subject to any fines, penalties, injunctive relief or any other civil or criminal liabilities which in the aggregate have or may have material adverse effect on its financial condition or its ability to perform its obligations and duties under the Contract Document and undertake theProject; and'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $fourthenth_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <?php $fiftheenth_index = isset($atc_data->data_json) ? $atc_data->data_json[14] : 'The Contractor at any stage during the tender process or afterwards has not added, deleted, altered or otherwise changed in any manner, whether intentionally or by mistake ,any condition, term or intention of the Contract documents including the addenda / corrigenda or revisions thereof, without the approval of the Employer.No representation or warranty by the Contractor contained herein or in any other document furnished by it to Employer, or to any Governmental Authority in relation to applicable permits contains or will contain any untrue statement of material fact or omits or will omit to state a material fact necessary to make such representation or warranty not misleading. The Contractor shall appoint the adequate numbers of Quantity Surveyor to prepare a detailed floor wise BOQ within3 months from the date of Letter of Intent (LOI).The Contractor shall raise the Request for Information (RFI) for any technical details, specifications, Drawings, Conflicting, insufficient or unclear information, instructions, errors in the documents, which the Contractor feels are necessary to commence any activity, to the Employer in writing within 30 (Thirty) days in advance before the planning start of activity.Every month the Contractor shall prepare and present through PPT a report on "Health of Project" covering progress, resources, decisions, etc. related to work w.r.t. schedule to the Employer.The Employer represents and warrants to the Contractor as follows:'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $fiftheenth_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <?php $sixtheenth_index = isset($atc_data->data_json) ? $atc_data->data_json[15] : 'The Employer is validly existing and in good standing order under the laws of the jurisdiction of its incorporation;The Employer is competent to enter in contract, has the full power and authority to execute, deliver and perform the Contract Documents and carry out the transaction contemplated hereby;The Employer has taken all corporate and other actions under the applicable Laws and it has the power to execute, deliver and perform its obligations under the Contract Documents under its charter documents.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $sixtheenth_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 2.0</td>
                                        <td>The Employer</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>2.1</td>
                                        <td>Right of access to site:</td>
                                        <?php $sevenththeenth_index = isset($atc_data->data_json) ? $atc_data->data_json[16] : 'Delete the Sub-paragraph (b).'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $sevenththeenth_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 2.2</td>
                                        <td>Permits, Licences or Approvals</td>
                                        <?php $eighteenth_index = isset($atc_data->data_json) ? $atc_data->data_json[17] : 'Entire sub clause is replaced by following:The Contractor shall, in relation to permits, licenses and approvals which is to be obtained in accordance with the Contract, be deemed to have satisfied himself as to which permits, licenses, and approvals are required by law and it is his absolute responsibility to obtain any permits, licences or approvals under the Contract. The Contractor shall also be responsible for liaison with authorities to obtain required statutory approvals related to the commissioning of equipment and works, as well as pay all fees and incidental expenses for such approvals.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $eighteenth_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 2.4</td>
                                        <td>Employer's FinancialArrangements</td>
                                        <?php $nineteenth_index = isset($atc_data->data_json) ? $atc_data->data_json[18] : 'Delete sub-clause entirely.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $nineteenth_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 2.5</td>
                                        <td>Employer's Claims</td>
                                        <?php $twentiethth_index = isset($atc_data->data_json) ? $atc_data->data_json[19] : 'After 2nd paragraph, add: "Furthermore the Employers claims shall include but shall not be limited to the payments by the Employer to other contractors to mobilize resources and/or execute the works as set out under Sub-Clauses 4.6, 4.8, 7.6, 8.6, 11.4 including Employer administration costs equivalent to 20% of the amounts paid to such contractor to mobilize such resources.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $twentiethth_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 3.1</td>
                                        <td>EmployerRepresentative'sDuties and Authority</td>
                                        <?php $twentieone_index = isset($atc_data->data_json) ? $atc_data->data_json[20] : 'Insert additional paragraphs:Any drawing issued by the Engineer identified as "Issued For Construction" shall be deemed as a Employer Representatives Instruction to executethe works. If the Contractor deems that all'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $twentieone_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <?php $twentietwo_index = isset($atc_data->data_json) ? $atc_data->data_json[21] : 'drawings issued represent a cumulative variation of more than 2% of the Contract Price, on account of quantity variations only, it shall bring it to the notice of the Engineer who shall obtain the Employers approval for this variation and issue a specific Instruction under clause 13.1. The Contractor shall refer to the Bill of Quantity and Specifications to ascertain the scope of Works to be executed, but the Drawings shall take precedence in case of any discrepancy, which the Contractor shall report to the Engineer before executing the Works.Notwithstanding the obligation, as set out above, to obtain approval, if, in the opinion of the Employer Representative, an emergency occurs affecting the safety of life or of the Works or of adjoining property, he may, without relieving the Contractor of any of his duties and responsibility under the Contract, instruct the Contractor to execute all such work or to do all such things as may, in the opinion of the Employer Representative, be necessary to abate or reduce the risk. The Contractor shall forthwith comply, despite the absence of approval of the Employer Representative, with any such instruction of the Employer Representative. The Engineer shall determine an addition to the Contract Price, in respect of such instruction, in accordance with Clause 13 and shall notify the Contractor accordingly, with a copy to the Employer.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $twentietwo_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub-Clause 3.4</td>
                                        <td>Replacement of the Employer Representative</td>
                                        <?php $twentiethree_index = isset($atc_data->data_json) ? $atc_data->data_json[22] : 'Replace "42 days" by "5 working days"Delete: "The Employer shall not replace the Engineer (...) to the Employer with supporting particulars".Add new paragraph:"The Employer may appoint one of its employees as the Employer Representative".'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $twentiethree_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 4.0</td>
                                        <td>The Contractor</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 4.2</td>
                                        <td>Performance Security</td>
                                        <?php $twentiefour_index = isset($atc_data->data_json) ? $atc_data->data_json[23] : 'Add the following paragraph after the 2nd paragraph:The Performance Security shall be in the form of a bank Guarantee from a nationalized or scheduled Indian bank in or in the form of an single irrevocable and unconditional bank guarantee of equivalent amount in the form annexed to the Particular Conditions of Contract from a nationalized or scheduled Indian bank, but not from any co-operative bank.The Performance Security shall only be held valid when all following conditions are met:Receipt of original Performance Security in hard copyReceipt of a copy of the Performance Security directly from the issuing bankThe authenticity of the Performance Security has been verified by the Employer and notified as such to the ContractorIn 3rd paragraph, replace first sentence by:"The Contractor shall ensure that the Performance Security is valid and enforceable until 60 days after Taking Over CertificateAdd following paragraph:Without limitation to the provisions of the rest of this Sub-Clause, whenever the Engineer determines an addition or a reduction to the Contract Price as a result of a change in cost and/or legislation, or as a result of a Variation, amounting to more than 20 percent of the portion of the Contract Price payable in a specific currency, the Contractor shall at the Employer Representatives request promptly increase, or may decrease, as the case may be, the value of the Performance Security in that currency by an equal percentage.All costs related to compliance with the requirements of this Sub Clause shall be borne by the Contractor. The Contractor shall not be entitled to receive any financial interest on the Performance Security.Replace 4th paragraph " The Employer shall not make claim event of"By'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $twentiefour_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <?php $twentiefive_index = isset($atc_data->data_json) ? $atc_data->data_json[24] : '" The Employer shall be entitled to make claim under the Performance Security under the Contract in the event of:"Replace "42 days" by "30 days" in sub paragraph(c )Replace the last paragraph by"The Employer shall return the Performance Security to the Contractor within 21 days after receiving Statement at Completion as per sub clause 14.10 "'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $twentiefive_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 4.4</td>
                                        <td>Subcontractors</td>
                                        <?php $twentiesix_index = isset($atc_data->data_json) ? $atc_data->data_json[25] : 'Additional Paragraph:The Contractor shall ensure that the requirements imposed on the Contractor by Sub- Clause 1.12 [Confidential Details] apply equally to each Subcontractor.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $twentiesix_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 4.6</td>
                                        <td>Co-operation</td>
                                        <?php $twentieseven_index = isset($atc_data->data_json) ? $atc_data->data_json[26] : 'Additional Paragraph:Contractor shall provide attendance to other contractors as specified in the Contractor General Obligations.Without prejudice to any other provisions, if the Contractor fails to provide adequate attendance as per specifications and fails to take corrective action after receiving the Employer Representatives written notification, then the Employer shall be entitled to employ and pay other contractors to provide the required attendance facilities. The Contractor shall, subject to Sub-Clause 2.5 pay to the Employer all costs arising from this failure plus 20% for theEmployers administration costs.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $twentieseven_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 4.8</td>
                                        <td>Safety Procedures</td>
                                        <?php $twentieneight_index = isset($atc_data->data_json) ? $atc_data->data_json[27] : 'Additional Paragraphs:"The Contractor shall comply with all provisions of the (i) Environmental, Health  Safety (EHS) Manual included in this Contract.The Engineer may charge specific penalties on the Contractor for repeated violations of safety or housekeeping specifications, at the rate of INR 1000/- per incidence for minor violations up to INR 10,000/- per incidence for major violation.Without prejudice to any other provisions, if the Contractor fails to provide adequate equipment and/or enforce HSE specifications at Site, and fails to take corrective action after receiving the EmployerWithout prejudice to any other provisions, if the Contractor fails to provide adequate equipment and/or enforce EHS specifications at Site, and fails to take corrective action after receiving the Engineers written notification, then the Employer shall be entitled to employ and pay other contractors to provide the required equipment and facilities. The Contractor shall, subject to Sub-Clause 2.5 pay to the Employer all costs arising from this failure plus 20% for the Employers administration costs."'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?=  $twentieneight_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 4.20</td>
                                        <td>Employer's Equipment and Free-issue Materials</td>
                                        <?php $twentienine_index = isset($atc_data->data_json) ? $atc_data->data_json[28] : 'Additional Paragraphs:The Contractor shall submit to the Employer at least four weeks in advance his requirement for materials. The materials shall be ordered by the Employer based on the indents submitted by the Contractor.List of material supplied by the Employer together with allowable wastage is specified in the Appendix to Tender. All materials supplied to the Contractor as aforesaid which are not incorporated into the Works shall be returned to the Employer at the Contractors expense.Recovery shall be made for that part of material found deteriorated or damaged which may have been caused to said materials while in custody of the Contractor, which condition will be determined solely by the Employer.Any wastage or breakage over the percentage stated in Annexure 2 and any loss, deteriorated, damaged will be recovered at a rate which shall be 1.25% of the average procurement price.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $twentienine_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 4.22</td>
                                        <td>Security of the Site</td>
                                        <?php $thirty_index = isset($atc_data->data_json) ? $atc_data->data_json[29] : 'Additional Paragraph:The Contractor shall be responsible for securing all materials, plant and equipment and hereby indemnifies and shall keep the Employer indemnified against all claims, damages, lossesand expenses in this respect.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $thirty_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 4.23</td>
                                        <td>Contractor's Operations on Site</td>
                                        <?php $thirtyone_index = isset($atc_data->data_json) ? $atc_data->data_json[30] : 'Add the following paragraphTaking Over of works in section or whole does not relieve the Contractor from the obligations towards general site attendance / infrastructure facilities to be provided to other contractors asdefined in the Contract.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $thirtyone_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 6.0</td>
                                        <td>Staff and Labour</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 6.4</td>
                                        <td>Labour Laws</td>
                                        <?php $thirtytwo_index = isset($atc_data->data_json) ? $atc_data->data_json[31] : 'Add the following paragraphsThe Contractor will be completely responsible for complying with all statutory provisions and Obligations of various acts, laws, rules and regulations, policies etc. including but not limited to the Contract Labour Act against risk  cost, Labour Insurance Policies, Workmen Compensation Liability under the Workmen Compensation Act and all amendments thereto ESIC Act, PF Act, rules, regulations etc. will be complied and followed completely by the Contractor and shall be to the Contractors account. The Contractor shall provide documentary evidence by submitting copies of such policies to the Employer.In every case in which by virtue of any sections of Workmens Compensation Act, if the Employer is obliged to pay compensation to any workmen / person(s) employed over the entire work site, in execution of the work order / agreement, the Employer will recover from the Contractor the compensation payable / paid by deducting it from deposits or any other deposits, retention monies or from any sums due from the Employer to the Contractor.Contractor must produce Certificate from Chartered Accountant on six- monthly basis for compliance of statutory requirements.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $thirtytwo_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 7.0</td>
                                        <td>Materials and Workmanship</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 7.6</td>
                                        <td>Remedial work</td>
                                        <?php $thirtythree_index = isset($atc_data->data_json) ? $atc_data->data_json[32] : 'At the end of the paragraph, add:"plus 20% for the Employers administration cost. This is without prejudice to the Employers rights under the Contract"'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $thirtythree_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 7.8</td>
                                        <td>Royalties</td>
                                        <?php $thirtyfour_index = isset($atc_data->data_json) ? $atc_data->data_json[33] : 'Insert the following to the end of Sub-Clause 7.8: "The Contractor shall indemnify and hold harmless the Employer and anyone directly or indirectly employed by the Employer from and against all claims, damages, losses and expenses, including attorneys fees arising out of any infringement of such rights during or after completion of the Work and shall defend all such claims in connection with any alleged infringement of such rights."'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $thirtyfour_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 8.0</td>
                                        <td>Commencement Delays and Suspension</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 8.1</td>
                                        <td>Commencement of Works</td>
                                        <?php $thirtyfive_index = isset($atc_data->data_json) ? $atc_data->data_json[34] : 'In the first paragraph, replace "42 days" by "90 days" and add: "The Contractor shall not be entitled to any cost adjustment under Sub Clause13.8 on account of this period between the Letterof Acceptance and the Commencement Date".'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $thirtyfive_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 8.4</td>
                                        <td>Extension of Time for Completion</td>
                                        <?php $thirtysix_index = isset($atc_data->data_json) ? $atc_data->data_json[35] : 'Delete sub paragraph (a ) and substitute with"Not Applicable'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $thirtysix_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 8.6</td>
                                        <td>Rate of Progress</td>
                                        <?php $thirtyseven_index = isset($atc_data->data_json) ? $atc_data->data_json[36] : 'Additional Paragraph:Without prejudice to any other provisions, if the Contractor fails to provide sufficient resources to complete the Works within the Time for Completion, and fails to take corrective action after receiving the Engineers written notification, then the Employer shall be entitled to employ and pay other contractors to provide the required resources (persons, tools, plants and machineries) or execute part of the Works at the Contractors risk. If the Contractor fails to provide as set out hereinabove, then the Contractor shall be liable to pay to the Employer 20% of the administration costs in addition to the Employers claims as mentioned in sub clause No.2.5.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $thirtyseven_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 8.7</td>
                                        <td>Delay Damages</td>
                                        <?php $thirtyeight_index = isset($atc_data->data_json) ? $atc_data->data_json[37] : 'Additional Paragraphs:If the Contractor fails to achieve any of the milestones indicated in Contractors programme under Sub-Clause 8.3 or as specified in the Appendix to Tender, the Engineer may withhold temporary amounts from the Contractors payments, at the rate indicated in the Appendix to Tender applied to the Section of the Works which is delayed. This retention shall be released as soon as the Contractor achieves the subsequent milestones within the specified time, otherwise this retention shall be treated as part of the delay damages paid to the Employer under this Sub- Clause.If the Works are to be completed in Sections within the respective time specified in the Appendix to Tender then the Contractor shall pay delay damages to the Employer for any delay in completing the respective Sections of the Works at the rate specified in the Appendix to Tender applied to the value of the works delayed.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $thirtyeight_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 8.8</td>
                                        <td>Suspension of Work</td>
                                        <?php $thirtynine_index = isset($atc_data->data_json) ? $atc_data->data_json[38] : 'Add:Within five (5) working days after receiving the notice of suspension from the Employer Representative, the Contractor shall submit to the Engineer its proposed demobilization plan including the list of Contractors Personnel, its Subcontractors Personnel, and the Contractors Equipment who are required to remain at Site to protect the Works. As soon as practicable after receiving such proposal, the Engineer shall proceed in accordance with sub-clause 3.5 [Determinations].'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $thirtynine_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 8.9</td>
                                        <td>Consequences of Suspension</td>
                                        <?php $fourty_index = isset($atc_data->data_json) ? $atc_data->data_json[39] : 'Additional ParagraphCosts incurred by the Contractor shall be paid as follows:Costs associated with Contractors Equipment shall be calculated as 1% of their depreciated book value per month of suspension.Costs associated with items under the Preliminaries BOQ shall be paid as per the said BOQ rate minus ten percent (10%)Other Costs shall be paid as per actual direct costs incurred by the Contractor,'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?= $fourty_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <?php $fourtyone_index = isset($atc_data->data_json) ? $atc_data->data_json[40] : 'against contemporary records, plus tenpercent (10%) for the Contractors overhead and profit.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?=  $fourtyone_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 11</td>
                                        <td>Defects Liability</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 11.4</td>
                                        <td>Failure to Remedy Defects</td>
                                        <?php $fourtytwo_index = isset($atc_data->data_json) ? $atc_data->data_json[41] : 'At the end of paragraph (a) add "plus 20% for theEmployers administration costs".'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?=  $fourtytwo_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 12.0</td>
                                        <td>Measurement and Evaluation</td>
                                        <?php $fourtythree_index = isset($atc_data->data_json) ? $atc_data->data_json[42] : 'At the end of paragraph (a) add "plus 20% for theEmployers administration costs".'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?=  $fourtythree_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 12.3</td>
                                        <td>Evaluation</td>
                                        <?php $fourtyfour_index = isset($atc_data->data_json) ? $atc_data->data_json[43] : 'In 2nd paragraph, remove conditions (a) (i), (ii), (iii), and (iv)Add the followingThe actual quantities of individual items can vary to any extent from such provisional quantities. The Employer reserves the right to increase or decrease any of the quantities or to totally omit any item of Work and the Contractor shall not claim any extras or damages for increase / decrease in rates or overheads / profit or loss of turnover etc. on these grounds."Contractors overhead and profit shall be 15% of actual direct costs.Direct cost is the sum of the direct materials costs and direct labor costs excluding food, accommodation, and other overheads of labour"'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?=  $fourtyfour_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 13.0</td>
                                        <td>Variations and Adjustments</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 13.3</td>
                                        <td>Variation Procedure</td>
                                        <?php $fourtyfive_index = isset($atc_data->data_json) ? $atc_data->data_json[44] : 'Insert the following to the end of Sub-Clause 13.3:In case the Contractor does not agree to the rate determined by the Engineer under Sub Clause 12.3, without any prejudice to any other provision the Employer shall be entitled to employ and pay other contractors to execute the works subject of the Variation, provided always that if the Contractor commences works or incurs any expenditure in regard thereto before the rates have been determined as hereinbefore mentioned, then in such case the Contractor shall only be entitled to be paid in respect of works carried out or expenditure incurred prior to the said notice, subjected to verification  acceptance by Employers Representative.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?=  $fourtyfive_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 13.8</td>
                                        <td>Adjustments for Changes in Cost</td>
                                        <?php $fourtysix_index = isset($atc_data->data_json) ? $atc_data->data_json[45] : 'NA'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?=  $fourtysix_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 14.0</td>
                                        <td>Contract Price and Payment</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 14.2</td>
                                        <td>Advance payment</td>
                                        <?php $fourtyseven_index = isset($atc_data->data_json) ? $atc_data->data_json[46] : 'Amend paragraph 5 as follows:Replace a  b from paragraph 5 by the following:-Start repayment of advance payment. Shall commence from 3rd Payment Certificate i.e. 2nd Interim payment against work done.100% Advance shall be recovered from RA bills prior to 75% of Total work done value.'; ?>
                                        <td><textarea class="form-control" style="width:100%; height:100px;"><?=  $fourtyseven_index ?></textarea></td>
                                        <td><textarea class="form-control" name="data_remarks[]" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 14.6</td>
                                        <td>Issue of Interim Payment Certificates</td>
                                        <td><textarea style="width:100%; height:100px;">Add following paragraph:With every invoice for progress payment, the Contractor shall also submit a declaration, clearly stating that all the payments due to the Contractor's vendors/ suppliers for the Works and subcontractors including the labour contractors engaged for the Works have been made up to the period ending 60 days previous to the date of invoice. The aforesaid declaration shall cover the following payments but shall not be limited to the same.Salaries and all statuary dues ofContractor's employees engaged for theWorks;Payments to the subcontractors and labour contractors up to the period stated above.Payment to all the suppliers and vendors for the Works.All the statutory payments towards Income Tax, Sales Tax, etc.Payment of the net liability of the GSTMeasurement sheetsMaterial Reconciliation of free issue materialsMaterial Inspection reportWork inspection reportLedger of Non-compliance reportMaterial invoicesIn case the Engineer wants to see the documentary evidence for all or any of the above, the Contractor shall produce the same without delay. In case the Contractor fails to submit such documentary evidence, it shall be deemed thatthe Contractor has not fulfilled the obligations</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">towards the Contractor's sub-contractors / suppliers / labour contractors / vendors / staff / statutory compliances. Under such conditions, the Employer shall have the right to withhold further payments to the Contractor till the evidences are submitted and Employer shall have the right to pay them directly on behalf of the Contractor and debit from the Contractor's account to which the contractor shall have no objection whatsoever.Further, the Engineer shall give a notice to the Contractor to submit a statement of outstanding dues along with a "No Objection Certificate (NOC)" letter authorizing the Employer to make direct payments to the sub vendors/suppliers/sub-contractors and deduct such amounts from the amounts due to the Contractor or recover from the securities available with the Employer in the form of performance security or any other bank guarantee provided by the Contractor.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 14.7</td>
                                        <td>Payment</td>
                                        <td><textarea style="width:100%; height:100px;">Replace "56 days" in line 1 of sub Para (b) by "30 days".</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 14.8</td>
                                        <td>Delayed Payment</td>
                                        <td><textarea style="width:100%; height:100px;">Clause deleted.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 15.0</td>
                                        <td>Termination by Employer</td>
                                        <td><textarea style="width:100%; height:100px;">"Employer" to be read as "Employer"</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 15.2</td>
                                        <td>Termination by Employer</td>
                                        <td><textarea style="width:100%; height:100px;">Add the following events of termination after (f):makes a general assignment of this contract for the benefit of his creditors,files any petition against the Employer and/or against the debtor's for taking advantage of any debtor's act or to reorganize under bankruptcy or similar laws,fails to deploy /supply sufficient skilled superintendent and workmen or suitable materials and/or equipment as required under the contract/agreed schedule for a continuous period of two months,fails to make payments to sub-contractors or for labour and vendors for material supply and/or equipment, continuously beyond a period of 60 days from the due date of such payment as the case may be, to</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">sub-contractors or non-compliance of laws, ordinances, rules, regulations or orders of any public/statutory body having jurisdiction,abandons the Works continuously for a period of 21 days not arising out of force majeure event, ordies or becomes insane or is imprisoned, or is bankrupt or adjudged insolvent, or fails to promptly comply with the requirements of any amendment on the scope of the existing work order, orif a trustee or court receiver is appointed for the Contractor or for any of his propertydisregards the authority of the Employer or neglects to execute the Work in accordance with the contract documents including requirements of the progress schedule, orfails to promptly comply with the requirements of any amendment on the scope of the existing work order, orInsert after first sentence in the 5th paragraph"The Employer reserves the right to retain/procure some/ all of the site infra structure facilities/ equipment created by the Contractor in connection with the Contract."</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">Additional Clauses</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Add New Sub clause 15.6</td>
                                        <td>Corrupt Practices</td>
                                        <td><textarea style="width:100%; height:100px;">No representatives or employees of Employer or those of his Consultants directly or indirectly involved in this Project shall be offered by the Contractor or any of his sub -contractor, directly or indirectly, any benefit, fee, commission, dividend, gift or consideration of any kind in connection with the services and will not at any time offer gratuities or merchandise, cash services or any other inducement.to the Employer's personnel.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Add New Sub clause 15.7</td>
                                        <td>Contractor'sContinuing Liability</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">When the Contractor's services have been discontinued or terminated, said discontinuance or termination shall not affect any right of the Employer to claim against the Contractor then existing or which may thereafter accrue. Any retention or payment of monies by the Employer due to the Contractor will not release the Contractor from his liability.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Add New Sub clause 15.8</td>
                                        <td>Employer's special power of foreclosure of the works</td>
                                        <td><textarea style="width:100%; height:100px;">In case at any time after the issue of Notice of Award, the Employer shall for any reason whatsoever not require the whole or part of the Works to be carried out, the Employer shall give notice in writing of the fact to the Contractor. The Contractor shall be paid at Contract Rates for the portion of Work executed including extra works. The Employer shall after accessing the stage at which such foreclosure was ordered, pay to the Contractor, a compensation based on the price of the work that would not be completed as a result of such a foreclosure, after deducting the monies due from the Contractor including advances, taxes, duties, levies, punitive damages, royalties etc. If this clause is exercised at a stage where the work executed is up to 50% of the contract value then the percentage shall be on mutually discussed and agreed value, if the executed contract value is 50% or more, then 1% of the remaining contract value shall be paid. Upon such foreclosure Employer shall be at absolute liberty and entitled to carry remaining work through any other Contractor or in any other manner as Employer deems fit.In such event of foreclosure, the Contractor shall within a period of 30 days from receipt of notice from the Employer, remove its materials, equipment, plants, tools, construction machinery, property etc. from the premises. In the event, if the Contractor fails to comply with any such instruction, the Employer may remove them at the Contractor's expense or sell them by auction or private sale at risk and account of the Contractor in all respects and the certificate of the Employer as to expenses of any such removal and the amount of the proceeds and expense of any such sale shall be final and conclusive against the Contractor.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Add New Sub clause 15.9</td>
                                        <td>Termination on Account of Breach of Faith</td>
                                        <td><textarea style="width:100%; height:100px;">In case the Employer can reasonably establish the Contractor's intention to defraud or defame or disrupt the business activities of the Employer,</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">either intentionally or otherwise, then without any prejudice to any other additional remedy and/or compensation he may seek from the Contractor, the Employer shall have the right to forfeit any monies due to the Contractor, the Contractor's deposits and further recover any outstanding money due to the Employer. The causes of breach of faith shall be but not limited to:-Alteration of the Contract Documents including the addenda/ corrigenda at any stage during the tender process or afterwards, irrespective whether such an alteration in any manner, benefits the Employer or the Contractor or is neutral.Divulging business information of the Employer with any other person or organization not a party to this Contract, without an express authority from the EmployerIndulging in corrupt practicesSupporting anti-national activitiesAny other cause that may be reasonable</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 16.0</td>
                                        <td>Suspension andTermination by Contractor</td>
                                        <td><textarea style="width:100%; height:100px;">This Clause and its references are deleted.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 17.0</td>
                                        <td>Risk  Responsibility</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 17.1</td>
                                        <td>Indemnities</td>
                                        <td><textarea style="width:100%; height:100px;">The last para starting from "the Employer shall indemnify ………………. damage to the property" stand deleted.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 17.6</td>
                                        <td>Limitation of Liability</td>
                                        <td><textarea style="width:100%; height:100px;">Delete the Sub-Clause 17.6:</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 18</td>
                                        <td>Insurance</td>
                                        <td><textarea style="width:100%; height:100px;">The entire Clause is replaced by the following clauses.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 18.1</td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">The Contractor shall be responsible for all injury or damage to persons, animals or things and for all damage to property which may arise from any factor or omission on the part of the Contractor or any Subcontractors or any Nominatedsubcontractor or any of their employees.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 18.2</td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">The liability under this Clause shall also cover, inter alia, any damage to structures, whether immediately adjacent to the Works or otherwise,any damage to roads, streets, footpaths, bridges</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">as well as damage caused to the buildings and other structures and works forming part of the Work. The Contractor shall also be responsible for any damage caused to the buildings and other structures forming part of the Works and where such damage is caused due to rain, wind, frost or other inclemency of weather.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 18.3</td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">The Contractor shall indemnify and keep indemnified the Employer and hold him harmless in respect of all and any loss and expenses arising from any such injury or damage to persons or property as aforesaid and also against any claim made by any third party in respect of injury or damage, whether under any statute or otherwise and also in respect of any award or compensationor damage consequent upon such claim.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 18.4</td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">The Contractor shall, at his own expense, effect and maintain till the end of the Defect Liability Period under this Contract, or as mentioned in special conditions of contract, with an insurance company approved by the Employer Contractor's All Risks insurance including loss or damage by fire, storm, tempest, lightning, flood, earthquake, aircraft or anything dropped there from, aerial objects, riot and civil commotion in the joint names of the Employer's and the Contractor (Name of the Company) being the Principal Beneficiary) against all risk as per the standard comprehensive All Risk Policy for 110% of the Contract Price and deposit such insurance policy or policies with the Employer before commencing the Works. In case the Employer obtains the CAR policy on his own, the cost for obtaining such policy shall either be recovered from the Contractor or such cost for the said policy, as set out in the Contract BOQ shall not be paid to the Contractor.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 18.5</td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">The Contractor shall reinstate all damage of every sort mentioned in this Clause so as to deliver up the whole of the Works complete and perfect in every respect and so as to make good or otherwise satisfy all claims for damage toproperty of third parties.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 18.6</td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">The Contractor shall also indemnify and keep indemnified the Employer against all claims which may be made against the Employer, by any person in respect of anything which may arise in respect of the Works or in consequence thereofand shall, at his own expense, effect and</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">maintain, until the completion of the Work, with an Insurance Company approved by the Employer a Third Party Public Liability Insurance policy in the joint names of the Employer / and the Contractor ( being the Principal Beneficiary) against such risks and deposit such policy or policies before commencement of the Works. The minimum limit of the coverage under the Policy shall be Rs. 50.00 Lakhs per accident or occurrence, there being no limit on the number of such accidents or occurrences. In case the Employer obtains the this policy on his own, the cost for obtaining such policy shall either be recovered from the Contractor or such cost for the said policy, as set out in the Contract BOQ shall not be paid to the Contractor as mentioned in Special conditions of contract.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 18.7</td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">The Contractor shall also indemnify the Employer against all which may be upon the Employer, whether under the Workmen's Compensation Act 1923 or any other applicable Laws, during the currency of this Contract or in respect of any employee of the Contractor or Sub Contractor and shall at his own expense effect and maintain until the end of the Defect Liability Period, with an insurance company, approved by the Employer, a policy of insurance against such risks and deposit such policy or policies with the Employer from time to time during the currency of this Contract.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 18.8</td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">In default of the Contractor insuring as provided above, the Employer may so insure and may deduct the premiums paid from any moneys due or which may become due to the Contractor.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 18.9</td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">The Contractor shall be responsible for any liability which may not be covered by the insurance policies referred to above and also for all other damages to any person, animal or defective carrying out of this Contract, whatever, may be the reasons due to which the damage shall have been caused.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 18.10</td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">The Contractor shall also indemnify and keep indemnified the Employer against all and any cost, charges or expenses arising out of any claim or proceedings relating to the Works and also in respect of any award of damages orcompensation arising thereon.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 18.11</td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">Without prejudice to the other rights of the Employer against Contractor in respect of such default, the Employer shall be entitled to deduct from any sums payable to the Contractor the amount of any damages, compensation costs, charges and other expenses paid by the Employer and which are payable by the Contractor under this Clause.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 18.12</td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">The Contractor shall, upon settlement by the Insurer of any claim made against the Insurer pursuant to a policy taken under this Clause, proceed with due diligence to rebuild or repair the Works destroyed or damaged. In this event all the monies received from the Insurer in respect of such damage shall be paid to the Contractor and the Contractor shall not be entitled to any further payment in respect of the expenditure incurred for rebuilding or repairing of the materials or goods destroyed or damaged.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 18.13</td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">The Contractor, in case of re building or reinstatement after fire, shall be entitled to such extension of time for completion as the Employer may deem fit, but shall, however, not be entitled to reimbursement by the Employer of any shortfall or deficiency in the amount finally paid by the insurer in settlement of any claim arising as set out herein.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 18.14</td>
                                        <td>INJURY TO PERSONS AND PROPERTY OF EMPLOYER</td>
                                        <td><textarea style="width:100%; height:100px;">The Contractor shall be liable for and shall indemnify and keep indemnified the Employer against any liability, loss, claim or proceedings whatsoever arising under any Law in respect of personal injury to or the death of any person whomsoever arising out of or in the course of or caused by the carrying out of the Works, unless such claim arises due to any act or neglect of the Employer or of any person for whom the Employer is responsible.Except for such loss or damages as is at the risk of the Employer, the Contractor shall be liable for and shall indemnify, keep indemnified and hold harmless the Employer against any expense, liability, loss, claim or proceedings in respect of any injury or damage whatsoever to any property movable or immovable in so far as such injury or damage arises out of or in the course of or byreason of carrying out of the Work, and provided</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">always that the same is due to any negligence, omission or default of the Contractor, his servants or agents or of any Nominate or SubContractor, his servant or agent</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub clause 18.15</td>
                                        <td>INSURANCE OF THE WORK BY THE CONTRACTOR.</td>
                                        <td><textarea style="width:100%; height:100px;">The Contractor shall in the joint names of the Employer and Contractor (the Employer being the Principal Beneficiary) insure against for the full value thereof all Work executed and all unfixed materials and goods intends for, delivered to and placed on or adjacent to the Work, but excluding temporary building plant, tools and equipment owned or hired by the Contractor or any Sub Contractor and shall keep such Work materials and goods so insured until the end of the Period. Such insurances shall be with insurers approved by the Employer and the Contractor shall deposit with the Employer the insurance policy or policies and the receipts in respect of premiums paid by the Contractor and should the Contractor make default in insuring or continuing to insure as aforesaid the Employer may himself insure against any risk with respect of which the default shall have occurred and deduct a sum equivalent to the amount paid by him in respect of premium from any monies due to or to become due to the Contractor.Upon settlement of any claim under the insurances aforesaid the Contractor with due diligence shall restore Work damaged, replace or repair unfixed materials or goods which have been destroyed or injured, remove or dispose of any debris and proceed with the carrying out and completion of the Work. All monies received from such insurances shall be paid to the Contractor by installments along with the interim bills. The Contractor shall not be entitled to payment in respect of the restoration of Work damaged, the replacement and repair of any unfixed materials or goods and the removal and disposal of debris other than the monies received under the said insurances.All Work executed and all unfixed materials and goods intended for, delivered to and placed on or adjacent to the Works (except temporary buildings, plant, tools and equipment owned or hired by the Contractor or any Sub Contractor) shall be at the sole risk of the Contractor as regards loss or damage by fire, storm, tempest, lightning, flood, earthquake, aircraft or anything dropped there from, aerial objects riot and civilcommotion. In case any loss or damage affecting</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">the Work or any part thereof or any such unfixed materials or goods is occasioned by any one or more of the said contingencies then:The occurrence of such loss or damage shall be disregarded in computing any amounts payable to the Contractor under or by virtue of this Contract.The Contractor with due diligence shall restore Work damaged, replace or repair any unfixed materials or goods which have been destroyed or injured, removed and disposed of any debris and proceed with carrying out and completion of the Work.The existing structure together with all the contents thereof and the Works and all unfixed materials and goods intended for, delivered to and placed on or adjacent to the Works (except temporary buildings, plant, tools and equipment owned or hired by the Contractor or any Sub Contractor) shall be at the sole risk of the Employer as regards loss or damage by fire, storm, tempest, lightning, flood, earthquake, aircraft or anything dropped there from, aerial objects, riot and civil commotion, and the Employer shall maintain adequate insurance against that risk if any loss or damage affecting the Work or any part thereof or any such unfixed materials or goods is occasioned by any one or more of the said contingencies then:The Principal Beneficiary for all required insurance as provided in the Contract and taken by the Contractor shall be the Employer</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 19.0</td>
                                        <td>Force Majeure</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 19.1</td>
                                        <td>Definition of Force Majeure</td>
                                        <td><textarea style="width:100%; height:100px;">The following events shall be added to the list:</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 19.1</td>
                                        <td>Definition of Force Majeure</td>
                                        <td><textarea style="width:100%; height:100px;">Boycotts or strikes (other than those by the employees of the parties or by any act or omission by any of them) disrupting supplies for more than 30 days at one instanceAny judgment or order of a competent court or statutory authority against the affected party, in any proceedings, restricting its rights under this ContractAny act of war (whether declared or undeclared), invasion , armed conflict, or act of foreign enemy , blockade , embargo, riot , terrorist or military action, civilcommotion or sabotage, which prevent the</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">construction work for more than 60 days in one instance at one stretchExpropriation or acquisition of the site by the Government.Any unlawful or unauthorized or without jurisdiction revocation of or refusal to renew or grant without valid cause or approval required by the affected party to perform its obligations under this contract (other than the consent of obtaining of which was a condition precedent) provided that such a revocation, refusal to renew or grant did not result from the failure or inability of either party to comply with any condition relating to grant, maintenance or renewal of such consents or permits.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 19.7</td>
                                        <td>Release from Performance under the Law</td>
                                        <td><textarea style="width:100%; height:100px;">Add:Neither Party shall be liable in any manner whatsoever to the other Party in respect of any claim, losses, expense, or demand or proceedings relating to or arising out of occurrence orexistence of any event or act of Force Majeure.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Clause 20.0</td>
                                        <td>CLAIMS, DISPUTES AND ARBITRATION</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 20.1</td>
                                        <td>Contractor's Claims</td>
                                        <td><textarea style="width:100%; height:100px;">Delete "42 days" in first line of 5th paragraph andreplace with "28 days".:</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 20.6</td>
                                        <td>Performance during Arbitration</td>
                                        <td><textarea style="width:100%; height:100px;">Delete Sub-Clause 20.6 and substitute with: "Unless settled amicably, any dispute in respect of which the DAB's decision (if any) has not become final and binding shall be finally settled by arbitration in accordance with the Arbitration and Conciliation Act 1996 and/or statutory modifications thereof. The arbitration award shall be final and binding upon the parties, and language of the proceedings shall be in English. If the Contract Price is less than INR 50,000,000/-, then the dispute shall be settled by a sole arbitrator appointed by the Employer, to whom the Contractor shall not have any reasonable objection.If the Contract Price is INR 50,000,000/- or higher, unless both Parties agree to the appointment of a sole arbitrator, reference shall be made to three arbitrators, one to be appointed by each Partywithin 30 (thirty) days after receipt of a written</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">notice from the other Party having appointed an arbitrator before issue of the notice, and a third arbitrator to be selected by the two arbitrators so appointed by the parties within 30 (thirty) days of the date of nomination of the second arbitrator.The venue of arbitration shall be as stated in"Appendix".Only persons in the following categories shall be eligible for appointment as arbitrators:Past / Present Presidents of the Institution of Engineers.Past / Present Presidents of the Institution of Surveyors.Past / Present Presidents of the Institute of Architects.Ex Judge of High Court or Supreme Court A designated Senior Counsel and / or a senior Counsel of the High Court or Supreme Court."</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Sub Clause 20.7</td>
                                        <td>Costs</td>
                                        <td><textarea style="width:100%; height:100px;">During the arbitration the parties shall bear their own respective costs which shall be subject to arbitration award.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>New Sub Clause 20.9</td>
                                        <td>Disclaimer</td>
                                        <td><textarea style="width:100%; height:100px;">New Sub Clause addedThough adequate care has been taken in the preparation of this tender document, the Contractor should satisfy itself/themselves that it is complete in all respects. Intimation of discrepancy, if any, should be intimated to Employer immediately, Non-receipt of any such intimation shall be deemed to be a confirmation that the Contractor is satisfied about the completeness of the tender document in all respects.Neither Employer, nor its employees, consultants, advisors accept any liability or responsibility for the accuracy or completeness of, nor make any representation or warranty, express, or implied, with respect to the information contained in the tender, or on which the tender is based, or any otherinformation or representations</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">supplied or made in connection with the Selection Process.Nothing in the tender is, or should be relied on, as a promise or representation as to the future. In Furnishing the tender, neither Employer, nor its employees, consultants, advisors undertake to provide the recipient with access to any additional information or to update the tender or to correct any inaccuracies therein which may become apparent. Employer, its employees, consultants and advisors reserve the right, without prior notice, to change the procedure for the identification of the Preferred Contractor or terminate discussions and the delivery of information at any time before the signing of any agreement for the Project without assigning reasons thereof.Neither Employer nor its employees or consultants will have any liability to any prospective Contractor or any other person under law, equity or contract, or otherwise for any alleged loss, expense or damage which may arise from or be incurred or suffered in connection with anything contained in the tender , any matter deemed to form part of the tender , the award of the Project, the Project information and any other information supplied by or on behalf of Employer or their employees, any consultants or otherwise arising in any way from the selection process for the Project.Employer reserves the right to change, modify, add to or alter the tender Process including inclusion of additionalevaluation criteria. Any change in the</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">tender Process shall be intimated to all shortlisted Contractors.Employer reserves the right to change any or all of the provisions of the tender Such changes will be Intimated to Contractors.Employer reserves the right to reject any or the entire tender submitted in response to the tender at any stage without assigning any reasons whatsoever.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>New Sub Clause 20.10</td>
                                        <td>Continuation of Work during Dispute</td>
                                        <td><textarea style="width:100%; height:100px;">Provided always that the Employer shall not withhold the payment for an Interim Bill and shall not withhold the issuance of an Interim Certificate nor the Contractor except with the consent in writing of the Employer shall in any way delay the carrying out of the Works by reason of any such matters, question or dispute being referred to arbitration but shall proceed with the Work with all due diligence and shall, until completion of arbitration proceedings, relieve the Contractor of his obligations to adhere strictly to the Employer's instructions with regard to the actual carrying out of the Works. The Work shall however be undertaken as per time scheduled, independent of such exigencies unless the Employer desires otherwise.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>Additional Clauses</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>COVID Specific Measures</td>
                                        <td><textarea style="width:100%; height:100px;">The contractor is requested to adhere to the 'COVID 19 Preventive Measures and protocols and all instructions given from time to time by the local authority/Government/ICMR.Prior to the work commencement at site, a discussion specific to Guidelines to be followed for Prevention of COVID-19 will be held during the kick-off meeting.Works can only commence post the meeting has been attended by the contractor.Nonadherence to this clause will be treated as a 'Breach of Contract'</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><textarea style="width:100%; height:100px;">Any cost impact on account of the same shall have to be borne by the Contractor.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Covid Vaccination  Testing</td>
                                        <td><textarea style="width:100%; height:100px;">Contractor shall ensure that all their staff  labour deployed at side shall be Vaccinated for Covid -19 and shall submit the vaccination report to the Company before deployment. Otherwise, the person should be having negative RAT (Rapid Antigen Test Report or RT PCR Test Report from ICMR approved laboratory and having validity as per ICMR guidelines.No Claims shall be entertained from the Contractor in this regard.</textarea></td>
                                        <td><textarea style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                </table>
                                <p></p>
                                <div class="footer-note">End of document.</div>
                                <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                                    <button class="btn btn-info only-save customer-form-submiter">
                                        <?php echo _l('submit'); ?>
                                    </button>

                                </div>
                                <?php echo form_close(); ?>
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