<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Organizations;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use AppBundle\Entity\Users;

class XMLParserController extends Controller
{
    public function indexAction(Request $request)
    {
        $xml = '<?xml version="1.0"?>
                <orgs>
                    <org displayName="ООО Краснознаменск" ogrn="9697567955367" oktmo="34586905567">
                        <user firstname="Василий" middlename="Иванович" lastname="Пупкин" inn="8947493759347894" snils="9762738648233" />
                        <user firstname="Виталий" middlename="Петрович" lastname="Швабрин" inn="8947493345457894" snils="9762345358233"  />
                    </org>
                    <org displayName="ООО Серп и молот" ogrn="9693453534747" oktmo="34585645567">
                        <user firstname="Александр" middlename="Сергеевич" lastname="Пушкин" inn="8947345345354894" snils="7667877898233"  />
                    </org>
                </orgs>';
        if ($request->getMethod() == "POST") {

            $file = $request->files->get('xml');

            $file->move(
                $this->getParameter('path_to_xml'),
                $file->getClientOriginalName()
            );
            dump($file);

            $encoders = array(new XmlEncoder());
            $normalizers = array(new ObjectNormalizer());

            $serializer = new Serializer($normalizers, $encoders);
            $xml = file_get_contents($this->getParameter('path_to_xml') . '/' . $file->getClientOriginalName());
            $organizationsXML = $serializer->decode($xml, 'xml');

            $em = $this->getDoctrine()->getManager();

            foreach ($organizationsXML['org'] as $organizationXML) {
                $organization = $em->getRepository(Organizations::class)->findOneBy(array(
                    'ogrn' => $organizationXML['@ogrn']
                ));
                if (!$organization) {
                    $organization = new Organizations();
                }
                $organization->setOgrn($organizationXML['@ogrn'])
                    ->setTitle($organizationXML['@displayName'])
                    ->setOktmo($organizationXML['@oktmo']);
                $em->persist($organization);
                foreach ($organizationXML['user'] as $key => $userXML) {
                    if (is_numeric($key)) {
                        $user = $em->getRepository(Users::class)->findOneBy(array(
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
                        $em->persist($user);
                    } else {
                        $user = $em->getRepository(Users::class)->findOneBy(array(
                            'snils' => $organizationXML['user']["@snils"]
                        ));
                        if (!$user) {
                            $user = new Users();
                        }
                        $user->setFirstname($organizationXML['user']['@firstname'])
                            ->setLastname($organizationXML['user']['@lastname'])
                            ->setMiddlename($organizationXML['user']['@middlename'])
                            ->setOrganization($organization)
                            ->setSnils($organizationXML['user']['@snils'])
                            ->setTin($organizationXML['user']['@inn']);
                        $em->persist($user);
                        break;
                    }
                }
                $em->flush();
            }
            $this->addFlash('success', 'Импорт прошел успешно');
        }

        return $this->render('AppBundle:XMLParser:index.html.twig', array(
            // ...
        ));
    }

}
