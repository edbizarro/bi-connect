<?php

include dirname(__DIR__) . '/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Bi\Connect\ComscoreConnect;

$sheet_location_server = '/examples/sheets';
$valid_file            = false;
if ($_FILES) {
    if ($_FILES['sheet']['name']) {
        if (! $_FILES['sheet']['error']) {
            $new_file_name = strtolower($_FILES['sheet']['tmp_name']);
            if ($_FILES['sheet']['size'] > 102400000) {
                $valid_file = false;
                echo 'Size Erro.';
            } else {
                $valid_file = true;
            }
        } else {
            $valid_file = false;
            echo $_FILES['sheet']['error'];
        }
        if ($valid_file) {
            $comscore = new ComscoreConnect('upload');

            $params = [];

            $response = $comscore->getInfosByFile(
                $_FILES['sheet'],
                $params
            );

            echo '<h1> Response SheetInfo</h1>';
            var_dump($response);
            die();

            if (is_null($response)) {
                echo 'Error getting the report' . PHP_EOL;
            }
        }
    }
}
?>
<form action="ComscoreConnectExample.php" method="post" enctype="multipart/form-data">
	Arquivo: <input type="file" name="sheet" size="25" />
  <br/>
	<input type="submit" name="submit" value="Submit" />
</form>
