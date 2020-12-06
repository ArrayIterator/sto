<?php
$error = $error ?? error_get_last();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Internal Error</title>
</head>
<body>
<?php print_r($error); ?>
</body>
</html>