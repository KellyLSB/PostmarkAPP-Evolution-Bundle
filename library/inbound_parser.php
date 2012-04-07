<?php

namespace Bundles\Postmarkapp;
use Exception;
use StdClass;
use e;

class Inbound_Parser {

	private $email;

	public function __construct(StdClass $email) {
		$this->email = $email;
	}

	public function from() {
		return (array) $this->email->FromFull;
	}

	public function subject() {
		return $this->email->Subject;
	}

	public function body() {
		$html = $this->email->HtmlBody;
		$text = $this->email->TextBody;

		if(empty($html) && !empty($text))
			return $text;
		else if(!empty($html) && empty($text))
			return $html;
		else if(!empty($html) && !empty($text))
			return array('html' => $html, 'text' => $text);
		else
			return 'No message body.';
	}

	public function hash() {
		return $this->email->MailboxHash;
	}

	public function attachments() {
		$files = (array) $this->email->Attachments;
		foreach($files as &$file)
			$file = (array) $file;

		return $files;
	}

}