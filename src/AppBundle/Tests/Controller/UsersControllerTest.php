<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UsersControllerTest extends WebTestCase
{
    protected static $translation;
    public static function setUpBeforeClass() {
        $kernel = static::createKernel();
        $kernel->boot();
        self::$translation = $kernel->getContainer()->get('translator');
    }

    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $create = self::$translation->trans('create');
        $edit = self::$translation->trans('edit');
        $delete = self::$translation->trans('delete');
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/users/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /users/");
        $crawler = $client->click($crawler->selectLink($create)->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton($create)->form(array(
            'appbundle_users[lastname]'  => 'LTest',
            'appbundle_users[firstname]'  => 'FTest',
            'appbundle_users[middlename]'  => 'MTest',
            'appbundle_users[birth]'  => '2017-01-01',
            'appbundle_users[tin]'  => '1231231231231231',
            'appbundle_users[snils]'  => '1231231231211',
            'appbundle_users[organization]'  => 20,
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink($edit)->link());

        $form = $crawler->selectButton($edit)->form(array(
            'appbundle_users[lastname]'  => 'LTest',
            'appbundle_users[firstname]'  => 'FTest',
            'appbundle_users[middlename]'  => 'MTest',
            'appbundle_users[birth]'  => '2017-01-01',
            'appbundle_users[tin]'  => '1231231231231231',
            'appbundle_users[snils]'  => '1231231231211',
            'appbundle_users[organization]'  => 20,
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="LTest"]')->count(), 'Missing element [value="LTest"]');

        // Delete the entity
        $client->submit($crawler->selectButton($delete)->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/', $client->getResponse()->getContent());
    }


}
