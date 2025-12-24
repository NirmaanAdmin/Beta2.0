<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Letter of Acceptance - Alibaug Beach House Project</title>
    <style>
        body {
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            margin-bottom: 30px;
        }

        .draft-mark {
            font-weight: bold;
            color: #666;
            margin-bottom: 10px;
        }

        .ref {
            margin-bottom: 20px;
        }

        .date {
            margin-bottom: 20px;
        }

        .address-block {
            margin-bottom: 20px;
        }

        .subject {
            font-weight: bold;
            margin: 20px 0;
        }

        .loa-title {
            font-weight: bold;
            margin: 20px 0;
        }

        .content {
            margin-bottom: 30px;
        }

        .list {
            margin-left: 20px;
        }

        .highlight {
            font-weight: bold;
        }

        .signature-block {
            margin-top: 50px;
        }

        .acknowledgement {
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 20px;
        }

        .underline {
            text-decoration: underline;
        }

        .enclosure-list {
            margin-left: 20px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9em;
            color: #666;
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
                            <?php echo form_open(admin_url('purchase/letter_of_agreement_add_update'), array('id' => 'letter-of-agreement-form')); 
                            $dreaft_mark = isset($loa_data[0]['draft_mark']) ? $loa_data[0]['draft_mark'] : 'Draft LOA';
                            ?>
                            <div class="draft-mark">[(<input type="text" class="form-control" name="draft_mark" value="<?php echo $dreaft_mark; ?>">)]</div>
                            <!-- Hidden fields for tracking -->
                            <input type="hidden" name="loa_id" value="<?php echo isset($loa_data[0]['id']) ? $loa_data[0]['id'] : ''; ?>">
                            <input type="hidden" name="tender_id" value="<?php echo $tender_data[0]['tender_id']; ?>">
                            <input type="hidden" name="vendor_id" value="<?php echo $tender_data[0]['vendor_id']; ?>">
                            <input type="hidden" name="project_id" value="<?php echo $tender_data[0]['project_id']; ?>">
                            <div class="header">
                                <?php $ref = isset($loa_data[0]['our_ref']) ? $loa_data[0]['our_ref'] : ''; ?>
                                <div class="ref">Our ref: <input type="text" class="form-control" name="our_ref" value="<?php echo $ref; ?>"></div>
                                <div class="date"><?php echo date('d M, Y', strtotime($tender_data[0]['date'])) ?></div>
                            </div>

                            <div class="address-block">
                                <?php $company_name = isset($loa_data[0]['company_name']) ? $loa_data[0]['company_name'] : 'M/s ' . get_vendor_name_by_id($tender_data[0]['vendor_id']); ?>
                                <p><strong><input type="text" class="form-control" name="company_name" value="<?php echo $company_name; ?>"></strong></p>
                                <?php $address = isset($loa_data[0]['address']) ? $loa_data[0]['address'] : get_vendor_all_details_by_id($tender_data[0]['vendor_id'])->address; ?>
                                <p><input type="text" class="form-control" name="address" value="<?php echo $address; ?>"></p>
                                <?php $email = isset($loa_data[0]['email']) ? $loa_data[0]['email'] : get_vendor_all_details_by_id($tender_data[0]['vendor_id'])->com_email; ?>
                                <p>Email:<input type="text" class="form-control" name="email" value="<?php echo $email; ?>"></p>
                                <?php $attn = isset($loa_data[0]['attn']) ? $loa_data[0]['attn'] : ''; ?>
                                <p><strong>Attn : <input type="text" class="form-control" name="attn" value="<?php echo $attn; ?>"></strong></p>
                                <?php $contact_number = isset($loa_data[0]['contact_number']) ? $loa_data[0]['contact_number'] : get_vendor_all_details_by_id($tender_data[0]['vendor_id'])->phonenumber; ?>
                                <p><strong>Cont. :<input type="text" class="form-control" name="contact_number" value="<?php echo $contact_number; ?>"> </strong></p>
                            </div>

                            <p>Dear Sir,</p>

                            <div class="subject">
                                <p><?php echo get_project_name_by_id($tender_data[0]['project_id']); ?></p>
                                <p><?php echo tender_name_by_id($tender_data[0]['tender_id']); ?></p>
                            </div>

                            <div class="loa-title">LETTER OF ACCEPTANCE</div>

                            <div class="content">
                                <p>We refer to the following:</p>

                                <ul class="list">
                                    <li>Our Tender Invitation Notice and tender documents issued vide email dated 20<sup>th</sup> July 2023.</li>
                                    <li>Your offer (R0) dated 1<sup>st</sup> August' 2023</li>
                                    <li>Techno- Commercial meeting 16<sup>th</sup> August' 2023</li>
                                    <li>Addendum 01 dated 11<sup>th</sup> August' 2023</li>
                                    <li>Your revised offer (R1) dated 17<sup>th</sup> August' 2023</li>
                                    <li>Your revised offer (R2) dated 24<sup>th</sup> August' 2023</li>
                                    <li>The outcomes of discussion held on 24<sup>th</sup> August' 2023 at site with client.</li>
                                    <li>Your final offer dated 24<sup>th</sup> August' 2023,</li>
                                </ul>

                                <p>We hereby accept your final offer of <span class="highlight">Rs: 0</span> for <span class="highlight"><?php echo tender_name_by_id($tender_data[0]['tender_id']); ?></span> Interior and Mill Works as accepted contract amount including all taxes, GST duties, levies, cess, royalties, excluding of labour cess in conformity with the Conditions of Contract, Specification, Drawings, Bill of Quantities and Addendum etc. issued to you. The labour cess shall be deposited by us. Please note that this a re-measurable contract and payment is subjected to the quantities executed and certified at site.</p>

                                <p>The Time for Completion shall be <span class="highlight">on or before 30<sup>th</sup> November' 2023</span> including site mobilization, holidays, monsoon etc.</p>

                                <p>It is expressly understood and agreed that your last tender submission is unconditional and in full compliance with the technical and commercial terms of the tender documents and its addenda issued to you.</p>

                                <p>It is understood that you are fully conversant with local working conditions, and supply of material, plant and labour necessary to perform your obligations in accordance with the tender documents, your above-mentioned final offer and this acceptance, and any responsibility or expense towards this shall be managed by you.</p>

                                <p>You shall receive all necessary instructions and documents from our Project Manager, M/s. Ascentis India Projects Pvt Ltd.</p>

                                <p>The following documents shall form part of the Contract:</p>

                                <ol class="list">
                                    <li>Change Orders (if any) issued from time to time; and</li>
                                    <li>This Letter of Acceptance</li>
                                    <li>Letter of Acceptance containing references to the final offer letter from the Contractor which supersedes/withdraws all earlier Contractor's correspondence, thereby making them null and void.</li>
                                    <li>Tender addendum issued (if any) & minutes of meeting, if attested by all concerned parties participating in the Tender.</li>
                                    <li>Particular Conditions of Contract</li>
                                    <li>the "Condition of Contract for Construction" First Edition 1999 published by the Federation Internationale des Ingenieurs-Conseils (FIDIC). To be subscribe by contractor.</li>
                                    <li>Appendix to Tender</li>
                                    <li>Contractor General obligation</li>
                                    <li>Environmental, Health & Safety (EHS) Manual</li>
                                    <li>Bill of quantities read in conjunction of Preamble notes.</li>
                                    <li>Technical Specifications, finishing schedule and approved make list.</li>
                                    <li>Drawings</li>
                                </ol>

                                <p>This Letter of Acceptance supersedes any condition laid down in any communication made between us, if contradicted. This LOA shall constitute a binding contract between us, upon receipt of the documents mentioned above and a formal agreement shall also be signed incorporating this LOA.</p>

                                <p>Please acknowledge receipt of this Letter of Acceptance by signing and returning the counterpart of this letter along with the signed copy of attached documents to our office within the next 24 hours.</p>
                            </div>

                            <div class="signature-block">
                                <p>Yours sincerely,</p>
                                <p><strong>FOR M/s Basillus International LLP.</strong></p>
                            </div>

                            <div class="enclosures">
                                <p><strong>Encl:</strong></p>
                                <ol class="enclosure-list">
                                    <li>Annexure -1: BOQ</li>
                                    <li>Annexure-2: Appendix to conditions of contract</li>
                                    <li>Annexure-3: Particular Condition of Contract</li>
                                    <li>Annexure -- 4: Contractor General obligation</li>
                                    <li>Annexure -- 5: Environmental, Health & Safety (EHS) Manual</li>
                                    <li>Annexure -- 6: List of Make and RIL List of Approved Make</li>
                                    <li>Annexure -- 7: All communications</li>
                                </ol>
                            </div>

                            <div class="acknowledgement">
                                <p class="underline">Acknowledged and confirmed by:</p>
                                <?php $contractor_name = isset($loa_data[0]['contractor_name']) ? $loa_data[0]['contractor_name'] : ''; ?>
                                <p>Name of the Contractor: - <strong><input type="text" class="form-control" name="contractor_name" value="<?php echo $contractor_name; ?>"></strong></p>
                                <?php $represented_by = isset($loa_data[0]['represented_by']) ? $loa_data[0]['represented_by'] : ''; ?>
                                <p>Represented by (in capitals): - <strong><input type="text" class="form-control" name="represented_by" value="<?php echo $represented_by; ?>"></strong></p>
                                <?php $designation = isset($loa_data[0]['designation']) ? $loa_data[0]['designation'] : ''; ?>
                                <p>Designation / Position: - <strong><input type="text" class="form-control" name="designation" value="<?php echo $designation; ?>"></strong></p>
                                <p>Signature: - _______________________________</p>
                                <p>Date: - _______________________________</p>
                                <p>Company Stamp: - _______________________________</p>
                            </div>

                            <div class="footer col-md-2 ">
                                <?php $document_number = isset($loa_data[0]['document_number']) ? $loa_data[0]['document_number'] : ''; ?>
                                <p>Document: <input type="text" class="form-control" name="document_number" value="<?php echo $document_number; ?>"></p>
                            </div>
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