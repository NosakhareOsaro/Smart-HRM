<?php

/**
 * Attendance Class
 *
 * Handles attendance-related database operations.
 */
class Attendance extends Database
{
    protected $db;

    /**
     * Attendance constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->db = $this->conn;
    }

    /**
     * Create a new attendance record.
     *
     * @param int    $user_id            User ID
     * @param string $date               Attendance date
     * @param string $attendance_status  Attendance status
     *
     * @return int|bool   The attendance ID on success, false on failure
     */
    public function create($user_id, $date, $attendance_status)
    {
        $sql = "INSERT INTO attendance (user_id, date, attendance_status) 
                VALUES (:user_id, :date, :attendance_status)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':attendance_status', $attendance_status);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Get attendance details by user ID and date.
     *
     * @param int    $user_id  User ID
     * @param string $date     Attendance date
     *
     * @return array|bool   Attendance details on success, false on failure
     */
    public function getByUserIdAndDate($user_id, $date)
    {
        $sql = "SELECT * FROM attendance WHERE user_id = :user_id AND date = :date";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $stmt->rowCount() > 0 ? $result : false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get all attendance records.
     *
     * @return array|bool   All attendance records on success, false on failure
     */
    public function getAll()
    {
        $sql = "SELECT * FROM attendance";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $stmt->rowCount() > 0 ? $result : false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update attendance status by user ID and date.
     *
     * @param int    $user_id            User ID
     * @param string $date               Attendance date
     * @param string $attendance_status  Attendance status
     *
     * @return bool   True on success, false on failure
     */
    public function updateAttendanceStatus($user_id, $date, $attendance_status)
    {
        $sql = "UPDATE attendance SET attendance_status = :attendance_status 
                WHERE user_id = :user_id AND date = :date";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':attendance_status', $attendance_status);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':date', $date);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update attendance status by user ID.
     *
     * @param int    $user_id            User ID
     * @param string $attendance_status  Attendance status
     *
     * @return bool   True on success, false on failure
     */
    public function update($user_id, $attendance_status)
    {
        $sql = "UPDATE attendance SET attendance_status = :attendance_status 
                WHERE user_id = :user_id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':attendance_status', $attendance_status);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Search attendance records by month and year.
     *
     * @param int $month  Month
     * @param int $year   Year
     *
     * @return array|bool   Attendance records on success, false on failure
     */
    public function search($month, $year)
    {
        $sql = "SELECT users.first_name AS user_name, DAY(date) AS day, attendance_status
                FROM attendance
                INNER JOIN users ON attendance.user_id = users.id
                WHERE MONTH(date) = :month AND YEAR(date) = :year";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':month', $month);
            $stmt->bindParam(':year', $year);
            $stmt->execute();
            $attendanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $stmt->rowCount() > 0 ? $attendanceData : false;
        } catch (PDOException $e) {
            return false;
        }
    }
}
