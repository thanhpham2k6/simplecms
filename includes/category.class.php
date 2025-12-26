<?php
class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($name, $slug, $description = '') {
        $sql = "INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':slug' => $slug,
            ':description' => $description
        ]);
    }
    
    public function getAll() {
        $sql = "SELECT c.*, COUNT(pc.post_id) as post_count 
                FROM categories c 
                LEFT JOIN post_categories pc ON c.id = pc.category_id 
                GROUP BY c.id 
                ORDER BY c.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function update($id, $name, $slug, $description) {
        $sql = "UPDATE categories SET name = :name, slug = :slug, description = :description WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':slug' => $slug,
            ':description' => $description
        ]);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    public function addPostCategory($post_id, $category_id) {
        $sql = "INSERT INTO post_categories (post_id, category_id) VALUES (:post_id, :category_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':post_id' => $post_id, ':category_id' => $category_id]);
    }
    
    public function removePostCategory($post_id, $category_id) {
        $sql = "DELETE FROM post_categories WHERE post_id = :post_id AND category_id = :category_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':post_id' => $post_id, ':category_id' => $category_id]);
    }
}
