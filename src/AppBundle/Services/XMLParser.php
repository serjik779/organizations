<?php

namespace AppBundle\Services;


use AppBundle\Entity\Organizations;
use AppBundle\Entity\Users;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Acl\Exception\Exception;
use Symfony\Component\Serializer\Serializer;

class XMLParser
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * XMLParser constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $xml
     * @param Serializer $serializer
     * @return bool
     */
    public function parseXml($xml, Serializer $serializer) {
        $organizationsXML = $serializer->decode($xml, 'xml');
        $stop = false;
        foreach ($organizationsXML['org'] as $organizationXML) {
            $organization = $this->em->getRepository(Organizations::class)->findOneBy(array(
                'ogrn' => $organizationXML['@ogrn']
            ));
            if (!$organization) {
                $organization = new Organizations();
            }
            $organization->setOgrn($organizationXML['@ogrn'])
                ->setTitle($organizationXML['@displayName'])
                ->setOktmo($organizationXML['@oktmo']);
            $this->em->persist($organization);
            if (!isset($organizationXML['user'])) {
                continue;
            }
            foreach ($organizationXML['user'] as $key => $userXML) {
                if (!is_array($userXML)) {
                    $userXML = $organizationXML['user'];
                    $stop = true;
                }
                $user = $this->em->getRepository(Users::class)->findOneBy(array(
                    'snils' => $userXML["@snils"]
                ));
                if (!$user) {
                    $user = new Users();
                }
                $user->setFirstname($userXML['@firstname'])
                    ->setLastname($userXML['@lastname'])
                    ->setMiddlename($userXML['@middlename'])
                    ->setOrganization($organization)
                    ->setSnils($userXML['@snils'])
                    ->setTin($userXML['@inn']);
                $this->em->persist($user);
                if ($stop) {
                    break;
                }
            }
        }
        $this->em->flush();
        return true;
    }
}