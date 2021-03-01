<?php
namespace Usersystem;

use \Usersystem\DataSource;

class Member
{

    private $ds;

    function __construct()
    {
        require_once "DataSource.php";
        $this->ds = new DataSource();
    }

    function getMemberById($memberId){
        $query = "select * FROM " . DataSource::USERTABLE . " WHERE ID = ?";
        $paramType = "i";
        $paramArray = array($memberId);
        $memberResult = $this->ds->select($query, $paramType, $paramArray);
        
        return $memberResult;
    }
	
	
    function getMemberByUNAME($memberName){
        $query = "select * FROM " . DataSource::USERTABLE . " WHERE UserName = ?";
        $paramType = "s";
        $paramArray = array($memberName);
        $memberResultByName = $this->ds->select($query, $paramType, $paramArray);
        
        return $memberResultByName;
    }

    function checkMemberExist($memberName){
        $query = "select UserName FROM " . DataSource::USERTABLE . " WHERE UserName = ?";
        $paramType = "s";
        $paramArray = array(trim($memberName));
        $memberResultByName = $this->ds->select($query, $paramType, $paramArray);
        $searchedMember = (isset($memberResultByName[0]['UserName'])) ? $memberResultByName[0]['UserName'] : false;
        return ($searchedMember !== false && $searchedMember === $memberName) ? true : false;
    }
	
	function getAllMember(){
        $query = "select * FROM " . DataSource::USERTABLE;
        $AllMemberResult = $this->ds->select($query);
        
        return $AllMemberResult;
    }

    function registerUser($username, $password){

            $userSecret = rand(100000000, 999999999);
            $userToken = rand(100000000, 999999999);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO " . DataSource::USERTABLE . " (UserName, UserPassword, UserSecret, UserToken) VALUES (?, ?, ?, ?)";
            $paramType = "ssii";
            $paramArray = Array($username, $hashed_password, $userSecret, $userToken);
            if($this->ds->insert($query, $paramType, $paramArray)){
				return true;
			} else { return false; }
    }
    
    public function processLogin($username, $password) {
		$queryUserPassworldHash = "select UserPassword, UserName FROM " . DataSource::USERTABLE . " WHERE UserName = ?";
		$paramTypeForUP = "s";
		$paramArrayForUP = array($username);
		$userPassworldHashResult = $this->ds->select($queryUserPassworldHash, $paramTypeForUP, $paramArrayForUP);

		$passwordHash = (isset($userPassworldHashResult[0]['UserPassword'])) ? $userPassworldHashResult[0]['UserPassword'] : "zero";
        $queryedUser = (isset($userPassworldHashResult[0]['UserName'])) ? $userPassworldHashResult[0]['UserName'] : "ThisDoesnotExist";
				
		if (password_verify($password, $passwordHash) && $username === $queryedUser) {
        $query = "select * FROM " . DataSource::USERTABLE . " WHERE UserName = ? AND UserPassword = ?";
        $paramType = "ss";
        $paramArray = array($username, $passwordHash);
        $memberResult = $this->ds->select($query, $paramType, $paramArray);
        if(!empty($memberResult)) {
			$_SESSION["UserID"] = $memberResult[0]["ID"];
			$_SESSION["UserName"] = $memberResult[0]["UserName"];
			$_SESSION["UserSecret"] = $memberResult[0]["UserSecret"];
			$_SESSION["UserToken"] = $memberResult[0]["UserToken"];
            return true;
        } else {
               			   
			return false;
		}
          } else {
               return false;
         }
		
    }
}