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

  public static function virement($montant, $envoyeur_numero_compte, $receveur_numero_compte): string
  {
    $operations = fopen("operations.txt", "a+") or die("Impossible d'ouvrir ce fichier");
    $comptes = file('comptes.txt');
    $nouveau_compte = fopen('comptes.txt', 'w');
    // $clients = file('clients.txt');

    $field = [
      'id' => round(0, 2598499000),
      'from' => $envoyeur_numero_compte,
      'to' => $receveur_numero_compte,
      'date_virement' => date('Y-m-d'),
      'heure_virement' => time(),
      'type' => 'virement'
    ];

    // !checking if the envoyeur_numero_compte exist
    foreach ($comptes as $compte) {
      $sender_client = json_decode($compte);
      if ($sender_client->numero === $envoyeur_numero_compte) {

        $sender_balance = $sender_client->montant;
        // !checking if the receveur_numero_compte exist
        foreach ($comptes as $compte) {
          $receveur_client = json_decode($compte);
          if ($receveur_client->numero === $receveur_numero_compte) {

            // ! let's check if the sender balance is > to $montant
            if (($sender_balance - $montant) <= 0) {
              throw new Exception("Operation échouée, vous n'avez pas assez d'argent sur votre compte!");
            }

            $sender_client->montant -= $montant;
            $receveur_client->montant += $montant;

            foreach ($comptes as $key => $compte) {
              $decoded_compte = json_decode($compte);

              if ($decoded_compte->id === $sender_client->id) {
                $comptes[$key] = json_encode($sender_client);
              }

              if ($decoded_compte->id === $receveur_client->id) {
                $comptes[$key] = json_encode($receveur_client);
              }
            }

            foreach ($comptes as $compte) {
              $encoded_field = $compte;
              fwrite($nouveau_compte, $encoded_field . PHP_EOL);
            }

            fwrite($operations, json_encode($field) . PHP_EOL);

            fclose($comptes);
            fclose($operations);
            
            return "Virement effectué!";
          }
        }
        throw new Exception("Ce compte receveur n'existe pas!");
      }
    }
    throw new Exception("Cet utilisateur qui essai d'envoyer n'existe pas!");
  }

  public static function voir_solde($numero_compte): string
  {
    $comptes = file('comptes.txt');

    foreach ($comptes as $compte) {
      $decoded_compte = json_decode($compte);
      if ($decoded_compte->numero === $numero_compte) {
        return "Votre solde est de: " . $decoded_compte->montant . PHP_EOL;
      }
    }

    return "Compte introuvable!";
  }

  public function details_compte($numero_compte)
  {
  }
}

// $client_1 = new Client("Moussa", "BADJI", 758966569, 'mab@gmail.com', 'Colobane');
// $client_1->creer_client();
// print_r($client_1->faire_depot(100000000000000, 8623744632));

try {
  // echo Client::faire_depot(2589, 8858375260);
  // echo Client::faire_retrait(999990000, 6874317391);
  // echo Client::voir_solde(6945104914);
  echo Client::virement(5000, 7185192164, 8858375260);
} catch (Exception $e) {
  echo $e->getMessage();
}
