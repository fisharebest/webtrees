<?php namespace Fisharebest\Localization;

/**
 * Class Translation - a set of translated messages, such as a .MO file.
 *
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright (c) 2015 Greg Roach
 * @license   GPLv3+
 */
class Translation {
	// Constants for processing .MO files
	const MO_MAGIC_LITTLE_ENDIAN = '950412de';
	const MO_MAGIC_BIG_ENDIAN = 'de120495';
	const PACK_LITTLE_ENDIAN = 'V';
	const PACK_BIG_ENDIAN = 'N';

	/** @var array An association of English -> translated messages */
	private $translations;

	/**
	 * The code for this variant.
	 *
	 * @param string $filename
	 */
	public function __construct($filename) {
		$this->translations = array();

		switch (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
		case 'csv':
			$fp = fopen($filename, 'r');
			if ($fp) {
				while (($data = fgetcsv($fp, 0, ';')) !== false) {
					$this->translations[$data[0]] = $data[1];
				}
				fclose($fp);
			}
			break;

		case 'mo':
			$fp = fopen($filename, 'rb');
			if ($fp) {
				$this->readMoFile($fp);
				fclose($fp);
			}
			break;

		case 'php':
			$this->translations = include $filename;
			break;
		}
	}

	/**
	 * The translation strings
	 *
	 * @return array
	 */
	public function asArray() {
		return $this->translations;
	}

	/**
	 * Read specific binary data (32 bit words) from a .MO file
	 *
	 * @param resource $fp
	 * @param integer  $offset
	 * @param integer  $count
	 * @param string   $pack   "N" for big-endian, "V" for little-endian
	 *
	 * @return integer[]
	 */
	private function readMoWords($fp, $offset, $count, $pack) {
		fseek($fp, $offset);

		return unpack($pack . $count, fread($fp, $count * 4));
	}

	/**
	 * Read and parse a .MO (gettext) file
	 *
	 * @link https://www.gnu.org/software/gettext/manual/html_node/MO-Files.html
	 *
	 * @param resource $fp
	 */
	private function readMoFile($fp) {
		// How is the numeric data packed in the .MO file?
		$magic = $this->readMoWords($fp, 0, 1, self::PACK_LITTLE_ENDIAN);

		switch (dechex($magic[1])) {
		case self::MO_MAGIC_LITTLE_ENDIAN:
			$pack = self::PACK_LITTLE_ENDIAN;
			break;
		case self::MO_MAGIC_BIG_ENDIAN:
			$pack = self::PACK_BIG_ENDIAN;
			break;
		default:
			// Not a valid .MO file.
			throw new \InvalidArgumentException('Invalid .MO file');
		}

		// Read the lookup tables
		list(, $number_of_strings, $offset_original, $offset_translated) = $this->readMoWords($fp, 8, 3, $pack);
		$lookup_original   = $this->readMoWords($fp, $offset_original, $number_of_strings * 2, $pack);
		$lookup_translated = $this->readMoWords($fp, $offset_translated, $number_of_strings * 2, $pack);

		// Read the strings
		for ($n = 1; $n < $number_of_strings; ++$n) {
			fseek($fp, $lookup_original[$n * 2 + 2]);
			$original = fread($fp, $lookup_original[$n * 2 + 1]);
			fseek($fp, $lookup_translated[$n * 2 + 2]);
			$translated = fread($fp, $lookup_translated[$n * 2 + 1]);
			$this->translations[$original] = $translated;
		}
	}
}
