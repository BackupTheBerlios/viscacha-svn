<?php
/**
 * This is the field data frontend.
 *
 * @package		Cms
 * @subpackage	Modules
 * @author		Matthias Mohr
 * @since 		1.0
 */
class FieldDataPages {

	protected $position;
	protected $baseUri;
	protected $mainFields;

	public function  __construct($position, $baseUri, array $mainFields) {
		if (count($mainFields) == 0) {
			Core::throwError('Please provide fields to show.', INTERNAL_ERROR);
		}

		$this->position = Core::constructObject($position);
		$this->baseUri = $baseUri;
		$this->mainFields = $mainFields;
	}

	public function getBaseUri() {
		return $this->baseUri;
	}

	public function setBaseUri($uri) {
		$this->baseUri = $uri;
	}

	public function getPosition() {
		return $this->position;
	}
	
	public function detail($id, $tpl = null) {
		$data = new CustomData($this->position);
		if ($id > 0 && !$data->load($id)) {
			$this->notFoundError();
		}
		else {
			$html = array();
			$fields = $data->getFields();
			foreach ($fields as $field) {
				if ($field->canRead()) {
					$html[] = array(
						'field' => Sanitize::saveHTML($field->getFieldName()),
						'name' => Sanitize::saveHTML($field->getName()),
						'description' => Sanitize::saveHTML($field->getDescription()),
						'code' => $field->getOutputCode(),
						'label' => !$field->noLabel()
					);
				}
			}
			$tpl = Response::getObject()->appendTemplate($tpl ? $tpl : '/Cms/fields/data_categories_detail');
			$tpl->assign('data', $data, false);
			$tpl->assign('fields', $html, false);
			$tpl->assign('id', $id);
			$tpl->assign('baseUri', $this->baseUri);
			$tpl->output();
		}
	}

	public function write($tpl = null) {
		$id = Request::get(1, VAR_INT);
		$isSent = (Request::get(2, VAR_URI) == 'send');

		$data = new CustomData($this->position);
		if ($id > 0 && !$data->load($id)) {
			CmsPage::error('Der gewählte Datensatz wurde leider nicht gefunden.');
		}
		else {
			if ($id == 0) {
				$data->setToDefault();
			}
			$fields = $data->getFields();

			if ($isSent) {
				$options = array();
				foreach ($fields as $field) {
					if ($field->canWrite()) {
						$options[$field->getFieldName()] = $field->getValidation();
					}
				}

				$result = Validator::checkRequest($options);

				foreach ($fields as $field) {
					if ($field->canWrite()) {
						$name = $field->getFieldName();
						if (isset($result['data'][$name])) {
							$field->setData($result['data'][$name]);
						}
					}
				}

				if (count($result['error']) > 0) {
					CmsPage::error($result['error']);
				}
				else {
					$success = false;
					if ($id > 0) {
						$success = $data->edit($id);
					}
					else {
						$id = $data->add();
						if ($id > 0) {
							$success = true;
						}
						else {
							$id = 0;
							$success = false;
						}
					}
					if ($success) {
						CmsPage::ok("Der Datensatz wurde erfolgreich gespeichert.");
					}
					else {
						CmsPage::error("Der Datensatz konnt leider nicht gespeichert werden.");
					}
				}
			}

			$html = array();
			foreach ($fields as $field) {
				if ($field->canWrite()) {
					$html[] = array(
						'field' => Sanitize::saveHTML($field->getFieldName()),
						'name' => Sanitize::saveHTML($field->getName()),
						'description' => Sanitize::saveHTML($field->getDescription()),
						'code' => $field->getInputCode(),
						'label' => !$field->noLabel()
					);
				}
			}
			$tpl = Response::getObject()->appendTemplate($tpl ? $tpl : '/Cms/fields/data_categories_write');
			$tpl->assign('data', $data, false);
			$tpl->assign('fields', $html, false);
			$tpl->assign('id', $id);
			$tpl->assign('baseUri', $this->baseUri);
			$tpl->output();
		}
	}

	public function remove() {
		$id = Request::get(1, VAR_INT);
		$data = new CustomData($this->position);
		if ($data->load($id)) {
			if (Request::get(2) == 'yes') {
				if ($data->remove()) {
					CmsPage::ok("Der Datensatz wurde erfolgreich gelöscht.");
				}
				else {
					CmsPage::error("Der Datensatz konnte leider nicht gelöscht werden.");
				}
			}
			else {
				CmsPage::yesNo(
					"Möchten Sie den gewählten Datensatz inkl. aller evtl. verknüpften Daten wirklich löschen?",
					URI::build($this->baseUri.'/remove/'.$id.'/yes'),
					URI::build($this->baseUri)
				);
			}
		}
		else {
			CmsPage::error('Der Datensatz wurde nicht gefunden.');
		}
	}

	public function overview($tpl = null, $pagination = 0, CustomDataFilter $filter = null) {
		if ($filter === null) {
			$filter = new CustomDataFilter($this->position);
			foreach ($this->mainFields as $field) {
				$filter->field($field);
			}
			$filter->orderBy(reset($this->mainFields));
		}

		$pages = '';
		if ($pagination > 0) {
			$pg = new Pagination($pagination, $filter->getAmount());
			$pg->setUri($this->baseUri);
			$pg->parsePage();
			$filter->limit($pg->getPerPage(), $pg->getOffset());
			$pages = $pg->build();
		}

		$tpl = Response::getObject()->appendTemplate($tpl ? $tpl : "/Cms/fields/data_categories");
		$tpl->assign('pages', $pages, false);
		$tpl->assign('list', $filter->retrieveList(), false);
		$tpl->assign('baseUri', $this->baseUri);
		$tpl->output();
	}

}
?>