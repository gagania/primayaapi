<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model {
    protected $table = 'a_user';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_name','user_real_name','user_email', 'user_pass'];
    
    function getData() {
        $sql = "select user_name,user_real_name, user_email from $this->table";
        echo $sql;exit;
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }
    
    function check($userName) {
        $sql = "select id,user_name,user_pass,user_email from $this->table where user_id='$userName'";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }
    
    function checkRegister($userName) {
        $sql = "select id,user_name,user_pass,user_email from $this->table where user_name='$userName'";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }
    function checkToken($userToken) {
        $sql = "select id,user_token from $this->table where user_token='$userToken'";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }
}

