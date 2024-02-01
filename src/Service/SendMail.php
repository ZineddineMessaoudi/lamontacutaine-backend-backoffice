<?php

namespace App\Service;

use App\Entity\User;
use App\Model\newsletter;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


class SendMail
{
   private $mailer;
   private $email;
   private $flash;

   /**
    * Constructs a new instance of the class.
    *
    * @param MailerInterface $mailer The mailer service.
    * @param Email $email The email object.
    * @param FlashBagInterface $flash The flash bag service.
    */
   public function __construct(MailerInterface $mailer, Email $email, FlashBagInterface $flash)
   {
      $this->mailer = $mailer;
      $this->email = $email;
      $this->flash = $flash;
   }

   /**
    * Sends a newsletter to a list of users.
    *
    * @param Newsletter $newsletter The newsletter object.
    * @param array $users The list of users to send the newsletter to.
    * @throws \Exception If there was an error sending the email.
    */
   public function sendNewsletter(Newsletter $newsletter, array $users): void
   {
      // Create an array of email addresses from the users
      $toAddresses = array_map(function ($email) {
         return new Address($email);
      }, $users);

      try {
         // Set up the email with the necessary information
         $this->email->from('Associationlamontacutaine@gmail.com')
            ->to('Associationlamontacutaine@gmail.com')
            ->bcc(...$toAddresses)
            ->subject($newsletter->getSubject())
            ->text($newsletter->getTitle() . $newsletter->getBody())
            ->html('<h2>' . $newsletter->getTitle() . '</h2><p>' . $newsletter->getBody() . '</p>');

         // Send the email
         $this->mailer->send($this->email);

         // Display success message
         $this->flash->add('success', 'La newsletter a bien été envoyée');
      } catch (\Exception $e) {
         // Display error message and throw exception
         $this->flash->add('danger', 'La newsletter n\'a pas pu être envoyée');
         throw new \Exception('Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
      }
   }

   public function sendEmailFromUser(User $user, string $subject, string $body)
   {
      try {
         $this->email->from('Associationlamontacutaine@gmail.com')
            ->to('Associationlamontacutaine@gmail.com')
            ->subject($subject)
            ->text($body . 'Envoyé par' . $user->getFirstname() . ' ' . $user->getLastname() . ' (' . $user->getEmail() . ')')
            ->html('<h2>' . $subject . '</h2><p>' . $body . '</p><p>Envoyé par ' . $user->getFirstname() . ' ' . $user->getLastname() . ' (' . $user->getEmail() . ')</p>');

         $this->mailer->send($this->email);
      } catch (\Exception $e) {
         throw new \Exception('Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
      }
   }

   public function sendCustomEmail(string $to, string $subject, string $body)
   {
      try {
         $this->email->from('Associationlamontacutaine@gmail.com')
            ->to($to)
            ->subject($subject)
            ->text($body)
            ->html('<h2>' . $subject . '</h2><p>' . $body . '</p>');

         $this->mailer->send($this->email);
      } catch (\Exception $e) {
         throw new \Exception('Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
      }
   }
}
