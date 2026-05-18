<?php
// UserController - Handles user management actions
require_once __DIR__ . '/../models/UserModel.php';

class UserController {
    private $user;

    public function __construct() {
        $this->user = new UserModel();
    }

    public function getAllUsers($limit = 50, $offset = 0) {
        return $this->user->getAllUsers($limit, $offset);
    }

    public function searchUsers($search_term, $limit = 50, $offset = 0) {
        return $this->user->searchUsers($search_term, $limit, $offset);
    }

    public function deactivateUser($user_id) {
        return $this->user->deactivateUser($user_id);
    }

    public function reactivateUser($user_id) {
        return $this->user->reactivateUser($user_id);
    }

    public function promoteToModerator($user_id) {
        return $this->user->updateUserRole($user_id, 'moderator');
    }

    public function demoteFromModerator($user_id) {
        return $this->user->updateUserRole($user_id, 'buyer');
    }
}
?>
