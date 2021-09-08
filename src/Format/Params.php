<?php

namespace Gsdk\Format;

class Params {

	private array $params = [];
	private array $data = [];

	private static function formatParam($value, $options) {
		switch ($options['type']) {
			case 'text':
				break;
			case 'email':
				$options['href'] = 'mailto:' . $value;
				break;
			case 'address':
				$value = '<address>' . $value . '</address>';
				break;
			case 'phone':
				$options['href'] = 'tel:' . $value;
				//$value = preg_replace('', '', $value);
				break;
			case 'url':
				$options['href'] = $value;
				break;
			case 'enum':
				$value = self::formatEnum($value, $options['enum']);
				break;
			case 'array':
				$value = ($options['array'][$value] ?? null);
				break;
			case 'price':
				$value = self::formatNumber($value, self::PRICE_FORMAT)
					. ' <span>' . CURRENCY::getLabel(CURRENCY::getDefault()) . '</span>';
				break;
			default:
				$fn = 'format' . ucfirst($options['type']);
				if (method_exists(__NAMESPACE__, $fn)) {
					$fnParams = [$value];
					if (isset($options['format']))
						$fnParams[] = $options['format'];

					$value = call_user_func_array(['Format', $fn], $fnParams);
				}
		}

		if (!$value)
			return $value;
		else if (isset($options['href']))
			return '<a href="' . $options['href'] . '">' . $value . '</a>';
		else
			return $value;
	}

	public function __construct(array $params, array $data = null) {
		$this->params = $params;
		if ($data)
			$this->data = $data;
	}

	public function setData(array $data) {
		$this->data = $data;
	}

	public function __toString() {
		static $defaultParam = ['type' => 'text', 'label' => ''];

		$html = '';
		foreach ($this->params as $k => $v) {
			if (!is_array($v))
				$v = ['label' => $v];

			$v = array_merge($defaultParam, $v);
			if (!isset($this->data[$k]))
				continue;

			$value = $this->data[$k];
			if (null === $value || '' === $value)
				continue;

			$value = self::formatParam($value, $v);
			if (null === $value)
				continue;

			$html .= '<tr class="' . $k . '"><th>' . $v['label'] . '</th><td>' . $value . '</td></tr>';
		}
		return $html ? '<table class="table-params">' . $html . '</table>' : '';
	}

}