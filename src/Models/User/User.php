<?php

namespace DegreePlanner\Models\User;

use DegreePlanner\Database\Database;
use DegreePlanner\Models\Major\Major;

class User {
    public $id;
    private $email;
    private $passwordHash;
    public $firstName;
    public $middleName;
    public $lastName;
    public $role;
    public $advisor;
    public $majors;

    public static function findUserByNetId($net_id): ?User {
        $db = new Database();
        $db = $db->db();

        $user = self::findUserByIdWithDb($db, $net_id);

        $db->close();

        return $user;
    }

    /** Allow finding multiple users without closing DB each query */
    public static function findUserByIdWithDb($db, $net_id): ?User {
        $q = "SELECT * FROM users WHERE net_id = ? LIMIT 1";
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
        $userDetails = $result->fetch_assoc();
        $stmt->close();

        $userDetails = self::getRole($db, $userDetails);
        if ($userDetails['role'] == 'student') {
            $userDetails["majors"] = Major::getStudentMajorsWithDb($db, $net_id);
        } else {
            $userDetails["majors"] = array();
        }

        return $userDetails != null ? new User($userDetails) : null;
    }

    private static function getRole($db, $userDetails) {
        $net_id = $userDetails['net_id'];
        if ($net_id == null) {
            return null;
        }

        // Check if they're a student
        $role = self::getUserRole($db, $net_id, 'students');
        if ($role) {
            $userDetails['role'] = 'student';
            $userDetails['advisor_id'] = $role['advisor_id'];
            return $userDetails;
        }

        // Check if they're faculty
        $role = self::getUserRole($db, $net_id, 'faculty');
        if ($role) {
            $userDetails['role'] = 'faculty';
            $userDetails['advisor_id'] = null;
            return $userDetails;
        }

        return null;

    }

    private static function getUserRole($db, $net_id, $tableName) {
        $q = "SELECT * FROM $tableName WHERE net_id = ? LIMIT 1";
        $stmt = $db->prepare($q);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("s", $net_id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;

    }

    public static function login($net_id, $password): ?User {
        $user = self::findUserByNetId($net_id);
        if ($user == null) {
            password_hash('', PASSWORD_ARGON2ID); // We want to hash even when user DNE
            return null;
        }

        $password_hash = $user->passwordHash;

        // Compare the given password to the stored password
        return password_verify($password, $password_hash) ? $user : null;
    }

    public static function register($net_id, $email, $password, $first_name, $middle_name, $last_name, $role) {
        $db = new Database();
        $db = $db->db();

        $password_hash = password_hash($password, PASSWORD_ARGON2ID);

        $q = "INSERT INTO users "
            . "(net_id, email, password_hash, first_name, middle_name, last_name)"
            . "VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($q);

        if (!$stmt) {
            $db->close();
            return "Could not complete registration!";
        }

        $stmt->bind_param("ssssss", $net_id, $email, $password_hash, $first_name, $middle_name, $last_name);

        try {
            $stmt->execute();
        } catch (\mysqli_sql_exception $e) {
            $error = $e->getMessage();
            if (strpos($error, 'Duplicate entry') !== false && strpos($error, 'users.PRIMARY') !== false) {
                $msg = "This Net ID is already registered!";
            } elseif (strpos($error, 'Duplicate entry') !== false && strpos($error, 'users.uc_email') !== false) {
                $msg = "This email is already in use!";
            } else {
                $msg = "Could not complete registration!";
            }
            $stmt->close();
            $db->close();
            return $msg;
        }
        $stmt->close();

        if ($role === 'student') {
            $table = 'students';
        } else {
            $table = 'faculty';
        }

        $q = "INSERT INTO $table (net_id) VALUES (?)";
        $stmt = $db->prepare($q);
        $stmt->bind_param("s", $net_id);
        $stmt->execute();

        $stmt->close();
        $db->close();

        return true;
    }

    public function hasMajor($major) {
        return in_array($major, $this->majors);
    }

    public function getFullName() {
        return $this->firstName . ' ' . $this->middleName . ' ' . $this->lastName;
    }

    public function __construct($userDetails) {
        $this->id = $userDetails['net_id'];
        $this->email = $userDetails['email'];
        $this->passwordHash = $userDetails['password_hash'];
        $this->firstName = $userDetails['first_name'];
        $this->middleName = $userDetails['middle_name'];
        $this->lastName = $userDetails['last_name'];
        $this->role = $userDetails['role'];
        $this->advisor = $userDetails['advisor_id'];
        $this->majors = $userDetails["majors"];
    }

}
