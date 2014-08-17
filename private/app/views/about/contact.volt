<?php

use \Pcan\Captcha\Recaptcha; ?>
<div class="container-fluid">
    <div class='table-responsive'>
        <table class="table table-condensed table-bordered">
            <thead>
                <tr>
                    <th colspan='2' class="centerCell">Contact Parracan</th>
                </tr>
                <tr>
                    <th class="centerCell">Postal</th>
                    <th class="centerCell">Phone</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>15 Cliff Avenue,<br/> Winston Hills,<br/>NSW 2153 Australia</td>
                    <td style='padding:0;'>
                        <table class='table-condensed table-borderless'>
                            <tr class='noline'>
                                <td class="cellLeft">Annie Nielsen</td>
                                <td class="cellLeft">0425 265 169</td>
                            </tr>
                            <tr class='noline'>
                                <td class="cellLeft">Terry McBride</td>
                                <td class="cellLeft">0418 859 211</td>
                            </tr>
                            <tr class='noline'>
                                <td class="cellLeft">Phil Bradley</td>
                                <td class="cellLeft">0425 265 170</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>

        </table>
    </div>
    {{ content() }}
    <div class='container'>

        <form method='post'>
            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th colspan='2' class='centerCell'><span style='font-weight:bold'>Send Email Direct (or use <a href='mailto:secretary@parracan.org'>secretary@parracan.org</a>)</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($errors)) {
                        $msgarray = $errors->filter('name');
                        if (count($msgarray) > 0) {
                            foreach ($msgarray as $msg) {
                                echo '<tr><td colspan="2"><p class="text-danger">' . $msg . '</p></td></tr>';
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td class='rightCell'><label for='name'>Name</label></td>
                        <td class='leftCell'><?php echo $form->render('name'); ?></td>
                    </tr>
                    <tr>
                        <td class='rightCell'><label for='telephone'>Telephone</label></td>
                        <td class='leftCell'><?php echo $form->render('telephone'); ?></td>
                    </tr>
                    <?php
                    if (isset($errors)) {
                        $msgarray = $errors->filter('email');
                        if (count($msgarray) > 0) {
                            foreach ($msgarray as $msg) {
                                echo '<tr><td colspan="2"><p class="text-danger">' . $msg . '</p></td></tr>';
                            }
                        }
                    }
                    ?>           
                    <tr>
                        <td class='rightCell'><label for='email'>Sender's email</label></td>
                        <td class='leftCell'><?php echo $form->render('email'); ?></td>
                    </tr>
<?php
if (isset($errors)) {
    $msgarray = $errors->filter('body');
    if (count($msgarray) > 0) {
        foreach ($msgarray as $msg) {
            echo '<tr><td colspan="2"><p class="text-danger">' . $msg . '</p></td></tr>';
        }
    }
}
?>               
                    <tr>
                        <td class='rightCell'><label for='body'>Text</label></td>
                        <td class='leftCell'><?php echo $form->render('body'); ?></td>
                    </tr>
<?php
$config = Phalcon\DI::getDefault()->get('config');
if ($config->application->recaptcha) {
    echo Recaptcha::htmlCaptcha($config);
}
?>
                    <tr>
                        <td colspan='2' class='centerCell'><?php echo $this->tag->submitButton(array('Send as Email', 'class' => 'btn btn-default')) ?></td>
                    </tr>


                </tbody>
            </table>
        </form>
    </div>
