<?php

namespace App\Model;

class Newsletter
{
   private string $subject;

   private string $title;

   private string $body;

   /**
    * Get the value of subject
    */ 
   public function getSubject()
   {
      return $this->subject;
   }

   /**
    * Set the value of subject
    *
    * @return  self
    */ 
   public function setSubject(string $subject)
   {
      $this->subject = $subject;

      return $this;
   }

   /**
    * Get the value of body
    */ 
   public function getBody(): string
   {
      return $this->body;
   }

   /**
    * Set the value of body
    *
    * @return  self
    */ 
   public function setBody(string $body)
   {
      $this->body = $body;

      return $this;
   }

   /**
    * Get the value of title
    */ 
   public function getTitle()
   {
      return $this->title;
   }

   /**
    * Set the value of title
    *
    * @return  self
    */ 
   public function setTitle($title)
   {
      $this->title = $title;

      return $this;
   }
}