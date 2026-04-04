<?php
// models/Category.php
// Handles database queries for categories (e.g., Food, Salary, Rent)

class Category {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    /**
     * Gets all categories to display in our dropdown
     */
    public function getAll() {
        // query() is used here instead of prepare() because we are not passing any
        // user input into the SQL, so there is no risk of SQL injection.
        $result = $this->db->query("SELECT * FROM categories ORDER BY type ASC, name ASC");
        
        // fetch_all() grabs all the rows at once as an array
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>