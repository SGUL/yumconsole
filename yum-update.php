<?php
include('Net/SSH2.php');
include('Crypt/RSA.php');
$server=$_GET['server'];
unlink("./output/$server.txt");
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
    die();
}
$out = $ssh->exec('yum update -y');

$ret = $ssh->getExitStatus();
$fh=fopen("output/$server.txt","w");

if ($ret==0) {
	fwrite($fh,"$ret,NO,0");
        echo "$ret,NO,0";
} else {
	fwrite($fh,"$ret,YES,-1");
	echo "$ret,YES,-1";
}	

fclose($fh);

echo $out;
?>
