<?php
// includes/berita_functions.php
require_once 'config/database.php';

class BeritaManager
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function getBerita($limit = 3, $offset = 0, $show_all = false)
    {
        try {
            $query = "SELECT 
                        b.id, b.judul, b.slug, b.excerpt, b.gambar_utama, 
                        b.gambar_kedua, b.tanggal_publish,
                        k.nama_kategori
                      FROM berita b 
                      LEFT JOIN kategori_berita k ON b.kategori_id = k.id 
                      WHERE b.status = 'published' 
                      ORDER BY b.tanggal_publish DESC";

            if (!$show_all) {
                $query .= " LIMIT :limit OFFSET :offset";
            }

            $stmt = $this->db->prepare($query);

            if (!$show_all) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting berita: " . $e->getMessage());
            return [];
        }
    }

    public function getBeritaById($id)
    {
        try {
            $query = "SELECT 
                        b.*, k.nama_kategori
                      FROM berita b 
                      LEFT JOIN kategori_berita k ON b.kategori_id = k.id 
                      WHERE b.id = :id AND b.status = 'published'";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting berita by ID: " . $e->getMessage());
            return false;
        }
    }

    public function getBeritaBySlug($slug)
    {
        try {
            $query = "SELECT 
                        b.*, k.nama_kategori
                      FROM berita b 
                      LEFT JOIN kategori_berita k ON b.kategori_id = k.id 
                      WHERE b.slug = :slug AND b.status = 'published'";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error getting berita by slug: " . $e->getMessage());
            return false;
        }
    }

    public function getTotalBerita()
    {
        try {
            $query = "SELECT COUNT(*) as total FROM berita WHERE status = 'published'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();

            return $result['total'];
        } catch (PDOException $e) {
            error_log("Error getting total berita: " . $e->getMessage());
            return 0;
        }
    }

    public function getKategori()
    {
        try {
            $query = "SELECT * FROM kategori_berita ORDER BY nama_kategori";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting kategori: " . $e->getMessage());
            return [];
        }
    }
}
