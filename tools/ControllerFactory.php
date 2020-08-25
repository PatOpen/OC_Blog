<?php


namespace OC_Blog\Tools;


use GuzzleHttp\Psr7\ServerRequest;

class ControllerFactory {

	/**
	 * @var mixed
	 */
	private $server;

	/**
	 * @var object
	 */
	private object $twig;

	/**
	 * @var array
	 */
	private array $data;

	/**
	 * @var int
	 */
	private int $slug;

	/**
	 * @var array
	 */
	private array $post;

	public function __construct(object $twig,int $slug = 0, array $post = []){

		$this->twig = $twig;
		$this->slug = $slug;
		$this->post = $post;
		$this->server = ( new ConstantGlobal(ServerRequest::fromGlobals()) )->getServerName()['SERVER_NAME'];

}

	public function getTwig(): object {
		return $this->twig;
	}

	public function getServer(): string {
		return $this->server;
	}

	public function getSlug(): int {
		return $this->slug;
	}

	public function getPost(): array {
		return $this->post;
	}

	public function addData($key, $value) {
		$this->data[$key] = $value;
	}

	public function render($twigPath, $data) {
		echo $this->twig->rrender($twigPath, $data);
	}


}
