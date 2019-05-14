<?php
/**
 * User: Nicu Neculache
 * Date: 13.05.2019
 * Time: 12:33
 */

class Password
{
    private $id = 0;
    private $id_user = 0;
    private $last_data_change;
    private $link_website = "";
    private $name_website = "";
    private $username = "";
    private $password = "";

    /**
     * @return string
     */
    public function getNameWebsite()
    {
        return $this->name_website;
    }

    /**
     * @return string
     */
    public function getLinkWebsite()
    {
        return $this->link_website;
    }

    /**
     * @return mixed
     */
    public function getLastDataChange()
    {
        return $this->last_data_change;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function get_all(){

    }
}