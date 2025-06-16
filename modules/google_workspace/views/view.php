<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if ($status == 'public') { ?>
                    <?php if ($type == 'doc') { ?>
                        <iframe src="https://docs.google.com/document/d/<?php echo $driveid; ?>/edit?usp=sharing" width="100%" height="800"></iframe>
                    <?php } else if ($type == 'sheet') { ?>
                        <iframe src="https://docs.google.com/spreadsheets/d/<?php echo $driveid; ?>/edit?usp=sharing" width="100%" height="800"></iframe>
                    <?php } else if ($type == 'slide') { ?>
                        <iframe src="https://docs.google.com/presentation/d/<?php echo $driveid; ?>/edit?usp=sharing" width="100%" height="800"></iframe>
                    <?php } else if ($type == 'form') { ?>
                        <iframe src="https://docs.google.com/forms/d/<?php echo $driveid; ?>/edit?usp=sharing" width="100%" height="800"></iframe>
                    <?php } else if ($type == 'drive') { ?>
                        <iframe src="https://drive.google.com/file/d/<?php echo $driveid; ?>/preview" width="100%" height="800"></iframe>
                    <?php } ?>
                <?php } else { ?>
                    <iframe src="https://drive.google.com/file/d/<?php echo $driveid; ?>/preview" width="100%" height="800"></iframe>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

</body>
</html>