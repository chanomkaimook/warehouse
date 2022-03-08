<?php 
	/*
	/*	conf_tableFull(border)
	/*		-	border = 0 or 1
	*/
	//======================================//
	//====		Table width 100%		====//
	//======================================//
	function conf_tableFull($table){
		$template = array(
				'table_open'            => $table,

				'thead_open'            => '<thead>',
				'thead_close'           => '</thead>',

				'heading_row_start'     => '<tr>',
				'heading_row_end'       => '</tr>',
				'heading_cell_start'    => '<th>',
				'heading_cell_end'      => '</th>',

				'tbody_open'            => '<tbody>',
				'tbody_close'           => '</tbody>',

				'row_start'             => '<tr>',
				'row_end'               => '</tr>',
				'cell_start'            => '<td>',
				'cell_end'              => '</td>',

				'row_alt_start'         => '<tr>',
				'row_alt_end'           => '</tr>',
				'cell_alt_start'        => '<td>',
				'cell_alt_end'          => '</td>',

				'table_close'           => '</table>'
		);
		
		return $template;
	}
	function conf_table($table){
		$template = $table;
		
		return $template;
	}
?>