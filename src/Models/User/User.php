<?php

namespace DegreePlanner\Models\User;

use DegreePlanner\Database\Database;

class User {
    public $id;
    private $email;
    private $password_hash;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $role;

    public static function findUserByNetId($net_id) {
        $db = new Database();
        $db = $db->db();
        $q = "SELECT net_id, email, password_hash FROM users WHERE net_id = ? LIMIT 1";
        $stmt = $db->prepare($q);

        if (!$stmt) {
            $db->close();
            return null;
        }

        $stmt->bind_param("s", $net_id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $stmt->close();
            $db->close();
            return null;
        }
        $user = $result->fetch_assoc();
        $stmt->close();
        $db->close();

        return new User($user);
    }

    public static function register($net_id) {
        return self::findUserByNetId($net_id);
    }

    public static function login($net_id, $password): ?User {
        $user = self::findUserByNetId($net_id);
        if ($user == null) {
            password_hash('', PASSWORD_ARGON2ID); // We want to hash even when user DNE
            return null;
        }

        $password_hash = $user->password_hash;

        // Compare the given password to the stored password
        return password_verify($password, $password_hash) ? $user : null;
    }

    public function __construct($user) {
        $this->id = $user['net_id'];
        $this->email = $user['email'];
        $this->password_hash = $user['password_hash'];
    }
}
