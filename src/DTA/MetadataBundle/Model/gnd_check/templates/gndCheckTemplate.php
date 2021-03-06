
public function gndExists(){
    $gnd = $this->get<?php echo ucfirst($gndColumnName);?>();
    if($gnd === null or $gnd === "")
        return null;
    if(!function_exists('curl_init')){
        return null;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://d-nb.info/gnd/$gnd/about/html");
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode;
}