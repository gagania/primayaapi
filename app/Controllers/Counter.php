<?php namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;

class Counter extends ResourceController {
    
    protected $modelName = 'App\Models\CounterModel';
    protected $format = 'json';
    
    public function regnumber() {
        helper(['form']);
        $rules =[
            'period' =>'required',
            'ctgr' => 'required',
        ];
        
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getError());
        } else {
           
            $period = $this->request->getVar('period');
            $ctgr = $this->request->getVar('ctgr');
            $data = $this->model->getRegNumber($period,$ctgr);
            return $this->respond($data);
        }
    }
    
    public function index() {
        $posts = $this->model->getData();
        return $this->respond($posts);
    }
    
    public function show($id = null) {
        $data = $this->model->find($id);
        if($data){
            return $this->respond($data);
        }else{
            return $this->failNotFound('No Data Found with id '.$id);
        }
    }

}
