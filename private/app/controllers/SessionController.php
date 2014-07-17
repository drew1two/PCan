<?php

namespace Pcan\Controllers;

use Pcan\Forms\LoginForm;
use Pcan\Forms\SignUpForm;
use Pcan\Forms\ForgotPasswordForm;
use Pcan\Auth\Exception as AuthException;
use Pcan\Models\Users;
use Pcan\Models\ResetPasswords;


require_once __DIR__ . '/../../vendor/google/recaptcha-php/recaptchalib.php';


/**
 * Controller used handle non-authenticated session actions like login/logout, user signup, and forgotten passwords
 */
class SessionController extends ControllerBase {

    protected function captchaCheck() {
        // do the google captcha
        $privatekey = "6LcXY_YSAAAAAAhWs4KE3Q7koqqB4nLHKQmIKnmA"; //localhost
        $resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

        if (!$resp->is_valid) {
// What happens when the CAPTCHA was entered incorrectly
            throw new AuthException('Invalid Captcha. Try again.');
        }
    }

    public function indexAction() {
        
    }

    /**
     * Allow a user to signup to the system
     */
    public function signupAction() {
        $form = new SignUpForm();

        if ($this->request->isPost()) {
            try {
                
                $config = $this->getDI()->get('config');
                // Captcha check new signups first
                if ($config->application->signupCaptcha)
                {
                    $this->captchaCheck();
                }
                if ($form->isValid($this->request->getPost()) != false) {
                    
                    $user = new Users();

                    $user->assign(array(
                        'name' => $this->request->getPost('name', 'striptags'),
                        'email' => $this->request->getPost('email'),
                        'password' => $this->security->hash($this->request->getPost('password')),
                        'profilesId' => 2
                    ));
                    //echo "User profile: " . $user->profilesId;

                    if ($user->save()) {
                        return $this->dispatcher->forward(array(
                                    'controller' => 'index',
                                    'action' => 'index'
                        ));
                    }
                    $this->flash->error($user->getMessages());
                }
            } catch (AuthException $e) {
                $this->flash->error($e->getMessage());
            }
        }

        $this->view->form = $form;
    }

    /**
     * Starts a session in the admin backend
     */
    public function loginAction() {
        $form = new LoginForm();

        try {

            if (!$this->request->isPost()) {

                if ($this->auth->hasRememberMe()) {
                    return $this->auth->loginWithRememberMe();
                }
            } else {
                // captcha or local validation first?
                if ($form->isValid($this->request->getPost()) == false) {
                    echo "Not valid<br/>";
                    foreach ($form->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                } else {
                    echo "Check<br/>";
                    $this->auth->check(array(
                        'email' => $this->request->getPost('email'),
                        'password' => $this->request->getPost('password'),
                        'remember' => $this->request->getPost('remember')
                    ));
                    // do the google captcha
                    $config = $this->getDI()->get('config');
                    if ($config->application->loginCaptcha)
                    {
                        $this->captchaCheck();
                    }
                    return $this->response->redirect('myaccount/edit');
                }
            }
        } catch (AuthException $e) {
            $this->flash->error($e->getMessage());
        }

        $this->view->form = $form;
    }

    /**
     * Shows the forgot password form
     */
    public function forgotPasswordAction() {
        $form = new ForgotPasswordForm();

        if ($this->request->isPost()) {

            if ($form->isValid($this->request->getPost()) == false) {
                foreach ($form->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {

                $user = Users::findFirstByEmail($this->request->getPost('email'));
                if (!$user) {
                    $this->flash->success('There is no account associated to this email');
                } else {

                    $resetPassword = new ResetPasswords();
                    $resetPassword->usersId = $user->id;
                    if ($resetPassword->save()) {
                        $this->flash->success('Success! Please check your messages for an email reset password');
                    } else {
                        foreach ($resetPassword->getMessages() as $message) {
                            $this->flash->error($message);
                        }
                    }
                }
            }
        }

        $this->view->form = $form;
    }

    /**
     * Closes the session
     */
    public function logoutAction() {
        $this->auth->remove();

        return $this->response->redirect('index/index');
    }

}
