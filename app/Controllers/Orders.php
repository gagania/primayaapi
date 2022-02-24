<?php namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;

class Orders extends ResourceController {
    
    protected $modelName = 'App\Models\OrderModel';
    protected $counterModelName = 'App\Models\CounterModel';
    protected $productModelName = 'App\Models\ProductModel';
    protected $format = 'json';
    
    public function index() {
        $posts = $this->model->getData();
        return $this->respond($posts);
    }
    
    public function order() {
        $where = array();
        if ($this->request->getVar('id')) {
            $where['id'] = $this->request->getVar('id');
        }
        if ($this->request->getVar('user_id')) {
            $where['order_user_id'] = $this->request->getVar('user_id');
        }
        
        $order = new $this->modelName();
        $data = $order->getOrders($where);
        return $this->respond($data);
    }
    
    public function show($id = null) {
        $data = $this->model->getData($id);
//        $data = $this->model->find($id);
        if($data){
            return $this->respond($data);
        }else{
            return $this->failNotFound('No Data Found with id '.$id);
        }
    }

    public function create() {
        helper(['form']);
        $rules =[
            'order_table' => 'required',
            'order_user_name' => 'required',
            'order_status' => 'required',
        ];
        
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getError());
        } else {
            $data = [
                'order_table' => $this->request->getVar('order_table'),
                'order_user_name' => $this->request->getVar('order_user_name'),
                'created_by' => $this->request->getVar('created_by'),
                'order_detail' => $this->request->getVar('order_detail'),
                'order_date' => $this->request->getVar('order_date_date'),
                'order_status' => $this->request->getVar('order_status'),
                'user_id' => $this->request->getVar('user_id'),
            ];
            $data['order_date'] = date('Y-m-d H:i:s',strtotime(str_replace("/","-",$data['order_date'])));
            if ($this->request->getVar('order_cost')) {
                $data['order_cost'] = $this->request->getVar('order_cost');
            }
            if ($this->request->getVar('order_ppn')) {
                $data['order_ppn'] = $this->request->getVar('order_ppn');
            }
            if ($this->request->getVar('order_total_cost')) {
                $data['order_total_cost'] = $this->request->getVar('order_total_cost');
            }
            
            //get regNumber
            $counterModel = new $this->counterModelName();
            $regNmbr = $counterModel->getRegNumber('PSN',date('Y-m-d'));
            $data['order_nmbr'] = $regNmbr['regnmbr'];
            $post_id = $this->model->insert($data);
            $data['id'] = $post_id;
            return $this->respondCreated($data);
        }
    }
    
    public function update($id = null) {
//        $input = $this->request->getRawInput();
        helper(['form']);
        if ($id == NULL) {
            return $this->fail("required ID.");
        } else {
            
        $rules =[
            'id'=>'required',
            'order_table' => 'required',
            'order_user_name' => 'required',
            'order_status' => 'required',
        ];
        
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getError());
        } else {
//            $data = [
//                'order_table' => $this->request->getVar('order_table'),
//                'order_user_name' => $this->request->getVar('order_user_name'),
//                'created_by' => $this->request->getVar('created_by'),
//                'order_detail' => $this->request->getVar('order_detail'),
//                'order_date' => $this->request->getVar('order_date_date'),
//                'order_status' => $this->request->getVar('order_status'),
//                'user_id' => $this->request->getVar('user_id'),
//            ];
            $data = $this->request->getRawInput();
            if ($this->request->getVar('order_cost')) {
                $data['order_cost'] = $this->request->getVar('order_cost');
            }
            if ($this->request->getVar('order_ppn')) {
                $data['order_ppn'] = $this->request->getVar('order_ppn');
            }
            if ($this->request->getVar('order_total_cost')) {
                $data['order_total_cost'] = $this->request->getVar('order_total_cost');
            }
            
            $post_id = $this->model->save($data);
            $data['id'] = $post_id;
            return $this->respondCreated($data);
        }
            return $this->respond("update success");
        }
    }
    
    public function history() {
        if (!$this->request->getVar('user_id')) {
            $this->fail('User Id not found');
        }
        $where = array();
        if ($this->request->getVar('id')) {
            $where['id'] = $this->request->getVar('id');
        }
        if ($this->request->getVar('user_id')) {
            $where['order_user_id'] = $this->request->getVar('user_id');
        }
        
        $order = new $this->modelName();
        $data = $order->getOrders($where);
        return $this->respond($data);
    }
    
    public function registrationinfo() {
        if (!$this->request->getVar('user_id')) {
            $this->fail('User Id not found');
        }
        $where = array();
        if ($this->request->getVar('id')) {
            $where['id'] = $this->request->getVar('id');
        }
        if ($this->request->getVar('user_id')) {
            $where['order_user_id'] = $this->request->getVar('user_id');
        }
        if ($this->request->getVar('status')) {
            $where['order_status'] = $this->request->getVar('status');
        }
        if ($this->request->getVar('paid')) {
            $where['is_paid'] = $this->request->getVar('paid');
        }
        
        $order = new $this->modelName();
        $data = $order->getOrdersForRegistration($where);
        return $this->respond($data);
    }
    
    public function updateStatus() {
        
        $input = $this->request->getRawInput();
        if (!isset($input['order_reg_nmbr'])) {
            return $this->fail("required Reg Number.");
}
        if ($input['order_reg_nmbr'] == '') {
            return $this->fail("Reg Number is empty.");
        }
       
        $data = [
            'order_reg_nmbr'=> $input['order_reg_nmbr'],
            'order_reference'=> $input['order_reference'],
            'order_result_code'=> $input['order_result_code'],
            'is_paid'=>$input['is_paid']
        ];
        $sql = "UPDATE t_order set order_reference=?,order_result_code=?,is_paid=? WHERE order_reg_nmbr = ? ";
        $this->model->query($sql, [$data['order_reference'],$data['order_result_code'],$data['is_paid'], $data['order_reg_nmbr']]);
        return $this->respond("update success");
    }
    
    public function delete_order() {
        $input = $this->request->getRawInput();
        if (!isset($input['order_reg_nmbr'])) {
            return $this->fail("required Reg Number.");
        }
        if ($input['order_reg_nmbr'] == '') {
            return $this->fail("Reg Number is empty.");
        }
        
        $sql = "DELETE FROM t_order WHERE order_reg_nmbr = ? ";
        $this->model->query($sql, [$input['order_reg_nmbr']]);
        return $this->respond("delete success");
    }
}