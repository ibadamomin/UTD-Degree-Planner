<?php

namespace DegreePlanner\Models\User;


use DegreePlanner\Database\Database;

class User {
    public $id;
    public $email;
    public $password_hash;

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

    public static function login($net_id, $password): ?User {
        $user = self::findUserByNetId($net_id);
        if ($user == null) {
            $password_hash = ''; // We want to hash even when user DNE to prevent timing attacks.
        } else {
            $password_hash = $user->password_hash;
        }

        if (password_verify($password, $password_hash)) {
            return $user;
        } else {
            return null;
        }
    }

    public function __construct($user) {
        $this->id = $user['net_id'];
        $this->email = $user['email'];
        $this->password_hash = $user['password_hash'];
    }
}
