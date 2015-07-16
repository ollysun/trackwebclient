<?php
namespace Adapter;
use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class HubAdapter extends BaseAdapter {

	public function createNewHub($postData) {
		return $this->request(ServiceConstant::URL_ADD_HUB, $postData, self::HTTP_POST);
	}
	public function editOneHub($id) {
		return $this->request(ServiceConstant::URL_EDIT_HUB, $postData, self::HTTP_PUT);
	}
	public function getOneHub($id) {
		return $this->request(ServiceConstant::URL_GET_HUB, array('id' => $id), self::HTTP_GET);
	}

	public function getHubs($status = null, $state = null) {
		$filter = ($status != null ? '&status=' . $status : '');
		$filter .= ($state != null ? '&state=' . $state : '');
		//$filter = '';
		return $this->request(ServiceConstant::URL_GET_ALL_HUBS . '?' . $filter, array(), self::HTTP_GET);
	}
}