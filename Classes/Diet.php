<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/config/dbconfig.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/PreparationMode.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/DishNutrient.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/Dish.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/PatientProfile.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/AssessmentRule.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/PlanningRule.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/PlanningRuleOutput.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/HelpClass.php');

class Diet {

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
                    $parts = explode("dish.nutrient", $rule->getText());
                    $processedRule = implode($parts);

                    foreach ($allNutrients as $key => $nutrient) {
                        if (strpos($processedRule, '.' . $key) > 0) {
                            if (key_exists($key, $dishNutrients)) {
                                $processedRule = str_replace('.' . $key, $dishNutrients[$key]->getQuantity(), $processedRule);
                            } else {
                                $processedRule = "return false;";
                            }
                        }
                    }
                } elseif (strpos($rule->getText(), 'preparationMode')) {
                    $parts = explode("dish.preparationMode", $rule->getText());
                    $processedPrepRule = implode($parts);
                    foreach ($allPrepModes as $key => $prepMode) {
                        if (strpos($processedPrepRule, '.' . $key) > 0) {
                            if (key_exists($key, $dishPrepModes)) {
                                $addDish = false;
                            }
                        }
                    }
                }

                if (eval($processedRule)) {
//                    echo '</br></br>';
//                    echo 'failed rule: ' . $id;
                }
                $satisfiedRules = $satisfiedRules && !eval($processedRule) && $addDish;
            }
            if ($satisfiedRules) {
//                echo '</br></br>';
//                echo 'good dish: ' . $dishId;
                $resultedDishes[$dishId] = $dish;
            }
        }

        return $resultedDishes;
    }

    public static function planDailyDietWORestrictions($dishes, PlanningRule $rule) {
        $acceptedError = 5 / 100;
        $recommendedKcal = $rule->getKCal();
        $otherRecommendedValues = array();

        foreach ($rule->getOutputs() as $output) {
            $otherRecommendedValues[$output->getNutrientId()] = array('min' => $output->getMinQuantity(), 'max' => $output->getMaxQuantity());
            $addedValues[$output->getNutrientId()] = 0;
        }

        $minsFullfilled = false;
        while ($minsFullfilled === false) {
            $addedKcal = 0;
            $addedValues = array();
            foreach ($rule->getOutputs() as $output) {
                $addedValues[$output->getNutrientId()] = 0;
            }
            $dietDishes = array();

            $allMins = true;
            $dishes = HelpClass::shuffle_assoc($dishes);
            $refusedDishesCounter = 0;
            foreach ($dishes as $dish) {
                if ($refusedDishesCounter > 10) {
                    break;
                }
                $nutrients = $dish->getNutrients();
                $calories = $dish->getCalories();
                if ($addedKcal + ($calories) - ($acceptedError * $recommendedKcal) < $recommendedKcal) {
                    $addDish = true;
                    foreach ($otherRecommendedValues as $nutrient => $recommendedValue) {
                        if (isset($nutrients[$nutrient]) && $nutrients[$nutrient] != null && (($addedValues[$nutrient] + ($nutrients[$nutrient]->getQuantity())) > ($recommendedValue['max'] + ($acceptedError * $recommendedValue['max'])))) {
                            $addDish = false;
                        }
                    }
                    if ($addDish) {
                        $dietDishes[$dish->getId()] = $dish;
                        $addedKcal += $calories;
                        foreach ($otherRecommendedValues as $nutrient => $recommendedValue) {
                            if (isset($nutrients[$nutrient]) && $nutrients[$nutrient] != null) {
                                $addedValues[$nutrient] += $nutrients[$nutrient]->getQuantity();
                            }
                        }
                    } else {
                        $refusedDishesCounter++;
                    }
                }
            }

            foreach ($otherRecommendedValues as $nutrient => $recommendedValue) {
                if ($addedValues[$nutrient] < $recommendedValue['min']) {
                    $allMins = false;
                }
            }
            if ($allMins && abs($recommendedKcal - $addedKcal) < 100) {
                $minsFullfilled = true;
            }
        }
        return $dietDishes;
    }

    public static function planWeeklyDiet($dishes, PlanningRule $rule) {
        $dishTypes = HelpClass::getDishTypes();
        $foundWeeklyDiet = false;
        $tries = 0;
        while ($tries < 5 && $foundWeeklyDiet === false) {
            $tries++;
            $counter = 1;
            $dailyDiets = array();
            while (count($dailyDiets) < 50) {
                $dailyDiets[$counter] = self::planDailyDietWORestrictions($dishes, $rule);
                $counter++;
            }

            //  $dishesArray = array();
            $goodDiets = array();
            foreach ($dailyDiets as $dayDiet) {
                $drinksCounter = 0;
                $snacksCounter = 0;
                $mainDishesCounter = 0;
                foreach ($dayDiet as $dishId => $dish) {
                    if ($dishTypes[$dish->getDishType()] == 'drink') {
                        $drinksCounter++;
                    } elseif ($dishTypes[$dish->getDishType()] == 'snack') {
                        $snacksCounter++;
                    } else {
                        $mainDishesCounter++;
                    }
                }
                if ($tries == 4 or ($drinksCounter <= 3 && $drinksCounter > 0 && $snacksCounter == 3 && $mainDishesCounter > 2)) {
                    $goodDietsCounter = count($goodDiets) + 1;
                    $goodDiets[$goodDietsCounter] = $dayDiet;
                }

                if (count($goodDiets) >= 7) {
                    if ($tries == 4) {
                        echo "*Dropped soft requirements";
                    }
                    $foundWeeklyDiet = true;
                    break;
                }
            }
        }

        return $goodDiets;
    }

    public static function getDiet($patientId) {
        $dishes = self::assessDishes($patientId);
//        $dishes = Dish::getAll();
//        foreach ($dishes as $id => $dish) {
//            //if ($id > 89) {
//            if($dish->getQuantityPerPortion() > 180){
//                $calories = $dish->getCalories() * 0.7;
//                $quantity = $dish->getQuantityPerPortion() * 0.7;
//
//                $sql = "UPDATE dish SET calories = '{$calories}', 
//                    quantityPerPortion = '{$quantity}'
//                    WHERE id = {$id}";
//
//                $result = mysql_query($sql);
//                $nutrients = $dish->getNutrients();
//
//                foreach ($nutrients as $nutrient) {
//                    $nutrientQuantity = $nutrient->getQuantity() * 0.7;
//                    $sql = "UPDATE dishNutrients SET quantity = '{$nutrientQuantity}'
//                    WHERE nutrientId = {$nutrient->getId()} AND dishId = {$id}";
//
//                    $result = mysql_query($sql);
//                }
//            }
//            //   }
//        } 

        $patient = new PatientProfile();
        $patient->load($patientId);

        $rule = $patient->getPlanningRule();

        if ($rule->getProvideDiet() == 0) {
            return $rule->getHint();
        }

        return self::planWeeklyDiet($dishes, $rule);
    }

}

?>
