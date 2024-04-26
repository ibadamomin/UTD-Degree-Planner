<?php

namespace DegreePlanner\Models\User;

use DegreePlanner\Database\Database;

class Advisor extends User {

    public function __construct($userDetails) {
        parent::__construct($userDetails);
        $this->role = 'faculty';
    }

    public function getStudents() {
        $db = new Database();
        $db = $db->db();

        $q = <<<EOT
        SELECT advisor_id, s.net_id, email, first_name, middle_name, last_name, mi.major_id, name, degree_type
        FROM faculty as f
        INNER JOIN students as s ON s.advisor_id = f.net_id
        INNER JOIN users u ON u.net_id = s.net_id
        INNER JOIN majors_in mi ON s.net_id = mi.net_id
        INNER JOIN majors m ON mi.major_id = m.major_id
        WHERE f.net_id = ?
        ORDER BY s.net_id
        EOT;
        $stmt = $db->prepare($q);

        if (!$stmt) {
            $db->close();
            return null;
        }

        $stmt->bind_param("s", $this->id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $stmt->close();
            $db->close();
            return null;
        }
        $student_arr = array();
        while ($student = $result->fetch_assoc()) {
            $user = new Student($student);
            if ($user == null) {
                continue;
            }
            if (self::isStudentInArray($user->id, $student_arr)) {
                $user = $student_arr[$user->id];
                $user->addMajor($student);
            }
            $student_arr[$user->id] = $user;
        }
        $stmt->close();
        $db->close();
        return $student_arr;
    }

    private static function isStudentInArray($studentId, $studentArr) {
        foreach ($studentArr as $student) {
            if ($student->id === $studentId) {
                return true;
            }
        }
        return false;
    }

}
