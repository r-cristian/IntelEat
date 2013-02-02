<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/config/dbconfig.php');

class HelpClass {

    public static function getGenders() {
        $sql = "SELECT * FROM gender";

        $result = mysql_query($sql);
        if (!$result) {
            die("Query didn't work. " . mysql_error());
        }
        $units = array();
        while ($row = mysql_fetch_assoc($result)) {
            $genders[$row['id']] = $row['name'];
        }

        return $genders;
    }

    public static function getUnits() {
        $sql = "SELECT * FROM unit";

        $result = mysql_query($sql);
        if (!$result) {
            die("Query didn't work. " . mysql_error());
        }
        $units = array();
        while ($row = mysql_fetch_assoc($result)) {
            $units[$row['id']] = $row['name'];
        }

        return $units;
    }

    public static function getDiabetesTypes() {
        $sql = "SELECT * FROM diabetesType";

        $result = mysql_query($sql);
        if (!$result)
            die("Query didn't work. " . mysql_error());
        $types = array();

        while ($row = mysql_fetch_assoc($result)) {
            $types[$row['id']] = $row['name'];
        }

        return $types;
    }

    public static function getDishTypes() {
        $sql = "SELECT * FROM dishType";

        $result = mysql_query($sql);
        if (!$result)
            die("Query didn't work. " . mysql_error());
        $dishTypes = array();

        while ($row = mysql_fetch_assoc($result)) {
            $dishTypes[$row['id']] = $row['name'];
        }
        return $dishTypes;
    }

    public static function getLifestyles() {
        $sql = "SELECT * FROM lifestyle";

        $result = mysql_query($sql);
        if (!$result)
            die("Query didn't work. " . mysql_error());
        $lifestyles = array();

        while ($row = mysql_fetch_assoc($result)) {
            $lifestyles[$row['id']] = $row['name'];
        }
        return $lifestyles;
    }

    public static function multiexplode($delimiters, $string) {
        $ary = explode($delimiters[0], $string);
        array_shift($delimiters);
        if ($delimiters != NULL) {
            foreach ($ary as $key => $val) {
                $ary[$key] = multiexplode($delimiters, $val);
            }
        }
        return $ary;
    }

    public static function shuffle_assoc($list) {
        if (!is_array($list))
            return $list;

        $keys = array_keys($list);
        shuffle($keys);
        $random = array();
        foreach ($keys as $key)
            $random[$key] = $list[$key];

        return $random;
    }

    public static function assessDishes($patientId) {
        $patient = new PatientProfile();
        $patient->load($patientId);

        $rules = AssessmentRule::getAll();
        $dishes = Dish::getAll();
        $allNutrients = DishNutrient::getAllNutrients();
        $allPrepModes = PreparationMode::getAll();
        $resultedDishes = array();

        foreach ($dishes as $dishId => $dish) {
            $dishNutrients = $dish->getNutrients();
            $dishPrepModes = $dish->getPreparationModes();
            $satisfiedRules = true;
            foreach ($rules as $id => $rule) {
                $processedRule = "return false;";
                $addDish = true;

                if (strpos($rule->getText(), 'nutrient')) {
                    $parts = explode("dish.nutrient.", $rule->getText());
                    $processedRule = implode($parts);
                    foreach ($allNutrients as $key => $nutrient) {
                        if (strpos($processedRule, $key) > 0) {
                            if (key_exists($key, $dishNutrients)) {
                                str_replace($key, $dishNutrients[$key]->getQuantity(), $processedRule);
                            } else {
                                $processedRule = "return false;";
                            }
                        }
                    }
                } elseif (strpos($rule->getText(), 'preparationMode')) {
                    $parts = explode("dish.preparationMode.", $rule->getText());
                    $processedPrepRule = implode($parts);
                    foreach ($allPrepModes as $key => $prepMode) {
                        if (strpos($processedPrepRule, $key > 0)) {
                            if (key_exists($key, $dishPrepModes)) {
                                $addDish = false;
                            }
                        }
                    }
                }
                $satisfiedRules = $satisfiedRules && !eval($processedRule) && $addDish;
            }
            if ($satisfiedRules) {
                $resultedDishes[$dishId] = $dish;
            }
        }

        return $resultedDishes;
    }

    public static function planDailyDietWORestrictions($dishes, PlanningRule $rule) {
        $dishes = self::shuffle_assoc($dishes);
        $acceptedError = 2 / 100;
        $recommendedKcal = $rule->getKCal();
        $otherRecommendedValues = array();
        $addedKcal = $rule->getKCal();
        $addedValues = array();
        $dietDishes = array();
        foreach ($rule->getOutputs() as $output) {
            $otherRecommendedValues[$output->getNutrientId()] = $output->getQuantity();
            $addedValues[$output->getNutrientId()] = 0;
        }
        foreach ($dishes as $dish) {
            $nutrients = $dish->getNutrients();
            $calories = $dish->getCalories();
            if ($addedKcal + ($calories / 1000) - ($acceptedError * $recommendedKcal) < $recommendedKcal) {
                $addDish = true;
                foreach ($otherRecommendedValues as $nutrient => $recommendedValue) {
                    if ($addedValues[$nutrient] + ($nutrients[$nutrient]) > $recommendedValue + ($acceptedError * $recommendedValue)) {
                        $addDish = false;
                    }
                }
                if ($addDish) {
                    $dietDishes[$dish->getId()] = $dish;
                    $addedKcal += $calories;
                    foreach ($otherRecommendedValues as $nutrient => $recommendedValue) {
                        $addedValues[$nutrient] += $nutrients[$nutrient];
                    }
                }
            }
        }
        return $dietDishes;
    }

    public static function planDailyDiet($dishes, $alreadyPlannedDishes, PlanningRule $rule) {
        $dishes = self::shuffle_assoc($dishes);
        $acceptedError = 2 / 100;
        $recommendedKcal = $rule->getKCal();
        $otherRecommendedValues = array();
        $addedKcal = $rule->getKCal();
        $addedValues = array();
        $dietDishes = array();
        foreach ($rule->getOutputs() as $output) {
            $otherRecommendedValues[$output->getNutrientId()] = $output->getQuantity();
        }
        foreach ($dishes as $dish) {
            if (!key_exists($dish->getId(), $alreadyPlannedDishes)) {
                $nutrients = $dish->getNutrients();
                $calories = $dish->getCalories();
                if ($addedKcal + ($calories / 1000) - ($acceptedError * $recommendedKcal) < $recommendedKcal) {
                    $addDish = true;
                    foreach ($otherRecommendedValues as $nutrient => $recommendedValue) {
                        if ($addedValues[$nutrient] + ($nutrients[$nutrient]) > $recommendedValue + ($acceptedError * $recommendedValue)) {
                            $addDish = false;
                        }
                    }
                    if ($addDish) {
                        $dietDishes[$dish->getId()] = $dish;
                        $addedKcal += $calories;
                        foreach ($otherRecommendedValues as $nutrient => $recommendedValue) {
                            $addedValues[$nutrient] += $nutrients[$nutrient];
                        }
                    }
                }
            }
        }
        if ($addedKcal + 10 / 100 * $recommendedKcal < $recommendedKcal) {
            $dietDishes = self::planDailyDiet($dishes, $rule);
        }
        return $dietDishes;
        }

        public static function planWeeklyDiet($dishes, PlanningRule $rule){
        $counter = 1;
        $alreadyPlannedDishes = array();
        $dailyDiets = array();
        while (count($dailyDiets) < 8) {
            $dailyDiets[$counter] = self::planDailyDiet($dishes, $alreadyPlannedDishes, $rule);
            array_merge($alreadyPlannedDishes, $dailyDiets[$counter]);
        }
        return $dailyDiets;
    }
    
    public static function getDiet($patientId){
        $dishes = self::assessDishes($patientId);
        $patient = new PatientProfile();
        $patient->load($patientId);
        return self::planWeeklyDiet($dishes, $patient->getPlanningRule());
    }

}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
