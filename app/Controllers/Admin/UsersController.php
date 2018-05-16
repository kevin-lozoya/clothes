<?php
namespace App\Controllers\Admin;

use App\Modules\Log;
use App\Models\AuthUser;
use App\Models\AuthGroup;
use App\Modules\Template;
use App\Models\AuthMembership;
use Sirius\Validation\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Database\Capsule\Manager as Capsule;

class UsersController extends Template {

  public function getIndex() {
    $users = AuthUser::join('auth_membership', 'auth_user.id', '=', 'auth_membership.user_id')
                      ->join('auth_group', 'auth_membership.group_id', '=', 'auth_group.id')
                      ->where ('auth_group.role', '<>', 'root')
                      ->get();

    $groups = AuthGroup::where('role', '<>', 'root')->get();
    
    $errors = !empty($_SESSION['errors']) ? $_SESSION['errors'] : null;
    $message = isset($_SESSION['message']) ? $_SESSION['message'] : null;
    unset($_SESSION['errors']);
    unset($_SESSION['message']);

    return $this->render('admin/users.twig', [
      'users' => $users,
      'groups' => $groups,
      'errors' => $errors,
      'message' => $message
    ]);
  }

  public function postCreate() {
    $validator = new Validator;
    $validator->add('CreateUserForm', 'required');
    $validator->add('u:Username', 'required');
    $validator->add('r:Role', 'required');
    $validator->add('e:Mail', 'required');
    $validator->add('e:Mail', 'email');
    $validator->add('p:Password', 'required');
    $validator->add('cp:Confirm Password', 'required');

    if ($validator->validate($_POST)) {
      if ($_POST['p'] == $_POST['cp']) {
        Capsule::beginTransaction();
        try {
          $user = AuthUser::create([
            'username' => $_POST['u'],
            'email' => $_POST['e'],
            'password' => password_hash($_POST['p'], PASSWORD_DEFAULT),
          ]);
          AuthMembership::create([
            'user_id' => $user->id
          ]);
          Capsule::commit();
        } catch(QueryException $e) {
          Log::logError($e->getMessage());
          $validator->addMessage('', 'Username and/or email already exists. Try again.');
        }
      }
      else {
        $validator->addMessage('', "Passwords didn't match.");
      }
    }

    $_SESSION['errors'] = $validator->getMessages();
    var_dump($_SESSION['errors']);

    header('Location: '.BASE_URL.'admin/users');

  }

  public function postUpdate() {
    $validator = new Validator();
    $validator->add('UpdateUserForm', 'required');
    $validator->add('id:Id', 'required');
    $validator->add('id:Id', 'integer');
    $validator->add('u:Username', 'required');
    $validator->add('r:Role', 'required');
    $validator->add('r:Role', 'integer');
    $validator->add('e:Mail', 'required');
    $validator->add('e:Mail', 'email');

    if ($validator->validate($_POST)) {
      $statusTransaction = true;
      Capsule::beginTransaction();
      try {
        AuthUser::where('id', $_POST['id'])->update([
          'username' => $_POST['u'],
          'email' => $_POST['e']
        ]);
        AuthMembership::where('user_id', $_POST['id'])->update([
          'group_id' => $_POST['r']
        ]);
        Capsule::commit();
      } catch(QueryException $e) {
        Log::logError($e->getMessage());
      }
    }

    header('Location: '.BASE_URL.'admin/users');
  }
  
  public function postDelete() {
    $validator = new Validator();
    $validator->add('id', 'required');
    $validator->add('id', 'integer');

    if ($validator->validate($_POST)) {
      AuthUser::where('id', $_POST['id'])->delete();
    }

    header('Location: '.BASE_URL.'admin/users');
  }

}
?>