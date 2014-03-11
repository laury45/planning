
<?php
/** Classe gérant les sessions et l'authentification sur le site 
 * 
 */

class Auth {
  // variables concernant la session
  
  /** les messages d'erreur */
  public $msg_error; 
  // variables concernant mysql
  /** le server mysql */
  private $host; 
  /** le nom de l'utilisateur mysql */
  private $log;  
  /** la base de donnée */
  private $bdd;  
  /** le mot de pass de l'utilisateur mysql */
  private $pass; 
  /** La connexion à la base */
  private $db;
  /** le temps maxi de connexion */
  private $temps_max;

 
  /** Constructeur de la classe */
  
  function __construct($host,$bdd,$log,$pass,$timeout){
    $this->host=$host;
    $this->bdd=$bdd;
    $this->log=$log;
    $this->$pass=$pass;
    $this->navigateur=$_SERVER['HTTP_USER_AGENT'];
    $this->temps_max=$timeout;
    $this->mesg_error="";
    //on se connecte à la base de donnees
    /* ouverture de la base de donnee*/
    $this->db=mysql_connect($this->host, $this->log,$pass) or die("erreur de connexion au serveur");
    mysql_select_db($this->bdd)  or die("erreur de connexion a la base de donnees");
  }


  /** le destructeur de la classe: deconnecte de la base de donnees */
  function __destruct(){
    mysql_close();
  }

  /** function qui observe le champ msg_error */
  public function Erreur_Message(){
    return $this->msg_error;
  }

  /** méthode qui realise l'authentification d'une session et l'initialisation des variables de session*/
  
  public function AuthUser($user_input, $pass_input){
    // On selectionne dans la base le champs correspondant au nom d'utilisateur.
    if(empty($user_input) || empty($pass_input))
      {
	$this->msg_error = 'remplir tous les champs.';
	return false;
      }
    // verification du login pour eviter l'injection SQL
    $pattern="/^[a-zA-Z]([a-zA-Z0-9]{3,})$/";
    if(!preg_match($pattern,$user_input)){
      $this->msg_error = "login incorrect il doit respecter l'expression reguliere :(\"/^[a-zA-Z]([a-zA-Z]{3,})$/\")";
      return false;
    }
    // récupération des infos de la base de données
    $requete="SELECT  * FROM utilisateur WHERE login='".$user_input."';";	
    $result = mysql_query($requete);
    if (!$result){ 
      $this->msg_error = "erreur d'accès à la table utilisateur";
      return false;
    }
    if(mysql_num_rows($result) != 1){
      $this->msg_error =  'login  incorrect.';
      return false;
    }
    //l'utilisateur est bien dans la base
    $user = mysql_fetch_assoc($result);
    // On vérifie maintenant si le mot de passe est bon
    $pass_input=md5($pass_input);
    if($pass_input!=$user['password']){
      $this->msg_error = 'mot de passe incorrect';
      return false;
    }
    
    // stockage dans la session des infos de la base de données
    $_SESSION['login']=$user_input;
    /*$_SESSION['prec_navigateur']=$user['navigateur'];
    $_SESSION['prec_duree']=$user['duree'];
    $_SESSION['prec_connect']=$user['derniere_connect'];
    $_SESSION['prec_session_id']=$user['session_id'];*/
    
    if($user['etat']==1){
      $this->msg_error = 'Une session a votre nom est encore active ou vous vous êtes mal deconnecté';
      if($this->navigateur==$user['navigateur'])
	 $this->msg_error=$this->msg_error." sur le même navigateur";
      else $this->msg_error=$this->msg_error." sur un autre navigateur";
      return false;
      }
    
    
    // on met à jour la table utilisateur avec les infos de la nouvelle session
    $req1="derniere_connect='".time()."',";
    $req2="session_id='".session_id()."',";
    $req3="navigateur='".$this->navigateur."',";
    $req4="etat='1'";
    $requete="UPDATE utilisateur SET ".$req1." ".$req2." ".$req3." ".$req4." WHERE login='".$user_input."';";	   
    mysql_query($requete);
    if (!$result) {
      $this->msg_error = 'erreur inattendue';
      return false; 
    }	
     // stockage dans la session des nouvelles infos
    $_SESSION['navigateur']=$this->navigateur;
    $_SESSION['derniere_connect']=time();
    $_SESSION['session_id']=session_id();
    return true;
  }
  /** Méthode réinitialisation de Passwd */  
  
/** Méthode réinitialisation de Passwd */  
  public function reinitPasswd($userLogin){
    $this->msg_error = "";
    $requete="SELECT  * FROM utilisateur WHERE login='".$userLogin."';";  
    $result = mysql_query($requete);
    if (!$result){ 
      $this->msg_error = "login incorrect";
      return false;
    }
    if(mysql_num_rows($result) != 1){
      $this->msg_error = "login incorrect";
      return false;
    }
    else {
      if(!empty ($_POST['newpassword'] )){ 
        $pass=md5($_POST['newpassword']);
        $req1="password='".$pass."'"; 
        $requete="UPDATE utilisateur SET ".$req1."  WHERE login='".$userLogin."';";
        $result=mysql_query($requete);
         if (!$result) {
          $this->msg_error='Requete invalide : ' . mysql_error();
          return false;
      } 
      else {$this->msg_error = "password changé";}
    }
}
return true;
}

  /** Méthode  qui met fin à la session si celle-ci existe 
   * avec mise à jour de l'etat et de la durée de la session
   * */
  public function fin_session(){
    if(isset($_SESSION['login'])){
      $req1="etat='0',";
      $req2="duree='".$this->temps_connexion($_SESSION['login'])."'";
      $requete="UPDATE utilisateur SET ".$req1." ".$req2." WHERE login='".$_SESSION['login']."';";
      $result=mysql_query($requete);
      if (!$result) {
	die('Requete invalide : ' . mysql_error());
      } 
      // fin de la session
      $_SESSION=array();//on efface toutes les variables de la session   
      session_destroy();
    }
  }
    
  /** Calcul temps connexion */
  public function temps_connexion($login){
     return time()-$_SESSION['derniere_connect'];
  }
  
  public function get_temps_max(){
    return $this->temps_max;
  }

  }
 ?>
