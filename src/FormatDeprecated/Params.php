<?php

namespace Gsdk\FormatDeprecated;

use Gsdk\FormatDeprecated as Format;

class Params {

	private array $params = [];
	private array $data = [];

	private static function formatParam($value, $options) {
		if (!is_scalar($value))
			$value = serialize($value);

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
				$value = call_user_func([$options['enum'], 'getLabel'], $value);
				break;
			case 'array':
				$value = ($options['array'][$value] ?? null);
				break;
			case 'price':
				$value = Format::number($value, Format::PRICE_FORMAT)
					. (isset($options['currency']) ? ' <span>' . $options['currency'] . '</span>' : '');
				break;
			default:
				if (method_exists(Format::class, $options['type'])) {
					$fnParams = [$value];
					if (isset($options['format']))
						$fnParams[] = $options['format'];

					$value = call_user_func_array([Format::class, $options['type']], $fnParams);
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