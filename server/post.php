<?php
echo 'hello';
file_put_contents('text.txt', implode(', ', $_REQUEST) );
?>
