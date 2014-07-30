<form id='<?php echo $form_id ?>'  action='<?php echo $_SERVER['REQUEST_URI'] ?>' method='<?php echo $method ?>' >	
	<div>
		<p><?php echo $message ?></p>
		<input type='hidden' name='confirm' value='<?php echo $action ?>'>
		<div class='form-btn'>
			<input class='form-field' type='submit' name='<?php echo $form_id ?>-action-cancel' value='<?php echo $cancel_value ?>' />
		</div>
		<div class='submit form-btn'>
			<input class='form-field' type='submit' name='<?php echo $form_id ?>-action-confirm' value='<?php echo $confirm_value; ?>' />
		</div>
	</div>
</form>