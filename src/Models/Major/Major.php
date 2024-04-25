<?php

namespace DegreePlanner\Models\Major;

use DegreePlanner\Database\Database;

class Major {
    public $id;
    public $major_name;
    public $degree_type;

    public static function getStudentMajors($net_id) {
        $db = new Database();
        $db = $db->db();

        $q = "SELECT * FROM majors_in WHERE net_id = ?";
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

        $major_ids = array();
        while ($row = $result->fetch_assoc()) {
            $major_ids[] = $row['major_id'];
        }

        $stmt->close();

        $major_arr = array();
        foreach ($major_ids as $major_id) {
            $major = self::getMajorFromId($major_id);
            if ($major != null) {
                $major_arr[] = $major;
            }
        }

        $db->close();

        return $major_arr;
    }

    public static function getMajorFromId($major_id) {
        $db = new Database();
        $db = $db->db();

        $q = "SELECT * FROM majors WHERE major_id = ?";
        $stmt = $db->prepare($q);

        if (!$stmt) {
            $db->close();
            return null;
        }

        $stmt->bind_param("s", $major_id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $stmt->close();
            $db->close();
            return null;
        }

        $major = $result->fetch_assoc();

        $stmt -> close();
        $db->close();

        return $major != null ? new Major($major) : null;

    }

    public function __construct($majorDetails) {
        $this->id = $majorDetails["major_id"];
        $this->major_name = $majorDetails["name"];
        $this->degree_type = $majorDetails["degree_type"];
    }
}