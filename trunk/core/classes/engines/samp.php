<?php

class samp {
	public $connectionType = "udp://";
	private $socket;
	private $address;
	private $port;


	public function initiate($socket, $address, $port) {

		$this->socket 	= $socket;
		$this->address 	= $address;
		$this->port	 	= $port;

	}


	public function get_data() {
		global $language;

		/* Request data */
		$info = $this->get_info();

		if(!$info) return false;

		$rules = $this->get_rules();
		$players = $this->get_players();

		$return = array(
			'general' => array(
				'online_players' => $info['online_players'],
				'maximum_online_players' => $info['maximum_online_players'],
				'map' => $info['map']
			),

			'players' => array(),

			'details' => array(
				'description' => $info['gamemode'],
				'server_version' => $info['hostname']
			)
		); 

		foreach($rules as $rule) {
			$return['details'][] = array(
					'name' => 'Rule:' . $rule['rulename'],
					'value' => $rule['value']
				);
		}

		foreach($players as $player) {
			$return['players'][] = array(
					'name' => $player['name'],
					'score' => $player['score']
				);
		}

		return $return;
	}


	private function request($type) {

		/* Split the ip */
		$ip = explode('.', gethostbyname($this->address));

		/* Generate the packet */
		$data = 'SAMP';
		$data .= chr($ip[0]);
		$data .= chr($ip[1]);
		$data .= chr($ip[2]);
		$data .= chr($ip[3]);
		$data .= chr($this->port & 0xFF);
		$data .= chr($this->port >> 8 & 0xFF); 
		$data .= $type;

		/* Sending the data to the server */
		fwrite($this->socket, $data); 

	}


	private function get_info() {

		/* Initiate the variable where we store the informations */
		$data = array();

		/* Send the request for the specific information you want to receive */
		$this->request('i');

		/* Read the first bytes */
		$header = fread($this->socket, 4);
		if($header != 'SAMP') return false;
		fread($this->socket, 7);

		/* Start reading and storing the data */
		$data['password'] 				= $this->convert_ascii(fread($this->socket, 1), 1);
		$data['online_players']			= $this->convert_ascii(fread($this->socket, 2), 2) / 10;
		$data['maximum_online_players']	= $this->convert_ascii(fread($this->socket, 2), 2) / 10;
		$data['hostname_length']		= $this->convert_ascii(fread($this->socket, 4), 4);
		$data['hostname']				= fread($this->socket, $data['hostname_length']);
		@$data['gamemode_length']		= $this->convert_ascii(fread($this->socket, 4), 4);
		@$data['gamemode']				= ($data['gamemode_length'] != '0000') ? fread($this->socket, $data['gamemode_length']) : false;
		@$data['map_length']			= ($data['gamemode'] != false) ? $this->convert_ascii(fread($this->socket, 4), 4) : false;
		@$data['map']					= ($data['map_length'] != false) ? fread($this->socket, $data['map_length']) : false;
		
		return $data;
	}

	private function get_rules() {

		/* Initiate the variable where we store the informations */
		$data = array();

		/* Send the request for the specific information you want to receive */
		$this->request('r');

		/* Read the first bytes, nothing important */
		fread($this->socket, 11);

		/* Start reading and storing the data */
		$rule_count = intval($this->convert_ascii(fread($this->socket, 2), 2));
		$rule_count = ($rule_count > 10) ? substr($rule_count, 0, 1) : $rule_count;

		for($i = 1; $i <= $rule_count; $i++) {
			@$data[$i]['rulename_length']	= $this->convert_ascii(fread($this->socket, 1), 1);
			if(intval($data[$i]['rulename_length']) != 0) {
				$data[$i]['rulename']			= fread($this->socket, $data[$i]['rulename_length']);
				$data[$i]['value_length']		= $this->convert_ascii(fread($this->socket, 1), 1);
				$data[$i]['value']				= fread($this->socket, $data[$i]['value_length']);
			} else unset($data[$i]);
			unset($data[$i]['rulename_length']);
			unset($data[$i]['value_length']);
		}

		return $data;
	}

	private function get_players() {

		/* Initiate the variable where we store the informations */
		$data = array();

		/* Send the request for the specific information you want to receive */
		$this->request('c');

		/* Read the first bytes, nothing important */
		fread($this->socket, 11);

		/* Start reading and storing the data */
		$player_count = intval($this->convert_ascii(fread($this->socket, 2), 2));
		
		for($i = 1; $i <= $player_count; $i++) {
			@$data[$i]['name_length'] = $this->convert_ascii(fread($this->socket, 1), 1);
			if(intval($data[$i]['name_length']) != 0) {
				$data[$i]['name'] = fread($this->socket, $data[$i]['name_length']);
				$data[$i]['score'] = $this->convert_ascii(fread($this->socket, 4), 4);
			} else {
				unset($data[$i]);
				break;
			}
			unset($data[$i]['name_length']);
		}
		
		return $data;
	}

	/* Function that can converts ascii to integers */
	private function convert_ascii($data, $length) {

		/* Initiate the result data */
		$result_data = '';

		for($i=0; $i <= $length - 1; $i++) {
			@$result_data .= ord($data[$i]);
		}

		return $result_data;

	}

}


?>