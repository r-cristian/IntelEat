<?php
include_once('Classes/HelpClass.php');
include_once('Classes/PatientProfile.php');

$genders = HelpClass::getGenders();
$diabetesTypes = HelpClass::getDiabetesTypes();
$lifestyles = HelpClass::getLifestyles();

$error = '';
$index = strpos($_SERVER['REQUEST_URI'], '?patient');
$redirectUrl = substr($_SERVER['REQUEST_URI'], 0, $index);
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!empty($_POST['name']) && !empty($_POST['age']) && !empty($_POST['height']) && !empty($_POST['weight'])) {
        if (!is_numeric($_POST['age'])) {
            $error = "Age must be numeric.";
        } elseif ($_POST['age'] > 120) {
            $error = "You are too old to be alive.";
        }
        if (!is_numeric($_POST['height']) || $_POST['height'] <= 0) {
            $error .= "</br> Height must be a positive number.";
        } elseif ($_POST['height'] > 220) {
            $error .= "</br>Unrealistic height.";
        }
        if (!is_numeric($_POST['weight']) || $_POST['weight'] <= 0) {
            $error .= "</br>Weight must be a positive number.";
        } elseif ($_POST['weight'] > 400) {
            $error .= " </br>Unrealistic height.";
        }

        if (empty($error)) {
            $patient = new PatientProfile();
            if (isset($_GET['patient'])) {
                $patient->load($_GET['patient']);
            }

            $patient->setName($_POST['name']);
            $patient->setAge($_POST['age']);
            $patient->setGender($_POST['gender']);
            $patient->setHeight($_POST['height']);
            $patient->setWeight($_POST['weight']);
            $patient->setLifestyle($_POST['lifestyle']);
            $patient->setDiabetesType($_POST['diabetesType']);
            if (!$patient->save()) {
                $error = "Unable to save patient profile.";
            } else {
                $success = true;
            }
        }
    } else {
        $error = "You must fill in all fields.";
    }
} else {
    if (isset($_GET['patient'])) {
        //edit
        $patient = new PatientProfile();
        if (!$patient->load($_GET['patient'])) {
            header("Location: $redirectUrl");
        }
    } else {
        //add
        //nothing to do
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <h1>  <?php echo!isset($patient) ? 'Add patient profile' : 'Edit patient profile' ?>
        </h1>
        </br>
        </br>
        <?php if (isset($error) && !empty($error)): ?>   
            <span style="color: red;"><?php echo $error ?></span>

        <?php endif ?>
        <?php if (!isset($success) || $success == null): ?>
            <form action="" method="post">
                <table>
                    <tbody>                    
                        <tr>
                            <td>Name:
                            </td>
                            <td>
                                <input type="text" id="name" name="name" value="<?php echo isset($patient) ? $patient->getName() : '' ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>Age:
                            </td>
                            <td>
                                <input type="text" id="age" name="age" value="<?php echo isset($patient) ? $patient->getAge() : '' ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>Height (cm):
                            </td>
                            <td>
                                <input type="text" id="height" name="height" value="<?php echo isset($patient) ? $patient->getHeight() : '' ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>Weight (kg):
                            </td>
                            <td>
                                <input type="text" id="weight" name="weight" value="<?php echo isset($patient) ? $patient->getWeight() : '' ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <td>Gender:
                            </td>
                            <td>
                                <select id="gender" name="gender">
                                    <?php
                                    foreach ($genders as $key => $gender) {
                                        echo ('<option value="' . $key . '"');
                                        echo (isset($patient) && $patient->getGender() == $key ? ' selected ' : ' ' );
                                        echo ">$gender</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Lifestyle:
                            </td>
                            <td>
                                <select id="lifestyle" name="lifestyle">
                                    <?php
                                    foreach ($lifestyles as $key => $lifestyle) {
                                        echo ('<option value="' . $key . '"');
                                        echo (isset($patient) && $patient->getLifestyle() == $key ? ' selected ' : ' ' );
                                        echo ">$lifestyle</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Diabetes type:
                            </td>
                            <td>
                                <select id="diabetesType" name="diabetesType">
                                    <?php
                                    foreach ($diabetesTypes as $key => $diabetesType) {
                                        echo ('<option value="' . $key . '"');
                                        echo (isset($patient) && $patient->getDiabetesType() == $key ? ' selected ' : ' ' );
                                        echo ">$diabetesType</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan = "2" style = "align: right;">
                                <input type = "submit" value = "Submit"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        <?php else: ?>
            <h1> You have successfully saved patient <b><?php echo $patient->getName() ?></b>!</h1>
            </br>
            <a href="<?php echo $redirectUrl . '?patient=' . $patient->getId() ?>"> Edit patient profile...</a>
        <?php endif ?>
            </br></br>
            <a href="patientsList.php"> Go to Patients list</a>
    </body>
</html>