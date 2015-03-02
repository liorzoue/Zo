<?php


	function bdd_init() {
		global $bdd;

		try
		{
		    $pdo = new PDO('mysql:host='.$bdd['host'].';dbname='.$bdd['dbname'].'', $bdd['username'], $bdd['password']);
		    return $pdo;
		}
		catch(Exception $e)
		{
		    echo 'Erreur : '.$e->getMessage().'<br />';
		    echo 'N° : '.$e->getCode();

		    return 'erreur n'.$e->getCode();
		}
	}

	function bdd_check() {
		try {
			
			$pdo = bdd_init();

			$sth = $pdo->prepare('SHOW TABLES LIKE \'user\'');
			$sth->execute();
			$result = $sth->fetchAll();

			if (count($result) == 1) {
				return true;
			} else {
				return false;
			}

		} catch (Exception $e) {
			throw new Exception("[check_bdd]:".$e->getMessage(), 1);
		}
	}

	function bdd_create_new_bdd() {
		try {
			if(bdd_check()) {
				throw new Exception("[bdd_create_new_bdd]: Creation déjà effectuée.", 1);
			}
		} catch (Exception $e) {
			throw new Exception("[bdd_create_new_bdd]:".$e->getMessage(), 1);
		}

		$pdo = bdd_init();

		/* 
			Creation de la base 
			Script SQL
		*/

	}

?>