<h1>Login</h1>

<?=$login_form?>
<?php if($allow_create_user) : ?>
 <p><a href='<?=create_url('user/create')?>' > Create new user</a></p>
<?php endif; ?> 
 
 

