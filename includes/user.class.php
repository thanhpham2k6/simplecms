<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function register($username, $email, $password, $role = 'subscriber') {
        $sql = "INSERT INTO users (username, email, password, role, created_at) 
                VALUES (:username, :email, :password, :role, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_BCRYPT),
            ':role' => $role
        ]);
    }
    
    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username = :username OR email = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
        return false;
    }
    
    public function logout() {
        session_destroy();
        redirect(SITE_URL);
    }
    
    public function getById($id) {
        $sql = "SELECT id, username, email, role, created_at FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function getAll() {
        $sql = "SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE users SET ";
        $fields = [];
        $params = [':id' => $id];
        
        foreach($data as $key => $value) {
            if($key === 'password') {
                $fields[] = "password = :password";
                $params[':password'] = password_hash($value, PASSWORD_BCRYPT);
            } else {
                $fields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }
        
        $sql .= implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
