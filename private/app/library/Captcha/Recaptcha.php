<?php

namespace Pcan\Captcha;

/*
 * 
 */
require_once __DIR__ . '/../../../vendor/google/recaptcha-php/recaptchalib.php';

class Recaptcha {

    /**
     * @param type $config Phalcon config object with captcha keys
     * @return string html for form inside table with 2 columns
     */
    static function htmlCaptcha($config) {
        $html = "<tr><td class='centerCell' colspan='2'>"
                . "</td></tr>"
                . "<tr><td class='centerCell'><label for='body'>Captcha</label></td><td class='leftCell'>"
                . recaptcha_get_html($config->application->captchaPublic)
                . "</td><td><p><span>Captcha required to discourage web-bots.<br/>Sorry for the inconvenience.</span></p></td>"
                . "</tr>";
        return $html;
    }
    /**
     * 
     * @param type $request Phalcon HTTP request object
     * @param type $config  Phalcon config object with captcha keys
     * @return type boolean
     */
    static function checkCaptcha($request, $config) {
        // do the google captcha
        $challenge = $request->getPost('recaptcha_challenge_field');
        $response = $request->getPost('recaptcha_response_field');
        $address = $request->getClientAddress();
        $resp = recaptcha_check_answer($config->application->captchaPrivate, $address, $challenge, $response);
        return $resp;
    }

}
