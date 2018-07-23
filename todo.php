<?php
/**
 * Created by Kyle Coots
 * User: kyle
 * Date: 7/22/18
 * Time: 12:42 AM
 */

require_once "./conn.php";

class todo{

    private $db_conn = '';

    private $action = '';
    private $item = '';
    private $id = 0;

    public $response;

    public function __construct($conn, $action, $item='', $id=null)
    {
        if(isset($action) && ctype_alpha($action)){
            $this->action = $action;
        }

        if(isset($item)){
            $this->item = $item;
        }

        if(isset($id)){
            $this->id = $id;
        }

        $this->db_conn = $conn;

        $this->response = $this->__init();
    }

    private function __init(){
        switch ($this->action){
            case 'get':
                return $this->get();
                break;
            case 'delete':
                return $this->delete();
                break;
            case 'update':
                return $this->update();
                break;
            case 'insert':
                return $this->insert();
                break;
        }
    }

    public function get(){

        $response = FALSE;
        $prepare = FALSE;
        $query = "SELECT * FROM `items`";

        if(is_object($this->item)){

            $prepare = TRUE;
            $param = '';
            $type = '';

            $query = "SELECT * FROM `items`";

            if(isset($this->item->deleted)) {
                $query .= " WHERE `deleted` = ?";
                $param .= $this->item->deleted;
                $type .= 's';
            }
            if(isset($this->item->date)){

                if(isset($this->item->deleted)){
                    $query .= " AND ";
                    $param .= $this->item->date;
                    $type .= 's';
                }else{
                    $query .= " WHERE ";
                    $param .= $this->item->date;
                    $type .= 's';
                }

                if(isset($this->item->dateBefore)){
                    $query .= "`date` > ?";
                }
                if(isset($this->item->dateAfter)){
                    $query .= "`date` > ?";
                }
                if(!isset($this->item->dateAfter) && !isset($this->item->dateBefore)) {
                    $query .= "`date` = ?";

                }

            }
            if(isset($this->item->id)) {
                $query = "SELECT * FROM `items` WHERE `iditems` = ?";
                $param = $this->item->id;
                $type = 'i';
            }

        }
        if($dbh = $this->db_conn->prepare($query)) {

            if($prepare){
                $param = explode(",",$param);

                switch (count($param)){
                    case 1:
                        $dbh->bind_param($type, $param[0]);
                        break;
                    case 2:
                        $dbh->bind_param($type, $param[0], $param[1]);
                        break;
                }

            }

            $dbh->execute();

            $res = $dbh->get_result();

            $response = $res->fetch_all(MYSQLI_ASSOC);


            $dbh->close();
        }

        $this->db_conn->close();

        return $response;
    }

    private function delete(){

        $response = FALSE;

        $query = "UPDATE `items`
                  SET `deleted`=1
                  WHERE `iditems` = ?";

        if($dbh = $this->db_conn->prepare($query)) {

            $dbh->bind_param("i", $this->id);

            $dbh->execute();

            $dbh->close();
        }

        $this->db_conn->close();

        return $response;
    }

    private function update(){

        $response = FALSE;

        $query = "UPDATE `items`
                  SET `item`=?,`deleted`=0
                  WHERE `iditems` = ?";

        if($dbh = $this->db_conn->prepare($query)) {

            $dbh->bind_param("sis", $this->item, $this->id);

            $dbh->execute();

            $dbh->close();
        }

        $this->db_conn->close();

        return $response;
    }

    private function insert(){

        $response = FALSE;

        $query = "INSERT INTO `items` (`item`) VALUES (?) ";

        if($dbh = $this->db_conn->prepare($query)) {

            $dbh->bind_param("s", $this->item);

            $dbh->execute();

            $response = $dbh->insert_id;

            $dbh->close();
        }

        $this->db_conn->close();

        return $response;
    }

}

if($_POST['action'] === 'get'){
    $obj = new stdClass;
    $obj->deleted = FALSE;
    $todo = new todo($mysqli, 'get', $obj);
    echo json_encode($todo->response);
}

if($_POST['action'] === 'insert'){
    $item = $_POST['item'];
    $todo = new todo($mysqli, 'insert', $item);
    echo json_encode($todo->response);
}

if($_POST['action'] === 'delete'){
    $todo = new todo($mysqli, 'delete', '', $_POST['item_id']);
}
