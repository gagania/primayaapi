<?php namespace App\Models;
 
use CodeIgniter\Model;
 
class CategoryModel extends Model
{
    protected $table = 'm_category';
    protected $primaryKey = 'id';
    protected $allowedFields = ['ctgr_name'];
    
    function getData() {
        $sql = "select * from $this->table as a";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }
}