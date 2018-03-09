<?php

function test_dump($var, $die = false, $all = false)
{
	global $USER;
	if ( ($USER->GetID() == 1) || ($all == true)) {
		?>
		<font style="text-align: left; font-size: 12px;">
			<pre>
				<?var_dump($var)?>				
			</pre>
		</font>
		<br>

		<?php
	}

	if ($die) {
		die;
	}
}

// Для продакшена (для авторизованных):
function pra($var, $die = true)
{
	global $USER;
	if ($UESR->isAdmin) {?>

		<font style="text-align: left; font-size: 12px;">
			<pre>
				<?php print_r($var)?>				
			</pre>
		</font>
		<br>

	<?php
	}
	
	if ($die) {
		die;
	}
}

// Для продакшена (добавить в адресн.строку "dump=y"):
function prget($var, $die = true)
{
	global $USER;
	if ($_REQUEST['dump'] == 'y') {?>

		<font style="text-align: left; font-size: 12px;">
			<pre>
				<?php print_r($var)?>				
			</pre>
		</font>
		<br>

	<?php
	}
	
	if ($die) {
		die;
	}
}

// Для продакшена (не работает при динамических ip):
function prip($var, $die = true)
{
	global $USER;
	if ($_SERVER['REMOTE_ADDR'] == '128.71.94.133') {?>

		<font style="text-align: left; font-size: 12px;">
			<pre>
				<?php print_r($var)?>				
			</pre>
		</font>
		<br>

	<?php
	}

	if ($die) {
		die;
	}
}

function pr($var, $die = true)
{
	?>
	<font style="text-align: left; font-size: 12px;">
		<pre>
			<?php print_r($var)?>				
		</pre>
	</font>
	<br>

	<?php
	if ($die) {
		die;
	}
}

function vd($var, $die = true)
{
	?>
	<font style="text-align: left; font-size: 12px;">
		<pre>
			<?php var_dump($var)?>				
		</pre>
	</font>
	<br>

	<?php
	if ($die) {
		die;
	}
}