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
        $dishes = HelpClass::shuffle_assoc($dishes);
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
        $dishes = HelpClass::shuffle_assoc($dishes);
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
            $dietDishes = self::planDailyDietWORestrictions($dishes, $rule);
        }
        return $dietDishes;
    }

    public static function planWeeklyDiet($dishes, PlanningRule $rule) {
        $counter = 1;
        // $alreadyPlannedDishes = array();
        $dailyDiets = array();
        while (count($dailyDiets) < 50) {
            $dailyDiets[$counter] = self::planDailyDietWORestrictions($dishes, $rule);
            $counter++;
            //$alreadyPlannedDishes = $alreadyPlannedDishes + $dailyDiets[$counter];
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
                    $goodDiets[++count($goodDiets)] = $dayDiet;
                    $dishesArray = $dishesArray + $dayDiet;
                }
                if (count($goodDiets == 7)) {
                    break;
                }
            }
        }

        return $goodDiets;
    }

    public static function getDiet($patientId) {
        $dishes = self::assessDishes($patientId);
        $patient = new PatientProfile();
        $diets = array();
        $patient->load($patientId);

        return self::planWeeklyDiet($dishes, $patient->getPlanningRule());
    }

}

?>
