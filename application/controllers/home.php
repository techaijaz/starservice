<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	function __construct(){
		parent::__construct();
		//$this->utilities->validateSession();
		$this->load->library('Layouts');
		$this->load->model('auth/auths');
	}
	
	
	public function index() {
		//$extraHead = "activateHeadMeanu('topsignin')";
		//$this->layouts->set_extra_head($extraHead);
		$this->layouts->set_title('Home');
		
		$data['getServiceData'] = $this->commonModel->getservice(Date("2018-03-04"));
		//$this->layouts->add_include('assets/js/main.js')->add_include('assets/css/coustom.css')->add_include('https://www.google.com/recaptcha/api.js',false);
		$this->layouts->dbview('home/main_page',$data);
		
	}
	
	public function openaddservice(){
		$data['getServiceData'] = "";
		$data['productList'] = $this->utilities->getProduct();
		echo $this->load->view('home/addpopup',$data,true);
	}
	
	public function editservice(){
		$id=$this->input->post('id');
		$data['getServiceDataArr'] = $this->auths->getServiceSetails($id);
		/* echo "<pre>";
		print_r($data['getServiceDataArr']);die;
		echo "</pre>"; */
		//$data['getServiceData'] =  $this->commonModel->getRecord('services','*',array('id'=>$id),array(),"","","array","0");
		echo $this->load->view('home/updatepopup',$data,true);
	}
	
	public function viewDetail(){
		$data['Kashif']="My Name";
		/*$id=$this->input->post('id');
		$data['getServiceDataArr'] = $this->auths->getServiceSetails($id);
		 echo "<pre>";
		print_r($data['getServiceDataArr']);die;
		echo "</pre>"; */
		//$data['getServiceData'] =  $this->commonModel->getRecord('services','*',array('id'=>$id),array(),"","","array","0");
		echo $this->load->view('home/viewpopup',$data,true);
	}
	
	public function service() {
		$custArr = Array(
			"name"=>$this->input->post('name'),
			"mobile"=>$this->input->post('mobile'),
			"address"=>$this->input->post('addr'),
			"date_added"=>date("Y-m-d H:i:s"),
			"added_by"=>$this->utilities->getSessionUserData('uid')
		);
		$putArrSerDet = Array();
		print_r($this->input->post());die;
		$putArrSerDet['product'] = $this->input->post('product');
		$putArrSerDet['brand'] = $this->input->post('brand');
		$putArrSerDet['modelNum'] = $this->input->post('modelNum');
		$putArrSerDet['warranty'] = $this->input->post('warranty');
		$putArrSerDet['dateSold'] = $this->utilities->convertDateFormatForDbase($this->input->post('dateSold'));
		$putArrSerDet['services'] = $this->input->post('services');
		$putArrSerDet['duration'] = $this->input->post('duration');
		$sdate=$this->input->post('sdate');
		if(!empty($sdate)){
			$putArr['service_date'] = $this->utilities->convertDateFormatForDbase($this->input->post('sdate'));
		}else{
			$putArr['service_date'] ="";
		}
		
		$putArr['notes'] = $this->input->post('notes');
		$putArr['added_by'] = $this->utilities->getSessionUserData('uid');
		$putArr['date_modified'] = date("Y-m-d H:i:s");
		
		$up_res = $this->commonModel->insertRecord('services',$putArr);
		if($up_res){
			$putArrSerDet['service_id'] = $up_res;
			$putArrSerDet['added_by'] = $this->utilities->getSessionUserData('uid');
			$putArrSerDet['date_modified'] = date("Y-m-d H:i:s");
			$service_date = $this->utilities->convertDateFormatForDbase($this->input->post('sdate'));
			if($this->input->post('noofservice')>0){
				for($i=1;$i<=$this->input->post('noofservice');$i++){
					$serDuration = $this->input->post('serDuration');
					
					if($i==1){
						$putArrSerDet['service_date'] = $service_date;
					}else{
						switch ($serDuration){
							case "15d" :
							$newSerDate = date( "Y-m-d", strtotime( "$service_date +15 days" ) );
							break;
							case "mon" :
							$newSerDate = date("Y-m-d",strtotime("$service_date +1 month"));
							break;
							case "3mon" :
							$newSerDate = date("Y-m-d",strtotime("$service_date +3 month"));
							break;
							case "6mon" :
							$newSerDate = date("Y-m-d",strtotime("$service_date +6 month"));
							break;
							case "12mon" :
							$newSerDate = date("Y-m-d",strtotime("$service_date +12 month"));
							break;
						}
						$putArrSerDet['service_date'] = $newSerDate;
						$service_date = $newSerDate;
					}
					
					$this->commonModel->insertRecord('service_details',$putArrSerDet);
				}
			}
		
			echo json_encode(array("status"=>"success","msg"=>"Service recorded successfully.","data"=>$up_res));
		}else{
			echo json_encode(array("status"=>"error","msg"=>"Service not recorded.","data"=>""));
		}
	}
	
	public function updateservice() {
		$postArr = Array();
		$postArr['name'] = $this->input->post('name');
		$postArr['contact'] = $this->input->post('contact');
		$postArr['service_date'] = $this->utilities->convertDateFormatForDbase($this->input->post('sdate'));
		$postArr['address'] = $this->input->post('address');
		$postArr['updated_by'] = $this->utilities->getSessionUserData('uid');
		$postArr['date_modified'] = date("Y-m-d H:i:s");
		
		$up_res = $this->commonModel->updateRecord('services',$postArr,array("id"=>$this->input->post('id')));
		if($up_res){
			echo json_encode(array("status"=>"success","msg"=>"Service record update successfully.","data"=>$up_res));
		}else{
			echo json_encode(array("status"=>"error","msg"=>"Service not updated.","data"=>""));
		}
	}
	
	public function deleteservic(){
		$dl_res = $this->commonModel->deleteRecord('services',array("id"=>$this->input->post('id')));
		if($dl_res){
			echo json_encode(array("status"=>"success","msg"=>"Service record delete successfully.","data"=>$dl_res));
		}else{
			echo json_encode(array("status"=>"success","msg"=>"Delete failed.","data"=>$dl_res));
		}
	}
	
	public function isMobile() {
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}
	
	public function getBrandList() {
		$prodId = $this->input->post('prodId');
		$str ="<option value=''></option>";
		if($prodId){
			$brandList = $this->utilities->getBrand($prodId);
			if($brandList){
				foreach($brandList as $brand){
					$str .="<option value='".$brand['id']."'>".$brand['name']."</option>";
				}
			}
		}
		echo $str;
	}
}
