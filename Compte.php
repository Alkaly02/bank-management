<?php

class Compte
{
  private $id;
  private $numero;
  private $montant = 0;
  private $date_creation;
  private $heure_creation;

  public static function creer_compte($user_id, $numero)
  {
    $comptes = fopen("comptes.txt", "a+") or die("Impossible d'ouvrir le fichier");
    $field = [
      'id' => rand(0, 100000) * 0.5, 
      'user_id' => $user_id, 
      'numero' => $numero, 
      "date_creation" => date('Y-m-d'), 
      'heure_creation' => time(), 
      'montant' => 0
    ];
    $encoded_field = json_encode($field);

    fwrite($comptes, $encoded_field . PHP_EOL);
    fclose($comptes);
  }

  public static function liste_comptes()
  {
    $comptes = file('comptes.txt');
    return $comptes;
  }

  public function details_compte($numero_compte)
  {
  }

  public function bloquer_compte($numero_compte)
  {
  }
}

$compte = new Compte();

// print_r($compte->liste_comptes());

?>
