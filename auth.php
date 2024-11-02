<?php
class Auth {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        session_start();
    }

    public function register($username, $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        return $this->db->query(
            'INSERT INTO users (username, password) VALUES (:username, :password)',
            [':username' => $username, ':password' => $hashed]
        );
    }

    public function login($username, $password) {
        $result = $this->db->query(
            'SELECT * FROM users WHERE username = :username',
            [':username' => $username]
        );
        $user = $result->fetchArray(SQLITE3_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['is_admin'] = $user['is_admin'];
            return true;
        }
        return false;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }

    public function logout() {
        session_destroy();
    }
}