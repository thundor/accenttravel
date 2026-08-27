<?php

class Image
{
	public function get()
	{
		try
		{
			if(!empty($_GET['id']))
			{
				$id = intval($_GET['id']);
				$token = sha1($id . ':' . 'IMAGE-LOADER-KEY-' . date('Y'));

				$imgurl = 'https://tbs.accenttravel.ro/reseller/multiUse/hotelImage';
				$filename = $imgurl."?id=$id&token=$token";
				$headers = get_headers($filename);
				if(in_array("Location: /reseller/assets/phpThumb/img/placeholder.gif", $headers)){
					$filename = 'https://accenttravel.ro/themes/accent/assets/images/placeholder.png';
				}
				header("HTTP/1.1 302 Moved Temporarily");
			    header("Location: " . $filename);
			    exit();
			}
			else
			{
				throw new \Exception("Error Processing Request", 1);
			}
		}
		catch(\Exception $e)
		{
			header('HTTP/1.0 404 Not Found', true, 404);
		}
	}
}
$image = new Image();
$image->get();