<?php

/**
 * Linna App
 *
 *
 * @author Sebastian Rapetti <sebastian.rapetti@alice.it>
 * @copyright (c) 2017, Sebastian Rapetti
 * @license http://opensource.org/licenses/MIT MIT License
 *
 */

namespace App\Models;

use Linna\Mvc\Model;
use Linna\Auth\Password;
use App\Mappers\UserMapper;
use App\DomainObjects\User;

/**
 * User Model
 * 
 */
class UserModel extends Model
{
    /**
     * @var UserMapper $mapper Mapper for user object
     */
    protected $mapper;
    
    /**
     * @var Password $password Password utility class
     */
    protected $password;
    
    /**
     * Constructor
     * 
     * @param UserMapper $userMapper
     * @param Password $password
     */
    public function __construct(UserMapper $userMapper, Password $password)
    {
        parent::__construct();
        
        $this->mapper = $userMapper;
        
        $this->password = $password;
    }

    /**
     * Return all Users
     * 
     * @return array
     */
    public function getAllUsers()
    {
        return $this->mapper->getAllUsers();
    }
    
    /**
     * Enable a User
     * 
     * @param int $userId
     * 
     * @return int
     */
    public function enable(int $userId)
    {
        $user = $this->mapper->findById($userId);
        $user->active = 1;

        $this->mapper->save($user);

        $this->getUpdate = ['error' => 0];
        
        return 1;
    }
    
    /**
     * Disable a User
     * 
     * @param int $userId
     * 
     * @return int
     */
    public function disable(int $userId)
    {
        //get user
        $user = $this->mapper->findById($userId);
        
        //verify if user is root and set to disable
        if ($user->name !== 'root') {
            $user->active = 0;
        }
        
        //save
        $this->mapper->save($user);
        
        //store no error code for update view
        $this->getUpdate = ['error' => 0];
        
        return 1;
    }
    
    /**
     * Delete a User
     * 
     * @param int $userId
     * 
     * @return int
     */
    public function delete(int $userId)
    {   
        //get user
        $user = $this->mapper->findById($userId);
        
        //verify if user is root and delete
        if ($user->name !== 'root') {
            $this->mapper->delete($user);
        }

        return 1;
    }
    
    /**
     * Checks before password change
     * 
     * @param string $newPassword
     * @param string $confirmPassword
     * 
     * @return bool
     */
    protected function changePasswordChecks(string $newPassword, string $confirmPassword) :bool
    {
        //password must be not null
        if ($newPassword === null || $newPassword === '') {
            $this->getUpdate = ['error' => 2];
            return false;
        }

        //password must be equal to confirm password
        if ($newPassword !== $confirmPassword) {
            $this->getUpdate = ['error' => 1];
            return false;
        }
        
        return true;
    }
    
    /**
     * Change User password
     * 
     * @param int $userId
     * @param string $newPassword
     * @param string $confirmPassword
     * 
     * @return int
     */
    public function changePassword(int $userId, string $newPassword, string $confirmPassword)
    {
        //do password checks
        if ($this->changePasswordChecks($newPassword, $confirmPassword) === false) {
            return 0;
        }
        
        //get user
        $user = $this->mapper->findById($userId);
        //set new password
        $user->password = $this->password->hash($newPassword);
        
        //save
        $this->mapper->save($user);
        
        //store no error code for update view
        $this->getUpdate = ['error' => 0];

        return 1;
    }
    
    /**
     * Checks before modify a User
     * 
     * @param User $user
     * @param string $newName
     * 
     * @return bool
     */
    protected function modifyChecks(User $user, string $newName) : bool
    {
        //user name must be not null
        if ($newName === null || $newName === '') {
            $this->getUpdate = ['error' => 2];
            return false;
        }
        
        //search for user with new username
        $checkUser = $this->mapper->findByName($newName);
                
        //user name must be unique
        if (isset($checkUser->name) && $checkUser->name !== $user->name) {
            $this->getUpdate = ['error' => 1];
            return false;
        }
        
        return true;
    }
    
    /**
     * Modify a User
     * 
     * @param int $userId
     * @param string $newName
     * @param string $newDescription
     * 
     * @return int
     */
    public function modify(int $userId, string $newName, string $newDescription)
    {
        //get user
        $user = $this->mapper->findById($userId);
        
        //check User modify checks
        if ($this->modifyChecks($user, $newName) === false) {
            return 0;
        }
        
        //set new name
        $user->name = $newName;
        //set new description
        $user->description = $newDescription;
        
        //save
        $this->mapper->save($user);
        
        //store no error code for update view
        $this->getUpdate = ['error' => 0];

        return 1;
    }
}
