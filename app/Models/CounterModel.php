<?php
namespace App\Models;
 
use CodeIgniter\Model;

class CounterModel extends Model {

    public $table = 's_counter';
    private static $counterList = array();
    protected $allowedFields = ['cntr_period','cntr_cntr_code','cntr_cntr'];

    function runRegNumber($code,$period) {
        $period = str_replace("-","",$period);
        if ($period != '') { // will generate counter in all month in $year
            $line = $this->getCounterNumber($period, $code);
            if ($line) {
                //throw new Exception('cost center already exists for branch');
                /* Already exists for specified year */
                return;
            }
            $data = array();
            $data['cntr_period'] = $period;
            $data['cntr_cntr_code'] = $code;

            $this->db->table($this->table)->insert($data);
            
        }
    }
    
    function getCounterNumber($period='', $code='') {
        $sql = "SELECT * FROM $this->table WHERE cntr_period = ? AND cntr_cntr_code = ?";
        $query =  $this->db->query($sql, [$period, $code]);
        return $query->getResultArray();
    }
    
    public function getRegNumber($code,$period = '') {
        $period = str_replace("-","",$period);
        //create counter first
        $this->runRegNumber($code,$period);
        $key = "$period";
        
        if (!isset(self::$counterList[$key])) {
            $data = $this->getCounterNumber($period,$code);
            if (!$data) {
                return;
                
            }
            self::$counterList[$key] = $data[0];
        }

        $data = self::$counterList[$key];
        $data['cntr_cntr'] = $data['cntr_cntr'] + 1;

        self::$counterList[$key] = $data;
        $this->setCounterValue($data['id'], $data['cntr_cntr']);
        $num = str_pad($data['cntr_cntr'], 4, '0', STR_PAD_LEFT);

        $noReg = $code.$period.'-'.$num;
        return array('regnmbr'=>$noReg);
    }
    
    function setCounterValue($id, $value) {
        $counterModel = new \App\Models\CounterModel();
        $data = array();
        $data['cntr_cntr'] = $value;
//        $this->db->where(array('id'=>$id));
//        $this->db->update($id, $data);
        $counterModel->update($id, $data);
    }
    
    function reverseCounterValue() {
        $year = substr(date('Y'), 2);
        $month = date('m');
        $this->db->query('UPDATE '.$this->_table.' set cntr_cntr = cntr_cntr-1 WHERE cntr_period =  "'.$month.$year.'" and cntr_cntr_code = "'.self::SO.'"');
    }
    
    function checkCounter() {
        $year = substr(date('Y'), 2);
        $month = date('m');
        
        return $this->getCounterNumber('', $month.$year, self::SO);
    }
}