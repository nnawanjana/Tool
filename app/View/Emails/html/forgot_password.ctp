<p>Hey <?php echo $user['User']['name']; ?>,</p>

<p>We've set a new password you can use to log-in to Deal Expert.</p>

<p>Your new password is: <strong><?php echo $password;?></strong>.</p>

<p>You can log-in at: http://<?php echo $_SERVER['HTTP_HOST'];?>/users/login?email=<?php echo $user['User']['email'];?></p>