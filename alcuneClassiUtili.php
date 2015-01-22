<?php
// funzioni genereli per il login e monitoraggio dell'user
class usereFunction {
	public $result; // variabile dei risultati
	public $user;
	public $dati;
	public $idCliente;
	public $nomecliente;
	// funzione per il monitoraggio
	private function monitorUser($user) {
		$data = date('d.m.y - H:i');
		// inseriamo il count
		$query = new exe_query; 
		$query->execute("UPDATE user_reg SET last_connect = '$data' WHERE id = '$user'");	
	}
	// funzione per controllo del login
	public function loginUserOk() {
	 if(@$_SESSION['usrlogIN'] != '') {
			$this->monitorUser($_SESSION['usrlogIN']);
			$this->result = true;
			$select = mysql_query("SELECT * FROM user_reg WHERE email = '$_SESSION[usrlogIN]'");
			$ar = mysql_fetch_array($select);
			$this->nomecliente = $ar['name']." ".$ar['surname'];
			$this->idCliente = $ar['id'];
			// preleviamo la sigla della provincia per utilit&agrave;
			$selecPro =	mysql_query("SELECT * FROM istat_province WHERE codice_provincia = '$ar[shipping_pro]'"); $arPro = mysql_fetch_array($selecPro);
			// creiamo array con tutti i valori generali
			
		
			$ip =  str_replace(".","",$_SERVER['REMOTE_ADDR'].date('Ymd'));			// nel caso si sia loggato e aveva dei prodotti in db aggiorniamoli con il suo id
			mysql_query("UPDATE carrello_prv SET id_client = '$ar[id]' WHERE id_client = '$ip'");
			
			$arry = array(
						"id" => $ar['id'],
						"name" => $ar['name'],
						"surname" => $ar['surname'],
						"email" => $ar['email'], 
						"phone" => $ar['phone'],
						"city" => $ar['shipping_city'],
						"provincia" => $ar['shipping_pro'],
						"cap" => $ar['shipping_cap'], 
						"reg" => $ar['shipping_reg'],
						"adress" => $ar['shipping_adress'],
						"siglaProv" => $arPro['sigla_provincia'],
						"psw" => $_SESSION['pswlogIN'],
						"comuneCode" => $ar['shipping_city']);
			$this->dati = $arry;
			$this->user = $ar['id'];
			
			
		} else {
		$this->user = str_replace(".","",$_SERVER['REMOTE_ADDR'].date('Ymd'));
		$this->result = false;
		}
		
	}
	// funzione per il login
	public function loginUser($user, $password, $iduser, $idcart) {	
	// 1° controllo nel db la presenza dell'user attraverso corrispondenza della mail
		$password = md5($password);
		$query = mysql_query("SELECT * FROM user_reg WHERE email = '$user' AND password = '$password'"); $ar = mysql_fetch_array($query); 
		$idDCLiente = $ar['id'];
		if(mysql_num_rows($query) != 0) {// se sono presenti righe,		
		$_SESSION['pswlogIN'] = $password;
		$_SESSION['usrlogIN'] = $user;
		$this->result = true;
		$userID = str_replace(".","",$_SERVER['REMOTE_ADDR'].date('Ymd'));
		mysql_query("UPDATE carrello_prv SET id_client = '$idDCLiente' WHERE id_client = '$userID'");
		
		} else {
		$this->result = false;
		}
		$this->monitorUser($ar['id']);
		// se in quel momento l'utente ha un carrello aggiorniamo il suo id nel carrello provvisiorio
		//mysql_query("UPDATE carrello_prv SET id_client = '$idDCLiente' WHERE orderID_prv = '$idcart'");
	}	
	// funzione per l'iscrizzione nel sito
	public function userWrite($user) {
	// registriamo l'utente nel database
	$password = md5($user['password']);
	$data = date('d.m.y');
	$query= mysql_query("INSERT INTO user_reg SET 
										name = '$user[name]', 
										surname = '$user[surname]', 
										sex = '$user[sex]',
										phone = '$user[phone]',
										email = '$user[email]',
										ins_date = '$data',
										promo_point = 0,
										total_order = 0,
										total_bild = 0,
										password = '$password',
										mailFriend = '$user[mailFriend]'");
	// a fine registrazione invia la mail
	$oggetto = "Iscrizione a ".constPnl::SITE_NAME;
	$messaggio = "Benvenuto $user[name] $user[surname], <br/>
					grazie per esserti registrato su ".constPnl::SITE_NAME."<br/>
					conserva questa mail per effettuare l'accesso all'area riservata del sito con i seguenti dati:</p>
					<p>User (email): $user[email]<br/>
					Password: $user[password]</p>";
	// ora può inviare la mail
	$mail = new sendMail; $mail->invioMail($messaggio, $oggetto, $user['email']);
	if($query == '') {
		$this->result = false;
	} else { $this->result = true; }
	$this->loginUser($user['email'], $user['password'], "$user[email]", $user['idCart']);// registriamoci ed effettuiamo il login
	}
	// funzione per la verifica dell'esistenza del username
	public function verifyUser($username) {
	$query = new exe_query; $query->execute("SELECT * FROM user_reg WHERE username = '$username'");
		if($query->numRow != 0) {
		$this->result = false;
		} else { 
		$this->result = true;
		}
	}
	
}

// ########################


// CLASSE PER FORMARE I CONTENUTI
class getContent extends option{
	public $result;
	public $where;
	public $page;
	public $result_a = array();
	
	function getTopList($sez = '') {
	
	if($sez != '') { $ez = '&& sez = '.$sez; }	 else { $ez = ''; }	
	
	$query = mysql_query("SELECT * FROM annunc WHERE toplist = 1 && lista = 0 $ez ORDER BY id DESC");
	
	
	if(@mysql_num_rows($query) == 0) {
		
		$this->result .= '';
	} else {
			  
	while($ar = mysql_fetch_array($query)) {
			$this->result .= '
        	  <a href="view_annuncio.php?id='.$ar['id'].'"><div id="inner_top_slider_in"><img src="img/annunci/'.$ar['directory'].'/200X_'.$ar['img'].'" width="150px"  /></div></a>
        	 ';
		}
	}
	}
	
	private function getList($sez = '') {
			
	if($sez != '') { $ez = '&& sez = '.$sez; }	  else { $ez = ''; }		
			
	$query = mysql_query("SELECT * FROM annunc WHERE lista = 1 && toplist = 0 $ez ORDER BY id DESC");
	
		
		if(@mysql_num_rows($query) == 0) {
		
		$this->result .= '';
	} else {
		
	while($ar = mysql_fetch_array($query)) {
			$this->result .= '
        	<a href="view_annuncio.php?id='.$ar['id'].'"><div id="inner_slider_in"><img src="img/annunci/'.$ar['directory'].'/200X_'.$ar['img'].'" width="120px"  /></div></a>';
		}
	}
	}
	
	function getAnnunc($sez = '') {
	// impostiamo il luogo
	
	if(!isset($_SESSION['where'])) {
		$this->where = 'Tutta Italia';
	}
			
	if($sez != '') { $ez = '&& sez = '.$sez; }		 else { $ez = ''; }	
			
	$query = mysql_query("SELECT * FROM annunc WHERE lista = 0 && toplist = 0 $ez ORDER BY id DESC");
	
	if(@mysql_num_rows($query) == 0) {
		
		$this->result .= '<center>Nessun annuncio trovato</center>';
	} else {
	while($ar = mysql_fetch_array($query)) {
			$this->result .= '<a href="view_annuncio.php?id='.$ar['id'].'"><div id="annunci_in_on">
        <table width="95%" border="0" cellpadding="4" cellspacing="4">
  <tr>
    <td width="46%" class="in_an_tb"><img src="img/annunci/'.$ar['directory'].'/100X_'.$ar['img'].'" /></td>
    <td width="54%" valign="top"><p><span class="redMiniTitle">'.$ar['titolo'].'
</span></p>
      <p>'.$this->tagliaStringa($ar['messaggio'],100).'</p></td>
  </tr>
</table>
		</div></a>';
		}
	}
	}
	
	// calcolo delle pagine
	
	public function calqPageView($val, $start, $curPgm, $sez, $href, $curpage) {
		$limit = $val;
		$start = $start + $val;
		if($sez != '') { $ez = '&& sez = '.$sez; }		 else { $ez = ''; }	
		$x = mysql_query("SELECT * FROM annunc WHERE lista = 0 && toplist = 0 $ez");
		@$numRow = mysql_num_rows($x); 
		@$totalPage = $numRow / $val; //troviamo il numero di pagine esatte da creare	 RISOLTO TOGLIENdO L'ARROTONDAMENTO??	
		$pg = $curPgm+1;
		$pgmeno = $curPgm-1;
		$startmeno = $start - $val;		
		if(!$numRow) {
		$this->page = ""; // se non ci sono pagine
		} else if($curPgm == 0 && $pg > $totalPage ) {
		$this->page = ""; // se non ci sono pagine
		} else if($pg > $totalPage++ ) { // torna indietro
		$this->page = "<a href=\"index.php?rq=$pgw&strt=$startmeno&pag=$pgmeno\" ><B><<</B></a> - Pagina: $pg di $totalPage ";
		} else if($curPgm < 0 || $curPgm == 0) { // pagina successiva
		$go = 0;
		for($count = 1; $count <= $totalPage; $count++) {
			if($curpage == $pg) {
			$this->page .= "<a href=\"$href&init=$go&curpage=$pg#product\"><p id=\"pageNumber\" class=\"current\"> $pg </p></a>";
		} else { $this->page .= "<a href=\"$href&init=$go&curpage=$pg#product\"><p id=\"pageNumber\" > $pg </p></a>"; }
		$pg++;
		$go = $go+$start;
			}
		} 
	}
	
	private function getAnnuncioComplete($id) {
		
		$ar = mysql_fetch_array(mysql_query("SELECT * FROM annunc WHERE id = '$id'"));
		// 1° dato
		$this->result_a['titolo'] = $ar['titolo'];
		$this->result_a['messaggio'] = $ar['messaggio'];
		$this->result_a['bigfoto'] = '<a href="img/annunci/'.$ar['directory'].'/800X_'.$ar['img'].'" rel="lightbox[a]"><img src="img/annunci/'.$ar['directory'].'/400X_'.$ar['img'].'" width="350px" /></a>';
		$this->result_a['directory'] = $ar['directory'];
		
		// ora recuperiamo le foto
		$query = mysql_query("SELECT * FROM image WHERE id_user = '$id'");
		while($arimg = mysql_fetch_array($query)) {
			$this->result_a['img'] .= "<div class=\"foto_thumb\"><a style=\"cursor:pointer; \"onclick=\"loadImage('$arimg[image]') \"><img src=\"img/annunci/$ar[directory]/100X_$arimg[image]\" /></a></div>";
		}
		
	}
	
}


?>