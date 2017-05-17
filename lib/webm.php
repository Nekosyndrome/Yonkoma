<?php
/* 
 *  Class for handling webm files around the MitsubaBBS Project
 *  Copyright (C) 2014  Malkovich <chlodnapiwnica@gmail.com>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class webm
{
	public $width, $height;
	private $input_file;
	
	function __construct($x)
	{
		$this->input_file = $x;
	}
	
	function create_thumbnail($thumbnail, $max_w=125, $max_h=125, $max_time='00:00:05')
	{
		$ext = strtolower(pathinfo($thumbnail, PATHINFO_EXTENSION));
		$scale = 'scale=w='. $max_w. ':h='. $max_h. ':force_original_aspect_ratio=decrease';
		$exec_string = '';
		
		switch($ext)
		{
			case 'webm':
				$exec_string = 'ffmpeg -i '.$this->input_file.
									 ' -an'.
									 ' -t '.$max_time.
									 ' -vf '. $scale.
									 ' -y '.$thumbnail.
									 ' 2>&1';
				break;
			case 'gif':
				$exec_string = 'ffmpeg -i '.$this->input_file.
									 ' -t '.$max_time.
									 ' -r 10 '.
									 ' -vf '. $scale.
									 ' -y '.$thumbnail.
									 ' 2>&1';
				break;
			case 'jpg':
				$exec_string = 'ffmpeg -i '.$this->input_file.
									' -vframes 1'.
									' -vf '. $scale.
									' -y '. $thumbnail.
									' 2>&1';
				break;
		}
		if($exec_string == '') return false;
		exec($exec_string, $output, $return_var);
		return $return_var == 0;
	}

	function has_audio_stream()
	{
		$exec_string = 'ffprobe -v error -select_streams a:0 -show_entries stream '.$this->input_file . ' 2>&1';
		$lines = shell_exec($exec_string);
		$lines = explode("\n", $lines);
		$found = false;
		$ar = array();
		foreach ($lines as $line) if(($x=strpos($line, '=')) !== FALSE)
		{
			$key = substr($line,0,$x);
			$val = substr($line,$x+1,strlen($line)-($x+1));
			$ar[$key] = $val;
		}

		return count($ar)!=0;
	}
	
	function is_valid_webm()
	{
		$exec_string = 'ffprobe -v error -select_streams v:0 -show_entries stream '.$this->input_file . ' 2>&1';
		$lines = shell_exec($exec_string);
		$lines = explode("\n", $lines);
		$found = false;
		$ar = array();

		foreach ($lines as $line) if(($x=strpos($line, '=')) !== FALSE)
		{
			$key = substr($line,0,$x);
			$val = substr($line,$x+1,strlen($line)-($x+1));
			$ar[$key] = $val;
		}

		if(isset($ar['codec_name'])) if($ar['codec_name']=='vp8' || $ar['codec_name']=='vp9')
		{
			$this->width = intval($ar['width']);
			$this->height = intval($ar['height']);
			return true;
		}
		var_dump($ar['codec_name']);
		return false;
	}
}
?>
