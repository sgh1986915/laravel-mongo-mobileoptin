<?php

namespace MobileOptin\Classes;

	defined('FILE_WRITE_MODE') || define('FILE_WRITE_MODE', 0666);
	
	class Tools{
		
		// ------------------------------------------------------------------------

		/**
		 * Create a Random String
		 *
		 * Useful for generating passwords or hashes.
		 *
		 * @access	public
		 * @param	string	type of random string.  basic, alpha, alunum, numeric, nozero, unique, md5, encrypt and sha1
		 * @param	integer	number of characters
		 * @return	string
		 */
		public static function random_string($type = 'alnum', $len = 8)
		{
			switch($type)
			{
				case 'basic'	: return mt_rand();
					break;
				case 'alnum'	:
				case 'numeric'	:
				case 'nozero'	:
				case 'alpha'	:

						switch ($type)
						{
							case 'alpha'	:	$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
								break;
							case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
								break;
							case 'numeric'	:	$pool = '0123456789';
								break;
							case 'nozero'	:	$pool = '123456789';
								break;
						}

						$str = '';
						for ($i=0; $i < $len; $i++)
						{
							$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
						}
						return $str;
					break;
				case 'unique'	:
				case 'md5'		:

							return md5(uniqid(mt_rand()));
					break;
			}
		}

		public static function log_message($log_path = './' , $level = 'error', $msg, $php_error = FALSE)
		{
			
			$level = strtoupper($level);
			$filepath = $log_path . 'log.txt';
			$message  = '';
			
			if ( ! $fp = @fopen($filepath, 'ab') )
				return FALSE;
			
			$message .= $level.' '.(($level == 'INFO') ? ' -' : '-').' '.date("d-m-Y"). ' --> '.$msg."\n";

			fwrite($fp, $message);
			fclose($fp);

			@chmod($filepath, FILE_WRITE_MODE);
			return TRUE;
		}		
	}

?>