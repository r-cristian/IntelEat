<?php

class PlanningRule {

    private $id;
    private $minAge;
    private $maxAge;
    private $minBMI;
    private $maxBMI;
    private $gender;
    private $lifestyle;
    private $diabetesType;
    private $KCal;
    private $provideDiet;
    private $hint;
    private $outputs = array();

    public function getId() {
        return $this->id;
    }

    public function getMinAge() {
        return $this->minAge;
    }

    public function setMinAge($minAge) {
        $this->minAge = $minAge;
    }

    public function getMaxAge() {
        return $this->maxAge;
    }

    public function setMaxAge($maxAge) {
        $this->maxAge = $maxAge;
    }

    public function getMinBMI() {
        return $this->minBMI;
    }

    public function setMinBMI($minBMI) {
        $this->minBMI = $minBMI;
    }

    public function getMaxBMI() {
        return $this->maxBMI;
    }

    public function setMaxBMI($maxBMI) {
        $this->maxBMI = $maxBMI;
    }

    public function getGender() {
        return $this->gender;
    }

    public function setGender($gender) {
        $this->gender = $gender;
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

    public function getKCal() {
        return $this->KCal;
    }

    public function setKCal($Kcal) {
        $this->KCal = $Kcal;
    }

    public function getProvideDiet() {
        return $this->provideDiet;
    }

    public function setProvideDiet($provideDiet) {
        $this->provideDiet = $provideDiet;
    }

    public function getHint() {
        return $this->hint;
    }

    public function setHint($hint) {
        $this->hint = $hint;
    }

    public function getOutputs() {
        return $this->outputs;
    }

    public function __construct() {
        
    }

    public function load($id) {
        $sql = "SELECT * FROM planningRules 
                WHERE id = {$id}";

        $result = mysql_query($sql);
        if (!$result)
            return false;
        $row = mysql_fetch_assoc($result);

        $this->id = $row['id'];
        $this->minAge = $row['minAge'];
        $this->maxAge = $row['maxAge'];
        $this->minBMI = $row['minBMI'];
        $this->maxBMI = $row['maxBMI'];
        $this->gender = $row['genderId'];
        $this->lifestyle = $row['lifestyleId'];
        $this->diabetesType = $row['diabetesTypeId'];
        $this->KCal = $row['KCal'];
        $this->outputs = PlanningRuleOutput::getAllByRuleId($row['id']);
        $this->provideDiet = $row['provideDiet'];
        $this->hint = $row['hint'];

        return true;
    }

    public static function getAll() {
        $sql = "SELECT * FROM planningRules";

        $result = mysql_query($sql);
        if (!$result)
            return false;

        $rules = array();
        while ($row = mysql_fetch_assoc($result)) {
            $rules[$row['id']] = new PlanningRule();
            $rules[$row['id']]->load($row['id']);
        }
        return $rules;
    }

    public static function getByPatientProfile(PatientProfile $patientProfile) {        
        $sql = "SELECT * FROM planningRules 
                WHERE minAge <= {$patientProfile->getAge()} 
                  AND maxAge >= {$patientProfile->getAge()} 
                  AND minBMI <= {$patientProfile->getBMI()}
                  AND maxBMI >= {$patientProfile->getBMI()}
                  AND genderId = {$patientProfile->getGender()}
                  AND diabetesTypeId = {$patientProfile->getDiabetesType()}
                  AND lifestyleId = {$patientProfile->getLifestyle()}";
        $result = mysql_query($sql);
        if (!$result)
            return false;
        $row = mysql_fetch_assoc($result);
        $res = new PlanningRule();
        $res->load($row['id']);

        return $res;
    }

}

?>
