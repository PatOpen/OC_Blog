<?php


namespace OC_Blog\Tools;


use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ConstantGlobal
 *
 * Permet de renvoyer les constantes globals $_SERVER.
 *
 * @package OC_Blog\Tools
 */
class ConstantGlobal {

	/**
	 * @var ServerRequestInterface
	 */
	private ServerRequestInterface $request;

	public function __construct( ServerRequestInterface $request ) {
		$this->request = $request;
	}

	/**
	 * Retourne la constante global $SERVER['REQUEST_URI'].
	 *
	 * @return string
	 */
	public function getServerUri(): string {
		return $this->request->getUri()->getPath();
	}

	/**
	 * Retourne la constante global $SERVER.
	 *
	 * @return array
	 */
	public function getServerName(): array {
		return $this->request->getServerParams();
	}

	/**
	 * Récupère la variable $_FILE.
	 *
	 * @return array
	 */
	public function getFile(): array {
		return $this->request->getUploadedFiles();
	}

	/**
	 * Récupère la variable $_POST.
	 *
	 * @return array
	 */
	public function getPost(): array {
		return $this->request->getParsedBody();
	}
}