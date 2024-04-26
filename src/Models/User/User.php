<?php

namespace DegreePlanner\Models\User;

use DegreePlanner\Database\Database;

class User {
    public $id;
    private $email;
    private $passwordHash;
    public $firstName;
    public $middleName;
    public $lastName;
    public $role;


    public static function findUserByNetId($net_id): ?User {
        $db = new Database();
        $db = $db->db();

        $user = self::findUserByIdWithDb($db, $net_id);

        $db->close();

        return $user;
    }

    /** Allow finding multiple users without closing DB each query */
    public static function findUserByIdWithDb($db, $net_id): ?User {
        $user = self::tryRetrieveStudent($db, $net_id);
        if ($user == null) {
            $user = self::tryRetrieveAdvisor($db, $net_id);
        }
        return $user;
    }

    public static function tryRetrieveStudent($db, $net_id): ?Student {
        $q = "SELECT * FROM users NATURAL JOIN students NATURAL JOIN majors_in NATURAL JOIN majors WHERE net_id = ?";
        $stmt = $db->prepare($q);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("s", $net_id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $stmt->close();
            return null;
        }

        // Fetch first row of records. Due to join many records with the same student may return with different majors.
        $userDetails = $result->fetch_assoc();
        $student = new Student($userDetails);
        while ($record = $result->fetch_assoc()) {
            if ($record['net_id'] === $student->id) {
                $student->addMajor($record);
            }
        }
        $stmt->close();
        return $student;
    }

    public static function tryRetrieveAdvisor($db, $net_id): ?Advisor {
        $q = "SELECT * FROM faculty NATURAL JOIN users WHERE net_id= ?";
        $stmt = $db->prepare($q);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("s", $net_id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $stmt->close();
            return null;
        }

        $advisorDetails = $result->fetch_assoc();
        if ($advisorDetails == null) {
            return null;
        }
        $stmt->close();
        return new Advisor($advisorDetails);

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
            if (str_contains($error, 'Duplicate entry') && str_contains($error, 'users.PRIMARY')) {
                $msg = "This Net ID is already registered!";
            } elseif (str_contains($error, 'Duplicate entry') && str_contains($error, 'users.uc_email')) {
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

        // Todo() make this dynamic, add error handling, transactions
        $q = "INSERT INTO majors_in VALUES (?, 1)";
        $stmt = $db->prepare($q);
        $stmt->bind_param("s", $net_id);
        $stmt->execute();
        $stmt->close();


        $db->close();

        return true;
    }

    public function getFullName(): string {
        return $this->firstName . ' ' . $this->middleName . ' ' . $this->lastName;
    }

    public function getFirstLast(): string {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function __construct($userDetails) {
        $this->id = $userDetails['net_id'];
        $this->email = $userDetails['email'];
        $this->passwordHash = $userDetails['password_hash'];
        $this->firstName = $userDetails['first_name'];
        $this->middleName = $userDetails['middle_name'];
        $this->lastName = $userDetails['last_name'];
    }

}
