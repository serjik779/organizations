<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Organizations;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Exception\Exception;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use AppBundle\Entity\Users;

class XMLParserController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        if ($request->getMethod() == "POST") {

            $file = $request->files->get('xml', null);

            if (!$this->checkFileType($file)) {
                $this->addFlash('error', 'Тип файла должен быть XML');
                return $this->redirectToRoute('app_import');
            }

            if (!$file->move($this->getParameter('path_to_xml'), $file->getClientOriginalName())) {
                $this->addFlash('error', 'Не возможно переместить файл');
                return $this->redirectToRoute('app_import');
            }

            $encoders = array(new XmlEncoder());
            $normalizers = array(new ObjectNormalizer());

            $serializer = new Serializer($normalizers, $encoders);
            $xml = file_get_contents($this->getParameter('path_to_xml') . '/' . $file->getClientOriginalName());

            if (!$this->parseXml($xml, $serializer)) {
                $this->addFlash('error', 'Не возможно обработать файл');
                $this->redirectToRoute('app_import');
            }

            $this->addFlash('success', 'Импорт прошел успешно');
        }

        return $this->render('AppBundle:XMLParser:index.html.twig');
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    public function checkFileType($file) {
        if ((isset($file)) && ($file->getClientMimeType() == 'text/xml')) {
            return true;
        }
        return false;
    }

    /**
     * @param string $xml
     * @param Serializer $serializer
     * @return bool
     */
    public function parseXml(string $xml, Serializer $serializer) {
        try {
            $organizationsXML = $serializer->decode($xml, 'xml');
            $em = $this->getDoctrine()->getManager();
            $stop = false;
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
                    if (!is_array($userXML)) {
                        $userXML = $organizationXML['user'];
                        $stop = true;
                    }
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
                    if ($stop) {
                        break;
                    }
                }
            }
            $em->flush();
        } catch (Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return false;
        }
        return true;
    }

}
