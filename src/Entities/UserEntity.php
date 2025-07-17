<?php
namespace Idp\Entities;

use League\OAuth2\Server\Entities\UserEntityInterface;

class UserEntity implements UserEntityInterface
{
    protected $id;
    protected $email;
    protected $firstname;
    protected $lastname;
    protected $title;
    protected $customerid;

    public function __construct($id, $email, $firstname, $lastname, $title, $customerid) {
        $this->id    = $id;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->title = $title;
        $this->customerid = $customerid;
    }

    public function getIdentifier() {
        return $this->id;
    }

    public function getFirstname() {
        return $this->firstname;
    }

    public function getLastname() {
        return $this->lastname;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getCustomerId() {
      return $this->customerid;
    }

}
