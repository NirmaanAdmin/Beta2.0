<?php 
$query = '';
if($creator_type == 'staff'){
	$query = 'parent_id = '.$parent_id.' AND filetype = "folder" AND ( (name != "Inbox" AND (creator_type = "staff" OR (creator_id = 0 AND creator_type = "public"))) OR (name = "Inbox" AND creator_type = "staff" AND creator_id = '.$user_id.')
  )';
}
else{
	$query = 'parent_id = '.$parent_id.' and ((creator_id IN (1, '.$user_id.') and creator_type = "customer") or (creator_id = 0 and creator_type = "public")) and filetype = "folder"';
}
 
$items = $this->drawing_management_model->get_item('', $query, 'name, id, filetype');
if(count($items)){ ?>
	<?php 
	foreach ($items as $key => $value) { ?>
		<ul class="<?php echo ((isset($main_tree) && $main_tree == 1) ? 'main-tree' : 'tree'); ?>">
			<li class="display-flex item-row" data-id="<?php echo drawing_htmldecode($value['id']); ?>">
				<div class="radio-control">
					<div class="form-check">
						<input class="form-check-input" type="radio" name="select_folder" id="flex-radio-<?php echo drawing_htmldecode($value['id']); ?>" value="<?php echo drawing_htmldecode($value['id']); ?>" <?php echo (($selected_folder == $value['id']) ? 'checked' : '')?> <?php echo (in_array($value['id'], $selected_item) ? 'disabled' : ''); ?>>
						<label class="form-check-label" for="flex-radio-<?php echo drawing_htmldecode($value['id']); ?>">
						</label>
					</div>
				</div>
				<div class="tree-title ptop10 text-nowrap<?php echo ((isset($main_tree) && $main_tree == 1) ? ' bold' : ''); ?>" data-id="<?php echo drawing_htmldecode($value['id']); ?>">
					<?php echo drawing_htmldecode($value['name']); ?>					
				</div>
			</li>
			<?php 
			echo $this->load->view('includes/modal_contents/item_list.php', ['main_tree' => 0, 'parent_id' => $value['id']], true); ?>
		</ul>
	<?php } ?>
<?php } ?>
