<?php


class D {

	protected static $_roles = array(
		'user' => 'User',
		'advanceduser' => 'Advanced User',
		'admin' => 'Admin',
		'superadmin' => 'Super Admin',
	);

	public static function getDb() {
		return Zend_Db_Table::getDefaultAdapter();
	}

	public static function getOptionsSex() {
		return array('1' => 'female', '0' => 'male');
	}

	public static function getOptionsSalutation() {
		return array('1' => 'Mrs', '0' => 'Mr');
	}

	public static function getOptionsCountry() {
		return array(
				'de' => 'Germany',
				'at' => 'Austria',
				'ch' => 'Switzerland',
				'pl' => 'Poland',
				'ru' => 'Russia',
				'fr' => 'France',
				'it' => 'Italy',
				'uk' => 'United Kingdom',
				'be' => 'Belgium',
				'dk' => 'Danmark',
		);
	}

	public static function getOptionsZipCodeRadius() {
		return array(
			'0' => '0 km',
			'5' => '5 km',
			'10' => '10 km',
			'25' => '25 km',
			'50' => '50 km',
			'75' => '75 km',
			'100' => '100 km',
			'150' => '150 km',
			'200' => '200 km',
			'250' => '250 km',
		);
	}

	public static function getOptionsAge($choose = false) {
		$array = array();
		if ($choose) $array[0] = $choose;
		for ($i = 16; $i <= 90; $i++) {
			$array[$i] = $i;
		}
		return $array;
	}

	public static function getOptionsBodyHeight($choose = false) {
		$array = array();
		if ($choose) $array[0] = $choose;
		for ($i = 150; $i <= 230; $i++) {
			$array[$i] = $i . ' cm';
		}
		return $array;
	}

	public static function getOptionsClothingSize($choose = false) {
		$array = array();
		if ($choose) $array[0] = $choose;
		for ($i = 32; $i <= 54; $i=$i+2) {
			$array[$i] = $i;
		}
		for ($i = 94; $i <= 102; $i=$i+4) {
			$array[$i] = $i;
		}
		return $array;
	}

	public static function getOptionsChest($choose = false) {
		$array = array();
		if ($choose) $array[0] = $choose;
		for ($i = 70; $i <= 132; $i++) {
			$array[$i] = $i . ' cm';
		}
		return $array;
	}

	public static function getOptionsWaist($choose = false) {
		$array = array();
		if ($choose) $array[0] = $choose;
		for ($i = 50; $i <= 115; $i++) {
			$array[$i] = $i . ' cm';
		}
		return $array;
	}

	public static function getOptionsHip($choose = false) {
		$array = array();
		if ($choose) $array[0] = $choose;
		for ($i = 70; $i <= 142; $i++) {
			$array[$i] = $i . ' cm';
		}
		return $array;
	}

	public static function getOptionsShoeSize($choose = false) {
		$array = array();
		if ($choose) $array[0] = $choose;
		for ($i = 32; $i <= 49; $i++) {
			$array[$i] = $i;
		}
		return $array;
	}

	public static function getOptionsHairColor($choose = false) {
		$array = array();
		if ($choose) $array[0] = $choose;
		return $array = array_merge($array, array(
				'blond' => 'blond',
				'dark blond' => 'dark blond',
				'brown' => 'brown',
				'red' => 'red',
				'black' => 'black',
				'grey' => 'grey',
				'white' => 'white',
		));
	}

	public static function getOptionsEyeColor($choose = false) {
		$array = array();
		if ($choose) $array[0] = $choose;
		return $array = array_merge($array, array(
				'grey' => 'grey',
				'blue' => 'blue',
				'green' => 'green',
				'brown' => 'brown',
				'black' => 'black',
		));
	}

	public static function getOptionsCupSize($choose = false) {
		$array = array();
		if ($choose) $array[0] = $choose;
		for ($i = 65; $i <= 85; $i=$i+5) {
			$array[$i . ' A'] = $i . ' A';
		}
		for ($i = 65; $i <= 85; $i=$i+5) {
			$array[$i . ' B'] = $i . ' B';
		}
		for ($i = 65; $i <= 90; $i=$i+5) {
			$array[$i . ' C'] = $i . ' C';
		}
		for ($i = 70; $i <= 90; $i=$i+5) {
			$array[$i . ' D'] = $i . ' D';
		}
		for ($i = 70; $i <= 90; $i=$i+5) {
			$array[$i . ' DD'] = $i . ' DD';
		}
		for ($i = 70; $i <= 90; $i=$i+5) {
			$array[$i . ' E'] = $i . ' E';
		}
		for ($i = 70; $i <= 90; $i=$i+5) {
			$array[$i . ' F'] = $i . ' F';
		}
		return $array;
	}

}
