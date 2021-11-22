<?php

namespace Gsdk\Form;

use Gsdk\Form\Fieldset as AbstractFieldset;

//use Api;

class Form extends AbstractFieldset {

	const METHOD_GET = 'get';
	const METHOD_POST = 'post';
	const METHOD_DELETE = 'delete';
	const METHOD_PUT = 'put';

	protected $submitted = false;
	protected $errors = [];
	protected array $options = [
		'name' => null,
		'baseParams' => [],
		'method' => 'post',
		'submitAction' => 'submit',
		'successMessage' => false
	];

	public static function extend($type, $class) {
		ServiceManager::extend($type, $class);
	}

	public static function registerNamespace($namespace) {
		ServiceManager::registerNamespace($namespace);
	}

	public static function __callStatic(string $name, array $arguments) {
		$form = new static();
		return call_user_func_array([$form, $name], $arguments);
	}

	public function __call(string $name, array $arguments) {
		$this->addElement($arguments[0], $name, $arguments[1]);
		return $this;
	}

	public function __construct($options = null) {
		if (is_string($options))
			$options = ['name' => $options];

		parent::__construct($options);

		$this->init();
	}

	protected function init() { }

	public function addElement($element, $type = null, array $options = []) {
		parent::addElement($element, $type, $options);
		$this->setSubmitted(false);
		return $this;
	}

	public function name($name): static {
		return $this->setOption('name', $name);
	}

	public function method($method): static {
		return $this->setOption('method', strtolower($method));
	}

	public function setData($data) {
		if (!$this->isSubmitted())
			parent::setData($data);

		return $this;
	}

	public function setSubmitted($flag) {
		$this->submitted = (bool)$flag;
		return $this;
	}

	public function isSubmitted(): bool {
		return $this->submitted;
	}

	public function isSent(): bool {
		switch ($this->method) {
			case self::METHOD_POST:
				return ('POST' == $_SERVER['REQUEST_METHOD'] && (!$this->getName() || (isset($_POST[$this->getName()]) || isset($_FILES[$this->getName()]))));
			case self::METHOD_GET:
				$data = ($this->getName() ? (isset($_GET[$this->getName()]) ? $_GET[$this->getName()] : null) : $_GET);
				return !empty($data);//('GET' == $_SERVER['REQUEST_METHOD'] && (!$this->getName() || (isset($_GET[$this->getName()]))));
		}
		return false;
	}

	public function addError($error) {
		$this->errors[] = $error;
		return $this;
	}

	public function getErrors() {
		if (!$this->isSent())
			return $this->errors;

		$errors = $this->errors;

		foreach ($this->elements as $element) {
			if (!$element->isValid())
				$errors[$element->name] = $element->getError();
		}

		return $errors;
	}

	public function renderErrors() {
		if (empty($this->errors))
			return '';

		$s = '<div class="form-errors">';
		//$s .= '<div class="label"><span>!</span> Ошибки:</div>';
		$s .= '<ul>';
		foreach ($this->errors as $v) {
			$s .= '<li>' . $v . '</li>';
		}
		$s .= '</ul>';
		$s .= '</div>';

		return $s;
	}

	public function report() {
		if ($this->successMessage) {
			return '<div class="form-success"><span>!</span>' . $this->successMessage . '</div>';
		} else {
			return $this->renderErrors();
		}
		return '';
	}

	public function isValid(): bool {
		if (!empty($this->errors))
			return false;

		return parent::isValid();
	}

	public function reset() {
		foreach ($this->elements as $element) {
			$element->reset();
		}
		$this->errors = [];
		$this->submitted = false;
	}

	public function doAction($action, $options = []) {
		$cls = __NAMESPACE__ . '\\Action\\' . ucfirst($action);
		$action = new $cls($this, $options);
		if ($action->submit())
			return true;

		return false;
	}

	public function submit($options = []) {
		return $this->doAction($this->submitAction, $options);
	}

	public function submitApi($options = []) {
		return $this->doAction('apiSubmit', $options);
	}

	public function __toString(): string {
		return $this->render();
	}

}