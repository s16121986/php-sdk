<?php

namespace Gsdk\Form;

use Gsdk\Form\Fieldset as AbstractFieldset;

//use Api;

class Form extends AbstractFieldset {

	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_DELETE = 'DELETE';
	const METHOD_PUT = 'PUT';

	protected $submitted = false;
	protected $errors = [];
	protected $options = [
		'name' => null,
		'baseParams' => [],
		'method' => 'POST',
		'submitAction' => 'submit',
		'successMessage' => false
	];

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

	public function setName($method) {
		return $this->setOption('name', $method);
	}

	public function setMethod($method) {
		return $this->setOption('method', $method);
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

	public function isSubmitted() {
		return $this->submitted;
	}

	public function isSent() {
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
		foreach ($this->_errors as $v) {
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

	public function reset() {
		foreach ($this->elements as $element) {
			$element->reset();
		}
		$this->errors = [];
		$this->submitted = false;
	}

	public function doAction($action, $options = []) {
		$cls = 'Form\\Action\\' . ucfirst($action);
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

}