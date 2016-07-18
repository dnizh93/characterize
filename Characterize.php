<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Characterize{

	private $rows;
	private $max_length;

	public function __construct()
	{

	}

	public function init($max_length = 40, $is_print = false)
	{
		$this->rows = array();
		$this->max_length = $max_length;

		return $this;
	}

	public function make($text = '', $format = '', $size = null, $font_mode = null)
	{
		if(is_array($text)){

			$column = count($text);

			$data = array(
				'type' => 'column',
				'data' => $text,
				'column' => $column
			);

			array_push($this->rows, $data);

		}else{

			$data = array(
				'type' => 'row',
				'text' => $text,
				'format' => $format,
				'font_mode' => $font_mode,
			);

			array_push($this->rows, $data);
		}

		return $this;
	}

	public function render(){

		$finaldata = array();
		foreach($this->rows as $a){
			if($a['type'] == 'column'){
				$final = $this->chunk_format($a['data'], $a['column']);
			}else{
				$prop = $this->get_properties($a['text']);
				$final = $this->format($a['format'], $prop, $a['font_mode']);
			}

			// FINAL CHECK
			$prop = $this->get_properties($final);
			$final = $this->format('left', $prop);

			array_push($finaldata, $final);
		}

		return $finaldata;
	}

	public function spacer($string = null)
	{
		if(!is_null($string)){
			$this->make(str_repeat($string, $this->max_length));
		}else{
			$this->make(' ');
		}
	}

	public function chunk_format($data, $column)
	{
		$final = "";
		foreach($data as $d){
			$prop = $this->get_properties($d[0], $d[2]);
			$final .= $this->format($d[1], $prop);
		}

		return $final;
	}

	public function get_properties($text, $size = null)
	{
		// GET MAX LENGTH
		$prop['max_length'] = $this->max_length;

		if(!is_null($size)){
			$prop['max_length'] = $size;
		}

		// GET TEXT
		$prop['text'] = $text;

		// GET TEXT LENGTH
		$prop['strlen'] = strlen($text);

		// GET HALF TEXT LENGTH
		$prop['strlen_half'] = ($prop['strlen']/2);

		// GET REDUCTION WITH MAX LENGTH
		$prop['strlen_reduction_max'] = $prop['max_length'] - $prop['strlen'];

		// GET DIVISION COLUMN WITH MAX LENGTH BASED ON TEXT LENGTH (DCMLT)
		$prop['dcmlt'] = ($prop['max_length'] / $prop['strlen']);

		// GET HALF DCLMT
		$prop['dcmlt_halft'] = ($prop['dcmlt'] / 2);

		// GET OFFSET POSITION
		$prop['offset_pos'] = $prop['dcmlt_halft'] * $prop['strlen'];

		// GET CENTER POSITION
		$prop['center_pos'] = $prop['offset_pos'] - $prop['strlen_half'];

		// print_r($prop);

		return $prop;
	}

	public function format($format, $prop, $font_mode = null)
	{
		switch($format){
			case 'center':
				$push = $prop['center_pos'];
				break;

			case 'right':
				$push = $prop['strlen_reduction_max'];
				break;

			default:
				$push = 0;
				break;
		}

		// GET TEXT LENGTH + SPACER LENGTH
		$prop['strlen_spacer'] = $prop['strlen']+$push;
		$prop['rest_spacer'] = $prop['max_length'] - $prop['strlen_spacer'];

		$spacer_left = "";
		if($push > 0){
			$spacer_left = str_repeat(" ", $push);
		}

		$spacer_right = "";
		if($prop['rest_spacer'] > 0){
			$spacer_right = str_repeat(" ", $prop['rest_spacer']);
		}

		$result = $spacer_left.$prop['text'].$spacer_right;
		$final = substr($result, 0, $prop['max_length']);

		return $final;
	}
}
