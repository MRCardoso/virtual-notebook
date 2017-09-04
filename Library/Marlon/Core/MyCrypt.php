<?php
/**
| ----------------------------------------------------------------------------
| Class with responsability of create hashing for passwords and tokens
| ----------------------------------------------------------------------------
* PHP version 7.1
*/

namespace Library\Marlon\Core;

class MyCrypt
{
    protected $saltPrefix = '2a';
    protected $cost = 8;
    protected $length = 22;
    
    /**
    | -------------------------------------------------------------------
    | Create a hashing for the string informed
    | -------------------------------------------------------------------
    * @param string $string
    * @param mixed $cost
    * @return string the hash created
    */
    public function hash($string, $cost = NULL)
    {
        $cost = (int) ( empty($cost) ? $this->cost : $cost );
        $salt = $this->generateSalt();
        $hashString = $this->generateHash($cost, $salt);

        return crypt($string, $hashString);
    }

    /**
    | -------------------------------------------------------------------
    | Create a Token for reset password
    | -------------------------------------------------------------------
    * @param string $string
    * @return string the token hashed
    */
    public function token($string)
    {
        $salt = $this->generateSalt();
        return hash('sha256', bin2hex(join('', [$salt, $string, time()])));
    }

    /**
    | -------------------------------------------------------------------
    | Validate if the string is equal to the hashing previous save
    | -------------------------------------------------------------------
    * @param string $string
    * @return bool
    */
    public function check($string, $hash) {
		return (bool) ( crypt($string, $hash) === $hash );
	}

    /**
    | -------------------------------------------------------------------
    | Generate a random salt
    | -------------------------------------------------------------------
    * @return string
    */
    private function generateSalt()
    {
        $seed = uniqid(mt_rand(), true);

        $salt = base64_encode($seed);
        $salt = str_replace('+', '.', $salt);

        return substr($salt, 0, $this->length);
    }

    /**
    | -------------------------------------------------------------------
    | Build a hash string for crypt()
    | -------------------------------------------------------------------
    * @param  integer $cost The hashing cost
    * @param  string $salt  The salt
    * @return string
    */
    private function generateHash($cost, $salt) {
		return sprintf('$%s$%02d$%s$', $this->saltPrefix, $cost, $salt);
	}
}