<?php

class CustomImageSlider extends CustomExternalFields {

	public function getTypeName() {
		return 'Image Slider';
	}

	public function getClassPath() {
		return 'Cms.DataFields.Types.CustomImageSlider';
	}

	public function getDbDataType() {
		return null;
	}
	
	public function noLabel() {
		return true;
	}

	public function getInputCode($data = null) {
		return $this->getDataCode('/Cms/bits/imageslider/input', $data);
	}

	public function getOutputCode($data = null) {
		return $this->getDataCode('/Cms/bits/imageslider/output', $data);
	}

	public function getValidation() {
		
	}

	public function deleteData(CustomExternalFieldData &$data) {

	}

	public function insertData(CustomExternalFieldData &$data) {

	}

	public function selectData(CustomExternalFieldData &$data) {
		$path = $this->getUploadDir();
		$dir = new Folder($path);
		$files = $dir->getFiles();
		foreach ($files as $key => $file) {
			$files[$key] = new GalleryImage($path, $file->name());
		}
		$data->setData(null, $files);
	}

	public function updateData(CustomExternalFieldData &$data) {

	}
	
	protected function getUploadDir() {
		return 'client/upload/' . URI::clean($this->position->getName()) . '/' . $this->getFieldName() . '/';
	}

}

class GalleryImage {
	
	private $file;
	private $thumb;
	
	public function __construct($dir, $file) {
		$this->file = $dir.$file;
		$this->thumb = $dir.'thumb/'.$file;
	}
	
	public function getFile() {
		return URI::build($this->file);
	}
	
	public function getThumbnail() {
		return URI::build($this->thumb);
	}
	
}

?>
