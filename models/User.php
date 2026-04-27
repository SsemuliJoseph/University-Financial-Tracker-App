<?php
// models/User.php
// The User Model interacts exclusively with the 'users' database table

class User
{
    private $db;

    // Constructor gets called automatically when we do "new User($db)"
    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Checks if an email is already registered in the system
     */
    public function emailExists($email)
    {
        // prepare() sets up the SQL query but leaves '?' as a placeholder. This stops SQL injection!
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE email = ?");

        // bind_param() fills in the '?' safely. 's' means it is treating $email as a String.
        $stmt->bind_param("s", $email);

        // Execute the query
        $stmt->execute();

        // get_result() fetches the data returned by the query
        $result = $stmt->get_result();

        // Check if there are more than 0 rows (meaning the email was found)
        return $result->num_rows > 0;
    }

    /**
     * Creates a new user in the database
     */
    public function create($name, $email, $password, $role = 'student')
    {
        // password_hash() scrambles the password safely so that no one (not even the Database admin) can read it
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Another safety check, we prepare the INSERT statement
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");

        // We bind 4 variables. "ssss" means they are all strings.
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

        // Execute the insert. Returns true on success, false on failure.
        return $stmt->execute();
    }

    /**
     * Attempts to log a user in. 
     * Returns an array of user data on success, or false on failure.
     */
    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->num_rows === 1 ? $res->fetch_assoc() : false;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->num_rows === 1 ? $res->fetch_assoc() : false;
    }

    public function incrementFailedAttempts($email) {
        $stmt = $this->db->prepare("UPDATE users SET failed_attempts = failed_attempts + 1 WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
    }

    public function lockAccount($email) {
        $stmt = $this->db->prepare("UPDATE users SET locked_until = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
    }

    public function resetFailedAttempts($email) {
        $stmt = $this->db->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
    }
    
    public function updateRememberToken($id, $token) {
        $stmt = $this->db->prepare("UPDATE users SET remember_token = ? WHERE user_id = ?");
        $stmt->bind_param("si", $token, $id);
        $stmt->execute();
    }

    public function getByRememberToken($token) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE remember_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->num_rows === 1 ? $res->fetch_assoc() : false;
    }
    
    public function updateProfile($id, $name, $currency, $avatar = null) {
        if ($avatar !== null) {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, currency = ?, avatar = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $name, $currency, $avatar, $id);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, currency = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $name, $currency, $id);
        }
        return $stmt->execute();
    }
    
    public function updatePassword($id, $new_password) {
        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->bind_param("si", $hashed, $id);
        return $stmt->execute();
    }

    public function login($email, $password)
    {
        // Fetch the user by their email
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if exactly one user was found
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc(); // fetch_assoc() turns the row into an associative array (key-value pairs)
            
            // password_verify() checks if the typed password matches the scrambled hash in the database
            if (password_verify($password, $user['password'])) {
                // Remove the password from the array before returning it so we don't accidentally leak it
                unset($user['password']);
                return $user;
            }
        }
        
        // Return false if email not found OR password didn't match
        return false;
    }

    /**
     * Gets all users (Admin use only)
     */
    public function getAllUsers()
    {
        $result = $this->db->query("SELECT user_id, name, email, role, created_at FROM users ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Updates a user's role (Admin use only)
     */
    public function updateRole($user_id, $role)
    {
        $stmt = $this->db->prepare("UPDATE users SET role = ? WHERE user_id = ?");
        $stmt->bind_param("si", $role, $user_id);
        return $stmt->execute();
    }

    /**
     * Deletes a user account entirely (Admin use only)
     */
    public function delete($user_id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }
}
