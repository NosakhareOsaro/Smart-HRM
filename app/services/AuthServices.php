<?php

namespace App\Services;

use App\Models\User;
use Database;

class AuthService
{
    protected $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function authenticate(string $email, string $password): ?User
    {
        // Perform the authentication logic
        $query = "SELECT * FROM users WHERE email = ?";
        $user = $this->db->queryOne($query, [$email]);

        if ($user && password_verify($password, $user['password'])) {
            return new User($user['name'], $user['email'], $user['password']);
        }

        return null;
    }

    public function login(User $user)
    {
        // Set up the user session or generate an authentication token
        // ...
      
            $_SESSION['is_login'] = true;		
            $_SESSION['user_id'] = $user['id'];
            
            $logged_in_user_id =  $_SESSION['user_id'];
            $last_login_at = date("Y-m-d G:i:s");  
            $last_login_at_ip = $_SERVER['REMOTE_ADDR'];
            
            $update_data = [
                'last_login_at' => $last_login_at,
                'last_login_at_ip' => $last_login_at_ip
            ];

            $update = $user->update('id', $logged_in_user_id, $update_data);
    }

    public function logout()
    {
        // Clear the user session or authentication token
        // ...
        session_destroy();

        header('location: ' .  base_url(""));
    }

    public function register(User $user)
    {
        // Save the user record in the database
        $query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $hashedPassword = password_hash($user->password, PASSWORD_ARGON2ID);
        $this->db->execute($query, [$user->name, $user->email, $hashedPassword]);

        $_SESSION['is_login'] = true;
        $_SESSION['user_id'] = $query['id'];
    }

    public function userExists(string $email): bool
    {
        // Check if a user with the given email exists in the database
        $query = "SELECT COUNT(*) FROM users WHERE email = ?";
        $result = $this->db->querySingleValue($query, [$email]);

        return ($result > 0);
    }
}
