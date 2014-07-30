<?php if(!empty($error_message)): ?>
<div class='error'>
	<?php echo $error_message; ?>
</div>	
	
<?php endif; ?>

<form id='<?php echo $form_id ?>' method='<?php echo $method; ?>' action='<?php echo $action; ?>'>	
	<?php if (!empty($errors)): ?>
	<div class="error-message">
		<?php echo $error_message; ?>
	</div>
	<?php endif ?>
	
	<div class='fields-holder'>
		<?php echo $fields; ?>
	</div>
	
	<div class='form-buttons'>
		<div class='submit form-btn'>
			<input class='form-field' type='submit' name='<?php echo $form_id ?>-action-submit' value='<?php echo $submit_value; ?>' />
		</div>
		<?php if ($delete && !empty($record_data)): ?>
		<div class='submit-delete form-btn'>
			<input class='form-field' type='submit' name='<?php echo $form_id ?>-action-delete' value='<?php echo $delete_value; ?>' />
		</div>
		<?php endif ?>
	</div>
</form>
