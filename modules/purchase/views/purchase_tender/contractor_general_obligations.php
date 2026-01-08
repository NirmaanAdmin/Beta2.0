<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <title>Contractor Obligations - Top TOC</title>
    <style>
        :root {
            --fg: #222;
            --muted: #555;
            --border: #ddd;
            --bg: #fff;
        }

        body {
            font-family: system-ui, Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        h1,
        h2,
        h3,
        h4 {
            margin-top: 1.4em;
        }

        p {
            margin: 0.4em 0;
        }

        /* .doc {
            max-width: 1000px;
            margin: auto;
            padding: 2em;
        } */

        .doc-table {
            border-collapse: collapse;
            width: 100%;
            margin: 1em 0;
        }

        .doc-table td,
        .doc-table th {
            border: 1px solid #ccc;
            padding: 6px 8px;
            vertical-align: top;
        }

        .sidebar-toc {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100%;
            overflow: auto;
            background: #f8f8f8;
            padding: 1em;
            border-right: 1px solid #ccc;
        }

        .sidebar-toc h2 {
            margin-top: 0;
        }

        .sidebar-toc ul {
            list-style: none;
            padding-left: 0;
            font-size: 0.9em;
        }

        .sidebar-toc li {
            margin: 0.3em 0;
        }

        .sidebar-toc a {
            text-decoration: none;
            color: #333;
        }

        .doc.with-sidebar {
            margin-left: 280px;
        }

        .top-toc {
            background: #f8f8f8;
            border: 1px solid #ccc;
            padding: 1em;
            margin-bottom: 1em;
        }

        .top-toc summary {
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0 22px;
        }

        th,
        td {
            border: 1px solid var(--border);
            padding: 8px 10px;
            vertical-align: top;
        }

        th {
            background: #f7f7f7;
            font-weight: 600;
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
                            <div class='doc'>
                                <h1>Contractor’s General Obligations</h1>
                                <?php echo form_open(admin_url('purchase/appendix_to_contract_add_update'), array('id' => 'appendix-to-contract-form'));
                                ?>
                                <input type="hidden" name="tender_id" value="<?php echo $tender_data[0]['tender_id']; ?>">
                                <input type="hidden" name="vendor_id" value="<?php echo $tender_data[0]['vendor_id']; ?>">
                                <input type="hidden" name="project_id" value="<?php echo $tender_data[0]['project_id']; ?>">
                                <table border="1" cellspacing="0" cellpadding="6" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 13px;">
                                    <tr>
                                        <th colspan="2" style="text-align:left;">DOCUMENT CONTROL SHEET</th>
                                    </tr>

                                    <tr>
                                        <th style="width:40%;">QMS Reference</th>
                                        <td><input type="text" class="form-control" name="qms_reference" value="BGJ-LOA-UGFID-017-R0" /></td>
                                    </tr>
                                    <tr>
                                        <th>Document number</th>
                                        <td><input type="text" class="form-control" name="document_number" value="BGJ-LOA-UGFID-017-R0" /></td>
                                    </tr>
                                    <tr>
                                        <th>Document title</th>
                                        <td><input type="text" class="form-control" name="document_title" value="Contractor's General Obligations" /></td>
                                    </tr>
                                    <tr>
                                        <th>Document purpose</th>
                                        <td><input type="text" class="form-control" name="document_purpose" value="Issued for Tenders" /></td>
                                    </tr>
                                    <tr>
                                        <th>Document status</th>
                                        <td><input type="text" class="form-control" name="document_status" value="Approved" /></td>
                                    </tr>
                                    <tr>
                                        <th>No. of pages (including this page)</th>
                                        <td><input type="text" class="form-control" name="no_of_pages" value="34" /></td>
                                    </tr>

                                    <!-- Revision Control -->
                                    <tr>
                                        <th colspan="2" style="text-align:left;">Revision Control</th>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <table border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse; width: 100%; font-size: 13px;">
                                                <tr>
                                                    <th style="width:15%;">Rev. no.</th>
                                                    <th style="width:25%;">Prepared by</th>
                                                    <th style="width:25%;">Approved by</th>
                                                    <th style="width:20%;">Effective from</th>
                                                    <th style="width:15%;">Effective to</th>
                                                </tr>
                                                <tr>
                                                    <td>R0</td>
                                                    <td></td>
                                                    <td>Abhishek Intodia</td>
                                                    <td>01 Oct 2025</td>
                                                    <td>-</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <!-- Revision History -->
                                    <tr>
                                        <th colspan="2" style="text-align:left;">Revision History</th>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="text-align:center; font-style: italic; color:#7f8c8d;">
                                            No revision history available
                                        </td>
                                    </tr>
                                </table>


                                <div class='top-toc'>
                                    <details open>
                                        <summary><strong>Table of Contents</strong></summary>
                                        <ul>
                                            <li><a href='#general'>GENERAL</a></li>
                                            <li><a href='#the-site'>THE SITE</a></li>
                                            <li><a href='#site-location'>Site Location</a></li>
                                            <li><a href='#survey-plan'>Survey Plan</a></li>
                                            <li><a href='#geotechnical-investigation-sub-surface-conditions'>Geotechnical Investigation/ Sub
                                                    Surface Conditions</a></li>
                                            <li><a href='#topographical-survey'>Topographical Survey</a></li>
                                            <li><a href='#site-working-hours'>Site Working Hours</a></li>
                                            <li><a href='#access-to-site'>Access to Site</a></li>
                                            <li><a href='#traffic-police-dispensations'>Traffic Police Dispensations</a></li>
                                            <li><a href='#hoarding-gate-houses-entrance-gates'>Hoarding, Gate Houses, Entrance Gates</a></li>
                                            <li><a href='#project-signboard'>Project Signboard</a></li>
                                            <li><a href='#site-setting-out-and-surveys'>SITE SETTING OUT AND SURVEYS</a></li>
                                            <li><a href='#benchmark'>Benchmark</a></li>
                                            <li><a href='#surveys'>Surveys</a></li>
                                            <li><a href='#site-protection'>SITE PROTECTION</a></li>
                                            <li><a href='#excavated-slopes'>Excavated slopes</a></li>
                                            <li><a href='#noise-and-dust'>Noise and Dust</a></li>
                                            <li><a href='#storm-water-curing-water'>Storm Water &amp; Curing Water</a></li>
                                            <li><a href='#ground-surfaces'>Ground Surfaces</a></li>
                                            <li><a href='#protection-of-public-and-adjoining-properties'>Protection of Public and Adjoining
                                                    Properties</a></li>
                                            <li><a href='#protection-of-environment'>Protection of Environment</a></li>
                                            <li><a href='#contractor-s-site-organization-and-resources'>CONTRACTOR’S SITE ORGANIZATION AND
                                                    RESOURCES</a></li>
                                            <li><a href='#contractor-s-representative-and-supervisory-staff'>Contractor’s Representative and
                                                    Supervisory Staff</a></li>
                                            <li><a href='#contractor-s-plant-machinery'>Contractor’s Plant &amp; Machinery</a></li>
                                            <li><a href='#batching-plant'>Batching plant</a></li>
                                            <li><a href='#contractor-store-site-offices-and-other-facilities'>Contractor Store, Site offices,
                                                    and Other Facilities</a></li>
                                            <li><a href='#labour-camps'>Labour Camps</a></li>
                                            <li><a href='#sanitation'>Sanitation</a></li>
                                            <li><a href='#security'>Security</a></li>
                                            <li><a href='#site-safety'>Site Safety</a></li>
                                            <li><a href='#fire-protection'>Fire protection</a></li>
                                            <li><a href='#scaffolding-staging-guard-rails-barricades'>Scaffolding, staging, guard rails,
                                                    barricades</a></li>
                                            <li><a href='#temporary-lighting-and-ventilation'>Temporary Lighting and Ventilation</a></li>
                                            <li><a href='#housekeeping'>Housekeeping</a></li>
                                            <li><a href='#temporary-power-and-water-supply'>Temporary Power and Water Supply</a></li>
                                            <li><a href='#quality-assurance'>QUALITY ASSURANCE</a></li>
                                            <li><a href='#quality-plan'>Quality Plan</a></li>
                                            <li><a href='#method-statements'>Method Statements</a></li>
                                            <li><a href='#programme'>PROGRAMME</a></li>
                                            <li><a href='#drawings-specifications-interpretations'>DRAWINGS, SPECIFICATIONS, INTERPRETATIONS</a>
                                            </li>
                                            <li><a href='#contractor-s-design'>Contractor’s Design</a></li>
                                            <li><a href='#drawings-issued-to-contractor'>Drawings Issued to Contractor</a></li>
                                            <li><a href='#shop-drawings-product-data'>Shop Drawings, Product Data</a></li>
                                            <li><a href='#samples'>Samples</a></li>
                                            <li><a href='#approvals'>Approvals</a></li>
                                            <li><a href='#ordering-and-delivery-of-materials-and-equipment-for-the-work'>Ordering and Delivery
                                                    of Materials and Equipment for the Work</a></li>
                                            <li><a href='#materials-workmanship-storage-inspections'>MATERIALS, WORKMANSHIP, STORAGE,
                                                    INSPECTIONS</a></li>
                                            <li><a href='#materials-and-workmanship'>Materials and Workmanship</a></li>
                                            <li><a href='#special-makes-or-brands'>Special Makes or Brands</a></li>
                                            <li><a href='#free-issue-material-by-employer'>Free Issue Material by Employer</a></li>
                                            <li><a href='#materials-delivery-storage-and-handling'>Materials Delivery, Storage, and Handling</a>
                                            </li>
                                            <li><a href='#right-type-of-workmen-plant-and-machinery'>Right Type of Workmen, Plant, and
                                                    Machinery</a></li>
                                            <li><a href='#artists-and-tradesmen'>Artists and Tradesmen</a></li>
                                            <li><a href='#workmanship-productivity'>Workmanship / Productivity</a></li>
                                            <li><a href='#inspection'>Inspection</a></li>
                                            <li><a href='#testing'>Testing</a></li>
                                            <li><a href='#certificates'>Certificates</a></li>
                                            <li><a href='#covering-up'>Covering Up</a></li>
                                            <li><a href='#tolerances'>Tolerances</a></li>
                                            <li><a href='#utilities-and-substructures'>Utilities and Substructures</a></li>
                                            <li><a href='#restoration-and-repair'>Restoration and Repair</a></li>
                                            <li><a href='#night-work'>Night Work</a></li>
                                            <li><a href='#maintenance-during-construction'>Maintenance during Construction</a></li>
                                            <li><a href='#overloading'>Overloading</a></li>
                                            <li><a href='#use-of-explosives'>Use of Explosives</a></li>
                                            <li><a href='#bureau-of-indian-standards'>BUREAU OF INDIAN STANDARDS</a></li>
                                            <li><a href='#meetings-and-reporting'>MEETINGS AND REPORTING</a></li>
                                            <li><a href='#progress-meetings'>Progress Meetings</a></li>
                                            <li><a href='#contractor-s-daily-reports'>Contractor’s Daily Reports</a></li>
                                            <li><a href='#contractor-s-monthly-reports'>Contractor’s Monthly Reports</a></li>
                                            <li><a href='#project-close-out-deliverables'>PROJECT CLOSE-OUT DELIVERABLES</a></li>
                                            <li><a href='#general-builder-s-work-for-other-contractors'>GENERAL BUILDER’S WORK FOR OTHER
                                                    CONTRACTORS</a></li>
                                            <li><a href='#site-attendance-for-other-contractors'>SITE ATTENDANCE FOR OTHER CONTRACTORS</a></li>
                                            <li><a href='#general'>General</a></li>
                                            <li><a href='#safety-attendance'>Safety Attendance</a></li>
                                            <li><a href='#general-attendance'>General Attendance</a></li>
                                            <li><a href='#miscellaneous-provision'>Miscellaneous Provision</a></li>
                                            <li><a href='#oral-agreements'>Oral Agreements</a></li>
                                            <li><a href='#site-order-books'>Site Order Books</a></li>
                                            <li><a href='#problem-solving'>Problem Solving</a></li>
                                            <li><a href='#encumbrances'>Encumbrances</a></li>
                                            <li><a href='#further-assurance'>Further Assurance</a></li>
                                            <li><a href='#no-third-party-beneficiaries'>No Third-Party Beneficiaries</a></li>
                                            <li><a href='#public-announcements'>Public Announcements</a></li>
                                            <li><a href='#validity-of-commercial-instrument'>Validity of commercial instrument</a></li>
                                            <li><a href='#site-attendance-matrix'>SITE ATTENDANCE MATRIX</a></li>
                                        </ul>
                                    </details>
                                </div>

                                <h2 id='general'>GENERAL</h2>
                                <p>The Contractor shall allow for compliance of its General Obligations described in this document. This
                                    document shall be read in conjunction with all other documents forming part of the Contract. Any discrepancy
                                    / ambiguity noticed by the Contractor shall be brought to the knowledge of the Engineer whose decision shall
                                    be final and binding. Such opinion as to the intent of the Contract requirement shall not entitle the
                                    Contractor to any additional costs, or extension of time to the Contract.</p>
                                <p>The Contractor is advised to take into account all the aspects while pricing their Preliminaries and
                                    Attendance.</p>
                                <h2 id='the-site'>THE SITE</h2>
                                <h3 id='site-location'>Site Location</h3>
                                <p>Refer to Appendix to Tender for the site location.</p>
                                <p>The Contractor confirms that before tendering for the works the Contractor has visited and examined the Site
                                    and satisfied himself as to the nature of the existing roads or other means of communication and the
                                    character of the soil and of the excavations, the correct dimensions of the Work and the facilities for
                                    obtaining any special articles called for in the Contract Document and shall have obtained his own
                                    information on all matters affecting the continuation and progress of the Works including but not limited to
                                    mitigation of any external causes that may hamper the progress of work. The costs associated with such
                                    measures will be deemed to have been included in the Contractor’s price. No extra claims made in consequence
                                    of any misunderstanding or incorrect information on any of these points, or on the grounds of insufficient
                                    description, shall not be entertained or allowed at any stage. Should the Contractor after visiting the
                                    Site, find any discrepancies, omissions, ambiguities or conflicts in or among the Contract Document, or be
                                    in doubt as to their meaning, he shall bring the questions to the Engineer attention, not later than three
                                    (3) working days before the date of submission of Tender.</p>
                                <p>The Contractor shall be aware of Projects being carried out by all authorities in the Vicinity of the Project
                                    Area and understand fully, the implication on this Project.</p>
                                <p>Additionally, the Contractor shall obtain consents from the Employer, make all necessary arrangements, and
                                    pay all the costs for additional land areas or accesses, required by him outside the limit of the Employer’s
                                    land, without liability to the Employer.</p>
                                <h3 id='survey-plan'>Survey Plan</h3>
                                <p>A copy of the Survey Plans is available with the Employer’s Representative. Contractor shall note the bounds
                                    of the site and plan their logistics.</p>
                                <h3 id='geotechnical-investigation-sub-surface-conditions'>Geotechnical Investigation/ Sub Surface Conditions
                                </h3>
                                <p>The Contractor shall promptly notify the Engineer in writing of any subsurface or latent physical conditions
                                    at the site differing materially from those indicated in the Contract Documents or of any unusual nature
                                    differing materially from those ordinarily encountered and generally recognized as inherent in construction
                                    of the character provided for in the contract documents. The Engineer will investigate those conditions and
                                    obtain such additional tests and surveys as may deem necessary. In case the Engineer finds that the
                                    conditions differ significantly from those indicated in the contract documents or from those inherent in the
                                    construction, an amendment order may be issued after a proper consultation with the relevant Specialist
                                    Consultant to incorporate the necessary revisions.</p>
                                <p>The Contractor shall protect the Workplaces from the ingress of water, either surface or groundwater. It will
                                    be the Contractors responsibility to keep the site in reasonably dry condition so as not cause a safety
                                    hazard or health hazard.</p>
                                <h3 id='topographical-survey'>Topographical Survey</h3>
                                <p>A copy of topographical survey of the plot is available with the Employer’s Representative. The Employer
                                    shall not be liable for any misstatement or error in the topographical survey and makes no representations
                                    in respect of the fitness of the Site.</p>
                                <h3 id='site-working-hours'>Site Working Hours</h3>
                                <p>There are no restrictions placed on the working hours that the Contractor is permitted to work on site.
                                    However, all Statutory Regulations are required to be followed.</p>
                                <p>The normal working hours for the Site are Monday to Saturday from 9:00 to 22:00. All work beyond the normal
                                    working hours shall be indicated in the Tender programme. The Contractor shall confirm the shifts and hours
                                    that will be worked on site by the Contractor and his Subcontractors.</p>
                                <p>The Contractor shall obtain the approval of the Engineer for the weekly working schedule that includes
                                    outside normal working hours.</p>
                                <p>Any concealed works taken up for execution during out of hours working shall have the prior approval of the
                                    Employer’s Representative. The Site may be closed during all official national holidays.</p>
                                <h3 id='access-to-site'>Access to Site</h3>
                                <p>Access to, from, and around the Site may be congested as a consequence of the construction works ongoing in
                                    the vicinity.</p>
                                <p>The Contractor is required to assess the facilities available during a Site visit and no claims shall be
                                    entertained on this account.</p>
                                <p>The Contractor shall be responsible to obtain all transportation permits for the delivery of materials,
                                    plants to site.</p>
                                <h3 id='traffic-police-dispensations'>Traffic Police Dispensations</h3>
                                <p>The Contractor shall acknowledge that the transportation to site might at times be restricted by the local
                                    authorities / traffic polices due to VIP movements. The Contractor shall plan their works considering this
                                    aspect and no extension of time or additional cost is allowed on this account.</p>
                                <h3 id='hoarding-gate-houses-entrance-gates'>Hoarding, Gate Houses, Entrance Gates</h3>
                                <p>The Employer has barricaded the Site with hoarding around the perimeter. During the course of the Project,
                                    the boundaries to the work areas will be re-aligned from time to time to facilitate carrying out of the
                                    works.</p>
                                <h3 id='project-signboard'>Project Signboard</h3>
                                <p>Not Applicable.</p>
                                <h2 id='site-setting-out-and-surveys'>SITE SETTING OUT AND SURVEYS</h2>
                                <h3 id='benchmark'>Benchmark</h3>
                                <p>The Engineer shall provide a land surveying benchmark on Site that is cross referenced with other existing
                                    benchmarks.</p>
                                <p>The Contractor shall verify and co-relate all the survey data available at the Site before commencing the
                                    Work and shall immediately report in writing any errors or inconsistencies to the Employer’s Representative.
                                    In the absence of such report, the Contractor shall be responsible for any error in the Work resulting from
                                    such variations and shall bear the cost of corrective Work.</p>
                                <p>The scope of Work covered under this tender includes setting out all the Work from one reference point being
                                    made available to the Contractor by the Employer’s Representative. The Engineer shall determine any lines
                                    levels which may be required for the execution of the Work and shall furnish to the Contractor by way of
                                    accurately dimensioned drawings, such information as shall enable the Contractor to complete the Work.</p>
                                <p>All further line out shall be carried out by the Contractor using total station and shall be checked by the
                                    Engineer before commencement of actual Works. The Contractor shall set out and level the Work and shall be
                                    responsible for the accuracy of the same in accordance with the drawings. The Contractor shall carefully
                                    preserve all survey markings as also setting out stakes, reference points, bench marks, and monuments.
                                    Should any stakes, points or benches be removed or destroyed by any act of the Contractor or his employees,
                                    they shall be reset at the Contractor’s expenses.</p>
                                <p>Commencement of Works by the Contractor shall be regarded as its acceptance of the correctness of all survey
                                    and setting out data available at the Site and no claims shall be entertained or allowed in respect of any
                                    errors or discrepancies found at a later date. If at any time error in this regard appears during its
                                    progress of the Work, the Contractor shall at its own expense rectify such error to the satisfaction of the
                                    Employer’s Representative.</p>
                                <p>The approval by the Engineer of the setting out by the Contractor shall not relieve the Contractor of any of
                                    the responsibilities, obligations, and liabilities under the Contract.</p>
                                <h3 id='surveys'>Surveys</h3>
                                <p>The Contractor shall establish, maintain and assume responsibility for all benchmarks and grid lines, and all
                                    other levels, lines, dimensions and grades that are necessary for the execution of the Work, in conformity
                                    with the Contract Documents.</p>
                                <p>The Contractor shall be entirely and exclusively responsible for the horizontal, vertical and other alignment
                                    for all levels and dimensions and for the correctness of every part of the Works, and he shall rectify
                                    effectively any errors or imperfections therein.</p>
                                <p>The Contractor shall give at least five working days’ notice in writing when he will require the services of
                                    the Engineer for laying out any portion of the Work. The Contractor shall provide all the instruments and
                                    attendance required by the Engineer for checking the Work. Contractor is responsible for the correctness of
                                    the same.</p>
                                <p>The Contractor shall entirely at his own cost amend to the satisfaction of the Engineer any error found at
                                    any stage which may arise through inaccurate settings in relation to the set out and level of the Work which
                                    are provided by the Employer’s Representative.</p>
                                <p>The inspection of any points, lines and levels by the Engineer shall not in any way relieve the Contractor of
                                    his responsibility for the accuracy thereof and the Contractor shall carefully protect and preserve all
                                    apparatus used in setting-out the Works</p>
                                <h2 id='site-protection'>SITE PROTECTION</h2>
                                <h3 id='excavated-slopes'>Excavated slopes</h3>
                                <p>Not Applicable.</p>
                                <h3 id='noise-and-dust'>Noise and Dust</h3>
                                <p>Due to the proximity of residential developments, mitigation of dust and noise shall be required. Due care
                                    shall be required to ensure that silencers and baffles are fitted to all plant and equipment, where
                                    applicable.</p>
                                <h3 id='storm-water-curing-water'>Storm Water &amp; Curing Water</h3>
                                <p>Not Applicable.</p>
                                <h3 id='ground-surfaces'>Ground Surfaces</h3>
                                <p>On completion of sub-structure work, the Contractor shall reinstate the ground surfaces of the site to match
                                    the condition existing at the commencement, or as required for subsequent construction.</p>
                                <p>The Contractor shall smooth and level disturbed surfaces generally.</p>
                                <p>The Contractor shall merge new surfaces with existing adjacent surfaces to ensure continuity of level and
                                    finish.</p>
                                <h3 id='protection-of-public-and-adjoining-properties'>Protection of Public and Adjoining Properties</h3>
                                <p>The Contractor shall at all times so conduct his operations as to cause the least possible obstruction and
                                    inconvenience to traffic and the general public and the residents in the vicinity of the Work, to protect
                                    persons and property, and to preserve access to driveways, houses and buildings. The Contractor shall have
                                    due regard to the rights of the public and shall not create any public nuisance. No road, street or highway
                                    shall be closed to the public except with the permission and in accordance with the requirements of the
                                    proper authorities.</p>
                                <h3 id='protection-of-environment'>Protection of Environment</h3>
                                <p>The Contractor understands that the Site is free from pollutants at the time of access to the Site and
                                    commencement of the Works. The Contractor shall comply with all applicable environmental laws, regulations
                                    and guidelines and shall ensure that the Site is and remains free from pollutants at the end of the Project.
                                    The Contractor shall ensure inter alia, that neither the soil nor the ground water is polluted or
                                    contaminated by fuels or lubricants emitted by machinery operated on the Site or by other dangerous or
                                    poisonous substances which are or are deemed to be hazardous to the environment. Notwithstanding the above,
                                    the Contractor shall comply with all the directions and decisions of the Engineer in this regard and all
                                    applicable environmental legislations in relation to the same including obtaining statutory consents and
                                    approvals as may be required.</p>
                                <p>Where applicable, it is the sole responsibility of the Contractor to obtain the approvals and clearances from
                                    the relevant authorities and submit to the Engineer before commencement of relevant portion of the Works.
                                </p>
                                <p>It is obligatory for the contractor to follow all guidelines enclosed with the contract documents and provide
                                    all information, documentation &amp; photographs as may be required from time to time during the entire
                                    course of the project.</p>
                                <h2 id='contractor-s-site-organization-and-resources'>CONTRACTOR’S SITE ORGANIZATION AND RESOURCES</h2>
                                <h3 id='contractor-s-representative-and-supervisory-staff'>Contractor’s Representative and Supervisory Staff
                                </h3>
                                <p>The Contractor shall provide and ensure continued effective supervision of the Work with the help of the
                                    Contractor’s Representative, assisted by full time qualified, experienced, and competent supervisors and
                                    adequate staff, to the satisfaction of the Engineer for the entire duration of the Work.</p>
                                <p>Key Site Personnel shall include:</p>
                                <p>Project Manager</p>
                                <p>Trade Engineers</p>
                                <p>Planning Engineer</p>
                                <p>Billing Engineer</p>
                                <p>QA/QC Engineer</p>
                                <p>HSE In-Charge</p>
                                <p>All key staff employed at the Site by the Contractor shall be considered essential to the performance of the
                                    Works, and all key staff shall be subject to the approval of the Employer’s Representative. However, such
                                    approval shall not relieve the Contractor of any of its contractual obligations. No staff including the
                                    resident Contractor’s Representative and other technical supervisory staff shall be removed or transferred
                                    from the Work without the prior written permission of the Employer’s Representative.</p>
                                <p>The Engineer shall, however, have the authority to order the removal from Site of any undesirable Contractor
                                    personnel due to their misconduct, incompetence, or negligence in the performance of their duties.</p>
                                <p>If key staff becomes unavailable for assignment to the Works for reasons beyond the Contractor’s control, the
                                    Contractor shall immediately notify the Engineer and evaluate the impact on the project. Prior to
                                    substitution or addition of any key staff, the Contractor shall obtain the Engineer written consent as to
                                    the acceptability of replacements or additions to such personnel.</p>
                                <p>Non-appointment of Project Manager within stipulated time may result in Punitive Damages of Rs 50,000/- per
                                    week of delay from the mutually agreed deployment date.</p>
                                <p>The Contractor shall at all times be fully responsible for the acts, omissions, defaults and neglect of all
                                    of its representatives, agents, servants, workmen and suppliers and those of its Subcontractors.</p>
                                <h3 id='contractor-s-plant-machinery'>Contractor’s Plant &amp; Machinery</h3>
                                <p>The Contractor shall provide and install all equipment, materials, plant, cranes, hoists, ladders, and
                                    scaffolding, necessary for the execution of the Work in conformity with the Contract Documents and to the
                                    satisfaction of Employer’s Representative.</p>
                                <p>The Contractor shall provide a separate resource histogram indicating the plant and equipment allocation over
                                    the Contract duration. A copy of this histogram is to be issued with the tender submission. However, this
                                    shall be treated as the minimum requirement and the Contractor shall augment additional resources as and
                                    when required to main the progress to achieve completion of works within the stipulated time.</p>
                                <p>All the tools, equipment and machinery provided by contractor for the execution of the Works should be in
                                    perfect condition. Any fault or non-operation of the tools, equipment and machinery, shall be rectified
                                    immediately by the Contractor and no time extension shall be allowed at all in the event of some fault of
                                    non-operation of tools, equipment and machinery.</p>
                                <p>Plant and equipment’s, once employed for the works by the Contractor and approved by the Employer shall not
                                    be removed from the works without prior consent from the Employer’s Representative.</p>
                                <p>All the vehicles / plants proposed to be used shall have valid fitness certificate, calibration certificate
                                    and Emission Certificate as applicable. The operators / drivers shall have valid license. All the documents
                                    of vehicles and licenses of the operators and drivers shall be submitted to the Engineer for verification,
                                    before being deployed in the Works.</p>
                                <h3 id='batching-plant'>Batching plant</h3>
                                <p>Not Applicable.</p>
                                <h3 id='contractor-store-site-offices-and-other-facilities'>Contractor Store, Site offices, and Other Facilities
                                </h3>
                                <p>The Contractor shall provide within the mobilisation period, maintain and keep clean temporary office
                                    accommodation to a reasonable standard for the Contractor’s own staff and his Subcontractors, throughout the
                                    duration of the contract. The Contractor shall install the following facilities for use by its workforce
                                    for:</p>
                                <p>Lockable stores/lay down and storage areas</p>
                                <p>Drinking water points</p>
                                <p>Any other facility mutually agreed between the Employer and the Contractor</p>
                                <p>In the event of store being on upper floor or in an area with basement, the floor loading and the stacking
                                    must be as per the Engineer standards, specifications and guidelines.</p>
                                <p>The Contractor shall prepare a site logistics plan showing the location of these facilities during
                                    mobilisation period and obtain the approval of the Employer’s Representative.</p>
                                <p>The Contractor shall maintain amenities and keep tidy, clean, and in sanitary condition at all times.</p>
                                <p>On completion of the Works or at any other time that may be determined by the Engineer the Contractor shall
                                    remove from site all the temporary accommodation and facilities following receipt of the written permission
                                    of the Employer’s Representative. The Contractor shall remove all buried cables, pipelines, conduits and
                                    tanks installed for the temporary facilities. Clearance of temporary structures shall be deemed part of
                                    achieving completion of Works.</p>
                                <h3 id='labour-camps'>Labour Camps</h3>
                                <p>Labour camps shall not be permitted on the Site. The Contractor shall provide and maintain all necessary
                                    accommodation and welfare facilities for the Contractor’s and Subcontractors’ workmen at his own cost.</p>
                                <h3 id='sanitation'>Sanitation</h3>
                                <p>The Contractor shall provide and maintain adequate toilet facilities for its workmen (separate for men and
                                    women). The Contractor shall provide a foul drainage system to all of his site offices, kitchens, toilets,
                                    first aid room and other rooms as necessary including collection of effluent in purpose made suitably sized
                                    buried storage tank(s). The contractor shall allow for collection and disposal of domestic garbage from
                                    their Office to a suitable approved disposal point.</p>
                                <p>applicable.</p>
                                <h3 id='security'>Security</h3>
                                <p>The Employer will provide general access control for the Site (without any liabilities). All</p>
                                <p>Contractor’s personnel and Sub-Contractors must sign the access register when entering and</p>
                                <p>exiting the Site. CCTV camera should be installed at entry and exit points with dedicated security person by
                                    the contractor.</p>
                                <p>The Contractor shall at all times be fully responsible for the security of its materials and equipment at
                                    Site, whether its own or those of any Subcontractors, and provide adequate number of watchmen for that
                                    purpose. Neither the Employer nor the Engineer shall be responsible for any loss due to theft, fire,
                                    accident or any other reasons, whatsoever.</p>
                                <h3 id='site-safety'>Site Safety</h3>
                                <p>The Contractor shall strictly comply with all the provisions of the HSE Manual and provide all necessary
                                    safety apparatus and facilities for its personnel and its Subcontractors. It shall in particular establish a
                                    fully equipped and staffed first aid centre at Site to deal with accidental injuries and workers health.</p>
                                <p>Protective gear such as safety helmets, boots, belts etc. shall be provided by the Contractor at its own cost
                                    to all its man-power at the Site. The Contractor shall impose such requirements on all Subcontractors also.
                                    It shall be the responsibility of the Contractor to ensure that such protective gear is worn at all times by
                                    all personnel working at the Site during the term of the Project. The Employer and Engineer shall each have
                                    the right to stop any person not wearing such protective gear from working on the Site.</p>
                                <p>Regular safety audit shall be carried out by the Employer’s Representative.</p>
                                <p>In case the Contractor fails to make arrangements and provide necessary facilities as aforesaid, the Employer
                                    shall be entitled to do so and recover the costs thereof from the Contractor or impose financial penalty
                                    upon the Contractor. The decision of the Engineer in this regard shall be final and binding on the
                                    Contractor.</p>
                                <h3 id='fire-protection'>Fire protection</h3>
                                <p>The Contractor shall continuously maintain adequate protection for the Works against fire and other hazards
                                    and shall protect the Employer’s property and adjacent property from damage or loss during the performance
                                    of the Contract.</p>
                                <p>All combustible material, food matter, garbage, scrap, and other debris generated during the performance of
                                    the Work shall be collected and removed from the Site daily.</p>
                                <p>An adequate number and type of fire extinguishers shall be provided at the Site for fire control and shall be
                                    kept/maintained in satisfactory and effective working condition, at all times.</p>
                                <p>Fire blankets shall be used for all welding operations.</p>
                                <h3 id='scaffolding-staging-guard-rails-barricades'>Scaffolding, staging, guard rails, barricades</h3>
                                <p>The Contractor shall at its cost provide steel scaffolding, staging, required during construction. The
                                    supports for the scaffolding shall be strong, adequate for the particular situations, tied together with
                                    horizontal pieces and braced properly.</p>
                                <p>The temporary access to the various parts of the building under construction shall be rigid and strong enough
                                    to avoid any chance of mishaps. The entire scaffolding arrangement together with the staging, guard rails,
                                    barricades and safety barriers, and temporary stairs shall be to the approval of the Engineer which approval
                                    shall not relieve the Contractor of any of its responsibilities, obligations and liabilities for safety and
                                    for timely completion of the Work.</p>
                                <p>The use of wooden scaffolding at Site is strictly forbidden.</p>
                                <h3 id='temporary-lighting-and-ventilation'>Temporary Lighting and Ventilation</h3>
                                <p>The Contractor shall make necessary arrangements in respect of the provision of adequate lighting and
                                    ventilation (natural as well as artificial) at all places where its workmen are engaged for carrying out the
                                    Work in a proper, safe and satisfactory manner. The Contractor shall also provide general lighting in common
                                    areas such as entrances, staircases, etc with minimum lux level requirements besides illuminating the
                                    workplaces.</p>
                                <h3 id='housekeeping'>Housekeeping</h3>
                                <p>The Contractor shall be required to maintain the site works and surroundings in a neat and orderly manner
                                    free from accumulating debris, haphazard stacking of materials, unhygienic and unsafe environment; cleaning
                                    of site at all levels inside and outside, removal of unwanted materials, packing cases, etc. shall be
                                    undertaken at least once on daily basis. The Contractor shall nominate the safety officer to be responsible
                                    for housekeeping. Unwanted materials and debris shall be carted away from site and disposed off on a daily
                                    basis outside the premises.</p>
                                <p>The Contractor shall maintain the Site and all work thereon in neat, clean and tidy conditions at all times.
                                    The Contractor shall remove all rubbish and debris from the Site on daily basis and as directed by the
                                    Employer’s Representative. Suitable steel skips shall be provided at strategic locations around the Site to
                                    receive waste and packaging materials.</p>
                                <p>Just prior to the Taking-Over of the Works, or whenever so directed by the Employer’s Representative, the
                                    Contractor shall carry out all the work necessary to ensure that the Site is clear and the Works are clean
                                    in every respect, the surplus materials, debris, sheds and all other temporary structures are removed from
                                    the Site, all plant and machinery of the Contractor are removed from site, the areas under floors are
                                    cleared of rubbish, the gutters and drains are cleared, the doors and sashes are eased, the locks and
                                    fastenings are oiled, all electrical, plumbing and other services are tested and commissioned, the keys are
                                    clearly labelled and handed to the Employer’s Representative, so that at the time of Taking-Over the whole
                                    Site and the Works are left fit for immediate occupation and use, to the approval and satisfaction of the
                                    Employer’s or its Representative.</p>
                                <p>Should the Contractor fail to comply with the cleaning requirements, whether progressively or before
                                    completion, or fail to clear the Site as directed and required, then the Employer’s Representative, after
                                    giving due notice in writing to the Contractor, shall have the right to employ other persons or agencies to
                                    carry out the cleaning and/or clearing work and all costs incurred on such work including Engineer
                                    administration costs shall be recovered from the Contractor and shall be deducted by the Employer from any
                                    money that may be payable or that may become payable to the Contractor</p>
                                <h3 id='temporary-power-and-water-supply'>Temporary Power and Water Supply</h3>
                                <p>Refer to the Appendix to Tender.</p>
                                <h2 id='quality-assurance'>QUALITY ASSURANCE</h2>
                                <h3 id='quality-plan'>Quality Plan</h3>
                                <p>The Contractor shall have a well-established system for all kinds of construction documentation generated on
                                    the project. The Engineer shall conduct an alignment session with contractor at the time of kick-off
                                    meeting, explaining the contractor about the standard procedures to be adopted for specific documentation
                                    like Technical Submittals, Request for Information, Non- conformance notices, change requests, Site
                                    Instructions, Invoicing procedures, Construction start-up, Schedules, Drawings, and all other procedures as
                                    indicated by the Employer’s Representative. The Contractor shall be responsible to follow those procedures,
                                    wherever</p>
                                <p>applicable to them, for the execution of work. Immediately after the contract award, the Engineer shall
                                    arrange for a Kick-off meeting and Contractor shall be bound to fulfil all the requirements mentioned in
                                    that meeting.</p>
                                <p>The Contractor shall maintain and make available all the records pertaining to quality checks, registers and
                                    tests, to the Engineer during audits by them and make necessary corrections, additions or actions based upon
                                    the findings / observations of the audits. Inspection and test plans shall be implemented as per the formats
                                    approved by / recommendation of the Employer’s Representative.</p>
                                <p>The Contractor will be required to submit a Project Specific Quality Plan within 14 days of the award of the
                                    Contract. The Quality Plan will be structured in the following format and detail the following provisions to
                                    be implemented during the Contract:</p>
                                <p>Project Scope of Works.</p>
                                <p>Company Quality Manual &amp; Procedures.</p>
                                <p>Contract Documentation</p>
                                <p>Control of Subcontractors Works</p>
                                <p>Procurement and Manufactures</p>
                                <p>Provision of stage quality checklists.</p>
                                <p>Samples / mock-ups / job standards</p>
                                <p>Offsite quality management.</p>
                                <p>Information Management.</p>
                                <p>Method Statements</p>
                                <p>Inspections and test equipment.</p>
                                <p>Workmanship.</p>
                                <p>Training.</p>
                                <p>Corrective Action Procedures.</p>
                                <p>Maintenance of Records</p>
                                <p>Maintenance of Test Certificates</p>
                                <p>Handover Procedure</p>
                                <p>The Contractor is to employ suitably qualified full-time Quality Managers to manage the various quality
                                    issues on site and to ensure that all samples, mock-ups and job standards are provided in a timely manner
                                    for the Engineer inspection.</p>
                                <p>If the Contractor does not submit the construction methodology and Quality Plan or if the Engineer finds
                                    Contractor’s submitted construction methodology and Quality Plan as inadequate for execution of work, then
                                    the Contractor should follow the Engineer Quality Plan and all work procedures. A “documented NC” will be
                                    issued to the Contractor in case of any Non-compliance are found against the approved Drawings,
                                    Specifications, PQP, relevant Work Procedures, codes as applicable by the Employer’s Representative. The
                                    Contractor shall provide necessary resources to implement the quality management plan. Any deviation from
                                    quality requirements and acceptance criteria will lead to imposing damages on the Contractor after giving
                                    adequate chance to the Contractor for rectification. Notwithstanding any damages imposed, the Engineer shall
                                    be further entitled to take action as specified under the Conditions of Contract “Defective Works” in this
                                    document.</p>
                                <p>One set of copies of all relevant IS or any other codes shall be provided and shall be made available at Site
                                    office of the Contractor at all times. The same shall be made available to the Engineer free of cost, if
                                    requested by the Employer’s Representative.</p>
                                <h3 id='method-statements'>Method Statements</h3>
                                <p>The Contractor shall provide detailed trade-wise method statements that describe:</p>
                                <p>the arrangements and methods proposed to be adopted for the execution of the Works</p>
                                <p>details of the plants/equipment and manpower to be utilised All Method Statements are to be issued to the
                                    Engineer for his consent.</p>
                                <p>Hazardous activities shall be spelt out clearly in the detailed method statements.</p>
                                <p>The structure of the method statements shall be mutually agreed between the Engineer and the Contractor.</p>
                                <h2 id='programme'>PROGRAMME</h2>
                                <p>Contractor shall submit a detailed construction schedule in electronic format (MS Project or Primavera). The
                                    Contractor shall identify all major activities for engineering, procurement and construction including
                                    temporary work, fabrication and erection, their durations and interrelationships, consistent with the
                                    construction methodology and milestones to complete the Works within the Time for Completion.</p>
                                <p>In addition to the construction schedule, the Engineer may, from time to time, request additional programmes
                                    to further explain how specific sections of the works are to be carried out in order to achieve the dates in
                                    the construction schedule.</p>
                                <p>The Programme shall demonstrate</p>
                                <p>The order in which the Contractor proposes to carry out the Works and the time limits for the carrying out of
                                    each activity, or group of activities with links and leads, lags and constraints in accordance with the
                                    proposed sequence of construction.</p>
                                <p>Key interfacing activities including design submittals, major equipment / material deliveries, off-site
                                    manufacturing processes, Statutory Authorities processes and all other external constraints that may affect
                                    the completion of the Works.</p>
                                <p>The time limits within which the submission of any drawings, specifications or other submissions produced by
                                    the Contractor and approval by the Engineer are required.</p>
                                <p>The latest dates by which the Engineer shall supply all drawings and information with respect to each
                                    activity or group of activities. These dates may be supplied in separate information schedules.</p>
                                <p>Commencement dates for the jointly procured Provisional Sums.</p>
                                <p>All major assumptions such as time units used, etc, shall be indicated.</p>
                                <p>Imposed dates and contractual dates shall be shown as calendar dates.</p>
                                <p>Commissioning dates including tests on completion dates</p>
                                <p>Critical Path Analysis</p>
                                <p>The Contractor shall also submit to the Employer’s Representative:</p>
                                <p>Labour deployment plan, building-wise, activity-wise month-wise.</p>
                                <p>Logistics plan for plant, machinery, and site facilities</p>
                                <p>Material procurement &amp; delivery plan</p>
                                <p>The Contractor shall update and revise the above schedules every month with recovery plan to catch up next
                                    milestone in case of slippage. The recovery plan shall also include time and details of deployment of
                                    additional resources to achieve next milestone. Each updated and revised schedule shall be submitted to the
                                    Engineer for approval simultaneously with the Contractor’s application for progress payment for the same
                                    time period.</p>
                                <p>The Contractor shall submit every three months look ahead plan for a period of 90 days for procurement of
                                    materials, manpower and machinery planned.</p>
                                <p>All programmes supplied by the Contractor shall be compatible with each other and shall use the same
                                    specified code structure.</p>
                                <h2 id='drawings-specifications-interpretations'>DRAWINGS, SPECIFICATIONS, INTERPRETATIONS</h2>
                                <h3 id='contractor-s-design'>Contractor’s Design</h3>
                                <p>Where the Contractor is required to carry out design for any part of the Works, it shall produce design
                                    documents so that the Works, when completed in accordance with those design documents shall:</p>
                                <p>Be fit for the required purpose.</p>
                                <p>Comply with the requirements of the Contract and all local laws.</p>
                                <p>The personnel / consultants engaged by the Contractor to carry out design work shall be professionally
                                    competent for the purpose.</p>
                                <h3 id='drawings-issued-to-contractor'>Drawings Issued to Contractor</h3>
                                <p>Two copies of all drawings and their subsequent revisions will be issued to the Contractor via listing on
                                    transmittals by the Employer’s Representative. The Contractor shall maintain a drawing register listing all
                                    drawings and their latest revisions. All superseded drawings shall be so stamped and withdrawn from
                                    circulation at the Site. It shall be the responsibility of the Contractor to ascertain and ensure that all
                                    the Works are carried out in accordance with the latest revisions of the drawings issued to him. Should the
                                    Contractor fail to do this, all the rectifications and remedial work that may be required to conform to the
                                    latest revisions of the drawings shall be at the Contractor&#x27;s cost and nothing extra shall be payable.
                                </p>
                                <p>The Contractor, in the execution of the Work, shall make no deviations from the drawings, specifications, and
                                    other Contract Documents. Interpretations and clarifications shall be issued by the Employer’s
                                    Representative.</p>
                                <p>No scaling of any drawing shall be done to obtain the dimensions. Figured dimensions on the drawings shall be
                                    used for carrying out the Work. Drawings with large scale details shall take precedence over small scale
                                    drawings. Where any drawings and details have not been provided but are necessary for the execution of the
                                    Work, it shall be the responsibility of the Contractor to seek these drawings and details in writing from
                                    the Engineer at least four weeks prior to the latest date by which the Contractor needs these drawings and
                                    details to suit the programmed execution of the Work. No extension of time shall be allowed for any delays
                                    caused due to the Contractor&#x27;s failure to seek such details.</p>
                                <h3 id='shop-drawings-product-data'>Shop Drawings, Product Data</h3>
                                <p>Definitions</p>
                                <p>Shop drawings are defined as drawings, diagrams, schedules and other data specially prepared for the work by
                                    the Contractor or any of the Subcontractors, manufacturers, suppliers or distributors to illustrate some
                                    portion of the work and includes fabrication, erection, layout, setting out drawings, manufacturers standard
                                    drawings, schedules, descriptive literature, illustrations catalogues, brochures, performance and test data,
                                    wiring and control diagrams and other drawings and descriptive data pertaining to materials equipment,
                                    piping ducting and conducting systems as requested to show that the materials, equipment or systems and
                                    position there to conform to the Contract Documents.</p>
                                <p>The term “manufactured” as used in the Contract applies to standard units usually mass produced. The term
                                    “fabricated” as used in the Contract means items specifically assembled or made out of selected materials to
                                    meet individual design requirements.</p>
                                <p>Shop drawings shall establish actual detail of all manufactured or fabricated items, indicate proper relation
                                    to adjoining work, amplify design details of mechanical and electrical</p>
                                <p>installations in proper relation to physical spaces in the structure and incorporate minor changes of design
                                    or construction to suit actual conditions.</p>
                                <p>Product Data is defined as illustrations, standard schedules, performance charts, illustrations, brochures,
                                    diagrams and other information furnished by the Contractor to illustrate a material product or system for
                                    some portion of the work.</p>
                                <p>Samples are defined as physical examples, which illustrate materials, equipment or workmanship and establish
                                    standards by which work will be judged.</p>
                                <p>The Contractor shall be responsible for developing typical details indicated in the Consultant’s drawings
                                    into construction details through the use of his own expertise and that of the specialist subcontractors
                                    employed by the Contractor.</p>
                                <p>No portion of work requiring submission of a Shop Drawings, Product Data, Samples and Coordination Drawings
                                    shall commence until the submittal has been approved by the Employer’s Representative. All such portions of
                                    the work shall be in accordance with approved submittals.</p>
                                <p>Unless otherwise specified or directed by the Engineer the Contractor shall submit to the Engineer for his
                                    review and approval all shop drawings, samples, material lists, equipment procurement dates, instruction
                                    manuals, record documents, manufacturer’s equipment manuals and other information required by the contract
                                    documents. Submittals and their contents including deviation shall be properly prepared, identified and
                                    transmitted as provided herein or as the Engineer may otherwise direct. Except for record documents and
                                    instruction manuals for operation and maintenance, submittal including deviation shall be approved before
                                    the material or equipment covered by the submittal is delivered to the site. The Contractor shall ensure
                                    that the submission of samples or shop drawings and any other information to be submitted by the Contractor
                                    to the Engineer shall be in accordance with the project schedule set out under contract. Unless specifically
                                    authorized by the Engineer in writing, all samples or Shop Drawings must be submitted by the Contractor for
                                    approval not less than thirty (30) days before the date the particular Work involved is scheduled to begin.
                                </p>
                                <p>The Engineer shall check and approve such samples or shop drawings, with reasonable promptness only for
                                    conformity with the design intent of the Project and for compliance with the specifications including the
                                    Technical Specifications set out in the Contract Documents. The Work shall be carried out by the Contractor
                                    in accordance with the approved samples or Shop Drawings.</p>
                                <p>Shop drawings shall be submitted in hardcopies (3 Set) for approval in A1/A0 format only as approved by
                                    Engineer.</p>
                                <p>Methodology</p>
                                <p>BBS/SD shall be prepared in AutoCAD format and submitted for approval at minimum two weeks in advance of
                                    planned material order or works execution to allow the Engineer</p>
                                <p>/Architect/Consultant ample time for scrutiny. No claims for extension of time shall be entertained because
                                    of any delay in the work due to his failure to produce BBS/SD at the right time, in accordance with the
                                    approved programme.</p>
                                <p>Samples of all the materials so specified shall be submitted to the Engineer prior to procurement. These will
                                    be submitted in triplicate for approval and retention by the Engineer and Architect and shall be kept in
                                    their site office for reference and verification till the completion of the Project.</p>
                                <p>Approval of BBS/SD shall not be considered as a guarantee of measurements or of building dimensions. Where
                                    drawings are approved, said approval does not mean that the drawings supersede the Contract requirements,
                                    nor does it in any way relieve the Contractor of the responsibility or requirement to furnish material and
                                    perform work as required by the Contract.</p>
                                <p>Where the work of the Contractor has to be installed in close proximity to, or will interfere with work of
                                    other trades, he shall assist in working out space conditions to make a satisfactory adjustment. If so
                                    directed by the Employer’s Representative, the Contractor shall prepare composite working drawings and
                                    sections at a suitable scale not less than 1:50, clearly showing how his work is to be installed in relation
                                    to the work of other trades. If the Contractor installs his work before coordinating with other trades, or
                                    so as to cause any interference with work of other trades, he shall make all the necessary changes without
                                    extra cost to the Employer.</p>
                                <p>Within two weeks of approval of all the relevant BBS/SD, the Contractor shall submit a statement of variation
                                    in Contract quantities. The Contractor shall make recommendation to the Engineer for acceptance of
                                    anticipated variation in Contract amounts and also advise the Engineer to initiate action for procurement of
                                    additional materials for the completion of project if required.</p>
                                <h3 id='samples'>Samples</h3>
                                <p>Where required in the Specifications, the Contractor shall submit control samples of products and materials,
                                    sections, components and finishes, indicating colour, gloss, pattern, texture and the like. The Contractor
                                    shall provide first installed examples where necessary.</p>
                                <p>The Contractor shall label or mark each sample stating the product name, manufacturer’s reference number,
                                    name of colour, contact details and date, and cross reference to transmittal number.</p>
                                <p>Un-labelled samples will not be accepted.</p>
                                <p>Where finishes are subject to variation, each sample shall be a set of three samples indicating the typical
                                    finish and the limits of variation.</p>
                                <p>All samples to be retained at the designated area within the site premises.</p>
                                <p>Where variations in texture, color, grain or other characteristics are inherent and anticipated in the
                                    samples submitted, a sufficient quantity shall be provided by the Contractor to the Engineer to indicate the
                                    full range of characteristics, which will be present in the materials having variations.</p>
                                <p>Acceptance of any sample by the Engineer shall be only for characteristics for uses named in such acceptance
                                    and for no other purpose/use. Acceptance of a sample shall not be taken to change or modify any requirement
                                    of the Contract provisions. Once a material has been accepted, no further change in brand or make will be
                                    permitted without prior approval of the Employer’s Representative.</p>
                                <p>The Contractor shall execute samples of workmanship for the Engineer as and when required.</p>
                                <p>The Contractor must obtain the Engineer prior written approval of the respective samples of workmanship and
                                    prototypes before proceeding with the execution of the various sections of the Works.</p>
                                <p>The finished Work must correspond to the approved samples of materials, workmanship and prototypes. The
                                    Engineer at its sole discretion may return certain samples for use in the Works. These shall be installed in
                                    good condition and suitably marked for identification. Such samples and any packing are to be provided at
                                    the expenses of the Contractor for the use of the Engineer and are to be displayed in a sample room.</p>
                                <p>In the event of non-compliance with samples of materials, workmanship and prototypes by the Contractor, the
                                    Engineer may reject the works and the Contractor shall have to rectify such defective works in accordance
                                    with the provisions of the contract.</p>
                                <h3 id='approvals'>Approvals</h3>
                                <p>All drawing, material, method statements and specification transmittals will be approved in accordance with
                                    the following regime:</p>
                                <p>The Contractor is to allow in the programming of the Works for the following approval periods:</p>
                                <p>RFIs 3 calendar days</p>
                                <p>Material Submittals 7 calendar days</p>
                                <p>Method Statements 7 calendar days</p>
                                <p>Shop Drawings 7 calendar days</p>
                                <p>Specifications 7 calendar days</p>
                                <p>These durations will commence from the date of receipt by, and the date of issue by, the Project Site Office.
                                </p>
                                <h3 id='ordering-and-delivery-of-materials-and-equipment-for-the-work'>Ordering and Delivery of Materials and
                                    Equipment for the Work</h3>
                                <p>The Contractor shall place its orders for the specified materials and equipment at the earliest possible date
                                    upon the execution of the Contract for the Works or at such times as may be specifically stated elsewhere
                                    herein for any particular material or equipment.</p>
                                <p>The Contractor shall be required to produce and submit to the Engineer all shipping documents and any other
                                    documentary proofs of such placement of orders such as factory work orders, packing list, bill of loading,
                                    forwarder advice etc., within one month prior to the scheduled delivery date.</p>
                                <p>In case the Contractor fails to submit as aforesaid and thus likely to cause interruption or delay in the
                                    progress of the Works, then the Engineer shall be at liberty to direct the Contractor to air- freight the
                                    same without any additional cost to the Employer.</p>
                                <h2 id='materials-workmanship-storage-inspections'>MATERIALS, WORKMANSHIP, STORAGE, INSPECTIONS</h2>
                                <h3 id='materials-and-workmanship'>Materials and Workmanship</h3>
                                <p>The Contractor shall be responsible for the establishment of a full and comprehensive quality control system
                                    for the Works. The system shall include, but not be limited to, the means of controlling the testing and
                                    receipt of materials, the inspection of the Works, the filing and ordering of drawings and correspondence
                                    and the duties and responsibilities of staff members.</p>
                                <p>All materials and equipment to be incorporated in the Work shall be new and as per applicable Codes. The
                                    materials, equipment, and workmanship shall be of the best quality of the specified type, in conformity with
                                    Contract Documents and the best engineering and construction practices, comply with the specifications and
                                    to the entire satisfaction of Employer’s</p>
                                <p>Representative. This requirement shall be strictly enforced at all times and stages of the Work and no
                                    request for change whatsoever shall be entertained on the grounds of anything to the contrary being the
                                    prevailing practice. The Contractor shall immediately remove from the Site any materials, equipment and/or
                                    workmanship which, in the opinion of the Engineer are defective or unsuitable or not in conformity with the
                                    Contract Documents and best Engineering and construction practices, and the Contractor shall replace such
                                    rejected materials, equipment and/or workmanship with proper, specified, and required and approved
                                    materials, equipment and/or workmanship, all at its own cost within a period of fourteen (14) days from the
                                    date of issuance of such notice.</p>
                                <p>The Contractor shall, whenever required to do so by the Engineer immediately submit satisfactory evidence and
                                    necessary test results as to the kind and quality of the materials and equipment.</p>
                                <h3 id='special-makes-or-brands'>Special Makes or Brands</h3>
                                <p>Where special makes or brands are called for, they are mentioned as a standard. Others of equivalent quality
                                    may be used provided the substituted materials as being equivalent to the brand specified, and prior
                                    approval for the use of such substituted materials is obtained in writing from the Employer’s
                                    Representative. Unless substitutions are approved by the Engineer in writing in advance, no deviations from
                                    the Specifications and other Contract Documents shall be permitted, the Contractor shall indicate and submit
                                    written evidence of those materials or equipment called for in the Specifications and other Contract
                                    Documents that are not obtainable for incorporation in the Work within the time limit of the Contract.
                                    Failure to indicate this in writing within two weeks of the signing of the Contract will be deemed
                                    sufficient cause for denial of any request for an extension of time because of the same.</p>
                                <p>Alternative equivalent brands if suggested by the Contractor during construction may be considered provided
                                    the suggested brand fully meets the requirements and is acceptable to the Employer’s Representative. Any
                                    variation in price due to the use of alternate brands shall be permissible provided it is pre-approved in
                                    writing by the Employer’s Representative.</p>
                                <h3 id='free-issue-material-by-employer'>Free Issue Material by Employer</h3>
                                <p>In case of Material supplied by Employer free of cost at site, the unloading cost &amp; cost of testing to be
                                    borne by the contractor and the wastage as allowed in the Particular Conditions / Appendix to tender.</p>
                                <p>Contractor must verify the quantity &amp; the same shall be under his custody. For all receipt of the free
                                    issue materials, the weighment / measurements shall be witnessed / carried out jointly by the representative
                                    of the Engineer and the authorized representative of Contractor. The material after weighment / measurement
                                    shall stand final and immediately issued to the Contractor. The weighment charges shall be borne by the
                                    contractor.</p>
                                <p>On receipt of materials at site, the contractor / his authorised representative will check the materials
                                    jointly with the Supplier’s representative and check the same. Defects, if any, including shade variation
                                    shall be brought to the notice of the Employer, before accepting or rejecting the materials, within 7 days.
                                    If the contractor fails to do so, then the contractor shall be liable at his cost for all the consequences
                                    including replacements of the materials at a later date, if so, desired by the Employer’s Representative.
                                </p>
                                <p>The materials supplied at Site shall be transported to the point of work by the Contractor at his own cost.
                                    No handling or transportation charges shall be paid on this account.</p>
                                <p>The cumulative reconciliation statement for the materials supplied by the Employer up to the date of
                                    submission of Running Account Bill shall be submitted along with the bills. The Engineer shall not accept
                                    bills which are not submitted along with a valid reconciliation statement. The</p>
                                <p>materials supplied at site shall be transported to the point of work by the Contractor at his own cost. No
                                    handling or transportation charges shall be paid on this account.</p>
                                <p>Notwithstanding the reconciliation statement submitted by the contractor, if found necessary the Engineer at
                                    any stage shall make a theoretical assessment of the materials consumed in the works. The difference in the
                                    quantity of items actually issued to the contractor and the theoretical quantity that is to be consumed,
                                    including the permitted wastage / loss as specified in clause 1 i.e. (Excess wastage) shall be recovered
                                    from the contractor at the rate fixed as the weighted average rate that the material had during the contract
                                    period, multiplied by a factor</p>
                                <p>1.25 (One point two five).</p>
                                <p>The materials issued from the Employer’s stores shall be fully accounted for as required hereinafter. In
                                    accounting for the Employer’s materials issued to the Contractor, allowances, as indicated below against
                                    each item, will be made to cover all wastage and loss that may have been incurred in the process of
                                    handling.</p>
                                <p>The Contractor shall, at all times when requested, satisfy the Engineer /Employer by the production of
                                    records or books or submissions of returns that the materials supplied are being used for the purpose for
                                    which they are supplied and the Contractor shall at all times keep the records updated to enable the
                                    Engineer /Employer to apply such checks as he may desire to impose. The Contractor shall, at all times,
                                    permit the Engineer /Employer’s representatives to inspect his godown. The Contractor shall not, without
                                    prior written permission of the Employer’s Representative, utilise or dispose of the materials for any
                                    purpose other than that intended in the Contract.</p>
                                <p>The Contractor shall submit to the Engineer at least one month in advance his quarterly requirement for
                                    materials. The materials shall be ordered by the Employer based on the indents submitted by the Contractor.
                                    Payment for such materials shall be made directly by the Employer. The Contractor will be informed of the
                                    orders placed with suppliers and shall make all necessary arrangements for the collection and storage at
                                    site of such materials.</p>
                                <p>The Contractor shall at his cost make his own arrangements for the storage of materials at the work site as
                                    required and instructed by Employer’s Representative. The Handling and storage facility for cement shall be
                                    so arranged that no cement shall be kept in storage for more than 90 (ninety) days from the date of receipt
                                    of cement from the factory. If any cement is kept for more than 90 (ninety) days in the Contractor’s
                                    storage, it shall be tested at the Contractor’s cost in an approved laboratory and until the result of such
                                    tests are found satisfactory shall not be used in any work. If it is found defective in any way, it shall
                                    not be used. The contractor shall make assessment of the materials to be consumed in the works at the
                                    beginning of the work. However, the decision of the Employers Representative regarding the assessment shall
                                    be final. The cost of handling, storage, loss due to wastage, theft etc. shall be borne by the contractor.
                                </p>
                                <p>The Employer will not be responsible for any delay in the supply of material. Delay due to late supply of
                                    materials will, however, be given due consideration for granting an extension of time for the completion of
                                    the Works, if, in the opinion of the Engineer the Works have actually been delayed on this account. The
                                    decision of the Engineer will be final and binding in this regard. No compensation or any claim for damages
                                    or idle time will be entertained by the Employer on this account.</p>
                                <p>All materials other than those listed above shall be obtained by the Contractor at his own cost.</p>
                                <h3 id='materials-delivery-storage-and-handling'>Materials Delivery, Storage, and Handling</h3>
                                <p>The Contractor shall be responsible for proper unloading, storage, protection and handling of materials. The
                                    Contractor shall provide a method statement to this effect at the commencement of work.</p>
                                <p>The Contractor shall be responsible for unloading, storage and handling of any Employer supplied materials.
                                    The contractor shall verify the delivery notes and quantities and notify the discrepancies / damages if any
                                    to the Engineer immediately.</p>
                                <p>The Contractor shall, at its own cost, provide adequate storage sheds and yards at the Site, at locations
                                    pre-approved by the Employer’s Representative, for all materials and equipment that are to be incorporated
                                    in the Work. This shall be for all the materials and equipment, supplied by the Contractor or any
                                    Sub-Contractor. In addition to being water-tight and weather-proof, the storage facilities shall be of such
                                    a manner that all the materials and equipment are adequately protected in every way from any deterioration
                                    or contamination or damage whatsoever, and to the complete satisfaction of the Employer’s Representative.
                                </p>
                                <p>The method of storing of all the materials and equipment shall be in conformity with the Specifications
                                    and/or to the directions and instructions of the Employer’s Representative. At no time shall any material or
                                    equipment be stored in open or in contact with the ground.</p>
                                <p>Should any of the materials or equipment deteriorate or be contaminated or damaged in any way due to improper
                                    storage or for any other reason than such materials and equipment shall not be incorporated in the Work and
                                    shall be removed forthwith from the Site and the replacement of all such materials and equipment shall be
                                    entirely at the cost and expense of the Contractor.</p>
                                <p>Where, after permission has been sought and obtained from the Employer’s Representative, any material or
                                    equipment is kept on any portion of the structure, this shall be done in such a manner as to prevent any
                                    overloading whatsoever of the structure, to the complete satisfaction. The cost associated with any damage
                                    to any portion of the structure in this respect shall be to the account of the Contractor and shall be borne
                                    by him.</p>
                                <p>Should delays be caused on account of removal and replacement of any materials or equipment or on account of
                                    any lack of security, the Contractor shall not be entitled to any extension of time or increase in the
                                    Contract Price.</p>
                                <p>Hazardous materials shall be stored in accordance with the manufacturer’s recommendations.</p>
                                <h3 id='right-type-of-workmen-plant-and-machinery'>Right Type of Workmen, Plant, and Machinery</h3>
                                <p>The Contractor shall employ the right type of workmen, plant and machinery, jigs, tools etc. to fabricate
                                    and/or install all materials and equipment. They shall be fabricated and/or installed without any damage and
                                    in accordance with the manufacturer&#x27;s instructions and manuals, and to the satisfaction of the
                                    Employer’s Representative.</p>
                                <p>The Contractor shall submit to the Engineer as and when required by the Employer’s Representative.</p>
                                <p>Detail calculation of labour requirement</p>
                                <p>Alternate labour arrangement in peak period</p>
                                <h3 id='artists-and-tradesmen'>Artists and Tradesmen</h3>
                                <p>The Contractor shall permit the execution of Work not forming part of this Contract by artists, tradesmen or
                                    other persons engaged by the Employer. Each such person shall be deemed to be a person for whom the Employer
                                    is responsible and such person shall not be deemed to be a Nominated Subcontractor / Direct Subcontractor.
                                </p>
                                <h3 id='workmanship-productivity'>Workmanship / Productivity</h3>
                                <p>The quality of workmanship produced by skilled, knowledgeable and experienced workmen, mechanics and artisans
                                    shall be ensured by the Contractor. Particular attention shall be given to the appearance and finish or
                                    exposed Work. Workmanship will be consistent with the standards specified in the specifications including
                                    the tolerances. In case the specifications do not deal</p>
                                <p>specifically with the above issues, the provisions of the applicable codes will apply. In absence of
                                    applicable codes, the decision of the Engineer with regard to the quality and adequacy of workmanship shall
                                    be final and binding.</p>
                                <h3 id='inspection'>Inspection</h3>
                                <p>All materials, equipment, and workmanship shall be subject to inspection, examination and testing at all
                                    times and stages during construction, manufacture and/or installation, by the Engineer and they shall have
                                    the right to reject and order the removal and replacement of any defective material, equipment and / or
                                    workmanship or require its correction and rectification. The Contractor shall not proceed with any operation
                                    or sequence or trade of the Work until the previous operation or sequence or trade has been inspected and
                                    approved by the Employer’s Representative. No embedded items or any other work shall be covered up unless
                                    these have been inspected and approved by the Employer’s Representative. The onus shall be on the Contractor
                                    to get such inspections carried out and obtain such approvals. Should the Contractor fail to comply with
                                    these requirements, then all additional or redoing of work necessitated as a consequence thereof shall be at
                                    the Contractor&#x27;s cost and expense. No inspection or approval shall relieve the Contractor of any of its
                                    responsibilities, obligations and liabilities under the Contract. No defective workmanship shall be repaired
                                    or patched up in any way without inspection and direction of the Employer’s Representative.</p>
                                <p>Rejected workmanship shall be immediately corrected and rectified and rejected materials and equipment shall
                                    be removed and replaced with proper, specified and required materials and equipment, by the Contractor to
                                    the approval and satisfaction of the Employer’s Representative. The cost of all such correction and
                                    rectification and such removal and replacement shall be to the account of the Contractor and shall be borne
                                    by him, and also, the Contractor shall be responsible for all delays in this regard. The Contractor shall
                                    promptly segregate and remove the rejected materials and equipment from the Site and shall not reuse them in
                                    the Work. If the Contractor fails to proceed at once with the correction and rectification of rejected
                                    workmanship and/or the removal and replacement of rejected materials and equipment, the Employer shall have
                                    the right to employ other persons / agencies to correct and rectify such workmanship and/or remove and
                                    replace such materials and equipment, and recover the cost thereof from the Contractor, or the Employer may
                                    terminate the right of the Contractor to proceed further with the Work. If a result of examination,
                                    inspection, measurement or testing, material or workmanship is found to be defective or otherwise not
                                    accordance with the Contract, the Engineer may reject the material or workmanship by giving instruction /
                                    Notice to the contractor with reason &amp; with time specified for rectification / for making good the
                                    defect.</p>
                                <p>If the Contractor fails to comply with the instruction with in specified time, the Engineer shall be entitled
                                    to employ and pay other person to carry out work at expenses of Contractor.</p>
                                <p>The Contractor shall furnish promptly and without any charge, all facilities, access, labour, materials,
                                    plant and tools required and necessary for enabling the Engineer to carry out inspections and tests in a
                                    safe and convenient manner. The Contractor shall ascertain and ensure that the facilities and access
                                    provided for the carrying out of all inspections are completely safe in every respect and the Contractor
                                    shall be fully responsible and liable for all matters in connection with such safety.</p>
                                <h3 id='testing'>Testing</h3>
                                <p>All the tests on materials, equipment, and workmanship that shall be necessary in connection with the
                                    execution of the Work, as decided by the Engineer and as called for in the Contract Documents, shall be
                                    carried out at the cost of the Contractor at the place of work or of manufacture or fabrication or at the
                                    Site at a laboratory set up by the contractors. The Contractor shall provide all transportation, assistance,
                                    instruments, machines, labour and</p>
                                <p>materials as are required for the examining, measuring and testing as described above, and all expenses
                                    connected with the tests done at site shall be borne by the Contractor.</p>
                                <p>The contractor site laboratory should have at least the following instruments:</p>
                                <p>Screw gauge, slide callipers, etc.</p>
                                <p>Steel Tapes (30m., 15m., 5m., 3m.)</p>
                                <p>Digital thermometer with external sensing probe</p>
                                <p>Temperature Humidity Meter</p>
                                <p>Electronic weights – 30 &amp; 5 kg</p>
                                <p>Pressure gauge</p>
                                <p>Any other equipment.</p>
                                <p>However, should the Engineer wish to get any additional external third-party testing done, the cost of the
                                    same would be borne by the Employer.</p>
                                <h3 id='certificates'>Certificates</h3>
                                <p>The Contractor shall furnish, at its own cost, test certificates for the various materials and equipment as
                                    called for. Such test certificates shall be from the manufacturer for the particular consignment/lot/piece
                                    and shall be duly authenticated by respective consultants. The details in respect of the test certificates
                                    shall be as decided by the Engineer (in consultation with consultants) for the relevant items. No payment
                                    will be made in the absence of required test certificates.</p>
                                <h3 id='covering-up'>Covering Up</h3>
                                <p>The Contractor shall give at least 24 hours clear notice in writing to the Engineer before covering up any of
                                    the Work in foundations or any other such areas in order that inspection of the Work may be carried out for
                                    maintaining proper quality control. In the event of the Contractor failing to provide such notice he shall,
                                    at its own expense, uncover such Work as required to allow the inspection to be taken and thereafter shall
                                    reinstate the Work to the satisfaction of the Employer’s Representative.</p>
                                <h3 id='tolerances'>Tolerances</h3>
                                <p>In case work does not conform to the dimensions and limits of tolerances specified in the Contract Documents
                                    and/or the applicable standards, the Contractor shall be liable for all costs and expenses incurred for
                                    rectifications and/or replacements of other contractors&#x27; work required, in accordance with the
                                    directions of the Employer’s Representative, for the proper installation of the finishing elements and/or
                                    equipment, and/or for structural purposes. The Engineer decision in this respect shall be final and binding
                                    on the Contractor, and all such costs and expenses shall be recovered from the relevant contractor(s) and
                                    shall be deducted by the Employer from any money that may be payable or that may become payable under the
                                    Contract to such contractor(s) for and on behalf of the Contractor.</p>
                                <h3 id='utilities-and-substructures'>Utilities and Substructures</h3>
                                <p>The indication of the type and approximate location of existing utilities and sub-structures in the Contract
                                    Documents represents a diligent search of known records, but the accuracy and completeness of such
                                    indications are not warranted by the Engineer and utility structures and services not so indicated may
                                    exist. Before commencing any excavations, the Contractor shall investigate and determine the actual
                                    locations and protect the indicated utilities and structures. He shall determine existence, position and
                                    ownership of other utilities and substructures in the site or before the work is to be performed by
                                    communications with such owners, search of records or otherwise, and shall protect all such utilities and
                                    substructures.</p>
                                <p>In case the temporary interruption of utility services is necessary for the execution of the Work, the
                                    Contractor shall make all arrangements with the utility providers and pay all fees and charges levied by
                                    them for the interruptions and shall notify the affected users at least twenty-four (24) hours in advance of
                                    the probable duration of interruption unless such notice is given by the appropriate utility provider.</p>
                                <h3 id='restoration-and-repair'>Restoration and Repair</h3>
                                <p>Except for those improvements and facilities required to be permanently removed by the contract documents,
                                    the Contractor shall make satisfactory and acceptable arrangements with the appropriate owners and at his
                                    expense, replace and restore all structures, roads, property, utilities and facilities disturbed,
                                    disconnected or damaged as a result or consequence of his work or the operations of those for whom he is
                                    responsible or liable, including that caused by trespassing any of them with or without his knowledge or
                                    consent or by transporting of workmen, materials or equipment to or from the site. No claim to the Engineer
                                    either for time or cost shall be entertained.</p>
                                <h3 id='night-work'>Night Work</h3>
                                <p>In case the Contractor wishes to continue the Work at night, he may request, in writing, the Engineer to
                                    allow such work. The Engineer may grant such permission as soon as reasonable possible after assessing all
                                    the safety, security, environmental and site conditions. The Engineer decision in this regard shall be final
                                    and binding on the Contractor. In case the Engineer grants permission to continue the work at night, the
                                    Contractor shall not be entitled to claim any escalation in rates, prices or loss of profit or increase in
                                    overheads. It is understood that the Contractor shall be always responsible for safety and security of the
                                    Work and the workmen. In case the permission to Work at night is refused by the Employer’s Representative,
                                    the Contractor shall not cite the same as a reason for delay.</p>
                                <p>No women shall be deployed at work during night after 7 pm. It will be the Contractor’s responsibility to
                                    ensure that there is no in-convenience caused to the neighbours and no violation of law and order in
                                    general.</p>
                                <p>The Contractor may work without permission in writing From the Employer’s Representative, when the work is
                                    unavoidable or absolutely necessary for the safety of Works, in which case the Contractor shall immediately
                                    advise the Employer’s Representative.</p>
                                <h3 id='maintenance-during-construction'>Maintenance during Construction</h3>
                                <p>All Works shall remain in the care of the Contractor until handed over to the Employer, and the Contractor
                                    shall be responsible to make good at his own cost all losses and damages caused.</p>
                                <h3 id='overloading'>Overloading</h3>
                                <p>No part of the Work or new and existing structures, scaffolding, shoring, sheeting, construction machinery
                                    and equipment, or other permanent and temporary facilities shall be loaded in any manner or subjected to
                                    stresses or pressures that could endanger any of them. The Contractor shall bear the cost of correcting
                                    damage caused by loading or abnormal stresses or pressures.</p>
                                <h3 id='use-of-explosives'>Use of Explosives</h3>
                                <p>The Contractor shall comply with all laws, ordinances, regulations, codes, and orders governing the
                                    transportation, storage and use of explosives. The Contractor shall exercise extreme care not to endanger
                                    life or property and shall be responsible for all injury or damage resulting from the use of explosives for
                                    or on the Work. No blasting shall be done in the vicinity of existing structures above or below the ground
                                    without the prior written consent of the Employer’s Representative. However, consequences of any injury
                                    either to the property or person will be</p>
                                <p>the responsibility of the Contractor and the Contractor shall take all required Licenses and insurance
                                    required to carry out such work or as instructed by the Engineer / statutory authorities at his own cost.
                                </p>
                                <h2 id='bureau-of-indian-standards'>BUREAU OF INDIAN STANDARDS</h2>
                                <p>A reference made to any Indian Standards Specifications / CPWD Specifications in the Contract Documents shall
                                    imply reference to the latest version of that Standard, including such revisions/amendments as may be
                                    issued, during the currency of the Contract, by the Bureau of Indian Standards and the corresponding
                                    clause/s therein shall hold valid in place of those referred to. The Contractor shall keep copies at the
                                    Site of all latest publications of relevant Indian Standard Specifications applicable to the Work at the
                                    Site, as listed in the Specifications.</p>
                                <h2 id='meetings-and-reporting'>MEETINGS AND REPORTING</h2>
                                <h3 id='progress-meetings'>Progress Meetings</h3>
                                <p>The Contractor will be required to attend Weekly Progress Meetings with the Engineer to report the progress
                                    of the design, procurement and construction works. The Contractor will be required to submit a detailed
                                    progress report supported by an updated short-term programme 24 hours prior to the meeting. The report shall
                                    describe:</p>
                                <p>Design information required</p>
                                <p>Procurement Status</p>
                                <p>Approval of Submittals.</p>
                                <p>Progress of Works.</p>
                                <p>Co-ordination Issues</p>
                                <p>Programme Review</p>
                                <p>Mitigation of Delays.</p>
                                <p>Variations</p>
                                <p>Payments</p>
                                <p>The Engineer may call additional meetings to discuss the following issues. The Contractor shall attend the
                                    meetings and provide necessary information.</p>
                                <p>Design Development Meetings.</p>
                                <p>Quality Control Meetings.</p>
                                <p>Statutory / Utility Authority Meetings.</p>
                                <p>Cost / Commercial Meetings.</p>
                                <p>Health and Safety Meetings</p>
                                <p>Project Co-ordination Meetings</p>
                                <p>Any other meeting called by the Employer’s Representative.</p>
                                <h3 id='contractor-s-daily-reports'>Contractor’s Daily Reports</h3>
                                <p>The Contractor shall submit Daily Reports to the Engineer by 7:00pm each working day. These reports shall
                                    contain details of the following:</p>
                                <p>Record of the Site progress</p>
                                <p>Number of employees on the Site (state subcontractors’ separately)</p>
                                <p>Number of men employed on individual trades</p>
                                <p>Plant and machinery at site (including an indication as to whether the plant is working or standing)</p>
                                <p>Notification of accidents</p>
                                <p>Events influencing the progress of the Work</p>
                                <p>Record of Free Issue Material, if any</p>
                                <h3 id='contractor-s-monthly-reports'>Contractor’s Monthly Reports</h3>
                                <p>The Contractor shall issue a detailed monthly report to the Engineer containing the following details in
                                    regard to the Contract:</p>
                                <p>Contract commencement date.</p>
                                <p>Contract completion date.</p>
                                <p>Forecast completion date.</p>
                                <p>Reasons for any delay and actions taken to mitigate the delay.</p>
                                <p>Extension of time requested/awarded.</p>
                                <p>Summary of the progress of the Works.</p>
                                <p>Long lead procurement schedule.</p>
                                <p>Outstanding approvals.</p>
                                <p>Key information required in the next 4 weeks.</p>
                                <p>Outline Statement of Account</p>
                                <p>Schedule of Provisional Sums</p>
                                <p>Payments.</p>
                                <p>Appendix A - Current construction and commissioning programme.</p>
                                <p>Appendix B – Schedule of labour employed at Site.</p>
                                <p>Appendix C – Schedule of plant and equipment</p>
                                <p>Appendix D - Schedule of Subcontractors employed at site (start/finish dates)</p>
                                <p>Appendix E - Progress photographs in digital format</p>
                                <h2 id='project-close-out-deliverables'>PROJECT CLOSE-OUT DELIVERABLES</h2>
                                <p>The Contractor shall submit to the Engineer three sets of handing over documents containing the following on
                                    completion of the work before issuance of certificate of taking over by the Employer.</p>
                                <p>Detailed equipment data</p>
                                <p>As built drawings approved by consultant and architect. – hard copy &amp; soft copy (2 Set)</p>
                                <p>All original certificates of approval from statutory authorities.</p>
                                <p>Warrantee for all equipment</p>
                                <p>Manufacturers’ certificate for proper installation as per manufacturer’s installation</p>
                                <p>manual.</p>
                                <p>Reconciliation statement.</p>
                                <p>Contact list</p>
                                <p>Handing over/ taking over certificate issued by Employer</p>
                                <p>The Contractor shall periodically submit as-built drawings as and when work in all respects is completed in a
                                    particular area. These drawings shall be submitted in the form of 3 sets of CDs and each containing complete
                                    set of drawings on approved scale indicating the work as - installed.</p>
                                <p>The Contractor shall arrange for training of the Employer’s operating staff for the correct operation of
                                    important equipment as directed by the Employer’s Representative. The Contractor shall also familiarize the
                                    operating staff during the erection period with the design, construction and all maintenance aspects of the
                                    equipment. The period of training shall not be less than two months or such time till the Employer’s
                                    personnel is comfortable in their operation. The service personnel shall also be trained for routine
                                    maintenance, overhauling, adjustments, testing, minor repairs, and replacements. Nothing extra shall be paid
                                    to the Contractor for training the Employer personnel.</p>
                                <h2 id='general-builder-s-work-for-other-contractors'>GENERAL BUILDER’S WORK FOR OTHER CONTRACTORS</h2>
                                <p>The Contractor shall plan and coordinate the whole of the work under the Contract including all services,
                                    penetrations and embedment, concrete profiles and structural members, to prevent physical conflicts and
                                    changes to the installed work, with other Contractors.</p>
                                <p>Where the Contractor omits or wrongly locates any services penetrations or embedment, the</p>
                                <p>Contractor shall carry out all required remedial or ‘out of sequence’ work.</p>
                                <p>The Contractor shall determine and ascertain from the Vendors and persons engaged on separate contracts, in
                                    connection with the Project, the extent of all chasings, cutting and forming of all openings, holes, details
                                    of all inserts, sleeves, etc. that are required to accommodate the various services.</p>
                                <p>The Contractor shall determine and ascertain the routes of all services and positions of all floor and wall
                                    openings, outlets, traps, the details of all inserts, equipment and services and shall carry out the
                                    construction and making good of all &quot;builder&#x27;s work&quot; in accordance with and as shown,
                                    described and/or measured in the drawings, Specifications, Builders work Schedule and other Contract
                                    Documents. Also, the Contractor shall ensure that all required services, inserts, sleeves, embedment etc.
                                    are in place/position before he proceeds with its work. Should the Contractor fail to comply with these
                                    requirements and the consequence of such failure necessitates the breaking, re-doing and making good of any
                                    work, then the cost of all such breaking, re-doing and making good of any work shall be to the account of
                                    the Contractor and shall be borne by him. No breaking and cutting of completed work shall be done unless
                                    specifically authorised in writing by the Employer’s Representative. No work shall be done over broken or
                                    patched work without first ascertaining that the broken surface is adequately prepared and reinforced to
                                    receive and hold further work, as determined by the Employer’s Representative.</p>
                                <h2 id='site-attendance-for-other-contractors'>SITE ATTENDANCE FOR OTHER CONTRACTORS</h2>
                                <h3 id='general'>General</h3>
                                <p>Unless specified otherwise, the other trade contractors’ scope of works include the whole of the works
                                    pertaining to their trade.</p>
                                <p>The contractor is responsible to provide the All Site attendance to all contractors working at site as
                                    Specified in the Site Attendance Matrix.</p>
                                <h3 id='safety-attendance'>Safety Attendance</h3>
                                <p>The Contractor’s safety in-charge shall liaise with the Engineer for all safety related aspects / inspections
                                    for and on behalf of the Contractor.</p>
                                <p>The Contractor shall provide the following facilities for the use of their Sub- contractors</p>
                                <p>All barricading apparatus</p>
                                <p>First Aid</p>
                                <p>Firefighting equipment</p>
                                <h3 id='general-attendance'>General Attendance</h3>
                                <p>The contractor is responsible to provide the General Site attendance to all contractors working at site as
                                    specified in the Site Attendance Matrix. Examples of general attendance are:</p>
                                <p>Site access roads and other site infrastructure</p>
                                <p>Construction Power</p>
                                <p>Construction Water</p>
                                <p>Common Area Housekeeping</p>
                                <p>Passenger/ builders hoist</p>
                                <p>Scaffolding and staging</p>
                                <p>Drinking water</p>
                                <p>Labour camp facilities</p>
                                <p>Workers toilets</p>
                                <h3 id='miscellaneous-provision'>Miscellaneous Provision</h3>
                                <h3 id='oral-agreements'>Oral Agreements</h3>
                                <p>The oral order, objection, claim or notice by any party to the others shall not affect or modify any of the
                                    terms or obligations contained in any of the Contract Documents and none of the provisions of the contract
                                    documents shall be held to be waived or modified by reason of any act whatsoever, other than by a definitely
                                    agreed waiver of modifications thereof in writing and no other evidence shall be introduced in any
                                    proceeding of any other waiver or modification.</p>
                                <h3 id='site-order-books'>Site Order Books</h3>
                                <p>The Contractor shall keep site order book at site to receive instruction from Employer.</p>
                                <h3 id='problem-solving'>Problem Solving</h3>
                                <p>The Contractor is responsible for sorting out problems, difficulties, troubles and bottlenecks on &amp;
                                    around the Work sites and to take appropriate remedial steps immediately to completely ensure that all Works
                                    are carried out properly, smoothly and within the stipulated time limit, without any sort of financial
                                    liability and or any responsibility on the Employer.</p>
                                <h3 id='encumbrances'>Encumbrances</h3>
                                <p>The Contractor represents that the Work shall be performed, finished and delivered to the Employer free from
                                    all encumbrances including but not limited to claims, liens and charges etc.</p>
                                <h3 id='further-assurance'>Further Assurance</h3>
                                <p>From time to time, as and when requested by either Party hereto, the other Party shall execute and deliver,
                                    or cause to be executed and delivered, all such documents and instruments and shall take, or cause to be
                                    taken, all such further or other actions, as such other Party may reasonably deem necessary or desirable to
                                    consummate the transactions contemplated under the Contract Documents and take such other actions as may be
                                    reasonably requested from time to time in order to carry out, evidence and confirm their rights and the
                                    intended purpose of the Contract Documents within agreed timeline by both parties.</p>
                                <h3 id='no-third-party-beneficiaries'>No Third-Party Beneficiaries</h3>
                                <p>The Contract Documents is for the sole benefit of the Parties hereto and their permitted assigns and nothing
                                    herein expressed or implied shall give or be construed to give to any Person, other than the Parties hereto
                                    and such assigns, any legal or equitable rights, remedy or claim under or by reason of the Contract
                                    Documents or any part hereof.</p>
                                <h3 id='public-announcements'>Public Announcements</h3>
                                <p>The Contractor shall not and shall procure that it will not permit any of its managers’, members’, partners’,
                                    shareholders’, directors’, officers’, employees’ or other agents’ or representatives’ or ‘sub-contractors’,
                                    to issue any information, document or article for publication in any news or communications media or make
                                    any public statement in relation to the Project and / or Site and / or Contract Documents without the prior
                                    written consent of</p>
                                <p>the Employer. Without prejudice to the foregoing, any press release / advertisement / promotional material /
                                    statement by the Contractor (or any person on its behalf) in relation to the Project, the Site or any of the
                                    terms of the Contract Documents shall be coordinated by the Contractor with the Employer and shall be
                                    subject to the Employer’s prior written approval. Further, the Contractor shall not without the prior
                                    written consent of the Employer display its name or any other logo or any other symbol belonging to him on
                                    any part of the Site / Works / Yard.</p>
                                <h3 id='validity-of-commercial-instrument'>Validity of commercial instrument</h3>
                                <p>Any Bank Guarantee shall be considered submitted/valid only after the following</p>
                                <p>The Bank Guarantee submitted by the contractor in hard copy.</p>
                                <p>After receipt of the second copy of bank guarantee, by the Employer, directly from the issuing bank</p>
                                <p>The authenticity of the Bank guarantee verified by the Employers.</p>
                                <p>The Guarantee is issued from by a Nationalized or Scheduled bank only and not from any co-operative bank.</p>
                                <h2 id='site-attendance-matrix'>SITE ATTENDANCE MATRIX</h2>
                                <p>Supply (S), Install (I), Utilize (U), Maintain(M)</p>

                                <p></p>
                                <table>
                                    <tr>
                                        <th><br /><br /><br />S. NO</th>
                                        <th><br /><br /><br />DESCRIPTION</th>
                                        <th><br />EMPLOYER / PMC</th>
                                        <th>MAIN CIVIL CONTRACTOR</th>
                                        <th>ID CONTRACTOR</th>
                                        <th>ELECTRICAL</th>
                                        <th>PLUMBING &amp; FIRE FIGHTING</th>
                                        <th>EXTERNAL DEVELOPMENT</th>
                                        <th>DOORS AND WINDOWS</th>
                                        <th>LANDSCAPE - HARDSCAPE</th>
                                        <th>HORTICULTURE</th>
                                        <th>HVAC</th>
                                        <th>WATER PROOFING</th>
                                        <th>LIFTS</th>
                                        <th>OTHERS MISC WORKS</th>
                                        <th><br /><br /><br />REMARKS</th>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td><input type="text" class="form-control" name="desc1" value="Site Boundary wall" /></td>
                                        <td><input type="text" class="form-control" name="employer1" value="S/I / U" /></td>
                                        <td><input type="text" class="form-control" name="civil1" value="U/M" /></td>
                                        <td><input type="text" class="form-control" name="id1" value="U" /></td>
                                        <td><input type="text" class="form-control" name="electrical1" value="U" /></td>
                                        <td><input type="text" class="form-control" name="plumbing1" value="U" /></td>
                                        <td><input type="text" class="form-control" name="external1" value="U" /></td>
                                        <td><input type="text" class="form-control" name="doors1" value="U" /></td>
                                        <td><input type="text" class="form-control" name="landscape1" value="U" /></td>
                                        <td><input type="text" class="form-control" name="horticulture1" value="U" /></td>
                                        <td><input type="text" class="form-control" name="hvac1" value="U" /></td>
                                        <td><input type="text" class="form-control" name="waterproofing1" value="U" /></td>
                                        <td><input type="text" class="form-control" name="lifts1" value="U" /></td>
                                        <td><input type="text" class="form-control" name="others1" value="U" /></td>
                                        <td><input type="text" class="form-control" name="remarks1" value="" /></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td><input class="form-control" type="text" name="desc2" value="Gates, Boom Barriers, Fencing, Security posts, Wicked gates/Barricades and others arrangements at Entry / Exit Points to secure the site." /></td>
                                        <td><input type="text" class="form-control" name="employer2" value="U" /></td>
                                        <td><input type="text" class="form-control" name="civil2" value="I/U/M" /></td>
                                        <td><input type="text" class="form-control" name="id2" value="U" /></td>
                                        <td><input type="text" class="form-control" name="electrical2" value="U" /></td>
                                        <td><input type="text" class="form-control" name="plumbing2" value="U" /></td>
                                        <td><input type="text" class="form-control" name="external2" value="U" /></td>
                                        <td><input type="text" class="form-control" name="doors2" value="U" /></td>
                                        <td><input type="text" class="form-control" name="landscape2" value="U" /></td>
                                        <td><input type="text" class="form-control" name="horticulture2" value="U" /></td>
                                        <td><input type="text" class="form-control" name="hvac2" value="U" /></td>
                                        <td><input type="text" class="form-control" name="waterproofing2" value="U" /></td>
                                        <td><input type="text" class="form-control" name="lifts2" value="U" /></td>
                                        <td><input type="text" class="form-control" name="others2" value="U" /></td>
                                        <td><input type="text" class="form-control" name="remarks2" value="" /></td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td><input type="text" class="form-control" name="desc3" value="Site Access control &amp; Biometric" /></td>
                                        <td><input type="text" class="form-control" name="employer3" value="S/I/U" /></td>
                                        <td><input type="text" class="form-control" name="civil3" value="U/M" /></td>
                                        <td><input type="text" class="form-control" name="id3" value="U" /></td>
                                        <td><input type="text" class="form-control" name="electrical3" value="U" /></td>
                                        <td><input type="text" class="form-control" name="plumbing3" value="U" /></td>
                                        <td><input type="text" class="form-control" name="external3" value="U" /></td>
                                        <td><input type="text" class="form-control" name="doors3" value="U" /></td>
                                        <td><input type="text" class="form-control" name="landscape3" value="U" /></td>
                                        <td><input type="text" class="form-control" name="horticulture3" value="U" /></td>
                                        <td><input type="text" class="form-control" name="hvac3" value="U" /></td>
                                        <td><input type="text" class="form-control" name="waterproofing3" value="U" /></td>
                                        <td><input type="text" class="form-control" name="lifts3" value="U" /></td>
                                        <td><input type="text" class="form-control" name="others3" value="U" /></td>
                                        <td><input type="text" class="form-control" name="remarks3" value="" /></td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td><input type="text" class="form-control" name="desc4" value="Site Main Gate Security personnel" /></td>
                                        <td><input type="text" class="form-control" name="employer4" value="S/U" /></td>
                                        <td><input type="text" class="form-control" name="civil4" value="U" /></td>
                                        <td><input type="text" class="form-control" name="id4" value="U" /></td>
                                        <td><input type="text" class="form-control" name="electrical4" value="U" /></td>
                                        <td><input type="text" class="form-control" name="plumbing4" value="U" /></td>
                                        <td><input type="text" class="form-control" name="external4" value="U" /></td>
                                        <td><input type="text" class="form-control" name="doors4" value="U" /></td>
                                        <td><input type="text" class="form-control" name="landscape4" value="U" /></td>
                                        <td><input type="text" class="form-control" name="horticulture4" value="U" /></td>
                                        <td><input type="text" class="form-control" name="hvac4" value="U" /></td>
                                        <td><input type="text" class="form-control" name="waterproofing4" value="U" /></td>
                                        <td><input type="text" class="form-control" name="lifts4" value="U" /></td>
                                        <td><input type="text" class="form-control" name="others4" value="U" /></td>
                                        <td><input type="text" class="form-control" name="remarks4" value="" /></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </table>
                                <p></p>
                                <p></p>
                                <table>
                                    <tr>
                                        <th><br /><br /><br /><br />4</th>
                                        <th><br /><br />Security for contractors’ storage areas, infrastructure, Labour Camps and at respective works</th>
                                        <th></th>
                                        <th><br /><br /><br /><br />S/U/M</th>
                                        <th><br /><br /><br /><br />S/U<br />/M</th>
                                        <th><br /><br /><br />S/ U/ M</th>
                                        <th><br /><br /><br /><br />S/U/M</th>
                                        <th><br /><br /><br /><br />S/U/ M</th>
                                        <th><br /><br /><br /><br />S/U<br />/M</th>
                                        <th><br /><br /><br /><br />S/U<br />/M</th>
                                        <th><br /><br /><br /><br />S/U<br />/M</th>
                                        <th><br /><br /><br />S/ U/ M</th>
                                        <th><br /><br /><br /><br />S/U<br />/M</th>
                                        <th><br /><br /><br /><br />S/U<br />/M</th>
                                        <th><br /><br /><br /><br />S/U/ M</th>
                                        <th>For better coordination, All contractors will preferably hire the same Security Agencies as appointed by Employer and pay directly on actual<br />deployment.</th>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>
                                            <textarea rows="5" cols="90" name="desc5" class="form-control" style="width:100%; height:100px;">Security for Labour Camps</textarea>
                                        </td>
                                        <td></td>
                                        <td><input type="text" name="civil5" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="id5" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="electrical5" value="S/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="plumbing5" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="external5" value="S/U/ M" class="form-control" /></td>
                                        <td><input type="text" name="doors5" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="landscape5" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="horticulture5" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="hvac5" value="S/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing5" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="lifts5" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="others5" value="S/U/ M" class="form-control" /></td>
                                        <td>
                                            <textarea rows="5" cols="90" name="remarks5" class="form-control" style="width:100%; height:100px;"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>
                                            <textarea rows="5" cols="90" name="desc6" class="form-control" style="width:100%; height:100px;">Temporary site roads, Approach Roads, Culverts.</textarea>
                                        </td>
                                        <td></td>
                                        <td><input type="text" name="civil6" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="id6" value="U" class="form-control" /></td>
                                        <td><input type="text" name="electrical6" value="U" class="form-control" /></td>
                                        <td><input type="text" name="plumbing6" value="U" class="form-control" /></td>
                                        <td><input type="text" name="external6" value="U" class="form-control" /></td>
                                        <td><input type="text" name="doors6" value="U" class="form-control" /></td>
                                        <td><input type="text" name="landscape6" value="U" class="form-control" /></td>
                                        <td><input type="text" name="horticulture6" value="U" class="form-control" /></td>
                                        <td><input type="text" name="hvac6" value="U" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing6" value="U" class="form-control" /></td>
                                        <td><input type="text" name="lifts6" value="U" class="form-control" /></td>
                                        <td><input type="text" name="others6" value="U" class="form-control" /></td>
                                        <td>
                                            <textarea rows="5" cols="90" name="remarks6" class="form-control" style="width:100%; height:100px;"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>7</td>
                                        <td>
                                            <textarea rows="5" cols="90" name="desc7" class="form-control" style="width:100%; height:100px;">Power supply for construction, Common Lighting, common facilities including Back Up power</textarea>
                                        </td>
                                        <td></td>
                                        <td><input type="text" name="civil7" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="id7" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="electrical7" value="U" class="form-control" /></td>
                                        <td><input type="text" name="plumbing7" value="U" class="form-control" /></td>
                                        <td><input type="text" name="external7" value="U" class="form-control" /></td>
                                        <td><input type="text" name="doors7" value="U" class="form-control" /></td>
                                        <td><input type="text" name="landscape7" value="U" class="form-control" /></td>
                                        <td><input type="text" name="horticulture7" value="U" class="form-control" /></td>
                                        <td><input type="text" name="hvac7" value="U" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing7" value="U" class="form-control" /></td>
                                        <td><input type="text" name="lifts7" value="U" class="form-control" /></td>
                                        <td><input type="text" name="others7" value="U" class="form-control" /></td>
                                        <td>
                                            <textarea rows="5" cols="90" name="remarks7" class="form-control" style="width:100%; height:100px;">All other trade contractors will pay electricity charges on mutually agreed % age</textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>8</td>
                                        <td>
                                            <textarea rows="5" cols="90" name="desc8" class="form-control" style="width:100%; height:100px;">Debris Chute and Disposal of Debris</textarea>
                                        </td>
                                        <td></td>
                                        <td><input type="text" name="civil8" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="id8" value="U" class="form-control" /></td>
                                        <td><input type="text" name="electrical8" value="U" class="form-control" /></td>
                                        <td><input type="text" name="plumbing8" value="U" class="form-control" /></td>
                                        <td><input type="text" name="external8" value="U" class="form-control" /></td>
                                        <td><input type="text" name="doors8" value="U" class="form-control" /></td>
                                        <td><input type="text" name="landscape8" value="U" class="form-control" /></td>
                                        <td><input type="text" name="horticulture8" value="U" class="form-control" /></td>
                                        <td><input type="text" name="hvac8" value="U" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing8" value="U" class="form-control" /></td>
                                        <td><input type="text" name="lifts8" value="U" class="form-control" /></td>
                                        <td><input type="text" name="others8" value="U" class="form-control" /></td>
                                        <td><textarea rows="5" cols="90" name="remarks8" class="form-control" style="width:100%; height:100px;"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td>9</td>
                                        <td><textarea rows="5" cols="90" name="desc9" class="form-control" style="width:100%; height:100px;">Water supply, Treatment, Network for distribution for construction including testing of water</textarea></td>
                                        <td></td>
                                        <td><input type="text" name="civil9" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="id9" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="electrical9" value="U" class="form-control" /></td>
                                        <td><input type="text" name="plumbing9" value="U" class="form-control" /></td>
                                        <td><input type="text" name="external9" value="U" class="form-control" /></td>
                                        <td><input type="text" name="doors9" value="U" class="form-control" /></td>
                                        <td><input type="text" name="landscape9" value="U" class="form-control" /></td>
                                        <td><input type="text" name="horticulture9" value="U" class="form-control" /></td>
                                        <td><input type="text" name="hvac9" value="U" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing9" value="U" class="form-control" /></td>
                                        <td><input type="text" name="lifts9" value="U" class="form-control" /></td>
                                        <td><input type="text" name="others9" value="U" class="form-control" /></td>
                                        <td><textarea rows="5" cols="90" name="remarks9" class="form-control" style="width:100%; height:100px;">All other trade contractors will pay water charges on mutually agreed %age.</textarea></td>
                                    </tr>
                                    <tr>
                                        <td>10</td>
                                        <td><textarea rows="5" cols="90" name="desc10" class="form-control" style="width:100%; height:100px;">Drinking water, RO Treatment, Network for distribution for Drinking water, including testing of water</textarea></td>
                                        <td></td>
                                        <td><input type="text" name="civil10" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="id10" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="electrical10" value="S/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="plumbing10" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="external10" value="S/U/ M" class="form-control" /></td>
                                        <td><input type="text" name="doors10" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="landscape10" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="horticulture10" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="hvac10" value="S/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing10" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="lifts10" value="S/U/M" class="form-control" /></td>
                                        <td><input type="text" name="others10" value="S/U/ M" class="form-control" /></td>
                                        <td><textarea rows="5" cols="90" name="remarks10" class="form-control" style="width:100%; height:100px;">All other trade contractors will pay water charges on mutually agreed %age.</textarea></td>
                                    </tr>
                                </table>
                                <p></p>
                                <p></p>
                                <table>
                                    <tr>
                                        <th><br /><br />11</th>
                                        <th>handrails, Staircase Temp Rails, inside &amp; outside, Staging, closing of openings, cut outs, Shafts,<br />Safety nets at 2 levels</th>
                                        <th></th>
                                        <th><br /><br />S/I/U/M</th>
                                        <th><br /><br />U</th>
                                        <th><br /><br />U</th>
                                        <th><br /><br />U</th>
                                        <th><br /><br />U</th>
                                        <th><br /><br />U</th>
                                        <th><br /><br />U</th>
                                        <th><br /><br />U</th>
                                        <th><br /><br />U</th>
                                        <th><br /><br />U</th>
                                        <th><br /><br />U</th>
                                        <th><br /><br />U</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <td>12</td>
                                        <td><textarea name="desc12" class="form-control">Canteen- Staff and Labour</textarea></td>
                                        <td></td>
                                        <td><input type="text" name="civil12" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="id12" value="U" class="form-control" /></td>
                                        <td><input type="text" name="electrical12" value="U" class="form-control" /></td>
                                        <td><input type="text" name="plumbing12" value="U" class="form-control" /></td>
                                        <td><input type="text" name="external12" value="U" class="form-control" /></td>
                                        <td><input type="text" name="doors12" value="U" class="form-control" /></td>
                                        <td><input type="text" name="landscape12" value="U" class="form-control" /></td>
                                        <td><input type="text" name="horticulture12" value="U" class="form-control" /></td>
                                        <td><input type="text" name="hvac12" value="U" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing12" value="U" class="form-control" /></td>
                                        <td><input type="text" name="lifts12" value="U" class="form-control" /></td>
                                        <td><input type="text" name="others12" value="U" class="form-control" /></td>
                                        <td><input type="text" name="remarks12" value="" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <td>13</td>
                                        <td><textarea name="desc13" class="form-control">Toilets Facilities- Mobile Toilets</textarea></td>
                                        <td></td>
                                        <td><input type="text" name="civil13" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="id13" value="U" class="form-control" /></td>
                                        <td><input type="text" name="electrical13" value="U" class="form-control" /></td>
                                        <td><input type="text" name="plumbing13" value="U" class="form-control" /></td>
                                        <td><input type="text" name="external13" value="U" class="form-control" /></td>
                                        <td><input type="text" name="doors13" value="U" class="form-control" /></td>
                                        <td><input type="text" name="landscape13" value="U" class="form-control" /></td>
                                        <td><input type="text" name="horticulture13" value="U" class="form-control" /></td>
                                        <td><input type="text" name="hvac13" value="U" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing13" value="U" class="form-control" /></td>
                                        <td><input type="text" name="lifts13" value="U" class="form-control" /></td>
                                        <td><input type="text" name="others13" value="U" class="form-control" /></td>
                                        <td><input type="text" name="remarks13" value="" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <td>14</td>
                                        <td><textarea name="desc14" class="form-control">Site Office, Store Room, stacking yards</textarea></td>
                                        <td></td>
                                        <td><input type="text" name="civil14" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="id14" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="electrical14" value="S/I/U/ M" class="form-control" /></td>
                                        <td><input type="text" name="plumbing14" value="S/I/U/ M" class="form-control" /></td>
                                        <td><input type="text" name="external14" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="doors14" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="landscape14" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="horticulture14" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="hvac14" value="S/ I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing14" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="lifts14" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="others14" value="S/I/ U/M" class="form-control" /></td>
                                        <td><input type="text" name="remarks14" value="" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <td>15</td>
                                        <td><textarea name="desc15" class="form-control">OHC, First aid room &amp; Medical services</textarea></td>
                                        <td></td>
                                        <td><input type="text" name="civil15" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="id15" value="U" class="form-control" /></td>
                                        <td><input type="text" name="electrical15" value="U" class="form-control" /></td>
                                        <td><input type="text" name="plumbing15" value="U" class="form-control" /></td>
                                        <td><input type="text" name="external15" value="U" class="form-control" /></td>
                                        <td><input type="text" name="doors15" value="U" class="form-control" /></td>
                                        <td><input type="text" name="landscape15" value="U" class="form-control" /></td>
                                        <td><input type="text" name="horticulture15" value="U" class="form-control" /></td>
                                        <td><input type="text" name="hvac15" value="U" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing15" value="U" class="form-control" /></td>
                                        <td><input type="text" name="lifts15" value="U" class="form-control" /></td>
                                        <td><input type="text" name="others15" value="U" class="form-control" /></td>
                                        <td><input type="text" name="remarks15" value="" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <td>16</td>
                                        <td><textarea name="desc16" class="form-control">Ambulance</textarea></td>
                                        <td></td>
                                        <td><input type="text" name="civil16" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="id16" value="U" class="form-control" /></td>
                                        <td><input type="text" name="electrical16" value="U" class="form-control" /></td>
                                        <td><input type="text" name="plumbing16" value="U" class="form-control" /></td>
                                        <td><input type="text" name="external16" value="U" class="form-control" /></td>
                                        <td><input type="text" name="doors16" value="U" class="form-control" /></td>
                                        <td><input type="text" name="landscape16" value="U" class="form-control" /></td>
                                        <td><input type="text" name="horticulture16" value="U" class="form-control" /></td>
                                        <td><input type="text" name="hvac16" value="U" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing16" value="U" class="form-control" /></td>
                                        <td><input type="text" name="lifts16" value="U" class="form-control" /></td>
                                        <td><input type="text" name="others16" value="U" class="form-control" /></td>
                                        <td><input type="text" name="remarks16" value="" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <td>17</td>
                                        <td><textarea name="desc17" class="form-control">Induction Room Safety and Quality</textarea></td>
                                        <td></td>
                                        <td><input type="text" name="civil17" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="id17" value="U" class="form-control" /></td>
                                        <td><input type="text" name="electrical17" value="U" class="form-control" /></td>
                                        <td><input type="text" name="plumbing17" value="U" class="form-control" /></td>
                                        <td><input type="text" name="external17" value="U" class="form-control" /></td>
                                        <td><input type="text" name="doors17" value="U" class="form-control" /></td>
                                        <td><input type="text" name="landscape17" value="U" class="form-control" /></td>
                                        <td><input type="text" name="horticulture17" value="U" class="form-control" /></td>
                                        <td><input type="text" name="hvac17" value="U" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing17" value="U" class="form-control" /></td>
                                        <td><input type="text" name="lifts17" value="U" class="form-control" /></td>
                                        <td><input type="text" name="others17" value="U" class="form-control" /></td>
                                        <td><input type="text" name="remarks17" value="" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <td>18</td>
                                        <td><textarea name="desc18" class="form-control">Routine housekeeping for General and Common areas inside and outside Buildings</textarea></td>
                                        <td></td>
                                        <td><input type="text" name="civil18" value="U/M" class="form-control" /></td>
                                        <td><input type="text" name="id18" value="U" class="form-control" /></td>
                                        <td><input type="text" name="electrical18" value="U" class="form-control" /></td>
                                        <td><input type="text" name="plumbing18" value="U" class="form-control" /></td>
                                        <td><input type="text" name="external18" value="U" class="form-control" /></td>
                                        <td><input type="text" name="doors18" value="U" class="form-control" /></td>
                                        <td><input type="text" name="landscape18" value="U" class="form-control" /></td>
                                        <td><input type="text" name="horticulture18" value="U" class="form-control" /></td>
                                        <td><input type="text" name="hvac18" value="U" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing18" value="U" class="form-control" /></td>
                                        <td><input type="text" name="lifts18" value="U" class="form-control" /></td>
                                        <td><input type="text" name="others18" value="U" class="form-control" /></td>
                                        <td><input type="text" name="remarks18" value="" class="form-control" /></td>
                                    </tr>
                                    <tr>
                                        <td>19</td>
                                        <td><textarea name="desc19" class="form-control">Protection and Final Cleaning of Works</textarea></td>
                                        <td></td>
                                        <td><input type="text" name="civil19" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="id19" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="electrical19" value="S/I/U/ M" class="form-control" /></td>
                                        <td><input type="text" name="plumbing19" value="S/I/U/ M" class="form-control" /></td>
                                        <td><input type="text" name="external19" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="doors19" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="landscape19" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="horticulture19" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="hvac19" value="S/ I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing19" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="lifts19" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="others19" value="S/I/ U/M" class="form-control" /></td>
                                        <td><input type="text" name="remarks19" value="" class="form-control" /></td>
                                    </tr>
                                </table>
                                <p></p>
                                <p></p>
                                <table>
                                    <tr>
                                        <th><br /><br />20</th>
                                        <th><br />Fire Fighting equipment &amp; Fire Extinguishers, Fire Alarms, Emergency Siren, Signages for common areas</th>
                                        <th></th>
                                        <th><br /><br />S/I/U/M</th>
                                        <th>S/I/ U/ M</th>
                                        <th>S/I<br />/U<br />/ M</th>
                                        <th>S/I/U/ M</th>
                                        <th>S/I/U<br />/M</th>
                                        <th>S/I/ U/ M</th>
                                        <th>S/I/ U/ M</th>
                                        <th>S/I/ U/ M</th>
                                        <th>S/ I/ U/ M</th>
                                        <th>S/I/ U/ M</th>
                                        <th>S/I/ U/ M</th>
                                        <th>S/I/ U/M</th>
                                        <th>Fire Safety Norms shall be followed by all Contractors for their respective works.</th>
                                    </tr>
                                    
                                    <tr>
                                        <td>21</td>
                                        <td><textarea name="desc21" class="form-control">Labour accommodation and facilities including Power, Water supply Sanitation, Power back up including maintenance</textarea></td>
                                        <td></td>
                                        <td><input type="text" name="civil21" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="id21" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="electrical21" value="S/I/U/ M" class="form-control" /></td>
                                        <td><input type="text" name="plumbing21" value="S/I/U/ M" class="form-control" /></td>
                                        <td><input type="text" name="external21" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="doors21" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="landscape21" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="horticulture21" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="hvac21" value="S/ I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing21" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="lifts21" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="others21" value="S/I/ U/M" class="form-control" /></td>
                                        <td><textarea name="remarks21" class="form-control">If available, Labour camp facility to other contractors will be provided on chargeable basis.</textarea></td>
                                    </tr>
                                    <tr>
                                        <td>22</td>
                                        <td><textarea name="desc22" class="form-control">Crèche Facilities including. Power, Water supply, sanitation including maintenance</textarea></td>
                                        <td></td>
                                        <td><input type="text" name="civil22" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="id22" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="electrical22" value="S/I/U/ M" class="form-control" /></td>
                                        <td><input type="text" name="plumbing22" value="S/I/U/ M" class="form-control" /></td>
                                        <td><input type="text" name="external22" value="S/I/U/M" class="form-control" /></td>
                                        <td><input type="text" name="doors22" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="landscape22" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="horticulture22" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="hvac22" value="S/ I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="waterproofing22" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="lifts22" value="S/I/ U/ M" class="form-control" /></td>
                                        <td><input type="text" name="others22" value="S/I/ U/M" class="form-control" /></td>
                                        <td><textarea name="remarks22" class="form-control">If available, Labour camp facility to other contractors will be provided on chargeable basis.</textarea></td>
                                    </tr>
                                </table>
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