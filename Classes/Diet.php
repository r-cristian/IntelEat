<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/config/dbconfig.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/PreparationMode.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/DishNutrient.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/Dish.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/PatientProfile.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/AssessmentRule.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/PlanningRule.php');
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
                              $processedRule =  str_replace('.' . $key, $dishNutrients[$key]->getQuantity(), $processedRule);
                              
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
                $satisfiedRules = $satisfiedRules && !eval($processedRule) && $addDish;
            }
            if ($satisfiedRules) {
                $resultedDishes[$dishId] = $dish;
            }
        }

        return $resultedDishes;
    }

    public static function planDailyDietWORestrictions($dishes, PlanningRule $rule) {
        $dishes = HelpClass::shuffle_assoc($dishes);
        $acceptedError = 2 / 100;
        $recommendedKcal = $rule->getKCal();
        $otherRecommendedValues = array();

        $addedKcal = $rule->getKCal();
        $addedValues = array();
        $dietDishes = array();
        foreach ($rule->getOutputs() as $output) {
            $otherRecommendedValues[$output->getNutrientId()] = array('min' => $output->getMinQuantity(), 'max' => $output->getMaxQuantity());
            $addedValues[$output->getNutrientId()] = 0;
        }

        $minsFullfilled = false;
        while ($minsFullfilled === false) {
            foreach ($dishes as $dish) {
                $nutrients = $dish->getNutrients();
                $calories = $dish->getCalories();
                if ($addedKcal + ($calories / 1000) - ($acceptedError * $recommendedKcal) < $recommendedKcal) {
                    $addDish = true;
                    foreach ($otherRecommendedValues as $nutrient => $recommendedValue) {
                        if ($addedValues[$nutrient] + ($nutrients[$nutrient]) > $recommendedValue['max'] + ($acceptedError * $recommendedValue['max'])) {
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
            $allMins = true;
            foreach ($otherRecommendedValues as $nutrient => $recommendedValue) {
                if ($addedValues[$nutrient] < $recommendedValue['min']) {
                    $allMins = false;
                }
            }
            if ($allMins) {
                $minsFullfilled = true;
            }
        }
        return $dietDishes;
    }

    public static function planWeeklyDiet($dishes, PlanningRule $rule) {
        $counter = 1;
        $dailyDiets = array();
        while (count($dailyDiets) < 25) {
            $dailyDiets[$counter] = self::planDailyDietWORestrictions($dishes, $rule);
            $counter++;
        }
        $dishTypes = HelpClass::getDishTypes();
        $foundWeeklyDiet = false;

        while ($foundWeeklyDiet === false) {
            $drinksCounter = 0;
            $snacksCounter = 0;
            $mainDishesCounter = 0;

            $dishesArray = array();

            foreach ($dailyDiets as $dayDiet) {
                $duplicates = false;
                foreach ($dayDiet as $dishId => $dish) {
                    if (array_key_exists($dishId, $dishesArray)) {
                        $duplicates = true;
                    }
                    if ($dishTypes[$dish->getType()] == 'drink') {
                        $drinksCounter++;
                    } elseif ($dishTypes[$dish->getType()] == 'snack') {
                        $snacksCounter++;
                    } else {
                        $mainDishesCounter++;
                    }
                }

                if (!$duplicates && $drinksCounter <= 3 && $drinksCounter > 0 && $snacksCounter == 3 && $mainDishesCounter > 2) {
                    $goodDietsCounter = count($goodDiets) + 1;
                    $goodDiets[count($goodDietsCounter)] = $dayDiet;
                    $dishesArray = $dishesArray + $dayDiet;
                }
                if (count($goodDiets) == 2) {
                    break;
                }
            }
        }

        return $goodDiets;
    }

    public static function getDiet($patientId) {
        $dishes = self::assessDishes($patientId);
      
        $patient = new PatientProfile();
        $patient->load($patientId);

        $rule = $patient->getPlanningRule();
        var_dump($rule);
        if ($rule->getProvideDiet() == 0) {
            return $rule->getHint();
        }

        return self::planWeeklyDiet($dishes, $rule);
    }

}

?>
