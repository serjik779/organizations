<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrganizationsControllerTest extends WebTestCase
{
    protected static $translation;
    public static function setUpBeforeClass() {
        $kernel = static::createKernel();
        $kernel->boot();
        self::$translation = $kernel->getContainer()->get('translator');
    }

    public function testCompleteScenario()
    {
        $create = self::$translation->trans('create');
        $edit = self::$translation->trans('edit');
        $delete = self::$translation->trans('delete');
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/organizations/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /organizations/");
        $crawler = $client->click($crawler->selectLink($create)->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton($create)->form(array(
            'appbundle_organizations[title]'  => 'Test',
            'appbundle_organizations[ogrn]'  => '1231231231231',
            'appbundle_organizations[oktmo]'  => '12312312312',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink($edit)->link());

        $form = $crawler->selectButton($edit)->form(array(
            'appbundle_organizations[title]'  => 'Test3',
            'appbundle_organizations[ogrn]'  => '1231231231251',
            'appbundle_organizations[oktmo]'  => '12312312352',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="Test3"]')->count(), 'Missing element [value="Foo"]');
    }


}
