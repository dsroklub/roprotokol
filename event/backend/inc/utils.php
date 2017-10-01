function sanestring($s,$slash=false) {
   $allowedchars=".:;@abcdefghijklmnopqrstuvwxyzæøåABCDEFGHIJKLMNOPQRSTUVWXYZÆØÅ01234567890_-#";
   if ($slash) {
       $allowedchars.="/";
   }
    $r="";
    for ($i=0; $i<100 && $i < strlen($s) ;$i++) {
        $c=$s[$i];
        if (strpos($allowedchars,$c)>=0){
            $r.=$c;
        }        
    }
    return $r;
}
