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
        $translator = $this->get('translator');
        if ($request->getMethod() == "POST") {
            $file = $request->files->get('xml', null);
            if (!(isset($file)) || ($file->getClientMimeType() != 'text/xml')) {
                $this->addFlash('error', $translator->trans('type.most.xml'));
                return $this->redirectToRoute('app_import');
            }
            if (!$file->move($this->getParameter('path_to_xml'), $file->getClientOriginalName())) {
                $this->addFlash('error', $translator->trans('not.possible.move.file'));
                return $this->redirectToRoute('app_import');
            }
            $encoders = array(new XmlEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $xml = file_get_contents($this->getParameter('path_to_xml') . '/' . $file->getClientOriginalName());
            try {
                $this->get('xmlparser')->parseXml($xml, $serializer);
            } catch (Exception $e) {
                $this->addFlash('error', $translator->trans('not.possible.handle.file') . $e->getMessage());
                $this->redirectToRoute('app_import');
            }
            $this->addFlash('success', $translator->trans('import.complete'));
        }
        return $this->render('AppBundle:XMLParser:index.html.twig');
    }
}
