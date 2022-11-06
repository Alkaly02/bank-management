<?php

require './Compte.php';

class Client
{
  private $id;
  private $prenom;
  private $nom;
  private $telephone;
  private $email;
  private $adresse;

  public function __construct($prenom, $nom, $telephone, $email, $adresse)
  {
    $this->prenom = $prenom;
    $this->nom = $nom;
    $this->telephone = $telephone;
    $this->email = $email;
    $this->adresse = $adresse;
  }

  public function creer_client()
  {
    $clients = fopen("clients.txt", "a+") or die("Impossible d'ouvrir le fichier");
    $user_id = rand(0, 100000);
    $field = [
      'id' => $user_id,
      'prenom' => $this->prenom,
      "nom" => $this->nom,
      'email' => $this->email,
      'adresse' => $this->adresse,
      'telephone' => $this->telephone
    ];
    $encoded_field = json_encode($field);

    $nouveau_compte = new Compte();
    $nouveau_compte->creer_compte($user_id, rand(0, 9999999999));

    fwrite($clients, $encoded_field . PHP_EOL);
    fclose($clients);
  }

  public static function faire_depot($montant, $numero_compte): void
  {
    $comptes = file('comptes.txt');
    $nouveau_compte = fopen('comptes.txt', 'w');

    foreach ($comptes as $compte) {
      $decoded_compte = json_decode($compte);
      if ($decoded_compte->numero === $numero_compte) {
        $decoded_compte->montant += $montant;
      }
      $encoded_field = json_encode($decoded_compte);
      fwrite($nouveau_compte, $encoded_field . PHP_EOL);
    }
  }

  public static function faire_retrait($montant, $numero_compte)
  {
    $comptes = file('comptes.txt');
    $nouveau_compte = fopen('comptes.txt', 'w');

    foreach ($comptes as $compte) {
      $decoded_compte = json_decode($compte);
      if ($decoded_compte->numero == $numero_compte) {
        if (($decoded_compte->montant - $montant) >= 0) {
          echo $decoded_compte->montant;
          $decoded_compte->montant -= $montant;
        }
      }
      $encoded_field = json_encode($decoded_compte);
      fwrite($nouveau_compte, $encoded_field . PHP_EOL);
    }
    return $montant;
  }

  public function virement($montant, $numero_compte): int
  {
    return $montant;
  }

  public function voir_solde($numero_compte): float
  {

    return 2;
  }

  public function details_compte($numero_compte)
  {
  }
}

// $client_1 = new Client("Moussa", "BADJI", 758966569, 'mab@gmail.com', 'fass');
// $client_1->creer_client();
// print_r($client_1->faire_depot(100000000000000, 8623744632));

try {
  // echo Client::faire_depot(2589800000, 6874317391);
  echo Client::faire_retrait(999990000, 6874317391);
} catch (Exception $e) {
  $e->getMessage();
}
