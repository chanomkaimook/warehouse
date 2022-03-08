<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_retailbarcode extends CI_Model {
   
    //---------------------------- DATATABLE ----------------------------//
    var $order_column = array("ID", "RPL_NAME_TH", "RPL_NAME_US", null, null);  

	function make_query() {  
        
        $this->db->select('
            retail_productmain.ID AS RPM_ID, 
            retail_productmain.NAME_TH AS RPM_NAME_TH, 
            retail_productmain.NAME_US AS RPM_NAME_US,
            retail_productlist.NAME_TH AS RPL_NAME_TH, 
            retail_productlist.NAME_US AS RPL_NAME_US, 
            retail_productlist.STATUS AS RPL_STATUS,
            retail_productlist.DATE_STARTS AS RPL_DATE_STARTS,
            retail_productlist.ID AS RPL_ID,
            retail_productlist.CODE_PRODUCT AS CODEPRODUCT,
            retail_productlist.CODEMAC AS CODEMAC
        ');  
        $this->db->from('retail_productmain'); 
        $this->db->join('retail_productlist', "retail_productmain.ID = retail_productlist.PROMAIN_ID", 'right');
        // $this->db->where('retail_productlist.promain_id not in(6,12)');
        $this->db->where('retail_productlist.promain_id not in(6,12,14,15,16)');    //  14,15,16 dryeage

        if(!empty($_POST["search"]["value"])) {  
           $this->db->like("retail_productmain.NAME_TH", $_POST["search"]["value"]);  
           $this->db->or_like("retail_productmain.NAME_US", $_POST["search"]["value"]);  
           $this->db->or_like("retail_productlist.NAME_TH", $_POST["search"]["value"]);  
           $this->db->or_like("retail_productlist.NAME_US", $_POST["search"]["value"]); 
        } 

        if(!empty($_POST["order"])) {  
             $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);  
        } else {  
             $this->db->order_by('retail_productlist.ID', 'DESC');  
        }  
 
        if(!empty($_POST["keyword"])) {  
            $this->db->like("retail_productmain.NAME_TH", $_POST["selectproductmain"]);  
            $this->db->or_like("retail_productmain.NAME_US", $_POST["selectproductmain"]);  
            $this->db->or_like("retail_productlist.NAME_TH", $_POST["selectproductmain"]);  
            $this->db->or_like("retail_productlist.NAME_US", $_POST["selectproductmain"]); 
        } 

        if(!empty($_POST["selectproductmain"])) {  
            $this->db->where('retail_productmain.ID', $_POST["selectproductmain"]); 
        } 
         
        if(!empty($_POST["status"])){
            $status = $_POST["status"];
            if($_POST["status"] == 'off'){ $status = '0'; }
            $this->db->where('retail_productlist.STATUS', $status); 
        }
          
    }
    

	function barcode($code){
        //
        //	generate barcode
        //
        require_once "asset/plugin/phpbarcode/src/BarcodeGenerator.php";
        require_once "asset/plugin/phpbarcode/src/BarcodeGeneratorHTML.php";
        require_once "asset/plugin/phpbarcode/src/BarcodeGeneratorPNG.php";
        
        $result = "";

		// $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		$border = 1;//กำหนดความหน้าของเส้น Barcode
		$height = 20;//กำหนดความสูงของ Barcode
        $img_barcode =  $generator->getBarcode($code , $generator::TYPE_CODE_128,$border,$height);

        //  save image on director 
        file_put_contents("asset/images/barcode/".$code.".png",$img_barcode);
        
        //  get image barcode from directory
        $objOpen = opendir('asset/images/barcode');
        while (($file = readdir($objOpen)) !== false)
        {
            $filename = "";
            $image = $code.".png";
            $type = strchr($file,".");      //  check type file
            if($type == ".png"){
                if($file == $image){
                    $result = "<img src='".base_url('asset/images/barcode/'.$file)."' >";
                }
            }
        }
        
		return $result;
    }
    
    function make_datatables(){  
        $this->make_query();  
        if($_POST["length"] != -1) {  
            $this->db->limit($_POST['length'], $_POST['start']);  
        }  
        
        // echo $this->db->get_compiled_select();
        $query = $this->db->get();  
        return $query->result();  
    }  
    function get_filtered_data(){  
        $this->make_query();  
        $query = $this->db->get();  
        return $query->num_rows();  
    }       
    function get_all_data(){  
        $this->db->select("*");  
        $this->db->from('retail_productmain');  
        return $this->db->count_all_results();  
    }  
    // ====================== EDIT STATUS ========================== //
    function updateProductlist() {
        //
        //  @param id   @int = product id
        //  @param code @text = product code online
        //  @param codemac @text = product code mac
        //
        $id = $this->input->post('id');
        $code = $this->input->post('code');
        $codemac = $this->input->post('codemac');
        if($code == ""){
            $code = null;
            $r_codeproduct = get_WhereParaSelect('code_product','retail_productlist','id',$id);
            if($r_codeproduct){
                $imagecodeproduct = $r_codeproduct->code_product;
            }
        }
        if($codemac == ""){
            $codemac = null;
            $r_codemac = get_WhereParaSelect('codemac','retail_productlist','id',$id);
            if($r_codemac){
                $imagecodemac = $r_codemac->code_product;
            }
        }

        $error = 1;
        $txt = "";
        //
        //  update query
        $dataupdate = array(
                        'codemac'           => $codemac,
                        'code_product'      => $code
                    );
        $this->db->where('id', $id);
        $this->db->update('retail_productlist', $dataupdate);
        if($this->db->affected_rows() !== null){
            //
            //  delete temp file image
            if($imagecodeproduct){
                unlink("asset/images/barcode/".$imagecodeproduct.".png");
            }
            //
            //  delete temp file image
            if($imagecodemac){
                unlink("asset/images/barcode/".$imagecodemac.".png");
            }

            $error = 0;
            $txt = "ทำรายการทำเร็จ";
        }

        // ============== Log_Detail ============== //
        $log_query = $this->db->last_query();
        $last_id = $this->session->userdata('log_id');
        $detail = "Update code product To ".$status_producttxt." Code : ".$this->session->userdata('useradminid')." Name : ".$this->session->userdata('useradminname');
        $type = "Update";
        $arraylog = array(
            'log_id'  	 	 => $last_id,
            'detail'  		 => $detail,
            'logquery'       => $log_query,
            'type'     	 	 => $type,
            'date_starts'    => date('Y-m-d H:i:s')
        );
        updateLog($arraylog);
        // ======================================= //

        $data = array(
            "error_code" 		=> $error ,
            "txt" 				=> $txt
        );
        $data = json_encode($data);
        return $data;
    } 

}
?>