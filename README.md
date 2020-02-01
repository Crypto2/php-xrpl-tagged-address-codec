# XRPL Tagged Address Codec for PHP

#### Encode and Decode an XRPL account address and destination tag to/from X-formatted (tagged) address.

Destination tags provide a way for exchanges, payment processors, corporates or entities which accept incoming payments, escrows, checks and similar transcations to use a single receiving wallet while being able to disambiguate incoming transactions by instructing the senders to include a destination tag.

This package allows encoding and decoding from an XRPL address and destination tag to / from 'Tagged Addresses', containing both the destination account address and tag in one string. This way users can simply copy-paste the string, eliminating possible user error when copying / entering a numeric destination tag.

#### Hopefully all exchanges, wallets & other software using destination tags will implement this address codec. A migration period will be required to allow users to enter both address formats.

#### The website [https://xrpaddress.info](https://xrpaddress.info/) is available for users, exchanges and developers to provide some context and best practices.

## Install

Install with composer: crypto2/php-xrpl-tagged-address-codec

## Use

### Encoding
require('./vendor/autoload.php');

$x = new Crypto2\XAddress();<br />
//Main Net - No Tag<br />
$address = $x->Encode('rGWrZyQqhTp9Xu7G5Pkayo7bXjH4k4QYpf', null, false);<br />
//Main Net - With Tag<br />
$address = $x->Encode('rGWrZyQqhTp9Xu7G5Pkayo7bXjH4k4QYpf', 12345, false);<br />
//Test Net - With Tag<br />
$address = $x->Encode('rGWrZyQqhTp9Xu7G5Pkayo7bXjH4k4QYpf', 12345, true);

### Decoding
require('./vendor/autoload.php');

$x = new Crypto2\XAddress();<br />
$tmp = $x->Decode('XVLhHMPHU98es4dbozjVtdWzVrDjtV8xvjGQTYPiAx6gwDC');

Return is an array with keys: address, dest_tag, testnet<br />
address: The Ripple address<br />
dest_tag: The Destination Tag or null if one wasn't set in the tag<br />
testnet: true if it is a testnet address, false otherwise

## Credits

This readme based on the one for the Java/NPM package at https://github.com/xrp-community/xrpl-tagged-address-codec

This concept is based on the [concept](https://github.com/xrp-community/standards-drafts/issues/6) from [@nbougalis](https://github.com/nbougalis)
 
Big thanks to [@sublimator](https://github.com/sublimator) for his fiddles, ideas and fixes and [@intelliot](https://github.com/intelliot) for the idea of adding an `X` / `T` prefix for (new) address recognizability. 
