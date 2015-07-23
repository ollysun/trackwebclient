<?php
namespace Adapter;
use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;

class BranchAdapter extends BaseAdapter {
	public function createNewHub($postData) {
		return $this->request(ServiceConstant::URL_BRANCH_ADD, $postData, self::HTTP_POST);
	}
	public function editOneHub($postData) {
		return $this->request(ServiceConstant::URL_BRANCH_EDIT, $postData, self::HTTP_POST);
	}
	public function getOneHub($id) {
		return $this->request(ServiceConstant::URL_GET_HUB, array('id' => $id), self::HTTP_GET);
	}
	public function getHubs($state = null) {
		$filter = 'branch_type='.ServiceConstant::BRANCH_TYPE_HUB;
		$filter .= ($state != null ? '&state_id=' . $state : '');
		//$filter = '';
		return $this->request(ServiceConstant::URL_BRANCH_GET_ALL . '?' . $filter, array(), self::HTTP_GET);
	}

	public function createNewCentre($postData) {
		return $this->request(ServiceConstant::URL_BRANCH_ADD, $postData, self::HTTP_POST);
	}
	public function editOneCentre($postData) {
		return $this->request(ServiceConstant::URL_BRANCH_EDIT, $postData, self::HTTP_POST);
	}
	public function getOneCentre($id) {
		return $this->request(ServiceConstant::URL_BRANCH_GET_ONE, array('id' => $id), self::HTTP_GET);
	}
	public function getCentres($hub_id=null, $state_id = null, $status = null) {
		$filter = ($hub_id != null ? 'hub_id=' . $hub_id : '');
		$filter .= ($state_id != null ? '&state=' . $state_id : '');
		$filter .= ($status != null ? '&status=' . $status : '');
		return $this->request(ServiceConstant::URL_BRANCH_GET_ALL_EC . '?' . $filter, array(), self::HTTP_GET);
	}
}