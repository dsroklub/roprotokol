<?php
$pw=$argv[1];
$hpw= '{SHA}' . base64_encode(sha1($pw, TRUE));
echo "$pw => $hpw\n";
