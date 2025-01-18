<?php 
class user 
{ 
    private $id;
    private $name;
    private $email;
    private $role;
    private $status;
    private $expiredDate;
    private $keyFlag;
    private $defaultTime;
    
    // Constructor 
    public function __construct(){} 
    
    function setId($id){
        $this->id = $id;
    }
    
    function getId() {
        return $this->id;
    }
    
    function setName($name){
        $this->name = $name;
    }
   
    function getName(){
        return $this->name;
    }
    
    function setEmail($email){
        $this->email = $email;
    }
    
    function getEmail(){
        return $this->email;
    }
    
    function setRole($role){
        $this->role = $role;
    }
   
    function getRole(){
        return $this->role;
    }
    
    function setStatus($status){
        $this->status = $status;
    }
    
    function getStatus(){
        return $this->status;
    }
    
    function setExpiredDate($expiredDate){
        $this->expiredDate = $expiredDate;
    }
    
    function getExpiredDate(){
        return $this->expiredDate;
    }
    
    function setKeyFlag($keyFlag){
        $this->keyFlag = $keyFlag;
    }
    
    function getKeyFlag() {
        return $this->keyFlag;
    }
    
    function setDefaultTime($defaultTime){
        $this->defaultTime = $defaultTime;
    }
    
    function getDefaultTime() {
        return $this->defaultTime;
    }
}
?>