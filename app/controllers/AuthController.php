<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\AuthService;

class AuthController
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login()
    {
        // Get the submitted login credentials from the request
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Validate the login credentials
        if (!$this->validateLoginCredentials($email, $password)) {
            // Redirect the user back to the login page with an error message
            header("Location: /login?error=invalid_credentials");
            exit;
        }

        // Attempt to authenticate the user
        $user = $this->authService->authenticate($email, $password);
        if (!$user) {
            // Redirect the user back to the login page with an error message
            header("Location: /login?error=authentication_failed");
            exit;
        }

        // Set up the user session or generate an authentication token
        $this->authService->login($user);

        // Redirect the user to the home page or a protected resource
        header("Location: /home");
        exit;
    }

    public function register()
    {
        // Get the submitted registration data from the request
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Validate the registration data
        if (!$this->validateRegistrationData($name, $email, $password)) {
            // Redirect the user back to the registration page with an error message
            header("Location: /register?error=invalid_data");
            exit;
        }

        // Check if the user already exists
        if ($this->authService->userExists($email)) {
            // Redirect the user back to the registration page with an error message
            header("Location: /register?error=user_exists");
            exit;
        }

        // Create a new user
        $user = new User($name, $email, $password);
        $this->authService->register($user);

        // Redirect the user to the login page or a success page
        header("Location: /login?success=registration_complete");
        exit;
    }

    public function logout()
    {
        // Perform any necessary cleanup tasks or session/cookie clearing
        $this->authService->logout();

        // Redirect the user to the home page or a public page
        header("Location: /");
        exit;
    }

    private function validateLoginCredentials($email, $password)
    {
        // Implement your validation logic for login credentials
        if (empty($email) || empty($password)) {
            return false;
        }
        // Additional validation rules...

        return true; // Return true if the validation passes, or false otherwise
    }

    private function validateRegistrationData($name, $email, $password)
    {
        // Implement your validation logic for registration data
        if (empty($name) || empty($email) || empty($password)) {
            return false;
        }
        // Additional validation rules...

        return true; // Return true if the validation passes, or false otherwise
    }
}
