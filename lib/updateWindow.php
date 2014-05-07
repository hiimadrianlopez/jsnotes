<?php
file_put_contents("../notes/".$_POST["name"].".txt", stripslashes($_POST["content"]));
?>