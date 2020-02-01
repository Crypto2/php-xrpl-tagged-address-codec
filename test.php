<?php
require('./vendor/autoload.php');

print "The output addresses should match the examples table at https://github.com/xrp-community/standards-drafts/issues/6\n";

$x = new Crypto2\XAddress();
$testaddr = 'rGWrZyQqhTp9Xu7G5Pkayo7bXjH4k4QYpf';
$tags = array(null,0,1,2,32,276,65591,16781933,4294967294,4294967295);
foreach ($tags as $dt) {
	$addr = $x->Encode($testaddr, $dt, false);
	print "x-address: ".$addr."\n";
	$dec = $x->Decode($addr);
	if ($dec['address'] != $testaddr || $dec['dest_tag'] !== $dt) {
		print "Encoding mismatch!\n";
		print_r($dec);
	}
}
foreach ($tags as $dt) {
	$addr = $x->Encode($testaddr, $dt, true);
	print "x-address: ".$addr."\n";
	$dec = $x->Decode($addr);
	if ($dec['address'] != $testaddr || $dec['dest_tag'] !== $dt) {
		print "Encoding mismatch!\n";
		print_r($dec);
	}
}
