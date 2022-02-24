<?php namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use App\Models\LocationModel as locationModel;

class Products extends ResourceController {
    
    protected $modelName = 'App\Models\ProductModel';
    protected $format = 'json';
    
    public function __construct() {
        $this->locationModel = new locationModel();
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
    
    function getall() {
        if ($this->request->getVar('prdc_status')) {
            $data = $this->model->where('prdc_status', $this->request->getVar('prdc_status'))->findAll();
        } else {
            $data = $this->model->getData();
        }
        return $this->respond($data);
    }
}
