<?php

namespace DTA\MetadataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class WorkflowDomainController extends DTABaseController {

    /** @inheritdoc */
    public $domainKey = "WorkflowDomain";
    /** @inheritdoc */
    public $domainMenu = array(
        array("caption" => "Tasks", "path" => 'home'),
        array("caption" => "Publikationsgruppen", "path" => 'home'),
        array("caption" => "Nachweise", "path" => 'home'),
        array("caption" => "Arbeitsschritte", "path" => 'home'),
        array("caption" => "Partner", "path" => 'home'),
        array("caption" => "Statistiken", "path" => 'home'),
    );

    /**
     * 
     * @return type
     * @Route("/arbeitsfluss/", name="workflowDomain")
     */
    public function indexAction() {

        $writ = new \DTA\MetadataBundle\Model\Writ();
        $writ->setNumpages(3);
        $writ->save();
        return $this->renderDomainSpecific('DTAMetadataBundle:WorkflowDomain:index.html.twig', array(
                    'persistedObject' => $writ,
                    ));
    }

}
