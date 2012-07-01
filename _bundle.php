<?php

namespace Bundles\PostmarkApp;
use Bundles\SQL\SQLBundle;
use Exception;
use e;

class Bundle extends SQLBundle {

	public function _on_framework_loaded() {
		e::$environment->requireVar('postmarkapp.api');
		e::$environment->requireVar('postmarkapp.from');
	}
	
	public function sendEmail($array) {
		$email = new Mail_Postmark(e::$environment->requireVar('postmarkapp.api'), e::$environment->requireVar('postmarkapp.from'));
		
		if(!is_array($array['to']))
			$array['to'] = array($array['to']);
		
		foreach($array['to'] as $addy)
			$email = $email->addTo($addy);
		
		$array['body'] = str_replace(array("\n","\t"), '', trim($array['body']));
		
		$email->subject($array['subject'])
			->messageHtml($array['body'])
			->send();
	}

	public function inbound($test = false) {
		if($test) $input = file_get_contents(__DIR__.'/configure/test.json');
		else $input = file_get_contents('php://input');

		$source = json_decode($input);
		$data = base64_encode(serialize($source));

		$email = $this->newInbound();
		$email->from = $source->From;
		$email->data = $data;
		$email->save();

		return new Inbound_Parser($source);
	}

	public function route() {
		$email = $this->inbound(true);
		dump($email->attachments());
		e\Complete();
	}
	
}