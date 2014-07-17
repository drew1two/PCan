{{ content() }}
<?php
require_once __DIR__ . '/../../../vendor/google/recaptcha-php/recaptchalib.php';
?>
<div align="center">

	{{ form('class': 'form-search') }}

		<div align="left">
			<h2>Sign Up</h2>
		</div>

		<table class="signup">
                    <thead>
                        <tr>
                            <th></th><th style='width:350px'></th>                              
                        </tr>

                    </thead>
			<tr>
				<td align="right">{{ form.label('name') }}</td>
				<td>
					{{ form.render('name') }}
					{{ form.messages('name') }}
				</td>
			</tr>
			<tr>
				<td align="right">{{ form.label('email') }}</td>
				<td>
					{{ form.render('email') }}
					{{ form.messages('email') }}
				</td>
			</tr>
			<tr>
				<td align="right">{{ form.label('password') }}</td>
				<td>
					{{ form.render('password') }}
					{{ form.messages('password') }}
				</td>
			</tr>
			<tr>
				<td align="right">{{ form.label('confirmPassword') }}</td>
				<td>
					{{ form.render('confirmPassword') }}
					{{ form.messages('confirmPassword') }}
				</td>
			</tr>
			<tr>
				<td align="right"></td>
				<td>
					{{ form.render('terms') }} {{ form.label('terms') }}
					{{ form.messages('terms') }}
				</td>
			</tr>
                        <?php
                            $config = Phalcon\DI::getDefault()->get('config');
                            if ($config->application->signupCaptcha)
                            {
                                echo "<tr><td align='right' colspan='2'>";
                                echo recaptcha_get_html($config->application->captchaPublic);
                                echo "</td></tr>";
                                if (!$config->application->loginCaptcha)
                                {
                                echo "<tr><td colspan='2'>Captcha helps discourage robots." 
                                . " <br/>This inconvenience is regretted. Login is Captcha-free."
                                . " <br/>Signup requires email confirmation</td></tr>";             
                                }
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