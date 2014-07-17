{{ content() }}
<?php
// https://www.google.com/recaptcha/admin#site/318137110
    require_once __DIR__ . '/../../../vendor/google/recaptcha-php/recaptchalib.php';
?>

<div align="center" class="well">
    {{ form('class': 'form-search') }}

    <div align="left">
        <h2>Log In</h2>
    </div>
    <table class="table table-striped">
        <tbody>
            <tr>
                <td><label for="email">Email</label></td>
                <td>{{ form.render('email') }}</td>    
            </tr>
            <tr>
                <td><label for="password">Password</label></td>
                <td>{{ form.render('password') }}</td>    
            </tr>
            <?php
            $config = Phalcon\DI::getDefault()->get('config');
            if ($config->application->loginCaptcha) {
                echo "<tr><td colspan='2'>";
                echo recaptcha_get_html($config->application->captchaPublic);
                echo "</tr></td>";
            }
            ?> 
        </tbody>
    </table>

    {{ form.render('go') }}

    <div align="center" class="remember">
        {{ form.render('remember') }}
        {{ form.label('remember') }}
    </div>

    {{ form.render('csrf', ['value': security.getToken()]) }}

    <hr>

    <div class="forgot">
        {{ link_to("session/forgotPassword", "Forgot my password") }}
        | {{ link_to("session/signup", "Signup") }}
    </div>

</form>

</div>