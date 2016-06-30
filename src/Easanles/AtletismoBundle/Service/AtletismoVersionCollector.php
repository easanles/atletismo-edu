<?php

# Copyright (c) 2016 Eduardo Alonso Sanlés
# Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php

namespace Easanles\AtletismoBundle\Service;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AtletismoVersionCollector extends DataCollector{

	/* (non-PHPdoc)
	 * @see \Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface::collect()
	 */
	public function collect(Request $request, Response $response, \Exception $exception = null) {
		$VERSION = 'v1.1';
		$ITERATION = '';
		
       $this->data = array(
       		'version' => $VERSION,
       		'iteration' => $ITERATION,
       );
	}
	
	public function getVersion(){
		return $this->data['version'];
	}
	
	public function getIteration(){
		return $this->data['iteration'];
	}
	
	/* (non-PHPdoc)
	 * @see \Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface::getName()
	 */
	public function getName() {
       return 'App Version';
	}

}