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

    /**
     * Assessing dishes
     * This method read assessment rules from the database and applies them to the provided list
     * of dishes
     * 
     * @param int $patientId
     * @return array of dishes
     */
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

                $satisfiedRules = $satisfiedRules && !eval($processedRule) && $addDish;
            }
            if ($satisfiedRules) {
                $resultedDishes[$dishId] = $dish;
            }
        }

        return $resultedDishes;
    }

    /**
     * Method used to plan a daily diet considering quantities of
     * KCalories and nutrients (with an error)
     * 
     * @param type $dishes
     * @param PlanningRule $rule
     * @return type
     */
    public static function planDailyDiet($dishes, PlanningRule $rule) {
        $acceptedError = 5 / 100;
        $recommendedKcal = $rule->getKCal();
        $otherRecommendedValues = array();

        foreach ($rule->getOutputs() as $output) {
            $otherRecommendedValues[$output->getNutrientId()] = array('min' => $output->getMinQuantity(), 'max' => $output->getMaxQuantity());
        }
        //the minmum values of the nutrients must be fullfilled for a diet to be valid
        $minsFullfilled = false;
        while ($minsFullfilled === false) {
            $addedKcal = 0;
            $addedValues = array();
            foreach ($rule->getOutputs() as $output) {
                $addedValues[$output->getNutrientId()] = 0;
            }
            $dietDishes = array();

            $allMins = true;
            //random sort the dishes array
            $dishes = HelpClass::shuffle_assoc($dishes);
            $refusedDishesCounter = 0;

            foreach ($dishes as $dish) {
                //if too many dishes have been refused break the for, so a new diet will start
                //to be built
                if ($refusedDishesCounter > 10) {
                    break;
                }
                $nutrients = $dish->getNutrients();
                $calories = $dish->getCalories();
                //if by adding this dish to the diet the calories amount won't be exceeded
                if ($addedKcal + ($calories) - ($acceptedError * $recommendedKcal) < $recommendedKcal) {
                    $addDish = true;
                    foreach ($otherRecommendedValues as $nutrient => $recommendedValue) {
                        //check if the nutrients values will be exceeded
                        if (isset($nutrients[$nutrient]) && $nutrients[$nutrient] != null && (($addedValues[$nutrient] + ($nutrients[$nutrient]->getQuantity())) > ($recommendedValue['max'] + ($acceptedError * $recommendedValue['max'])))) {
                            $addDish = false;
                        }
                    }
                    //if dish can be added, add it and compute all added values
                    if ($addDish) {
                        $dietDishes[$dish->getId()] = $dish;
                        $addedKcal += $calories;
                        foreach ($otherRecommendedValues as $nutrient => $recommendedValue) {
                            if (isset($nutrients[$nutrient]) && $nutrients[$nutrient] != null) {
                                $addedValues[$nutrient] += $nutrients[$nutrient]->getQuantity();
                            }
                        }
                        //count how many dishes have been refused in a row
                        //if more than 10 have been refused it means that the diet will most likely never be completed
                    } else {
                        $refusedDishesCounter++;
                    }
                }
            }
            //check if the computed values are higher than the min recommended values
            foreach ($otherRecommendedValues as $nutrient => $recommendedValue) {
                if ($addedValues[$nutrient] < $recommendedValue['min']) {
                    $allMins = false;
                }
            }
            //check if there are enough calories
            if ($allMins && abs($recommendedKcal - $addedKcal) < 100) {
                $minsFullfilled = true;
            }
        }
        return $dietDishes;
    }

    /**
     * Method used to plan a weekly diet
     * It will generate a number of daily diets, try to find 7 diets fullfilling
     * the soft requirements; if not possible within a number of tries it will return the next 7 diets
     * 
     * @param array $dishes
     * @param PlanningRule $rule
     * @return array
     */
    public static function planWeeklyDiet($dishes, PlanningRule $rule) {
        $dishTypes = HelpClass::getDishTypes();
        $foundWeeklyDiet = false;
        $tries = 0;
        //the while stops if the number of tries has been reached or the diet has been completed
        while ($tries < 5 && $foundWeeklyDiet === false) {
            $tries++;
            $counter = 1;
            $dailyDiets = array();
            //generate a sufficient number of daily diets
            while (count($dailyDiets) < 50) {
                $dailyDiets[$counter] = self::planDailyDiet($dishes, $rule);
                $counter++;
            }

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
                //check soft requirements or if the number of tries is close to the limit
                //if satisfied, add the daily diet to the weekly diet 
                if ($tries == 4 or ($drinksCounter <= 3 && $drinksCounter > 0 && $snacksCounter == 3 && $mainDishesCounter > 2)) {
                    $goodDietsCounter = count($goodDiets) + 1;
                    $goodDiets[$goodDietsCounter] = $dayDiet;
                }

                //if 7 daily diets, break
                if (count($goodDiets) >= 7) {
                    //if the max number of tries is to be reached, drop the soft requirements
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

    /**
     * Method used to create diet
     * It asses the dishes, then builds a weekly diet
     * @param int $patientId
     * @return array
     */
    public static function getDiet($patientId) {
        $dishes = self::assessDishes($patientId);
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
