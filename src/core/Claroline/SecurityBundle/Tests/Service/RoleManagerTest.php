<?php

namespace Claroline\SecurityBundle\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Claroline\SecurityBundle\Entity\Role;

class RoleManagerTest extends WebTestCase
{
    private $client;
    private $roleManager;
    
    public function setUp()
    {
        $this->client = self::createClient();
        $this->roleManager = $this->client
            ->getContainer()
            ->get("claroline.security.role_manager");
        $this->client->beginTransaction();
    }
    
    public function tearDown()
    {
        $this->client->rollback();
    }
    
    public function testANewRoleCanbeCreatedAndRetrieved()
    {
        $role = new Role();
        $role->setName('ROLE_TEST');
        $this->roleManager->create($role);
        
        $retrievedRole = $this->roleManager->getRole('ROLE_TEST');
        
        $this->assertEquals($role, $retrievedRole);
    }
    
    public function testCreateARoleWithInvalidNameThrowsAnException()
    {
        $this->setExpectedException('Claroline\SecurityBundle\Service\Exception\RoleException');
        
        $role = new Role();
        $role->setName('');
        
        $this->roleManager->create($role);
    }
    
    public function testARoleCantBeDuplicated()
    {
        $this->setExpectedException('Claroline\SecurityBundle\Service\Exception\RoleException');
        
        $firstRole = new Role();
        $firstRole->setName('ROLE_TEST');
        $secondRole = new Role();
        $secondRole->setName('ROLE_TEST');
        
        $this->roleManager->create($firstRole);
        $this->roleManager->create($secondRole);
        
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $roles = $em->getReposistory('Claroline\SecurityBundle\Entity\Role')->findAll();        
        $this->assertEquals(1, count($roles));
    }
    
    public function testGetRoleDoesntCreateANonExistentRoleIfNotSpecified()
    {
        $role = $this->roleManager->getRole('ROLE_NON_EXISTANT');
        $this->assertNull($role);
    }
    
    public function testGetRoleCreatesANewRoleIfSpecifiedAnIfRoleDoesntExist()
    {
        $role = $this->roleManager->getRole(
            'ROLE_NON_EXISTANT', 
            RoleManager::CREATE_IF_NOT_EXISTS
        );
        
        $this->assertInstanceOf('Claroline\SecurityBundle\Entity\Role', $role);
        $this->assertEquals('ROLE_NON_EXISTANT', $role->getName());
        $this->assertEquals('ROLE_NON_EXISTANT', $role->getRole());
    }
}