<link rel="stylesheet" href="/media/css/login.css">
<?php
if ($use_username) {
	$username = array(
		'name'	=> 'username',
		'id'	=> 'username',
		'value' => set_value('username'),
		'maxlength'	=> $this->config->item('username_max_length', 'tank_auth'),
		'size'	=> 30,
	);
}
$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'value'	=> set_value('email'),
	'maxlength'	=> 80,
	'size'	=> 30,
);
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'value' => set_value('password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$confirm_password = array(
	'name'	=> 'confirm_password',
	'id'	=> 'confirm_password',
	'value' => set_value('confirm_password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$captcha = array(
	'name'	=> 'captcha',
	'id'	=> 'captcha',
	'maxlength'	=> 8,
);


$firstname = array(
	'name'	=> 'firstname',
	'id'	=> 'firstname',
	'value' => set_value('firstname'),
	'size'	=> 30,
);
$lastname = array(
	'name'	=> 'lastname',
	'id'	=> 'lastname',
	'value' => set_value('lastname'),
	'size'	=> 30,
);
$shopname = array(
	'name'	=> 'shopname',
	'id'	=> 'shopname',
	'value' => set_value('shopname'),
	'size'	=> 30,
);
$shopdescription = array(
	'name'	=> 'shopdescription',
	'id'	=> 'shopdescription',
	'value' => set_value('shopdescription'),
	'size'	=> 30,
);
?>

<div align="center">
<fieldset>
<legend align="center">REGISTER</legend>


<?php echo form_open($this->uri->uri_string()); ?>
<table>
	<?php if ($use_username) { ?>
	<tr>
		<td><?php echo form_label('Username', $username['id']); ?></td>
		<td><?php echo form_input($username); ?></td>
		<td style="color: red;"><?php echo form_error($username['name']); ?><?php echo isset($errors[$username['name']])?$errors[$username['name']]:''; ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td><?php echo form_label('Email Address', $email['id']); ?></td>
		<td><?php echo form_input($email); ?></td>
		<td style="color: red;"><?php echo form_error($email['name']); ?><?php echo isset($errors[$email['name']])?$errors[$email['name']]:''; ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Password', $password['id']); ?></td>
		<td><?php echo form_password($password); ?></td>
		<td style="color: red;"><?php echo form_error($password['name']); ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Confirm Password', $confirm_password['id']); ?></td>
		<td><?php echo form_password($confirm_password); ?></td>
		<td style="color: red;"><?php echo form_error($confirm_password['name']); ?></td>
	</tr>

	<!-- profile information -->
	<tr>
		<td colspan="3"><h3>Profile information</h3></td>
	</tr>
	<tr>
		<td><?php echo form_label('Firstname', $firstname['id']); ?></td>
		<td><?php echo form_input($firstname); ?></td>
		<td style="color: red;"><?php echo form_error($firstname['name']); ?><?php echo isset($errors[$firstname['name']])?$errors[$firstname['name']]:''; ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Lastname', $lastname['id']); ?></td>
		<td><?php echo form_input($lastname); ?></td>
		<td style="color: red;"><?php echo form_error($lastname['name']); ?><?php echo isset($errors[$lastname['name']])?$errors[$lastname['name']]:''; ?></td>
	</tr>

	<!-- shop information -->
	<tr>
		<td colspan="3"><h3>Shop information</h3></td>
	</tr>
	<tr>
		<td><?php echo form_label('Shop name', $shopname['id']); ?></td>
		<td><?php echo form_input($shopname); ?></td>
		<td style="color: red;"><?php echo form_error($shopname['name']); ?><?php echo isset($errors[$shopname['name']])?$errors[$shopname['name']]:''; ?></td>
	</tr>
	<tr>
		<td><?php echo form_label('Shop description', $shopdescription['id']); ?></td>
		<td><?php echo form_input($shopdescription); ?></td>
		<td style="color: red;"><?php echo form_error($shopdescription['name']); ?><?php echo isset($errors[$shopdescription['name']])?$errors[$shopdescription['name']]:''; ?></td>
	</tr>

	<!-- shop information -->

	<?php if ($captcha_registration) {
		if ($use_recaptcha) { ?>
	<tr>
		<td colspan="2">
			<div id="recaptcha_image"></div>
		</td>
		<td>
			<a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a>
			<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')">Get an audio CAPTCHA</a></div>
			<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')">Get an image CAPTCHA</a></div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="recaptcha_only_if_image">Enter the words above</div>
			<div class="recaptcha_only_if_audio">Enter the numbers you hear</div>
		</td>
		<td><input type="text" id="recaptcha_response_field" name="recaptcha_response_field" /></td>
		<td style="color: red;"><?php echo form_error('recaptcha_response_field'); ?></td>
		<?php echo $recaptcha_html; ?>
	</tr>
	<?php } else { ?>
	<tr>
		<td colspan="3">
			<p>Enter the code exactly as it appears:</p>
			<?php echo $captcha_html; ?>
		</td>
	</tr>
	<tr>
		<td><?php echo form_label('Confirmation Code', $captcha['id']); ?></td>
		<td><?php echo form_input($captcha); ?></td>
		<td style="color: red;"><?php echo form_error($captcha['name']); ?></td>
	</tr>
	<?php }
	} ?>
</table>
<?php echo form_submit('register', 'Register','class=btn'); ?>
<?php echo form_reset('cancel', 'Later','class=btn'); ?>
</fieldset>
<?php echo form_close(); ?>
</div>