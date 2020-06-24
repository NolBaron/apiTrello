<?php
	class TrelloModel extends CI_Model  {
		
		// KEY AND TOKEN FOR MIXFINITY LOGIN
		private $key = "1d4b387fb2430ef71522ba93cbd72e88";  
		private $token = "9b7277b4b5b0835d6431e0d0bec7618fcb0e35a38594c92ec672cc5a1e36bd26";
		private $base = 'https://api.trello.com/1';
 

		public function getBoards(){
			$result = $this->makeRequest("/organizations/mixfinity/boards");
			return $result;
		}

		public function getCards($f_id){
			//echo "https://api.trello.com/1/boards/".$f_id."?lists=open&list_fields=name&fields=name,desc";
			$result = $this->makeRequest("/boards/".$f_id."/cards");
			return $result;
		}

		public function getLists($f_id){
			$result = $this->makeRequest("/boards/".$f_id."/lists?cards=open&list_fields=name&fields=name,desc");
			return $result;
		}

		public function getCardsByList($f_id){
			$result = $this->makeRequest("/boards/".$f_id."/lists?cards=open&card_fields=name,shortUrl&fields=name");
			return $result;			
		}

		public function getBacklog($f_id){
			$result = $this->getLists($f_id);
			return $result[0]->id;
		}

		public function createBoard($data){
			if(is_array($data)){
				$data["idOrganization"] = "mixfinity";
				$data["prefs_permissionLevel"] = "org";
				$result = $this->makeRequest("/boards", $data);

				$current_board = $result->id;

			

				return $result->id;
			} else {
				return 0;
			}
		}

		public function createChecklist($first_list, $project_id){
			/*$data = array(
				"name" => "Controleren",
				"desc" => "Deze punten moeten gecontroleerd worden",
				"idList" => $first_list
			);
			$card_id = $this->createCard($data);*/
			$categories = $this->db->get_where('check_categories', array("active" => "1"))->result();



			foreach($categories as $c){

				$data = array(
					"name" => $c->name . " controleren",
					"desc" => "Deze punten moeten gecontroleerd worden",
					"idList" => $first_list
				);
				$card_id = $this->createCard($data);

				$data = array(
					"name" => $c->name
				);
				$checklist_id = $this->createChecklistCategory($card_id, $data);
				$items = $this->db->get_where("check_items", array(
					"active" => 1,
					"category_id" => $c->id
				))->result();
				foreach($items as $i){
					$data = array(
						"idChecklist" => $checklist_id,
						"name" => $i->name
					);	
					$this->createChecklistItem($card_id, $data);
				}
			}

			$this->db->update("projects", array("init" => 1), array("id" => $project_id));
	
		}

		private function createChecklistCategory($card_id, $data){
			$checklist_id = $this->makeRequest("/cards/" . $card_id . "/checklists", $data, "POST")->id;
			return $checklist_id;
		}

		private function createChecklistItem($card_id, $data){
			$result = $this->makeRequest("/cards/" . $card_id . "/checklist/" . $data["idChecklist"] . "/checkItem", $data, "POST");

		}

		/* Card */

		public function createCard($data){
			if(is_array($data)){
				$result = $this->makeRequest("/cards", $data);
				return $result->id;
			} else {
				return 0;
			}
		}

		public function addMember($data, $board_id){
			if(is_array($data)){

				foreach($data as $user){
					$result = $this->makeRequest("/boards/" . $board_id . "/members/" . $user["idMember"], $user, "PUT");
				}
				
			}
		}

		public function updateCard($data){
			if(is_array($data)){
				$result = $this->makeRequest("/cards/" . $data["trello_id"], $data, "PUT");
				return $result->id;
			}	else {
				return 0;
			}
		}


		public function deleteCard($f_id){

			$this->makeRequest("/cards/" . $f_id, array(), "DELETE");
			return true;

		}

		public function moveCard($card_id = "", $list_id = ""){
			if(!empty($card_id) && !empty($list_id)){
				$this->makeRequest("/cards/" . $card_id . "/idList", array("value" => $list_id), "PUT");
				return true;
			}
		}

		public function addComment($card_id = "", $comment = ""){

			if(!empty($card_id) && !empty($comment)){
				$this->makeRequest("/cards/" . $card_id . "/actions/comments", array("text" => $comment), "POST");

			}

		}

		

		private function makeRequest($input, $data = array(), $method = ""){
			$url = $this->base.$input;

			$sendData = "";

			if(is_array($data)){
				foreach($data as $key => $value){
					$sendData .= "&" . $key . "=" . $value;
				}
			}

			if(strpos($input, "?") > 0 ){
				$suffix = '&key='.$this->key.'&token='.$this->token;
			} else {
				$suffix = '?key='.$this->key.'&token='.$this->token;
			}

			$url = $url . $suffix;
	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
			curl_setopt($ch, CURLOPT_HEADER, 0);
			if(strlen($sendData) > 1){
				curl_setopt($ch, CURLOPT_POSTFIELDS, $sendData);
			}
			if($method == "PUT" || $method == "put"){
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			}
			if($method == "DELETE" || $method == "delete"){
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			}
			curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
		
			$output = curl_exec($ch);
			$request =  curl_getinfo($ch, CURLINFO_HEADER_OUT);
			$error = curl_error($ch);
			//echo $error;
			curl_close($ch);
			//echo $output;

			if(strlen($error) > 0){
				echo "De volgende fout heeft zich voorgedaan: ". $error;
				die();
			}
			try{
				return json_decode( $output );
			} catch(Exception $e){
				return $output;
			}
			
		}

		private function makeRequestAsync($input, $data = array(), $method = ""){
			$url = $this->base.$input;

			$sendData = "";

			if(is_array($data)){
				foreach($data as $key => $value){
					$sendData .= "&" . $key . "=" . $value;
				}
			}

			if(strpos($input, "?") > 0 ){
				$suffix = '&key='.$this->key.'&token='.$this->token;
			} else {
				$suffix = '?key='.$this->key.'&token='.$this->token;
			}

			$url = $url . $suffix;
	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
			curl_setopt($ch, CURLOPT_HEADER, 0);
			if(strlen($sendData) > 1){
				curl_setopt($ch, CURLOPT_POSTFIELDS, $sendData);
			}
			if($method == "PUT" || $method == "put"){
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
			}
			if($method == "DELETE" || $method == "delete"){
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			}
			curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
		
			return $ch;
		
			
		}



	} 


?>	 