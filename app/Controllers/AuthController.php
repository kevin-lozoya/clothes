<?php
namespace App\Controllers;

use App\Template;
use App\Models\User;
use App\Modules\Log;
use App\Modules\Mailer;
use Sirius\Validation\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Database\Capsule\Manager as Capsule;

class AuthController extends Template {

  public function getLogin() {
    return $this->render('auth/login.twig');
  }

  public function postLogin() {
    $validator = new Validator();
    $validator->add('email:Email', 'required');
    $validator->add('email:Email', 'email');
    $validator->add('password:Password', 'required');

    if ($validator->validate($_POST)) {
      $user = User::where('email', $_POST['email'])->where('registration', 0)->first();
      if ($user) {
        if (password_verify($_POST['password'], $user->password)) {
          // OK
          $_SESSION['user'] = [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email
          ];
          Log::logInfo('Login success: '.$user->id);
          header('Location: '.BASE_URL);
        }
      }
      // Not OK
        $validator->addMessage('', 'Incorrect email and/or password');
    }

    $errors = $validator->getMessages();
    return $this->render('auth/login.twig', [
      'errors' => $errors
    ]);
  }

  public function getSignup() {
    return $this->render('auth/signup.twig');
  }

  public function postSignup() {
    $validator = new Validator();
    $validator->add('username:Username', 'required');
    $validator->add('email:Email', 'required');
    $validator->add('email:Email', 'email');
    $validator->add('password:Password', 'required');
    $validator->add('confirmpassword:Password Confirm', 'required');

    if ($validator->validate($_POST)) {
      if ($_POST['password'] == $_POST['confirmpassword']) {
        $token = md5(uniqid(rand(), true));
        $statusRegistration = true;
        Capsule::beginTransaction();
        try {
          $user = User::create([
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'token' => $token,
            'registration' => 1
          ]);
        } catch(QueryException $e) {
          $statusRegistration = false;
          Log::logError($e->getMessage());
          $validator->addMessage('', 'Username and/or email already exists. Try again.');
        }

        if ($statusRegistration) {
          $mailBody = $this->render('mails/registration.twig', [
            'url_confirmation' => 'auth/confirm/'.$token.'/'.$_POST['email']
          ]);
          $statusRegistration = Mailer::send($_POST['email'], 'Registration Confirmation', $mailBody);
          
          if ($statusRegistration) {
            Capsule::commit();
            return $this->render('auth/signup.twig', [
              'signup_confirm' => true
            ]);
          }
        }
      }
      else {
        $validator->addMessage('', "Those passwords didn't match.");
      }
    }

    $errors = $validator->getMessages();

    return $this->render('auth/signup.twig', [
      'errors' => $errors
    ]);
  }

  public function getConfirm(string $token, string $email) {
    $user = User::where('token', $token)
                ->where('email', $email)
                ->where('registration', 1)
                ->first();
    if ($user) {
      try {
        User::where('id', $user->id)
            ->update([
              'registration' => 0,
              'token' => ''
            ]);
        return $this->render('auth/confirm.twig');
      }
      catch(QueryException $e) {
        Log::logError($e->getMessage());
      }
    }

    header('Location: '.BASE_URL);
  }

  public function getResetpassword(string $token) {
    $user = User::where('token', $token)
                ->where('reset_password', 1)
                ->first();
    if ($user) {
      return $this->render('auth/resetpassword.twig', [
        'token' => $token
      ]);
    }

    header('Location: '.BASE_URL);
  }

  public function postResetpassword(string $token) {
    $validator = new Validator();
    $validator->add('ResetPasswordForm', 'required');
    $validator->add('t', 'required'); // Token
    $validator->add('p', 'required'); // Password
    $validator->add('cp', 'required'); // Confirm Password
    
    if ($validator->validate($_POST)) {
      $resetsuccess = false;
      if ($_POST['p'] == $_POST['cp']) {
        $user = User::where('token', $_POST['t'])
                    ->where('reset_password', 1)
                    ->first();
        if ($user) {
          try {
            User::where('id', $user->id)->update([
              'password' => password_hash($_POST['p'], PASSWORD_DEFAULT),
              'reset_password' => 0,
              'token' => ''
            ]);
            $resetsuccess = true;
          } catch (QueryException $e) {
            Log::logError($e->getMessage());
            $validator->addMessage('', 'The password could not be changed. Try again.');
          }
        }
        else {
          header('Location: '.BASE_URL.'/auth/login');
        }
      }
      else {
        $validator->addMessage('', "Passwords don't match.");
      }

      $errors = $validator->getMessages();

      return $this->render('auth/resetpassword.twig', [
        'token' => $_POST['t'],
        'errors' => $errors,
        'resetsuccess' => $resetsuccess
      ]);
    }

    header('Location: '.BASE_URL);
  }

  public function postRecover() {
    $validator = new Validator();
    $validator->add('RecoverPasswordForm', 'required');
    $validator->add('email', 'required');
    if ($validator->validate($_POST)) {
      $user = User::where('email', $_POST['email'])->first();
      if ($user) {
        $token = md5(uniqid(rand(), true));
        Capsule::beginTransaction();
        try {
          User::where('id', $user->id)->update([
            'token' => $token,
            'reset_password' => 1
          ]);
        } catch(QueryException $e) {
          Log::logError($e->getMessage());
        }
        $mailBody = $this->render('mails/recoverpassword.twig', [
          'username' => $user->username,
          'url_resetpassword' => 'auth/resetpassword/'.$token
        ]);
        Mailer::send($user->email, 'Password Reset', $mailBody);
        Log::logInfo('Recover Password User: '.$user->id);
        Capsule::commit();
      }
      return $this->render('auth/recover.twig');
    }

    header('Location: '.BASE_URL);
  }

  public function getLogout() {
    session_destroy();
    header('Location: '.BASE_URL.'auth/login');
  }


}
?>