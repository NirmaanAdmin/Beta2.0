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
            font-family: Arial, sans-serif;
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
                            <div class="draft-mark">[(Draft LOA)]</div>

                            <div class="header">
                                <div class="ref">Our ref: ABH-LOA-[UGF-ID]-017-R0</div>
                                <div class="date"><?php echo date('d M, Y', strtotime($tender_data[0]['date'])) ?></div>
                            </div>

                            <div class="address-block">
                                <p><strong>M/s <?php echo get_vendor_name_by_id($tender_data[0]['vendor_id']); ?></strong></p>
                                <p><?php echo get_vendor_all_details_by_id($tender_data[0]['vendor_id'])->address; ?></p>
                                <p>Email: <?php echo get_vendor_all_details_by_id($tender_data[0]['vendor_id'])->com_email; ?></p>
                                <p><strong>Attn : </strong></p>
                                <p><strong>Cont. :</strong> <?php echo get_vendor_all_details_by_id($tender_data[0]['vendor_id'])->phonenumber; ?></p>
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

                                <p>We hereby accept your final offer of <span class="highlight">Rs: 9,69,06,263/- (Nine Crore Sixty-Nine Lakh Six Thousand Two Hundred Sixty-Three Only)</span> for <span class="highlight">Upper Ground Floor</span> Interior and Mill Works as accepted contract amount including all taxes, GST duties, levies, cess, royalties, excluding of labour cess in conformity with the Conditions of Contract, Specification, Drawings, Bill of Quantities and Addendum etc. issued to you. The labour cess shall be deposited by us. Please note that this a re-measurable contract and payment is subjected to the quantities executed and certified at site.</p>

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
                                <p>Name of the Contractor: - <strong>M/S Ashish Inter Build Pvt. Ltd.</strong></p>
                                <p>Represented by (in capitals): - <strong>Mr. Mehul Pabari</strong></p>
                                <p>Designation / Position: - <strong>Project Manager</strong></p>
                                <p>Signature: - _______________________________</p>
                                <p>Date: - _______________________________</p>
                                <p>Company Stamp: - _______________________________</p>
                            </div>

                            <div class="footer">
                                <p>Document: BGJ-LOA-UGF-AIPL-017-R0</p>
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