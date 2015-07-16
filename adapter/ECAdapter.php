<?php
namespace Adapter;
use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class ECAdapter extends BaseAdapter {

	public function createNewEC($postData) {
		return $this->request(ServiceConstant::URL_ADD_EC, $postData, self::HTTP_POST);
	}
	public function editOneEC($id) {
		return $this->request(ServiceConstant::URL_EDIT_EC, $postData, self::HTTP_PUT);
	}
	public function getOneEC($id) {
		return $this->request(ServiceConstant::URL_GET_EC, array('id' => $id), self::HTTP_GET);
	}

	public function getHubs($status = null, $status = null) {
		$filter = ($hub != null ? '&hub=' . $hub : '');
		$filter .= ($status != null ? '&status=' . $status : '');
		//$filter = '';
		return $this->request(ServiceConstant::URL_GET_ALL_EC . '?' . $filter, array(), self::HTTP_GET);
	}
}