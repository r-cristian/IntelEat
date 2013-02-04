<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/config/dbconfig.php');

class PatientProfile {

    private $id = null;
    private $name;
    private $age;
    private $gender;
    private $height;
    private $weight;
    private $lifestyle;
    private $diabetesType;

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getAge() {
        return $this->age;
    }

    public function setAge($age) {
        $this->age = $age;
    }

    public function getGender() {
        return $this->gender;
    }

    public function setGender($gender) {
        $this->gender = $gender;
    }

    public function getHeight() {
        return $this->height;
    }

    public function setHeight($height) {
        $this->height = $height;
    }

    public function getWeight() {
        return $this->weight;
    }

    public function setWeight($weight) {
        $this->weight = $weight;
    }

    public function getLifestyle() {
        return $this->lifestyle;
    }

    public function setLifestyle($lifestyle) {
        $this->lifestyle = $lifestyle;
    }

    public function getDiabetesType() {
        return $this->diabetesType;
    }

    public function setDiabetesType($diabetesType) {
        $this->diabetesType = $diabetesType;
    }
    
    public function getBMI(){
        return $this->height / ($this->weight * $this->weight) * 1000;
    }
    
    public function getPlanningRule(){
        return PlanningRule::getByPatientProfile($this);
    }

    public function __construct() {
        
    }

    public function save() {
        if ($this->id == null) {
            $sql = "INSERT INTO patient
                (name, age, gender, height, weight, lifestyle, diabetesType)
                VALUES
                ('{$this->name}', '{$this->age}', '{$this->gender}', '{$this->height}', '{$this->weight}', '{$this->lifestyle}', '{$this->diabetesType}');";
        } else {
            $sql = "UPDATE patient SET name = '{$this->name}',
                    age = '{$this->age}',
                    gender = '{$this->gender}',
                    height = '{$this->height}',
                    weight = '{$this->weight}',
                    lifestyle = '{$this->lifestyle}',
                    diabetesType = '{$this->diabetesType}' 
                    WHERE id = {$this->id}";
        }

        $result = mysql_query($sql);
        if (!$result)
            return false;
        if ($this->id === null) {
            $this->id = mysql_insert_id();
        }

        return true;
    }

    public function load($id) {
        $sql = "SELECT * FROM patient 
                WHERE id = {$id}";

        $result = mysql_query($sql);
        if (!$result)
            return false;
        $row = mysql_fetch_assoc($result);

        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->age = $row['age'];
        $this->gender = $row['gender'];
        $this->height = $row['height'];
        $this->weight = $row['weight'];
        $this->lifestyle = $row['lifestyle'];
        $this->diabetesType = $row['diabetesType'];

        return true;
    }

    public static function getAll() {
        $sql = "SELECT * FROM patient";

        $result = mysql_query($sql);
        if (!$result)
            return false;        
        $patients = array();
        while ($row = mysql_fetch_assoc($result)) {
            $patients[$row['id']] = new PatientProfile();
            $patients[$row['id']]->load($row['id']);
        }
        return $patients;
    }
    
    public static function deleteById($id){
         $sql = "DELETE FROM patient 
                 WHERE id = {$id}";

        $result = mysql_query($sql);
        if (!$result)
            return false;
        return true;
    }

}

?>
