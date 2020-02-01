<?php
namespace Crypto2;

use InvalidArgumentException;

/**
 * @package      Crypto2\php-xrpl-tagged-address-codec
 * @author       Crypto2
 * @copyright    2020 Crypto2
 * @license      http://www.opensource.org/licenses/MIT    The MIT License
 * @link         https://github.com/Crypto2/php-xrpl-tagged-address-codec
 */

class XAddress {
	private $base58 = null;

	public function __construct() {
		$this->base58 = new \StephenHill\Base58('rpshnaf39wBUDNEGHJKLM4PQRST7VWXYZ2bcdeCg65jkm8oFqi1tuvAxyz');
	}

	public function Decode($addr) {
		$bin = $this->base58->decode($addr);
		if (strlen($bin) != 35) {
			throw new InvalidArgumentException('Invalid X-address');
		}		
		/* Check for mainnet or testnet prefix */
		if ($bin[0] == chr(0x05) && $bin[1] == chr(0x44)) {
			$testnet = false;
		} else if ($bin[0] == chr(0x04) && $bin[1] == chr(0x93)) {
			$testnet = true;
		} else {
			throw new InvalidArgumentException('Invalid X-address');
		}
		/* Check address checksum */
		$hash1 = hash('sha256', substr($bin, 0, 31), TRUE);
		$hash2 = hash('sha256', $hash1, TRUE);
		if (substr($hash2, 0, 4) != substr($bin, -4)) {
			throw new InvalidArgumentException('Invalid X-address checksum');
		}

		$flags = $bin[22];
		if ($flags == chr(0x01)) {
			/* 32-bit Destination Tag */
			return array(
				'address' => $this->EncodeRippleAddress(substr($bin, 2, 20)),
				'dest_tag' => unpack('V', substr($bin, 23, 4))[1],
				'testnet' => $testnet,
			);
		} else if ($flags == chr(0x00)) {
			/* No Destination Tag */
			return array(
				'address' => $this->EncodeRippleAddress(substr($bin, 2, 20)),
				'dest_tag' => null,
				'testnet' => $testnet,
			);
		} else {
			throw new InvalidArgumentException('Unsupported flags');
		}
	}

	public function Encode($addr, $dest_tag = null, $testnet = false) {
		if ($dest_tag !== null && ($dest_tag < 0 || $dest_tag > 4294967295)) {
			throw new InvalidArgumentException('Invalid destination tag');
		}
		$bin = $this->base58->decode($addr);
		if (strlen($bin) != 25 || $bin[0] != 0) {
			throw new InvalidArgumentException('Invalid Ripple address');
		}
		/* Check address checksum */
		$hash1 = hash('sha256', substr($bin, 0, 21), TRUE);
		$hash2 = hash('sha256', $hash1, TRUE);
		if (substr($hash2, 0, 4) != substr($bin, -4)) {
			throw new InvalidArgumentException('Invalid Ripple address checksum');
		}

		if (!$testnet) {
			$prefix = chr(0x05).chr(0x44);
		} else {
			$prefix = chr(0x04).chr(0x93);
		}
		$xaddr = $prefix;
		$xaddr .= substr($bin, 1, 20); // Account ID
		if ($dest_tag === null) {
			$xaddr .= str_repeat(chr(0), 9); // 9 = 1 byte of flags + 8 bytes of empty tag
		} else {
			$xaddr .= chr(1); //TAG_32 flag
			$xaddr .= pack('VV', $dest_tag, 0); // dest tag + 4 zero bytes
		}

		/* Calculate address checksum */
		$hash1 = hash('sha256', $xaddr, TRUE);
		$hash2 = hash('sha256', $hash1, TRUE);
		$xaddr .= substr($hash2, 0, 4);

		return $this->base58->encode($xaddr);
	}

	private function EncodeRippleAddress($account) {
		if (strlen($account) != 20) {
			throw new InvalidArgumentException('Invalid account ID');
		}

		$xaddr = chr(0); // Ripple version byte
		$xaddr .= $account; // Account ID

		/* Calculate address checksum */
		$hash1 = hash('sha256', $xaddr, TRUE);
		$hash2 = hash('sha256', $hash1, TRUE);
		$xaddr .= substr($hash2, 0, 4);

		return $this->base58->encode($xaddr);
	}
};
