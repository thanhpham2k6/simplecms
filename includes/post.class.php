<?php
class Post {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO posts (title, slug, content, excerpt, author_id, status, featured_image, created_at) 
                VALUES (:title, :slug, :content, :excerpt, :author_id, :status, :featured_image, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':content' => $data['content'],
            ':excerpt' => $data['excerpt'] ?? '',
            ':author_id' => $data['author_id'],
            ':status' => $data['status'] ?? 'draft',
            ':featured_image' => $data['featured_image'] ?? null
        ]);
    }
    
    public function getAll($status = null, $limit = 10, $offset = 0) {
        $sql = "SELECT p.*, u.username as author_name 
                FROM posts p 
                LEFT JOIN users u ON p.author_id = u.id";
        
        if($status) {
            $sql .= " WHERE p.status = :status";
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        if($status) {
            $stmt->bindValue(':status', $status);
        }
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT p.*, u.username as author_name 
                FROM posts p 
                LEFT JOIN users u ON p.author_id = u.id 
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function getBySlug($slug) {
        $sql = "SELECT p.*, u.username as author_name 
                FROM posts p 
                LEFT JOIN users u ON p.author_id = u.id 
                WHERE p.slug = :slug AND p.status = 'published'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE posts SET 
                title = :title,
                slug = :slug,
                content = :content,
                excerpt = :excerpt,
                status = :status,
                featured_image = :featured_image,
                updated_at = NOW()
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'],
            ':slug' => $data['slug'],
            ':content' => $data['content'],
            ':excerpt' => $data['excerpt'] ?? '',
            ':status' => $data['status'],
            ':featured_image' => $data['featured_image'] ?? null
        ]);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM posts WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    public function count($status = null) {
        $sql = "SELECT COUNT(*) as total FROM posts";
        if($status) {
            $sql .= " WHERE status = :status";
        }
        $stmt = $this->db->prepare($sql);
        if($status) {
            $stmt->execute([':status' => $status]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetch()['total'];
    }
}
