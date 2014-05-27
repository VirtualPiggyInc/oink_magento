<?php
/**
 * @package Oink.Services.Interfaces
 */
    interface IParentService
    {
        public function AuthenticateParent($username, $badLogin);
        public function GetChildProfiles($token);
    }
?>
