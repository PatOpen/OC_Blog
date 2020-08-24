<?php


namespace OC_Blog\Tools;


class HtmlResponse {
	protected $twig;
	protected $statusCode;
	protected $data;
	protected $twigPath;

	public function __construct($statusCode, $twigPath, $data)
	{
		$this->statusCode = $statusCode;
		$this->data = $data;
		$this->twigPath = $twigPath;
	}
	public function setTwig($twig)
	{
		$this->twig = $twig;
	}

	public function addData($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function render() {
		echo $this->twig($this->twigPath, $this->data);
	}
}


