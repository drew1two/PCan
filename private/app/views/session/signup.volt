<?php use \Pcan\Captcha\Recaptcha; ?>

{{ content() }}

<div align="center">
	{{ form('class': 'form-search') }}

		<table class="table table-condensed">
                    <thead>
                        <tr><th colspan='2' class='centerCell'><label>Sign Up</label></th></tr>

                    </thead>
			<tr>
				<td class='rightCell'>{{ form.label('name') }}</td>
				<td class='leftCell'>
					{{ form.render('name') }}
					{{ form.messages('name') }}
				</td>
			</tr>
			<tr>
				<td  class='rightCell'>{{ form.label('email') }}</td>
				<td class='leftCell'>
					{{ form.render('email') }}
					{{ form.messages('email') }}
				</td>
			</tr>
			<tr>
				<td  class='rightCell'>{{ form.label('password') }}</td>
				<td class='leftCell'>
					{{ form.render('password') }}
					{{ form.messages('password') }}
				</td>
			</tr>
			<tr>
				<td class='rightCell'>{{ form.label('confirmPassword') }}</td>
				<td class='leftCell'>
					{{ form.render('confirmPassword') }}
					{{ form.messages('confirmPassword') }}
				</td>
			</tr>
			
          <?php
             $config = Phalcon\DI::getDefault()->get('config');
             if ($config->application->signupCaptcha)
             {
                 echo Recaptcha::htmlCaptcha($config);      
             }
         ?>
			<tr>
				<td colspan='2' >{{ form.render('Sign Up') }}</td>
			</tr>
 		</table>

		{{ form.render('csrf', ['value': security.getToken()]) }}
		{{ form.messages('csrf') }}

		<hr>

	</form>

</div>