<?php namespace App\Models;
 
use CodeIgniter\Model;
 
class OrderModel extends Model
{
    protected $table = 't_order';
    protected $primaryKey = 'id';
    protected $allowedFields = ['order_table','order_nmbr','order_user_name','created_by',
                                'order_prdc_id','order_status','order_cost',
                                'order_ppn','order_total_cost','order_detail'];
    
    function getData($id='') {
        $sql = "select a.id,a.order_nmbr,a.order_table,c.ctgr_name,p.prdc_name"
                . " from $this->table as a"
                . " left join m_product as p on p.id=a.order_prdc_id"
                . " left join m_category as c on c.id=p.prdc_ctgr_id";
        if ($id!= '') {
            $sql .=" where a.id = $id";
        }
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }
    
    public function getOrders($where = array()) {
        $this->select("$this->table.id,$this->table.order_table,$this->table.order_nmbr,"
                . " $this->table.is_paid,$this->table.order_user_name,"
                . "$this->table.order_cost,$this->table.order_ppn,"
                . "$this->table.order_total_cost,$this->table.order_detail");
        $this->join('m_product as p',"p.id=$this->table.order_prdc_id",'LEFT');
        $this->join('m_category c', "c.id = p.prdc_ctgr_id", 'LEFT');
        
        $this->orderBy("$this->table.id");
        if (sizeof($where) > 0) {
            foreach($where as $index => $value) {
                $this->where($this->table.'.'.$index,$value);
            }
        }

        $result = $this->findAll();
        return $result;
    }
}