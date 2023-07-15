<?php

/**
 * User Class
 *
 * Handles user-related database operations.
 */
class User extends Database
{
    protected $db;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->db = $this->conn;
    }

    /**
     * Create a new user.
     *
     * @param string $firstName   First name
     * @param string $lastName    Last name
     * @param string $email       Email address
     * @param string $password    Password
     * @param string $phone       Phone number
     * @param string $role        User role
     * @param string $employeeId  Employee ID
     *
     * @return int|bool           The user ID on success, false on failure
     */
    public function create($firstName, $lastName, $email, $password, $phone, $role, $employeeId)
    {
        $createdAt = date("Y-m-d G:i:s");
        $password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (first_name, last_name, email, password, phone, role, employee_id, created_at) 
                VALUES (:firstName, :lastName, :email, :password, :phone, :role, :employeeId, :createdAt)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':employeeId', $employeeId);
            $stmt->bindParam(':createdAt', $createdAt);
            $stmt->execute();

            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Get user details by ID.
     *
     * @param int $id   User ID
     *
     * @return array|bool     User details on success, false on failure
     */
    public function get($id)
    {
        $sql = "SELECT * FROM users WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $stmt->rowCount() > 0 ? $result : false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Get user details by email.
     *
     * @param string $email   Email address
     *
     * @return array|bool     User details on success, false on failure
     */
    public function getByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $stmt->rowCount() > 0 ? $result : false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Get all users.
     *
     * @return array|bool     All users on success, false on failure
     */
    public function getAll()
    {
        $sql = "SELECT * FROM users";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $stmt->rowCount() > 0 ? $result : false;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Rest of the code...

    /**
     * Update user details.
     *
     * @param int    $id          User ID
     * @param string $firstName   First name
     * @param string $lastName    Last name
     * @param string $email       Email address
     * @param string $phone       Phone number
     * @param string $role        User role
     * @param string $employeeId  Employee ID
     *
     * @return bool   True on success, false on failure
     */
    public function update($id, $firstName, $lastName, $email, $phone, $role, $employeeId)
    {
        $updatedAt = date("Y-m-d G:i:s");

        $sql = "UPDATE users SET first_name = :firstName, last_name = :lastName, email = :email, phone = :phone,
                role = :role, employee_id = :employeeId, updated_at = :updatedAt WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':employeeId', $employeeId);
            $stmt->bindParam(':updatedAt', $updatedAt);
            $stmt->execute();

            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Delete a user.
     *
     * @param int $id   User ID
     *
     * @return bool   True on success, false on failure
     */
    public function delete($id)
    {
        $sql = "DELETE FROM users WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
