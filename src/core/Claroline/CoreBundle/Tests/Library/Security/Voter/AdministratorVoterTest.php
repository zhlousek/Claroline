<?php

namespace Claroline\CoreBundle\Library\Security\Voter;

use Claroline\CoreBundle\Library\Testing\FunctionalTestCase;
use Claroline\CoreBundle\Library\Security\Acl\ClassIdentity;
use Claroline\CoreBundle\Library\Security\PlatformRoles;

class AdministratorVoterTest extends FunctionalTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->loadUserFixture();
    }

    public function testAdministrorIsAlwaysGranted()
    {
        $admin = $this->getFixtureReference('user/admin');

        $this->logUser($admin);
        $security = $this->getSecurityContext();

        $this->assertTrue($security->isGranted(PlatformRoles::ADMIN));
        $this->assertTrue($security->isGranted(array('ROLE_FOO', 'ROLE_BAR')));
        $this->assertTrue($security->isGranted('VIEW', new \stdClass()));
        $this->assertTrue($security->isGranted('VIEW', ClassIdentity::fromDomainClass(__CLASS__)));
    }
}