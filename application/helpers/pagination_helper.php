<?php 
///		Pagination		///
	function conf_pagination($link,$sql,$perpage) {
		$config['base_url'] = $link;
		$config['total_rows'] = $sql;
		
		$config['per_page'] = $perpage;
		$config["uri_segment"] = 4;
		$config['num_links'] = 3;
		$config['full_tag_open'] = '<ul id="pagination" class="list-unstyled list-inline">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		
		$config['cur_tag_open'] = '<li><b>';
		$config['cur_tag_close'] = '</b></li>';
		$config['attributes'] = array('data-id' => $config['per_page']);
		$config['attributes']['rel'] = FALSE;		// attribute sample "rel=prev" on code
		//$config['use_page_numbers'] = TRUE;
		
		return $config;
	}
	function conf_paginationfront($link,$sql,$perpage) {
		$config['base_url'] = $link;
		$config['total_rows'] = $sql;
		
		$config['per_page'] = $perpage;
		$config["uri_segment"] = 3;
		$config['num_links'] = 3;
		$config['full_tag_open'] = '<ul id="pagination" class="list-unstyled list-inline">';
		$config['full_tag_close'] = '</ul>';
		
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		
		$config['cur_tag_open'] = '<li><b>';
		$config['cur_tag_close'] = '</b></li>';
		$config['attributes'] = array('data-id' => $config['per_page']);
		$config['attributes']['rel'] = FALSE;		// attribute sample "rel=prev" on code
		//$config['use_page_numbers'] = TRUE;
		
		return $config;
	}
?>