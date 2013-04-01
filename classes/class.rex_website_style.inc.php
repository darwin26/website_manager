<?php
class rex_website_style {
	protected $name;
	protected $icon;
	protected $color;

	public function __construct($name, $icon, $color) {
		$this->name = $name;
		$this->icon = $icon;
		$this->color = $color;
	}

	public function getName() {
		return $this->name;
	}

	public function getIcon() {
		return $this->icon;
	}

	public function getIconUrl() {
		global $REX;

		return '../' . $REX['MEDIA_ADDON_DIR'] . '/website_manager/' . $this->icon;
	}

	public function getColor() {
		return $this->color;
	}
}
