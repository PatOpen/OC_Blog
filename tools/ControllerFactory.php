<?php


namespace OC_Blog\Tools;


use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Class ControllerFactory
 *
 * Classe Parent de tous les controleurs.
 *
 * @package OC_Blog\Tools
 */
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
	 * @var int
	 */
	private int $slug;

	/**
	 * @var array
	 */
	private array $post;


	/**
	 * ControllerFactory constructor.
	 *
	 * @param object $twig
	 * @param int $slug
	 * @param array $post
	 */
	public function __construct(object $twig,int $slug = 0, array $post = [], ConstantGlobal $constant_global){

		$this->twig = $twig;
		$this->slug = $slug;
		$this->post = $post;
		//$this->server = ( new ConstantGlobal(ServerRequest::fromGlobals()));
		$this->server = $constant_global;
}

	/**
	 * Renvoi un objet Twig.
	 *
	 * @return object
	 */
	public function getTwig(): object {
		return $this->twig;
	}

	/**
	 * Renvoi une instance de ServerRequest.
	 *
	 * Permet d'utiliser la variable global de $_SERVER['SERVER_NAME'].
	 *
	 * @return string
	 */
	public function getServer(): string {
		return $this->server->getServerName()['SERVER_NAME'];
	}

	/**
	 * Permet d'utiliser $_FILE
	 *
	 * @return array
	 */
	public function getUpFile(): array {
		return $this->server->getFile();
	}

	/**
	 * Renvoi le slug de l'url.
	 *
	 * @return int
	 */
	public function getSlug(): int {
		return $this->slug;
	}

	/**
	 * Renvoi les informations posté par l'utilisateur $_POST.
	 *
	 * @return array
	 */
	public function getPost(): array {
		return $this->post;
	}

	/**
	 * Affiche la page demandé avec Twig.
	 *
	 * @param string $twigPath
	 * @param array $data
	 */
	public function render(string $twigPath, array $data): void {
		echo $this->twig->render($twigPath, $data);
	}

	/**
	 * Redirige à l'url demandé.
	 *
	 * @param string $path
	 */
	public function redirect(string $path): void{
		header('HTTP/1.1 Moved Permanently', false, 302);
		header('Status: 302 Moved Permanently', false, 302);
		header('location: http://'.$path);
	}
}

