<?php
	function error_handler($errNo, $errStr, $errFile, $errLine)
	{
		if(ob_get_length()){
			ob_clean(); //clear any generated output
		}
		$eMessage = 'Error no.: '.$errNo.chr(10).
					'TEXT.: '.$errStr.chr(10).
					'LOCATION.: '.$errFile.
					', line: '.$errLine;
		
		echo $eMessage;
		exit;
	}
?>