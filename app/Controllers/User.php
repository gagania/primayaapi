<?php namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;

class User extends ResourceController {
    
    protected $modelName = 'App\Models\UserModel';
    protected $format = 'json';
    
    public function login() {
        $user = $this->model->check($this->request->getVar('username'));
        if (sizeof($user) > 0) {
            if (password_verify($this->request->getVar('userpass'), $user[0]['user_pass'])){
                unset($user[0]['user_pass']);
                return $this->respond($user);
            } else {
                return $this->fail('Wrong Password');
            }
        } else {
            return $this->fail('User not found');
        }
    }
    
    public function index() {
        $posts = $this->model->getData();
        return $this->respond($posts);
    }
    
    public function create() {
        helper(['form']);
        $rules =[
            'user_name' =>'required|min_length[4]',
            'user_birthdate' => 'required',
            'user_gndr' => 'required',
            'user_telp' => 'required',
            'user_height' => 'required',
            'user_weight' => 'required',
            'user_idnt' => 'required',
            'user_email' => 'required',
            'user_pass' => 'required',
            'user_addr' => 'required',
        ];
        
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getError());
        } else {
            $checkData = $this->model->checkRegister(trim($this->request->getVar('user_email')));
            if ($checkData) {
                return $this->fail('User already exist');
            }
            $data = [
                'user_name' => $this->request->getVar('user_name'),
                'user_birthdate' => $this->request->getVar('user_birthdate'),
                'user_gndr' => $this->request->getVar('user_gndr'),
                'user_telp' => $this->request->getVar('user_telp'),
                'user_height' => $this->request->getVar('user_height'),
                'user_weight' => $this->request->getVar('user_weight'),
                'user_rltn' => $this->request->getVar('user_rltn'),
                'user_idnt' => $this->request->getVar('user_idnt'),
                'user_email' => $this->request->getVar('user_email'),
                'user_pass' => $this->request->getVar('user_pass'),
                'user_addr' => $this->request->getVar('user_addr')
            ];
            $options = array('cost' => 11);
            $data['user_pass'] = password_hash((string) $data['user_pass'], PASSWORD_BCRYPT, $options);
            $data['user_birthdate'] = date('Y-m-d',strtotime(str_replace("/","-",$data['user_birthdate'])));
            $post_id = $this->model->insert($data);
            $data['id'] = $post_id;
            $this->sendemail($data);
            $returnData['id'] = $data['id'];
            $returnData['user_name'] = $data['user_name'];
            $returnData['user_email'] = $data['user_email'];
            return $this->respond($returnData);
        }
    }
    
    public function sendemail($response = array()) {
        $email = \Config\Services::email();

        $email->setTo($response['user_email']);
        $email->setFrom('no-reply@qrlab.id', 'Konfirmasi Registrasi [Test Primaya Hospital]');
        $data['users'] = $response;
        $message = view('user/template-email', $data);
        $email->setSubject('Konfirmasi Registrasi [Test Primaya Hospital]');
        $email->setMessage($message);

        if ($email->send()) {
//            echo 'Email successfully sent';
        } else {
            $data = $email->printDebugger(['headers']);
            print_r($data);
        }
    }
    
    
    public function show($id = null) {
        $data = $this->model->find($id);
        return $this->respond($data);
    }
    
    public function update($id = null) {
        helper(['form']);
        $rules =[
            'user_name' =>'required|min_length[4]',
            'user_birthdate' => 'required',
            'user_gndr' => 'required',
            'user_telp' => 'required',
            'user_height' => 'required',
            'user_weight' => 'required',
            'user_rltn' => 'required',
            'user_idnt' => 'required',
            'user_email' => 'required',
        ];
        
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getError());
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'id' =>$id,
                'user_name' => $input['user_name'],
                'user_birthdate' => $input['user_birthdate'],
                'user_gndr' => $input['user_gndr'],
                'user_telp' => $input['user_telp'],
                'user_height' => $input['user_height'],
                'user_weight' => $input['user_weight'],
                'user_rltn' => $input['user_rltn'],
                'user_idnt' => $input['user_idnt'],
                'user_email' => $input['user_email']
            ];
            $data['user_birthdate'] = date('Y-m-d',strtotime($data['user_birthdate']));
            $this->model->save($data);
            return $this->respond($data);
        }
    }
    
    
    public function updateStatus() {
        
        $input = $this->request->getRawInput();
        if (!isset($input['id'])) {
            return $this->fail("required ID.");
        }
        if ($input['id'] == '') {
            return $this->fail("ID is empty.");
        }
       
        $data = [
            'id'=> $input['id'],
            'is_verified'=>$input['is_verified']
        ];
        $sql = "UPDATE t_user set is_verified=? WHERE id = ? ";
        $this->model->query($sql, [$data['is_verified'], $data['id']]);
        return $this->respond("update success");
    }
    
    function change_pass() {
        $user = $this->model->check($this->request->getVar('useremail'));
        if (sizeof($user) > 0) {
            //send email
            return $this->respond($this->sendemailConf($user[0]));
        } else {
            return $this->fail('User not found');
        }
    }
    
    public function sendemailConf($response = array()) {
        $email = \Config\Services::email();

        $email->setTo($response['user_email']);
        $email->setFrom('no-reply@qrlab.id', 'Ganti Password [Test Primaya Hospital]');
//        $view = new \CodeIgniter\View\View();
        $data['users'] = $response;
        //update token
        $data['token'] = $token = bin2hex(random_bytes(50));
        $this->updateToken($response['user_email'],$token);
        $message = view('user/template-changepass-email', $data);
        $email->setSubject('Change Password [Test Primaya Hospital]');
        $email->setMessage($message);

        if ($email->send()) {
            echo 'Email successfully sent';
        } else {
            $data = $email->printDebugger(['headers']);
            print_r($data);
        }
    }
    
    public function updateToken($email,$token) {
        $data = [
            'user_email'=> $email,
            'user_token'=>$token
        ];
        $sql = "UPDATE t_user set user_token=? WHERE user_email = ? ";
        $this->model->query($sql, [$data['user_token'], $data['user_email']]);
    }
    
    public function updatePass() {
        
        $input = $this->request->getRawInput();
        if (!isset($input['id'])) {
            return $this->fail("required ID.");
        }
        if ($input['id'] == '') {
            return $this->fail("ID is empty.");
        }
       
        $data = [
            'id'=> $input['id'],
            'user_pass'=>$input['user_pass']
        ];
        $sql = "UPDATE t_user set user_pass=?,user_token = ? WHERE id = ? ";
        $this->model->query($sql, [$data['user_pass'], '',$data['id']]);
        return $this->respond("update success");
    }
    
    function check_token() {
        $user = $this->model->checkToken($this->request->getVar('usertoken'));
        if (sizeof($user) > 0) {
            return $this->respond($user);
        } else {
            return $this->fail('User not found');
        }
    }
}
