<?php namespace App\Models;
 
use CodeIgniter\Model;
 
class ProductModel extends Model
{
    protected $table = 'm_product';
    protected $primaryKey = 'id';
    protected $allowedFields = ['prdc_name','prdc_price'];
    
    function getData() {
        $sql = "select a.id,a.prdc_name,a.prdc_price,b.ctgr_name from $this->table as a"
                . " left join m_category as b on b.id=a.prdc_ctgr_id";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }
    
    public function getProducts($where = array()) {
        $this->select("$this->table.*,b.ctgr_name");
        $this->join('m_category b', 'b.id = '.$this->table.'.prdc_ctgr_id', 'LEFT');
        $this->orderBy("$this->table.prdc_name");

        if (sizeof($where) >0) {
            foreach($where as $index => $value) {
                $this->where($this->table.'.'.$index,$value);
            }
        }

        $result = $this->findAll();
        return $result;
    }
}