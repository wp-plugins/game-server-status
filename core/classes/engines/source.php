<?php

class source {
	public $connectionType = "udp://";
	private $socket;
	private $address;
	private $port;
	private $unread_bytes;
	private $data;
	private $data_offset = 0;
	private $engine;
	private $challenge;
	private $return_data = array();

	/* Number equivalent to the specific engine */
	const GOLDSOURCE 	= 0;
	const SOURCE 		= 1;

	/* Source query information */
	const INFO_REQUEST 				= "\xFF\xFF\xFF\xFFTSource Engine Query\x00";
	const INFO_HEADER_SOURCE 		= 0x49;
	const INFO_HEADER_GOLDSOURCE 	= 0x6D;

	const RULES_CHALLENGE_REQUEST	= "\xFF\xFF\xFF\xFF\x56\xFF\xFF\xFF\xFF";
	const RULES_REQUEST 			= "\xFF\xFF\xFF\xFF\x56";

	const PLAYER_CHALLENGE_REQUEST	= "\xFF\xFF\xFF\xFF\x55\xFF\xFF\xFF\xFF";
	const PLAYER_REQUEST 			= "\xFF\xFF\xFF\xFF\x55";

	public function initiate($socket, $address, $port) {

		$this->socket 	= $socket;
		$this->address 	= $address;
		$this->port	 	= $port;

	}


	public function get_data() {
		global $language;

		/* Request info data */
		$this->get_info();


		if($this->engine === false) return false;

		$this->return_data['players'] = 'false';

		return $this->return_data;
	}

	private function get_info() {
		global $language;

		/* Request INFO data */
		fwrite($this->socket, self::INFO_REQUEST);
	
		/* Read the first 4 bytes because they don't mean anything at this point */
		$type = fread($this->socket, 4);

		/* Read all the remaining data */
		@$this->data = fread($this->socket, $this->get_unread_bytes());

		/* Read the header and detect the type of the server */
		$header = $this->read_byte();

		if($header == self::INFO_HEADER_SOURCE) {
			$this->engine = self::SOURCE;
		} else
		if($header == self::INFO_HEADER_GOLDSOURCE) {
			$this->engine = self::GOLDSOURCE;
		} else {
			$this->engine = false;
			return false;
		}


		/* Start parsing and storing the data */
		if($this->engine == self::SOURCE) {

			$this->store_data('details', 'Protocol', $this->read_byte());
			$this->store_data('details', 'Name', $this->read_string());
			$this->store_data('general', 'Map', $this->read_string(), 'unchecked', 'map');
			$this->store_data('details', 'Folder', $this->read_string());
			$this->store_data('details', 'Game', $this->read_string());

			$steam_app_id = $this->read_short();
			$this->store_data('details', 'Steam App ID', $steam_app_id);

			$this->store_data('general', 'Online Players', $this->read_byte(), 'user', 'online_players');
			$this->store_data('general', 'Maximum Online Players', $this->read_byte(), 'user', 'maximum_online_players');
			
			$this->store_data('details', 'Bots', $this->read_byte());

			$type = chr($this->read_byte());
			switch($type) {
				case 'd' :	$type_text = 'Dedicated';		break;
				case 'l' :	$type_text = 'Non Dedicated';	break;
				case 'p' :	$type_text = 'SourceTV';		break;
			}
			$this->store_data('details', 'Type', $type_text);

			$os = chr($this->read_byte());
			switch($os) {
				case 'l' :			$os_text = 'Linux';		break;
				case 'w' :			$os_text = 'Windows';		break;
				case 'm' || 'o' :	$os_text = 'Mac';			break;
			}
			$this->store_data('details', 'OS', $os_text);

			$visibility = ($this->read_byte()) ? 'Private' : 'Public';
			$this->store_data('details', 'Visibility', $visibility);
			
			$vac = ($this->read_byte()) ? 'Secured' : 'Unsecured';
			$this->store_data('details', 'VAC', $vac);

			/* Check for extra data for The Ship game */
			if($steam_app_id == 2400) {

				/* Parse the mode */
				$mode = $this->read_byte();
				switch($mode) {
					case '0' : $mode = 'Hunt';
					case '1' : $mode = 'Elimination';
					case '2' : $mode = 'Duel';
					case '3' : $mode = 'Deathmatch';
					case '4' : $mode = 'VIP Team';
					case '5' : $mode = 'Team Elimination';
				}

				/* Insert the extra details */
				$this->store_data('details', 'Mode', $mode);
				$this->store_data('details', 'Witnesses', $this->read_byte());
				$this->store_data('details', 'Duration', $this->read_byte());

			}

			$this->store_data('details', 'Version', $this->read_string());

			/* Check to see if there is more data to read */
			if(strlen(substr($this->data, $this->data_offset)) > 0) {

				/* Check for extra data flags */
				$extra = $this->read_byte();

				/* Game Port */
				if($extra & 0x80) {

					$this->store_data('details', 'Game Port', $this->read_short());

				}

				/* Steam Id */
				if($extra & 10) {

					$this->read_unsigned_long();

				}

				/* Spectator data */
				if($extra & 0x40) {

					$this->store_data('details', 'Specator Port', $this->read_short());
					$this->store_data('details', 'Spectator name', $this->read_string());

				}

				/* Keywords / Tags */
				if($extra & 0x20) {

					/* Parse the keywords */
					$keywords = $this->read_string(); 
					$keywords = (!empty($keywords)) ? $keywords : $this->read_string();
					$keywords = substr($keywords, strpos($keywords, '@') + 1);

					$this->store_data('details', 'Tags', $keywords);

				}
			}

		} else {

			/* The IP address of the server, worthless to display */
			$this->read_string();

			$this->store_data('details', 'Name', $this->read_string());
			$this->store_data('general', 'Map', $this->read_string(), 'unchecked', 'map');
			$this->store_data('details', 'Folder', $this->read_string());
			$this->store_data('details', 'Game', $this->read_string());
			$this->store_data('general', 'Online Players', $this->read_byte(), 'user', 'online_players');
			$this->store_data('general', 'Maximum Online Players', $this->read_byte(), 'user', 'maximum_online_players');
			$this->store_data('details', 'Protocol', $this->read_byte());

			$type = chr($this->read_byte());
			switch($type) {
				case 'd' :	$type_text = 'Dedicated';		break;
				case 'l' :	$type_text = 'Non Dedicated';	break;
				case 'p' :	$type_text = 'SourceTV';		break;
			}
			$this->store_data('details', 'Type', $type_text);

			$os = chr($this->read_byte());
			switch($os) {
				case 'l' :			$os_text = 'Linux';		break;
				case 'w' :			$os_text = 'Windows';		break;
				case 'm' || 'o' :	$os_text = 'Mac';			break;
			}
			$this->store_data('details', 'OS', $os_text);

			$visibility = ($this->read_byte()) ? 'Private' : 'Public';
			$this->store_data('details', 'Visibility', $visibility);
				
			$mod = $this->read_byte();
			$mod_text = ($mod) ? 'Half-Life Mod' : 'Half-Life';
			$this->store_data('details', 'Mod', $mod_text);
		}

		$this->reset();

	}


	private function get_unread_bytes() {
		$status = stream_get_meta_data($this->socket);
		$this->unread_bytes = $status['unread_bytes'];

		return $this->unread_bytes;
	}

	private function reset() {

		$this->unread_bytes = 0;
		$this->data = null;
		$this->data_offset = 0;

	}

	private function store_data($where, $name, $value, $icon = 'false', $where2 = null) {

		if($where2)
			$this->return_data[$where][$where2] = array(
				'name' => $name,
				'value' => $value
			);
		else
			$this->return_data[$where][] = array(
				'name' => $name,
				'value' => $value
			);
	}

	private function read_basic($length = 1) {

		$processed_data = substr($this->data, $this->data_offset, $length);

		/* Increment the offset */
		$this->data_offset += $length;


		return $processed_data;

	}

	private function read_byte() {

		/* Read a byte from the data received */
		$processed_data = ord(substr($this->data, $this->data_offset, 1));

		/* Increment the offset */
		$this->data_offset += 1;

		return $processed_data;
	}

	private function read_short() {

		/* Read the 16bit integer */
		$processed_data = unpack('Sshort', substr($this->data, $this->data_offset, 2));

		/* Increment the offset */
		$this->data_offset += 2;

		return $processed_data['short'];
	}

	private function read_long() {

		/* Read the 32bit integer */
		$processed_data = unpack('Llong', substr($this->data, $this->data_offset, 4));

		/* Increment the offset */
		$this->data_offset += 4;

		return $processed_data['long'];
	}

	private function read_unsigned_long() {

		$processed_data = UnPack( 'Vlong', substr($this->data, $this->data_offset, 4));
		
		/* Increment the offset */
		$this->data_offset += 4;

		return $processed_data['long'];
	}


	private function read_float() {

		/* Read the 32bit float */
		$processed_data = unpack('ffloat', substr($this->data, $this->data_offset, 4));

		/* Increment the offset */
		$this->data_offset += 4;

		return $processed_data['float'];
	}

	private function read_string($delimiter = "\x00") {

		/* Get the position of the delimiter */
		$position = strpos($this->data, $delimiter, $this->data_offset);

		/* Get the string until we detect the delimiter position */
		$processed_data = substr($this->data, $this->data_offset, $position - $this->data_offset);
		//var_dump($position);
		/* Increment the offset */
		$this->data_offset += $position - $this->data_offset + 1; 

		return $processed_data;
	}


}


?>