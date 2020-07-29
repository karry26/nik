<?php


class user_model extends Model
{
    public function __construct()
    {
        parent::__construct();

    }
    public function createUser($data,$login_type){
        try {
            echo $login_type;
            var_dump($data);
            $db = $this->getDb();

            if ($login_type == "google") {
                $pswd = password_hash($data["sub"], PASSWORD_BCRYPT);
            }
            $q = "INSERT INTO users(first_name,last_name,login_type,email,pic,password,phone_num,address,city,state)
            VALUES(:first_name,:last_name,:login_type,:email,:pic,:password,:phone_num,:address,:city,:state)";
            $stmt = $db->prepare($q);
            $stmt->execute(array($data["given_name"], $data["family_name"], $login_type, $data["email"], $data["picture"], $pswd,
                $data["pNum"], $data["address"], $data["city"], $data["state"]));
        }catch (PDOException $e){
            if(ERROR_DEBUG_MODE){
                echo "Error".$e; // For debugging
            }
        }

    }

    public function userExists($email,$login_type){
        try {
            $stmt = $this->getDb()->prepare("SELECT count(*) FROM users WHERE email=? AND login_type=?");
            $stmt->bindParam(1, $email, PDO::PARAM_STR);
            $stmt->bindParam(2, $login_type, PDO::PARAM_STR);

            $stmt->execute();
            $record = $stmt->fetchColumn();
            return $record > 0;
        }catch (PDOException $e){
            if(ERROR_DEBUG_MODE){
                echo "Error".$e; // For debugging
            }
        }

    }

    public function getUserId($email,$login_type){
        try {
            $q = "SELECT id FROM users WHERE email= :email AND login_type= :login_type";
            $stmt = $this->getDb()->prepare($q);
            $stmt->execute(array(":email" => $email, ":login_type" => $login_type));
            return $stmt->fetchColumn();
        }catch (PDOException $e){
            if(ERROR_DEBUG_MODE){
                echo "Error".$e; // For debugging
            }
        }

    }
    public function getUserDataByEmail($params,$email,$login_type){
        try {
            $params = implode(",", $params);
            $q = "SELECT " . $params . " FROM users WHERE email= :email AND login_type= :login_type";
            $stmt = $this->getDb()->prepare($q);
            $stmt->execute(array(":email" => $email, ":login_type" => $login_type));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch (PDOException $e){
            if(ERROR_DEBUG_MODE){
                echo "Error".$e; // For debugging
            }
        }

    }
    public function getUserDataByID($params,$id){
        try {
            $params = implode(",", $params);
            $q = "SELECT " . $params . " FROM users WHERE id = :id";
            $stmt = $this->getDb()->prepare($q);
            $stmt->execute(array(":id" => $id));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch (PDOException $e){
            if(ERROR_DEBUG_MODE){
                echo "Error".$e; // For debugging
            }
        }

    }
    public function updateUser($param,$paramvalue,$id){

        try {
            $q="UPDATE users SET ".$param."=:".$param." WHERE id = :id";
            $stmt = $this->getDb()->prepare($q);
            $stmt->execute(array(":" . $param => $paramvalue, ":id" => $id));
        }catch (PDOException $e){
            if(ERROR_DEBUG_MODE){
                echo "Error".$e; // For debugging
            }
        }
    }

    public function updateProperty($propid,$userid){
        $this->updateWishOrProp("property",$propid,$userid);
    }
    public function updateWishlist($propid,$userid){
        $this->updateWishOrProp("wishlist",$propid,$userid);
    }
    private function updateWishOrProp($toUpdate,$propid,$userid){
        try {
            $q = "SELECT ".$toUpdate." FROM users WHERE id= :id ";
            $stmt = $this->getDb()->prepare($q);
            $stmt->execute(array(":id" => $userid));
            $current_list=$stmt->fetchColumn();
            $current_list_array=explode(",",$current_list);
            if (!empty(trim($current_list))) {
                if(!in_array($propid,$current_list_array)) {
                    $current_list .= ("," . $propid); //add a comma seperated prop id to list of properties
                }else{
                    echo "Prop already in the wishlist";
                }
            }
            else
                $current_list=$propid;
            $this->updateUser($toUpdate,$current_list,$userid);
        }catch (PDOException $e){
            if(ERROR_DEBUG_MODE){
                echo "Error".$e; // For debugging
            }
        }
    }
    private function getWishlist($propid,$userid){
        $q = "SELECT wishlist FROM users WHERE id= :id ";
        $stmt = $this->getDb()->prepare($q);
        $stmt->execute(array(":id" => $userid));
        $current_list = $stmt->fetchColumn();
        $current_list_array = explode(",", $current_list);
        return array($current_list,$current_list_array);
    }

    public function checkPropInWishlist($propid,$userid){
        try {
            $wishlist_data=$this->getWishlist($propid,$userid);
            $current_list=$wishlist_data[0];
            $current_list_array=$wishlist_data[1];
            if (!empty(trim($current_list))) {
                if (!in_array($propid, $current_list_array)) {
                    return false;
                } else {
                    return true;
                }
            }
        }
        catch (PDOException $e){
            if(ERROR_DEBUG_MODE){
                echo "Error".$e; // For debugging
            }
        }
    }


    public function removePropFromWishlist($propid,$userid){
        try {
            $wishlist_data=$this->getWishlist($propid,$userid);
            $current_list_array=$wishlist_data[1];
            if (!empty($current_list_array)) {
                if (($key = array_search($propid, $current_list_array)) !== false) {
                    unset($current_list_array[$key]);
                }
            }
            $current_list=implode(",",$current_list_array);
            $this->updateUser("wishlist",$current_list,$userid);

        }
        catch (PDOException $e){
            if(ERROR_DEBUG_MODE){
                echo "Error".$e; // For debugging
            }
        }
    }

}