<?php
	require('../../global.php');
	
	switch($ac){
		case 'edit':
			$set = array(
				'isforcedlogin = '.(int)$val_isforcedlogin,
				'dock = "'.$val_dock.'"',
				'desk1 = "'.$val_desk1.'"',
				'desk2 = "'.$val_desk2.'"',
				'desk3 = "'.$val_desk3.'"',
				'desk4 = "'.$val_desk4.'"',
				'desk5 = "'.$val_desk5.'"',
				'desk = '.$val_desk,
				'appxy = "'.$val_appxy.'"',
				'appsize = "'.$val_appsize.'"',
				'dockpos = "'.$val_dockpos.'"',
				'skin = "'.$val_skin.'"',
				'wallpaper_id = '.$val_wallpaper_id,
				'wallpapertype = "'.$val_wallpapertype.'"'
			);
			$db->update(0, 0, 'tb_setting', $set);
			echo json_encode(array(
				'info' => '',
				'status' => 'y'
			));
			break;
		case 'updateApps':
			$appsrs = $db->select(0, 0, 'tb_app', 'tbid,name,icon', 'and tbid in ('.$appsid.')');
			foreach($appsrs as $a){
				echo '<div class="app" appid="'.$a['tbid'].'"><img src="../../'.$a['icon'].'" alt="'.$a['name'].'" title="'.$a['name'].'"><span class="del">删</span></div>';
			}
			break;
	}
?>