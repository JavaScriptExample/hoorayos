<?php
	require('../../global.php');

	//验证是否登入
	if(!checkLogin()){
		redirect('../error.php?code='.$errorcode['noLogin']);
	}
	//验证是否为管理员
	else if(!checkAdmin()){
		redirect('../error.php?code='.$errorcode['noAdmin']);
	}
	//验证是否有权限
	else if(!checkPermissions(4)){
		redirect('../error.php?code='.$errorcode['noPermissions']);
	}

	if(isset($_GET['permissionid'])){
		$permission = $db->get('tb_permission', '*', array(
			'tbid' => $_GET['permissionid']
		));
		if($permission['apps_id'] != ''){
			$permission['appsinfo'] = $db->select('tb_app', array('tbid', 'name', 'icon'), array(
				'tbid' => explode(',', $permission['apps_id'])
			));
		}
	}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>权限管理</title>
	<?php include('sysapp/global_css.php'); ?>
</head>
<body>
	<form action="detail.ajax.php" method="post" name="form" id="form" class="form-horizontal">
		<div class="title-bar">编辑权限</div>
		<div class="creatbox">
			<div class="panel panel-default">
				<div class="panel-body">
					<input type="hidden" name="ac" value="edit">
					<input type="hidden" name="id" value="<?php echo $_GET['permissionid']; ?>">
					<div class="form-group">
						<label for="inputName" class="col-sm-2 control-label">权限名称：</label>
						<div class="col-sm-10">
							<input type="text" name="val_name" class="form-control" id="inputName" value="<?php echo $permission['name']; ?>" datatype="*" nullmsg="请输入权限名称">
							<span class="help-block"></span>
						</div>
					</div>
					<div class="form-group">
						<label for="inputAppsId" class="col-sm-2 control-label">专属应用：</label>
						<div class="col-sm-10">
							<div class="permissions_apps">
								<?php
									if($permission['appsinfo'] != NULL){
										foreach($permission['appsinfo'] as $v){
											echo '<div class="app" appid="'.$v['tbid'].'">';
												echo '<img src="../../'.$v['icon'].'" alt="'.$v['name'].'" title="'.$v['name'].'">';
												echo '<span class="del">删</span>';
											echo '</div>';
										}
									}
								?>
							</div>
							<a class="btn btn-default" href="javascript:;" menu="addapps">添加应用</a>
							<input type="hidden" name="val_apps_id" id="inputAppsId" value="<?php echo $permission['apps_id']; ?>" datatype="*" nullmsg="请选择专属应用">
							<span class="help-block"></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="bottom-bar">
			<a class="btn btn-primary pull-right" id="btn-submit" href="javascript:;"><i class="glyphicon glyphicon-ok"></i> 确定</a>
			<a class="btn btn-default pull-right" href="javascript:window.parent.closeDetailIframe();" style="margin-right:10px"><i class="glyphicon glyphicon-chevron-up"></i> 返回权限列表</a>
		</div>
	</form>
	<?php include('sysapp/global_js.php'); ?>
	<script>
	$(function(){
		$('#form').Validform({
			btnSubmit: '#btn-submit',
			postonce: false,
			showAllError: true,
			//msg：提示信息;
			//o:{obj:*,type:*,curform:*}, obj指向的是当前验证的表单元素（或表单对象），type指示提示的状态，值为1、2、3、4， 1：正在检测/提交数据，2：通过验证，3：验证失败，4：提示ignore状态, curform为当前form对象;
			//cssctl:内置的提示信息样式控制函数，该函数需传入两个参数：显示提示信息的对象 和 当前提示的状态（既形参o中的type）;
			tiptype: function(msg, o){
				if(!o.obj.is('form')){//验证表单元素时o.obj为该表单元素，全部验证通过提交表单时o.obj为该表单对象;
					var B = o.obj.parents('.form-group');
					var T = B.find('.help-block');
					if(o.type == 2){
						B.removeClass('has-error');
						T.text('');
					}else{
						B.addClass('has-error');
						T.text(msg);
					}
				}
			},
			ajaxPost: true,
			callback: function(data){
				if($('input[name="id"]').val() != ''){
					if(data.status == 'y'){
						window.parent.closeDetailIframe(function(){
							window.parent.$('#pagination').trigger('currentPage');
						});
						window.parent.swal({
							type : 'success',
							title : '编辑成功'
						});
					}
				}else{
					if(data.status == 'y'){
						swal({
							type : 'success',
							title : '添加成功',
							text : '是否继续添加？',
							showCancelButton : true,
							confirmButtonText : '继续添加',
							cancelButtonText : '返回',
							closeOnConfirm : false,
							closeOnCancel : false
						}, function(isConfirm){
							if(isConfirm){
								location.reload();
							}else{
								window.parent.closeDetailIframe(function(){
									window.parent.$('#table').bootstrapTable('refresh');
								});
							}
						});
					}
				}
			}
		});
		//添加应用
		$('a[menu=addapps]').click(function(){
			dialog({
				id : 'alert_addapps',
				title : '添加应用',
				url : 'alert_addapps.php',
				data : {
					appsid : $('#inputAppsId').val()
				},
				padding : 0,
				width : 360,
				height : 320,
				ok : function(){
					$('#inputAppsId').val(this.data.appsid).focusout();
					$.ajax({
						type : 'POST',
						url : 'detail.ajax.php',
						data : 'ac=updateApps&appsid=' + this.data.appsid
					}).done(function(msg){
						$('.permissions_apps').html(msg);
					});
				},
				okValue : '确认',
				cancel : true,
				cancelValue : '取消'
			}).showModal();
		});
		//删除应用
		$('.permissions_apps').on('click','.app .del',function(){
			var appid = $(this).parent().attr('appid');
			var appsid = $('#inputAppsId').val().split(',');
			var newappsid = [];
			for(var i=0, j=0; i<appsid.length; i++){
				if(appsid[i] != appid){
					newappsid[j] = appsid[i];
					j++;
				}
			}
			$('#inputAppsId').val(newappsid.join(',')).focusout();
			$(this).parent().remove();
		});
	});
	</script>
</body>
</html>