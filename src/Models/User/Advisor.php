<?php

namespace DegreePlanner\Models\User;

use DegreePlanner\Database\Database;

class Advisor extends User {

    public static function getStudents($advisor_id) {
        $db = new Database();
        $db = $db->db();

        $q = "SELECT * FROM students WHERE advisor_id = ?";
        $stmt = $db->prepare($q);

        if (!$stmt) {
            $db->close();
            return null;
        }

        $stmt->bind_param("s", $advisor_id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $stmt->close();
            $db->close();
            return null;
        }
        $student_arr = array();
        while ($student = $result->fetch_assoc()) {
            $user = User::findUserByIdWithDb($db, $student['net_id']);
            if ($user == null) {
                continue;
            }
            $student_arr[] = $user;
        }
        $stmt->close();
        $db->close();
        return $student_arr;
    }

}
