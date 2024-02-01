<?php

namespace App\Service;

class RegenPassword
{
   public function createPassword(): string
   {
      // Caractères autorisés pour le mot de passe
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*-_';

      // Longueur du tableau de caractères
      $charLength = strlen($characters);

      // Initialiser le mot de passe
      $password = '';

      // Générer le mot de passe aléatoire
      for ($i = 0; $i < 8; $i++) {
          $password .= $characters[random_int(0, $charLength - 1)];
      }

      return $password;
   }
}