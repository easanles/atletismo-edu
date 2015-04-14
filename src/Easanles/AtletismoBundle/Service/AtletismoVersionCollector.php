<?php

namespace Easanles\AtletismoBundle\Service;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AtletismoVersionCollector extends DataCollector{

	/* (non-PHPdoc)
	 * @see \Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface::collect()
	 */
	public function collect(Request $request, Response $response, \Exception $exception = null) {
       $this->data = array(
       		'version' => 'dev 1',
       );
	}
	
	public function getVersion(){
		return $this->data['version'];
	}

	/* (non-PHPdoc)
	 * @see \Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface::getName()
	 */
	public function getName() {
       return 'App Version';
	}

}