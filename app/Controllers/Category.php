<?php namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;

class Category extends ResourceController {
    
    protected $modelName = 'App\Models\CategoryModel';
    protected $format = 'json';
    
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
