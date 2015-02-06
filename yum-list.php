<?php
include('Net/SSH2.php');
include('Crypt/RSA.php');
$server=$_GET['server'];
unlink("output/$server.txt");
unlink("output/$server.details");

$ssh = new Net_SSH2($server);
$key = new Crypt_RSA();
//$privatekey=".ssh/id_rsa";
//$key->loadKey(file_get_contents($privatekey));
$key->loadKey("ADD KEY HERE");

if (!$ssh->login('root', $key)) {
    $fh=fopen("output/$server.txt","w");
    fwrite($fh,"255,NO,-1");
    fclose($fh);
    echo "255,NO,-1";
    exit;
}
$out = $ssh->exec('yum list updates');
$pattern = '/(Loaded|Loading|Repository|Excluding|Finished|Skipping|\ \*|(\ )+\:)/im';
$rows = array();
$lines = explode("\n", $out);
foreach ($lines as $key => $value) {
    if (!preg_match($pattern, $value)) {
        $rows[] = $value;
    }
}
$output = implode("\n", $rows);
$anyOutput = strlen(ltrim(rtrim(($output))));
$ret = $ssh->getExitStatus();
$fh=fopen("output/$server.txt","w");
$fd=fopen("output/$server.details","w");
if ($anyOutput > 0) {
	$c = substr_count( $output, "\n" );
	fwrite($fh,"$ret,YES,$c");
	echo "$ret,YES,$c";
	fwrite($fd,$output);
} else {
	fwrite($fh,"$ret,NO,0");
	echo "$ret,NO,0";
}	
fclose($fh);
fclose($fd);
?>
